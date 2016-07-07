<?php
/**
 * @author     mfris
 * @copyright  PIXELFEDERATION s.r.o
 */

namespace VitessPdo\PDO\VtCtld\Command;

use VitessPdo\PDO\VtCtld\Command\Parameter\Parameter;

/**
 * Description of class GetSchema
 *
 * @author  mfris
 * @package VitessPdo\PDO\VtCtld\Command
 */
class GetSchema extends Command
{

    /**
     * @const string
     */
    const PARAM_TABLET = 'tablet';

    /**
     * GetSchema constructor.
     *
     * @param string $tablet
     */
    public function __construct($tablet)
    {
        $this->set(self::PARAM_TABLET, new Parameter(self::PARAM_TABLET, $tablet));
    }
}
