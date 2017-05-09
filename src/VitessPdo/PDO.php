<?php
/**
 * Copyright 2017 PIXELFEDERATION s.r.o.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VitessPdo;

use VitessPdo\PDO\Attributes;
use VitessPdo\PDO\Dsn\Dsn;
use VitessPdo\PDO\Fetcher\Factory as FetcherFactory;
use VitessPdo\PDO\MySql\Emulator;
use VitessPdo\PDO\ParamProcessor;
use VitessPdo\PDO\PDOStatement;
use VitessPdo\PDO\QueryAnalyzer\Analyzer;
use VitessPdo\PDO\QueryExecutor\Executor;
use VitessPdo\PDO\QueryExecutor\ExecutorInterface;
use VitessPdo\PDO\QueryExecutor\ResultInterface;
use VitessPdo\PDO\Vitess\Vitess;
use VitessPdo\PDO\Vitess\Result;
use PDO as CorePDO;
use VitessPdo\PDO\Exception as PDOException;
use Exception;

/**
 * Represents a connection between PHP and a database server.
 * This implementation only supports Vitess db. (@see http://vitess.io)
 *
 * @author  mfris
 * @package VitessPdo
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class PDO
{

    /**
     * @var Dsn
     */
    private $dsn;

    /**
     * @var Vitess
     */
    private $vitess;

    /**
     * @var ExecutorInterface
     */
    private $executor;

    /**
     * @var Analyzer
     */
    private $queryAnalyzer;

    /**
     * @var ParamProcessor
     */
    private $paramProcessor;

    /**
     * @var FetcherFactory
     */
    private $fetcherFactory;

    /**
     * @var Attributes
     */
    private $attributes;

    /**
     * @var Result
     */
    private $lastResult;

    /**
     * PDO constructor.
     *
     * @param       $dsn
     * @param null  $username
     * @param null  $password
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct($dsn, $username = null, $password = null, array $options = [])
    {
        $this->dsn = new Dsn($dsn);
        $this->connect($options);
    }

    /**
     * @param array $options
     */
    private function connect(array $options)
    {
        $config               = $this->dsn->getConfig();
        $host                 = $config->getHost();
        $port                 = $config->getPort();
        $connectionString     = "{$host}:{$port}";
        $this->attributes     = new Attributes();
        $this->vitess         = new Vitess($connectionString, $this->attributes);
        $this->executor       = $this->vitess;
        $this->fetcherFactory = new FetcherFactory();

        if ($config->hasVtCtldData()) {
            $emulator = new Emulator($this->dsn);
            $this->executor = new Executor($this->vitess, $emulator);
        }

        $this->queryAnalyzer  = new Analyzer();
        $this->paramProcessor = new ParamProcessor();

        if (array_key_exists(CorePDO::MYSQL_ATTR_INIT_COMMAND, $options)) {
            // Vitess doesn't support SET NAMES queries yet
            // $query = $options[CorePDO::MYSQL_ATTR_INIT_COMMAND];
            // $this->vtgateConnection->execute($this->vitessCtx, $query, [], TabletType::MASTER);
            return;
        }
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     *
     * PDO::exec() executes an SQL statement in a single function call, returning the number of rows affected
     * by the statement.
     *
     * PDO::exec() does not return results from a SELECT statement. For a SELECT statement that you only need to issue
     * once during your program, consider issuing PDO::query(). For a statement that you need to issue multiple times,
     * prepare a PDOStatement object with PDO::prepare() and issue the statement with PDOStatement::execute().
     *
     * @param string $statement - The SQL statement to prepare and execute.
     *                            Data inside the query should be properly escaped.
     *
     * @return int - PDO::exec() returns the number of rows that were modified or deleted by the SQL statement
     *               you issued. If no rows were affected, PDO::exec() returns 0.
     */
    public function exec($statement)
    {
        $query = $this->queryAnalyzer->parseQuery($statement);
        $this->lastResult = $this->executor->executeWrite($query);

        if (!$this->lastResult->isSuccess()) {
            return false;
        }

        $cursor = $this->lastResult->getCursor();

        return $cursor->getRowsAffected();
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     *
     * public PDOStatement PDO::query ( string $statement )
     * public PDOStatement PDO::query ( string $statement, int $PDO::FETCH_COLUMN, int $colno )
     * public PDOStatement PDO::query ( string $statement, int $PDO::FETCH_CLASS, string $classname, array $ctorargs )
     * public PDOStatement PDO::query ( string $statement, int $PDO::FETCH_INTO, object $object )
     *
     * PDO::query() executes an SQL statement in a single function call, returning the result set (if any) returned
     * by the statement as a PDOStatement object.
     *
     * For a query that you need to issue multiple times, you will realize better performance if you prepare
     * a PDOStatement object using PDO::prepare() and issue the statement with multiple calls
     * to PDOStatement::execute().
     *
     * If you do not fetch all of the data in a result set before issuing your next call to PDO::query(), your call
     * may fail. Call PDOStatement::closeCursor() to release the database resources associated with the PDOStatement
     * object before issuing your next call to PDO::query().
     *
     * Note:
     * Although this function is only documented as having a single parameter, you may pass additional arguments
     * to this function. They will be treated as though you called PDOStatement::setFetchMode()
     * on the resultant statement object.
     *
     * @param string     $statement      - The SQL statement to prepare and execute.
     *                                 Data inside the query should be properly escaped.
     * @param int        $fetchStyle
     * @param mixed      $fetchArgument
     * @param array|null $ctorArgs
     *
     * @return PDOStatement|false    - PDO::query() returns a PDOStatement object, or FALSE on failure.
     * @throws PDOException
     * @throws Exception
     */
    public function query($statement, $fetchStyle = CorePdo::FETCH_BOTH, $fetchArgument = null, array $ctorArgs = [])
    {
        $pdoStatement = $this->prepare($statement);

        try {
            $pdoStatement->execute();
            $pdoStatement->fetchAll($fetchStyle, $fetchArgument, $ctorArgs);
        } catch (Exception $e) {
            if ($this->attributes->isErrorModeException()) {
                throw $e;
            }

            return false;
        }

        return $pdoStatement;
    }

    /**
     * Checks if inside a transaction
     *
     * Checks if a transaction is currently active within the driver. This method only works for database drivers
     * that support transactions.
     *
     * @return bool - Returns TRUE if a transaction is currently active, and FALSE if not.
     */
    public function inTransaction()
    {
        return $this->vitess->isInTransaction();
    }

    /**
     * Returns TRUE if a transaction is currently active, and FALSE if not.
     *
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO object
     * instance are not committed until you end the transaction by calling PDO::commit(). Calling PDO::rollBack() will
     * roll back all changes to the database and return the connection to autocommit mode.
     *
     * Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition language
     * (DDL) statement such as DROP TABLE or CREATE TABLE is issued within a transaction. The implicit COMMIT will
     * prevent you from rolling back any other changes within the transaction boundary.
     *
     * @return bool - Returns TRUE on success or FALSE on failure.
     */
    public function beginTransaction()
    {
        return $this->vitess->beginTransaction();
    }

    /**
     * Commits a transaction
     *
     * Commits a transaction, returning the database connection to autocommit mode until the next call
     * to PDO::beginTransaction() starts a new transaction.
     *
     * @return bool - Returns TRUE on success or FALSE on failure.
     */
    public function commit()
    {
        $this->lastResult = $this->vitess->commitTransaction();

        return $this->lastResult->isSuccess();
    }

    /**
     * Rolls back a transaction
     *
     * Rolls back the current transaction, as initiated by PDO::beginTransaction(). A PDOException will be thrown
     * if no transaction is active.
     *
     * If the database was set to autocommit mode, this function will restore autocommit mode after it has rolled
     * back the transaction.
     *
     * Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition language
     * (DDL) statement such as DROP TABLE or CREATE TABLE is issued within a transaction. The implicit COMMIT will
     * prevent you from rolling back any other changes within the transaction boundary.
     *
     * @return bool - Returns TRUE on success or FALSE on failure.
     */
    public function rollback()
    {
        $this->lastResult = $this->vitess->rollbackTransaction();

        return $this->lastResult->isSuccess();
    }

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * Returns the ID of the last inserted row, or the last value from a sequence object, depending on the underlying
     * river. For example, PDO_PGSQL requires you to specify the name of a sequence object for the name parameter.
     *
     * @param string $name - Name of the sequence object from which the ID should be returned.
     *
     * @return string - If a sequence name was not specified for the name parameter, PDO::lastInsertId() returns
     *                  a string representing the row ID of the last row that was inserted into the database.
     *
     *                  If a sequence name was specified for the name parameter, PDO::lastInsertId() returns a string
     *                  representing the last value retrieved from the specified sequence object.
     *
     *                  If the PDO driver does not support this capability, PDO::lastInsertId() triggers
     *                  an IM001 SQLSTATE.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function lastInsertId($name = null)
    {
        if (!$this->lastResult) {
            return Result::DEFAULT_LAST_INSERT_ID;
        }

        return $this->lastResult->getLastInsertId();
    }

    /**
     * Prepares a statement for execution and returns a statement object
     *
     * Prepares an SQL statement to be executed by the PDOStatement::execute() method. The SQL statement can contain
     * zero or more named (:name) or question mark (?) parameter markers for which real values will be substituted when
     * the statement is executed. You cannot use both named and question mark parameter markers within the same SQL
     * statement; pick one or the other parameter style. Use these parameters to bind any user-input, do not include
     * the user-input directly in the query.
     *
     * You must include a unique parameter marker for each value you wish to pass in to the statement when you call
     * PDOStatement::execute(). You cannot use a named parameter marker of the same name more than once in a prepared
     * statement, unless emulation mode is on.
     *
     * Note:
     * Parameter markers can represent a complete data literal only. Neither part of literal, nor keyword, nor
     * identifier, nor whatever arbitrary query part can be bound using parameters. For example, you cannot bind
     * multiple values to a single parameter in the IN() clause of an SQL statement.
     *
     * Calling PDO::prepare() and PDOStatement::execute() for statements that will be issued multiple times with
     * different parameter values optimizes the performance of your application by allowing the driver to negotiate
     * client and/or server side caching of the query plan and meta information, and helps to prevent SQL injection
     * attacks by eliminating the need to manually quote the parameters.
     *
     * PDO will emulate prepared statements/bound parameters for drivers that do not natively support them, and can
     * also rewrite named or question mark style parameter markers to something more appropriate, if the driver
     * supports one style but not the other.
     *
     * @param string $statement
     * @param array $driverOptions
     *
     * @return PDOStatement
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepare($statement, array $driverOptions = [])
    {
        $statementClass = $this->attributes->get(CorePDO::ATTR_STATEMENT_CLASS);

        return new $statementClass(
            $statement,
            $this->executor,
            $this->attributes,
            $this->paramProcessor,
            $this->queryAnalyzer,
            $this->fetcherFactory,
            function (ResultInterface $result) {
                $this->lastResult = $result;
            }
        );
    }

    /**
     * Sets an attribute on the database handle. Some of the available generic attributes are listed below;
     * some drivers may make use of additional driver specific attributes.
     *
     * PDO::ATTR_CASE: Force column names to a specific case.
     * (NOT IMPLEMENTED YET)
     *  - PDO::CASE_LOWER: Force column names to lower case.
     *  - PDO::CASE_NATURAL: Leave column names as returned by the database driver.
     *  - PDO::CASE_UPPER: Force column names to upper case.
     *
     * PDO::ATTR_ERRMODE: Error reporting.
     *  - PDO::ERRMODE_SILENT: Just set error codes.
     *  - PDO::ERRMODE_WARNING: Raise E_WARNING.
     *  - PDO::ERRMODE_EXCEPTION: Throw exceptions.
     *
     * PDO::ATTR_ORACLE_NULLS (available with all drivers, not just Oracle): Conversion of NULL and empty strings.
     * (NOT IMPLEMENTED YET)
     *  - PDO::NULL_NATURAL: No conversion.
     *  - PDO::NULL_EMPTY_STRING: Empty string is converted to NULL.
     *  - PDO::NULL_TO_STRING: NULL is converted to an empty string.
     *
     * PDO::ATTR_STRINGIFY_FETCHES: Convert numeric values to strings when fetching. Requires bool.
     * (NOT IMPLEMENTED YET)
     *
     *  PDO::ATTR_STATEMENT_CLASS: Set user-supplied statement class derived from PDOStatement. Cannot be used
     *  with persistent PDO instances. Requires array(string classname, array(mixed constructor_args)).
     * (NOT IMPLEMENTED YET)
     *
     * PDO::ATTR_TIMEOUT: Specifies the timeout duration in seconds. Not all drivers support this option,
     * and its meaning may differ from driver to driver. For example, sqlite will wait for up to this time value
     * before giving up on obtaining an writable lock, but other drivers may interpret this as a connect or a read
     * timeout interval. Requires int.
     * (NOT IMPLEMENTED YET)
     *
     * PDO::ATTR_AUTOCOMMIT (available in OCI, Firebird and MySQL): Whether to autocommit every single statement.
     * (NOT IMPLEMENTED YET)
     *
     * PDO::ATTR_EMULATE_PREPARES Enables or disables emulation of prepared statements. Some drivers do not support
     * native prepared statements or have limited support for them. Use this setting to force PDO to either always
     * emulate prepared statements (if TRUE), or to try to use native prepared statements (if FALSE).
     * It will always fall back to emulating the prepared statement if the driver cannot successfully prepare
     * the current query. Requires bool.
     * (NOT IMPLEMENTED YET)
     *
     * PDO::MYSQL_ATTR_USE_BUFFERED_QUERY (available in MySQL): Use buffered queries.
     * (NOT IMPLEMENTED YET)
     *
     * PDO::ATTR_DEFAULT_FETCH_MODE: Set default fetch mode. Description of modes is available
     * in PDOStatement::fetch() documentation.
     * (NOT IMPLEMENTED YET)
     *
     * @param int $attribute
     * @param mixed $value
     *
     * @return bool - Returns TRUE on success or FALSE on failure.
     * @throws PDO\Exception
     */
    public function setAttribute($attribute, $value)
    {
        $this->attributes->set($attribute, $value);

        return true;
    }

    /**
     * Retrieve a database connection attribute
     *
     * This function returns the value of a database connection attribute. To retrieve PDOStatement attributes,
     * refer to PDOStatement::getAttribute().
     * Note that some database/driver combinations may not support all of the database connection attributes.
     *
     * @param int $attribute - One of the PDO::ATTR_* constants. The constants that apply to database
     *                         connections are as follows:
     *                          PDO::ATTR_AUTOCOMMIT
     *                          PDO::ATTR_CASE
     *                          PDO::ATTR_CLIENT_VERSION
     *                          PDO::ATTR_CONNECTION_STATUS
     *                          PDO::ATTR_DRIVER_NAME
     *                          PDO::ATTR_ERRMODE
     *                          PDO::ATTR_ORACLE_NULLS
     *                          PDO::ATTR_PERSISTENT
     *                          PDO::ATTR_PREFETCH
     *                          PDO::ATTR_SERVER_INFO
     *                          PDO::ATTR_SERVER_VERSION
     *                          PDO::ATTR_TIMEOUT
     *
     * @return mixed - A successful call returns the value of the requested PDO attribute.
     *                 An unsuccessful call returns null.
     */
    public function getAttribute($attribute)
    {
        return $this->attributes->get($attribute);
    }

    /**
     * Quotes a string for use in a query.
     *
     * PDO::quote() places quotes around the input string (if required) and escapes special characters within the input
     * string, using a quoting style appropriate to the underlying driver.
     *
     * If you are using this function to build SQL statements, you are strongly recommended to use PDO::prepare()
     * to prepare SQL statements with bound parameters instead of using PDO::quote() to interpolate user input into
     * an SQL statement. Prepared statements with bound parameters are not only more portable, more convenient,
     * immune to SQL injection, but are often much faster to execute than interpolated queries, as both the server
     * and client side can cache a compiled form of the query.
     *
     * Not all PDO drivers implement this method (notably PDO_ODBC). Consider using prepared statements instead.
     *
     * CAUTION
     * Security: the default character set
     *
     * The character set must be set either on the server level, or within the database connection itself
     * (depending on the driver) for it to affect PDO::quote(). See the driver-specific documentation
     * for more information.
     *
     * @param string $string        - The string to be quoted.
     * @param int $parameterType    - Provides a data type hint for drivers that have alternate quoting styles.
     *
     * @return string               - Returns a quoted string that is theoretically safe to pass into an SQL statement.
     *                                Returns FALSE if the driver does not support quoting in this way.
     */
    public function quote($string, $parameterType = CorePDO::PARAM_STR)
    {
        return "'" . $this->paramProcessor->processEscaped($string, $parameterType) . "'";
    }

    /**
     * PDO::errorInfo — Fetch extended error information associated with the last operation on the database handle
     *
     * PDO::errorInfo() only retrieves error information for operations performed directly on the database handle.
     * If you create a PDOStatement object through PDO::prepare() or PDO::query() and invoke an error on the statement
     * handle, PDO::errorInfo() will not reflect the error from the statement handle.
     * You must call PDOStatement::errorInfo() to return the error information for an operation performed
     * on a particular statement handle.
     *
     * @return array - returns an array of error information about the last operation performed by this database handle.
     *                 The array consists of the following fields:
     *
     *                 Element      Information
     *                 0            SQLSTATE error code (a five characters alphanumeric identifier defined
     *                              in the ANSI SQL standard).
     *                 1            Driver-specific error code.
     *                 2            Driver-specific error message.
     * Note:
     * If the SQLSTATE error code is not set or there is no driver-specific error,
     * the elements following element 0 will be set to NULL.
     */
    public function errorInfo()
    {
        if (!$this->lastResult) {
            return false;
        }

        $error = $this->lastResult->getError();

        if (!$error) {
            return false;
        }

        return $error->getInfoAsArray();
    }

    /**
     * @return PDO\Vitess\ClusterConfig
     */
    public function getClusterConfig()
    {
        return $this->vitess->getClusterConfig();
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        unset($this->vitess);
    }
}
