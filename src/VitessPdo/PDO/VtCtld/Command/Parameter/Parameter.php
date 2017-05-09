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

namespace VitessPdo\PDO\VtCtld\Command\Parameter;

/**
 * Description of class Parameter
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command\Parameter
 */
class Parameter
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * NamedParameter constructor.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $this->sanitize($value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function sanitize($value)
    {
        $value = escapeshellarg(trim((string) $value));
        $value = str_replace("\n", ' ', $value);

        return $value;
    }
}
