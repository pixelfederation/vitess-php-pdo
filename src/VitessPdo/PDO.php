<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdo;

use VitessPdo\PDO\Dsn;
use VitessPdo\PDO\QueryAnalyzer;
use VTContext;
use VTGateConn;
use VTGrpcClient;
use VTGateTx;
use topodata\TabletType;
use Grpc;
use PDO as CorePDO;
use PDOException;
use Exception;

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
     * @var VTContext
     */
    private $vitessCtx;

    /**
     * @var VTGrpcClient
     */
    private $grpcClient;

    /**
     * @var VTGateConn
     */
    private $vtgateConnection;

    /**
     * @var VTGateTx
     */
    private $transaction = null;

    /**
     * @var QueryAnalyzer
     */
    private $queryAnalyzer;

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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function connect(array $options)
    {
        try {
            $this->vitessCtx        = VTContext::getDefault();
            $host                   = $this->dsn->getConfig()->getHost();
            $port                   = $this->dsn->getConfig()->getPort();
            $this->grpcClient       = new VTGrpcClient("{$host}:{$port}");
            $this->vtgateConnection = new VTGateConn($this->grpcClient);
            $this->queryAnalyzer    = new QueryAnalyzer();
        } catch (Exception $e) {
            throw new PDOException("Error while connecting to vitess: " . $e->getMessage(), $e->getCode(), $e);
        }

        // Vitess doesn't support SET NAMES queries yet
//        if (isset($options[CorePDO::MYSQL_ATTR_INIT_COMMAND])) {
//            $query = $options[CorePDO::MYSQL_ATTR_INIT_COMMAND];
//            $this->vtgateConnection->execute($this->vitessCtx, $query, [], TabletType::MASTER);
//        }
        //new \PDOStatement();
    }

    /**
     * @param string $statement
     *
     * @return int
     */
    public function exec($statement)
    {
        $isInTransaction = $this->isInTransaction();
        $transaction = $this->getTransaction();
        $cursor = $transaction->execute($this->vitessCtx, $statement, [], TabletType::MASTER);

        if (!$isInTransaction) {
            $this->commitTransaction();
        }

        return $cursor->getRowsAffected();
    }

    /**
     * @return bool
     */
    private function isInTransaction()
    {
        return $this->transaction !== null;
    }

    /**
     * @return VTGateTx
     */
    private function getTransaction()
    {
        if (!$this->transaction) {
            $this->transaction = $this->vtgateConnection->begin($this->vitessCtx);
        }

        return $this->transaction;
    }

    /**
     * @throws \VTException
     */
    private function commitTransaction()
    {
        if (!$this->isInTransaction()) {
            throw new PDOException("Not in a transaction.");
        }

        $this->transaction->commit($this->vitessCtx);
        $this->transaction = null;
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->vtgateConnection->close();
    }
}
