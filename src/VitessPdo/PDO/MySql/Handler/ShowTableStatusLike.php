<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Handler;

use VitessPdo\PDO\QueryAnalyzer\Query;

/**
 * Description of class ShowTableStatusLike
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler
 */
class ShowTableStatusLike extends ShowTableStatus
{

    /**
     * @param Query $query
     *
     * @return array
     */
    protected function prepareData(Query $query)
    {
        $data = parent::prepareData($query);
        $likeExpr = $query->getLikeExpression();
        $pattern = '/^' . str_replace('%', '.*', trim($likeExpr, "'")) . '$/';
        $newData = [];

        foreach ($data as $row) {
            if (preg_match($pattern, $row['Name'])) {
                $newData[] = $row;
            }
        }

        return $newData;
    }
}
