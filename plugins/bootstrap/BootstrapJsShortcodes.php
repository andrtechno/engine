<?php

namespace panix\engine\plugins\bootstrap;

use lo\plugins\BaseShortcode;
use panix\engine\plugins\bootstrap\widgets\Accordion;
use panix\engine\plugins\bootstrap\widgets\Tabs;

/**
 * Plugin Name: Bootstrap 4 JavaScript Shortcodes
 * Plugin URI: https://github.com/andrtechno/engine/tree/master/plugins/bootstrap
 * Version: 1.0
 * Description: A shortcodes pack with Bootstrap 4 JavaScript
 * Author: Andrey S
 * Author URI: https://github.com/andrtechno
 */
class BootstrapJsShortcodes extends BaseShortcode
{
    /**
     * @return array
     */
    public static function shortcodes()
    {
        return [
            'tabs' => [
                'callback' => [Tabs::class, 'widget'],
                'config' => [
                    'type' => 'tabs',
                    'xclass' => false
                ],
                'tooltip' => '[tabs][tab title="*"] ... [/tab][/tabs]'
            ],
            'accordion' => [
                'callback' => [Accordion::class, 'widget'],
                'config' => [
                    'xclass' => 'sh-accordion'
                ],
                'tooltip' => '[accordion][panel title="*"] ... [/panel][/accordion]'
            ],
        ];
    }
}

