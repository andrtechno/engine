<?php

use yii\helpers\Html;


?>
<h1><?= Yii::t('app/default', 'ACCOUNT_BANNED'); ?></h1>
<div>
    <p><?= Html::encode($message) ?></p>
    <p>До <?= $time ?></p>
</div>