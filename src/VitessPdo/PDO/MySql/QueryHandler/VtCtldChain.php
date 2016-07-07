<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler;

use VitessPdo\PDO\VtCtld\ClientInterface;

/**
 * Description of class VtCtldChain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
abstract class VtCtldChain extends Chain
{

    use DependencyTrait\VtCtld;

    /**
     * VtCtldMember constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->setClient($client);
        parent::__construct();
    }
}
