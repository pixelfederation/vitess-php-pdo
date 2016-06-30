<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\DependencyTrait;
use VitessPdo\PDO\MySql\QueryHandler\VctldMember;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\MySql\Result\Show\Tables;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;
use VitessPdo\PDO\VtCtld\Command\GetVSchema;
use VitessPdo\PDO\VtCtld\Result\GetVSchema as GetVSchemaResult;

/**
 * Description of class TablesMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class TablesMember extends VctldMember
{

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_TABLES) {
            return null;
        }

        $command = new GetVSchema();
        /* @var $vtCtldResult GetVSchemaResult */
        $vtCtldResult = $this->client->executeCommand($command);

        return new Tables($vtCtldResult);
    }
}
