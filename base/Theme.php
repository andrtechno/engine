<?php

namespace panix\engine\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class Theme extends \yii\base\Theme {

    public $name;

    public function init() {
        $this->name = \Yii::$app->settings->get('app', 'theme');
        $this->basePath = "@app/web/themes/{$this->name}";
        $modulesPaths = [];
        foreach (Yii::$app->getModules() as $id => $mod) {
            $modulesPaths['@' . $id] = "@app/web/themes/{$this->name}/modules/{$id}";
        }

        $this->pathMap = ArrayHelper::merge([
                    "@app/views" => "@app/web/themes/{$this->name}/views",
                        ], $modulesPaths);

        $this->baseUrl = "@app/web/themes/{$this->name}";
        parent::init();
    }

    public function alert($type, $text, $close = true) {
        $id = 'alert' . md5($type . Inflector::slug($text));
        if (!isset($_COOKIE[$id])) {
            $types = array('info', 'warning', 'success', 'failure', 'danger', 'error');
            $str = implode(', ', $types);
            if (in_array($type, $types)) {
                $this->render('_' . __FUNCTION__, ['type' => $type, 'message' => $text, 'close' => $close]);
            } else {
                die('erro alert theme');
                Yii::$app->controller->flashMessage('warning', Yii::t('app', 'TPL_' . strtoupper(__FUNCTION__), array(
                            '{tpl}' => __FUNCTION__,
                            '{type}' => $type,
                                //'{types}' => Html::encode($str)
                                )
                ));
            }
        }
    }
    private function render($tpl, $array = array()) {
        $render = null;
        $module = Yii::$app->controller->module->id;
        $controller = Yii::$app->controller->id;
        if ($module == 'install') {
            $theme = 'default';
        } else {
            $theme = $this->name;
        }
        if (Yii::$app->controller instanceof \panix\engine\controllers\AdminController) {
            $layouts = array(
                "@vendor/panix/mod-{$module}/views/layouts/{$tpl}_{$controller}",
                "@vendor/panix/mod-{$module}/views/layouts/" . $tpl,
                "@vendor/panix/mod-admin/views/layouts/" . $tpl,
            );
        } else {
            $layouts = array(
                "@vendor/panix/mod-{$module}/views/layouts/" . $tpl,
                "@web/themes/{$theme}/views/{$module}/layouts/" . $tpl,
                "@web/themes/{$theme}/views/layouts/" . $tpl,
            );
        }
        foreach ($layouts as $layout) {
          
            if (file_exists(Yii::getAlias($layout) . '.php')) {
                $render = $layout;
                break;
            }
        }
        echo Yii::$app->view->render($render, $array, false, false);
    }
}
