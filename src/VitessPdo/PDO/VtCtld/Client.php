<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Result\GetKeyspaces;
use VitessPdo\PDO\VtCtld\Result\GetVSchema;

/**
 * Description of class Client
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld
 */
final class Client
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
     * @const string
     */
    const COMMAND_GET_VSCHEMA = 'GetVSchema';

    /**
     * @const string
     */
    const COMMAND_GET_KEYSPACES = 'GetKeyspaces';

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
     * @return GetVSchema
     * @throws Exception
     */
    public function getVSchema()
    {
        $keyspace = $this->dsn->getConfig()->getKeyspace();

        if (!$keyspace) {
            throw new Exception("SQLSTATE[3D000]: Invalid catalog name: 1046 No database selected");
        }

        $output = $this->executeCommand(self::COMMAND_GET_VSCHEMA, [$keyspace]);

        return new GetVSchema($keyspace, $output);
    }

    /**
     * @return GetKeyspaces
     * @throws Exception
     */
    public function getKeyspaces()
    {
        $output = $this->executeCommand(self::COMMAND_GET_KEYSPACES);

        return new GetKeyspaces($output);
    }

    /**
     * @param string $command
     * @param array $params
     *
     * @return string
     * @throws Exception
     */
    private function executeCommand($command, array $params = [])
    {
        $params = array_map(function ($param) {
            return escapeshellcmd($param);
        }, $params);

        $cmd = self::VTCTLD_EXECUTABLE . ' ' . $this->getServerString() . ' ' . $command . ' ' . implode(" ", $params);

        $output = shell_exec($cmd);

        if ($output === null) {
            throw new Exception("Invalid vtctld command - " . $cmd);
        }

        return $output;
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
