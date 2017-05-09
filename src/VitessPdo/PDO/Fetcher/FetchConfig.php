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

/**
 * Description of class FetchConfig
 *
 * @author  mfris
 * @package VitessPdo\PDO\Fetcher
 */
class FetchConfig
{

    /**
     * @var int
     */
    private $fetchStyle = CorePDO::FETCH_BOTH;

    /**
     * @var mixed
     */
    private $fetchArgument = null;

    /**
     * @var array
     */
    private $ctorArgs = [];

    /**
     * FetchConfig constructor.
     *
     * @param int   $fetchStyle
     * @param mixed $fetchArgument
     * @param array $ctorArgs
     */
    public function __construct($fetchStyle, $fetchArgument = null, array $ctorArgs = [])
    {
        $this->fetchStyle    = $fetchStyle;
        $this->fetchArgument = $fetchArgument;
        $this->ctorArgs      = $ctorArgs;
    }

    /**
     * @return int
     */
    public function getFetchStyle()
    {
        return $this->fetchStyle;
    }

    /**
     * @return mixed
     */
    public function getFetchArgument()
    {
        return $this->fetchArgument;
    }

    /**
     * @return array
     */
    public function getCtorArgs()
    {
        return $this->ctorArgs;
    }
}
