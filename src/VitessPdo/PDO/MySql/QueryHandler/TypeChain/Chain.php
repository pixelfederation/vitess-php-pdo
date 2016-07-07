<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\TypeChain;

use VitessPdo\PDO\MySql\QueryHandler\DependencyTrait;
use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\MySql\QueryHandler\Chain as AbstractChain;
use VitessPdo\PDO\VtCtld\ClientInterface;

/**
 * Description of class Chain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class Chain extends AbstractChain
{

    use DependencyTrait\Dsn;
    use DependencyTrait\VtCtld;

    /**
     * Chain constructor.
     * @param Dsn $dsn
     * @param ClientInterface $client
     */
    public function __construct(Dsn $dsn, ClientInterface $client)
    {
        $this->setDsn($dsn);
        $this->setClient($client);
        parent::__construct();
    }

    /**
     *
     */
    protected function initialize()
    {
        $this->first = new UseMember($this->dsn, $this->client);
        $this->first->setSuccessor($show = new ShowMember($this->client));
        $show->setSuccessor($select = new SelectMember());
        $select->setSuccessor($create = new CreateMember($this->client));
        $create->setSuccessor($alter = new AlterMember($this->client));
        $alter->setSuccessor(new DropMember($this->client));
    }
}
