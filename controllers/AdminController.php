<?php

namespace panix\engine\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use panix\engine\controllers\WebController;

class AdminController extends WebController {

    public $buttons = [];
    public $layout = '@app/web/themes/admin/views/layouts/main';



    public function beforeAction($event) {

        // Allow only authorized users access
        if (Yii::$app->user->isGuest && get_class($this) !== 'panix\mod\admin\controllers\AuthController') {
            //  Yii::$app->request->redirect($this->createUrl('/admin/auth'));
            Yii::$app->response->redirect(array('/admin/auth'));
        }
        //Yii::$app->errorHandler->errorAction = '/admin/errors/error';

        return parent::beforeAction($event);
    }

    public function init() {

        //if (!empty(Yii::$app->user) && !Yii::$app->user->can("admin")) {
        //    throw new ForbiddenHttpException(Yii::t('app','ACCESS_DENIED'));
        //}
        parent::init();
    }

    /**
     * action
     */
    public function actionCreate() {
        $this->actionUpdate(true);
    }


}
