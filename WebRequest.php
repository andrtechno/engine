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

    /**
     * Add param to current url. Url is based on $data and $_GET arrays
     *
     * @param $route
     * @param $data array of the data to add to the url.
     * @param $selectMany
     * @return string
     */
    public function addUrlParam($route, $data, $selectMany = false) {
        foreach ($data as $key => $val) {
            if (isset($_GET[$key]) && $key !== 'url' && $selectMany === true) {
                $tempData = explode(',', $_GET[$key]);
                $data[$key] = implode(',', array_unique(array_merge((array) $data[$key], $tempData)));
            }
        }

        return Yii::$app->urlManager->createUrl(array_merge([$route], array_merge($_GET, $data)));
    }

    /**
     * Delete param/value from current
     *
     * @param string $route
     * @param string $key to remove from query
     * @param null $value If not value - delete whole key
     * @return string new url
     */
    public function removeUrlParam($route, $key, $value = null) {
        $get = $_GET;
        if (isset($get[$key])) {
            if ($value === null)
                unset($get[$key]);
            else {
                $get[$key] = explode(',', $get[$key]);
                $pos = array_search($value, $get[$key]);
                // Delete value
                if (isset($get[$key][$pos]))
                    unset($get[$key][$pos]);
                // Save changes
                if (!empty($get[$key]))
                    $get[$key] = implode(',', $get[$key]);
                // Delete key if empty
                else
                    unset($get[$key]);
            }
        }
        return Yii::$app->urlManager->createUrl(array_merge([$route], $get));
    }

}
