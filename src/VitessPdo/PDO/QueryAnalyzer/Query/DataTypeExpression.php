<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryAnalyzer\Query;

use VitessPdo\PDO\Exception;

/**
 * Description of class DataTypeExpression
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
class DataTypeExpression extends ExpressionDecorator
{

    /**
     * @return string
     * @throws Exception
     */
    public function getCollation()
    {
        $dataType = $this->getExpression();

        return strpos($dataType, 'char') !== false || strpos($dataType, 'text') !== false ? 'utf8_general_ci' : '';
    }
}
