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

use PHPSQLParser\Options;
use PHPSQLParser\PHPSQLParser;
use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\Result\VtCtldResult;
use VitessPdo\PDO\QueryAnalyzer\CreateQuery;
use VitessPdo\PDO\QueryAnalyzer\Query;
use VitessPdo\PDO\VtCtld\Result\GetSchema;

/**
 * Description of class IndexFrom
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result\Show
 */
final class IndexFrom extends VtCtldResult
{

    /**
     * @var array
     */
    protected static $fields = [
        self::KEY_TABLE,
        self::KEY_NON_UNIQUE,
        self::KEY_NAME,
        self::KEY_SEQ_IN_INDEX,
        self::KEY_COLUMN_NAME,
        self::KEY_COLLATION,
        self::KEY_CARDINALITY,
        self::KEY_SUB_PART,
        self::KEY_PACKED,
        self::KEY_NULL,
        self::KEY_INDEX_TYPE,
        self::KEY_COMMENT,
        self::KEY_INDEX_COMMENT,
    ];

    /**
     * @var array
     */
    private static $tplData = [
        self::KEY_TABLE => 'dummy',
        0 => 'dummy',
        self::KEY_NON_UNIQUE => '0',
        1 => '0',
        self::KEY_NAME => '',
        2 => '',
        self::KEY_SEQ_IN_INDEX => '0',
        3 => '0',
        self::KEY_COLUMN_NAME => '',
        4 => '',
        self::KEY_COLLATION => 'A',
        5 => 'A',
        self::KEY_CARDINALITY => '0',
        6 => '0',
        self::KEY_SUB_PART => null,
        7 => null,
        self::KEY_PACKED => null,
        8 => null,
        self::KEY_NULL => '',
        9 => '',
        self::KEY_INDEX_TYPE => 'BTREE',
        10 => 'BTREE',
        self::KEY_COMMENT => '',
        11 => '',
        self::KEY_INDEX_COMMENT => '',
        12 => '',
    ];

    /**
     * @var string
     */
    private $fromExpr;

    const KEY_TABLE = 'Table';
    const KEY_NON_UNIQUE = 'Non_unique';
    const KEY_NAME = 'Key_name';
    const KEY_SEQ_IN_INDEX = 'Seq_in_index';
    const KEY_COLUMN_NAME = 'Column_name';
    const KEY_COLLATION = 'Collation';
    const KEY_CARDINALITY = 'Cardinality';
    const KEY_SUB_PART = 'Sub_part';
    const KEY_PACKED = 'Packed';
    const KEY_NULL = 'Null';
    const KEY_INDEX_TYPE = 'Index_type';
    const KEY_COMMENT = 'Comment';
    const KEY_INDEX_COMMENT = 'Index_comment';

    /**
     * Databases constructor.
     *
     * @param GetSchema $result
     * @param string    $fromExpr
     */
    public function __construct(GetSchema $result, $fromExpr)
    {
        $this->fromExpr = trim($fromExpr);
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

        $definition = $data->getTableDefinition($this->fromExpr);
        $defSchema = $definition->getSchema();
        $parser = new PHPSQLParser(false, false, [Options::CONSISTENT_SUB_TREES => true]);
        $query = new CreateQuery(new Query($defSchema, $parser->parse($defSchema)));
        $createExpr = $query->getObjectExpression();
        $indices = $createExpr->getIndices();

        $returnData = [];

        /* @var  $index Query\IndexExpression */
        foreach ($indices as $index) {
            foreach ($index->getColumns() as $columnIndex => $column) {
                $row = self::$tplData;
                $row[self::KEY_TABLE] = $row[0] = $definition->getName();
                $row[self::KEY_NON_UNIQUE] = $row[1] = $index->getNonUnique();
                $row[self::KEY_NAME] = $row[2] = $index->getKeyName();
                $row[self::KEY_SEQ_IN_INDEX] = $row[3] = $columnIndex + 1;
                $row[self::KEY_COLUMN_NAME] = $row[4] = $column->getColumnName();
                $row[self::KEY_NULL] = $row[9] = $column->getColumnType()->isNullable() ? 'YES' : '';

                $returnData[] = $row;
            }
        }

        return $returnData;
    }
}
