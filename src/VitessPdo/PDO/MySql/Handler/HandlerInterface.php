<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Handler;

use VitessPdo\PDO\MySql\Result;
use VitessPdo\PDO\QueryAnalyzer\Query;

/**
 * Description of interface HandlerInterface
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
interface HandlerInterface
{

    /**
     * @param Query $query
     *
     * @return Result
     */
    public function getResult(Query $query);
}
