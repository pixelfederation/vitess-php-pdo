<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryExecutor;

/**
 * Description of interface ResultInterface
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryExecutor
 */
interface ResultInterface
{

    /**
     * @return bool
     */
    public function isSuccess();

    /**
     * @return CursorInterface
     */
    public function getCursor();

    /**
     * @return Error
     */
    public function getError();
}
