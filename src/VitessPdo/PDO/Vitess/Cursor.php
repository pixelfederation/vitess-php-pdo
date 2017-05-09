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

namespace VitessPdo\PDO\Vitess;

use VitessPdo\PDO\QueryExecutor\CursorInterface;
use Vitess\Cursor as VitessCursor;

/**
 * Proxy for vitess cursor
 *
 * @author  mfris
 * @package VitessPdo\PDO\Vitess
 */
class Cursor implements CursorInterface
{

    /**
     * @var VitessCursor
     */
    private $cursor;

    /**
     * Cursor constructor.
     *
     * @param VitessCursor $cursor
     */
    public function __construct(VitessCursor $cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * @return int
     */
    public function getRowsAffected()
    {
        return $this->cursor->getRowsAffected();
    }

    /**
     * @return int
     */
    public function getInsertId()
    {
        return $this->cursor->getInsertId();
    }

    /**
     * @return \Vitess\Proto\Query\Field[]
     */
    public function getFields()
    {
        return $this->cursor->getFields();
    }

    /**
     * @return void
     */
    public function close()
    {
        $this->cursor->close();
    }

    /**
     * @return array|bool
     * @throws \Exception
     */
    public function next()
    {
        return $this->cursor->next();
    }
}
