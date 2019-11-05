<?php

use panix\engine\Html;

?>

<div class="webpanel webpanel-shadow-bottom">
    <nav class="webpanel-navbar navbar-expand-lg">

        <div class="navbar-header">
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="webpanel-logo" href="#">PIXELION</a>
        </div>
        <div id="navbar" class="webpanel-navbar-collapse wp-collapse">



            <ul class="webpanel-nav webpanel-navbar-nav">


                <li class="active"><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown</a>
                    <ul class="dropdown-menu webpanel-dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">Nav header</li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>
            </ul>


            <?php if (isset($this->context->dataModel)) {$dataModel=$this->context->dataModel; ?>
                <div class="navbar-form webpanel-navbar-nav">
                    <div class="webpanel-btn-group">
                        <?php
                        echo Html::a('<i class="icon-add"></i>', $dataModel->getCreateUrl(), array(
                            'title' => Yii::t($dataModel::MODULE_ID . '/default', 'CREATE'),
                            'target' => '_blank',
                            'data-toggle' => 'admin-tooltip',
                            'class' => 'webpanel-btn webpanel-btn-xs webpanel-btn-success'
                        ));
                        ?>
                        <?php
                        echo Html::a('<i class="icon-edit"></i>', $dataModel->getUpdateUrl(), array(
                            'title' => Yii::t('app', 'UPDATE', 0),
                            'target' => '_blank',
                            'data-toggle' => 'admin-tooltip',
                            'class' => 'webpanel-btn webpanel-btn-xs webpanel-btn-default'
                        ));
                        ?>
                    </div>
                </div>
            <?php } ?>

            <ul class="webpanel-nav webpanel-navbar-nav navbar-right">
                <li class="active"><a href="./">Default <span class="sr-only">(current)</span></a></li>


                <?php foreach (Yii::$app->getModules() as $module) { ?>
                    <?php if ($module instanceof \panix\engine\WebModule && $module->count) { ?>
                        <li title="<?= $module->count['label'] ?>">
                            <a href="<?= Yii::$app->urlManager->createUrl($module->count['url']); ?>" target="_blank">
                                <?php if (isset($module->icon)) { ?>
                                    <i class="<?= $module->icon ?>"></i>
                                <?php } ?>
                                <span class="hidden-md hidden-lg hidden-sm"><?= $module->count['label'] ?></span>
                                <span id="counter-<?=$module->id?>" class="count webpanel-label webpanel-label-success"><?= $module->count['num'] ?></span>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
                <li class="dropdown"><a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-target="#" class="dropdown-toggle" href="/testurl"><?= Yii::$app->user->getDisplayName(); ?></a>
                    <ul class="dropdown-menu webpanel-dropdown-menu">
                        <li><?= Html::a(Yii::t('app', 'UPDATE', 0), ['/admin/users/default/update', 'id' => Yii::$app->user->id]) ?></li>
                        <li><?= Html::a(Yii::t('app', 'ADMIN_PANEL'), ['/admin/users/default/update', 'id' => Yii::$app->user->id]) ?></li>
                        <li role="separator" class="divider"></li>
                        <li><?= Html::a(Yii::t('app', 'LOGOUT'), ['/admin/auth/logout']) ?></li>


                    </ul>
                </li> 
            </ul>
        </div><!--/.nav-collapse -->

    </nav>
</div>