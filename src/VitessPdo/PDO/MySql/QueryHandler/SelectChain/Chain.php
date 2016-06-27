<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\SelectChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\Chain as AbstractChain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\SelectQuery;

/**
 * Description of class Chain
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class Chain extends AbstractChain
{

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function getResult(QueryInterface $query)
    {
        $query = new SelectQuery($query);

        return parent::getResult($query);
    }

    /**
     *
     */
    protected function initialize()
    {
        $this->first = new UserMember();
        $this->first->setSuccessor(new ConnectionIdMember());
    }
}
