<?php

namespace Skypress\Taxonomy\Configuration;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

use Skypress\Core\Configuration\LoaderConfiguration;
use Skypress\Taxonomy\Entity\Taxonomy;

class Loader implements LoaderConfiguration
{
    const DIRECTORY_TYPE_DATA = 'taxonomies';

    public function __construct(LoaderConfiguration $loader)
    {
        $loader->setDirectoryTypeData(self::DIRECTORY_TYPE_DATA);
        $this->loader = $loader;
    }

    public function getData()
    {
        $data = [];
        try {
            $data = $this->loader->getData();
        } catch (\Exception $e) {
            return $data;
        }

        foreach ($data as $key => $item) {
            $data[$key] = new Taxonomy($item['key'], $item['post_types'], $item['params']);
        }

        return $data;
    }
}
