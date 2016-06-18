<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Cloud Distributed Filesystem',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'nav-left',
        ],
    ]);
    //$items = Yii::$app->user->isGuest ? ([
    //        ['label' => 'Login', 'url' => ['/site/login']]
    //    ]) : ([
    //        ['label' => 'Home', 'url' => ['/drive']],
    //        ['label' => 'Logout (' . Yii::$app->user->identity->name . ')', 'url' => ['/site/logout']],
    //    ]);
    $items = Yii::$app->user->isGuest ? ([
        ]) : ([
            ['label' => 'Logout (' . Yii::$app->user->identity->name . ')', 'url' => ['/site/logout']],
        ]);

    echo Nav::widget([
        'options' => ['class' => 'nav-right nav-menu'],
        'items' => $items,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="content is-pulled-left">&copy; Data Engineering <?= date('Y') ?></p>

        <p class="content is-pulled-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
