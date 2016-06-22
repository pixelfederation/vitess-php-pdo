<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Handler;

use VitessPdo\PDO\MySql\Cursor\Cursor;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\Query;

/**
 * Description of class ShowTableStatus
 *
 * @todo try to get some data from vttablet schemaz api
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class ShowTableStatus extends VtCtldBase
{

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
     * @var array
     */
    private static $fields = [
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
     * @param Query $query
     *
     * @return Result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getResult(Query $query)
    {
        $vtCtldResult = $this->client->getVSchema();

        $tplData = self::$tplData;
        $data = array_map(function ($row) use ($tplData) {
            $tmp = $tplData;
            $tmp['Name'] = $tmp[0] = $row[0];

            return $tmp;
        }, $vtCtldResult->getData());

        $cursor = new Cursor($data, self::$fields);

        return new Result($cursor);
    }
}
