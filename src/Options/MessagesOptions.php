<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 10/10/2016
 * Time: 7:10 PM
 */

namespace Dot\Authentication\Web\Options;


use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class MessageOptions
 * @package Dot\Authentication\Web\Options
 */
class MessagesOptions extends AbstractOptions
{
    const AUTHENTICATION_FAIL_MESSAGE = 0;
    const UNAUTHORIZED_MESSAGE = 1;

    protected $messages = [
        MessagesOptions::AUTHENTICATION_FAIL_MESSAGE =>
            'Authentication failed. Check your credentials and try again',

        MessagesOptions::UNAUTHORIZED_MESSAGE =>
            'You must be authenticated to access this content',
    ];


    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param $messages
     * @return $this
     */
    public function setMessages($messages)
    {
        $this->messages = ArrayUtils::merge($this->messages, $messages, true);
        return $this;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public function getMessage($key)
    {
        return isset($this->messages[$key]) ? $this->messages[$key] : null;
    }
}