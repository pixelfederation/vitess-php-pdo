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
use VitessPdo\PDO\VtCtld\Result\GetSchema;

/**
 * Description of class TableStatus
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result\Show
 */
final class TableStatus extends VtCtldResult
{

    /**
     * @var array
     */
    protected static $fields = [
        'Name',
        'Engine',
        'Version',
        'Row_format',
        'Rows',
        'Avg_row_length',
        'Data_length',
        'Max_data_length',
        'Index_length',
        'Data_free',
        'Auto_increment',
        'Create_time',
        'Update_time',
        'Check_time',
        'Collation',
        'Checksum',
        'Create_options',
        'Comment',
    ];

    /**
     * @var array
     */
    private static $tplData = [
        'Name' => 'dummy',
        0 => 'dummy',
        'Engine' => 'InnoDB',
        1 => 'InnoDB',
        'Version' => '10',
        2 => '10',
        'Row_format' => 'Compact',
        3 => 'Compact',
        'Rows' => '0',
        4 => '0',
        'Avg_row_length' => '0',
        5 => '0',
        'Data_length' => '0',
        6 => '0',
        'Max_data_length' => '0',
        7 => '0',
        'Index_length' => '0',
        8 => '0',
        'Data_free' => '0',
        9 => '0',
        'Auto_increment' => null,
        10 => null,
        'Create_time' => '2016-06-15 13:12:59',
        11 => '2016-06-15 13:12:59',
        'Update_time' => null,
        12 => null,
        'Check_time' => null,
        13 => null,
        'Collation' => 'utf8_bin',
        14 => 'utf8_bin',
        'Checksum' => null,
        15 => null,
        'Create_options' => '',
        16 => '',
        'Comment' => '',
        17 => '',
    ];

    /**
     * @var string
     */
    private $likeExpr;

    /**
     * Databases constructor.
     *
     * @param GetSchema $result
     * @param string     $fromExpr
     */
    public function __construct(GetSchema $result, $fromExpr = '')
    {
        $this->likeExpr = trim($fromExpr);
        parent::__construct($result);
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     */
    protected function transform($data)
    {
        if (!$data instanceof GetSchema\Schema) {
            throw new Exception('Schema instance missing.');
        }

        $returnData = [];

        foreach ($data->getTableDefinitions() as $definition) {
            $tmp = self::$tplData;
            $tmp['Name'] = $tmp[0] = $definition->getName();
            $returnData[] = $tmp;
        }

        if ($this->likeExpr !== '') {
            $returnData = $this->filterData($returnData);
        }

        return array_values($returnData);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function filterData(array $data)
    {
        $pattern = '/^' . str_replace('%', '.*', trim($this->likeExpr, "'")) . '$/';
        $newData = [];

        foreach ($data as $row) {
            if (preg_match($pattern, $row['Name'])) {
                $newData[] = $row;
            }
        }

        return $newData;
    }
}
