<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\CreateChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\VtCtldChain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\CreateQuery;

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
        $query = new CreateQuery($query);

        return parent::getResult($query);
    }

    /**
     *
     */
    protected function initialize()
    {
        $this->first = new TableMember($this->client);
    }
}
