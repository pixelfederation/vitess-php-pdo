<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\TypeChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\VtCtldMember;
use VitessPdo\PDO\MySql\QueryHandler\AlterChain\Chain as AlterChain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class CreateMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class AlterMember extends VtCtldMember
{

    /**
     * @var AlterChain
     */
    private $alterChain;

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query->isType(QueryInterface::TYPE_ALTER)) {
            return null;
        }

        return $this->getAlterChain()->getResult($query);
    }

    /**
     * @return AlterChain
     */
    private function getAlterChain()
    {
        if ($this->alterChain === null) {
            $this->alterChain = new AlterChain($this->client);
        }

        return $this->alterChain;
    }
}
