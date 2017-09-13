<?php

namespace panix\engine;

use Yii;
use yii\helpers\Url;

class Html extends \yii\helpers\Html {

    public static $iconPrefix = 'icon-';
    
    public static function icon($icon,$options=[]) {
        if(isset($options['class'])){
            $options['class'] .= ' '.self::$iconPrefix . $icon;
        }
        return static::tag('i', '', array_merge(['class'=>self::$iconPrefix . $icon],$options));
    }
    
    public static function aIconL($icon, $text, $url = null, $options = []) {
        if ($url !== null) {
            $options['href'] = Url::to($url);
        }
        $iconHtml = '<i class="' . self::$iconPrefix . ' ' . $icon . '"></i> ';
        return static::tag('a', $iconHtml . $text, $options);
    }

    public static function aIconR($icon, $text, $url = null, $options = []) {
        if ($url !== null) {
            $options['href'] = Url::to($url);
        }
        $iconHtml = '<i class="' . self::$iconPrefix . ' ' . $icon . '"></i> ';
        return static::tag('a', $text . $iconHtml, $options);
    }

    public static function text($message, $cut = false) {
        $config = Yii::$app->settings->get('app');
        //if (!$mode)
        //  $message = strip_tags(urldecode($message));
        //$message = htmlspecialchars(trim($message), ENT_QUOTES);
        // $message=html_entity_decode(htmlentities($message));
        if ($config['censor']) {
            $censor_l = explode(",", $config['censor_words']);
            foreach ($censor_l as $val)
                $message = preg_replace("#" . $val . "#iu", $config['censor_replace'], $message);
        }

        return self::highlight($message, $cut);
    }

    public static function highlight($text, $cut = false) {
        $params = (Yii::$app->request->get('word')) ? Yii::$app->request->get('word') : Yii::$app->request->get('tag');
        if ($params) {
            if ($cut) {
                $pos = max(mb_stripos($text, $params, null, Yii::$app->charset) - 100, 0);
                $fragment = mb_substr($text, $pos, 200, Yii::$app->charset);
            } else {
                $fragment = html_entity_decode(htmlentities($text));
            }
            if (is_array($params)) {
                foreach ($params as $k => $w) {
                    $fragment = str_replace($w, '<span class="highlight-word">' . $w . '</span>', $fragment);
                }
                $highlighted = $fragment;
            } else {
                $highlighted = str_replace($params, '<span class="highlight-word">' . $params . '</span>', $fragment);
            }
        } else {
            $highlighted = $text;
        }
        return $highlighted;
    }

}

?>
