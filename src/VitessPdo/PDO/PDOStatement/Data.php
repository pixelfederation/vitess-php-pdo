<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\PDOStatement;

use PDO as CorePDO;
use VitessPdo\PDO\Fetcher\FetchConfig;
use VitessPdo\PDO\Fetcher\FetcherInterface;

/**
 * Description of class Data
 *
 * @author  mfris
 * @package VitessPdo\PDO\PDOStatement
 */
class Data
{

    /**
     * @var array
     */
    private $rows = [];

    /**
     * Data constructor.
     *
     * @param array $rows
     */
    public function __construct(array $rows)
    {
        $this->rows[CorePDO::FETCH_BOTH] = $rows;
    }

    /**
     * @param FetcherInterface $fetcher
     * @param FetchConfig $fetchConfig
     *
     * @return array
     */
    public function fetchAll(FetcherInterface $fetcher, FetchConfig $fetchConfig)
    {
        $fetchStyle = $fetchConfig->getFetchStyle();

        if (!$this->hasRowsForFetchStyle($fetchStyle)) {
            $this->rows[$fetchStyle] = $fetcher->fetchAll($this->rows[CorePDO::FETCH_BOTH], $fetchConfig);
        }

        return $this->rows[$fetchStyle];
    }

    /**
     * @param $fetchStyle
     *
     * @return bool
     */
    private function hasRowsForFetchStyle($fetchStyle)
    {
        return isset($this->rows[$fetchStyle]);
    }
}
