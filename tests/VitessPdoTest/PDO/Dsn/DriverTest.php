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

namespace VitessPdoTest\PDO\Dsn;

use VitessPdo\PDO\Dsn\Driver;
use VitessPdo\PDO\Exception as DriverException;
use Exception;

/**
 * Class DriverTest
 *
 * @package VitessPdoTest\PDO\Dsn
 */
class DriverTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testConstruct()
    {
        $protocol = "vitess";
        $driver = null;

        try {
            $driver = new Driver($protocol);
        } catch (Exception $e) {
            self::fail("Error while constructing the driver: " . $e->getMessage());
        }

        self::assertNotNull($driver);
        self::assertEquals("vitess", $driver->getProtocol());
    }

    /**
     *
     */
    public function testIncorrectConstruct()
    {
        $protocol = "mysql";

        $this->expectException(DriverException::class);
        new Driver($protocol);
    }
}
