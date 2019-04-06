<?php

namespace panix\engine\bootstrap;

use yii\base\InvalidCallException;
use yii\helpers\ArrayHelper;
use panix\engine\Html;

class ActiveForm extends \yii\bootstrap4\ActiveForm
{
    public $fieldClass = 'panix\engine\bootstrap\ActiveField';
    public $layout = 'horizontal';
    public $title;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->fieldConfig = ArrayHelper::merge([
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-4 col-lg-2 col-form-label',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-8 col-lg-10',
                'error' => '',
                'hint' => '',
            ],
        ], $this->fieldConfig);
        parent::init();
    }



    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!empty($this->_fields)) {
            throw new InvalidCallException('Each beginField() should have a matching endField() call.');
        }

        $content = ob_get_clean();
        /*$html = '<div class="card bg-light">
    <div class="card-header"><h5>'.$this->title.'</h5></div>
    <div class="card-body">';*/
        $html = Html::beginForm($this->action, $this->method, $this->options);
        $html .= $content;

        if ($this->enableClientScript) {
            $this->registerClientScript();
        }

        $html .= Html::endForm();
        //$html .= '</div></div>';
        return $html;
    }
}
