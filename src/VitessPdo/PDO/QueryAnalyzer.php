<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO;

/**
 * Description of class QueryAnalyzer
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class QueryAnalyzer
{

    /**
     * @param string $sql
     * @return boolean
     */
    public function isInsertQuery($sql)
    {
        $sqlLc = $this->normalizeQuery($sql);

        return $this->isInsertQueryNormalized($sqlLc);
    }

    /**
     * @param string $sql
     * @return boolean
     */
    public function isUpdateQuery($sql)
    {
        $sqlLc = $this->normalizeQuery($sql);

        return $this->isUpdateQueryNormalized($sqlLc);
    }

    /**
     * @param string $sql
     * @return boolean
     */
    public function isDeleteQuery($sql)
    {
        $sqlLc = $this->normalizeQuery($sql);

        return $this->isDeleteQueryNormalized($sqlLc);
    }

    /**
     * @param string $sql
     *
     * @return bool
     */
    public function isWritableQuery($sql)
    {
        $sqlLc = $this->normalizeQuery($sql);

        return $this->isInsertQueryNormalized($sqlLc)
               || $this->isUpdateQueryNormalized($sqlLc)
               || $this->isDeleteQueryNormalized($sqlLc);
    }

    /**
     * @param string $sqlNormalized
     *
     * @return boolean
     */
    public function isUpdateQueryNormalized($sqlNormalized)
    {
        return substr($sqlNormalized, 0, 6) === "update";
    }

    /**
     * @param string $sqlNormalized
     *
     * @return boolean
     */
    public function isDeleteQueryNormalized($sqlNormalized)
    {
        return substr($sqlNormalized, 0, 6) === "delete";
    }

    /**
     * @param $sql
     *
     * @return array
     */
    public function getFieldsFromQuery($sql)
    {
        $sql = $this->normalizeQuery($sql);
        preg_match('/select (.*) from/', $sql, $matches);

        return array_map(function ($field) {
            return trim($field, " `");
        }, explode(',', $matches[1]));
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    private function normalizeQuery($sql)
    {
        return strtolower(trim($sql));
    }

    /**
     * @param string $sqlNormalized
     *
     * @return bool
     */
    private function isInsertQueryNormalized($sqlNormalized)
    {
        return substr($sqlNormalized, 0, 6) === "insert";
    }
}
