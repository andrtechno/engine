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
        <title><?= Html::encode(Yii::$app->name); ?></title>
        <?php $this->head(); ?>
    </head>
    <body>
    <?php $this->beginBody(); ?>
    <section>
        <?= $content; ?>
    </section>
    <footer>
        <div class="container">
            <p class="text-center"><?= Yii::$app->powered() ?></p>
        </div>
    </footer>
    <?php $this->endBody(); ?>
    </body>
    </html>
<?php $this->endPage(); ?>
