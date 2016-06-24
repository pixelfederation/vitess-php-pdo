<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\MemberInterface;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;
use VitessPdo\PDO\VtCtld\Client;

/**
 * Description of class Chain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class Chain
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var MemberInterface
     */
    private $first;

    /**
     * Chain constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->initialize();
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function getResult(QueryInterface $query)
    {
        $query = new ShowQuery($query);

        return $this->first->handle($query);
    }

    /**
     *
     */
    private function initialize()
    {
        $this->first = new TablesMember($this->client);
        $this->first->setSuccessor($tableStatus = new TableStatusMember($this->client));
        $tableStatus->setSuccessor($collation = new CollationMember($this->client));
        $collation->setSuccessor(new CreateDatabaseMember());
    }
}
