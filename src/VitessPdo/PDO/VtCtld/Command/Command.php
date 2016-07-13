<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the PIXEL FEDERATION, s.r.o. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL PIXEL FEDERATION, s.r.o. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace VitessPdo\PDO\VtCtld\Command;

use VitessPdo\PDO\Exception;
use VitessPdo\PDO\VtCtld\Command\Parameter\NamedParameter;

/**
 * Description of class Command
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command
 */
abstract class Command
{

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->name === null) {
            $this->name = array_pop(explode('\\', get_called_class()));
        }

        return $this->name;
    }

    /**
     * @return string
     */
    public function getResultClass()
    {
        return str_replace('Command', 'Result', __NAMESPACE__) . '\\' . $this->getName();
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $parts = [get_called_class()];

        foreach ($this->getParams() as $param) {
            $parts[] = (string) $param;
        }

        return implode('|', $parts);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return Command
     */
    protected function set($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @throws Exception
     */
    protected function get($key)
    {
        if (!isset($this->params[$key])) {
            throw new Exception("Key not found - {$key}.");
        }
    }
}
