<?php

namespace Skypress\CustomPostType\Configuration;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

use Skypress\Core\Configuration\LoaderConfiguration;
use Skypress\CustomPostType\Entity\CustomPostType;

class Loader implements LoaderConfiguration
{
    public function __construct(LoaderConfiguration $loader)
    {
        $loader->setDirectoryTypeData('custom-post-types');
        $this->loader = $loader;
    }

    public function getData()
    {
        $data = $this->loader->getData();
        foreach ($data as $key => $item) {
            $data[$key] = new CustomPostType($item['key'], $item['params']);
        }

        return $data;
    }
}
