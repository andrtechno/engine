<?php

namespace panix\engine\api;


use Yii;
use yii\base\Exception;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\HostControl;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;

class ApiController extends Controller
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
                'fallbackHostInfo' => Yii::$app->request->getHostInfo()
                //'fallbackHostInfo' => ((Yii::$app->request->isSecureConnection) ? 'https://' : 'http://') . Yii::$app->getModule('shop')->host,
            ];
        }
        if (Yii::$app->getModule('shop')->ips) {
            $b2['ipAcess'] = [
                'class' => IpAccessControl::class,
                'ips' => Yii::$app->getModule('shop')->ips,
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
            'cors' => [
                'Origin' => ['http://optikon.com.ua','https://optikon.com.ua'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => [],
            ]
        ];
        return $b;
    }
}