<?php
/**
 * Copyright 2017 PIXELFEDERATION s.r.o.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VitessPdo\PDO\Dsn;

use VitessPdo\PDO\Exception;

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
