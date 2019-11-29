<?php

namespace panix\engine\controllers;


use Yii;
use yii\web\HttpException;
use yii\web\Controller;


/**
 * Class CommonController
 *
 * @property string $icon
 * @property string $dataModel
 * @property string $pageName
 * @property array $breadcrumbs
 * @property array $jsMessages
 * @property boolean $dashboard
 *
 * @package panix\engine\controllers
 */
class CommonController extends Controller
{
    public $icon, $dataModel, $pageName, $breadcrumbs;
    public $jsMessages = [];
    public $dashboard = false;

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
            'ok' => Yii::t('app', 'OK'),
            'loading' => Yii::t('app', 'LOADING'),
        ];
        $languagePath = (Yii::$app->language != Yii::$app->languageManager->default->code) ? '/' . Yii::$app->language : '';
        $this->view->registerJs('
            var common = window.common || {};
            common.language = "' . Yii::$app->language . '";
            common.language_default = "' . Yii::$app->languageManager->default->code . '";
            common.language_path = "' . $languagePath . '";
            common.isDashboard = ' . boolval($this->dashboard) . ';
            common.message = ' . \yii\helpers\Json::encode($this->jsMessages) . ';', \yii\web\View::POS_HEAD, 'js-common');

        return parent::beforeAction($action);
    }

    /**
     * @param string $message
     * @throws HttpException
     */
    public function error404($message = '', $status = 404)
    {
        if (empty($message))
            $message = Yii::t('app/error', '404');
        throw new HttpException($status, $message);
    }

    /**
     * @inheritdoc
     */
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
