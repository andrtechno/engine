<?php

namespace panix\engine\actions;

use Yii;
use yii\base\Action;

/**
 * Class RemoveFileAction
 * @package panix\engine\actions
 */
class RemoveFileAction extends Action
{

    public $path;
    public $redirect;

    public function run()
    {

        if (Yii::$app->request->get('file')) {
            $fullPath = Yii::getAlias($this->path) . DIRECTORY_SEPARATOR . Yii::$app->request->get('file');
            if (file_exists($fullPath)) {
                unlink($fullPath);
                Yii::$app->session->addFlash('success', Yii::t('app', 'FILE_SUCCESS_DELETE'));
                return Yii::$app->response->redirect($this->redirect);
            }
        }
    }

}
