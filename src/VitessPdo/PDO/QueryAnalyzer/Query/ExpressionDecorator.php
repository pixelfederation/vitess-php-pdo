<?php
/**
 * Copyright 2017 PIXELFEDERATION s.r.o.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VitessPdo\PDO\QueryAnalyzer\Query;

/**
 * Description of class ExpressionDecorator
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
abstract class ExpressionDecorator implements ExpressionInterface
{

    /**
     * @var ExpressionInterface
     */
    private $decorated;

    /**
     * ExpressionDecorator constructor.
     *
     * @param ExpressionInterface $decorated
     */
    public function __construct(ExpressionInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getDecorated()->getType();
    }

    /**
     * @return false|string
     */
    public function getAlias()
    {
        return $this->getDecorated()->getAlias();
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->getDecorated()->getExpression();
    }

    /**
     * @return false|Expression[]
     */
    public function getSubTree()
    {
        return $this->getDecorated()->getSubTree();
    }

    /**
     * @return false|Expression
     */
    public function getCreateDef()
    {
        return $this->getDecorated()->getCreateDef();
    }

    /**
     * @return false|string
     */
    public function getDelim()
    {
        return $this->getDecorated()->getDelim();
    }

    /**
     * @return false|NoQuotes
     */
    public function getNoQuotes()
    {
        return $this->getDecorated()->getNoQuotes();
    }

    /**
     * @param string $type
     *
     * @return null|ExpressionInterface
     */
    public function findFirstInSubTree($type)
    {
        return $this->getDecorated()->findFirstInSubTree($type);
    }

    /**
     * @param string $type
     *
     * @return ExpressionInterface[]
     */
    public function findAllInSubTreeAfterInclusive($type)
    {
        return $this->getDecorated()->findAllInSubTreeAfterInclusive($type);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getData($key)
    {
        return $this->getDecorated()->getData($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasData($key)
    {
        return $this->getDecorated()->hasData($key);
    }

    /**
     * @return ExpressionInterface
     */
    protected function getDecorated()
    {
        return $this->decorated;
    }
}
