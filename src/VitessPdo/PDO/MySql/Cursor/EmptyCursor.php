<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Cursor;

/**
 * Description of class EmptyCursor
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Cursor
 */
class EmptyCursor extends Cursor
{

    /**
     * EmptyCursor constructor.
     */
    public function __construct()
    {
        parent::__construct([]);
    }
}
