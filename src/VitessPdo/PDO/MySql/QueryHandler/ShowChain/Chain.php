<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\ShowChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\VctldChain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\ShowQuery;

/**
 * Description of class Chain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class Chain extends VctldChain
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
        $this->first = new TablesMember($this->client);
        $this->first->setSuccessor($tableStatus = new TableStatusMember($this->client));
        $tableStatus->setSuccessor($databases = new DatabasesMember($this->client));
        $databases->setSuccessor($collation = new CollationMember($this->client));
        $collation->setSuccessor($database = new CreateDatabaseMember());
        $database->setSuccessor(new IndexFromMember($this->client));
    }
}
