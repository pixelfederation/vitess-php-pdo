<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdo\PDO;

use VitessPdo\PDO;
use VTCursor;
use PDO as CorePDO;

/**
 * Description of class PDOStatement
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class PDOStatement
{

    /**
     * @var string
     */
    private $query;

    /**
     * @var Vitess
     */
    private $vitess;

    /**
     * @var VTCursor
     */
    private $cursor;

    /**
     * PDOStatement constructor.
     *
     * @param string $query
     * @param Vitess $vitess
     */
    public function __construct($query, Vitess $vitess)
    {
        $this->query  = $query;
        $this->vitess = $vitess;
    }

    /**
     * @param array|null $inputParameters
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(array $inputParameters = null)
    {
        try {
            $this->cursor = $this->vitess->executeRead($this->query);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param int   $fetchStyle
     * @param int   $fetchArgument
     * @param array $ctorArgs
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetchAll(
        $fetchStyle = CorePDO::ATTR_DEFAULT_FETCH_MODE,
        $fetchArgument = CorePDO::FETCH_COLUMN,
        array $ctorArgs = []
    ) {
        $rows = [];

        while (($row = $this->cursor->next()) !== false) {
            $rows[] = $row;
        }

        return $rows;
    }
}
