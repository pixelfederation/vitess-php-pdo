<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Command\Command;

/**
 * Description of class ClientDecorator
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld
 */
abstract class ClientDecorator implements ClientInterface
{

    /**
     * @var ClientInterface
     */
    private $decoratedClient;

    /**
     * ClientDecorator constructor.
     *
     * @param ClientInterface $decoratedClient
     */
    public function __construct(ClientInterface $decoratedClient)
    {
        $this->decoratedClient = $decoratedClient;
    }

    /**
     * @param Command $command
     *
     * @return Result\Result
     * @throws Exception
     */
    public function executeCommand(Command $command)
    {
        return $this->getDecoratedClient()->executeCommand($command);
    }

    /**
     * @return ClientInterface
     */
    protected function getDecoratedClient()
    {
        return $this->decoratedClient;
    }
}
