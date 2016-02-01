<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\PDOStatement;

use PDO as CorePDO;

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
     * @param int $fetchStyle
     *
     * @return array
     */
    public function getRowsForFetchStyle($fetchStyle)
    {
        if (!$this->hasRowsForFetchStyle($fetchStyle)) {
            switch ($fetchStyle) {
                case CorePDO::FETCH_ASSOC:
                    $this->populateAssocData();
                    break;
                case CorePDO::FETCH_NUM:
                    $this->populateNumericData();
                    break;
            }
        }

        return $this->rows[$fetchStyle];
    }

    /**
     * @param $colIndex
     *
     * @return array
     */
    public function getSingleColumnRows($colIndex)
    {
        $rows = $this->getRowsForFetchStyle(CorePDO::FETCH_NUM);

        return array_map(function ($row) use ($colIndex) {
            return isset($row[$colIndex]) ? $row[$colIndex] : null;
        }, $rows);
    }

    /**
     * @return array
     */
    public function getKeyPairedRows()
    {
        $rows = $this->getRowsForFetchStyle(CorePDO::FETCH_BOTH);
        $pairedRows = [];

        foreach ($rows as $row) {
            $pairedRows[$row[0]] = $row[1];
        }

        return $pairedRows;
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

    /**
     *
     */
    private function populateAssocData()
    {
        $rows = $this->getRowsForFetchStyle(CorePDO::FETCH_BOTH);
        $this->rows[CorePDO::FETCH_ASSOC] = $this->filterRowsKeysByFunction($rows, 'is_string');
    }

    /**
     *
     */
    private function populateNumericData()
    {
        $rows = $this->getRowsForFetchStyle(CorePDO::FETCH_BOTH);
        $this->rows[CorePDO::FETCH_NUM] = $this->filterRowsKeysByFunction($rows, 'is_numeric');
    }

    /**
     * @param $rows
     * @param $function
     *
     * @return array
     */
    private function filterRowsKeysByFunction($rows, $function)
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
