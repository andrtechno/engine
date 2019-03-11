<?php

namespace panix\engine\maintenance\controllers;

use Yii;
use yii\web\Controller;

/**
 * Default controller of maintenance mode
 *
 * @see \yii\web\Controller
 */
class MaintenanceController extends Controller
{
    public $title;
    public $message;
    public $viewPath;

    /**
     * Initialize controller.
     */
    public function init()
    {

        $this->layout = Yii::$app->maintenanceMode->layoutPath;
        $this->title = ($this->title) ? $this->title : Yii::$app->maintenanceMode->title;
        $this->message = ($this->message) ? $this->message : Yii::$app->maintenanceMode->message;
        $this->viewPath = ($this->viewPath) ? $this->viewPath : Yii::$app->maintenanceMode->viewPath;
        parent::init();

    }

    /**
     * Index action.
     * @return bool|string
     */
    public function actionIndex()
    {
        $app = Yii::$app;

        if ($app->getRequest()->getIsAjax()) {
            return false;
        }

        return $this->render($this->viewPath, [
            'title' => $this->title,
            'message' => $this->message
        ]);
    }
} 