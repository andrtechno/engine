<?php

namespace panix\engine\controllers;

use Yii;
use yii\web\Controller;

class WebController extends Controller {

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
    }*/

}
