<?php

namespace panix\engine\behaviors;

use Yii;
use yii\base\View;
use yii\caching\Cache;
use panix\engine\controllers\WebController;

class LayoutBehavior extends \yii\base\Behavior {

    public $useCache = false;
    public $cacheDuration = 86400;

    public function events() {
        return [
            View::EVENT_BEFORE_RENDER => 'initialize',
        ];
    }

    public function init() {
        parent::init();
        Yii::debug('Initializing LayoutBehavior', __METHOD__);
        $this->useCache = $this->useCache && Yii::$app->cache instanceof Cache;
    }

    public function initialize() {
        /** @var WebController $controller */
        $controller = $this->owner->context;
        if (!($controller instanceof WebController)) {
            return false;
        }
        if (!empty($controller->layout)) {
            return false;
        }


        $moduleId = $controller->module !== null ? $controller->module->id : null;
        $controllerId = $controller->id;
        $actionId = $controller->action->id;
        $cacheKey = __CLASS__ . "_{$moduleId}_{$controllerId}_{$actionId}";
        $theme = $controller->view->theme->name;
        if ($this->useCache && false !== ($layout = Yii::$app->cache->get($cacheKey))) {
            $controller->layout = $layout;
            Yii::debug('Layout applied from cache:' . "\n" . $controller->layout, __METHOD__);
        } else {
            $layouts = [];
            $pathMaps = $controller->view->theme->pathMap;


            if (null !== $moduleId) {
                $layouts[] = "@webroot/themes/{$theme}/modules/{$moduleId}/layouts/{$moduleId}_{$controllerId}_{$actionId}";
                $layouts[] = "@webroot/themes/{$theme}/modules/{$moduleId}/layouts/{$moduleId}_{$controllerId}";
                $layouts[] = "@webroot/themes/{$theme}/modules/{$moduleId}/layouts/main";//{$moduleId}


                $layouts[] = "@app/modules/{$moduleId}/views/layouts/{$moduleId}_{$controllerId}_{$actionId}";
                $layouts[] = "@app/modules/{$moduleId}/views/layouts/{$moduleId}_{$controllerId}";
                $layouts[] = "@app/modules/{$moduleId}/views/layouts/{$moduleId}";
                $layouts[] = "@app/modules/{$moduleId}/views/layouts/main";//{$moduleId}



                $layouts[] = "@app/web/themes/{$theme}/modules/{$moduleId}/views/layouts/{$controllerId}_{$actionId}";
                $layouts[] = "@app/web/themes/{$theme}/modules/{$moduleId}/views/layouts/{$controllerId}";
                $layouts[] = "@app/web/themes/{$theme}/modules/{$moduleId}/views/layouts/default";

               // $layouts[] = "@webroot/themes/{$theme}/views/layouts/main";
                $layouts[] = "@webroot/themes/{$theme}/views/layouts/default";

                

            } else {

                $layouts[] = "@app/views/layouts/{$controllerId}_{$actionId}";
                $layouts[] = "@app/views/layouts/{$controllerId}";
            }

            if (is_array($pathMaps) && !empty($pathMaps)) {
               // VarDumper::dump($pathMaps,10,true);die;
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
                $layouts[] = "{$path}/layouts1/main";
            }

            $layouts[] = "@app/views/layouts2/main";

         //   VarDumper::dump($layouts,10,true);die;
            //foreach (Yii::$app->getModules() as $module){
            //    print_r($module);
            //}
            //die;
            foreach ($layouts as $layout) {
 
                $layoutPath = Yii::getAlias($layout . '.' . Yii::$app->getView()->defaultExtension);
                if (file_exists($layoutPath)) {
                    $controller->layout = $layout;

                    if ($this->useCache) {
                        Yii::$app->cache->set($cacheKey, $controller->layout, $this->cacheDuration);
                    }
                                   //print_r($layout);
                    break;
                }
            }
            Yii::debug('Layout applied:' . "\n" . $controller->layout, __METHOD__);
        }
    }

}
