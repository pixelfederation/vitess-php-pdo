<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler;

use VitessPdo\PDO\VtCtld\Client;

/**
 * Description of class VctldChain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
abstract class VctldChain extends Chain
{

    use DependencyTrait\Vctld;

    /**
     * VctldMember constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->setClient($client);
        parent::__construct();
    }
}
