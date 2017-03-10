<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class MessageOptions
 * @package Dot\Authentication\Web\Options
 */
class MessagesOptions extends AbstractOptions
{
    const AUTHENTICATION_FAILURE = 0;
    const AUTHENTICATION_INVALID_CREDENTIALS = 1;
    const AUTHENTICATION_IDENTITY_AMBIGUOUS = 2;
    const AUTHENTICATION_IDENTITY_NOT_FOUND = 3;
    const AUTHENTICATION_UNCATEGORIZED = 4;
    const AUTHENTICATION_MISSING_CREDENTIALS = 5;
    const AUTHENTICATION_SUCCESS = 6;
    const AUTHENTICATION_FAIL_UNKNOWN = 7;

    const UNAUTHORIZED = 8;

    protected $messages = [
        MessagesOptions::AUTHENTICATION_FAILURE =>
            'Authentication failed. Check your credentials and try again',

        MessagesOptions::AUTHENTICATION_INVALID_CREDENTIALS =>
            'Authentication failed. Check your credentials and try again',

        MessagesOptions::AUTHENTICATION_IDENTITY_AMBIGUOUS =>
            'Authentication failed. Check your credentials and try again',

        MessagesOptions::AUTHENTICATION_IDENTITY_NOT_FOUND =>
            'Authentication failed. Check your credentials and try again',

        MessagesOptions::AUTHENTICATION_UNCATEGORIZED =>
            'Authentication failed. Check your credentials and try again',

        MessagesOptions::AUTHENTICATION_MISSING_CREDENTIALS =>
            'Authentication failed. Missing or invalid credentials',

        MessagesOptions::AUTHENTICATION_SUCCESS =>
            'Welcome! You have successfully signed in',

        MessagesOptions::AUTHENTICATION_FAIL_UNKNOWN =>
            'Authentication failed. Check your credentials and try again',

        MessagesOptions::UNAUTHORIZED => 'You must sign in first to access the requested content',
    ];

    /**
     * MessagesOptions constructor.
     * @param null $options
     */
    public function __construct($options = null)
    {
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param $messages
     */
    public function setMessages(array $messages)
    {
        $this->messages = ArrayUtils::merge($this->messages, $messages, true);
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public function getMessage(int $key): string
    {
        return $this->messages[$key] ?? '';
    }
}
