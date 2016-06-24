<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\TypeChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\VctldMember;
use VitessPdo\PDO\MySql\QueryHandler\ShowChain\Chain as ShowChain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class ShowMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class ShowMember extends VctldMember
{

    /**
     * @var ShowChain
     */
    private $showChain;

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query->isType(QueryInterface::TYPE_SHOW)) {
            return null;
        }

        return $this->getShowChain()->getResult($query);
    }

    /**
     * @return ShowChain
     */
    private function getShowChain()
    {
        if ($this->showChain === null) {
            $this->showChain = new ShowChain($this->client);
        }

        return $this->showChain;
    }
}
