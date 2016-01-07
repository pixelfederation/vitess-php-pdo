<?php
/**
 * @author     mfris
 * @copyright  Pixel federation
 * @license    Internal use only
 */

namespace VitessPdo\PDO;

use VitessPdo\PDO;
use VTCursor;
use PDO as CorePDO;
use \Exception as CoreException;

/**
 * Description of class PDOStatement
 *
 * @author  mfris
 * @package VitessPdo\PDO
 */
class PDOStatement
{

    /**
     * @var string
     */
    private $query;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var Vitess
     */
    private $vitess;

    /**
     * @var ParamProcessor
     */
    private $paramProcessor;

    /**
     * @var VTCursor
     */
    private $cursor;

    /**
     * PDOStatement constructor.
     *
     * @param string $query
     * @param Vitess $vitess
     * @param ParamProcessor $paramProcessor
     */
    public function __construct($query, Vitess $vitess, ParamProcessor $paramProcessor)
    {
        $this->query  = $query;
        $this->vitess = $vitess;
        $this->paramProcessor = $paramProcessor;
    }

    /**
     * @param array|null $inputParameters
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(array $inputParameters = null)
    {
        try {
            if (!$inputParameters) {
                $inputParameters = $this->params;
            }

            if (array_key_exists(0, $inputParameters)) {
                $inputParameters = $this->repairUnnamedParamsArray($inputParameters);
            }

            $this->cursor = $this->vitess->executeRead($this->query, $inputParameters);
        } catch (CoreException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param int   $fetchStyle
     * @param int   $fetchArgument
     * @param array $ctorArgs
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetchAll(
        $fetchStyle = CorePDO::ATTR_DEFAULT_FETCH_MODE,
        $fetchArgument = CorePDO::FETCH_COLUMN,
        array $ctorArgs = []
    ) {
        $rows = [];

        while (($row = $this->cursor->next()) !== false) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param mixed $parameter
     * @param mixed $variable
     * @param int   $dataType
     * @param int   $length
     * @param array $driverOptions
     *
     * @return boolean
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function bindParam($parameter, &$variable, $dataType = CorePDO::PARAM_STR, $length = 0, $driverOptions = [])
    {
        return $this->bindValue($parameter, $variable, $dataType);
    }

    /**
     * @param mixed $parameter
     * @param mixed $value
     * @param int $dataType
     *
     * @return bool
     */
    public function bindValue($parameter, $value, $dataType = CorePDO::PARAM_STR)
    {
        try {
            if (is_int($parameter)) {
                --$parameter;
            }

            $variable = $this->paramProcessor->process($value, $dataType);
            $this->params[$parameter] = $variable;
        } catch (CoreException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function repairUnnamedParamsArray(array $params)
    {
        $newParams = [];

        foreach ($params as $key => $value) {
            $newParams['v' . ($key + 1)] = $value;
        }

        return $newParams;
    }
}
