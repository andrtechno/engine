<?php

namespace panix\engine\bootstrap;

use yii\base\InvalidCallException;
use yii\helpers\ArrayHelper;
use panix\engine\Html;

class ActiveForm extends \yii\bootstrap4\ActiveForm
{
    public $fieldClass = 'panix\engine\bootstrap\ActiveField';
    public $layout = self::LAYOUT_HORIZONTAL;
    public $title;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->fieldConfig = ArrayHelper::merge([
            'template' => "<div class=\"col-sm-4 col-md-4 col-lg-3 col-xl-2\">{label}</div>\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-form-label',
                'offset' => 'offset-sm-4 offset-lg-3 offset-xl-4',
                'wrapper' => 'col-sm-8 col-md-8 col-lg-9 col-xl-10',
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
