<?php

namespace panix\engine\api;

use Yii;
use yii\rest\ActiveController;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;
use yii\filters\HostControl;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;

class ApiActiveController extends ActiveController
{
    public function behaviors()
    {
        $b = [];
        if (Yii::$app->getModule('shop')->host) {
            $b['hostControl'] = [
                'class' => HostControl::class,
                'allowedHosts' => [
                    Yii::$app->getModule('shop')->host,
                    '*.' . Yii::$app->getModule('shop')->host,
                ],
                'denyCallback' => function ($action) {
                    throw new MethodNotAllowedHttpException('Oops!!!');
                },
                'fallbackHostInfo'=>Yii::$app->request->getHostInfo()
                //'fallbackHostInfo' => ((Yii::$app->request->isSecureConnection) ? 'https://' : 'http://') . Yii::$app->getModule('shop')->host,
            ];
        }


        $b['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formatParam' => 'format',
            'formats' => [
                'json' => Response::FORMAT_JSON,
                //'xml' => Response::FORMAT_XML,
            ]
        ];

        $b['corsFilter'] = [
            'class' => Cors::class,
        ];
        return $b;
    }
}