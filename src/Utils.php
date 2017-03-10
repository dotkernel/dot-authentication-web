<?php
/**
 * @see https://github.com/dotkernel/dot-authentication-web/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-authentication-web/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Authentication\Web;

use Dot\Authentication\AuthenticationResult;
use Dot\Authentication\Web\Options\MessagesOptions;

/**
 * Class Utils
 * @package Dot\Authentication
 */
final class Utils
{
    /** @var array */
    public static $authResultCodeToMessageMap = [
        AuthenticationResult::FAILURE => MessagesOptions::AUTHENTICATION_FAILURE,
        AuthenticationResult::FAILURE_INVALID_CREDENTIALS => MessagesOptions::AUTHENTICATION_INVALID_CREDENTIALS,
        AuthenticationResult::FAILURE_IDENTITY_AMBIGUOUS => MessagesOptions::AUTHENTICATION_IDENTITY_AMBIGUOUS,
        AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND => MessagesOptions::AUTHENTICATION_IDENTITY_NOT_FOUND,
        AuthenticationResult::FAILURE_UNCATEGORIZED => MessagesOptions::AUTHENTICATION_UNCATEGORIZED,
        AuthenticationResult::FAILURE_MISSING_CREDENTIALS => MessagesOptions::AUTHENTICATION_MISSING_CREDENTIALS,
        AuthenticationResult::SUCCESS => MessagesOptions::AUTHENTICATION_SUCCESS
    ];
}
