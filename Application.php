<?php

namespace panix\engine;

class Application extends \yii\web\Application {

    const version = '0.1a';

    public function run() {
        $this->name = $this->settings->get('app', 'sitename');
        parent::run();
    }

    public function getModulesInfo() {
        $modules = $this->getModules();
        if (YII_DEBUG)
            unset($modules['debug'], $modules['gii'], $modules['admin']);
        $result = array();
        foreach ($modules as $name => $className) {
            //$info = $this->getModule($name)->info;
            if (isset($this->getModule($name)->info))
                $result[$name] = $this->getModule($name)->info;
        }

        return $result;
    }

    public static function powered() {
        return \Yii::t('app', 'COPYRIGHT', [
                    'year' => date('Y')
        ]);
    }

    public function getVersion() {
        return self::version;
    }

    // public function init() {
    //     $this->setEngineModules();
    //     parent::init();
    // }
}

?>
