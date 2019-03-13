<?php

namespace panix\engine\jui;

use Yii;
use panix\engine\Html;
use yii\base\InvalidArgumentException;
use yii\helpers\FormatConverter;
use yii\helpers\Json;
use yii\jui\DatePickerLanguageAsset;

class DatetimePicker extends DatePicker
{


    /**
     * time
     * datetime
     * date
     *
     * @var string
     */
    public $mode = 'datetime';
    public $timeFormat = 'hh:mm:ss';

    public function init()
    {
        if (!in_array($this->mode, ['datetime', 'time', 'date']))
            throw new InvalidArgumentException('DatetimePicker Не верный режим, используйте ' . implode(', ', ['datetime', 'time', 'date']));
        parent::init();
    }

    public function run()
    {

        echo $this->renderWidget() . "\n";

        $containerID = $this->inline ? $this->containerOptions['id'] : $this->options['id'];
        $language = $this->language ? $this->language : Yii::$app->language;

        if (strncmp($this->dateFormat, 'php:', 4) === 0) {
            $this->clientOptions['dateFormat'] = FormatConverter::convertDatePhpToJui(substr($this->dateFormat, 4));
        } else {
            $this->clientOptions['dateFormat'] = FormatConverter::convertDateIcuToJui($this->dateFormat, 'date', $language);
        }
        $view = $this->getView();
        $mode = $this->mode;

        $this->clientOptions['timeFormat'] = $this->timeFormat;

        if ($language !== 'en-US') {
            $assetBundle = DatePickerLanguageAsset::register($view);
            $assetBundle->language = $language;
            $options = Json::htmlEncode($this->clientOptions);
            $language = Html::encode($language);
            $view->registerJs("jQuery('#{$containerID}').{$mode}picker($.extend({}, $.datepicker.regional['{$language}'], $options));");
        } else {
            $this->registerClientOptions("{$mode}picker", $containerID);
        }

        $this->registerClientEvents("{$mode}picker", $containerID);
        DatetimePickerAsset::register($view);
    }

}
