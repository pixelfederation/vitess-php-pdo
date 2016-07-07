<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\TypeChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\VtCtldMember;
use VitessPdo\PDO\MySql\QueryHandler\DropChain\Chain as DropChain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class CreateMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class DropMember extends VtCtldMember
{

    /**
     * @var DropChain
     */
    private $dropChain;

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query->isType(QueryInterface::TYPE_DROP)) {
            return null;
        }

        return $this->getDropChain()->getResult($query);
    }

    /**
     * @return DropChain
     */
    private function getDropChain()
    {
        if ($this->dropChain === null) {
            $this->dropChain = new DropChain($this->client);
        }

        return $this->dropChain;
    }
}
