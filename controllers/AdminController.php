<?php

namespace panix\engine\controllers;


use Yii;
use yii\web\HttpException;
use yii\web\Controller;
use panix\mod\rbac\filters\AccessControl;


/**
 * Class AdminController
 *
 * @property string $icon
 *
 * @package panix\engine\controllers
 */
class AdminController extends Controller
{


    public $icon, $breadcrumbs, $dataModel, $pageName;
    public $jsMessages = [];
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
     * @return \yii\web\Response
     */
    public function redirectPage($isNewRecord, $post)
    {
        Yii::$app->session->setFlash('success', Yii::t('app', ($isNewRecord) ? 'SUCCESS_CREATE' : 'SUCCESS_UPDATE'));
        $redirect = (isset($post['redirect'])) ? $post['redirect'] : Yii::$app->request->url;
        if (!Yii::$app->request->isAjax)
            return $this->redirect($redirect);
    }

    public function getAssetUrl()
    {
        $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@theme/assets"));
        return $assetsPaths[1];
    }


    public function beforeAction($action)
    {
        $this->jsMessages = [
            'error' => [
                '404' => Yii::t('app/error', '404')
            ],
            'cancel' => Yii::t('app', 'CANCEL'),
            'send' => Yii::t('app', 'SEND'),
            'delete' => Yii::t('app', 'DELETE'),
            'save' => Yii::t('app', 'SAVE'),
            'close' => Yii::t('app', 'CLOSE'),
            //  'ok' => Yii::t('app', 'OK'),
            'loading' => Yii::t('app', 'LOADING'),
        ];
        $languagePath = (Yii::$app->language != Yii::$app->languageManager->default->code) ? '/' . Yii::$app->language : '';
        $this->view->registerJs('
            var common = window.CMS_common || {};
            common.language="' . Yii::$app->language . '";
            common.language_default="' . Yii::$app->languageManager->default->code . '";
            common.language_path="' . $languagePath . '";
            common.token="' . Yii::$app->request->csrfToken . '";
            common.isDashboard=true;
            common.message=' . \yii\helpers\Json::encode($this->jsMessages) . ';', \yii\web\View::POS_HEAD, 'js-common');

        if (Yii::$app->user->isGuest && get_class($this) !== 'panix\mod\admin\controllers\AuthController') {
            return Yii::$app->response->redirect(['/admin/auth']);
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
            throw new ForbiddenHttpException(Yii::t('app', 'ACCESS_DENIED'));
        }*/

        Yii::setAlias('@theme', Yii::getAlias("@app/web/themes/dashboard"));
        Yii::setAlias('@web_theme', Yii::getAlias("@app/web/themes/" . Yii::$app->settings->get('app', 'theme')));


        parent::init();
    }


    public function actionCreate()
    {
        return $this->actionUpdate(false);
    }


    public function error404($message = '', $status = 404)
    {
        if (empty($message))
            $message = Yii::t('app/error', '404');
        throw new HttpException($status, $message);
    }

    public function render($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($view, $params);
        } else {
            return parent::render($view, $params);
        }

    }

    public function renderAjax($view, $params = [])
    {
        return $this->getView()->renderAjax($view, $params, $this);
    }
}
