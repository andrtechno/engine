<?php

namespace panix\engine;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\InvalidConfigException;
use yii\web\JqueryAsset;

class View extends \yii\web\View {

    /**
     * @event Event an event that is triggered by [[beginBody()]].
     */
    const EVENT_BEGIN_BODY = 'beginBody';

    /**
     * @event Event an event that is triggered by [[endBody()]].
     */
    const EVENT_END_BODY = 'endBody';

    /**
     * The location of registered JavaScript code block or files.
     * This means the location is in the head section.
     */
    const POS_HEAD = 1;

    /**
     * The location of registered JavaScript code block or files.
     * This means the location is at the beginning of the body section.
     */
    const POS_BEGIN = 2;

    /**
     * The location of registered JavaScript code block or files.
     * This means the location is at the end of the body section.
     */
    const POS_END = 3;

    /**
     * The location of registered JavaScript code block.
     * This means the JavaScript code block will be enclosed within `jQuery(document).ready()`.
     */
    const POS_READY = 4;

    /**
     * The location of registered JavaScript code block.
     * This means the JavaScript code block will be enclosed within `jQuery(window).load()`.
     */
    const POS_LOAD = 5;

    /**
     * This is internally used as the placeholder for receiving the content registered for the head section.
     */
    const PH_HEAD = '<![CDATA[YII-BLOCK-HEAD]]>';

    /**
     * This is internally used as the placeholder for receiving the content registered for the beginning of the body section.
     */
    const PH_BODY_BEGIN = '<![CDATA[YII-BLOCK-BODY-BEGIN]]>';

    /**
     * This is internally used as the placeholder for receiving the content registered for the end of the body section.
     */
    const PH_BODY_END = '<![CDATA[YII-BLOCK-BODY-END]]>';

    /**
     * @var AssetBundle[] list of the registered asset bundles. The keys are the bundle names, and the values
     * are the registered [[AssetBundle]] objects.
     * @see registerAssetBundle()
     */
    public $assetBundles = [];

    /**
     * @var string the page title
     */
    public $title;

    /**
     * @var array the registered meta tags.
     * @see registerMetaTag()
     */
    public $metaTags = [];

    /**
     * @var array the registered link tags.
     * @see registerLinkTag()
     */
    public $linkTags = [];

    /**
     * @var array the registered CSS code blocks.
     * @see registerCss()
     */
    public $css = [];

    /**
     * @var array the registered CSS files.
     * @see registerCssFile()
     */
    public $cssFiles = [];

    /**
     * @var array the registered JS code blocks
     * @see registerJs()
     */
    public $js = [];

    /**
     * @var array the registered JS files.
     * @see registerJsFile()
     */
    public $jsFiles = [];
    private $_assetManager;

    /**
     * Marks the position of an HTML head section.
     */
    public function head() {
        echo self::PH_HEAD;
    }

    /**
     * Marks the beginning of an HTML body section.
     */
    public function beginBody() {
        echo self::PH_BODY_BEGIN;
        $this->trigger(self::EVENT_BEGIN_BODY);
    }

    /**
     * Marks the ending of an HTML body section.
     */
    public function endBody() {
        $this->trigger(self::EVENT_END_BODY);
        echo self::PH_BODY_END;

        foreach (array_keys($this->assetBundles) as $bundle) {
            $this->registerAssetFiles($bundle);
        }
    }

    /**
     * Marks the ending of an HTML page.
     * @param bool $ajaxMode whether the view is rendering in AJAX mode.
     * If true, the JS scripts registered at [[POS_READY]] and [[POS_LOAD]] positions
     * will be rendered at the end of the view like normal scripts.
     */
    public function endPage($ajaxMode = false) {
        $this->trigger(self::EVENT_END_PAGE);
        $copyright = '<a href="//corner-cms.com/" id="corner" target="_blank"><span>' . Yii::t('app', 'CORNER') . '</span> &mdash; <span class="cr-logo">CORNER</span></a>';

        $content = ob_get_clean();

        $content = str_replace(base64_decode('e2NvcHlyaWdodH0='), $copyright, $content);
        echo strtr($content, [
            self::PH_HEAD => $this->renderHeadHtml(),
            self::PH_BODY_BEGIN => $this->renderBodyBeginHtml(),
            self::PH_BODY_END => $this->renderBodyEndHtml($ajaxMode),
        ]);

        $this->clear();
    }

    /**
     * Renders a view in response to an AJAX request.
     *
     * This method is similar to [[render()]] except that it will surround the view being rendered
     * with the calls of [[beginPage()]], [[head()]], [[beginBody()]], [[endBody()]] and [[endPage()]].
     * By doing so, the method is able to inject into the rendering result with JS/CSS scripts and files
     * that are registered with the view.
     *
     * @param string $view the view name. Please refer to [[render()]] on how to specify this parameter.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @param object $context the context that the view should use for rendering the view. If null,
     * existing [[context]] will be used.
     * @return string the rendering result
     * @see render()
     */
    public function renderAjax($view, $params = [], $context = null) {
        $viewFile = $this->findViewFile($view, $context);

        ob_start();
        ob_implicit_flush(false);

        $this->beginPage();
        $this->head();
        $this->beginBody();
        echo $this->renderFile($viewFile, $params, $context);
        $this->endBody();
        $this->endPage(true);

        return ob_get_clean();
    }

    /**
     * Registers the asset manager being used by this view object.
     * @return \yii\web\AssetManager the asset manager. Defaults to the "assetManager" application component.
     */
    public function getAssetManager() {
        return $this->_assetManager ? : Yii::$app->getAssetManager();
    }

    /**
     * Sets the asset manager.
     * @param \yii\web\AssetManager $value the asset manager
     */
    public function setAssetManager($value) {
        $this->_assetManager = $value;
    }

    /**
     * Clears up the registered meta tags, link tags, css/js scripts and files.
     */
    public function clear() {
        $this->metaTags = [];
        $this->linkTags = [];
        $this->css = [];
        $this->cssFiles = [];
        $this->js = [];
        $this->jsFiles = [];
        $this->assetBundles = [];
    }

    /**
     * Registers all files provided by an asset bundle including depending bundles files.
     * Removes a bundle from [[assetBundles]] once files are registered.
     * @param string $name name of the bundle to register
     */
    protected function registerAssetFiles($name) {
        if (!isset($this->assetBundles[$name])) {
            return;
        }
        $bundle = $this->assetBundles[$name];
        if ($bundle) {
            foreach ($bundle->depends as $dep) {
                $this->registerAssetFiles($dep);
            }
            $bundle->registerAssetFiles($this);
        }
        unset($this->assetBundles[$name]);
    }

    /**
     * Registers the named asset bundle.
     * All dependent asset bundles will be registered.
     * @param string $name the class name of the asset bundle (without the leading backslash)
     * @param int|null $position if set, this forces a minimum position for javascript files.
     * This will adjust depending assets javascript file position or fail if requirement can not be met.
     * If this is null, asset bundles position settings will not be changed.
     * See [[registerJsFile]] for more details on javascript position.
     * @return AssetBundle the registered asset bundle instance
     * @throws InvalidConfigException if the asset bundle does not exist or a circular dependency is detected
     */
    public function registerAssetBundle($name, $position = null) {
        if (!isset($this->assetBundles[$name])) {
            $am = $this->getAssetManager();
            $bundle = $am->getBundle($name);
            $this->assetBundles[$name] = false;
            // register dependencies
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            foreach ($bundle->depends as $dep) {
                $this->registerAssetBundle($dep, $pos);
            }
            $this->assetBundles[$name] = $bundle;
        } elseif ($this->assetBundles[$name] === false) {
            throw new InvalidConfigException("A circular dependency is detected for bundle '$name'.");
        } else {
            $bundle = $this->assetBundles[$name];
        }

        if ($position !== null) {
            $pos = isset($bundle->jsOptions['position']) ? $bundle->jsOptions['position'] : null;
            if ($pos === null) {
                $bundle->jsOptions['position'] = $pos = $position;
            } elseif ($pos > $position) {
                throw new InvalidConfigException("An asset bundle that depends on '$name' has a higher javascript file position configured than '$name'.");
            }
            // update position for all dependencies
            foreach ($bundle->depends as $dep) {
                $this->registerAssetBundle($dep, $pos);
            }
        }

        return $bundle;
    }

    /**
     * Registers a meta tag.
     *
     * For example, a description meta tag can be added like the following:
     *
     * ```php
     * $view->registerMetaTag([
     *     'name' => 'description',
     *     'content' => 'This website is about funny raccoons.'
     * ]);
     * ```
     *
     * will result in the meta tag `<meta name="description" content="This website is about funny raccoons.">`.
     *
     * @param array $options the HTML attributes for the meta tag.
     * @param string $key the key that identifies the meta tag. If two meta tags are registered
     * with the same key, the latter will overwrite the former. If this is null, the new meta tag
     * will be appended to the existing ones.
     */
    public function registerMetaTag($options, $key = null) {
        if ($key === null) {
            $this->metaTags[] = Html::tag('meta', '', $options);
        } else {
            $this->metaTags[$key] = Html::tag('meta', '', $options);
        }
    }

    /**
     * Registers a link tag.
     *
     * For example, a link tag for a custom [favicon](http://www.w3.org/2005/10/howto-favicon)
     * can be added like the following:
     *
     * ```php
     * $view->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => '/myicon.png']);
     * ```
     *
     * which will result in the following HTML: `<link rel="icon" type="image/png" href="/myicon.png">`.
     *
     * **Note:** To register link tags for CSS stylesheets, use [[registerCssFile()]] instead, which
     * has more options for this kind of link tag.
     *
     * @param array $options the HTML attributes for the link tag.
     * @param string $key the key that identifies the link tag. If two link tags are registered
     * with the same key, the latter will overwrite the former. If this is null, the new link tag
     * will be appended to the existing ones.
     */
    public function registerLinkTag($options, $key = null) {
        if ($key === null) {
            $this->linkTags[] = Html::tag('link', '', $options);
        } else {
            $this->linkTags[$key] = Html::tag('link', '', $options);
        }
    }

    /**
     * Registers a CSS code block.
     * @param string $css the content of the CSS code block to be registered
     * @param array $options the HTML attributes for the `<style>`-tag.
     * @param string $key the key that identifies the CSS code block. If null, it will use
     * $css as the key. If two CSS code blocks are registered with the same key, the latter
     * will overwrite the former.
     */
    public function registerCss($css, $options = [], $key = null) {
        $key = $key ? : md5($css);
        $this->css[$key] = Html::style($css, $options);
    }

    /**
     * Registers a CSS file.
     * @param string $url the CSS file to be registered.
     * @param array $options the HTML attributes for the link tag. Please refer to [[Html::cssFile()]] for
     * the supported options. The following options are specially handled and are not treated as HTML attributes:
     *
     * - `depends`: array, specifies the names of the asset bundles that this CSS file depends on.
     *
     * @param string $key the key that identifies the CSS script file. If null, it will use
     * $url as the key. If two CSS files are registered with the same key, the latter
     * will overwrite the former.
     */
    public function registerCssFile($url, $options = [], $key = null) {
        $url = Yii::getAlias($url);
        $key = $key ? : $url;

        $depends = ArrayHelper::remove($options, 'depends', []);

        if (empty($depends)) {
            $this->cssFiles[$key] = Html::cssFile($url, $options);
        } else {
            $this->getAssetManager()->bundles[$key] = Yii::createObject([
                        'class' => AssetBundle::className(),
                        'baseUrl' => '',
                        'css' => [strncmp($url, '//', 2) === 0 ? $url : ltrim($url, '/')],
                        'cssOptions' => $options,
                        'depends' => (array) $depends,
            ]);
            $this->registerAssetBundle($key);
        }
    }

}
