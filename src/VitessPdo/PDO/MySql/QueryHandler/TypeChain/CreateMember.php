<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\TypeChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\VtCtldMember;
use VitessPdo\PDO\MySql\QueryHandler\CreateChain\Chain as CreateChain;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class CreateMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class CreateMember extends VtCtldMember
{

    /**
     * @var CreateChain
     */
    private $createChain;

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query->isType(QueryInterface::TYPE_CREATE)) {
            return null;
        }

        return $this->getCreateChain()->getResult($query);
    }

    /**
     * @return CreateChain
     */
    private function getCreateChain()
    {
        if ($this->createChain === null) {
            $this->createChain = new CreateChain($this->client);
        }

        return $this->createChain;
    }
}
