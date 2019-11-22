<?php

namespace panix\engine\actions;

use Yii;
use yii\web\Response;
use yii\rest\Action;

class DeleteFileAction extends Action
{

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $json = [];
        $json['status'] = 'error';
        if (isset($_REQUEST) && Yii::$app->request->isAjax) {

            /** @var $model \yii\db\ActiveRecord */
            $model = new $this->modelClass;
            $entry = $model->find()->where(['id' => $_REQUEST['key']])->all();
            if ($entry) {
                foreach ($entry as $obj) {
                    /** @var $obj \yii\db\ActiveRecord */
                    $filesBehavior = $obj->getBehavior('uploadFile');

                    foreach ($filesBehavior->files as $attribute => $path) {
                        $filePath = Yii::getAlias($path) . DIRECTORY_SEPARATOR . $obj->{$attribute};
                        if (file_exists($filePath)) {
                           // echo $filePath;
                            unlink($filePath);
                            $obj->{$attribute} = NULL;

                        }
                    }
                    $obj->save();
                    $json['status'] = 'success';
                    $json['message'] = Yii::t('app', 'SUCCESS_RECORD_DELETE');

                }
            }
        }

        return $json;
    }

}
