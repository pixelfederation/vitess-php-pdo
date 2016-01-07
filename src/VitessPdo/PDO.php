<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdo;

use VitessPdo\PDO\Dsn;
use VitessPdo\PDO\ParamProcessor;
use VitessPdo\PDO\PDOStatement;
use VitessPdo\PDO\QueryAnalyzer;
use VitessPdo\PDO\Vitess;
use PDO as CorePDO;

/**
 * Represents a connection between PHP and a database server.
 * This implementation only supports Vitess db. (@see http://vitess.io)
 *
 * @author  mfris
 * @package VitessPdo
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var QueryAnalyzer
     */
    private $queryAnalyzer;

    /**
     * @var ParamProcessor
     */
    private $paramProcessor;

    /**
     * @var string
     */
    private $lastInsertId = self::DEFAULT_LAST_INSERT_ID;

    /**
     * @const string
     */
    const DEFAULT_LAST_INSERT_ID = '0';

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
        $host = $this->dsn->getConfig()->getHost();
        $port = $this->dsn->getConfig()->getPort();
        $connectionString = "{$host}:{$port}";
        $this->vitess = new Vitess($connectionString);
        $this->queryAnalyzer = new QueryAnalyzer();
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
        $cursor = $this->vitess->executeWrite($statement);

        if ($this->queryAnalyzer->isInsertQuery($statement)) {
            $this->lastInsertId = $cursor->getInsertId();
        }

        return $cursor->getRowsAffected();
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
        $this->resetLastInsertId();

        return $this->vitess->commitTransaction();
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
        $this->resetLastInsertId();

        return $this->vitess->rollbackTransaction();
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
        return $this->lastInsertId;
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
        return new PDOStatement($statement, $this->vitess, $this->paramProcessor);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        unset($this->vitess);
    }

    /**
     * @return void
     */
    private function resetLastInsertId()
    {
        $this->lastInsertId = self::DEFAULT_LAST_INSERT_ID;
    }
}
