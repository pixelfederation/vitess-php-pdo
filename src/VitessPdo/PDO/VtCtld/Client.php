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
