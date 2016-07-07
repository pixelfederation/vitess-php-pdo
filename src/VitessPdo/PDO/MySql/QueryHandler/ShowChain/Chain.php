<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\ShowChain\Helper\Tablet;
use VitessPdo\PDO\MySql\QueryHandler\VtCtldChain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;

/**
 * Description of class Chain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Chain extends VtCtldChain
{

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function getResult(QueryInterface $query)
    {
        $query = new ShowQuery($query);

        return parent::getResult($query);
    }

    /**
     *
     */
    protected function initialize()
    {
        $tabletHelper = new Tablet($this->client);

        $this->first = new TablesMember($this->client, $tabletHelper);
        $this->first->setSuccessor($tableStatus = new TableStatusMember($this->client, $tabletHelper));
        $tableStatus->setSuccessor($databases = new DatabasesMember($this->client));
        $databases->setSuccessor($collation = new CollationMember($this->client));
        $collation->setSuccessor($database = new CreateDatabaseMember());
        $database->setSuccessor($indexFrom = new IndexFromMember($this->client, $tabletHelper));
        $indexFrom->setSuccessor($fullColumns = new FullColumnsFromMember($this->client, $tabletHelper));
        $fullColumns->setSuccessor($createTable = new CreateTableMember($this->client, $tabletHelper));
        $createTable->setSuccessor(new EnginesMember());
    }
}
