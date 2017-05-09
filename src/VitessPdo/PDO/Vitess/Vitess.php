<?php
/**
 * Copyright 2017 PIXELFEDERATION s.r.o.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VitessPdo\PDO\Vitess;

use VitessPdo\PDO\Attributes;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryExecutor\ExecutorInterface;
use VitessPdo\PDO\Exception;
use VitessPdo\PDO\QueryAnalyzer\Query as Query;
use Vitess\Context;
use Vitess\VTGateConn;
use Vitess\Grpc\Client;
use Vitess\VTGateTx;
use Vitess\Exception as VitessException;
use Vitess\Proto\Topodata\TabletType;
use Grpc\ChannelCredentials;
use PDOException;

/**
 * Description of class Vitess
 *
 * @author  mfris
 * @package VitessPdo\PDO
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Vitess implements ExecutorInterface
{

    /**
     * @var Context
     */
    private $ctx;

    /**
     * @var Client
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
     * @var Attributes
     */
    private $attributes;

    /**
     * @var ClusterConfig
     */
    private $clusterConfig;

    /**
     * Vitess constructor.
     *
     * @param string $connectionString
     * @param Attributes $attributes
     * @throws PDOException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct($connectionString, Attributes $attributes)
    {
        $this->attributes = $attributes;

        try {
            $this->ctx        = Context::getDefault();
            $credentials      = ChannelCredentials::createInsecure();
            $this->grpcClient = new Client($connectionString, ['credentials' => $credentials]);
            $this->connection = new VTGateConn($this->grpcClient);
            $this->clusterConfig = new ClusterConfig();
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
     * @return Result
     */
    public function commitTransaction()
    {
        if (!$this->isInTransaction()) {
            return new Result(null, new Exception("Cannot commit. Not in transaction."));
        }

        try {
            $this->transaction->commit($this->ctx);
        } catch (VitessException $e) {
            $this->handleException($e);

            return new Result(null, $e);
        } finally {
            $this->resetTransaction();
        }

        return new Result();
    }

    /**
     * @return Result
     * @throws PDOException
     */
    public function rollbackTransaction()
    {
        if (!$this->isInTransaction()) {
            throw new PDOException("No transaction is active.");
        }

        try {
            $transaction = $this->getTransaction();
            $transaction->rollback($this->ctx);
        } catch (VitessException $e) {
            $this->handleException($e);

            return new Result(null, $e);
        } finally {
            $this->resetTransaction();
        }

        return new Result();
    }

    /**
     * @param QueryInterface $query
     * @param array          $params
     *
     * @return Result
     * @throws PDOException
     */
    public function executeWrite(QueryInterface $query, array $params = [])
    {
        $isInTransaction = $this->isInTransaction();
        $transaction = $this->getTransaction();

        $cursor = null;

        try {
            $cursor = $transaction->execute($this->ctx, $query->getSql(), $params, TabletType::MASTER);
        } catch (VitessException $e) {
            $this->handleException($e);

            return new Result(null, $e);
        }

        if (!$isInTransaction) {
            $commitResult = $this->commitTransaction();

            if (!$commitResult->isSuccess()) {
                return $commitResult;
            }
        }

        return new Result(new Cursor($cursor));
    }

    /**
     * @param QueryInterface $query
     * @param array          $params
     *
     * @return Result
     * @throws PDOException
     */
    public function executeRead(QueryInterface $query, array $params = [])
    {
        $cursor = null;

        try {
            $reader = $this->connection;
            $tabletType = $this->clusterConfig->getReadFrom();

            if ($this->isInTransaction()) {
                $reader = $this->getTransaction();
                $tabletType = TabletType::MASTER;
            }

            $cursor = $reader->execute($this->ctx, $query->getSql(), $params, $tabletType);
        } catch (VitessException $e) {
            $this->handleException($e);

            return new Result(null, $e);
        }

        return new Result(new Cursor($cursor));
    }

    /**
     * @return ClusterConfig
     */
    public function getClusterConfig()
    {
        return $this->clusterConfig;
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
     * @param VitessException $exception
     *
     * @throws PDOException
     */
    private function handleException(VitessException $exception)
    {
        switch (true) {
            case $this->attributes->isErrorModeSilent():
                break;

            case $this->attributes->isErrorModeWarning():
                trigger_error($exception->getMessage(), E_WARNING);
                break;

            case $this->attributes->isErrorModeException():
                throw new PDOException(
                    "Vitess exception (check previous exception stack): " . $exception->getMessage(),
                    0,
                    $exception
                );
                break;
        }
    }

    /**
     * destructor - rollbacks the active transaction if php script ends prematurely
     * and commit is forgotten to be called
     *
     * also the connection is being closed here
     */
    public function __destruct()
    {
        if ($this->isInTransaction()) {
            $this->rollbackTransaction();
        }

        $this->connection->close();
    }
}
