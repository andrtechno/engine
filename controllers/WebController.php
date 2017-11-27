<?php

namespace panix\engine\controllers;

use Yii;
use yii\web\Controller;
use panix\engine\CMS;
use yii\web\ForbiddenHttpException;

class WebController extends Controller {

    public $breadcrumbs = [];
    public $jsMessages = [];
    public $dataModel, $pageName, $title, $keywords, $description;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
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
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionMain() {
        return $this->render('index');
    }

    public function actions() {
        return [
            'error2' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    protected function error404($text = null) {
        if (!$text)
            $text = Yii::t('app/error', '404');
        throw new \yii\web\NotFoundHttpException($text);
    }

    public function beforeAction($action) {
        $this->view->registerJs('
            common.langauge="' . Yii::$app->language . '";
            common.token="' . Yii::$app->request->csrfToken . '";
            common.isDashboard=true;
            common.message=' . \yii\helpers\Json::encode($this->jsMessages) . ';', \yii\web\View::POS_HEAD, 'js-common');

        return parent::beforeAction($action);
    }

    public function init() {
        $user = Yii::$app->user;
        $timeZone = Yii::$app->settings->get('app', 'timezone');
        Yii::$app->timeZone = $timeZone;

        $this->jsMessages = [
            'error' => [
                '404' => Yii::t('app/error', '404')
            ],
            'cancel' => Yii::t('app', 'CANCEL'),
            'send' => Yii::t('app', 'SEND'),
            'delete' => Yii::t('app', 'DELETE'),
            'save' => Yii::t('app', 'SAVE'),
            'close' => Yii::t('app', 'CLOSE'),
            //  'ok' => Yii::t('app', 'OK'),
            'loading' => Yii::t('app', 'LOADING'),
        ];

        parent::init();
    }

    public function actionNoJavascript() {
        //TODO Пересмотреть данное решение для моб где нету вообще javascript
        $this->layout = 'error';
        return $this->render('no-javascript', [
                    'name' => '',
                    'message' => 'На вашем устройстве отключен javascript. Для корректной работы сайта рекомендуем включить.'
        ]);
    }

    public function actionError() {
        $exception = Yii::$app->errorHandler->exception;

        if ($exception !== null) {
            $statusCode = $exception->statusCode;
            $name = $exception->getName();
            $message = $exception->getMessage();

            $this->layout = 'error';

            return $this->render('error', [
                        'exception' => $exception,
                        'statusCode' => $statusCode,
                        'name' => $name,
                        'message' => $message
            ]);
        }
    }

    public function actionPlaceholder() {

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
        $padding = ($request->get('padding')) ? (int) $request->get('padding') : 0;

        $fontsize = $dimensions[0] / 2;


        if (strlen($text) == 4 && preg_match("/([A-Za-z]{1}[0-9]{3})$/i", $text)) {
            $text = '&#x' . $text . ';';
            $font = Yii::getAlias('@vendor/panix/engine/assets/fonts') . DIRECTORY_SEPARATOR . 'Corner.ttf';
        } elseif ($text == 'CORNER' || $text == 'corner') {
            $font = Yii::getAlias('@vendor/panix/engine/assets/fonts') . DIRECTORY_SEPARATOR . 'Corner.ttf';
        } else {
            $font = Yii::getAlias('@vendor/panix/engine/assets/fonts') . DIRECTORY_SEPARATOR . 'Exo2-Light.ttf';
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
