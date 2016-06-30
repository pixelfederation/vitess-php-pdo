<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
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
        return is_array(self::$fields) ? self::$fields : $this->specializedFIelds;
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    abstract protected function transform($data);
}
