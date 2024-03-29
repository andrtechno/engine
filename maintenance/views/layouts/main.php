<?php

use yii\helpers\Html;
use panix\engine\maintenance\Asset;

Asset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language; ?>">
<head>
    <meta charset="<?= \Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>
<div class="container">
    <?= $content; ?>
</div>


<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
