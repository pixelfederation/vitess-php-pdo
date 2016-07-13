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

namespace VitessPdo\PDO\QueryAnalyzer;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\QueryAnalyzer\Query\Expression;

/**
 * Description of class ShowQuery
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class ShowQuery extends QueryDecorator
{

    /**
     * @const string
     */
    const TYPE = QueryInterface::TYPE_SHOW;

    /**
     * @const string
     */
    const EXPRESSION_TABLES = 'TABLES';

    /**
     * @const string
     */
    const EXPRESSION_DATABASES = 'DATABASES';

    /**
     * @const string
     */
    const EXPRESSION_TABLE_STATUS = 'TABLE STATUS';

    /**
     * @const string
     */
    const EXPRESSION_COLLATION = 'COLLATION';

    /**
     * @const string
     */
    const EXPRESSION_ENGINES = 'ENGINES';

    /**
     * @const string
     */
    const EXPRESSION_CREATE_DATABASE = 'CREATE DATABASE';

    /**
     * @const string
     */
    const EXPRESSION_CREATE_TABLE = 'CREATE TABLE';

    /**
     * @const string
     */
    const EXPRESSION_INDEX = 'INDEX';

    /**
     * @const string
     */
    const EXPRESSION_FULL_COLUMNS = 'FULL COLUMNS';

    /**
     * @var string
     */
    private $object;

    /**
     * @var int
     */
    private $afterObjectIndex;

    /**
     * @var string[]
     */
    private $expressionValues = [];

    /**
     * @var string
     */
    private $databaseExpression;

    /**
     *
     * @return string
     * @throws Exception
     */
    public function getObject()
    {
        if ($this->object === null) {
            /* @var Expression[] */
            $expressions = $this->getExpressions();

            if (!isset($expressions[0]) || $expressions[0] === Expression::EXPR_LIKE) {
                throw new Exception("Object missing.");
            }

            $stopExprs = [Expression::EXPR_LIKE, Expression::EXPR_FROM];
            $objectParts = [];
            /* @var $expr Expression */
            foreach ($expressions as $index => $expr) {
                if (in_array($expr->getExpression(), $stopExprs) || $expr->getNoQuotes()) {
                    $this->afterObjectIndex = $index;
                    break;
                }

                $objectParts[] = $expr->getExpression();
            }

            $this->object = implode(' ', $objectParts);
        }

        return $this->object;
    }

    /**
     * @return null|string
     */
    public function getLikeExpression()
    {
        return $this->getExressionValue(Expression::EXPR_LIKE);
    }

    /**
     * @return null|string
     */
    public function getFromExpression()
    {
        return $this->getExressionValue(Expression::EXPR_FROM);
    }

    /**
     * @param string $type
     * @return string
     * @throws Exception
     */
    public function getCreateObjectExpression($type)
    {
        if ($this->databaseExpression === null) {
            if ($this->getObject() !== $type) {
                throw new Exception("Not a SHOW {$type} query.");
            }

            $expressions = $this->getExpressions();

            if ($this->afterObjectIndex === null) {
                throw new Exception("Invalid SHOW {$type} query - object name missing.");
            }

            /* @var $databaseExpression Expression */
            $databaseExpression = $expressions[$this->afterObjectIndex];
            $this->databaseExpression = $databaseExpression->getNoQuotes()->getParts()[0];
        }

        return $this->databaseExpression;
    }

    /**
     * @param string $expressionType
     *
     * @return null|string
     */
    private function getExressionValue($expressionType)
    {
        if (!array_key_exists($expressionType, $this->expressionValues)) {
            $expressions = $this->getExpressions();
            $likeIndex = null;

            /* @var $expr Expression */
            foreach ($expressions as $index => $expr) {
                if ($expr->getExpression() === $expressionType) {
                    $likeIndex = $index;
                    break;
                }
            }

            if (!$likeIndex) {
                return null;
            }

            /* @var $field Expression */
            $field = $expressions[$likeIndex + 1];
            $this->expressionValues[$expressionType] = $field->getExpression();
            $noQuotes = $field->getNoQuotes();

            if ($noQuotes) {
                $this->expressionValues[$expressionType] = $noQuotes->getPartsAsString();
            }
        }

        return $this->expressionValues[$expressionType];
    }
}
