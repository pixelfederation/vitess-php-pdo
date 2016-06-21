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
 * Description of class ShowCollation
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class ShowCollation extends Base
{

    /**
     * @var array
     */
    private static $data = [
        [
            'Collation' => 'utf8_bin',
            0 => 'utf8_bin',
            'Charset' => 'utf8',
            1 => 'utf8',
            'Id' => '83',
            2 => '83',
            'Default' => 'Yes',
            3 => 'Yes',
            'Compiled' => 'Yes',
            4 => 'Yes',
            'Sortlen' => '1',
            5 => '1',
        ],
    ];

    /**
     * @var array
     */
    private static $fields = [
        'Collation',
        'Charset',
        'Id',
        'Default',
        'Compiled',
        'Sortlen',
    ];

    /**
     * @param Query $query
     *
     * @return Result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getResult(Query $query)
    {
        $cursor = new Cursor(self::$data, self::$fields);

        return new Result($cursor);
    }
}
