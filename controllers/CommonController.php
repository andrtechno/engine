<?php

namespace panix\engine\controllers;


use Yii;
use yii\web\HttpException;
use yii\web\Controller;
use yii\widgets\ActiveForm;

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
        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            $this->jsMessages = [
                'error' => [
                    '404' => Yii::t('app/error', '404')
                ],
                'cancel' => Yii::t('app/default', 'CANCEL'),
                'send' => Yii::t('app/default', 'SEND'),
                'delete' => Yii::t('app/default', 'DELETE'),
                'save' => Yii::t('app/default', 'SAVE'),
                'close' => Yii::t('app/default', 'CLOSE'),
                'ok' => Yii::t('app/default', 'OK'),
                'loading' => Yii::t('app/default', 'LOADING'),
            ];
            $languagePath = (Yii::$app->language != Yii::$app->languageManager->default->code) ? '/' . Yii::$app->language : '';
            $this->view->registerJs('
            var common = window.common || {};
            common.language = "' . Yii::$app->language . '";
            common.language_default = "' . Yii::$app->languageManager->default->code . '";
            common.language_path = "' . $languagePath . '";
            common.isDashboard = "' . $this->dashboard . '";
            common.message = ' . \yii\helpers\Json::encode($this->jsMessages) . ';', \yii\web\View::POS_HEAD, 'js-common');
        }
        return parent::beforeAction($action);
    }

    /**
     * @param string $message
     * @param string|integer $status
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
            return parent::renderAjax($view, $params);
        } else {
            return parent::render($view, $params);
        }
    }
    protected function performAjaxValidation($model)
    {
        if (Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())) {
            //Yii::$app->response->format = Response::FORMAT_JSON;
            //echo json_encode(ActiveForm::validate($model));
            return $this->asJson(ActiveForm::validate($model));
            //Yii::$app->end();
        }
    }
}
