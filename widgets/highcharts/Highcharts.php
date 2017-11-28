<?php
namespace panix\engine\widgets\highcharts;

use Yii;
use yii\helpers\Json;

use panix\engine\Html;
use panix\engine\widgets\highcharts\HighchartsAsset;
/**
 * HighchartsWidget class file.
 * 
 * @author CORNER CMS development team <dev@corner-cms.com>
 * @version 6.0.3
 * 
 */

class Highcharts extends \panix\engine\data\Widget {

    protected $_constr = 'chart';
    protected $_baseScript = 'highcharts';
    protected $_baseScript3D = 'highcharts-3d';
    public $options = array();
    public $htmlOptions = array();
    public $setupOptions = array();
    public $scripts = array();
    public $callback = false;

    /**
     * Renders the widget.
     */
    public function run() {
        if (isset($this->htmlOptions['id'])) {
            $this->id = $this->htmlOptions['id'];
        } else {
            $this->htmlOptions['id'] = $this->getId();
        }

        echo Html::beginTag('div', $this->htmlOptions);
        echo Html::endTag('div');

        // check if options parameter is a json string
        if (is_string($this->options)) {
            if (!$this->options = Json::decode($this->options)) {
                throw new Exception('The options parameter is not valid JSON.');
            }
        }

        // merge options with default values
        $defaultOptions = array(
            'credits' => array(
                'enabled' => true,
                'text' => 'CORNER CMS',
                'href' => 'http://corner-cms.com',
            ),
            'chart' => array('renderTo' => $this->id)
        );
        $this->options = \yii\helpers\ArrayHelper::merge($defaultOptions, $this->options);
        array_unshift($this->scripts, $this->_baseScript);

        $this->registerAssets();
    }

    /**
     * Publishes and registers the necessary script files.
     */
    protected function registerAssets() {
            $view = Yii::$app->view;
            $bundle = HighchartsAsset::register($view);


        // register additional scripts
        $extension = YII_DEBUG ? '.src.js' : '.js';
        foreach ($this->scripts as $script) {
            $view->registerJsFile("{$bundle->baseUrl}/{$script}{$extension}");
        }

        // highcharts and highstock can't live on the same page
        if ($this->_baseScript === 'highstock') {
        // $cs->scriptMap["highcharts{$extension}"] = "{$bundle->baseUrl}/highstock{$extension}";
        }

        // prepare and register JavaScript code block
        $jsOptions = Json::encode($this->options);
        $setupOptions = Json::encode($this->setupOptions);
        $js = "Highcharts.setOptions($setupOptions); var chart = new Highcharts.{$this->_constr}($jsOptions);";
        $key = __CLASS__ . '#' . $this->id;
        if (is_string($this->callback)) {
            $callbackScript = "function {$this->callback}(data) {{$js}}";
            $view->registerJs($callbackScript, \yii\web\View::POS_END, $key);
           // $cs->registerScript($key, $callbackScript, CClientScript::POS_END);
        } else {
            $view->registerJs($js, \yii\web\View::POS_LOAD, $key);

        }
         
            //$view->registerJs('$("#menu-toggle").click(function (e) {
            //     chart.reflow();
           //});', \yii\web\View::POS_LOAD,$key.'resize');
    }

}
