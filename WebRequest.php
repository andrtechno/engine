<?php

namespace panix\engine;

use Yii;
use yii\web\Request;
use yii\web\NotFoundHttpException;

class WebRequest extends Request
{

    private $_pathInfo;

    public function getPathInfo()
    {
        $langCode = Yii::$app->language;
        $pathInfo = parent::getPathInfo();
        $parts = explode('/', $pathInfo);
        $langCode = ($parts[0] == 'ua') ? 'uk' : $parts[0];
        if (in_array($langCode, Yii::$app->languageManager->getCodes())) {
            // Valid language code detected.
            // Remove it from url path to make route work and activate lang
            //$langCode = $parts[0];

            // If language code is equal default - show 404 page
            if ($langCode === Yii::$app->languageManager->default->slug) {
                throw new NotFoundHttpException(Yii::t('app/error', '404'));
            }

            // if(Yii::$app->languageManager->default->id == Yii::$app->languageManager->active->id){

            // }
            //CMS::dump($parts);die;
            unset($parts[0]);
            $pathInfo = implode('/', $parts);
        }
        //var_dump($this->baseUrl);die;
        $this->_pathInfo = $pathInfo;


        // Activate language by code
        Yii::$app->languageManager->setActive($langCode);
        return $this->_pathInfo;
    }

    /**
     * @inheritDoc
     */
    public function resolve()
    {
        $result = Yii::$app->getUrlManager()->parseRequest($this);
        if ($result !== false) {
            list($route, $params) = $result;
            if ($this->queryParams === null) {
                $_GET = $params + $_GET; // preserve numeric keys
            } else {
                $this->queryParams = $params + $this->queryParams;
            }

            return [$route, $this->getQueryParams()];
        }

        throw new NotFoundHttpException(Yii::t('app/error', 404));
    }
}
