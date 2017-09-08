<?php

namespace panix\engine;

use Yii;
use yii\base\Module;
use yii\helpers\FileHelper;

class WebModule extends Module {

    // protected $info;
    public $routes = [];
    //  public static $moduleID;
    public $modelClasses = [];
    protected $_models;

    // protected $moduleNamespace;
    public function init() {
        $this->setAliases([
            '@' . $this->id => realpath(Yii::getAlias("@vendor/panix/mod-{$this->id}")),
        ]);
        $this->registerTranslations();
        if (method_exists($this, 'getDefaultModelClasses')) {
            $this->modelClasses = array_merge($this->getDefaultModelClasses(), $this->modelClasses);
        }


        
        // TODO: Пересмотреть.
        if (file_exists(Yii::getAlias('@app/web/themes/' . Yii::$app->view->theme->name . '/modules/' . $this->id).'.php')) {
            $this->viewPath = '@app/web/themes/' . Yii::$app->view->theme->name . '/modules/' . $this->id;
        }
        // echo $this->localePath;
        //  self::$moduleID = $this->id;
        parent::init();
    }

    public function model($name, $config = []) {
        // return object if already created
        if (!empty($this->_models[$name])) {
            return $this->_models[$name];
        }
        // create model and return it
        $className = $this->modelClasses[ucfirst($name)];
        $this->_models[$name] = Yii::createObject(array_merge(["class" => $className], $config));
        return $this->_models[$name];
    }

    /**
     * Функция для translations fileMap "@app/system/modules/{$this->id}/messages"
     */
    public function getTranslationsFileMap() {
        $lang = Yii::$app->language;
        $result = array();
        //$basepath = realpath(Yii::getAlias("@app/system/modules/{$this->id}/messages/{$lang}"));
        // $basepath = realpath(Yii::getAlias("@vendor/panix/mod-{$this->id}/messages/{$lang}"));
        $basepath = realpath(Yii::getAlias("@{$this->id}/messages/{$lang}"));
        if (is_dir($basepath)) {
            $fileList = FileHelper::findFiles($basepath, [
                        'only' => ['*.php'],
                        'recursive' => FALSE
            ]);

            foreach ($fileList as $path) {
                $result[$this->id . '/' . basename($path, '.php')] = basename($path);
            }
        } else {
            $result = [];
        }

        return $result;
    }

    public function registerTranslations() {

        Yii::$app->i18n->translations[$this->id . '/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            //'basePath' => '@app/system/modules/' . $this->id . '/messages',
            // 'basePath' => '@vendor/panix/mod-' . $this->id . '/messages',
            'basePath' => '@' . $this->id . '/messages',
            'fileMap' => $this->getTranslationsFileMap()
        ];
    }

}
