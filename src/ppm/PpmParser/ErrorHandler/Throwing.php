<?php declare(strict_types=1);

namespace PpmParser\ErrorHandler;

use PpmParser\Error;
use PpmParser\ErrorHandler;

/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error) {
        throw $error;
    }
}
