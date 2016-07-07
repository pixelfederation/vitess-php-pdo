<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Command\Command;
use VitessPdo\PDO\VtCtld\Command\DsnDependent;
use VitessPdo\PDO\VtCtld\Result\Result;

/**
 * Description of class Client
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld
 */
final class Client implements ClientInterface
{

    /**
     * @var Dsn
     */
    private $dsn;

    /**
     * @var string
     */
    private $serverString;

    /**
     * @const string
     */
    const VTCTLD_EXECUTABLE = 'vtctlclient';

    /**
     * Client constructor.
     *
     * @param Dsn $dsn
     */
    public function __construct(Dsn $dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * @param Command $command
     *
     * @return Result
     * @throws Exception
     */
    public function executeCommand(Command $command)
    {
        if ($command instanceof DsnDependent) {
            $command->setDsn($this->dsn);
        }

        $shellCmd = self::VTCTLD_EXECUTABLE . ' ' . $this->getServerString() . ' '
             . $command->getName() . ' ' . implode(' ', $command->getParams());

        exec($shellCmd, $output, $return);
        $output = implode("\n", $output);

        if ($return !== 0) {
            throw new Exception('Error running vtctld command - ' . $shellCmd . ', error: ' . $output);
        }

        $resultClass = $command->getResultClass();

        return new $resultClass($this->dsn, $output);
    }

    /**
     * @return string
     */
    private function getServerString()
    {
        if ($this->serverString === null) {
            $config             = $this->dsn->getConfig();
            $this->serverString = '--server ' . $config->getVtCtlHost() . ':' . $config->getVtCtlPort();
        }

        return $this->serverString;
    }
}
