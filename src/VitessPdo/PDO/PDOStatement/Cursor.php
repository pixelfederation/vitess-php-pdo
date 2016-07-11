<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 */

namespace VitessPdo\PDO\PDOStatement;

use VitessPdo\PDO\Fetcher\Factory as FetcherFactory;
use VitessPdo\PDO\Fetcher\FetchConfig;
use VitessPdo\PDO\QueryExecutor\CursorInterface;
use VitessPdo\PDO\Exception;
use PDO as CorePDO;

/**
 * Description of class Cursor
 *
 * @author  mfris
 * @package VitessPdo\PDO\PDOStatement
 */
class Cursor
{

    /**
     * @var CursorInterface
     */
    private $executorCursor;

    /**
     * @var FetcherFactory
     */
    private $fetcherFactory;

    /**
     * @var int
     */
    private $rowIndex = -1;

    /**
     * @var Data
     */
    private $data;

    /**
     * Cursor constructor.
     *
     * @param CursorInterface $executorCursor
     * @param FetcherFactory $fetcherFactory
     */
    public function __construct(CursorInterface $executorCursor, FetcherFactory $fetcherFactory)
    {
        $this->executorCursor = $executorCursor;
        $this->fetcherFactory = $fetcherFactory;
    }

    /**
     * @param FetchConfig $fetchConfig
     *
     * @return array
     * @throws Exception
     */
    public function fetchAll(FetchConfig $fetchConfig)
    {
        $fetcher = $this->fetcherFactory->getByFetchStyle($fetchConfig->getFetchStyle());

        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return $this->data->fetchAll($fetcher, $fetchConfig);
    }

    /**
     * @param FetchConfig $fetchConfig
     *
     * @return bool
     * @throws Exception
     */
    public function fetch(FetchConfig $fetchConfig)
    {
        $rows = $this->fetchAll($fetchConfig);

        if (isset($rows[++$this->rowIndex])) {
            return $rows[$this->rowIndex];
        }

        return false;
    }

    /**
     * @return int
     */
    public function getAffectedRows()
    {
        return $this->executorCursor->getRowsAffected();
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->executorCursor->getFields();
    }

    /**
     * @return bool
     */
    private function isInitialized()
    {
        return $this->data !== null;
    }

    /**
     *
     */
    private function initialize()
    {
        $rows = [];

        while (($row = $this->executorCursor->next()) !== false) {
            $rows[] = $row;
        }

        $this->data = new Data($rows);
    }
}
