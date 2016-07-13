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

namespace VitessPdoTest\PDO\Dsn;

use VitessPdo\PDO\Dsn\Config;
use VitessPdo\PDO\Exception as DriverException;
use Exception;

/**
 * Class ConfigTest
 *
 * @package VitessPdoTest\PDO\Dsn
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testConstruct()
    {
        $configString = "keyspace=testdb;host=localhost;port=15991;cell=testcell";
        $config = null;

        try {
            $config = new Config($configString);
        } catch (Exception $e) {
            self::fail("Error while constructing the driver: " . $e->getMessage());
        }

        self::assertNotNull($config);
        self::assertEquals("localhost", $config->getHost());
        self::assertEquals("testdb", $config->getKeyspace());
        self::assertEquals(15991, $config->getPort());
        self::assertEquals('testcell', $config->getCell());
    }

    public function testBadConstruct()
    {
        $configString = "port=15991";
        $this->expectException(DriverException::class);
        $this->expectExceptionMessage('Invalid config - host missing.');

        new Config($configString);
    }

    public function testConstructWithoutKeyspace()
    {
        $configString = "host=localhost;port=15991";
        $config = null;

        try {
            $config = new Config($configString);
        } catch (Exception $e) {
            self::fail("Error while constructing the driver: " . $e->getMessage());
        }

        self::assertNotNull($config);
        self::assertEquals("localhost", $config->getHost());
        self::assertEquals("", $config->getKeyspace());
        self::assertEquals(15991, $config->getPort());
    }

    public function testParamMissing()
    {
        $configString = "host=localhost;param1";

        $this->expectException(DriverException::class);
        $this->expectExceptionMessageRegExp("/^Invalid parameter -.*/");
        new Config($configString);
    }

    public function testHostMissing()
    {
        $configString = "keyspace=testdb";

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage("Invalid config - host missing.");
        new Config($configString);
    }

    public function testPortIsNotInteger()
    {
        $configString = "host=localhost;dbname=testdb;port=asd";

        $this->expectException(DriverException::class);
        $this->expectExceptionMessage("Invalid config - port has to be a positive integer.");
        new Config($configString);
    }
}
