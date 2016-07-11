<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Fetcher;

/**
 * Description of class KeyPairFetcher
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
class KeyPairFetcher implements FetcherInterface
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
        $pairedRows = [];

        foreach ($rows as $row) {
            $pairedRows[$row[0]] = $row[1];
        }

        return $pairedRows;
    }
}
