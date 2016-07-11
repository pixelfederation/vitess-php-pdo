<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Fetcher;

/**
 * Description of interface FetcherInterface
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
interface FetcherInterface
{

    /**
     *
     * @param array $rows
     * @param FetchConfig $fetchConfig
     *
     * @return array
     */
    public function fetchAll(array $rows, FetchConfig $fetchConfig);
}
