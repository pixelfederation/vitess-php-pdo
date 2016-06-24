<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\TypeChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\DsnMember;
use VitessPdo\PDO\MySql\Result\EmptyResult;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\UseQuery;

/**
 * Description of class UseMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class UseMember extends DsnMember
{

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query->isType(QueryInterface::TYPE_USE)) {
            return null;
        }

        $query = new UseQuery($query);
        $keyspace = $query->getDbName();
        $this->dsn->getConfig()->setKeyspace($keyspace);

        return new EmptyResult();
    }
}
