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

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain\Helper;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\DependencyTrait\VtCtld;
use VitessPdo\PDO\VtCtld\ClientInterface;
use VitessPdo\PDO\VtCtld\Command\ListAllTablets;
use VitessPdo\PDO\VtCtld\Result\ListAllTablets as ListAllTabletsResult;

/**
 * Description of class Tablet
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\QueryHandler\ShowChain\Helper
 */
class Tablet
{

    use VtCtld;

    /**
     * Tablet constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->setClient($client);
    }

    /**
     * @return ListAllTabletsResult\Tablet
     * @throws Exception
     */
    public function getTablet()
    {
        $command = new ListAllTablets();
        /* @var $vtCtldResult ListAllTabletsResult */
        $vtCtldResult = $this->client->executeCommand($command);

        $tablets = $vtCtldResult->getDataForCurrentKeyspace();

        return $tablets[0];
    }
}
