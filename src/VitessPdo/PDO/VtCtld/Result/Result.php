<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Result;

/**
 * Description of class Result
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Result
 */
abstract class Result
{

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
