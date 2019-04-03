<?php

namespace panix\engine\plugins\web\bbcode;

use panix\mod\plugins\BaseShortcode;
use panix\engine\plugins\web\bbcode\widgets\ColorWidget;
use panix\engine\plugins\web\bbcode\widgets\LinkWidget;
use yii\helpers\Html;

/**
 * Plugin Name: BB code
 * Plugin URI: https://github.com/andrtechno/engine/tree/master/plugins/web/bbcode
 * Version: 1.0
 * Description: A shortcode for bb codes in view. convert BBcode to Html [youtube code="ZM2tVuy8B_Y"]
 * Author: Andrey S
 * Author URI: https://github.com/andrtechno
 */
class BBCodeShortcodes extends BaseShortcode
{
    /**
     * @return array
     */
    public static function shortcodes()
    {
        return [
            // show original link
            'color' => function ($attrs, $content, $tag) {
                $options = [];
                $options['style'] = "color:";

                    return Html::tag('span', 'dsasdasdasda', $options);


            },
            'link' => [
                'callback' => [LinkWidget::class, 'widget'],
                'config' => [
                    'url' => '/mypage',
                    'alt' => false,
                    'title' => false,
                    'target' => false
                ],
                'tooltip' => '[link url=*]'
            ],
            'text' => [
                'callback' => [ColorWidget::class, 'widget'],
                'config' => [
                    'color' => 'red',
                    'bgcolor' => 'red',
                ],
                'tooltip' => '[text color="#990000" bgcolor="#990000"]'
            ]
        ];
    }
}

