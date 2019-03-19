<?php

namespace panix\engine\widgets\inputmask;

use Yii;
use panix\engine\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
/**
 * 
 * https://github.com/RobinHerbots/Inputmask/releases
 */
class InputMask extends InputWidget {

    public $defaultOptions = [];

    function run() {
        $this->defaultOptions = [
            'mask' => '+3 (999) 999-99-99',
        ];

        if (isset($this->options['class'])) {
            $this->options['class'] .= $this->options['class'];
        } else {
            $this->options['class'] = 'form-control';
        }
        $this->options['type']='tel';
        if ($this->hasModel())
            echo Html::activeTextInput($this->model, $this->attribute, $this->options);
        else
            echo Html::textInput($this->name, $this->value, $this->options);


        $view = Yii::$app->view;
        InputMaskAsset::register($view);
        $options = Json::encode($this->defaultOptions);
        $js[] = "$('#{$this->options['id']}').inputmask({$options});";
        $view->registerJs(implode("\n", $js));


        //  $options = CJavaScript::encode(CMap::mergeArray($this->defaultOptions, $this->options));
        //   $dir = dirname(__FILE__) . DS . 'assets';
        //    $baseUrl = Yii::app()->getAssetManager()->publish($dir, false, -1, YII_DEBUG);
        //  $cs = Yii::app()->getClientScript();
        // $cs->registerScriptFile($baseUrl . "/js/inputmask.min.js");
        ///  $cs->registerScriptFile($baseUrl . "/js/jquery.inputmask.min.js");
        // $cs->registerScriptFile($baseUrl . "/js/inputmask.phone.extensions.min.js");
        //$js = "$('#{$id}').inputmask({$options});";
        //$cs->registerScript(__CLASS__ . '#' . $id, $js);
    }

}
