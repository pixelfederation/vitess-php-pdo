<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Fetcher;

/**
 * Description of class NumFetcher
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
class NumFetcher extends SimpleFetcher
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
        return $this->filterRowsKeysByFunction($rows, 'is_numeric');
    }
}
