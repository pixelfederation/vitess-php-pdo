<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\TypeChain;

use VitessPdo\PDO\MySql\QueryHandler\DependencyTrait;
use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\MySql\QueryHandler\Chain as AbstractChain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\VtCtld\Client;

/**
 * Description of class Chain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class Chain extends AbstractChain
{

    use DependencyTrait\Dsn;
    use DependencyTrait\Vctld;

    /**
     * Chain constructor.
     * @param Dsn $dsn
     * @param Client $client
     */
    public function __construct(Dsn $dsn, Client $client)
    {
        $this->setDsn($dsn);
        $this->setClient($client);
        parent::__construct();
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     */
    public function getResult(QueryInterface $query)
    {
        return $this->first->handle($query);
    }

    /**
     *
     */
    protected function initialize()
    {
        $this->first = new UseMember($this->dsn);
        $this->first->setSuccessor($show = new ShowMember($this->client));
        $show->setSuccessor(new SelectMember());
    }
}
