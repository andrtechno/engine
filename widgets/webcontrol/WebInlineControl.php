<?php

namespace panix\engine\widgets\webcontrol;

use Yii;
use yii\web\View;
use yii\base\Object;
use panix\engine\widgets\webcontrol\WebInlineAsset;

class WebInlineControl extends Object {

    public function init() {
        if (Yii::$app->user->can('admin') && !Yii::$app->request->isAjax && $this->checkAdminRequest()) {
            $view = Yii::$app->view;
            WebInlineAsset::register($view);
            $view->on(View::EVENT_BEGIN_BODY, [$this, 'runnig']);
        }
    }

    public function runnig() {
        echo Yii::$app->view->render('@vendor/panix/engine/widgets/webcontrol/views/run');
    }

    private function checkAdminRequest() {
        $path = Yii::$app->request->getPathInfo();
        if (empty($path))
            return true;

        if (strpos($path, 'admin') !== false) {
            return false;
        }
        return true;
    }

}
