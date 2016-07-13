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
 * Description of class Field
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
class Expression implements ExpressionInterface
{

    /**
     * @var array
     */
    private $data;

    /**
     * @var NoQuotes
     */
    private $noQuotes;

    /**
     * @var Expression[]|false
     */
    private $subTree;

    /**
     * @var Expression|false
     */
    private $createDef;

    /**
     * Field constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->data[self::KEY_EXPR_TYPE];
    }

    /**
     * @return string|false
     */
    public function getAlias()
    {
        if (!isset($this->data[self::KEY_ALIAS])) {
            return false;
        }

        return $this->data[self::KEY_ALIAS];
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->data[self::KEY_BASE_EXPR];
    }

    /**
     * @return Expression[]|false
     */
    public function getSubTree()
    {
        if ($this->subTree === null) {
            if (!isset($this->data[self::KEY_SUB_TREE]) || !$this->data[self::KEY_SUB_TREE]) {
                $this->subTree = false;

                return $this->subTree;
            }

            $this->subTree = array_map(function (array $member) {
                return new Expression($member);
            }, $this->data[self::KEY_SUB_TREE]);
        }

        return $this->subTree;
    }

    /**
     * @return CreateExpression|false
     */
    public function getCreateDef()
    {
        if ($this->createDef === null) {
            if (!isset($this->data[self::KEY_CREATE_DEF])) {
                $this->createDef = false;

                return $this->createDef;
            }

            $this->createDef = new Expression($this->data[self::KEY_CREATE_DEF]);
        }

        return $this->createDef;
    }

    /**
     * @return string|false
     */
    public function getDelim()
    {
        if (!isset($this->data[self::KEY_DELIM])) {
            return false;
        }

        return $this->data[self::KEY_DELIM];
    }

    /**
     * @return NoQuotes|false
     */
    public function getNoQuotes()
    {
        if ($this->noQuotes === null) {
            if (!isset($this->data[self::KEY_NO_QUOTES])) {
                $this->noQuotes = false;

                return $this->noQuotes;
            }

            $this->noQuotes = new NoQuotes($this->data[self::KEY_NO_QUOTES]);
        }

        return $this->noQuotes;
    }

    /**
     * @param string $type
     *
     * @return null|ExpressionInterface
     */
    public function findFirstInSubTree($type)
    {
        foreach ($this->getSubTree() as $expression) {
            if ($expression->getType() === $type) {
                return $expression;
            }
        }

        return null;
    }

    /**
     * @param string $type
     *
     * @return ExpressionInterface[]
     */
    public function findAllInSubTreeAfterInclusive($type)
    {
        $expressions = [];
        $skip = true;

        foreach ($this->getSubTree() as $expression) {
            if ($expression->getType() === $type) {
                $skip = false;
            }

            if ($skip) {
                continue;
            }

            $expressions[] = $expression;
        }

        return $expressions;
    }

    /**
     * @param string $key
     *
     * @return mixed
     * @throws Exception
     */
    public function getData($key)
    {
        if (!$this->hasData($key)) {
            throw new Exception("Data key not found - '{$key}'.");
        }

        return $this->data[$key];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasData($key)
    {
        return isset($this->data[$key]);
    }
}
