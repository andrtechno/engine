<?php

namespace panix\engine;

use Yii;
use yii\web\Application;

/**
 * Class ApiApplication
 * @package panix\engine
 */
class ApiApplication extends Application
{

    public function run()
    {
        $langManager = $this->languageManager;
        $this->language = (isset($langManager->default->code)) ? $langManager->default->code : $this->language;
        return parent::run();
    }

}
