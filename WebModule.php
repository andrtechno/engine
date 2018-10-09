<?php

namespace panix\engine;

use Yii;
use yii\base\Module;
use yii\helpers\FileHelper;
use yii2mod\rbac\filters\AccessControl;
class WebModule extends Module
{

    public $assetsUrl;
    // protected $_icon;
    public $count = false;
    // protected $info;
    public $routes = [];
    //  public static $moduleID;
    public $modelClasses = [];
    protected $_models;
    public $icon;
    public $uploadPath;
    public $uploadAliasPath = null;
    public function behaviors2()
    {
        return [
            AccessControl::class
        ];
    }
    public function getMdFiles()
    {
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
    public function init()
    {
        if (file_exists(Yii::getAlias("@{$this->id}/assets"))) {
            $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@{$this->id}/assets"));
            $this->assetsUrl = $assetsPaths[1];
        }

        //$this->registerTranslations();

        $this->uploadAliasPath = "@webroot/uploads/content/{$this->id}";
        $this->uploadPath = "/uploads/content/{$this->id}";

        if (method_exists($this, 'getDefaultModelClasses')) {
            $this->modelClasses = array_merge($this->getDefaultModelClasses(), $this->modelClasses);
        }
        parent::init();
    }

    public function afterInstall()
    {

       // $reflectionClass = new \ReflectionClass(static::class);

       // $test = $this::getInstance();
//print_r(Yii::getAlias('@'.($this->id)));



       // $fileName2 = (new \ReflectionClass(new \panix\engine\WebModule($this->id)))->getFileName();

       // $fileName = (new \ReflectionClass(get_called_class()))->getFileName();
       // print_r($fileName2);


       /// print_r($reflectionClass->getNamespaceName());
        //die;

        // if ($this->uploadAliasPath && !file_exists(Yii::getPathOfAlias($this->uploadAliasPath)))
        //     CFileHelper::createDirectory(Yii::getPathOfAlias($this->uploadAliasPath), 0777);
        //Yii::$app->cache->flush();
        // Yii::app()->widgets->clear();
        return true;
    }

    /**
     * Method will be called after module removed
     */
    public function afterUninstall()
    {
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

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getAuthor()
    {
        return 'dev@pixelion.com.ua';
    }

    public function getName()
    {
        return Yii::t($this->id . "/default", 'MODULE_NAME');
    }

    public function getDescription()
    {
        return Yii::t($this->id . "/default", 'MODULE_DESC');
    }

}
