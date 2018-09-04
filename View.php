<?php

namespace panix\engine;

use Yii;
use yii\helpers\Url;

class View extends \yii\web\View
{

    public function endPage($ajaxMode = false)
    {
        $this->trigger(self::EVENT_END_PAGE);
        $copyright = '<a href="//pixelion.com.ua/" id="pixelion" target="_blank"><span>' . Yii::t('app', 'PIXELION') . '</span> &mdash; <span class="cr-logo">PIXELION</span></a>';

        $content = ob_get_clean();

        $template = "/block_([0-9])/";
        preg_match_all($template, $content, $result);

        foreach ($result[0] as $block) {
            if (!empty($block)) {
                $content = str_replace('{' . $block . '}', \panix\mod\admin\models\Block::render($block), $content);
            }
        }
        $content = str_replace(base64_decode('e2NvcHlyaWdodH0='), $copyright, $content);


        // if (!Yii::$app->request->isAjax && !preg_match("#" . base64_decode('e2NvcHlyaWdodH0=') . "#", $content)) { // && !preg_match("/print/", $this->layout)
        //  die(Yii::t('app','NO_COPYRIGHT'));
        // }


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
        }else{
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
