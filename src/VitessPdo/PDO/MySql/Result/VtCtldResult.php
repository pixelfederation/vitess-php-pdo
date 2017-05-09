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

namespace VitessPdo\PDO\MySql\Result;

use VitessPdo\PDO\MySql\Cursor\Cursor;
use VitessPdo\PDO\VtCtld\Result\Result as VtCtld;

/**
 * Description of class VtCtldResult
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result
 */
abstract class VtCtldResult extends Result
{

    /**
     * @var array
     */
    protected static $fields;

    /**
     * @var array
     */
    protected $specializedFIelds;

    /**
     * VtCtldResult constructor.
     *
     * @param VtCtld $result
     */
    public function __construct(VtCtld $result)
    {
        $data = $this->transform($result->getData());
        $cursor = new Cursor($data, $this->getFields());

        parent::__construct($cursor);
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        return is_array(static::$fields) ? static::$fields : $this->specializedFIelds;
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    abstract protected function transform($data);
}
