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

use PDO as CorePDO;
use VitessPdo\PDO\Fetcher\FetchConfig;
use VitessPdo\PDO\Fetcher\FetcherInterface;

/**
 * Description of class Data
 *
 * @author  mfris
 * @package VitessPdo\PDO\PDOStatement
 */
class Data
{

    /**
     * @var array
     */
    private $rows = [];

    /**
     * Data constructor.
     *
     * @param array $rows
     */
    public function __construct(array $rows)
    {
        $this->rows[CorePDO::FETCH_BOTH] = $rows;
    }

    /**
     * @param FetcherInterface $fetcher
     * @param FetchConfig $fetchConfig
     *
     * @return array
     */
    public function fetchAll(FetcherInterface $fetcher, FetchConfig $fetchConfig)
    {
        $fetchStyle = $fetchConfig->getFetchStyle();

        if (!$this->hasRowsForFetchStyle($fetchStyle)) {
            $this->rows[$fetchStyle] = $fetcher->fetchAll($this->rows[CorePDO::FETCH_BOTH], $fetchConfig);
        }

        return $this->rows[$fetchStyle];
    }

    /**
     * @param $fetchStyle
     *
     * @return bool
     */
    private function hasRowsForFetchStyle($fetchStyle)
    {
        return isset($this->rows[$fetchStyle]);
    }
}
