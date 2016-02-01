<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\PDOStatement;

use VitessPdo\PDO\Exception;
use Vitess\Cursor as VitessCursor;
use PDO as CorePDO;

/**
 * Description of class Cursor
 *
 * @author  mfris
 * @package VitessPdo\PDO\PDOStatement
 */
class Cursor
{

    /**
     * @var VitessCursor
     */
    private $vitessCursor;

    /**
     * @var int
     */
    private $rowIndex = -1;

    /**
     * @var Data
     */
    private $data;

    /**
     * @var array
     */
    private static $supportedFetchAllStyles = [
        CorePDO::FETCH_BOTH => CorePDO::FETCH_BOTH,
        CorePDO::FETCH_ASSOC => CorePDO::FETCH_ASSOC,
        CorePDO::FETCH_NUM => CorePDO::FETCH_NUM,
        CorePDO::FETCH_COLUMN => CorePDO::FETCH_COLUMN,
    ];

    /**
     * @var array
     */
    private static $supportedFetchStyles = [
        CorePDO::FETCH_BOTH => CorePDO::FETCH_BOTH,
        CorePDO::FETCH_ASSOC => CorePDO::FETCH_ASSOC,
        CorePDO::FETCH_NUM => CorePDO::FETCH_NUM,
    ];

    /**
     * Cursor constructor.
     *
     * @param VitessCursor $vitessCursor
     */
    public function __construct(VitessCursor $vitessCursor)
    {
        $this->vitessCursor = $vitessCursor;
    }

    /**
     * @param int   $fetchStyle
     * @param int   $fetchArgument
     *
     * @return array
     * @throws Exception
     */
    public function fetchAll(
        $fetchStyle = CorePDO::FETCH_BOTH,
        $fetchArgument = CorePDO::FETCH_COLUMN
    ) {
        if (!$this->isFetchAllStyleSupported($fetchStyle)) {
            throw new Exception("Fetch style not supported: {$fetchStyle}");
        }

        if (!$this->isInitialized()) {
            $this->initialize();
        }

        if ($fetchStyle === CorePDO::FETCH_COLUMN) {
            return $this->data->getSingleColumnRows($fetchArgument);
        }

        return $this->data->getRowsForFetchStyle($fetchStyle);
    }

    /**
     * @return array
     */
    public function fetchAllKeyPairs()
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return $this->data->getKeyPairedRows();
    }

    /**
     * @param int $fetchStyle
     *
     * @return bool
     * @throws Exception
     */
    public function fetch($fetchStyle = CorePDO::FETCH_BOTH)
    {
        if (!$this->isFetchStyleSupported($fetchStyle)) {
            throw new Exception("Fetch style not supported: {$fetchStyle}");
        }

        $rows = $this->fetchAll($fetchStyle);

        if (isset($rows[++$this->rowIndex])) {
            return $rows[$this->rowIndex];
        }

        return false;
    }

    /**
     * @return int
     */
    public function getAffectedRows()
    {
        return $this->vitessCursor->getRowsAffected();
    }

    /**
     * @return bool
     */
    private function isInitialized()
    {
        return $this->data !== null;
    }

    /**
     *
     */
    private function initialize()
    {
        $rows = [];

        while (($row = $this->vitessCursor->next()) !== false) {
            $rows[] = $row;
        }

        $this->data = new Data($rows);
    }

    /**
     * @param int $fetchMode
     *
     * @return bool
     */
    private function isFetchAllStyleSupported($fetchMode)
    {
        return isset(self::$supportedFetchAllStyles[$fetchMode]);
    }

    /**
     * @param int $fetchMode
     *
     * @return bool
     */
    private function isFetchStyleSupported($fetchMode)
    {
        return isset(self::$supportedFetchStyles[$fetchMode]);
    }
}
