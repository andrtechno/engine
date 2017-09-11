<?php

namespace panix\engine\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use panix\engine\controllers\WebController;

class AdminController extends WebController {

    public $buttons = [];
    public $layout = '@vendor/panix/mod-admin/views/layouts/main';

    protected function error404($text = null) {
        if (!$text) 
            $text = Yii::t('app/error', '404');
        throw new \yii\web\NotFoundHttpException($text);
    }

    public function beforeAction($event) {
        if (Yii::$app->user->isGuest && get_class($this) !== 'panix\mod\admin\controllers\AuthController') {
            Yii::$app->response->redirect(['/admin/auth']);
        }

        return parent::beforeAction($event);
    }

    public function init() {

        if (!empty(Yii::$app->user) && !Yii::$app->user->can("admin")
                && get_class($this) !== 'panix\mod\admin\controllers\AuthController'
                && get_class($this) !== 'panix\mod\admin\controllers\DefaultController') {
            throw new ForbiddenHttpException(Yii::t('app','ACCESS_DENIED'));
        }

        Yii::setAlias('@admin', Yii::getAlias('@vendor/panix/mod-admin'));

        parent::init();
    }

    /**
     * action
     */
    public function actionCreate() {
       return $this->actionUpdate(true);
    }

}
