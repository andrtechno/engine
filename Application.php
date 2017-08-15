<?php

namespace panix\engine;

class Application extends \yii\web\Application {

    public function getModulesInfo() {
        $modules = $this->getModules();
          if (YII_DEBUG)
              unset($modules['debug'], $modules['gii'], $modules['admin']);
        $result = array();
        foreach ($modules as $name => $className){
            //$info = $this->getModule($name)->info;
            if(isset($this->getModule($name)->info))
                $result[$name]=$this->getModule($name)->info;
        }

        return $result;
    }

   // public function init() {
   //     $this->setEngineModules();
   //     parent::init();
   // }

}

?>
