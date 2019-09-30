<?php

namespace panix\engine;

use Yii;
use yii\base\Component;
use yii\base\BootstrapInterface;

class BootstrapModule extends Component implements BootstrapInterface
{
    public function bootstrap($app)
    {
        foreach (Yii::$app->getModules() as $mod => $params) {
            $module = Yii::$app->getModule($mod);
            if ($module->hasMethod('bootstrap')) {
                $module->bootstrap(Yii::$app);
            }
        }
    }
}