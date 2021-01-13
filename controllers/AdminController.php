<?php

namespace panix\engine\controllers;


use panix\engine\CMS;
use Yii;
use panix\mod\rbac\filters\AccessControl;
use yii\web\HttpException;


/**
 * Class AdminController
 * @package panix\engine\controllers
 */
class AdminController extends CommonController
{


    public $buttons = [];
    public $layout = '@theme/views/layouts/main';
    public $dashboard = true;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'allowActions' => [
                    // 'index',
                    // The actions listed here will be allowed to everyone including guests.
                ]
            ],
        ];
    }

    /**
     * @param boolean $isNewRecord
     * @param array $post
     * @param array $action
     * @return \yii\web\Response
     */
    public function redirectPage($isNewRecord, $post, $action = ['index'])
    {

        Yii::$app->session->setFlash('success', Yii::t('app/default', ($isNewRecord) ? 'SUCCESS_CREATE' : 'SUCCESS_UPDATE'));
        $redirect = (isset($post['redirect'])) ? $post['redirect'] : Yii::$app->request->url;

        if ($isNewRecord) {
            return $this->redirect($action);
        }
        if (!Yii::$app->request->isAjax)
            return Yii::$app->response->redirect($redirect);
    }

    public function getAssetUrl()
    {
        $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@theme/assets"));
        return $assetsPaths[1];
    }

    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest && get_class($this) !== 'panix\mod\admin\controllers\AuthController') {
            return Yii::$app->response->redirect(['/admin/auth']);
        }
        if (!Yii::$app->user->isGuest && !Yii::$app->user->can('admin') && !Yii::$app->user->can('/admin/*')) {
            return Yii::$app->response->redirect(['/site/index']);
        }
        return parent::beforeAction($action);
    }


    /**
     * @inheritdoc
     */
    public function init()
    {

        // echo get_class($this);die;

        //panix\mod\admin\controllers\admin\DefaultController

        /*if (!empty(Yii::$app->user)
            && !Yii::$app->user->can("admin")
            && get_class($this) !== 'panix\mod\admin\controllers\AuthController'
            && get_class($this) !== 'panix\mod\admin\controllers\DefaultController'
        ) {
            throw new ForbiddenHttpException(Yii::t('app/default', 'ACCESS_DENIED'));
        }*/

        Yii::setAlias('@theme', Yii::getAlias("@app/web/themes/dashboard"));
        Yii::setAlias('@web_theme', Yii::getAlias("@app/web/themes/" . Yii::$app->settings->get('app', 'theme')));


        parent::init();
    }

}
