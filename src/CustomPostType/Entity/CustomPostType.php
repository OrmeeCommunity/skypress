<?php

namespace Skypress\CustomPostType\Entity;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

class CustomPostType
{
    protected $slug = null;

    protected $args = [];

    public function __construct(string $slug, array $args = [])
    {
        $this->setSlug($slug);
        $this->setArgs($args);
    }

    public function setSlug($slug)
    {
        $this->slug = sanitize_key($slug);
    }

    public function setArgs($args)
    {
        $name = ucwords(preg_replace('#([_-])#', ' ', $this->getSlug()));

        $this->args['labels'] = array_merge(
            [
                'name'          => $name,
                'singular_name' => $name,
                'menu_name'     => $name,
            ],
            $args['labels']
        );

        $this->args = array_merge(
                [
                    'public'            => true,
                    'show_ui'           => true,
                    'show_in_menu'      => true,
                    'show_in_admin_bar' => true,
                    'has_archive'       => true,
                    'supports'          => ['title', 'editor'],
                ],
                $args
        );

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function getLabels()
    {
        return $this->args['labels'];
    }
}
