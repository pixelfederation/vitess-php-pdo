<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdo;

use VitessPdo\PDO\Dsn;
use VitessPdo\PDO\PDOStatement;
use VitessPdo\PDO\QueryAnalyzer;
use VitessPdo\PDO\Vitess;
use Grpc;
use PDO as CorePDO;

/**
 * Description of class PDO
 *
 * @author  mfris
 * @package VitessPdo
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PDO
{

    /**
     * @var Dsn
     */
    private $dsn;

    /**
     * @var Vitess
     */
    private $vitess;

    /**
     * @var QueryAnalyzer
     */
    private $queryAnalyzer;

    /**
     * @var string
     */
    private $lastInsertId = self::DEFAULT_LAST_INSERT_ID;

    /**
     * @const string
     */
    const DEFAULT_LAST_INSERT_ID = '0';

    /**
     * PDO constructor.
     *
     * @param       $dsn
     * @param null  $username
     * @param null  $password
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct($dsn, $username = null, $password = null, array $options = [])
    {
        $this->dsn = new Dsn($dsn);
        $this->connect($options);
    }

    /**
     * @param array $options
     */
    private function connect(array $options)
    {
        $host = $this->dsn->getConfig()->getHost();
        $port = $this->dsn->getConfig()->getPort();
        $connectionString = "{$host}:{$port}";
        $this->vitess = new Vitess($connectionString);
        $this->queryAnalyzer = new QueryAnalyzer();

        if (isset($options[CorePDO::MYSQL_ATTR_INIT_COMMAND])) {
            // Vitess doesn't support SET NAMES queries yet
            // $query = $options[CorePDO::MYSQL_ATTR_INIT_COMMAND];
            // $this->vtgateConnection->execute($this->vitessCtx, $query, [], TabletType::MASTER);
        }
    }

    /**
     * @param string $statement
     *
     * @return int
     */
    public function exec($statement)
    {
        $cursor = $this->vitess->executeWrite($statement);

        if ($this->queryAnalyzer->isInsertQuery($statement)) {
            $this->lastInsertId = $cursor->getInsertId();
        }

        return $cursor->getRowsAffected();
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->vitess->isInTransaction();
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->vitess->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit()
    {
        $this->resetLastInsertId();

        return $this->vitess->commitTransaction();
    }

    /**
     * @return bool
     */
    public function rollback()
    {
        $this->resetLastInsertId();

        return $this->vitess->rollbackTransaction();
    }

    /**
     * @param string $name
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function lastInsertId($name = null)
    {
        return $this->lastInsertId;
    }

    /**
     * @param string $statement
     * @param array $driverOptions
     * @return PDOStatement
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepare($statement, array $driverOptions = [])
    {
        return new PDOStatement($statement, $this->vitess);
    }

    /**
     *
     */
    public function __destruct()
    {
        unset($this->vitess);
    }

    /**
     *
     */
    private function resetLastInsertId()
    {
        $this->lastInsertId = self::DEFAULT_LAST_INSERT_ID;
    }
}
