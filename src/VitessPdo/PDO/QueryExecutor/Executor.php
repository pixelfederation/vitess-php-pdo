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
