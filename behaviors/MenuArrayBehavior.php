<?php

/**
 * Represent model as array needed to create CMenu.
 * Usage:
 * 	'MenuArrayBehavior'=>array(
 * 		'class'=>'app.behaviors.MenuArrayBehavior',
 * 		'labelAttr'=>'name',
 * 		'urlExpression'=>'array("/shop/category", "id"=>$model->id)',
 * TODO: Cache queries
 * 	)
 */
class MenuArrayBehavior extends CActiveRecordBehavior {

    /**
     * @var string Owner attribute to be placed in `label` key
     */
    public $labelAttr;
    public $countProduct = false;

    /**
     * @var string Expression will be evaluated to create url.
     * Example: 'urlExpression'=>'array("/shop/category", "id"=>$model->id)',
     */
    public $urlExpression;

    public function menuArray() {
        return $this->walkArray($this->owner);
    }

    private function isActive($url = false) {
        if($url['seo_alias']==Yii::app()->request->getParam('seo_alias')){
            return true;
        }else{
            return false;
        }
        return false;
    }

    /**
     * Recursively build menu array
     * @param $model CActiveRecord model with NestedSet behavior
     * @return array
     */
    protected function walkArray($model) {
        $url = $this->evaluateUrlExpression($this->urlExpression, array('model' => $model));
        $data = array(
            'label' => $model->{$this->labelAttr},
            'url' => $url,
            'imagePath' => $model->getImageUrl('image', 'categories', '140x140'),
            'linkOptions' => array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'),
            'itemOptions' => array('class' => 'dropdown menu-item'),
            'active' => $this->isActive($url),
            'total_count' => ($this->countProduct) ? $model->countProducts : false,
        );
        // TODO: Cache result
        $children = $model->children()
                ->active()
                ->findAll();
        if (!empty($children)) {
            foreach ($children as $c)
                $data['items'][] = $this->walkArray($c);
        }
        return $data;
    }

    /**
     * @param $expression
     * @param array $data
     * @return mixed
     */
    public function evaluateUrlExpression($expression, $data = array()) {
        extract($data);
        return eval('return ' . $expression . ';');
    }

}
