<?php

namespace panix\engine\behaviors\wizard;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class WizardMenu extends Widget {

    /** @var array */
    public $options = [
        'activeCssClass' => 'active',
        'firstItemCssClass' => 'first',
        'lastItemCssClass' => 'last',
        'previousItemsCssClass' => 'previous'
    ];
    public $items;

    /**
     * @property string If not empty, this is added to the menu as the last item.
     * Used to add the conclusion, i.e. what happens when the wizard completes -
     * e.g. Register, to a menu.
     */
    public $menuLastItem;

    /** @var Behavior Wizard behavior class */
    protected $wizard;

    /** @var Controller */
    private $controller;

    /** @var Object */
    protected $widget;
    public $menuConfig = [];

    public function init() {
        $this->controller = $this->getView()->context;

        foreach ($this->controller->getBehaviors() as $behavior) {
            if ($behavior instanceof WizardBehavior)
                $this->wizard = $behavior;
        }
        if (!$this->wizard instanceof WizardBehavior)
            throw new InvalidConfigException(\Yii::t('app', 'Behavior ' . __NAMESPACE__ . '\Behavior not found at Controller'));

        

            
        $defaultConfig = [];
        $defaultConfig['class'] = '\yii\bootstrap\Nav';
        $defaultConfig['items'] = $this->generateMenuItems();
        

            
        $this->widget = \Yii::createObject(ArrayHelper::merge($defaultConfig, $this->menuConfig));

        parent::init();
    }

    public function run() {
        return $this->widget->run();
    }

    private function generateMenuItems() {

        $previous = true;
        $items = [];
        $url = [$this->controller->id . '/' . $this->controller->action->id];
        $parsedSteps = $this->wizard->getParsedSteps();

        foreach ($parsedSteps as $step) {
            $item = [];
            $item['label'] = $this->wizard->getStepLabel($step);
            if (($previous && !$this->wizard->forwardOnly) || ($step === $this->wizard->getCurrentStep())) {
                $item['url'] = $url + [$this->wizard->queryParam => $step];
      
                if ($step === $this->wizard->getCurrentStep()){
                    $previous = false;
                }
            }
            $item['active'] = $step === $this->wizard->getCurrentStep();
            if ($previous && !empty($this->options['previousItemsCssClass'])){
                $item['label'] .= \panix\engine\Html::tag('i','',['class'=>'icon-check text-success']);
                $item['options'] = ['class' => $this->options['previousItemsCssClass']];
            }
            $item['encode']=false;

            $items[] = $item;
        }
        if (!empty($this->menuLastItem))
            $items[] = array(
                'label' => $this->menuLastItem,
                'active' => false,
            );
      
        return $items;
    }

}
