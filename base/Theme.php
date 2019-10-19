<?php

namespace panix\engine\base;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class Theme
 *
 * @property string $name
 *
 * @package panix\engine\base
 */
class Theme extends \yii\base\Theme
{

    public $name = null;

    public function init()
    {
        Yii::debug('init', __METHOD__);
        if (preg_match("/admin/", Yii::$app->request->getUrl())) {
            //if (preg_match("/^\/\admin/", Yii::$app->request->getUrl())) {
            $this->name = 'dashboard';
        }
        if ($this->name == null) {
            Yii::debug('Loading null', __METHOD__);
            $this->name = Yii::$app->settings->get('app', 'theme');
        }


        Yii::debug('Loading ' . $this->name, __METHOD__);


        $this->basePath = "@app/web/themes/{$this->name}";
        $this->baseUrl = "@app/web/themes/{$this->name}";
        if (!file_exists(Yii::getAlias($this->basePath))) {
            throw new InvalidConfigException("Error: theme \"{$this->name}\" not found!");
        }

        $modulesPaths = [];
        foreach (Yii::$app->getModules() as $id => $mod) {
            $modulesPaths['@' . $id] = "@app/web/themes/{$this->name}/modules/{$id}";
            //$modulesPaths['@app/modules/' . $id] = "@frontend/themes/{$this->name}/modules/{$id}";
        }

        $this->pathMap = ArrayHelper::merge([
            "@app/views" => "@app/web/themes/{$this->name}/views",
            '@app/modules' => "@app/web/themes/{$this->name}/modules",
            '@app/widgets' => "@app/web/themes/{$this->name}/widgets",
        ], $modulesPaths);

        parent::init();
    }


    public function alert($content, $type = 'secondary')
    {
        return Yii::$app->view->render('@theme/views/_bootstrap/alert',[
            'type'=>$type,
            'content'=>$content
        ]);
        //return Html::tag('div', $text, ['class' => 'alert alert-' . $type]);
    }


    public function badge($text, $type = 'secondary')
    {
        return Html::tag('span', $text, ['class' => 'badge badge-' . $type]);
    }

}
