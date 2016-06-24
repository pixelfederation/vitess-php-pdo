<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryExecutor;

use VitessPdo\PDO\MySql\Emulator as MySqlEmulator;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\Vitess\Vitess;
use VitessPdo\PDO\QueryAnalyzer\Query as Query;

/**
 * Description of class QueryExecutor
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
final class Executor implements ExecutorInterface
{

    /**
     * @var Vitess
     */
    private $vitess;

    /**
     * @var MySqlEmulator
     */
    private $mysqlEmulator;

    /**
     * QueryExecutor constructor.
     *
     * @param Vitess   $vitess
     * @param MySqlEmulator $mysqlEmulator
     */
    public function __construct(Vitess $vitess, MySqlEmulator $mysqlEmulator)
    {
        $this->vitess        = $vitess;
        $this->mysqlEmulator = $mysqlEmulator;
    }

    /**
     * @param QueryInterface $query
     * @param array          $params
     *
     * @return ResultInterface
     */
    public function executeWrite(QueryInterface $query, array $params = [])
    {
        $result = $this->mysqlEmulator->getResult($query);

        if ($result) {
            return $result;
        }

        return $this->vitess->executeWrite($query, $params);
    }

    /**
     * @param QueryInterface $query
     * @param array          $params
     *
     * @return ResultInterface
     */
    public function executeRead(QueryInterface $query, array $params = [])
    {
        $result = $this->mysqlEmulator->getResult($query);

        if ($result) {
            return $result;
        }

        return $this->vitess->executeRead($query, $params);
    }
}
