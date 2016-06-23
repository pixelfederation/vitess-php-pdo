<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\QueryAnalyzer;

use VitessPdo\PDO\Exception;

/**
 * Description of class Query
 *
 * @author  mfris
 * @package VitessPdo\PDO\Analyzer
 */
class Query
{

    /**
     * @var string
     */
    private $sql;

    /**
     * @var array
     */
    private $parsedSql;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $showExpression = [];

    /**
     * @var string
     */
    private $databaseExpression;

    const SQL_COMMAND_INSERT = 'INSERT';
    const SQL_COMMAND_UPDATE = 'UPDATE';
    const SQL_COMMAND_DELETE = 'DELETE';

    const TYPE_SELECT = 'SELECT';
    const TYPE_INSERT = 'INSERT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';
    const TYPE_USE    = 'USE';
    const TYPE_SHOW   = 'SHOW';
    const TYPE_UNKNOWN = 'unknown';

    const KEY_BASE_EXPRESSION = 'base_expr';
    const KEY_NO_QUOTES = 'no_quotes';
    const KEY_PARTS = 'parts';

    const EXPRESSION_TABLES = 'TABLES';
    const EXPRESSION_TABLE = 'TABLE';
    const EXPRESSION_STATUS = 'STATUS';
    const EXPRESSION_LIKE = 'LIKE';
    const EXPRESSION_COLLATION = 'COLLATION';
    const EXPRESSION_CREATE = 'CREATE';
    const EXPRESSION_DATABASE = 'DATABASE';
    const EXPRESSION_INDEX = 'INDEX';
    const EXPRESSION_FROM = 'FROM';

    /**
     * @var array
     */
    private static $types = [
        self::TYPE_SELECT => self::TYPE_SELECT,
        self::TYPE_INSERT => self::TYPE_INSERT,
        self::TYPE_UPDATE => self::TYPE_UPDATE,
        self::TYPE_DELETE => self::TYPE_DELETE,
        self::TYPE_USE    => self::TYPE_USE,
        self::TYPE_SHOW   => self::TYPE_SHOW,
    ];

    /**
     * Query constructor.
     *
     * @param string $sql
     * @param array $parsedSql
     */
    public function __construct($sql, array $parsedSql)
    {
        $this->sql = $sql;
        $this->parsedSql = $parsedSql;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return bool
     */
    public function isInsert()
    {
        return isset($this->parsedSql[self::SQL_COMMAND_INSERT]);
    }

    /**
     * @return bool
     */
    public function isUpdate()
    {
        return isset($this->parsedSql[self::SQL_COMMAND_UPDATE]);
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return isset($this->parsedSql[self::SQL_COMMAND_DELETE]);
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return $this->isInsert()
            || $this->isUpdate()
            || $this->isDelete();
    }

    /**
     * @return string
     */
    public function getType()
    {
        if ($this->type === null) {
            $this->type = self::TYPE_UNKNOWN;

            foreach (self::$types as $type) {
                if ($this->isTypeParsed($type)) {
                    $this->type = $type;
                    break;
                }
            }
        }

        return $this->type;
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function getDbNameForUse()
    {
        if ($this->getType() !== self::TYPE_USE) {
            throw new Exception("Not a USE query.");
        }

        if (!isset($this->parsedSql[self::TYPE_USE][1])) {
            throw new Exception("Database name missing.");
        }

        return $this->parsedSql[self::TYPE_USE][1];
    }

    /**
     *
     * @param string $type
     * @param int $index
     * @return string|int
     * @throws Exception
     */
    public function getExpressionForType($type, $index = 0)
    {
        if (!isset($this->showExpression[$index])) {
            if ($this->getType() !== $type) {
                throw new Exception("Not a $type query.");
            }

            if (!isset($this->parsedSql[$type][$index])) {
                return 0;
            }

            $field = $this->parsedSql[$type][$index];
            $this->showExpression[$index] = $field[self::KEY_BASE_EXPRESSION];
        }

        return $this->showExpression[$index];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDatabaseExpression()
    {
        if ($this->databaseExpression === null) {
            /** @todo switch statement for other expression types as SHOW */
            if ($this->getType() !== self::TYPE_SHOW) {
                throw new Exception('Not a SHOW query.');
            }

            $databaseExpression = $this->parsedSql[self::TYPE_SHOW][2];
            $this->databaseExpression = $databaseExpression[self::KEY_NO_QUOTES][self::KEY_PARTS][0];
        }

        return $this->databaseExpression;
    }

    /**
     * @return string|null
     */
    public function getLikeExpression()
    {
        $keys = array_keys($this->parsedSql);
        $primaryIndex = $keys[0];
        $likeIndex = null;

        foreach ($this->parsedSql[$primaryIndex] as $index => $expr) {
            if ($expr[self::KEY_BASE_EXPRESSION] === self::EXPRESSION_LIKE) {
                $likeIndex = $index;
                break;
            }
        }

        if (!$likeIndex) {
            return null;
        }

        $field = $this->parsedSql[$primaryIndex][$likeIndex + 1];

        return $field[self::KEY_BASE_EXPRESSION];
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function isTypeParsed($type)
    {
        return isset($this->parsedSql[$type]);
    }
}
