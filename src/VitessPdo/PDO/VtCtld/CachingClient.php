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
