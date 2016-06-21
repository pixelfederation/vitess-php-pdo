<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Handler;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\Cursor\Cursor;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\Query;

/**
 * Description of class ShowTables
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class ShowCreateDatabase extends Base
{

    /**
     * @var Dsn
     */
    private $dsn;

    /**
     * @var array
     */
    private static $data = [
        'Database' => '{DB}',
        0 => '{DB}',
        'Create Database' => 'CREATE DATABASE `{DB}` /*!40100 DEFAULT CHARACTER SET utf8 */',
        1 => 'CREATE DATABASE `{DB}` /*!40100 DEFAULT CHARACTER SET utf8 */',
    ];

    /**
     * @var array
     */
    private static $fields = [
        'Database',
        'Create Database',
    ];

    /**
     * ShowCreateDatabase constructor.
     *
     * @param Dsn $dsn
     */
    public function __construct(Dsn $dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * @param Query $query
     *
     * @return Result
     * @throws Exception
     */
    public function getResult(Query $query)
    {
        $database = $this->getDatabase($query);

        $newData = [];
        $newData[] = array_map(function ($row) use ($database) {
            return str_replace('{DB}', $database, $row);
        }, self::$data);

        $cursor = new Cursor($newData, self::$fields);

        return new Result($cursor);
    }

    /**
     * @param Query $query
     *
     * @return string
     * @throws Exception
     */
    private function getDatabase(Query $query)
    {
        $queryDb = $query->getDatabaseExpression();
        $dsnDb = $this->dsn->getConfig()->getDbName();

        if ($queryDb !== $dsnDb) {
            throw new Exception('Invalid database in query - ' . $query->getSql());
        }

        return $dsnDb;
    }
}
