<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\VctldMember;
use VitessPdo\PDO\MySql\Result\Show\CreateTable;
use VitessPdo\PDO\MySql\Result\Show\IndexFrom;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;
use VitessPdo\PDO\VtCtld\Command\ListAllTablets;
use VitessPdo\PDO\VtCtld\Result\ListAllTablets as ListAllTabletsResult;
use VitessPdo\PDO\VtCtld\Command\GetSchema;
use VitessPdo\PDO\VtCtld\Result\GetSchema as GetSchemaResult;
use VitessPdo\PDO\VtCtld\Result\Result;

/**
 * Description of class CreateTableMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\QueryHandler\ShowChain
 */
class CreateTableMember extends VctldMember
{

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_CREATE_TABLE) {
            return null;
        }

        $table = $query->getCreateObjectExpression(ShowQuery::EXPRESSION_CREATE_TABLE);

        if (!$table) {
            throw new Exception('Table missing.');
        }

        $tablet = $this->getTablet();
        $schemaCmd = new GetSchema($tablet->getAlias());
        /* @var $result GetSchemaResult */
        $result = $this->client->executeCommand($schemaCmd);

        return new CreateTable($result, $table);
    }

    /**
     * @return ListAllTabletsResult\Tablet
     * @throws Exception
     */
    private function getTablet()
    {
        $command = new ListAllTablets();
        /* @var $vtCtldResult ListAllTabletsResult */
        $vtCtldResult = $this->client->executeCommand($command);

        $tablets = $vtCtldResult->getDataForCurrentKeyspace();

        return $tablets[0];
    }
}
