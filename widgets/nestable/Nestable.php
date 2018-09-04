<?php

namespace panix\engine\widgets\nestable;

use panix\engine\behaviors\NestedSetsBehavior;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap4\Button;
use yii\bootstrap4\ButtonGroup;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * Class Nestable
 * @package voskobovich\nestedsets\widgets
 */
class Nestable extends Widget
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var array
     */
    public $modelClass;

    /**
     * @var array
     */
    public $nameAttribute = 'name';

    /**
     * Behavior key in list all behaviors on model
     * @var string
     */
    public $behaviorName = 'nestedSetsBehavior';

    /**
     * @var array.
     */
    public $pluginOptions = [];

    /**
     * Url to MoveNodeAction
     * @var string
     */
    public $moveUrl;

    /**
     * Url to CreateNodeAction
     * @var string
     */
    public $createUrl;

    /**
     * Url to UpdateNodeAction
     * @var string
     */
    public $updateUrl;

    /**
     * Url to page additional update model
     * @var string
     */
    public $advancedUpdateRoute;

    /**
     * Url to DeleteNodeAction
     * @var string
     */
    public $deleteUrl;

    /**
     * Структура меню в php array формате
     * @var array
     */
    private $_items = [];

    /**
     * @var string
     */
    private $_leftAttribute;

    /**
     * @var string
     */
    private $_rightAttribute;

    /**
     * Инициализация плагина
     */
    public function init()
    {
        parent::init();

        if (empty($this->id)) {
            $this->id = $this->getId();
        }

        if ($this->modelClass == null) {
            throw new InvalidConfigException('Param "modelClass" must be contain model name');
        }

        if (null == $this->behaviorName) {
            throw new InvalidConfigException("No 'behaviorName' supplied on action initialization.");
        }

        if (null == $this->advancedUpdateRoute && ($controller = Yii::$app->controller)) {
            $this->advancedUpdateRoute = "{$controller->id}/update";
        }

        /** @var ActiveRecord $model */
        $model = new $this->modelClass;
        /** @var NestedSetsBehavior $behavior */
        $behavior = $model->getBehavior($this->behaviorName);

        $this->_leftAttribute = $behavior->leftAttribute;
        $this->_rightAttribute = $behavior->rightAttribute;

        $items = $model::find()
            ->orderBy([$this->_leftAttribute => SORT_ASC])
            ->all();
        $this->_items = $this->prepareItems($items);
    }

    /**
     * @param ActiveRecord[] $items
     * @return array
     */
    private function prepareItems($items)
    {
        $stack = [];
        $arraySet = [];

        foreach ($items as $item) {
            $stackSize = count($stack);
            while ($stackSize > 0 && $stack[$stackSize - 1]['rgt'] < $item->{$this->_leftAttribute}) {
                array_pop($stack);
                $stackSize--;
            }

            $link =& $arraySet;
            for ($i = 0; $i < $stackSize; $i++) {
                $link =& $link[$stack[$i]['index']]['children']; //navigate to the proper children array
            }
            $tmp = array_push($link, [
                'id' => $item->getPrimaryKey(),
                'name' => $item->{$this->nameAttribute},
                'update-url' => Url::to([$this->advancedUpdateRoute, 'id' => $item->getPrimaryKey()]),
                'children' => []
            ]);
            array_push($stack, [
                'index' => $tmp - 1,
                'rgt' => $item->{$this->_rightAttribute}
            ]);
        }

        return $arraySet;
    }

    /**
     * Работаем!
     */
    public function run()
    {
        $this->registerActionButtonsAssets();
        $this->actionButtons();

        Pjax::begin([
            'id' => $this->id . '-pjax'
        ]);
        $this->registerPluginAssets();
        $this->renderMenu();
        Pjax::end();

        $this->actionButtons();
    }

    /**
     * Register Asset manager
     */
    private function registerPluginAssets()
    {
        NestableAsset::register($this->getView());

        if ($this->moveUrl) {
            $this->pluginOptions['moveUrl'] = $this->moveUrl;
        }
        if ($this->createUrl) {
            $this->pluginOptions['createUrl'] = $this->createUrl;
        }
        if ($this->updateUrl) {
            $this->pluginOptions['updateUrl'] = $this->updateUrl;
        }
        if ($this->deleteUrl) {
            $this->pluginOptions['deleteUrl'] = $this->deleteUrl;
        }

        $view = $this->getView();

        $pluginOptions = ArrayHelper::merge($this->getDefaultPluginOptions(), $this->pluginOptions);
        $pluginOptions = Json::encode($pluginOptions);
        $view->registerJs("$('#{$this->id}').nestable({$pluginOptions});");
    }

    /**
     * Register Asset manager
     */
    private function registerActionButtonsAssets()
    {
        $view = $this->getView();
        $view->registerJs("
			$('.{$this->id}-nestable-menu').on('click', function(e) {
				var target = $(e.target),
				    action = target.data('action');

                    console.log(action);

				switch (action) {
					case 'expand-all':
					    $('#{$this->id}').nestable('expandAll');
					    $('.{$this->id}-nestable-menu [data-action=\"expand-all\"]').hide();
					    $('.{$this->id}-nestable-menu [data-action=\"collapse-all\"]').show();

						break;
					case 'collapse-all':
					    $('#{$this->id}').nestable('collapseAll');
					    $('.{$this->id}-nestable-menu [data-action=\"expand-all\"]').show();
					    $('.{$this->id}-nestable-menu [data-action=\"collapse-all\"]').hide();

						break;
					case 'create-item': $('#{$this->id}').nestable('createNode');
				}

				return false;
			});
		");
    }

    /**
     * Generate default plugin options
     * @return array
     */
    private function getDefaultPluginOptions()
    {
        $options = [
            'namePlaceholder' => $this->getPlaceholderForName(),
            'deleteAlert' => Yii::t('app', 'The nobe will be removed together with the children. Are you sure?'),
            'newNodeTitle' => Yii::t('app', 'Enter the new node name'),
        ];

        $controller = Yii::$app->controller;
        if ($controller) {
            $options['moveUrl'] = Url::to(["{$controller->id}/moveNode"]);
            $options['createUrl'] = Url::to(["{$controller->id}/createNode"]);
            $options['updateUrl'] = Url::to(["{$controller->id}/updateNode"]);
            $options['deleteUrl'] = Url::to(["{$controller->id}/deleteNode"]);
        }

        return $options;
    }

    /**
     * Get placeholder for Name input
     */
    public function getPlaceholderForName()
    {
        return Yii::t('app', 'Node name');
    }

    /**
     * Кнопки действий над виджетом
     */
    public function actionButtons()
    {
        echo Html::beginTag('div', ['class' => "{$this->id}-nestable-menu"]);

        echo Html::beginTag('div', ['class' => 'btn-group']);
        echo Html::button(Yii::t('app', 'Add node'), [
            'data-action' => 'create-item',
            'class' => 'btn btn-success'
        ]);
        echo Html::button(Yii::t('app', 'Collapse all'), [
            'data-action' => 'collapse-all',
            'class' => 'btn btn-default'
        ]);
        echo Html::button(Yii::t('app', 'Expand all'), [
            'data-action' => 'expand-all',
            'class' => 'btn btn-default',
            'style' => 'display: none'
        ]);
        echo Html::endTag('div');

        echo Html::endTag('div');
    }

    /**
     * Вывод меню
     */
    private function renderMenu()
    {
        echo Html::beginTag('div', ['class' => 'dd-nestable', 'id' => $this->id]);

        $menu = (count($this->_items) > 0) ? $this->_items : [
            ['id' => 0, 'name' => $this->getPlaceholderForName()]
        ];

        $this->printLevel($menu);

        echo Html::endTag('div');
    }

    /**
     * Распечатка одного уровня
     * @param $level
     */
    private function printLevel($level)
    {
        echo Html::beginTag('ol', ['class' => 'dd-list']);

        foreach ($level as $item) {
            $this->printItem($item);
        }

        echo Html::endTag('ol');
    }

    /**
     * Распечатка одного пункта
     * @param $item
     */
    private function printItem($item)
    {
        $htmlOptions = ['class' => 'dd-item'];
        $htmlOptions['data-id'] = !empty($item['id']) ? $item['id'] : '';

        echo Html::beginTag('li', $htmlOptions);

        echo Html::tag('div', '', ['class' => 'dd-handle']);
        echo Html::tag('div', $item['name'], ['class' => 'dd-content']);

        echo Html::beginTag('div', ['class' => 'dd-edit-panel']);
        echo Html::input('text', null, $item['name'], ['class' => 'dd-input-name', 'placeholder' => $this->getPlaceholderForName()]);

        echo Html::beginTag('div', ['class' => 'btn-group']);
        echo Html::button(Yii::t('app', 'Save'), [
            'data-action' => 'save',
            'class' => 'btn btn-success btn-sm',
        ]);
        echo Html::a(Yii::t('app', 'Advanced editing'), $item['update-url'], [
            'data-action' => 'advanced-editing',
            'class' => 'btn btn-default btn-sm',
            'target' => '_blank'
        ]);
        echo Html::button(Yii::t('app', 'Delete'), [
            'data-action' => 'delete',
            'class' => 'btn btn-danger btn-sm'
        ]);
        echo Html::endTag('div');

        echo Html::endTag('div');

        if (isset($item['children']) && count($item['children'])) {
            $this->printLevel($item['children']);
        }

        echo Html::endTag('li');
    }
}