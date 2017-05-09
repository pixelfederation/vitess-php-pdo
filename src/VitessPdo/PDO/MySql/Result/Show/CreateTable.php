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

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\Result\VtCtldResult;
use VitessPdo\PDO\QueryAnalyzer\Query;
use VitessPdo\PDO\VtCtld\Result\GetSchema;

/**
 * Description of class CreateTable
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result\Show
 */
final class CreateTable extends VtCtldResult
{

    /**
     * @var array
     */
    protected static $fields = [
        self::KEY_TABLE,
        self::KEY_CREATE_TABLE,
    ];

    /**
     * @var string
     */
    private $table;

    const KEY_TABLE = 'Table';
    const KEY_CREATE_TABLE = 'Create Table';

    /**
     * Databases constructor.
     *
     * @param GetSchema $result
     * @param string    $table
     */
    public function __construct(GetSchema $result, $table)
    {
        $this->table = trim($table);
        parent::__construct($result);
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function transform($data)
    {
        if (!$data instanceof GetSchema\Schema) {
            throw new Exception('Data is not a Schema.');
        }

        $definition = $data->getTableDefinition($this->table);
        $defSchema = $definition->getSchema();

        $returnData = [];
        $row = [
            self::KEY_TABLE => $definition->getName(),
            0 => $definition->getName(),
            self::KEY_CREATE_TABLE => $defSchema,
            1 => $defSchema,
        ];
        $returnData[] = $row;

        return $returnData;
    }
}
