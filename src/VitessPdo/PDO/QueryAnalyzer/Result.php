<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\QueryAnalyzer;

/**
 * Description of class Result
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class Result
{

    /**
     * @var array
     */
    private $parsedSql;

    const SQL_COMMAND_INSERT = 'INSERT';
    const SQL_COMMAND_UPDATE = 'UPDATE';
    const SQL_COMMAND_DELETE = 'DELETE';

    /**
     * Result constructor.
     *
     * @param array $parsedSql
     */
    public function __construct(array $parsedSql)
    {
        $this->parsedSql = $parsedSql;
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
}
