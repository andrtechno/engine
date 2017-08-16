<?php

namespace panix\engine;

use Yii;
use yii\web\Request;
use app\models\Lang;
use yii\helpers\Url;

class WebRequest extends Request {

    private $_lang_url;

    public function getLangUrl() {
        if ($this->_lang_url === null) {
            $this->_lang_url = $this->getUrl();



            $url_list = explode('/', $this->_lang_url);

            // Удаляем все пустые елементы
            // Теперь работает - в конце сторик / и без слэша.
            // foreach ($url_list as $k => $list){
            //var_dump($list);
            //  if (strlen($list) < 1){
            // echo $url_list[$k];
            //    unset($url_list[$k]);
            //  }
            // }

            $lang_url = isset($url_list[1]) ? $url_list[1] : null;

            Lang::setCurrent($lang_url);
            $uri = Lang::getCurrent()->url;

            // if ($lang_url !== null && $lang_url === $uri &&  strpos($this->_lang_url, $uri) === 1) {
            //     $this->_lang_url = substr($this->_lang_url, strlen($uri) + 1);
            //  }
        }

        return $this->_lang_url;
    }

    protected function resolvePathInfo() {
        $pathInfo = $this->getLangUrl();

        if (($pos = strpos($pathInfo, '?')) !== false) {
            $pathInfo = substr($pathInfo, 0, $pos);
        }

        $pathInfo = urldecode($pathInfo);

        // try to encode in UTF8 if not so
        // http://w3.org/International/questions/qa-forms-utf-8.html
        if (!preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E]              # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
            )*$%xs', $pathInfo)
        ) {
            $pathInfo = utf8_encode($pathInfo);
        }

        $scriptUrl = $this->getScriptUrl();
        $baseUrl = $this->getBaseUrl();
        if (strpos($pathInfo, $scriptUrl) === 0) {
            $pathInfo = substr($pathInfo, strlen($scriptUrl));
        } elseif ($baseUrl === '' || strpos($pathInfo, $baseUrl) === 0) {
            $pathInfo = substr($pathInfo, strlen($baseUrl));
        } elseif (isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0) {
            $pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
        } else {
            throw new InvalidConfigException('Unable to determine the path info of the current request.');
        }

        if ($pathInfo[0] === '/') {
            $pathInfo = substr($pathInfo, 1);
        }

        return (string) $pathInfo;
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
