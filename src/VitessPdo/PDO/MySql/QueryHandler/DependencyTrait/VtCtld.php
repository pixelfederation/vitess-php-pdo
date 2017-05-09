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

namespace VitessPdo\PDO\MySql\QueryHandler\DependencyTrait;

use VitessPdo\PDO\VtCtld\ClientInterface;

/**
 * Description of trait VtCtld
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\QueryHandler\DependencyTrait
 */
trait VtCtld
{

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * VtCtldMember constructor.
     *
     * @param ClientInterface $client
     */
    private function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }
}
