<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\Fetcher;

use PDO as CorePDO;
use VitessPdo\PDO\Exception;

/**
 * Description of class Factory
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
class Factory
{

    /**
     * @var array
     */
    private $styleToFetcherMap = [
        CorePDO::FETCH_BOTH => BothFetcher::class,
        CorePDO::FETCH_NUM => NumFetcher::class,
        CorePDO::FETCH_ASSOC => AssocFetcher::class,
        CorePDO::FETCH_COLUMN => ColumnFetcher::class,
        CorePDO::FETCH_KEY_PAIR => KeyPairFetcher::class
    ];

    /**
     * @var array
     */
    private $fetchers = [];

    /**
     * @param int $fetchMode
     *
     * @return FetcherInterface
     * @throws Exception
     */
    public function getByFetchStyle($fetchMode)
    {
        if (!isset($this->fetchers[$fetchMode])) {
            if (!isset($this->styleToFetcherMap[$fetchMode])) {
                throw new Exception('Fetch style not supported.');
            }

            $this->fetchers[$fetchMode] = new $this->styleToFetcherMap[$fetchMode]();
        }

        return $this->fetchers[$fetchMode];
    }
}
