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

namespace VitessPdo\PDO\PDOStatement;

use VitessPdo\PDO\Fetcher\Factory as FetcherFactory;
use VitessPdo\PDO\Fetcher\FetchConfig;
use VitessPdo\PDO\QueryExecutor\CursorInterface;
use VitessPdo\PDO\Exception;

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
     * @return array|bool
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
