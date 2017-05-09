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

namespace VitessPdo\PDO\Vitess;

use Vitess\Proto\Topodata\TabletType;

/**
 * Description of class ClusterConfig
 *
 * @author  mfris
 * @package VitessPdo\PDO\Vitess
 */
class ClusterConfig
{

    /**
     * @var int
     */
    private $readFrom = TabletType::REPLICA;

    /**
     * @var string
     */
    private $readFromReadable = self::READ_FROM_REPLICA;

    /**
     * @const string
     */
    const READ_FROM_REPLICA = 'replica';

    /**
     * @const string
     */
    const READ_FROM_MASTER = 'master';

    /**
     * @return ClusterConfig
     */
    public function readFromMaster()
    {
        $this->readFrom = TabletType::MASTER;
        $this->readFromReadable = self::READ_FROM_MASTER;

        return $this;
    }

    /**
     * @return ClusterConfig
     */
    public function readFromReplica()
    {
        $this->readFrom = TabletType::REPLICA;
        $this->readFromReadable = self::READ_FROM_REPLICA;

        return $this;
    }

    /**
     * @return int
     */
    public function getReadFrom()
    {
        return $this->readFrom;
    }

    /**
     * @return string
     */
    public function getReadFromHumanReadable()
    {
        return $this->readFromReadable;
    }
}
