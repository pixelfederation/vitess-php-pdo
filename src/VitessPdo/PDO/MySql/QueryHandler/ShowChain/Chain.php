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
use VitessPdo\PDO\MySql\QueryHandler\VtCtldChain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;

/**
 * Description of class Chain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Chain extends VtCtldChain
{

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function getResult(QueryInterface $query)
    {
        $query = new ShowQuery($query);

        return parent::getResult($query);
    }

    /**
     *
     */
    protected function initialize()
    {
        $tabletHelper = new Tablet($this->client);

        $this->first = new TablesMember($this->client, $tabletHelper);
        $this->first->setSuccessor($tableStatus = new TableStatusMember($this->client, $tabletHelper));
        $tableStatus->setSuccessor($databases = new DatabasesMember($this->client));
        $databases->setSuccessor($collation = new CollationMember($this->client));
        $collation->setSuccessor($database = new CreateDatabaseMember());
        $database->setSuccessor($indexFrom = new IndexFromMember($this->client, $tabletHelper));
        $indexFrom->setSuccessor($fullColumns = new FullColumnsFromMember($this->client, $tabletHelper));
        $fullColumns->setSuccessor($createTable = new CreateTableMember($this->client, $tabletHelper));
        $createTable->setSuccessor(new EnginesMember());
    }
}
