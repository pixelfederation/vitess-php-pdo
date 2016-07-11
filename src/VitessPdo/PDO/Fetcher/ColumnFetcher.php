<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Fetcher;

/**
 * Description of class ColumnFetcher
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
class ColumnFetcher implements FetcherInterface
{

    /**
     *
     * @param array $rows
     * @param FetchConfig $fetchConfig
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetchAll(array $rows, FetchConfig $fetchConfig)
    {
        $colIndex = $fetchConfig->getFetchArgument();

        return array_map(function ($row) use ($colIndex) {
            return isset($row[$colIndex]) ? $row[$colIndex] : null;
        }, $rows);
    }
}
