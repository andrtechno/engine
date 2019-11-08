<?php

namespace panix\engine\behaviors\nestedsets\actions_old;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\HttpException;


/**
 * Class CreateNodeAction
 * @package panix\engine\behaviors\nestedsets\actions
 */
class CreateNodeAction extends Action
{
    /**
     * Class to use to locate the supplied data ids
     * @var string
     */
    public $modelClass;

    /**
     * Attribute for name in model
     * @var string
     */
    public $nameAttribute = 'name';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (null == $this->modelClass) {
            throw new InvalidConfigException('Param "modelClass" must be contain model name with namespace.');
        }
    }

    /**
     * @return null
     * @throws HttpException
     */
    public function run()
    {
        $name = Yii::$app->request->post('name');

        /** @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|\yii\db\ActiveRecord $model */
        $model = new $this->modelClass;
        $model->{$this->nameAttribute} = $name;

        $roots = $model::find()->roots()->all();

        if (isset($roots[0])) {
            $model->appendTo($roots[0]);
        } else {
            $model->moveAsRoot();
        }

        return null;
    }
}