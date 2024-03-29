<?php

declare(strict_types=1);

namespace Windwalker\Session\Handler;

use SessionHandler;

/**
 * Class PhpHandler
 *
 * @since 2.0
 */
class NativeHandler extends SessionHandler implements HandlerInterface
{
    /**
     * isSupported
     *
     * @return  boolean
     */
    public static function isSupported(): bool
    {
        return true;
    }
}
