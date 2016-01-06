<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdo\PDO;

use VTContext;
use VTGateConn;
use VTGrpcClient;
use VTGateTx;
use VTCursor;
use VTException;
use topodata\TabletType;
use PDOException;

/**
 * Description of class Vitess
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class Vitess
{

    /**
     * @var VTContext
     */
    private $ctx;

    /**
     * @var VTGrpcClient
     */
    private $grpcClient;

    /**
     * @var VTGateConn
     */
    private $connection;

    /**
     * @var VTGateTx
     */
    private $transaction = null;

    /**
     * Vitess constructor.
     *
     * @param string $connectionString
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct($connectionString)
    {
        try {
            $this->ctx        = VTContext::getDefault();
            $this->grpcClient = new VTGrpcClient($connectionString);
            $this->connection = new VTGateConn($this->grpcClient);
        } catch (Exception $e) {
            throw new PDOException("Error while connecting to vitess: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return bool
     */
    public function isInTransaction()
    {
        return $this->transaction !== null;
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        if ($this->isInTransaction()) {
            return false;
        }

        $this->getTransaction();

        return true;
    }

    /**
     * @return bool
     */
    public function commitTransaction()
    {
        if (!$this->isInTransaction()) {
            return false;
        }

        try {
            $this->transaction->commit($this->ctx);
        } catch (VTException $e) {
            return false;
        } finally {
            $this->resetTransaction();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function rollbackTransaction()
    {
        if (!$this->isInTransaction()) {
            throw new PDOException("No transaction is active.");
        }

        try {
            $transaction = $this->getTransaction();
            $transaction->rollback($this->ctx);
        } catch (VTException $e) {
            return false;
        } finally {
            $this->resetTransaction();
        }

        return true;
    }

    /**
     * @param $sql
     * @param array $params
     *
     * @return VTCursor
     * @throws VTException
     */
    public function executeWrite($sql, array $params = [])
    {
        $isInTransaction = $this->isInTransaction();
        $transaction = $this->getTransaction();
        $cursor = $transaction->execute($this->ctx, $sql, $params, TabletType::MASTER);

        if (!$isInTransaction) {
            $this->commitTransaction();
        }

        return $cursor;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return VTCursor
     */
    public function executeRead($sql, array $params = [])
    {
        $cursor = $this->connection->execute($this->ctx, $sql, $params, TabletType::REPLICA);

        return $cursor;
    }

    /**
     * @return VTGateTx
     */
    private function getTransaction()
    {
        if (!$this->transaction) {
            $this->transaction = $this->connection->begin($this->ctx);
        }

        return $this->transaction;
    }

    /**
     * @return void
     */
    private function resetTransaction()
    {
        $this->transaction = null;
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->connection->close();
    }
}
