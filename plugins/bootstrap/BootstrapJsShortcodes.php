<?php

namespace lo\shortcodes\bootstrap;

use lo\plugins\BaseShortcode;
use lo\shortcodes\bootstrap\widgets\Accordion;
use lo\shortcodes\bootstrap\widgets\Tabs;

/**
 * Plugin Name: Bootstrap 3 JavaScript Shortcodes
 * Plugin URI: https://github.com/andrtechno/engine/tree/master/plugins/bootstrap
 * Version: 1.0
 * Description: A shortcodes pack with Bootstrap 3 JavaScript
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

