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

namespace VitessPdo\PDO\VtCtld\Command;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Command\Parameter\NamedParameter;
use VitessPdo\PDO\VtCtld\Command\Parameter\Parameter;

/**
 * Description of class ApplySchema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command
 */
class ApplySchema extends DsnDependent
{

    /**
     * @const string
     */
    const PARAM_KEYSPACE = 'keyspace';

    /**
     * @const string
     */
    const PARAM_DDL_SQL = 'sql';

    /**
     * GetSchema constructor.
     *
     * @param string $sql
     */
    public function __construct($sql)
    {
        $this->set(self::PARAM_DDL_SQL, new NamedParameter(self::PARAM_DDL_SQL, $sql));
    }

    /**
     * @param Dsn $dsn
     *
     * @return DsnDependent
     * @throws Exception
     */
    public function setDsn(Dsn $dsn)
    {
        $this->dsn = $dsn;
        $keyspace = $dsn->getConfig()->getKeyspace();

        if (!$keyspace) {
            throw new Exception("SQLSTATE[3D000]: Invalid catalog name: 1046 No database selected");
        }

        $this->set(self::PARAM_KEYSPACE, new Parameter(self::PARAM_KEYSPACE, $keyspace));

        return $this;
    }
}
