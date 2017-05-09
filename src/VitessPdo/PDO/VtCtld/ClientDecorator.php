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

namespace VitessPdo\PDO\VtCtld;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Command\Command;

/**
 * Description of class ClientDecorator
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld
 */
abstract class ClientDecorator implements ClientInterface
{

    /**
     * @var ClientInterface
     */
    private $decoratedClient;

    /**
     * ClientDecorator constructor.
     *
     * @param ClientInterface $decoratedClient
     */
    public function __construct(ClientInterface $decoratedClient)
    {
        $this->decoratedClient = $decoratedClient;
    }

    /**
     * @param Command $command
     *
     * @return Result\Result
     * @throws Exception
     */
    public function executeCommand(Command $command)
    {
        return $this->getDecoratedClient()->executeCommand($command);
    }

    /**
     * @return ClientInterface
     */
    protected function getDecoratedClient()
    {
        return $this->decoratedClient;
    }
}
