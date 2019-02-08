<?php

namespace panix\engine\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii2mod\rbac\filters\AccessControl;
class AdminController extends WebController
{

    public $buttons = [];
    public $layout = '@vendor/panix/mod-admin/views/layouts/main';
    public $dashboard = true;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'allowActions' => [
                    'index',
                    // The actions listed here will be allowed to everyone including guests.
                ]
            ],
        ];
    }

    public function beforeAction($event)
    {
        if (Yii::$app->user->isGuest && get_class($this) !== 'panix\mod\admin\controllers\AuthController') {
            Yii::$app->response->redirect(['/admin/auth']);
        }

        return parent::beforeAction($event);
    }

    public function init()
    {
        // Yii::$app->assetManager->bundles['yii\jui\JuiAsset']['css'] = [];
        if (!empty(Yii::$app->user) && !Yii::$app->user->can("admin") && get_class($this) !== 'panix\mod\admin\controllers\AuthController' && get_class($this) !== 'panix\mod\admin\controllers\DefaultController') {
            throw new ForbiddenHttpException(Yii::t('app', 'ACCESS_DENIED'));
        }

        //Yii::setAlias('@admin', Yii::getAlias('@vendor/panix/mod-admin'));

        parent::init();
    }


    public function actionCreate()
    {
        return $this->actionUpdate(true);
    }

}
