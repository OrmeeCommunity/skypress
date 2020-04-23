<?php

namespace Skypress\Taxonomy\Hooks;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

use Skypress\Core\Hooks\ExecuteHooks;
use Skypress\Core\Configuration\LoaderConfiguration;

class RegisterTaxonomy implements ExecuteHooks
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

        foreach ($data as $taxonomy) {
            if (!taxonomy_exists($taxonomy->getSlug())) {
                register_taxonomy($taxonomy->getSlug(), $taxonomy->getPostTypes(), $taxonomy->getArgs());
            } else {
                register_taxonomy_for_object_type($taxonomy->getSlug(), $taxonomy->getPostTypes());
            }
        }
    }
}
