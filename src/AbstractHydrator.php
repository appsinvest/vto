<?php

/**
 * AbstractHydrator
 * php version 7.4
 *
 * @category VTO
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/vto
 */

namespace SoftInvest\VTO;

use Closure;

class AbstractHydrator
{
    public static function getHydrator(Closure $callback, $className)
    {
        $arr = $callback();

        $result = $arr ? $className::hydrate([$arr->id => (array)$arr]) : null;

        return $result !== null ? $result->first() : null;
    }
}