<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO;

use VitessPdo\PDO\Dsn\Config;
use VitessPdo\PDO\Dsn\Driver;

/**
 * Description of class Dsn
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class Dsn
{

    /**
     * @var string
     */
    private $dsnString;

    /**
     * @var Driver
     */
    private $driver;

    /**
     * @var Config
     */
    private $config;

    /**
     * Dsn constructor.
     */
    public function __construct($dsnString)
    {
        $this->dsnString = $dsnString;
        $this->parse();
    }

    /**
     * @return string
     */
    public function getDsnString()
    {
        return $this->dsnString;
    }

    /**
     * @return Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     *
     */
    private function parse()
    {
        $dsnParts = explode(":", $this->dsnString);

        if ((count($dsnParts)) !== 2) {
            throw new Exception("Invalid dsn string - exactly one colon has to be present.");
        }

        $this->driver = new Driver($dsnParts[0]);
        $this->config = new Config($dsnParts[1]);
    }
}
