<?php

namespace panix\engine\widgets\webcontrol;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\View;
use yii\base\Object;
use panix\engine\widgets\webcontrol\WebInlineAsset;

class WebInlineControl extends Object {

    public function init() {
        if (Yii::$app->user->can('admin') && !Yii::$app->request->isAjax) {
            $view = Yii::$app->view;
            WebInlineAsset::register($view);
            $view->on(View::EVENT_BEGIN_BODY, [$this, '_run']);
        }
    }

    public function _run() {

        echo Yii::$app->view->render('@vendor/panix/engine/widgets/webcontrol/views/run');
    }

}
