<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO;

use VitessPdo\PDO;
use VitessPdo\PDO\PDOStatement\Cursor;
use PDO as CorePDO;
use \Exception as CoreException;
use PDOException;

/**
 * Represents a prepared statement and, after the statement is executed, an associated result set.
 *
 * @author  mfris
 * @package VitessPdo\PDO
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PDOStatement
{

    /**
     * @var string
     */
    private $query;

    /**
     * @var Attributes
     */
    private $attributes;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var Vitess
     */
    private $vitess;

    /**
     * @var ParamProcessor
     */
    private $paramProcessor;

    /**
     * @var QueryAnalyzer
     */
    private $queryAnalyzer;

    /**
     * @var Cursor
     */
    private $cursor;

    /**
     * PDOStatement constructor.
     *
     * @param string $query
     * @param Vitess $vitess
     * @param Attributes $attributes
     * @param ParamProcessor $paramProcessor
     * @param QueryAnalyzer $queryAnalyzer
     */
    public function __construct(
        $query,
        Vitess $vitess,
        Attributes $attributes,
        ParamProcessor $paramProcessor,
        QueryAnalyzer $queryAnalyzer
    ) {
        $this->query  = $query;
        $this->vitess = $vitess;
        $this->attributes = $attributes;
        $this->paramProcessor = $paramProcessor;
        $this->queryAnalyzer = $queryAnalyzer;
    }

    /**
     * Executes a prepared statement
     *
     * Execute the prepared statement. If the prepared statement included parameter markers, you must either:
     *
     *  call PDOStatement::bindParam() to bind PHP variables to the parameter markers: bound variables pass
     *  their value as input and receive the output value, if any, of their associated parameter markers
     *  or pass an array of input-only parameter values
     *
     *
     * @param array|null $inputParameters - An array of values with as many elements as there are bound parameters
     *                                      in the SQL statement being executed. All values are treated
     *                                      as PDO::PARAM_STR. (not yet implemented because of a bug in vitess)
     *
     *                                      You cannot bind multiple values to a single parameter; for example,
     *                                      you cannot bind two values to a single named parameter in an IN() clause.
     *
     *                                      You cannot bind more values than specified; if more keys exist
     *                                      in input_parameters than in the SQL specified in the PDO::prepare(),
     *                                      then the statement will fail and an error is emitted.
     * @return bool
     * @throws PDOException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(array $inputParameters = null)
    {
        $this->reset();

        if ($inputParameters) {
            if (array_key_exists(0, $inputParameters)) {
                $inputParameters = $this->repairUnnamedParamsArray($inputParameters);
            }

            foreach ($inputParameters as $key => $value) {
                $this->bindValue($key, $value); // default type is string
            }
        }

        $vitessMethod = 'executeRead';

        if ($this->queryAnalyzer->isWritableQuery($this->query)) {
            $vitessMethod = 'executeWrite';
        }

        try {
            $cursorOrFalse = $this->vitess->{$vitessMethod}($this->query, $this->params);

            if (!$cursorOrFalse) {
                return false;
            }

            $this->cursor = new Cursor($cursorOrFalse);
        } catch (CoreException $e) {
            if ($e instanceof PDOException && $this->attributes->isErrorModeException()) {
                throw $e;
            }

            return false;
        }

        return true;
    }

    /**
     * Returns an array containing all of the result set rows
     *
     * @param int $fetchStyle - Controls the contents of the returned array as documented in PDOStatement::fetch().
     *                          Defaults to value of PDO::ATTR_DEFAULT_FETCH_MODE (which defaults to PDO::FETCH_BOTH)
     *
     *                          To return an array consisting of all values of a single column from the result set,
     *                          specify PDO::FETCH_COLUMN. You can specify which column you want with
     *                          the fetch_argument parameter.
     *
     *                          To fetch only the unique values of a single column from the result set,
     *                          bitwise-OR PDO::FETCH_COLUMN with PDO::FETCH_UNIQUE.
     *
     *                          To return an associative array grouped by the values of a specified column,
     *                          bitwise-OR PDO::FETCH_COLUMN with PDO::FETCH_GROUP.
     *
     * @param int $fetchArgument -  This argument has a different meaning depending on the value
     *                              of the fetch_style parameter:
     *
     *                              PDO::FETCH_COLUMN: Returns the indicated 0-indexed column.
     *
     *                              PDO::FETCH_CLASS: Returns instances of the specified class, mapping the columns
     *                              of each row to named properties in the class.
     *
     *                              PDO::FETCH_FUNC: Returns the results of calling the specified function, using
     *                              each row's columns as parameters in the call.
     *
     * @param array $ctorArgs - Arguments of custom class constructor when the fetch_style parameter
     *                          is PDO::FETCH_CLASS.
     *
     * @return array -  PDOStatement::fetchAll() returns an array containing all of the remaining rows in the
     *                  result set. The array represents each row as either an array of column values or an object
     *                  with properties corresponding to each column name. An empty array is returned if there are zero
     *                  results to fetch, or FALSE on failure.
     *
     *                  Using this method to fetch large result sets will result in a heavy demand on system
     *                  and possibly network resources. Rather than retrieving all of the data and manipulating
     *                  it in PHP, consider using the database server to manipulate the result sets. For example,
     *                  use the WHERE and ORDER BY clauses in SQL to restrict results before retrieving
     *                  and processing them with PHP.
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetchAll(
        $fetchStyle = CorePDO::FETCH_BOTH,
        $fetchArgument = CorePDO::FETCH_COLUMN,
        array $ctorArgs = []
    ) {
        if (!$this->cursor) {
            throw new Exception("Statement not executed yet.");
        }

        if ($fetchStyle === CorePDO::FETCH_KEY_PAIR) {
            return $this->cursor->fetchAllKeyPairs();
        }

        return $this->cursor->fetchAll($fetchStyle, $fetchArgument);
    }

    /**
     * Fetches the next row from a result set.
     *
     * Fetches a row from a result set associated with a PDOStatement object. The fetch_style parameter
     * determines how PDO returns the row.
     *
     * @param int $fetchStyle - Controls how the next row will be returned to the caller. This value must be
     *                          one of the PDO::FETCH_* constants, defaulting to value of PDO::ATTR_DEFAULT_FETCH_MODE
     *                          (which defaults to PDO::FETCH_BOTH).
     *
     *                          - PDO::FETCH_ASSOC: returns an array indexed by column name as returned in your
     *                            result set
     *                          - PDO::FETCH_BOTH (default): returns an array indexed by both column name and 0-indexed
     *                            column number as returned in your result set
     *                          - PDO::FETCH_BOUND: returns TRUE and assigns the values of the columns in your result
     *                            set to the PHP variables to which they were bound with the PDOStatement::bindColumn()
     *                            method
     *                          - PDO::FETCH_CLASS: returns a new instance of the requested class, mapping the columns
     *                            of the result set to named properties in the class. If fetch_style includes
     *                            PDO::FETCH_CLASSTYPE (e.g. PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE) then the name
     *                            of the class is determined from a value of the first column.
     *                            NOT IMPLEMENTED
     *                          - PDO::FETCH_INTO: updates an existing instance of the requested class, mapping
     *                            the columns of the result set to named properties in the class
     *                            NOT IMPLEMENTED
     *                          - PDO::FETCH_LAZY: combines PDO::FETCH_BOTH and PDO::FETCH_OBJ, creating the object
     *                            variable names as they are accessed
     *                            NOT IMPLEMENTED
     *                          - PDO::FETCH_NAMED: returns an array with the same form as PDO::FETCH_ASSOC, except
     *                            that if there are multiple columns with the same name, the value referred to by that
     *                            key will be an array of all the values in the row that had that column name
     *                            NOT IMPLEMENTED
     *                          - PDO::FETCH_NUM: returns an array indexed by column number as returned in your result
     *                            set, starting at column 0
     *                            NOT IMPLEMENTED
     *                          - PDO::FETCH_OBJ: returns an anonymous object with property names that correspond
     *                            to the column names returned in your result set
     *                            NOT IMPLEMENTED
     *
     * @param int $cursorOrientation - For a PDOStatement object representing a scrollable cursor, this value determines
     *                                 which row will be returned to the caller. This value must be one
     *                                 of the PDO::FETCH_ORI_* constants, defaulting to PDO::FETCH_ORI_NEXT. To request
     *                                 a scrollable cursor for your PDOStatement object, you must set
     *                                 the PDO::ATTR_CURSOR attribute to PDO::CURSOR_SCROLL when you prepare the SQL
     *                                 statement with PDO::prepare().
     *
     * @param int $cursorOffset - For a PDOStatement object representing a scrollable cursor for which
     *                            the cursor_orientation parameter is set to PDO::FETCH_ORI_ABS, this value specifies
     *                            the absolute number of the row in the result set that shall be fetched.
     *
     *                            For a PDOStatement object representing a scrollable cursor for which
     *                            the cursor_orientation parameter is set to PDO::FETCH_ORI_REL, this value specifies
     *                            the row to fetch relative to the cursor position before PDOStatement::fetch()
     *                            was called.
     *
     * @return mixed - The return value of this function on success depends on the fetch type.
     *                 In all cases, FALSE is returned on failure.
     *
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(
        $fetchStyle = CorePDO::FETCH_BOTH,
        $cursorOrientation = CorePDO::FETCH_ORI_NEXT,
        $cursorOffset = 0
    ) {
        if (!$this->cursor) {
            throw new Exception("Statement not executed yet.");
        }

        return $this->cursor->fetch($fetchStyle);
    }

    /**
     * Returns a single column from the next row of a result set
     *
     * Returns a single column from the next row of a result set or FALSE if there are no more rows.
     *
     * Note:
     * PDOStatement::fetchColumn() should not be used to retrieve boolean columns, as it is impossible to distinguish
     * a value of FALSE from there being no more rows to retrieve. Use PDOStatement::fetch() instead.
     *
     * Warning:
     * There is no way to return another column from the same row if you use PDOStatement::fetchColumn()
     * to retrieve data.
     *
     * @param int $columnNumber - 0-indexed number of the column you wish to retrieve from the row. If no value
     *                            is supplied, PDOStatement::fetchColumn() fetches the first column.
     *
     * @return mixed            - returns a single column in the next row of a result set.
     */
    public function fetchColumn($columnNumber = 0)
    {
        $row = $this->fetch(CorePDO::FETCH_NUM);

        if ($row) {
            return $row[$columnNumber];
        }

        return false;
    }

    /**
     * Binds a parameter to the specified variable name
     *
     * Binds a PHP variable to a corresponding named or question mark placeholder in the SQL statement that was used
     * to prepare the statement. Unlike PDOStatement::bindValue(), the variable is bound as a reference and will only
     * be evaluated at the time that PDOStatement::execute() is called.
     *
     * Most parameters are input parameters, that is, parameters that are used in a read-only fashion to build up
     * the query. Some drivers support the invocation of stored procedures that return data as output parameters,
     * and some also as input/output parameters that both send in data and are updated to receive it.
     *
     * @param mixed $parameter - Parameter identifier. For a prepared statement using named placeholders,
     *                           this will be a parameter name of the form :name. For a prepared statement using
     *                           question mark placeholders, this will be the 1-indexed position of the parameter.
     *
     * @param mixed $variable  - Name of the PHP variable to bind to the SQL statement parameter.
     *
     * @param int $dataType    - Explicit data type for the parameter using the PDO::PARAM_* constants. To return
     *                           an INOUT parameter from a stored procedure, use the bitwise OR operator
     *                           to set the PDO::PARAM_INPUT_OUTPUT bits for the data_type parameter.
     *
     * @param int $length      - Length of the data type. To indicate that a parameter is an OUT parameter
     *                           from a stored procedure, you must explicitly set the length.
     *
     * @param array $driverOptions
     *
     * @return boolean - Returns TRUE on success or FALSE on failure.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function bindParam($parameter, &$variable, $dataType = CorePDO::PARAM_STR, $length = 0, $driverOptions = [])
    {
        return $this->bindValue($parameter, $variable, $dataType);
    }

    /**
     * Binds a value to a parameter
     *
     * Binds a value to a corresponding named or question mark placeholder in the SQL statement that was used
     * to prepare the statement.
     *
     * @param mixed $parameter - Parameter identifier. For a prepared statement using named placeholders, this will be
     *                           a parameter name of the form :name. For a prepared statement using question mark
     *                           placeholders, this will be the 1-indexed position of the parameter.
     *
     * @param mixed $value     - The value to bind to the parameter.
     * @param int $dataType    - The value to bind to the parameter.
     *
     * @return bool            - Returns TRUE on success or FALSE on failure.
     */
    public function bindValue($parameter, $value, $dataType = CorePDO::PARAM_STR)
    {
        try {
            if (is_int($parameter)) {
                $parameter = "v{$parameter}";
            }

            $variable = $this->paramProcessor->process($value, $dataType);
            $this->params[$parameter] = $variable;
        } catch (CoreException $e) {
            return false;
        }

        return true;
    }

    /**
     * Returns the number of rows affected by the last SQL statement
     *
     * PDOStatement::rowCount() returns the number of rows affected by the last DELETE, INSERT, or UPDATE statement
     * executed by the corresponding PDOStatement object.
     *
     * If the last SQL statement executed by the associated PDOStatement was a SELECT statement, some databases
     * may return the number of rows returned by that statement. However, this behaviour is not guaranteed for all
     * databases and should not be relied on for portable applications.
     *
     * @return int - Returns the number of rows.
     * @throws Exception
     */
    public function rowCount()
    {
        if (!$this->cursor) {
            throw new Exception('Statement wasn\'t executed yet.');
        }

        return $this->cursor->getAffectedRows();
    }

    /**
     * Closes the cursor, enabling the statement to be executed again.
     *
     * PDOStatement::closeCursor() frees up the connection to the server so that other SQL statements may be issued,
     * but leaves the statement in a state that enables it to be executed again.
     *
     * This method is useful for database drivers that do not support executing a PDOStatement object when a previously
     * executed PDOStatement object still has unfetched rows. If your database driver suffers from this limitation,
     * the problem may manifest itself in an out-of-sequence error.
     *
     * PDOStatement::closeCursor() is implemented either as an optional driver specific method (allowing for
     * maximum efficiency), or as the generic PDO fallback if no driver specific function is installed.
     *
     * @return bool - Returns TRUE on success or FALSE on failure.
     */
    public function closeCursor()
    {
        return true;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function repairUnnamedParamsArray(array $params)
    {
        $newParams = [];

        foreach ($params as $key => $value) {
            $newParams['v' . ($key + 1)] = $value;
        }

        return $newParams;
    }

    /**
     *
     */
    private function reset()
    {
        $this->cursor = null;
    }
}
