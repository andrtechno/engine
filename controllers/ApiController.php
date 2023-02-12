<?php

namespace panix\engine\controllers;

use Yii;
use yii\helpers\Url;
use yii\rest\Controller;

/**
 * Class ApiController
 * @package panix\engine\controllers
 */
class ApiController extends Controller
{


    public function behaviors()
    {
        return [
            'access' => [
                'class' => \panix\mod\rbac\filters\AccessControl::class,
                'allowActions' => [
                    '*',
                    // The actions listed here will be allowed to everyone including guests.
                ]
            ],
        ];
    }


    public function getAssetUrl()
    {
        $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@theme/assets"));
        return $assetsPaths[1];
    }


    public function actionError()
    {
        /**
         * @var $handler \yii\web\ErrorHandler
         * @var $exception \yii\web\HttpException
         */
        $handler = Yii::$app->errorHandler;
        $exception = $handler->exception;

        if ($exception !== null) {
            $statusCode = $exception->statusCode;
            $name = $exception->getName();
            $message = $exception->getMessage();

            return $this->asJson([
                'success' => false,
                'status' => $statusCode,
                'name' => $name,
                'message' => $message
            ]);
        }
    }

    public function actionIndex()
    {
        return $this->asJson(['success' => true]);
    }

}
