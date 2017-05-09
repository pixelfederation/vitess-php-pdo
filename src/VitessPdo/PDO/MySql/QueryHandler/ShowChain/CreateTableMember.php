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

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\ShowChain\Helper\Tablet;
use VitessPdo\PDO\MySql\QueryHandler\VtCtldMember;
use VitessPdo\PDO\MySql\Result\Show\CreateTable;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;
use VitessPdo\PDO\VtCtld\ClientInterface;
use VitessPdo\PDO\VtCtld\Result\ListAllTablets as ListAllTabletsResult;
use VitessPdo\PDO\VtCtld\Command\GetSchema;
use VitessPdo\PDO\VtCtld\Result\GetSchema as GetSchemaResult;
use VitessPdo\PDO\VtCtld\Result\Result;

/**
 * Description of class CreateTableMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\QueryHandler\ShowChain
 */
class CreateTableMember extends VtCtldMember
{

    /**
     * @var Tablet
     */
    private $tablet;

    /**
     * TablesMember constructor.
     *
     * @param ClientInterface $client
     * @param Tablet $tablet
     */
    public function __construct(ClientInterface $client, Tablet $tablet)
    {
        $this->tablet = $tablet;
        parent::__construct($client);
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_CREATE_TABLE) {
            return null;
        }

        $table = $query->getCreateObjectExpression(ShowQuery::EXPRESSION_CREATE_TABLE);

        if (!$table) {
            throw new Exception('Table missing.');
        }

        $tablet = $this->tablet->getTablet();
        $schemaCmd = new GetSchema($tablet->getAlias());
        /* @var $result GetSchemaResult */
        $result = $this->client->executeCommand($schemaCmd);

        return new CreateTable($result, $table);
    }
}
