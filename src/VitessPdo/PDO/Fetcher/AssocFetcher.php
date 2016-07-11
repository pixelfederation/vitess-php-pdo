<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Fetcher;

/**
 * Description of class AssocFetcher
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
class AssocFetcher extends SimpleFetcher
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
        return $this->filterRowsKeysByFunction($rows, 'is_string');
    }
}
