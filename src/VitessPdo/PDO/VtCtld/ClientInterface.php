<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */
namespace VitessPdo\PDO\VtCtld;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Command\Command;
use VitessPdo\PDO\VtCtld\Result\Result;

/**
 * Description of class Client
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld
 */
interface ClientInterface
{

    /**
     * @param Command $command
     *
     * @return Result
     * @throws Exception
     */
    public function executeCommand(Command $command);
}
