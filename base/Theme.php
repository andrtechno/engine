<?php

namespace panix\engine\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class Theme extends \yii\base\Theme
{

    public $name;

    public function init()
    {
        if($this->name==null){
            $this->name = Yii::$app->settings->get('app', 'theme');
        }

        if(preg_match("/admin/", Yii::$app->request->getUrl())){
            $this->name = 'dashboard';
        }

        $this->basePath = "@app/web/themes/{$this->name}";
        $this->baseUrl = "@app/web/themes/{$this->name}";


        $modulesPaths = [];
        foreach (Yii::$app->getModules() as $id => $mod) {
           $modulesPaths['@' . $id] = "@app/web/themes/{$this->name}/modules/{$id}";
          //  $modulesPaths['@app/modules/' . $id] = "@frontend/themes/{$this->name}/modules/{$id}";
        }

        $this->pathMap = ArrayHelper::merge([
            "@app/views" => "@app/web/themes/{$this->name}/views",
            '@app/modules' => "@app/web/themes/{$this->name}/modules",
            '@app/widgets' => "@app/web/themes/{$this->name}/widgets",
        ], $modulesPaths);



//echo Yii::getAlias("@frontend/themes/{$this->name}/modules");

        parent::init();
    }

    public function alert($type, $text, $close = true)
    {
        $id = 'alert' . md5($type . Inflector::slug($text));
        if (!isset($_COOKIE[$id])) {
            $types = array('info', 'warning', 'success', 'failure', 'danger', 'error');
            $str = implode(', ', $types);
            if (in_array($type, $types)) {
                $this->render('_' . __FUNCTION__, ['type' => $type, 'message' => $text, 'close' => $close]);
            } else {
                die('error alert theme');
            }
        }
    }

    /*
    public function beginBlock(){
        $this->render('_beginBlock', []);
    }
    public function beginEnd(){
        $this->render('_endBlock', []);
    }
*/
    private function render($tpl, $array = array())
    {
        $render = null;
        $module = Yii::$app->controller->module->id;
        $controller = Yii::$app->controller->id;
        if ($module == 'install') {
            $theme = 'default';
        } else {
            $theme = $this->name;
        }
        if (Yii::$app->id == 'backend') {
            $layouts = array(
                "@vendor/panix/mod-{$module}/views/layouts/{$tpl}_{$controller}",
                "@vendor/panix/mod-{$module}/views/layouts/" . $tpl,
                "@web/views/layouts/" . $tpl,

            );
        } else {
            $layouts = array(
                "@vendor/panix/mod-{$module}/views/layouts/" . $tpl,
                "@frontend/web/themes/{$theme}/views/{$module}/layouts/" . $tpl,
                "@frontend/web/themes/{$theme}/views/layouts/" . $tpl,
            );
        }
        foreach ($layouts as $layout) {

            if (file_exists(Yii::getAlias($layout) . '.php')) {
                $render = $layout;
                break;
            }
        }
        return Yii::$app->view->render($render, $array, false, false);
    }

}
