<?php

namespace Skypress\Menu\Entity;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

class Menu
{
    protected $location = null;

    protected $description = '';

    public function __construct(string $location, string $description)
    {
        $this->setLocation($location);
        $this->setDescription($description);
    }

    public function setLocation($location)
    {
        $this->location = sanitize_key($location);

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
