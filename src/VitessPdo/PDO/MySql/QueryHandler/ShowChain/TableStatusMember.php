<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\DependencyTrait;
use VitessPdo\PDO\MySql\QueryHandler\ShowChain\Helper\Tablet;
use VitessPdo\PDO\MySql\QueryHandler\VtCtldMember;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\MySql\Result\Show\TableStatus;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;
use VitessPdo\PDO\VtCtld\ClientInterface;
use VitessPdo\PDO\VtCtld\Command\GetSchema;
use VitessPdo\PDO\VtCtld\Result\GetSchema as GetSchemaResult;

/**
 * Description of class TableStatusMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class TableStatusMember extends VtCtldMember
{

    /**
     * @var Tablet
     */
    private $tablet;

    /**
     * TablesMember constructor.
     *
     * @param ClientInterface $client
     * @param Tablet $tablet
     */
    public function __construct(ClientInterface $client, Tablet $tablet)
    {
        $this->tablet = $tablet;
        parent::__construct($client);
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_TABLE_STATUS) {
            return null;
        }

        $likeExpr = $query->getLikeExpression();
        $tablet = $this->tablet->getTablet();
        $command = new GetSchema($tablet->getAlias());
        /* @var $vtCtldResult GetSchemaResult */
        $vtCtldResult = $this->client->executeCommand($command);

        return new TableStatus($vtCtldResult, $likeExpr);
    }
}
