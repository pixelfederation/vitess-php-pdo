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

namespace VitessPdoTest\PDO;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\Exception as DriverException;
use Exception;

/**
 * Class DsnTest
 *
 * @package VitessPdoTest\PDO
 */
class DsnTest extends \PHPUnit_Framework_TestCase
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
