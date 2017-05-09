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
