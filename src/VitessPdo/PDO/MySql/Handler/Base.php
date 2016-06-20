<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Handler;

use VitessPdo\PDO\MySql\Result;
use VitessPdo\PDO\QueryAnalyzer\Query as Query;

/**
 * Description of class Base
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
abstract class Base implements HandlerInterface
{

    /**
     * @param Query $query
     *
     * @return Result
     */
    abstract public function getResult(Query $query);
}
