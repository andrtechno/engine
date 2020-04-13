<?php

namespace panix\engine\traits\query;

use Yii;

trait TranslateQueryTrait
{

    /**
     * @param int|null $id
     * @return $this /yii/db/Query
     */
    public function translate($id = null)
    {
        if (!$id) {
            $id = Yii::$app->languageManager->active['id'];
        }
        $this->joinWith(['translations as translate' => function ($query) use ($id) {
            /** @var \yii\db\Query $query */
            $query->andWhere(['translate.language_id' => $id]);
        }]);

        return $this;
    }

}
