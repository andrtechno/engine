<?php

namespace panix\engine;

//use panix\engine\controllers\WebController;
use Yii;
use yii\helpers\Url;

class View extends \yii\web\View
{

    private $maintenance = false;


    public function endPage($ajaxMode = false)
    {
        $this->trigger(self::EVENT_END_PAGE);


        $this->registerCss('
        #pixelion span.cr-logo{display:inline-block;font-size:17px;padding: 0 0 0 45px;position:relative;font-family:Pixelion,Montserrat;font-weight:normal;line-height: 40px;}
        #pixelion span.cr-logo:after{font-weight:normal;content:"\f002";left:0;top:0;position:absolute;font-size:37px;font-family:Pixelion;}
        ', [], 'pixelion');

        $copyright = '<a href="//pixelion.com.ua/" id="pixelion" target="_blank"><span>' . Yii::t('app', 'PIXELION') . '</span> &mdash; <span class="cr-logo">PIXELION</span></a>';

        $content = ob_get_clean();

        if (!(Yii::$app->controller instanceof \panix\engine\controllers\AdminController)) {
            if (!Yii::$app->request->isAjax && !preg_match("#" . base64_decode('e2NvcHlyaWdodH0=') . "#", $content)) { // && !preg_match("/print/", $this->layout)
                // die(Yii::t('app', 'NO_COPYRIGHT'));
                //Yii::$app->maintenanceMode->enabled = true;
            }
        }
        $template = "/block_([0-9])/";
        preg_match_all($template, $content, $result);

        foreach ($result[0] as $block) {
            if (!empty($block)) {
                $content = str_replace('{' . $block . '}', \panix\mod\admin\models\Block::render($block), $content);
            }
        }
        $content = str_replace(base64_decode('e2NvcHlyaWdodH0='), $copyright, $content);


        echo strtr($content, [
            self::PH_HEAD => $this->renderHeadHtml(),
            self::PH_BODY_BEGIN => $this->renderBodyBeginHtml(),
            self::PH_BODY_END => $this->renderBodyEndHtml($ajaxMode),
        ]);


        $this->clear();
    }

    public function head()
    {

        if (!Yii::$app->request->isAjax) {
            $this->registerMetaTag(['charset' => Yii::$app->charset]);
            $this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->name]);
            $this->registerMetaTag(['name' => 'generator', 'content' => Yii::$app->name . ' ' . Yii::$app->version]);
        } else {
            Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = false;
            Yii::$app->assetManager->bundles['yii\bootstrap4\BootstrapPluginAsset'] = false;
        }

        if (!(Yii::$app->controller instanceof \panix\engine\controllers\AdminController)) {
            Yii::$app->seo->run();

            // Open Graph default property
            $this->registerMetaTag(['property' => 'og:locale', 'content' => Yii::$app->language]);
            $this->registerMetaTag(['property' => 'og:type', 'content' => 'article']);

            foreach (Yii::$app->languageManager->languages as $lang) {
                if (Yii::$app->language == $lang->code) {
                    $url = Url::to("/" . Yii::$app->request->pathInfo, true);
                } else {
                    $url = Url::to("/{$lang->code}/" . Yii::$app->request->pathInfo, true);
                }
                $this->registerLinkTag(['rel' => 'alternate', 'hreflang' => $lang->code, 'href' => $url]);
            }
        }

        parent::head();
    }

}
