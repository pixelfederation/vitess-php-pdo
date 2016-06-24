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
 * Description of class CollationMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class CollationMember extends VctldMember
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
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_COLLATION) {
            return null;
        }

        return $this->getResultFromData(self::$data, self::$fields);
    }
}
