<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\Dsn;

use VitessPdo\PDO\Exception;

/**
 * Description of class Config
 *
 * @author  mfris
 * @package VitessPdo\PDO\Dsn
 */
class Config
{

    /**
     * @var string
     */
    private $configString;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port = 15991;

    /**
     * @var string
     */
    private $dbName;

    /**
     * @var string
     */
    private $vtCtlHost;

    /**
     * @var int
     */
    private $vtCtlPort;

    /**
     * @const string
     */
    const CONFIG_DBNAME = 'dbname';

    /**
     * @const string
     */
    const CONFIG_HOST = 'host';

    /**
     * @const string
     */
    const CONFIG_PORT = 'port';

    /**
     * @const string
     */
    const CONFIG_VTCTLD_HOST = 'vtctld_host';

    /**
     * @const string
     */
    const CONFIG_VTCTLD_PORT = 'vtctld_port';

    /**
     * Config constructor.
     *
     * @var string $configString
     */
    public function __construct($configString)
    {
        $this->configString = $configString;
        $this->validate();
    }

    /**
     * @return string
     */
    public function getConfigString()
    {
        return $this->configString;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @return string
     */
    public function getVtCtlHost()
    {
        return $this->vtCtlHost;
    }

    /**
     * @return int
     */
    public function getVtCtlPort()
    {
        return $this->vtCtlPort;
    }

    /**
     * @return bool
     */
    public function hasVtCtldData()
    {
        return $this->getVtCtlHost() && $this->getVtCtlPort();
    }

    /**
     * @throws Exception
     */
    private function validate()
    {
        $config = $this->parse();
        $this->setHost($config);
        $this->setPort($config);
        $this->setDbName($config);
        $this->setVtCtlHost($config);
        $this->setVtCtlPort($config);
    }

    /**
     * @param array $config
     *
     * @throws Exception
     */
    private function setHost(array $config)
    {
        if (!isset($config[self::CONFIG_HOST]) || trim($config[self::CONFIG_HOST]) === "") {
            throw new Exception("Invalid config - host missing.");
        }

        $this->host = trim($config[self::CONFIG_HOST]);
    }

    /**
     * @param array $config
     * @throws Exception
     */
    private function setPort(array $config)
    {
        if (!isset($config[self::CONFIG_PORT])) {
            return;
        }

        $port = (int) $config[self::CONFIG_PORT];

        if ($port <= 0) {
            throw new Exception("Invalid config - port has to be a positive integer.");
        }

        $this->port = $port;
    }

    /**
     * @param array $config
     *
     * @throws Exception
     */
    private function setDbName(array $config)
    {
        if (!isset($config[self::CONFIG_DBNAME]) || trim($config[self::CONFIG_DBNAME]) === "") {
            throw new Exception("Invalid config - db name missing.");
        }

        $this->dbName = trim($config[self::CONFIG_DBNAME]);
    }

    /**
     * @param array $config
     */
    private function setVtCtlHost(array $config)
    {
        if (!isset($config[self::CONFIG_VTCTLD_HOST]) || trim($config[self::CONFIG_VTCTLD_HOST]) === "") {
            return;
        }

        $this->vtCtlHost = trim($config[self::CONFIG_VTCTLD_HOST]);
    }

    /**
     * @param array $config
     * @throws Exception
     */
    private function setVtCtlPort(array $config)
    {
        if (!isset($config[self::CONFIG_VTCTLD_PORT])) {
            return;
        }

        $port = (int) $config[self::CONFIG_VTCTLD_PORT];

        if ($port <= 0) {
            throw new Exception("Invalid config - port has to be a positive integer.");
        }

        $this->vtCtlPort = $port;
    }

    /**
     * @return array
     * @throws
     */
    private function parse()
    {
        if ($this->configString === "") {
            return [];
        }

        $configStrings = explode(";", $this->configString);
        $configArray = [];

        foreach ($configStrings as $configParamString) {
            $tmp = explode("=", $configParamString);

            if (count($tmp) !== 2) {
                throw new Exception("Invalid parameter - {$configParamString}");
            }

            $configArray[$tmp[0]] = $tmp[1];
        }

        return $configArray;
    }
}
