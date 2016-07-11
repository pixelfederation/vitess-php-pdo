<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Fetcher;

/**
 * Description of class BothFetcher
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
class BothFetcher implements FetcherInterface
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
        return $rows;
    }
}
