<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Cursor;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\QueryExecutor\CursorInterface;

/**
 * Description of class Cursor
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Cursor
 */
class Cursor implements CursorInterface
{

    /**
     * @var array
     */
    private $rows;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var int
     */
    private $rowsAffected;

    /**
     * @var int
     */
    private $insertId;

    /**
     * @var int
     */
    private $currentIndex = 0;

    /**
     * Cursor constructor.
     *
     * @param array $rows
     * @param array $fields
     * @param int $rowsAffected
     * @param int $insertId
     */
    public function __construct(array $rows, array $fields = [], $rowsAffected = 0, $insertId = 0)
    {
        $this->rows = $rows;
        $this->fields = $fields;
        $this->rowsAffected = (int) $rowsAffected;
        $this->insertId = (int) $insertId;
    }

    /**
     * @return int
     */
    public function getRowsAffected()
    {
        return $this->rowsAffected;
    }

    /**
     * @return int
     */
    public function getInsertId()
    {
        return $this->insertId;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return void
     */
    public function close()
    {
    }

    /**
     * @return array|bool
     * @throws Exception
     */
    public function next()
    {
        if (!isset($this->rows[$this->currentIndex])) {
            return false;
        }

        return $this->rows[$this->currentIndex++];
    }
}
