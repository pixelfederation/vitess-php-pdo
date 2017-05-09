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

namespace VitessPdo\PDO\MySql\QueryHandler;

use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class MemberInterface
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
interface MemberInterface
{

    /**
     * @param MemberInterface $successor
     *
     * @return void
     */
    public function setSuccessor(MemberInterface $successor);

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     */
    public function handle(QueryInterface $query);
}
