<?php

namespace panix\engine\behaviors\nestedsets\actions;

use panix\engine\CMS;
use Yii;
use yii\web\Response;
use yii\rest\Action;

/**
 * Class CreateNodeAction
 * @package panix\engine\behaviors\nestedsets\actions
 */
class CreateNodeAction extends Action
{
    public $message;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->offMessage)
            $this->offMessage = Yii::t('app', 'SWITCH_OFF');

        if (!$this->onMessage)
            $this->onMessage = Yii::t('app', 'SWITCH_ON');

        Yii::$app->response->format = Response::FORMAT_JSON;
        $json = [];
        $json['success'] = false;
        if (Yii::$app->request->isAjax) {
            /* @var $modelClass \yii\db\ActiveRecord */

            $parent = $this->findModel(Yii::$app->request->get('parent_id'));

            $modelClass->name = $_GET['text'];
            $modelClass->slug = CMS::slug($modelClass->name);
            if ($modelClass->validate()) {
                $modelClass->appendTo($parent);
                $message = $this->message;
            } else {
                $message = $modelClass->getError('slug');
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            $json['message'] = $message;
        } else {
            $json['message'] = 'error [1]';
        }

        return $json;
    }
}