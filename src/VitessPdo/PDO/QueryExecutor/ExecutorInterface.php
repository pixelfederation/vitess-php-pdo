<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryExecutor;

use VitessPdo\PDO\QueryAnalyzer\Query as Query;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of interface ExecutorInterface
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
interface ExecutorInterface
{

    /**
     * @param QueryInterface $query
     * @param array          $params
     *
     * @return ResultInterface
     */
    public function executeWrite(QueryInterface $query, array $params = []);

    /**
     * @param QueryInterface $query
     * @param array          $params
     *
     * @return ResultInterface
     */
    public function executeRead(QueryInterface $query, array $params = []);
}
