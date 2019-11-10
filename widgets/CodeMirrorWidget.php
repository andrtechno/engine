<?php

namespace panix\engine\widgets;

use panix\engine\Html;
use yii\widgets\InputWidget;
use panix\engine\assets\codemirror\CodeMirrorPhpAsset;

class CodeMirrorWidget extends InputWidget
{

    public function run()
    {

        $view = $this->getView();
        CodeMirrorPhpAsset::register($view);
        if ($this->hasModel())
            return Html::activeTextarea($this->model, $this->attribute, $this->options);
        else
            return Html::textarea($this->name, $this->value, $this->options);

    }

}