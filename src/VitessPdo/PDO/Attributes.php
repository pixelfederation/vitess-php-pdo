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

namespace VitessPdo\PDO;

use PDO as CorePDO;

/**
 * Description of class Attributes
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class Attributes
{

    /**
     * @const string
     */
    const DRIVER_NAME = 'vitess';

    /**
     * @const string
     */
    const STATEMENT_CLASS = PDOStatement::class;

    /**
     * @var array
     */
    private $attributes = [
        CorePDO::ATTR_ERRMODE => CorePDO::ERRMODE_SILENT,
        CorePDO::ATTR_DEFAULT_FETCH_MODE => CorePDO::FETCH_BOTH,
        CorePDO::ATTR_DRIVER_NAME => self::DRIVER_NAME,
        CorePDO::ATTR_STATEMENT_CLASS => self::STATEMENT_CLASS,
    ];

    /**
     * @var array
     */
    private static $implementedAttributes = [
        CorePDO::ATTR_ERRMODE => CorePDO::ATTR_ERRMODE,
        CorePDO::ATTR_DEFAULT_FETCH_MODE => CorePDO::ATTR_DEFAULT_FETCH_MODE,
        CorePDO::ATTR_DRIVER_NAME => CorePDO::ATTR_DRIVER_NAME,
        CorePDO::ATTR_STATEMENT_CLASS => CorePDO::ATTR_STATEMENT_CLASS,
    ];

    /**
     * @var array
     */
    private static $validators = [
        CorePDO::ATTR_STATEMENT_CLASS => 'validateStatementClass',
    ];

    /**
     * @param $attribute
     *
     * @return bool
     */
    public function isImplemented($attribute)
    {
        return isset(self::$implementedAttributes[$attribute]);
    }

    /**
     * @param int $attribute
     * @param mixed $value
     *
     * @return Attributes
     * @throws Exception
     */
    public function set($attribute, $value)
    {
        if (!$this->isImplemented($attribute)) {
            throw new Exception("PDO parameter not implemented - {$attribute}");
        }

        if ($this->hasValidator($attribute)) {
            $validator = $this->getValidator($attribute);
            $value = $this->{$validator}($value);
        }

        $this->attributes[$attribute] = $value;

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return mixed
     * @throws Exception
     */
    public function get($attribute)
    {
        if (!isset($this->attributes[$attribute])) {
            return null;
        }

        return $this->attributes[$attribute];
    }

    /**
     * @return bool
     */
    public function isErrorModeSilent()
    {
        try {
            return $this->get(CorePDO::ATTR_ERRMODE) === CorePDO::ERRMODE_SILENT;
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isErrorModeWarning()
    {
        try {
            return $this->get(CorePDO::ATTR_ERRMODE) === CorePDO::ERRMODE_WARNING;
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isErrorModeException()
    {
        try {
            return $this->get(CorePDO::ATTR_ERRMODE) === CorePDO::ERRMODE_EXCEPTION;
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * @param int $attribute
     *
     * @return bool
     */
    private function hasValidator($attribute)
    {
        return isset(self::$validators[$attribute]);
    }

    /**
     * @param int $attribute
     *
     * @return string
     */
    private function getValidator($attribute)
    {
        return self::$validators[$attribute];
    }

    /**
     * @param array $input
     *
     * @return string
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function validateStatementClass(array $input)
    {
        if (!isset($input[0])) {
            Exception::newStatementClassException();
        }

        $statementClass = $input[0];

        if (!class_exists($statementClass)) {
            Exception::newStatementClassException();
        }

        return $statementClass;
    }
}
