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

namespace VitessPdo\PDO\MySql\Result\Show;

use VitessPdo\PDO\MySql\Result\VtCtldResult;
use VitessPdo\PDO\VtCtld\Result\GetKeyspaces;

/**
 * Description of class Databases
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result\Show
 */
final class Databases extends VtCtldResult
{

    /**
     * @var array
     */
    protected static $fields = ['Database', 0];

    /**
     * Databases constructor.
     *
     * @param GetKeyspaces $result
     */
    public function __construct(GetKeyspaces $result)
    {
        parent::__construct($result);
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    protected function transform($data)
    {
        return array_map(function ($keyspace) {
            return [
                'Database' => $keyspace,
                0 => $keyspace,
            ];
        }, $data);
    }
}
