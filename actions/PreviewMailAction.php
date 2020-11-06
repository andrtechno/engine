<?php

namespace panix\engine\actions;

use Yii;
use yii\base\Action;
use yii\web\HttpException;

class PreviewMailAction extends Action
{
    public $layout = '@app/mail/layouts/empty';
    public $view;
    public $data = [];

    public function run()
    {
        $this->controller->layout = $this->layout;
        if(Yii::$app->request->get('view')){
            $this->view = Yii::$app->request->get('view');
        }
        if(Yii::$app->request->get('layout')){
            $this->controller->layout = Yii::$app->request->get('layout');
        }
        if (file_exists(Yii::getAlias($this->view))) {
            return $this->controller->render($this->view, $this->data);
        }
        throw new HttpException(500);
    }

}
