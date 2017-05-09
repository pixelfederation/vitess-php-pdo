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

namespace VitessPdo\PDO\Fetcher;

use PDO as CorePDO;
use VitessPdo\PDO\Exception;

/**
 * Description of class Factory
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
class Factory
{

    /**
     * @var array
     */
    private $styleToFetcherMap = [
        CorePDO::FETCH_BOTH => BothFetcher::class,
        CorePDO::FETCH_NUM => NumFetcher::class,
        CorePDO::FETCH_ASSOC => AssocFetcher::class,
        CorePDO::FETCH_COLUMN => ColumnFetcher::class,
        CorePDO::FETCH_KEY_PAIR => KeyPairFetcher::class
    ];

    /**
     * @var array
     */
    private $fetchers = [];

    /**
     * @param int $fetchMode
     *
     * @return FetcherInterface
     * @throws Exception
     */
    public function getByFetchStyle($fetchMode)
    {
        if (!isset($this->fetchers[$fetchMode])) {
            if (!isset($this->styleToFetcherMap[$fetchMode])) {
                throw new Exception('Fetch style not supported.');
            }

            $this->fetchers[$fetchMode] = new $this->styleToFetcherMap[$fetchMode]();
        }

        return $this->fetchers[$fetchMode];
    }
}
