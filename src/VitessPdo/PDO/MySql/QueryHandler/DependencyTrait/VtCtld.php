<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\DependencyTrait;

use VitessPdo\PDO\VtCtld\ClientInterface;

/**
 * Description of trait VtCtld
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\QueryHandler\DependencyTrait
 */
trait VtCtld
{

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * VtCtldMember constructor.
     *
     * @param ClientInterface $client
     */
    private function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }
}
