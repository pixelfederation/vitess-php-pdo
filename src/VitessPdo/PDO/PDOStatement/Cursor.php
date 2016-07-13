<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the PIXEL FEDERATION, s.r.o. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL PIXEL FEDERATION, s.r.o. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
