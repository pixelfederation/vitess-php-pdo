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

namespace VitessPdo\PDO\QueryAnalyzer\Query;

/**
 * Description of class NoQuotes
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
class NoQuotes
{

    /**
     * @var array
     */
    private $data;

    /**
     * @const string
     */
    const KEY_PARTS = 'parts';

    /**
     * NoQuotes constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->data[self::KEY_PARTS];
    }

    /**
     * @return string
     */
    public function getPartsAsString()
    {
        return implode(' ', $this->getParts());
    }
}
