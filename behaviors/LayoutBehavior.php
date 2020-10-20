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

        $theme = $controller->view->theme->name;


        if (isset($controller->module)) {
            $layouts[] = "@app/web/themes/{$theme}/modules/{$controller->module->id}/views/layouts/{$controller->id}_{$controller->action->id}";
            $layouts[] = "@app/web/themes/{$theme}/modules/{$controller->module->id}/views/layouts/{$controller->id}";
            $layouts[] = "@app/web/themes/{$theme}/modules/{$controller->module->id}/views/layouts/default";
            $layouts[] = "@app/web/themes/{$theme}/views/layouts/default";

            $layouts[] = "@app/modules/{$controller->module->id}/views/layouts/{$controller->module->id}_{$controller->id}_{$controller->action->id}";
            $layouts[] = "@app/modules/{$controller->module->id}/views/layouts/{$controller->module->id}_{$controller->id}";
            $layouts[] = "@app/modules/{$controller->module->id}/views/layouts/{$controller->module->id}";
            $layouts[] = "@app/modules/{$controller->module->id}/views/layouts/main";
        }

        $layouts[] = "@app/views/layouts/main";
        //echo VarDumper::dump($layouts,10,true);die;

        foreach ($layouts as $layout) {
            $layoutPath = Yii::getAlias($layout . '.' . Yii::$app->getView()->defaultExtension);
            if (file_exists($layoutPath)) {
                $controller->layout = $layout;
                Yii::debug('Layout load ' . $layout, __METHOD__);
                break;
            }
        }

    }


    public function initialize222()
    {
        /** @var WebController $controller */
        $controller = $this->owner->context;

        //if (!($controller instanceof WebController) || !($controller instanceof AdminController)) {
        if (!$controller) {
            Yii::debug('LayoutBehavior error [1]', __METHOD__);
            return false;
        }
        if (!empty($controller->layout)) {
            Yii::debug('LayoutBehavior error [2]', __METHOD__);
            return false;
        }


        $moduleId = (isset($controller->module) && $controller->module !== null) ? $controller->module->id : null;
        $controllerId = $controller->id;
        $actionId = $controller->action->id;
        $cacheKey = __CLASS__ . "_{$moduleId}_{$controllerId}_{$actionId}";
        $theme = $controller->view->theme->name;

        if ($this->useCache && ($layout = Yii::$app->cache->get($cacheKey))) {

            $controller->layout = $layout;
            Yii::debug('Layout applied from cache:' . "\n" . $controller->layout, __METHOD__);
        } else {
            $layouts = [];
            $pathMaps = $controller->view->theme->pathMap;


            $app_id = Yii::$app->id;

            /* if ($moduleId !== null) {
                 $layouts[] = "@app/web/themes/{$theme}/modules/{$moduleId}/layouts/{$moduleId}_{$controllerId}_{$actionId}";
                 $layouts[] = "@app/web/themes/{$theme}/modules/{$moduleId}/layouts/{$moduleId}_{$controllerId}";
                 $layouts[] = "@app/web/themes/{$theme}/modules/{$moduleId}/layouts/main";//{$moduleId}


                 $layouts[] = "@app/modules/{$moduleId}/views/layouts/{$moduleId}_{$controllerId}_{$actionId}";
                 $layouts[] = "@app/modules/{$moduleId}/views/layouts/{$moduleId}_{$controllerId}";
                 $layouts[] = "@app/modules/{$moduleId}/views/layouts/{$moduleId}";
                 $layouts[] = "@app/modules/{$moduleId}/views/layouts/main";//{$moduleId}

                 foreach (Yii::$app->getModules() as $module) {
                     //$layouts[] = "@app/modules/{$moduleId}/views/layouts/{$moduleId}";
                 }

                 $layouts[] = "@app/web/themes/{$theme}/modules/{$moduleId}/views/layouts/{$controllerId}_{$actionId}";
                 $layouts[] = "@app/web/themes/{$theme}/modules/{$moduleId}/views/layouts/{$controllerId}";
                 $layouts[] = "@app/web/themes/{$theme}/modules/{$moduleId}/views/layouts/default";

                 // $layouts[] = "@webroot/themes/{$theme}/views/layouts/main";
                 $layouts[] = "@app/web/themes/{$theme}/views/layouts/default";
             } else {
                 $layouts[] = "@app/views/layouts/{$controllerId}_{$actionId}";
                 $layouts[] = "@app/views/layouts/{$controllerId}";
             }*/

            if (is_array($pathMaps) && !empty($pathMaps)) {
                foreach ($pathMaps as $path) {
                    if ($moduleId !== null) {
                        $layouts[] = "{$path}/layouts6/{$moduleId}_{$controllerId}_{$actionId}";
                        $layouts[] = "{$path}/layouts6/{$moduleId}_{$controllerId}";
                        $layouts[] = "{$path}/layouts5/main";
                    } else {
                        $layouts[] = "{$path}/layouts4/{$controllerId}_{$actionId}";
                        $layouts[] = "{$path}/layouts3/{$controllerId}";
                    }
                }
                // $layouts[] = "{$path}/layouts1/main";
            }

            $layouts[] = "@app/views/layouts/main";

            foreach ($layouts as $layout) {

                $layoutPath = Yii::getAlias($layout . '.' . Yii::$app->getView()->defaultExtension);
                if (file_exists($layoutPath)) {
                    $controller->layout = $layout;

                    //if ($this->useCache) {
                    //     Yii::$app->cache->set($cacheKey, $controller->layout, $this->cacheDuration);
                    // }
                    break;
                }
            }
            Yii::debug('Layout applied:' . "\n" . $controller->layout, __METHOD__);
        }
    }

}
