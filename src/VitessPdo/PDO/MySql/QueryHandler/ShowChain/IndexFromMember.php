<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\ShowChain\Helper\Tablet;
use VitessPdo\PDO\MySql\QueryHandler\VtCtldMember;
use VitessPdo\PDO\MySql\Result\Show\IndexFrom;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;
use VitessPdo\PDO\VtCtld\ClientInterface;
use VitessPdo\PDO\VtCtld\Result\ListAllTablets as ListAllTabletsResult;
use VitessPdo\PDO\VtCtld\Command\GetSchema;
use VitessPdo\PDO\VtCtld\Result\GetSchema as GetSchemaResult;
use VitessPdo\PDO\VtCtld\Result\Result;

/**
 * Description of class IndexFromMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\QueryHandler\ShowChain
 */
class IndexFromMember extends VtCtldMember
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
        if (!$query instanceof ShowQuery || $query->getObject() !== ShowQuery::EXPRESSION_INDEX) {
            return null;
        }

        $fromExpr = $query->getFromExpression();

        if (!$fromExpr) {
            throw new Exception('From expression missing.');
        }

        $tablet = $this->tablet->getTablet();
        $schemaCmd = new GetSchema($tablet->getAlias());
        /* @var $result GetSchemaResult */
        $result = $this->client->executeCommand($schemaCmd);

        return new IndexFrom($result, $fromExpr);
    }
}
