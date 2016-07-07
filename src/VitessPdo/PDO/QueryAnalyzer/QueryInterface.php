<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */
namespace VitessPdo\PDO\QueryAnalyzer;

use VitessPdo\PDO\Exception;

/**
 * Description of class Query
 *
 * @author  mfris
 * @package VitessPdo\PDO\Analyzer
 */
interface QueryInterface
{

    const TYPE_SELECT = 'SELECT';
    const TYPE_INSERT = 'INSERT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';
    const TYPE_CREATE = 'CREATE';
    const TYPE_ALTER = 'ALTER';
    const TYPE_DROP = 'DROP';
    const TYPE_USE    = 'USE';
    const TYPE_SHOW   = 'SHOW';
    const TYPE_UNKNOWN = 'unknown';

    /**
     * @return string
     */
    public function getSql();

    /**
     * @return array
     */
    public function getParsedSql();

    /**
     * @return bool
     */
    public function isInsert();

    /**
     * @return bool
     */
    public function isUpdate();

    /**
     * @return bool
     */
    public function isDelete();

    /**
     * @return bool
     */
    public function isWritable();

    /**
     * @param string $type
     *
     * @return bool
     * @throws Exception
     */
    public function isType($type);
}
