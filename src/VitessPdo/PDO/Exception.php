<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO;

use Exception as CoreException;

/**
 * Description of class Exception
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class Exception extends CoreException
{

    /**
     * @throws Exception
     */
    public static function newStatementClassException()
    {
        throw new Exception(
            "General error: PDO::ATTR_STATEMENT_CLASS requires format array(classname, array(ctor_args)); "
            . "the classname must be a string specifying an existing class"
        );
    }
}
