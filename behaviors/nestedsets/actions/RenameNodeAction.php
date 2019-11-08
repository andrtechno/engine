<?php

namespace panix\engine\behaviors\nestedsets\actions;

use Yii;
use yii\web\Response;
use yii\rest\Action;
use panix\engine\CMS;

/**
 * Class RenameNodeAction
 * @package panix\engine\behaviors\nestedsets\actions
 */
class RenameNodeAction extends Action
{
    public $successMessage;
    public $errorMessage;

    /**
     * @inheritdoc
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;


        if (!$this->successMessage)
            $this->successMessage = Yii::t('app', 'SUCCESS_UPDATE');

        if (!$this->errorMessage)
            $this->errorMessage = Yii::t('app', 'ERROR_UPDATE');


        $json = [];
        $json['success'] = false;
        if (Yii::$app->request->isAjax) {
            /* @var $modelClass \yii\db\ActiveRecord */
            $modelClass = $this->modelClass;


            if (strpos(Yii::$app->request->get('id'), 'j1_') === false) {
                $id = Yii::$app->request->get('id');
            } else {
                $id = str_replace('j1_', '', Yii::$app->request->get('id'));
            }

            $entry = $modelClass::findOne($id);
            if ($entry) {
                $entry->name = Yii::$app->request->get('text');
                $entry->slug = CMS::slug($entry->name);
                if ($entry->validate()) {
                    $entry->saveNode(false);
                    $json['success'] = true;
                    $json['message'] = $this->successMessage;
                } else {
                    $json['message'] = $this->errorMessage;
                }

            } else {
                $json['message'] = 'error [2]';
            }
        } else {
            $json['message'] = 'error [1]';
        }

        return $json;
    }
}