<?php

namespace panix\engine\plugins\censor;

use panix\mod\plugins\BasePlugin;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Plugin Name: Censor
 * Plugin URI: https://pixelion.com.ua
 * Version: 1.0
 * Description: Censor plugin
 * Author: Andrey S
 * Author URI: https://pixelion.com.ua
 */
class Censor extends BasePlugin
{
    /**
     * @var array
     */
    public static $config = [
        'search' => [
            'lox', 'poc'
        ],
        'replace' => '[CENSORED]',
    ];

    /**
     * @return array
     */
    public static function events()
    {
        return [
            Response::class => [
                Response::EVENT_AFTER_PREPARE => ['run', self::$config]
            ]
        ];
    }

    /**
     * @param $event
     */
    public static function run($event)
    {
        if (!$content = $event->sender->content) return;

        $search = ArrayHelper::getValue($event->data, 'search', self::$config['search']);
        $replace = ArrayHelper::getValue($event->data, 'replace', self::$config['replace']);

        foreach ($search as $val) {
            $content = preg_replace("#" . $val . "#iu", $replace, $content);
            //  $content = str_replace($val , $replace, $content);
        }

        $event->sender->content = $content;
        /* $event->sender->content = str_replace($search, Html::tag('span', $replace, [
             'style' => "background-color:$color;"
         ]), $content);*/
    }
}