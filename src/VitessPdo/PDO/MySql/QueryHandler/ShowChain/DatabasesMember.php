<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\VctldMember;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\MySql\Result\Show\Databases;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;
use VitessPdo\PDO\VtCtld\Command\GetKeyspaces;
use VitessPdo\PDO\VtCtld\Result\GetKeyspaces as GetKeyspacesResult;

/**
 * Description of class TablesMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class DatabasesMember extends VctldMember
{

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_DATABASES) {
            return null;
        }

        $command = new GetKeyspaces();
        /* @var $vtCtldResult GetKeyspacesResult */
        $vtCtldResult = $this->client->executeCommand($command);

        return new Databases($vtCtldResult);
    }
}
