<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Result;

use VitessPdo\PDO\MySql\Cursor\EmptyCursor;

/**
 * Description of class EmptyResult
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result
 */
class EmptyResult extends Result
{

    /**
     * EmptyResult constructor.
     */
    public function __construct()
    {
        parent::__construct(new EmptyCursor());
    }
}
