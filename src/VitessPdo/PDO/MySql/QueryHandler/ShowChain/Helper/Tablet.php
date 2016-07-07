<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain\Helper;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\DependencyTrait\VtCtld;
use VitessPdo\PDO\VtCtld\ClientInterface;
use VitessPdo\PDO\VtCtld\Command\ListAllTablets;
use VitessPdo\PDO\VtCtld\Result\ListAllTablets as ListAllTabletsResult;

/**
 * Description of class Tablet
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\QueryHandler\ShowChain\Helper
 */
class Tablet
{

    use VtCtld;

    /**
     * Tablet constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->setClient($client);
    }

    /**
     * @return ListAllTabletsResult\Tablet
     * @throws Exception
     */
    public function getTablet()
    {
        $command = new ListAllTablets();
        /* @var $vtCtldResult ListAllTabletsResult */
        $vtCtldResult = $this->client->executeCommand($command);

        $tablets = $vtCtldResult->getDataForCurrentKeyspace();

        return $tablets[0];
    }
}
