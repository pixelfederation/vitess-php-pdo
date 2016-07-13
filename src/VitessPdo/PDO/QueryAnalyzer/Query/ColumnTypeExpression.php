<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the PIXEL FEDERATION, s.r.o. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL PIXEL FEDERATION, s.r.o. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace VitessPdo\PDO\QueryAnalyzer\Query;

use VitessPdo\PDO\Exception;

/**
 * Description of class ColumnTypeExpression
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
class ColumnTypeExpression extends ExpressionDecorator
{

    /**
     * @var Expression
     */
    private $dataDype;

    /**
     * @var string
     */
    private $sqlType;

    /**
     * @var bool
     */
    private $hasDefaultValue;

    /**
     * @var string
     */
    private $defaultValue;

    /**
     * @return DataTypeExpression
     * @throws Exception
     */
    public function getDataType()
    {
        if ($this->dataDype === null) {
            $dataType = $this->findFirstInSubTree(self::TYPE_DATA_TYPE);

            if (!$dataType) {
                $dataType = $this->findFirstInSubTree(self::TYPE_RESERVED);
            }

            if (!$dataType || ($dataType->getType() === self::TYPE_RESERVED && $dataType->getExpression() !== 'enum')) {
                throw new Exception('Unable to find data type.');
            }

            $this->dataDype = new DataTypeExpression($dataType);
        }

        return $this->dataDype;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSqlType()
    {
        if ($this->sqlType === null) {
            $dataType = $this->getDataType();
            $bracketExpr = $this->findFirstInSubTree(self::TYPE_BRACKET_EXPRESSION);

            if (!$bracketExpr && $dataType->getSubTree()) {
                $bracketExpr = $dataType->findFirstInSubTree(self::TYPE_BRACKET_EXPRESSION);
            }

            $this->sqlType = $dataType->getExpression() . ($bracketExpr ? $bracketExpr->getExpression() : '');
        }

        return $this->sqlType;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isNullable()
    {
        return ((int) $this->getData(self::KEY_NULLABLE)) === 1;
    }

    /**
     * @return bool
     */
    public function hasDefault()
    {
        if ($this->hasDefaultValue === null) {
            $this->hasDefaultValue = $this->hasData(self::KEY_DEFAULT);

            if (!$this->hasDefaultValue) {
                $reserveds = $this->findAllInSubTreeAfterInclusive(self::TYPE_RESERVED);

                foreach ($reserveds as $reserved) {
                    if ($reserved->getExpression() === self::EXPR_DEFAULT) {
                        $this->hasDefaultValue = true;
                        break;
                    }
                }
            }
        }

        return $this->hasDefaultValue;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getDefault()
    {
        if ($this->defaultValue === null) {
            if (!$this->hasDefault()) {
                throw new Exception('There is no default value here.');
            }

            if ($this->hasData(self::KEY_DEFAULT)) {
                $this->defaultValue = trim($this->getData(self::KEY_DEFAULT), "'");

                return $this->defaultValue;
            }

            $reserveds = $this->findAllInSubTreeAfterInclusive(self::TYPE_RESERVED);

            foreach ($reserveds as $index => $reserved) {
                if ($reserved->getExpression() === self::EXPR_DEFAULT) {
                    $this->defaultValue = $reserveds[$index + 1]->getExpression();
                    break;
                }
            }
        }

        return $this->defaultValue;
    }
}
