<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\TypeChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\SelectChain\Chain as SelectChain;
use VitessPdo\PDO\MySql\QueryHandler\Member;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class SelectMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class SelectMember extends Member
{

    /**
     * @var SelectChain
     */
    private $selectChain;

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     * @throws Exception
     */
    public function process(QueryInterface $query)
    {
        if (!$query->isType(QueryInterface::TYPE_SELECT)) {
            return null;
        }

        return $this->getSelectChain()->getResult($query);
    }

    /**
     * @return SelectChain
     */
    private function getSelectChain()
    {
        if ($this->selectChain === null) {
            $this->selectChain = new SelectChain();
        }

        return $this->selectChain;
    }
}
