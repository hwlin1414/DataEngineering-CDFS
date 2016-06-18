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
<nav class="nav has-shadow">
    <div class="container">
        <div class="nav-left">
            <?= Html::a('Cloud Distributed Filesystem', ['/site/index'], ['class' => 'tag is-info is-large']) ?>
        </div>

        <div class="nav-right nav-menu">
            <?php
                if( !Yii::$app->user->isGuest)
                    echo Html::a('Logout (' . Yii::$app->user->identity->name . ')', ['/site/logout'], ['class' => 'nav-item']);
            ?>
        </div>
    </div>
</nav>
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
