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
class Expression
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
     * @const string
     */
    const KEY_EXPR_TYPE = 'expr_type';

    /**
     * @const string
     */
    const KEY_ALIAS = 'alias';

    /**
     * @const string
     */
    const KEY_BASE_EXPR = 'base_expr';

    /**
     * @const string
     */
    const KEY_SUB_TREE = 'sub_tree';

    /**
     * @const string
     */
    const KEY_DELIM = 'delim';

    /**
     * @const string
     */
    const KEY_NO_QUOTES = 'no_quotes';

    /**
     * @const string
     */
    const TYPE_FUNCTION = 'function';

    /**
     * @const string
     */
    const EXPR_USER = 'USER';

    /**
     * @const string
     */
    const EXPR_LIKE = 'LIKE';

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
     * @return string|false
     */
    public function getSubTree()
    {
        if (!isset($this->data[self::KEY_SUB_TREE])) {
            return false;
        }

        return new Expression($this->data[self::KEY_SUB_TREE]);
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
}
