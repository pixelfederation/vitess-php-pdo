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

namespace VitessPdo\PDO\VtCtld\Result;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Result\GetSchema\Schema;

/**
 * Description of class GetSchema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result
 */
final class GetSchema extends Result
{

    /**
     * @return string
     */
    public function getKeyspace()
    {
        return $this->dsn->getConfig()->getKeyspace();
    }

    /**
     * @throws Exception
     */
    protected function parse()
    {
        $data = json_decode(trim($this->responseString), true);

        $this->data = new Schema($data);
    }
}
