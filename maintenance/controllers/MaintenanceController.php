<?php

namespace panix\engine\maintenance\controllers;

use Yii;
use panix\engine\controllers\WebController;

class MaintenanceController extends WebController {

    /**
     * Initialize controller.
     */
    public function init() {
        $this->layout = Yii::$app->maintenanceMode->layoutPath;
        parent::init();
    }

    /**
     * Index action.
     *
     * @return bool|string
     */
    public function actionIndex() {
        $app = Yii::$app;
        if ($app->getRequest()->getIsAjax()) {
            return false;
        }
        return $this->render($app->maintenanceMode->viewPath, [
            'message' => $app->settings->get('app','maintenance_text')
        ]);
    }


    public function actionCopyright() {
        $app = Yii::$app;
        if ($app->getRequest()->getIsAjax()) {
            return false;
        }
        return $this->render($app->maintenanceMode->viewPath, [
            'message' => 'cxopy das'
        ]);
    }

}
