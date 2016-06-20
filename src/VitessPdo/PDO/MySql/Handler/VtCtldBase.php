<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Handler;

use VitessPdo\PDO\VtCtld\Client;

/**
 * Description of class VtCtldBase
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
abstract class VtCtldBase extends Base
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * Base constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
