<?php

namespace panix\engine;

use Yii;
use yii\web\Request;

class WebRequest extends Request {

    private $_pathInfo;

    public function getPathInfo() {
        $langCode =Yii::$app->language;
        $pathInfo = parent::getPathInfo();
        $parts = explode('/', $pathInfo);

        if (in_array($parts[0], Yii::$app->languageManager->getCodes())) {
            // Valid language code detected.
            // Remove it from url path to make route work and activate lang
            $langCode = $parts[0];

            // If language code is equal default - show 404 page
            if ($langCode === Yii::$app->languageManager->default->slug){
                throw new \yii\web\NotFoundHttpException(Yii::t('app/error', '404'));
            }

           // if(Yii::$app->languageManager->default->id == Yii::$app->languageManager->active->id){

           // }
            //CMS::dump($parts);die;
            unset($parts[0]);
            $pathInfo = implode( '/',$parts);
        }
        //var_dump($this->baseUrl);die;
        $this->_pathInfo = $pathInfo;

        // Activate language by code
        Yii::$app->languageManager->setActive($langCode);
        return $this->_pathInfo;
    }

}
