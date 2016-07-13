<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the PIXEL FEDERATION, s.r.o. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL PIXEL FEDERATION, s.r.o. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
    private $cell;

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
    private $keyspace = '';

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
    const CONFIG_CELL = 'cell';

    /**
     * @const string
     */
    const CONFIG_KEYSPACE = 'keyspace';

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
    public function getCell()
    {
        return $this->cell;
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
    public function getKeyspace()
    {
        return $this->keyspace;
    }

    /**
     * @param string $keyspace
     *
     * @return Config
     */
    public function setKeyspace($keyspace)
    {
        $this->keyspace = $keyspace;

        return $this;
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
        return $this->getVtCtlHost() && $this->getVtCtlPort() && $this->getCell();
    }

    /**
     * @throws Exception
     */
    private function validate()
    {
        $config = $this->parse();
        $this->setCell($config);
        $this->setHost($config);
        $this->setPort($config);
        $this->setConfigKeyspace($config);
        $this->setVtCtlHost($config);
        $this->setVtCtlPort($config);
    }

    /**
     * @param array $config
     */
    private function setCell(array $config)
    {
        if (!isset($config[self::CONFIG_CELL]) || trim($config[self::CONFIG_CELL]) === "") {
            return;
        }

        $this->cell = trim($config[self::CONFIG_CELL]);
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
    private function setConfigKeyspace(array $config)
    {
        if (!isset($config[self::CONFIG_KEYSPACE]) || trim($config[self::CONFIG_KEYSPACE]) === "") {
            return;
        }

        $this->keyspace = trim($config[self::CONFIG_KEYSPACE]);
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
