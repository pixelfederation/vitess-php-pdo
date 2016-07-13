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
