<?php
namespace panix\engine\blocks_settings;
use Yii;
class WidgetForm {
    public $model;
    public function __construct($form, $model) {
        $this->model = $model;
        $ref = new \ReflectionClass($this->model);
        Yii::setAlias('@test', dirname($ref->getFileName()).DIRECTORY_SEPARATOR);
        
        return Yii::$app->controller->render('@test/_form');
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