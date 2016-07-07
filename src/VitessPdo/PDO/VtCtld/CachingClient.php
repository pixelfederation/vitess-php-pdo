<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
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
