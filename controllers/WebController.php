<?php

namespace panix\engine\controllers;

use Yii;
use yii\web\Controller;

class WebController extends Controller {

    public $pageName;
    public $breadcrumbs = [];
    public function beforeAction($event) {
        $this->view->registerMetaTag(['name' => 'author', 'content' => Yii::$app->name]);
        $this->view->registerMetaTag(['name' => 'generator', 'content' => Yii::$app->name.' '.Yii::$app->version]);
        return parent::beforeAction($event);
    }
    public function init() {

        //  Yii::$app->language =Yii::$app->languageManager->active->code;

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
