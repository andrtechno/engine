<?php

namespace panix\engine;

use yii\helpers\Url;

class Html extends \yii\helpers\BaseHtml {

    public static $iconPrefix = '';

    public static function aIconL($icon, $text, $url = null, $options = []) {
        if ($url !== null) {
            $options['href'] = Url::to($url);
        }
        $iconHtml = '<i class="'.self::$iconPrefix.' ' . $icon . '"></i> ';
        return static::tag('a', $iconHtml . $text, $options);
    }

    public static function aIconR($icon, $text, $url = null, $options = []) {
        if ($url !== null) {
            $options['href'] = Url::to($url);
        }
        $iconHtml = '<i class="'.self::$iconPrefix.' ' . $icon . '"></i> ';
        return static::tag('a', $text . $iconHtml, $options);
    }

}

?>
