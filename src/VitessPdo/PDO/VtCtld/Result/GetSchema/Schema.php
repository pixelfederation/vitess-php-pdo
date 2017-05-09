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

namespace VitessPdo\PDO\VtCtld\Result\GetSchema;

use VitessPdo\PDO\Exception;

/**
 * Description of class Schema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result\GetSchema
 */
final class Schema
{

    /**
     * @var array
     */
    private $data;

    /**
     * @var TableDefinition[]
     */
    private $definitions;

    /**
     * @const string
     */
    const KEY_DATABASE_SCHEMA = 'database_schema';

    /**
     * @const string
     */
    const KEY_TABLE_DEFINITIONS = 'table_definitions';

    /**
     * Schema constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDatabaseSchema()
    {
        if (!isset($this->data[self::KEY_DATABASE_SCHEMA])) {
            throw new Exception('Database schema missing.');
        }

        return $this->data[self::KEY_DATABASE_SCHEMA];
    }

    /**
     * @return array|TableDefinition[]
     * @throws Exception
     */
    public function getTableDefinitions()
    {
        if (!isset($this->data[self::KEY_TABLE_DEFINITIONS])) {
            throw new Exception('Table definitions schema missing.');
        }

        if ($this->definitions === null) {
            $this->definitions = array_map(function (array $definitionData) {
                return new TableDefinition($definitionData);
            }, $this->data[self::KEY_TABLE_DEFINITIONS]);
        }

        return $this->definitions;
    }

    /**
     * @param string $table
     *
     * @return null|TableDefinition
     * @throws Exception
     */
    public function getTableDefinition($table)
    {
        foreach ($this->getTableDefinitions() as $definition) {
            if ($definition->getName() === $table) {
                return $definition;
            }
        }

        return null;
    }
}
