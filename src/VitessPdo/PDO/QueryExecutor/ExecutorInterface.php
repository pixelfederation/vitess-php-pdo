<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryExecutor;

use VitessPdo\PDO\QueryAnalyzer\Query as Query;

/**
 * Description of interface ExecutorInterface
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
interface ExecutorInterface
{

    /**
     * @param Query $query
     * @param array $params
     *
     * @return ResultInterface
     */
    public function executeWrite(Query $query, array $params = []);

    /**
     * @param Query $query
     * @param array $params
     *
     * @return ResultInterface
     */
    public function executeRead(Query $query, array $params = []);
}
