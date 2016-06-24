<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\QueryHandler\TypeChain;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\QueryHandler\Member;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\Query\Expression;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;
use VitessPdo\PDO\QueryAnalyzer\SelectQuery;

/**
 * Description of class SelectMember
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
class SelectMember extends Member
{

    /**
     * @var array
     */
    private static $data = [
        [
            'USER()' => 'vitess@vitess',
            0 => 'vitess@vitess',
        ],
    ];

    /**
     * @var array
     */
    private static $fields = [
        'USER()',
    ];

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

        $query = new SelectQuery($query);
        $field = $query->getFirstField();

        if ($field->getType() !== Expression::TYPE_FUNCTION
            || $field->getExpression() !== Expression::EXPR_USER) {
            return null;
        }

        return $this->getResultFromData(self::$data, self::$fields);
    }
}
