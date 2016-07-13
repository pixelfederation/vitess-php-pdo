<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the PIXEL FEDERATION, s.r.o. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL PIXEL FEDERATION, s.r.o. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
