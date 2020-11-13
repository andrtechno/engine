<?php

namespace panix\engine\actions;

use panix\engine\CMS;
use Yii;
use yii\web\Response;
use yii\rest\Action;

class DeleteAction extends Action
{
    public $primaryKey;

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //$model = new $this->modelClass;
        $json = [];
        $json['success'] = false;
//Yii::$app->request->isAjax || Yii::$app->request->isPost &&
        if (isset($_REQUEST['id'])) {

            /** @var $model \panix\engine\db\ActiveRecord */
            $model = new $this->modelClass;
            if (!$this->primaryKey) {
                $this->primaryKey = $model->primaryKey()[0];
            }
            $entry = $model->find()->where([$this->primaryKey => $_REQUEST['id']])->all();
            //print_r($entry);die;
            if ($entry) {
                foreach ($entry as $obj) {
                    if (!in_array($obj->primaryKey, $model->disallow_delete)) {
                        $obj->delete();
                        $json['success'] = true;
                        $json['message'] = Yii::t('app/default', 'SUCCESS_RECORD_DELETE');
                    } else {
                        $json['message'] = Yii::t('app/default', 'ERROR_RECORD_DELETE');
                    }
                }
            }
        } else {
            $json['message'] = Yii::t('app/error', '403');
        }

        return $json;
    }

}
