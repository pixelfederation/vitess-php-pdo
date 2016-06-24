<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\DependencyTrait;

use VitessPdo\PDO\VtCtld\Client;

/**
 * Description of trait Vctld
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\QueryHandler\DependencyTrait
 */
trait Vctld
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * VctldMember constructor.
     *
     * @param Client $client
     */
    private function setClient(Client $client)
    {
        $this->client = $client;
    }
}
