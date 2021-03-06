<?php
/**
 * This class defines the scalar token of GrabQL Interpreter.
 *
 * @package     GrabQL
 * @subpackage  Interpreter
 * @author      Alberto Arena <arena.alberto@gmail.com>
 * @copyright   Alberto Arena <arena.alberto@gmail.com>
 * @license     https://github.com/albertoarena/grabql/blob/master/LICENCE  MIT, BSD and GPL
 * @link        http://albertoarena.co.uk/grabql
 *
 */
namespace GrabQL\Interpreter\Token;

use GrabQL\Interpreter\Parser\Parser\Lexer;
use GrabQL\Runtime\Type\Reference;
use GrabQL\Runtime\FilterFactory;

class Scalar extends AbstractToken
{
    /** @var bool */
    protected $isObject;

    /** @var bool */
    protected $openCurlyBraces;

    /** @var bool */
    protected $isCommand;

    public function __construct()
    {
        $this->isCommand = false;
    }

    /**
     * @param bool|null $isCommand
     * @return bool
     */
    public function isCommand($isCommand = null)
    {
        if (!is_null($isCommand)) {
            $this->isCommand = $isCommand;
        }
        return $this->isCommand;
    }

    /**
     * @return array|mixed
     */
    public function internalProcess()
    {
        //\GrabQL\Utils\Logger::writePrefix('Scalar.process', array('token' => $this->token, 'data' => $this->data));
        $this->isObject = false;
        $this->openCurlyBraces = false;

        $items = array();
        foreach ($this->data as $token) {
            $item = $this->parseElement($token);
            if ($item !== null) {
                $items[] = $item;
            }
        }

        if ($this->isObject || $this->isCommand) {
            return $items;
        } else {
            return $items[0];
        }
    }

    /**
     * @param array $token
     * @return Reference|null
     * @throws \Exception
     */
    protected function parseElement($token)
    {
        //\GrabQL\Utils\Logger::writePrefix('Scalar.parseElement', $token);

        $ret = null;
        switch ($token['type']) {

            case Lexer::T_IDENTIFIER:
                $id = $token['value'];
                $filter = null;
                #GrabQL\Utils\Logger::writePrefix('Scalar.parseElement', 'identifier: ' . $id);

                // Check if identifier contains a filter ...
                if (preg_match('/([^:]*):([^\s]*)/', $id, $matches)) {
                    $id = $matches[1];
                    $filter = $matches[2];
                }

                // Create a reference to an existing symbol
                $obj = $this->runtime->symbols()->get($id);
                if (is_null($obj)) {
                    throw new \Exception('Referenced object not found: ' . $id);
                }
                $ret = new Reference(null, $obj);

                // Add eventual filter to the reference
                if (!is_null($filter)) {

                    $filterInstance = FilterFactory::build($filter);
                    if (is_null($filterInstance)) {
                        throw new \Exception('Filter not found ' . $filter);
                    }
                    $ret->setFilter($filterInstance);
                }
                break;

            case Lexer::T_COMMA:
                break;

            case Lexer::T_OPEN_CURLY_BRACE:
                //GrabQL\Utils\Logger::writePrefix('Scalar.parseElement', 'open curly brace');
                $this->isObject = true;
                $this->openCurlyBraces = true;
                break;

            case Lexer::T_CLOSE_CURLY_BRACE:
                //GrabQL\Utils\Logger::writePrefix('Scalar.parseElement', 'close curly brace');
                $this->openCurlyBraces = false;
                break;

            default:
                //GrabQL\Utils\Logger::writePrefix('Scalar.parseElement', 'value: ' . $token['value']);
                $ret = $token['value'];
                break;

        }
        return $ret;
    }

} 