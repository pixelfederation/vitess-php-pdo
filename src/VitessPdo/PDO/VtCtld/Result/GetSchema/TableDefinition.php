<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Result\GetSchema;

use VitessPdo\PDO\Exception;

/**
 * Description of class TableDefinition
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result\GetSchema
 */
class TableDefinition
{

    /**
     * @var array
     */
    private $data;

    /**
     * @const string
     */
    const KEY_NAME = 'name';

    /**
     * @const string
     */
    const KEY_SCHEMA = 'schema';

    /**
     * @const string
     */
    const KEY_COLUMNS = 'columns';

    /**
     * @const string
     */
    const KEY_PRIMARY_KEY_COLUMNS = 'primary_key_columns';

    /**
     * @const string
     */
    const KEY_TYPE = 'type';

    /**
     * @const string
     */
    const KEY_DATA_LENGTH = 'data_length';

    /**
     * TableDefinition constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getName()
    {
        if (!isset($this->data[self::KEY_NAME])) {
            throw new Exception('Name missing.');
        }

        return $this->data[self::KEY_NAME];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSchema()
    {
        if (!isset($this->data[self::KEY_SCHEMA])) {
            throw new Exception('Schema missing.');
        }

        return $this->data[self::KEY_SCHEMA];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getColumns()
    {
        if (!isset($this->data[self::KEY_COLUMNS])) {
            throw new Exception('Columns missing.');
        }

        return $this->data[self::KEY_COLUMNS];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPrimaryKeyColumns()
    {
        if (!isset($this->data[self::KEY_PRIMARY_KEY_COLUMNS])) {
            throw new Exception('Primary key columns missing.');
        }

        return $this->data[self::KEY_PRIMARY_KEY_COLUMNS];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getType()
    {
        if (!isset($this->data[self::KEY_TYPE])) {
            throw new Exception('Type missing.');
        }

        return $this->data[self::KEY_TYPE];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDataLength()
    {
        if (!isset($this->data[self::KEY_DATA_LENGTH])) {
            throw new Exception('Data length missing.');
        }

        return $this->data[self::KEY_DATA_LENGTH];
    }
}
