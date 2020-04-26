<?php

namespace Skypress\Headless\Hooks\Api;

defined('ABSPATH') or die('Cheatin&#8217; uh?');

use Skypress\Core\Hooks\ExecuteHooks;
use WP_REST_Response;

class Menu implements ExecuteHooks
{
    public function __construct($baseEndpoint)
    {
        $this->baseEndpoint = $baseEndpoint;
    }

    public function hooks()
    {
        add_action('rest_api_init', [$this, 'register']);
    }

    public function register()
    {
        register_rest_route($this->baseEndpoint, '/menus', [
            'methods'  => 'GET',
            'callback' => [$this, 'getAll'],
        ]);

        register_rest_route($this->baseEndpoint, '/menus/(?P<id>[a-zA-Z0-9_-]+)', [
            'methods'  => 'GET',
            'callback' => [$this, 'single'],
        ]);

        register_rest_route($this->baseEndpoint, '/menu-locations/(?P<id>[a-zA-Z0-9_-]+)', [
            'methods'  => 'GET',
            'callback' => [$this, 'singleByLocation'],
        ]);

        register_rest_route($this->baseEndpoint, '/menu-locations', [
            'methods'  => 'GET',
            'callback' => [$this, 'locations'],
        ]);
    }

    /**
     * Get all registered menus.
     *
     * @return array List of menus with slug and description
     */
    public function getAll()
    {
        $menus = get_terms('nav_menu', ['hide_empty' => true]);

        foreach ($menus as $key => $menu) {
            $menu->items = $this->getMenuItems($menu->term_id);
            if (class_exists('acf')) {
                $fields = get_fields($menu);
                if (!empty($fields)) {
                    foreach ($fields as $field_key => $item) {
                        $menus[$key]->$field_key = $item;
                    }
                }
            }
        }

        return new WP_REST_Response($menus);
    }

    /**
     * Get all locations.
     *
     * @return array List of locations
     **/
    public function locations()
    {
        $nav_menu_locations = get_nav_menu_locations();
        $locations = [];
        foreach ($nav_menu_locations as $location_slug => $menu_id) {
            if (null !== get_term($location_slug)) {
                $locations[$location_slug] = get_term($location_slug);
            } else {
                $locations[$location_slug] = [];
            }
            $locations[$location_slug]['slug'] = $location_slug;
            $locations[$location_slug]['menu'] = get_term($menu_id);
        }

        return new WP_REST_Response($locations);
    }

    /**
     * Get menu's data from his id.
     *
     * @param array $data WP REST API data variable
     *
     * @return object Menu's data with his items
     */
    public function singleByLocation($data)
    {
        $menu = new \stdClass();

        if (($locations = get_nav_menu_locations()) && isset($locations[$data->get_param('id')])) {
            $menu = get_term($locations[$data->get_param('id')]);
            $menu->items = $this->getMenuItems($locations[$data->get_param('id')]);
        } else {
            return new WP_Error('not_found', 'No location has been found with this id or slug: `' . $data->get_param('id') . '`. Please ensure you passed an existing location ID or location slug.', ['status' => 404]);
        }

        // check if there is acf installed
        if (class_exists('acf')) {
            $fields = get_fields($menu);
            if (!empty($fields)) {
                foreach ($fields as $field_key => $item) {
                    // add all acf custom fields
                    $menu[$field_key] = $item;
                }
            }
        }

        return new WP_REST_Response($menu);
    }

    public function menuHasChild(&$parents, $child)
    {
        foreach ($parents as $key => $item) {
            if ($child->menu_item_parent == $item->ID) {
                if (!$item->child_items) {
                    $item->child_items = [];
                }
                array_push($item->child_items, $child);

                return true;
            }

            if ($item->child_items) {
                if ($this->menuHasChild($item->child_items, $child)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Retrieve items for a specific menu.
     *
     * @param $id Menu id
     *
     * @return array List of menu items
     */
    public function getMenuItems($id)
    {
        $menu_items = wp_get_nav_menu_items($id);

        // check if there is acf installed
        if (class_exists('acf')) {
            foreach ($menu_items as $menu_key => $menu_item) {
                $fields = get_fields($menu_item->ID);
                if (!empty($fields)) {
                    foreach ($fields as $field_key => $item) {
                        // add all acf custom fields
                        $menu_items[$menu_key]->$field_key = $item;
                    }
                }
            }
        }

        // wordpress does not group child menu items with parent menu items
        $child_items = [];
        // pull all child menu items into separate object
        foreach ($menu_items as $key => $item) {
            if ($item->menu_item_parent) {
                array_push($child_items, $item);
                unset($menu_items[$key]);
            }
        }

        // push child items into their parent item in the original object
        do {
            foreach ($child_items as $key => $child_item) {
                if ($this->menuHasChild($menu_items, $child_item)) {
                    unset($child_items[$key]);
                }
            }
        } while (count($child_items));

        return array_values($menu_items);
    }

    /**
     * Get menu's data from his id.
     *    It ensures compatibility for previous versions when this endpoint
     *    was allowing locations id in place of menus id).
     *
     * @param array $data WP REST API data variable
     *
     * @return object Menu's data with his items
     */
    public function single($data)
    {
        // This ensure retro compatibility with versions `<= 0.5` when this endpoint
        //   was allowing locations id in place of menus id
        if (has_nav_menu($data->get_param('id'))) {
            $menu = singleByLocation($data);
        } elseif (is_nav_menu($data->get_param('id'))) {
            if (is_int($data->get_param('id'))) {
                $id = $data->get_param('id');
            } else {
                $id = wp_get_nav_menu_object($data->get_param('id'));
            }
            $menu = get_term($id);
            $menu->items = $this->getMenuItems($id);
        } else {
            return new WP_Error('not_found', 'No menu has been found with this id or slug: `' . $data->get_param('id') . '`. Please ensure you passed an existing menu ID, menu slug, location ID or location slug.', ['status' => 404]);
        }

        // check if there is acf installed
        if (class_exists('acf')) {
            $fields = get_fields($menu);
            if (!empty($fields)) {
                foreach ($fields as $field_key => $item) {
                    // add all acf custom fields
                    $menu->$field_key = $item;
                }
            }
        }

        return new WP_REST_Response($menu);
    }
}
