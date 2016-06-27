<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryAnalyzer;

use VitessPdo\PDO\Exception;

/**
 * Description of class UseQuery
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer
 */
class UseQuery extends QueryDecorator
{

    /**
     * @var string
     */
    private $dbName;

    /**
     * @const string
     */
    const TYPE = QueryInterface::TYPE_USE;

    /**
     *
     * @return string
     * @throws Exception
     */
    public function getDbName()
    {
        if ($this->dbName === null) {
            $parsedSql = $this->getParsedSql();

            if (!isset($parsedSql[1])) {
                throw new Exception("Database name missing.");
            }

            $this->dbName = trim($parsedSql[1], '`');
        }

        return $this->dbName;
    }
}
