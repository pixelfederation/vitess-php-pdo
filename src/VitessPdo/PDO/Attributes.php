<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o.
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
        CorePDO::ATTR_STATEMENT_CLASS => 'class_exists',
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

            if (!$validator($value)) {
                throw new Exception(
                    "General error: PDO::ATTR_STATEMENT_CLASS requires format array(classname, array(ctor_args)); "
                    . "the classname must be a string specifying an existing class"
                );
            }
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
}
