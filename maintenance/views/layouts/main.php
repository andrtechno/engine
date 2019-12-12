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
<section class="container">
    <div class="row">
        <div class="col-sm-6 offset-sm-3">
            <div class="d-flex1 align-items-center1">
                <div class="content">
                    <?= $content; ?>
                </div>

            </div>
        </div>
    </div>

</section>
<footer class="text-center">

    <p><?= Yii::$app->powered() ?></p>

</footer>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
