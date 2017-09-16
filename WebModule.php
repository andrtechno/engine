<?php

namespace panix\engine;

use Yii;
use yii\base\Module;
use yii\helpers\FileHelper;

class WebModule extends Module {

    public $count = false;
    // protected $info;
    public $routes = [];
    //  public static $moduleID;
    public $modelClasses = [];
    protected $_models;
    public $icon;

    // protected $moduleNamespace;
    public function init() {
        //$this->registerTranslations();
        if (method_exists($this, 'getDefaultModelClasses')) {
            $this->modelClasses = array_merge($this->getDefaultModelClasses(), $this->modelClasses);
        }
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

}
