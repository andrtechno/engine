<?php

namespace panix\engine\plugins\highlight;

use Yii;
use panix\engine\Html;
use panix\mod\plugins\BasePlugin;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Plugin Name: Censor
 * Plugin URI: https://pixelion.com.ua
 * Version: 1.0
 * Description: A Censor plugin
 * Author: Andrey S
 * Author URI: https://pixelion.com.ua
 */
class Highlight extends BasePlugin
{
    /**
     * @var array
     */
    public static $config = [
        'color' => 'red'
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

        $color = ArrayHelper::getValue($event->data, 'color', self::$config['color']);

        $highlight = Yii::$app->request->get('highlight');
        if ($highlight) {



            // $pattern =  '/<a[^>]*>'.$highlight.'<\/a>/i';


            $pattern =  "#" . Yii::$app->request->get('highlight') . "#iu";
           // echo $pattern;
           // die;
          // $content = static::getContentWithoutIgnoreBlocks($content);



            $event->sender->content = preg_replace($pattern, Html::tag('span', Yii::$app->request->get('highlight'), [
                'style' => "background-color:$color;"
            ]), $content);
        }
    }
    protected static function getIgnorePattern()
    {

        $patternIgnore = [
            '<a[^>]*>' => '<\/a>',
        ];
        $pattern = '(';
        foreach ($patternIgnore as $start => $end) {
            $pattern .= "$start.*?$end|";
        }
        $pattern .= '<.*?>)';

        return $pattern;
    }
    protected static function getContentWithoutIgnoreBlocks($content)
    {
        $pattern = static::getIgnorePattern();
        return preg_replace("~$pattern~isu", '', $content);
    }
}