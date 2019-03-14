<?php
namespace panix\engine\behaviors;

use Yii;
use yii\base\Behavior;

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
class MenuArrayBehavior extends Behavior {

    /**
     * @var string Owner attribute to be placed in `label` key
     */
    public $labelAttr;
    public $countItems = true;

    /**
     * @var string Expression will be evaluated to create url.
     * Example: 'urlExpression'=>'array("/shop/category", "id"=>$model->id)',
     */
    public $urlExpression;

    public function menuArray() {
        /** @param $this->owner \panix\engine\db\ActiveRecord */
        return $this->walkArray($this->owner);
    }

    private function isActive($url = false) {
        if (!Yii::$app instanceof \yii\console\Application) {
            if ($url['seo_alias'] == Yii::$app->request->get('seo_alias')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Recursively build menu array
     * @param $model \panix\engine\db\ActiveRecord model with NestedSet behavior
     * @return array
     */
    protected function walkArray($model) {
        $url = $this->evaluateUrlExpression($this->urlExpression, array('model' => $model));
        $data = array(
            'label' => $model->{$this->labelAttr},
            'url' => $url,
                    'id' => $model->id,
           // 'imagePath' => $model->getImageUrl('image', 'categories', '140x140'),
            'linkOptions' => array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'),
            'itemOptions' => array('class' => 'dropdown menu-item'),
            'active' => $this->isActive($url),
            'total_count' => ($this->countItems) ? $model->countItems : 0,
        );
        // TODO: Cache result
        $children = $model->children()
                ->published()
                ->all();
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
