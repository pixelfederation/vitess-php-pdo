<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\QueryAnalyzer;

use PHPSQLParser\PHPSQLParser;

/**
 * Description of class Analyzer
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class Analyzer
{

    /**
     * @var PHPSQLParser
     */
    private $parser;

    /**
     * Analyzer constructor.
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

        return $result->isWritable();
    }

    /**
     * @param string $sql
     *
     * @return QueryInterface
     */
    public function parseQuery($sql)
    {
        $parsedData = $this->parser->parse($sql);

        return new Query($sql, $parsedData);
    }
}
