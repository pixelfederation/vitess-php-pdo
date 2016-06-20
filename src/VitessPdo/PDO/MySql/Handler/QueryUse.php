<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Handler;

use VitessPdo\PDO\MySql\Result\EmptyResult;
use VitessPdo\PDO\QueryAnalyzer\Query as Query;

/**
 * Description of class QueryUse
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class QueryUse extends Base
{

    /**
     * @param Query $query
     *
     * @return EmptyResult
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getResult(Query $query)
    {
        return new EmptyResult();
    }
}
