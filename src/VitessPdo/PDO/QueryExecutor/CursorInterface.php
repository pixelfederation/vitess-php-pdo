<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryExecutor;

use Exception;

/**
 * Description of interface CursorInterface
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryExecutor
 */
interface CursorInterface
{

    /**
     * @return int
     */
    public function getRowsAffected();

    /**
     * @return int
     */
    public function getInsertId();

    /**
     * @return array
     */
    public function getFields();

    /**
     * @return void
     */
    public function close();

    /**
     * @return array|bool
     * @throws Exception
     */
    public function next();
}
