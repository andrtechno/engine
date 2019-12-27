<?php

namespace panix\engine\actions;

use Yii;
use yii\web\Response;
use yii\rest\Action;

class DeleteAction extends Action
{

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $json = [];
        $json['success'] = false;
        if (Yii::$app->request->isAjax || Yii::$app->request->isPost && isset($_REQUEST['id'])) {

            /** @var $model \panix\engine\db\ActiveRecord */
            $model = new $this->modelClass;
            $entry = $model->find()->where(['id' => $_REQUEST['id']])->all();
            //print_r($entry);die;
            if ($entry) {
                foreach ($entry as $obj) {
                    if (!in_array($obj->primaryKey, $model->disallow_delete)) {
                        $obj->delete();
                        $json['success'] = true;
                        $json['message'] = Yii::t('app', 'SUCCESS_RECORD_DELETE');
                    } else {
                        $json['message'] = Yii::t('app', 'ERROR_RECORD_DELETE');
                    }
                }
            }
        }else{
            $json['message'] = Yii::t('app', 'Forbidden');
        }

        return $json;
    }

}
