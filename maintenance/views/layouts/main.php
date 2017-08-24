<?php

use yii\helpers\Html;
use panix\engine\maintenance\Asset;

Asset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo \Yii::$app->language; ?>">
    <head>
        <meta charset="<?php echo \Yii::$app->charset; ?>">
        <title><?php echo Html::encode(Yii::$app->name); ?></title>
<?php $this->head(); ?>
    </head>
    <body>
            <?php $this->beginBody() ?>
        <section>
<?php echo $content; ?>
        </section>
        <footer>
            <div class="container">
                <p class="pull-right"><?= Yii::$app->powered() ?></p>
            </div>
        </footer>
<?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>