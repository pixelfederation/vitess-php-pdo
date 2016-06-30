<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\MySql\Result\Show;

use VitessPdo\PDO\MySql\Result\VtCtldResult;
use VitessPdo\PDO\VtCtld\Result\GetKeyspaces;

/**
 * Description of class Databases
 *
 * @author  mfris
 * @package VitessPdo\PDO\MySql\Result\Show
 */
final class Databases extends VtCtldResult
{

    /**
     * @var array
     */
    protected static $fields = ['Database', 0];

    /**
     * Databases constructor.
     *
     * @param GetKeyspaces $result
     */
    public function __construct(GetKeyspaces $result)
    {
        parent::__construct($result);
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    protected function transform($data)
    {
        return array_map(function ($keyspace) {
            return [
                'Database' => $keyspace,
                0 => $keyspace,
            ];
        }, $data);
    }
}
