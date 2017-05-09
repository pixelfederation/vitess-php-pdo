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

namespace VitessPdo\PDO\MySql;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\MySql\QueryHandler\TypeChain\Chain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\PDOStatement;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\VtCtld\CachingClient;
use VitessPdo\PDO\VtCtld\Client;

/**
 * Description of class QueryChain
 *
 * @author  mfris
 * @package Adminer\Vitess
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Emulator
{

    /**
     * @var Dsn
     */
    private $dsn;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Chain
     */
    private $chain;

    /**
     * QueryChain constructor.
     *
     * @param Dsn $dsn
     */
    public function __construct(Dsn $dsn)
    {
        $this->dsn = $dsn;
        $this->client = new CachingClient(new Client($dsn));
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     */
    public function getResult(QueryInterface $query)
    {
        return $this->getChain()->getResult($query);
    }

    /**
     * @return Chain
     */
    private function getChain()
    {
        if ($this->chain === null) {
            $this->chain = new Chain($this->dsn, $this->client);
        }

        return $this->chain;
    }
}
