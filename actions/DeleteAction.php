<?php

namespace panix\engine\actions;

use Yii;
use yii\web\Response;
use yii\rest\Action;

class DeleteAction extends Action
{

    public function run()
    {
        $json = [];
        $json['status'] = 'error';
        if (Yii::$app->request->isPost && isset($_REQUEST['id'])) {

            /** @var $model \yii\db\ActiveRecord */
            $model = new $this->modelClass;
            $entry = $model->find()->where(['id' => $_REQUEST['id']])->all();
            print_r($entry);die;
            if ($entry) {
                foreach ($entry as $obj) {
                    if (!in_array($obj->primaryKey, $model->disallow_delete)) {
                        $obj->delete();
                        $json['status'] = 'success';
                        $json['message'] = Yii::t('app', 'SUCCESS_RECORD_DELETE');
                    } else {
                        $json['status'] = 'error';
                        $json['message'] = Yii::t('app', 'ERROR_RECORD_DELETE');
                    }
                }
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

}
