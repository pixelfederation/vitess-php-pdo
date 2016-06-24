<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\MySql;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\MySql\QueryHandler\TypeChain\Chain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\PDOStatement;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
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
        $this->client = new Client($dsn);
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
