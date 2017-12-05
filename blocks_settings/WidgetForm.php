<?php
namespace panix\engine\blocks_settings;
use Yii;
class WidgetForm {
    public $model;
    public function __construct($form, $model) {
        $this->model = $model;
        Yii::$app->controller->render('das');
    }
    public function render() {

        /*$form = $this->renderBegin();
        $form .= $this->renderBody();
        $form .= $this->renderEnd();
        return $form;*/
        
        print_r($this->model);die;
return false;
        
    }

}