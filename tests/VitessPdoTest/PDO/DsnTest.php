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

namespace VitessPdoTest\PDO;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\Exception as DriverException;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class DsnTest
 *
 * @package VitessPdoTest\PDO
 */
class DsnTest extends TestCase
{

    /**
     *
     */
    public function testConstruct()
    {
        $dsnString = "vitess:keyspace=testdb;host=localhost;port=15991;cell=testcell";
        $dsn = null;

        try {
            $dsn = new Dsn($dsnString);
        } catch (Exception $e) {
            self::fail("Error while constructing Dsn object: " . $e->getMessage());
        }

        self::assertNotNull($dsn);
        self::assertNotNull($dsn->getDriver());
        self::assertNotNull($dsn->getConfig());
    }

    /**
     *
     */
    public function testWrongDsn1()
    {
        $dsnString = "vitess-keyspace=testdb;host=localhost;port=15991";

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage("Invalid dsn string - exactly one colon has to be present.");
        new Dsn($dsnString);
    }

    /**
     *
     */
    public function testVtctld()
    {
        $dsnString = "vitess:keyspace=testdb;host=localhost;port=15991;cell=testcell;"
                   . "vtctld_host=localhost;vtctld_port=12345";
        $dsn = null;

        try {
            $dsn = new Dsn($dsnString);
        } catch (Exception $e) {
            self::fail("Error while constructing Dsn object: " . $e->getMessage());
        }

        self::assertNotNull($dsn);
        self::assertNotNull($dsn->getConfig());
        self::assertNotNull($dsn->getConfig()->getVtCtlHost());
        self::assertEquals('localhost', $dsn->getConfig()->getVtCtlHost());
        self::assertNotNull($dsn->getConfig()->getVtCtlPort());
        self::assertEquals(12345, $dsn->getConfig()->getVtCtlPort());
    }
}
