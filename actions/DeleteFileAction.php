<?php

namespace panix\engine\actions;

use Yii;
use yii\db\Expression;
use yii\web\Response;
use yii\rest\Action;

class DeleteFileAction extends Action
{
    public $saveMethod = 'save';

    public function run()
    {

        $json = [];
        $json['status'] = 'error';
        if (isset($_REQUEST)) {

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
                            $obj->{$attribute} = new Expression('NULL');
                            unlink($filePath);
                        }
                    }

                    $obj->{$this->saveMethod}();
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        $json['status'] = 'success';
                        $json['message'] = Yii::t('app/default', 'SUCCESS_RECORD_DELETE');
                    } else {
                        return Yii::$app->response->redirect(Yii::$app->request->get('redirect'));
                    }


                }
            }
        }

        return $json;
    }

}
