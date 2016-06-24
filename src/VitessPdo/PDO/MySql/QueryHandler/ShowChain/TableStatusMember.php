<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\VctldMember;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;

/**
 * Description of class TableStatusMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class TableStatusMember extends VctldMember
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
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_TABLE_STATUS) {
            return null;
        }

        $data = $this->prepareData();
        $likeExpr = $query->getLikeExpression();

        if ($likeExpr) {
            $data = $this->filterData($data, $likeExpr);
        }

        return $this->getResultFromData($data, self::$fields);
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function prepareData()
    {
        $vtCtldResult = $this->client->getVSchema();

        $tplData = self::$tplData;

        return array_map(function ($row) use ($tplData) {
            $tmp = $tplData;
            $tmp['Name'] = $tmp[0] = $row[0];

            return $tmp;
        }, $vtCtldResult->getData());
    }

    /**
     * @param array $data
     * @param string $likeExpr
     *
     * @return array
     */
    protected function filterData(array $data, $likeExpr)
    {
        $pattern = '/^' . str_replace('%', '.*', trim($likeExpr, "'")) . '$/';
        $newData = [];

        foreach ($data as $row) {
            if (preg_match($pattern, $row['Name'])) {
                $newData[] = $row;
            }
        }

        return $newData;
    }
}
