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
    public $timeFormat = 'hh:HH:ss';
    public $dateFormat = 'yyyy-MM-dd';

    public $timeOnlyTitle = 'Choose Time';
    public $timeText = '';
    public $hourText = 'Час';
    public $minuteText = 'Минуты';
    public $secondText = 'Секунды';
    public $millisecText, $microsecText, $timezoneText;


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

        if (in_array($this->mode, ['datetime', 'time'])) {
            $this->clientOptions['timeFormat'] = $this->timeFormat;
            $this->clientOptions['timeOnlyTitle'] = 'Выберите время';
            $this->clientOptions['timeText'] = 'Время';
            $this->clientOptions['hourText'] = 'Час';
            $this->clientOptions['minuteText'] = 'Минуты';
            $this->clientOptions['secondText'] = 'Секунды';
            $this->clientOptions['microsecText'] = 'Microsecond';
            $this->clientOptions['millisecText'] = 'Millisecond';
            $this->clientOptions['timezoneText'] = 'Timezone';
        }


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


    /**
     * Renders the DatePicker widget.
     * @return string the rendering result.
     */
    protected function renderWidget()
    {
        $contents = [];

        // get formatted date value
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }
        if ($value !== null && $value !== '') {
            // format value according to dateFormat
            try {
                if($this->mode == 'time'){
                    $value = Yii::$app->formatter->asTime($value, $this->timeFormat);
                }else{
                    $value = Yii::$app->formatter->asDate($value, $this->dateFormat);
                }

            } catch(InvalidArgumentException $e) {
                // ignore exception and keep original value if it is not a valid date
            }
        }
        $options = $this->options;
        $options['value'] = $value;

        if ($this->inline === false) {
            // render a text input
            if ($this->hasModel()) {
                $contents[] = Html::activeTextInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::textInput($this->name, $value, $options);
            }
        } else {
            // render an inline date picker with hidden input
            if ($this->hasModel()) {
                $contents[] = Html::activeHiddenInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::hiddenInput($this->name, $value, $options);
            }
            $this->clientOptions['defaultDate'] = $value;
            $this->clientOptions['altField'] = '#' . $this->options['id'];
            $contents[] = Html::tag('div', null, $this->containerOptions);
        }

        return implode("\n", $contents);
    }

}
