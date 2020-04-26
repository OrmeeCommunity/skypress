<?php

namespace Skypress\Headless;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

abstract class Settings
{
    const BASE_ENDPOINT = 'skypress';

    const VERSION = 'v1';

    public static function getBaseEndpoint()
    {
        return sprintf('%s/%s', self::BASE_ENDPOINT, self::VERSION);
    }
}
