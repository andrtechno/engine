<?php
namespace panix\engine\plugins\content\codehighlight;

use panix\mod\plugins\BaseShortcode;

/**
 * Plugin Name: Code Highlighting
 * Plugin URI: https://github.com/andrtechno/engine/tree/master/plugins/content/codehighlight
 * Version: 1.12
 * Description: A shortcode for code highlighting in view. Use as [code lang="php"]...content...[/code]
 * Author: Andrey S
 * Author URI: https://github.com/andrtechno
 */
class CodeShortcodes extends BaseShortcode
{
    /**
     * @return array
     */
    public static function shortcodes()
    {
        return [
            'code' => [
                'callback' => [CodeWidget::class, 'widget'],
                'tooltip' => '[code style="*" lang="*"] ... [/code]',
                'config' => [
                    'style' => 'github',
                    'lang' => 'php'
                ]
            ],
        ];
    }
}