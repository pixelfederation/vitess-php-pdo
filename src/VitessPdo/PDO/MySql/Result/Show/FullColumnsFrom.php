<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Result\Show;

use PHPSQLParser\PHPSQLParser;
use VitessPdo\PDO\Exception;
use VitessPdo\PDO\MySql\Result\VtCtldResult;
use VitessPdo\PDO\QueryAnalyzer\CreateQuery;
use VitessPdo\PDO\QueryAnalyzer\Query;
use VitessPdo\PDO\VtCtld\Result\GetSchema;

/**
 * Description of class FullColumnsFrom
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result\Show
 */
final class FullColumnsFrom extends VtCtldResult
{

    /**
     * @var array
     */
    protected static $fields = [
        self::KEY_FIELD,
        self::KEY_TYPE,
        self::KEY_COLLATION,
        self::KEY_NULL,
        self::KEY_KEY,
        self::KEY_DEFAULT,
        self::KEY_EXTRA,
        self::KEY_PRIVILEGES,
        self::KEY_COMMENT,
    ];

    /**
     * @var array
     */
    private static $tplData = [
        self::KEY_FIELD => 'dummy',
        0 => 'dummy',
        self::KEY_TYPE => 'dummy',
        1 => 'dummy',
        self::KEY_COLLATION => 'dummy',
        2 => 'dummy',
        self::KEY_NULL => 'dummy',
        3 => 'dummy',
        self::KEY_KEY => 'dummy',
        4 => 'dummy',
        self::KEY_DEFAULT => 'dummy',
        5 => 'dummy',
        self::KEY_EXTRA => '',
        6 => '',
        self::KEY_PRIVILEGES => 'select,insert,update,references',
        7 => 'select,insert,update,references',
        self::KEY_COMMENT => '',
        8 => '',
    ];

    /**
     * @var string
     */
    private $fromExpr;

    const KEY_FIELD = 'Field';
    const KEY_TYPE = 'Type';
    const KEY_COLLATION = 'Collation';
    const KEY_NULL = 'Null';
    const KEY_KEY = 'Key';
    const KEY_DEFAULT = 'Default';
    const KEY_EXTRA = 'Extra';
    const KEY_PRIVILEGES = 'Privileges';
    const KEY_COMMENT = 'Comment';

    /**
     * Databases constructor.
     *
     * @param GetSchema $result
     * @param string    $fromExpr
     */
    public function __construct(GetSchema $result, $fromExpr)
    {
        $this->fromExpr = trim($fromExpr);
        parent::__construct($result);
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     */
    protected function transform($data)
    {
        if (!$data instanceof GetSchema\Schema) {
            throw new Exception('Data is not a Schema.');
        }

        $createExpr = $this->getCreateQueryFromSchema($data);
        $indices = $createExpr->getIndices();
        $columns = $createExpr->getColumns();

        $returnData = [];

        /* @var  $column Query\ColumnExpression */
        foreach ($columns as $column) {
            $row                      = self::$tplData;
            $columnType               = $column->getColumnType();
            $dataType                 = $columnType->getDataType();

            $key = $this->getKeyTypeFromColumn($column, $indices);
            $default = $this->getDefaultValueFromColumnType($columnType);

            $row[self::KEY_FIELD]     = $row[0] = $column->getColumnName();
            $row[self::KEY_TYPE]      = $row[1] = $columnType->getSqlType();
            $row[self::KEY_COLLATION] = $row[2] = $dataType->getCollation() !== '' ? $dataType->getCollation() : null;
            $row[self::KEY_NULL]      = $row[3] = $columnType->isNullable() ? 'YES' : 'NO';
            $row[self::KEY_KEY]       = $row[4] = $key;
            $row[self::KEY_DEFAULT]   = $row[5] = $default;

            $returnData[] = $row;
        }

        return $returnData;
    }

    /**
     * @param GetSchema\Schema $schema
     *
     * @return Query\CreateExpression
     * @throws Exception
     */
    private function getCreateQueryFromSchema(GetSchema\Schema $schema)
    {
        $definition = $schema->getTableDefinition($this->fromExpr);
        $defSchema = $definition->getSchema();
        $parser = new PHPSQLParser();
        $query = new CreateQuery(new Query($defSchema, $parser->parse($defSchema)));

        return $query->getObjectExpression();
    }

    /**
     * @param Query\ColumnExpression $column
     * @param array                  $indices
     *
     * @return string
     */
    private function getKeyTypeFromColumn(Query\ColumnExpression $column, array $indices)
    {
        $key = '';

        foreach ($indices as $index) {
            if ($index->hasColumn($column)) {
                $key = $index->getType() === Query\IndexExpression::TYPE_PRIMARY_KEY ? 'PRI' : 'MUL';
                break;
            }
        }

        return $key;
    }

    /**
     * @param Query\ColumnTypeExpression $columnType
     *
     * @return mixed|null|string
     * @throws Exception
     */
    private function getDefaultValueFromColumnType(Query\ColumnTypeExpression $columnType)
    {
        $default = '';

        if ($columnType->hasDefault()) {
            $default = $columnType->getDefault();

            if ($default === 'NULL') {
                $default = null;
            }
        }

        return $default;
    }
}
