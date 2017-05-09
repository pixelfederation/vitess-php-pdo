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

use VitessPdo\PDO\MySql\Cursor\Cursor;
use VitessPdo\PDO\MySql\Result\Result;
use VitessPdo\PDO\QueryAnalyzer\QueryInterface;

/**
 * Description of class Member
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Handler\Chain
 */
abstract class Member implements MemberInterface
{

    /**
     * @var MemberInterface
     */
    private $successor;

    /**
     * @param MemberInterface $successor
     *
     * @return MemberInterface
     */
    public function setSuccessor(MemberInterface $successor)
    {
        $this->successor = $successor;
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     */
    final public function handle(QueryInterface $query)
    {
        $result = $this->process($query);

        if ($result === null && $this->successor !== null) {
            $result = $this->successor->handle($query);
        }

        return $result;
    }

    /**
     * @param QueryInterface $query
     *
     * @return Result|null
     */
    abstract protected function process(QueryInterface $query);

    /**
     * @param array $data
     * @param array $fields
     *
     * @return Result
     */
    protected function getResultFromData(array $data, array $fields = [])
    {
        $cursor = new Cursor($data, $fields);

        return new Result($cursor);
    }
}
