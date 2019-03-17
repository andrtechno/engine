<?php
namespace panix\engine\plugins\bootstrap;

use lo\plugins\BaseShortcode;
use panix\engine\plugins\bootstrap\widgets\Alert;
use panix\engine\plugins\bootstrap\widgets\Badge;

/**
 * Plugin Name: Bootstrap 3 Components Shortcodes
 * Plugin URI: https://github.com/andrtechno/engine/tree/master/plugins/bootstrap
 * Version: 1.0
 * Description: A shortcodes pack with Bootstrap 3 components
 * Author: Andrey S
 * Author URI: https://github.com/andrtechno
 */
class BootstrapComponentsShortcodes extends BaseShortcode
{
    /**
     * @return array
     */
    public static function shortcodes()
    {
        return [
            'alert' => [
                'callback' => [Alert::class, 'widget'],
                'config' => [
                    'type' => Alert::TYPE_INFO,
                    'close' => false
                ],
                'tooltip' => '[alert close=1] ... [/alert]'
            ],
            'badge' => [
                'callback' => [Badge::class, 'widget'],
                'config' => [
                    'type' => Badge::TYPE_PRIMARY,
                    'text' => 'badge'
                ],
                'tooltip' => '[badge text="*"]'
            ],
        ];
    }
}

