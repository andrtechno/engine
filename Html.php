<?php

namespace panix\engine;

use Yii;
use yii\helpers\Url;
use panix\engine\emoji\Emoji;

class Html extends \yii\helpers\Html
{

    public static $iconPrefix = 'icon-';

    /**
     * Viber link
     * @param $text
     * @param string $number
     * @param array $options
     * @return string
     */
    public static function viber($text, $number = '', $options = [])
    {
        $number = CMS::phoneFormat($number);
        if (CMS::isMobile()) {
            return parent::a($text, 'viber://add?number=' . $number, $options);
        } else {
            $options['title'] = 'Должен быть устоновлен Viber для ПК';
            return parent::a($text, 'viber://chat?number=+' . $number, $options);
        }
    }

    /**
     * Telegram link
     * @param $text
     * @param string $name
     * @param array $options
     * @return string
     */
    public static function telegram($text, $name = '', $options = [])
    {
        return parent::a($text, 'tg://resolve?domain=' . $name, $options);
    }

    /**
     * WhatsApp link
     * @param $text
     * @param string $number
     * @param array $options
     * @return string
     */
    public static function whatsapp($text, $number = '', $options = [])
    {
        $number = CMS::phoneFormat($number);
        return parent::a($text, 'whatsapp://send?phone=+' . $number, $options);
    }

    /**
     * skypeCall link
     * @param $text
     * @param string $number
     * @param array $options
     * @return string
     */
    public static function skypeCall($text, $number, $options = [])
    {
        $number = CMS::phoneFormat($number);
        return parent::a($text, "skype:{$number}?call", $options);
    }

    /**
     * skypeChat link
     * @param $text
     * @param string $number
     * @param array $options
     * @return string
     */
    public static function skypeChat($text, $number, $options = [])
    {
        $number = CMS::phoneFormat($number);
        return parent::a($text, "skype:{$number}?chat", $options);
    }

    /**
     * Telephone link
     * @param $phone
     * @param array $options
     * @param null $replace
     * @return string
     */
    public static function tel($phone, $options = [], $replace = null)
    {
        return parent::a(CMS::phone_number_format($phone, $replace), 'tel:' . CMS::phoneFormat($phone), $options);
    }

    public static function error($model, $attribute, $options = [])
    {
        if (!isset($options['class'])) {
            $options['class'] = 'invalid-feedback';
        }
        return parent::error($model, $attribute, $options);
    }

    public static function icon($icon, $options = [])
    {
        if (isset($options['class'])) {
            $options['class'] .= ' ' . self::$iconPrefix . $icon;
        }
        return parent::tag('i', '', array_merge(['class' => self::$iconPrefix . $icon], $options));
    }

    public static function aIconL($icon, $text, $url = null, $options = [])
    {
        if ($url !== null) {
            $url = Url::to($url);
        }
        $icon = '<i class="' . self::$iconPrefix . ' ' . $icon . '"></i> ';
        return parent::a($icon . $text, $url, $options);
    }

    public static function aIconR($icon, $text, $url = null, $options = [])
    {
        if ($url !== null) {
            $url = Url::to($url);
        }
        $icon = '<i class="' . self::$iconPrefix . ' ' . $icon . '"></i> ';
        return parent::a($text . $icon, $url, $options);
    }

    public static function text($message)
    {
        $config = Yii::$app->settings->get('app');
        if ($config->censor) {
            $censor_l = explode(",", $config->censor_words);
            foreach ($censor_l as $val)
                $message = preg_replace("#" . $val . "#iu", $config->censor_replace, $message);
        }
        return Emoji::emoji_unified_to_html($message);
    }

    public static function highlight($text, $cut = false)
    {
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
        return Emoji::toHtml($highlighted);
    }

    public static function a($text, $url = null, $options = [])
    {
        if (!is_array($url)) {
            if (!isset($options['rel'])) {
                if (strpos(Yii::$app->request->hostName, 'app') === false) {
                    if (preg_match('%^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i', $url)) {
                        $options['rel'] = 'nofollow';
                    }
                }
            }
        }
        return parent::a($text, $url, $options);
    }

}
