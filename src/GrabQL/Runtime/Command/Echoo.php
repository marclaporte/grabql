<?php
/**
 * This class defines the echo command of GrabQL Runtime.
 *
 * @package     GrabQL
 * @subpackage  Runtime
 * @author      Alberto Arena <arena.alberto@gmail.com>
 * @copyright   Alberto Arena <arena.alberto@gmail.com>
 * @license     http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link        http://albertoarena.co.uk/grabql
 *
 */
namespace GrabQL\Runtime\Command;

use GrabQL\Runtime\Io\Out;

class Echoo extends Command
{
    /**
     * @var \GrabQL\Runtime\Io\Out
     */
    protected $out;

    public function __construct()
    {
        $this->out = new Out();
    }

    /**
     * @param array|null $args
     * @return mixed|void
     */
    public function execute($args = null)
    {
        $this->out->write($args);
    }
} 