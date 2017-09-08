<?php

namespace panix\engine;

use Yii;
use yii\web\Request;
use yii\helpers\Url;

class WebRequest extends Request {

    private $_pathInfo;

    public function getPathInfo() {
        $langCode = null;
        $pathInfo = parent::getPathInfo();

       // if (null === $this->_pathInfo) {


            $parts = explode('/', $pathInfo);

            if (in_array($parts[0], Yii::$app->languageManager->getCodes())) {
                // Valid language code detected.
                // Remove it from url path to make route work and activate lang
                $langCode = $parts[0];


                // If language code is equal default - show 404 page
                if ($langCode === Yii::$app->languageManager->default->code)
                    throw new \yii\web\NotFoundHttpException(Yii::t('app/error', '404'));

                unset($parts[0]);
                $pathInfo = implode($parts, '/');
            }

            $this->_pathInfo = $pathInfo;



            // Activate language by code
            Yii::$app->languageManager->setActive($langCode);
       // }

        return $pathInfo;
    }


}
