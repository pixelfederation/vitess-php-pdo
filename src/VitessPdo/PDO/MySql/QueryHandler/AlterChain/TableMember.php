<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\AlterChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\DependencyTrait;
use VitessPdo\PDO\MySql\QueryHandler\VtCtldMember;
use VitessPdo\PDO\MySql\Result\EmptyResult;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\AlterQuery;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\VtCtld\Command\ApplySchema;

/**
 * Description of class TableMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class TableMember extends VtCtldMember
{

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof AlterQuery || $query->getObject() !== AlterQuery::EXPRESSION_TABLE) {
            return null;
        }

        $command = new ApplySchema($query->getSql());
        $this->client->executeCommand($command);

        return new EmptyResult();
    }
}
