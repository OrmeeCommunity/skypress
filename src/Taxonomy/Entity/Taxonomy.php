<?php

namespace Skypress\Taxonomy\Entity;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

class Taxonomy
{
    protected $slug = null;

    protected $postTypes = [];

    protected $args = null;

    public function __construct($slug, $postTypes, $args = [])
    {
        $this->setSlug($slug);
        $this->setPostTypes($postTypes);
        $this->setArgs($args);
    }

    public function setSlug($slug)
    {
        $this->slug = sanitize_key($slug);

        return $this;
    }

    public function setPostTypes($postTypes)
    {
        if (is_array($postTypes)) {
            foreach ($postTypes as $key => $value) {
                $this->postTypes[] = $value;
            }
        } else {
            $this->postTypes[] = $postTypes;
        }

        return $this;
    }

    public function setArgs($args)
    {
        $args = is_array($args) ? $args : [$args];
        $args['labels'] = (array_key_exists('labels', $args) && is_array($args['labels'])) ? $args['labels'] : $args['labels'] = [];

        $name = ucwords(str_replace('_', ' ', $this->getSlug()));

        $args['labels'] = array_merge(
                [
                    'name'                  => $name,
                    'singular_name'         => $name,
                ],
                $args['labels']
        );

        $args = array_merge(
                [
                    'hierarchical' => true,
                ],
                $args
        );

        $this->args = $args;

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

    public function getPostTypes()
    {
        return $this->postTypes;
    }
}
