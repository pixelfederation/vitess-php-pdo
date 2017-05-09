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
use VitessPdo\PDO\VtCtld\Command\GetSchema;
use VitessPdo\PDO\VtCtld\Command\ListAllTablets;
use VitessPdo\PDO\VtCtld\Result\Result;

/**
 * Description of class CachingClient
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld
 */
class CachingClient extends ClientDecorator
{

    /**
     * @var array
     */
    private $responses = [];

    /**
     * @var array
     */
    private static $commandsToCache = [
        GetSchema::class => GetSchema::class,
        ListAllTablets::class => ListAllTablets::class,
    ];

    /**
     * @param Command $command
     * @return Result
     * @throws Exception
     */
    public function executeCommand(Command $command)
    {
        if ($this->isCommandCacheable($command)) {
            return $this->executeCommandCached($command);
        }

        return $this->getDecoratedClient()->executeCommand($command);
    }

    /**
     * @param Command $command
     * @return Result
     * @throws Exception
     */
    private function executeCommandCached(Command $command)
    {
        $cacheKey = $this->getCacheKey($command);

        if (!$this->isCached($cacheKey)) {
            $response = $this->getDecoratedClient()->executeCommand($command);
            $this->addToCache($cacheKey, $response);
        }

        return $this->getFromCache($cacheKey);
    }

    /**
     * @return void
     */
    public function clearCache()
    {
        $this->responses = [];
    }

    /**
     * @param Command $command
     *
     * @return string
     */
    private function getCacheKey(Command $command)
    {
        return (string) $command;
    }

    /**
     * @param string $cacheKey
     *
     * @return bool
     */
    private function isCached($cacheKey)
    {
        return isset($this->responses[$cacheKey]);
    }

    /**
     * @param string $cacheKey
     * @param Result $result
     */
    private function addToCache($cacheKey, Result $result)
    {
        $this->responses[$cacheKey] = $result;
    }

    /**
     * @param string $cacheKey
     *
     * @return Result
     */
    private function getFromCache($cacheKey)
    {
        return $this->responses[$cacheKey];
    }

    /**
     * @param Command $command
     *
     * @return bool
     */
    private function isCommandCacheable(Command $command)
    {
        $className = get_class($command);

        return isset(self::$commandsToCache[$className]);
    }
}
