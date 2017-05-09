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
 * Description of class TableDefinition
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result\GetSchema
 */
class TableDefinition
{

    /**
     * @var array
     */
    private $data;

    /**
     * @const string
     */
    const KEY_NAME = 'name';

    /**
     * @const string
     */
    const KEY_SCHEMA = 'schema';

    /**
     * @const string
     */
    const KEY_COLUMNS = 'columns';

    /**
     * @const string
     */
    const KEY_PRIMARY_KEY_COLUMNS = 'primary_key_columns';

    /**
     * @const string
     */
    const KEY_TYPE = 'type';

    /**
     * @const string
     */
    const KEY_DATA_LENGTH = 'data_length';

    /**
     * TableDefinition constructor.
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
    public function getName()
    {
        if (!isset($this->data[self::KEY_NAME])) {
            throw new Exception('Name missing.');
        }

        return $this->data[self::KEY_NAME];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSchema()
    {
        if (!isset($this->data[self::KEY_SCHEMA])) {
            throw new Exception('Schema missing.');
        }

        return $this->data[self::KEY_SCHEMA];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getColumns()
    {
        if (!isset($this->data[self::KEY_COLUMNS])) {
            throw new Exception('Columns missing.');
        }

        return $this->data[self::KEY_COLUMNS];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPrimaryKeyColumns()
    {
        if (!isset($this->data[self::KEY_PRIMARY_KEY_COLUMNS])) {
            throw new Exception('Primary key columns missing.');
        }

        return $this->data[self::KEY_PRIMARY_KEY_COLUMNS];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getType()
    {
        if (!isset($this->data[self::KEY_TYPE])) {
            throw new Exception('Type missing.');
        }

        return $this->data[self::KEY_TYPE];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDataLength()
    {
        if (!isset($this->data[self::KEY_DATA_LENGTH])) {
            throw new Exception('Data length missing.');
        }

        return $this->data[self::KEY_DATA_LENGTH];
    }
}
