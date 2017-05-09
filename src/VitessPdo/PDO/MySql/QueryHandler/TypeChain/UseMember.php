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

namespace VitessPdo\PDO\MySql\QueryHandler\TypeChain;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\DependencyTrait;
use VitessPdo\PDO\MySql\QueryHandler\Member;
use VitessPdo\PDO\MySql\Result\EmptyResult;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\UseQuery;
use VitessPdo\PDO\VtCtld\CachingClient;
use VitessPdo\PDO\VtCtld\ClientInterface;

/**
 * Description of class UseMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class UseMember extends Member
{

    use DependencyTrait\VtCtld;
    use DependencyTrait\Dsn;

    /**
     * UseMember constructor.
     * @param Dsn $dsn
     * @param ClientInterface $client
     */
    public function __construct(Dsn $dsn, ClientInterface $client)
    {
        $this->setDsn($dsn);
        $this->setClient($client);
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query->isType(QueryInterface::TYPE_USE)) {
            return null;
        }

        $query = new UseQuery($query);
        $keyspace = $query->getDbName();
        $this->dsn->getConfig()->setKeyspace($keyspace);

        if ($this->client instanceof CachingClient) {
            $this->client->clearCache();
        }

        return new EmptyResult();
    }
}
