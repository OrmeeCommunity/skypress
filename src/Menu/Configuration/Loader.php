<?php

namespace Skypress\Menu\Configuration;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

use Skypress\Core\Configuration\LoaderConfiguration;
use Skypress\Menu\Entity\Menu;

class Loader implements LoaderConfiguration
{
    public function __construct(LoaderConfiguration $loader)
    {
        $loader->setDirectoryTypeData('menus');
        $this->loader = $loader;
    }

    public function getData()
    {
        $data = $this->loader->getData();
        foreach ($data as $key => $item) {
            $data[$key] = new Menu($item['location'], $item['description']);
        }

        return $data;
    }
}
