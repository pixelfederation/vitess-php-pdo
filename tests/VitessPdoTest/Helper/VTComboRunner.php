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

namespace VitessPdoTest\Helper;

use RuntimeException;
use Grpc\ChannelCredentials;

/**
 * Class VTComboRunner
 *
 * @author  mfris
 * @package VitessPdoTest\Helper
 */
final class VTComboRunner
{

    /**
     * @var resource
     */
    private $process;

    /**
     * @const string
     */
    const HOST = 'localhost';

    /**
     * @const string
     */
    const PORT = '12346';

    /**
     * @const string
     */
    const KEYSPACE1 = 'user';

    /**
     * @const string
     */
    const KEYSPACE2 = 'lookup';

    /**
     * @const string
     */
    const CELL = 'test';

    /**
     * @throws RuntimeException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function run()
    {
        $vtRoot = getenv('VTROOT');
        if (!$vtRoot) {
            throw new RuntimeException('VTROOT env var not set; make sure to source dev.env');
        }

        $vtMysqlRoot = getenv('VT_MYSQL_ROOT');
        if (!$vtMysqlRoot) {
            throw new RuntimeException('VT_MYSQL_ROOT env var not set; make sure to set the path to Mysql/MariaDb');
        }

        $tmpDir = dirname(__DIR__ . '/../../vitess/.');
        $cmd = $tmpDir . '/run.py > /dev/null 2> /dev/null';

        $process = proc_open($cmd, [], $pipes, $tmpDir);
        if (!$process) {
            throw new RuntimeException("Failed to start mock vtgate server with command: $cmd");
        }
        $this->process = $process;

        // Wait for connection to be accepted.
        do {
            usleep(100000);
            $connection = @fsockopen(self::HOST, self::PORT);
        } while (!is_resource($connection));

        fclose($connection);
    }

    /**
     *
     */
    public function stop()
    {
        if (!$this->process) {
            return;
        }

        //
        /** @see http://php.net/manual/en/function.proc-terminate.php#81353 */
        $status = proc_get_status($this->process);
        $ppid = $status['pid'];
        $pidsOutput = trim(shell_exec("pgrep -P {$ppid}"));
        $pids = preg_split('/\s+/', $pidsOutput);

        foreach ($pids as $pid) {
            if (!is_numeric($pid)) {
                continue;
            }

            posix_kill($pid, SIGTERM);
        }

        proc_close($this->process);
        $this->process = null;
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->stop();
    }
}
