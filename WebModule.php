<?php

namespace panix\engine;

use Yii;
use yii\base\Module;
use yii\helpers\FileHelper;

class WebModule extends Module {
  // protected $_icon;
    public $count = false;
    // protected $info;
    public $routes = [];
    //  public static $moduleID;
    public $modelClasses = [];
    protected $_models;
    public $icon;

    public function getMdFiles() {
        $list = [];
        $files = FileHelper::findFiles(Yii::getAlias('@' . $this->id), [
                    'only' => ['*.md'],
                    'recursive' => false,
                    'caseSensitive' => false
        ]);
        foreach ($files as $file) {
            $list[basename($file, '.md')] = $file;
        }
        return $list;
    }

    // protected $moduleNamespace;
    public function init() {
        //$this->registerTranslations();
        if (method_exists($this, 'getDefaultModelClasses')) {
            $this->modelClasses = array_merge($this->getDefaultModelClasses(), $this->modelClasses);
        }
        parent::init();
    }
    
        public function afterInstall() {
       // if ($this->uploadAliasPath && !file_exists(Yii::getPathOfAlias($this->uploadAliasPath)))
       //     CFileHelper::createDirectory(Yii::getPathOfAlias($this->uploadAliasPath), 0777);
        //Yii::$app->cache->flush();
       // Yii::app()->widgets->clear();
        return true;
    }

    /**
     * Method will be called after module removed
     */
    public function afterUninstall() {
        //if ($this->uploadAliasPath && !file_exists(Yii::getPathOfAlias($this->uploadAliasPath)))
        //    CFileHelper::removeDirectory(Yii::getPathOfAlias($this->uploadAliasPath), array('traverseSymlinks' => true));

        //if (file_exists(Yii::getPathOfAlias("webroot.uploads.attachments.{$this->id}")))
        //    CFileHelper::removeDirectory(Yii::getPathOfAlias("webroot.uploads.attachments.{$this->id}"), array('traverseSymlinks' => true));

       // Yii::$app->cache->flush();


        //$moduleName = ucfirst($this->id) . '.';


        return true;
    }
    
   // public function getIcon() {
    //    return $this->_icon;
   // }

   public function setIcon($icon) {
        $this->icon = $icon;
    }

    public function getAuthor() {
        return 'dev@corner-cms.com';
    }

    public function getName() {
        return Yii::t($this->id."/default", 'MODULE_NAME');
    }

    public function getDescription() {
        return Yii::t($this->id."/default", 'MODULE_DESC');
    }
}
