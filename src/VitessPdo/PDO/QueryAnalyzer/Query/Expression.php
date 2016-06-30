<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryAnalyzer\Query;

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
            if (!isset($this->data[self::KEY_SUB_TREE])) {
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
}
