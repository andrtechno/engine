<?php

namespace panix\engine\controllers;

use Yii;
use yii\web\Controller;

class WebController extends Controller {

    public $pageName;
    public $breadcrumbs = [];
    public $jsMessages = [];
    public $dataModel;
    public function actionError() {
        $exception = Yii::$app->errorHandler->exception;

        if ($exception !== null) {
            $statusCode = $exception->statusCode;
            $name = $exception->getName();
            $message = $exception->getMessage();

           // $this->layout = 'error';

            return $this->render('error', [
                        'exception' => $exception,
                        'statusCode' => $statusCode,
                        'name' => $name,
                        'message' => $message
            ]);
        }
    }

    public function beforeAction($action) {





        $this->view->registerJs('
            common.langauge="' . Yii::$app->language . '";
            common.token="' . Yii::$app->request->csrfToken . '";
            common.isDashboard=true;
            common.message=' . \yii\helpers\Json::encode($this->jsMessages), \yii\web\View::POS_END, 'js-common');



        $this->view->registerMetaTag(['name' => 'author', 'content' => Yii::$app->name]);
        $this->view->registerMetaTag(['name' => 'generator', 'content' => Yii::$app->name . ' ' . Yii::$app->version]);

        return parent::beforeAction($action);
    }

    public function init() {
        $user = Yii::$app->user;
        $langManager = Yii::$app->languageManager;

        if (!$user->isGuest && $user->language) {
            if ($user->getLanguage() != $langManager->default->code) {
                $getLang = $langManager->getById($user->getLanguage())->code;
                Yii::app()->language = $getLang;
                $strpos = strpos(Yii::app()->request->requestUri, '/' . $getLang);
                if ($strpos === false) {
                    if ($langManager->default->code != $getLang) {
                        if ($this->isAdminController)
                            $this->redirect("/{$getLang}/admin");
                        else
                            $this->redirect('/' . $getLang);
                    }
                }
            } else {
                Yii::$app->language = $langManager->active->code;
            }
        } else {
            Yii::$app->language = $langManager->active->code;
        }
        //  Yii::$app->language =Yii::$app->languageManager->active->code;
        $timeZone = Yii::$app->settings->get('app', 'timezone');
        Yii::$app->timeZone = $timeZone;

        $this->jsMessages = array(
            'error' => array(
                '404' => Yii::t('app/error', '404')
            ),
            'cancel' => Yii::t('app', 'CANCEL'),
            'send' => Yii::t('app', 'SEND'),
            'delete' => Yii::t('app', 'DELETE'),
            'save' => Yii::t('app', 'SAVE'),
            'close' => Yii::t('app', 'CLOSE'),
            //  'ok' => Yii::t('app', 'OK'),
            'loading' => Yii::t('app', 'LOADING'),
        );

        parent::init();
    }

    /*
      public function renderContent($content) {
      $copyright = '<a href="//corner-cms.com/" id="corner" target="_blank"><span>' . Yii::t('app', 'CORNER') . '</span> &mdash; <span class="cr-logo">CORNER</span></a>';
      $layoutFile = $this->findLayoutFile($this->getView());

      if ($layoutFile !== false) {
      return $this->getView()->renderFile($layoutFile, ['content' => $content, 'copyright' => $copyright], $this);
      }
      return $content;
      } */
}
