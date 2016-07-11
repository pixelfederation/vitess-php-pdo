<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Fetcher;

/**
 * Description of class SimpleFetcher
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
abstract class SimpleFetcher implements FetcherInterface
{

    /**
     * @param $rows
     * @param $function
     *
     * @return array
     */
    protected function filterRowsKeysByFunction($rows, $function)
    {
        if (empty($rows)) {
            return [];
        }

        $strKeyEmptyArray = array_flip(array_filter(array_keys($rows[0]), $function));

        return array_map(function ($row) use ($strKeyEmptyArray) {
            return array_intersect_key($row, $strKeyEmptyArray);
        }, $rows);
    }
}
