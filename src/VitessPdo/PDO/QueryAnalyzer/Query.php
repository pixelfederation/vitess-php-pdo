<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\QueryAnalyzer;

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

    const SQL_COMMAND_INSERT = 'INSERT';
    const SQL_COMMAND_UPDATE = 'UPDATE';
    const SQL_COMMAND_DELETE = 'DELETE';

    const TYPE_SELECT = 'SELECT';
    const TYPE_INSERT = 'INSERT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';
    const TYPE_USE    = 'USE';
    const TYPE_UNKNOWN = 'unknown';

    /**
     * @var array
     */
    private static $types = [
        self::TYPE_SELECT => self::TYPE_SELECT,
        self::TYPE_INSERT => self::TYPE_INSERT,
        self::TYPE_UPDATE => self::TYPE_UPDATE,
        self::TYPE_DELETE => self::TYPE_DELETE,
        self::TYPE_USE    => self::TYPE_USE,
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
            foreach (self::$types as $type) {
                if ($this->isTypeParsed($type)) {
                    $this->type = $type;
                    break;
                }
            }

            if (!$this->type === null) {
                $this->type = self::TYPE_UNKNOWN;
            }
        }

        return $this->type;
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
