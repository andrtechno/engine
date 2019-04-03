<?php
namespace panix\engine\plugins\bootstrap;

use panix\mod\plugins\BaseShortcode;
use panix\engine\plugins\bootstrap\widgets\Col;
use panix\engine\plugins\bootstrap\widgets\Container;
use panix\engine\plugins\bootstrap\widgets\Row;

/**
 * Plugin Name: Bootstrap 4 Css Shortcodes
 * Plugin URI: https://github.com/andrtechno/engine/tree/master/plugins/bootstrap
 * Version: 1.0
 * Description: A shortcodes pack with Bootstrap 4 css elements
 * Author: Andrey S
 * Author URI: https://github.com/andrtechno
 */
class BootstrapCssShortcodes extends BaseShortcode
{
    /**
     * @return array
     */
    public static function shortcodes()
    {
        return [
            // Grid system
            'container' => [
                'callback' => [Container::class, 'widget'],
                'config' => [
                    'xlass' => false,
                    'fluid' => false
                ],
                'tooltip' => '[container] ... [/container]'
            ],
            'row' => [
                'callback' => [Row::class, 'widget'],
                'config' => [
                    'xlass' => false,
                ],
                'tooltip' => '[row] ... [/row]'
            ],
            'col' => [
                'callback' => [Col::class, 'widget'],
                'config' => [
                    "lg" => false,
                    "md" => 12,
                    "sm" => false,
                    "xl" => false,
                    "lg_offset" => false,
                    "md_offset" => false,
                    "sm_offset" => false,
                    "xl_offset" => false,
                    "lg_pull" => false,
                    "md_pull" => false,
                    "sm_pull" => false,
                    "xl_pull" => false,
                    "lg_push" => false,
                    "md_push" => false,
                    "sm_push" => false,
                    "xl_push" => false,
                    "xclass" => false
                ],
                'tooltip' => '[col md=6] ... [/col]'
            ],
        ];
    }
}

