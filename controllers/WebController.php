<?php

namespace panix\engine\controllers;

use Yii;
use panix\engine\CMS;
use yii\filters\AccessControl;


/**
 * Class WebController
 * @package panix\engine\controllers
 */
class WebController extends CommonController
{




    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'like' => [
                'class' => 'panix\engine\widgets\like\actions\LikeAction',
                //  'model'=>$this->dataModel
            ],
        ];
    }

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

    public function behaviors2()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        $this->layout = 'main';
        $this->view->title = Yii::t('yii', 'Home');
        return $this->render('index');
    }


    public function getAssetUrl()
    {
        $assetsPaths = Yii::$app->getAssetManager()->publish(Yii::getAlias("@theme/assets"));
        return $assetsPaths[1];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {

        $user = Yii::$app->user;
        $config = Yii::$app->settings->get('app');
        $timeZone = $config->timezone;
        Yii::$app->timeZone = $timeZone;

        Yii::setAlias('@theme', Yii::getAlias("@app/web/themes/{$config->theme}"));
        if (Yii::$app->hasModule('stats') && !$this->dashboard && !Yii::$app->request->isAjax) {

            if (isset(Yii::$app->stats)) {
                $stats = Yii::$app->stats;
                $stats->record();
            }

        }
        parent::init();
    }

    public function actionNoJavascript()
    {
        //TODO Пересмотреть данное решение для моб где нету вообще JavaScript
        $this->layout = 'error';
        return $this->render('no-javascript', [
            'name' => '',
            'message' => Yii::t('app', 'NO_JAVASCRIPT')
        ]);
    }

    public function actionError()
    {
        /** @var $handler \yii\web\ErrorHandler */
        /** @var $exception \yii\web\HttpException */
        $handler = Yii::$app->errorHandler;
        $exception = $handler->exception;

        if ($exception !== null) {
            $statusCode = $exception->statusCode;
            $name = $exception->getName();
            $message = $exception->getMessage();

            $this->layout = "@app/web/themes/{$this->view->theme->name}/views/layouts/error";

            $this->pageName = Yii::t('app/error', $statusCode);

            $this->view->title = $this->pageName;
            $this->breadcrumbs = [$statusCode];
            return $this->render('error', [
                'exception' => $exception,
                'handler' => $handler,
                'statusCode' => $statusCode,
                'name' => $name,
                'message' => $message
            ]);
        }
    }

    public function actionPlaceholder()
    {

        $request = Yii::$app->request;
        // Dimensions
        $getsize = ($request->get('size')) ? $request->get('size') : '100x100';
        $dimensions = explode('x', $getsize);

        if (empty($dimensions[0])) {
            $dimensions[0] = $dimensions[1];
        }
        if (empty($dimensions[1])) {
            $dimensions[1] = $dimensions[0];
        }

        header("Content-type: image/png");
        // Create image
        $image = imagecreate($dimensions[0], $dimensions[1]);

        // Colours
        $bg = ($request->get('bg')) ? $request->get('bg') : 'ccc';

        $bg = CMS::hex2rgb($bg);
        $opacityBg = ($request->get('bg')) ? 0 : 127;
        //$setbg = imagecolorallocate($image, $bg['r'], $bg['g'], $bg['b']);
        $setbg = imagecolorallocatealpha($image, $bg['r'], $bg['g'], $bg['b'], $opacityBg);

        $fg = ($request->get('fg')) ? $request->get('fg') : '999';
        $fg = CMS::hex2rgb($fg);
        $setfg = imagecolorallocate($image, $fg['r'], $fg['g'], $fg['b']);

        $text = ($request->get('text')) ? strip_tags($request->get('text')) : $getsize;
        $text = str_replace('+', ' ', $text);
        $padding = ($request->get('padding')) ? (int)$request->get('padding') : 0;

        $fontsize = $dimensions[0] / 2;


        if (strlen($text) == 4 && preg_match("/([A-Za-z]{1}[0-9]{3})$/i", $text)) {
            $text = '&#x' . $text . ';';
            $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Pixelion.ttf';
        } elseif ($text == 'PIXELION' || $text == 'pixelion') {
            $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Pixelion.ttf';
        } else {
            $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Exo2-Light.ttf';
        }

        $textBoundingBox = imagettfbbox($fontsize - $padding, 0, $font, $text);
        // decrease the default font size until it fits nicely within the image
        while (((($dimensions[0] - ($textBoundingBox[2] - $textBoundingBox[0])) < $padding) || (($dimensions[1] - ($textBoundingBox[1] - $textBoundingBox[7])) < $padding)) && ($fontsize - $padding > 1)) {
            $fontsize--;
            $textBoundingBox = imagettfbbox($fontsize - $padding, 0, $font, $text);
        }

        imagettftext($image, $fontsize - $padding, 0, ($dimensions[0] / 2) - (($textBoundingBox[2] - $textBoundingBox[0]) / 2), ($dimensions[1] / 2) - (($textBoundingBox[1] + $textBoundingBox[7]) / 2), $setfg, $font, $text);


        imagepng($image);
        imagedestroy($image);
        Yii::$app->end();
    }




}
