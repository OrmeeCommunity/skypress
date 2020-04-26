<?php

namespace Skypress\Menu\Hooks;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

use Skypress\Core\Hooks\ExecuteHooks;
use Skypress\Core\Configuration\LoaderConfiguration;

class RegisterMenu implements ExecuteHooks
{
    public function __construct(LoaderConfiguration $loader)
    {
        $this->loader = $loader;
    }

    public function hooks()
    {
        add_action('init', [$this, 'register']);
    }

    public function register()
    {
        $data = $this->loader->getData();

        foreach ($data as $item) {
            register_nav_menu($item->getLocation(), $item->getDescription());
        }
    }
}
