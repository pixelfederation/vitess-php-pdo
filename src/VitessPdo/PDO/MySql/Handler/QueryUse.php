<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Handler;

use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\MySql\Result\EmptyResult;
use VitessPdo\PDO\QueryAnalyzer\Query;

/**
 * Description of class QueryUse
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class QueryUse extends Base
{

    /**
     * @var Dsn
     */
    private $dsn;

    /**
     * QueryUse constructor.
     *
     * @param Dsn $dsn
     */
    public function __construct(Dsn $dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * @param Query $query
     *
     * @return EmptyResult
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getResult(Query $query)
    {
        $keyspace = $query->getDbNameForUse();
        $this->dsn->getConfig()->setKeyspace($keyspace);

        return new EmptyResult();
    }
}
