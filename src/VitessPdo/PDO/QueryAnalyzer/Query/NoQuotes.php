<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\QueryAnalyzer\Query;

/**
 * Description of class NoQuotes
 *
 * @author  mfris
 * @package VitessPdo\PDO\QueryAnalyzer\Query
 */
class NoQuotes
{

    /**
     * @var array
     */
    private $data;

    /**
     * @const string
     */
    const KEY_PARTS = 'parts';

    /**
     * NoQuotes constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getParts()
    {
        return $this->data[self::KEY_PARTS];
    }
}
