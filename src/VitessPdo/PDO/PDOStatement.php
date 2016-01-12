<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdo\PDO;

use VitessPdo\PDO;
use VTCursor;
use PDO as CorePDO;
use \Exception as CoreException;
use PDOException;

/**
 * Represents a prepared statement and, after the statement is executed, an associated result set.
 *
 * @author  mfris
 * @package VitessPdo\PDO
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
     * @var VTCursor
     */
    private $cursor;

    /**
     * @var array
     */
    private $rows;

    /**
     * PDOStatement constructor.
     *
     * @param string $query
     * @param Vitess $vitess
     * @param Attributes $attributes
     * @param ParamProcessor $paramProcessor
     */
    public function __construct($query, Vitess $vitess, Attributes $attributes, ParamProcessor $paramProcessor)
    {
        $this->query  = $query;
        $this->vitess = $vitess;
        $this->attributes = $attributes;
        $this->paramProcessor = $paramProcessor;
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
        $this->rows = null;

        try {
            if ($inputParameters) {
                if (array_key_exists(0, $inputParameters)) {
                    $inputParameters = $this->repairUnnamedParamsArray($inputParameters);
                }

                $this->params = $inputParameters;

                // string conversion problem in Vitess, uncomment after fixed
                // foreach ($inputParameters as $key => $value) {
                //     $this->bindValue($key, $value); // default type is string
                // }
            }

            $cursorOrFalse = $this->vitess->executeRead($this->query, $this->params);

            if ($cursorOrFalse === false) {
                return false;
            }

            $this->cursor = $cursorOrFalse;
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetchAll(
        $fetchStyle = CorePDO::ATTR_DEFAULT_FETCH_MODE,
        $fetchArgument = CorePDO::FETCH_COLUMN,
        array $ctorArgs = []
    ) {
        if ($this->rows === null) {
            $this->rows = [];

            while (($row = $this->cursor->next()) !== false) {
                $this->rows[] = $row;
            }
        }

        return $this->rows;
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
}
