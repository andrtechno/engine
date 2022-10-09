<?php

namespace panix\engine\behaviors;

use panix\mod\shop\components\FilterController;
use Yii;
use yii\base\View;
use yii\caching\Cache;
use panix\engine\controllers\WebController;
use panix\engine\controllers\AdminController;
use yii\helpers\VarDumper;

class LayoutBehavior extends \yii\base\Behavior
{

    public $useCache = false;
    public $cacheDuration = 86400;

    public function events()
    {
        return [
            View::EVENT_BEFORE_RENDER => 'initialize',
        ];
    }

    public function init()
    {
        parent::init();
        $this->useCache = $this->useCache && Yii::$app->cache instanceof Cache;
    }

    public function initialize()
    {
        /** @var WebController $controller */
        //$controller = $this->owner->context;
        $controller = Yii::$app->controller;
        //if (!($controller instanceof WebController) || !($controller instanceof AdminController) || ($controller instanceof FilterController)) {
        if (!isset($controller->view)) {
            //Yii::debug('LayoutBehavior error [1]', __METHOD__);
            return false;
        }
        if (!empty($controller->layout)) {
            //Yii::debug('LayoutBehavior error [2]', __METHOD__);
            return false;
        }
        $layouts = [];
        if (isset($controller->module)) {
            $layouts[] = "@theme/modules/{$controller->module->id}/views/layouts/{$controller->id}-{$controller->action->id}";
            $layouts[] = "@theme/modules/{$controller->module->id}/views/layouts/{$controller->id}";
            $layouts[] = "@theme/modules/{$controller->module->id}/views/layouts/default";


            $layouts[] = "@app/modules/{$controller->module->id}/views/layouts/{$controller->id}-{$controller->action->id}";
            $layouts[] = "@app/modules/{$controller->module->id}/views/layouts/{$controller->id}";
            $layouts[] = "@app/modules/{$controller->module->id}/views/layouts/default";
            $layouts[] = "@theme/views/layouts/default";
        }
        $layouts[] = "@app/views/layouts/main";

        foreach ($layouts as $layout) {
            $layoutPath = Yii::getAlias($layout . '.' . Yii::$app->getView()->defaultExtension);
            if (file_exists($layoutPath)) {
                $controller->layout = $layout;
                Yii::debug('Layout load ' . $layout, __METHOD__);
                break;
            }
        }

    }
}
