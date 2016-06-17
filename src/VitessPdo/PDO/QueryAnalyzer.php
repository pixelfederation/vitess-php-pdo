<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO;

use PHPSQLParser\PHPSQLParser;
use VitessPdo\PDO\QueryAnalyzer\Result;

/**
 * Description of class QueryAnalyzer
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class QueryAnalyzer
{

    /**
     * @var PHPSQLParser
     */
    private $parser;

    /**
     * QueryAnalyzer constructor.
     */
    public function __construct()
    {
        $this->parser = new PHPSQLParser();
    }

    /**
     * @param string $sql
     * @return boolean
     */
    public function isInsertQuery($sql)
    {
        return $this->parseQuery($sql)->isInsert();
    }

    /**
     * @param string $sql
     * @return boolean
     */
    public function isUpdateQuery($sql)
    {
        return $this->parseQuery($sql)->isUpdate();
    }

    /**
     * @param string $sql
     * @return boolean
     */
    public function isDeleteQuery($sql)
    {
        return $this->parseQuery($sql)->isDelete();
    }

    /**
     * @param string $sql
     *
     * @return bool
     */
    public function isWritableQuery($sql)
    {
        $result = $this->parseQuery($sql);

        return $result->isInsert()
               || $result->isUpdate()
               || $result->isDelete();
    }

    /**
     * @param string $sql
     *
     * @return Result
     */
    private function parseQuery($sql)
    {
        $parsedData = $this->parser->parse($sql);

        return new Result($parsedData);
    }
}
