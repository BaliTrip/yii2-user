<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('user', 'Редактирование роли: ') . ' ' . $role->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Управление ролями'), 'url' => ['role']];
$this->params['breadcrumbs'][] = Yii::t('user', 'Редактирование');
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="links-form">

        <?php
        if (!empty($error)) {
            ?>
            <div class="error-summary">
                <?php
                echo implode('<br>', $error);
                ?>
            </div> 
        <?php
        }
        ?>

        <?php $form = ActiveForm::begin(); ?>

        <div class="form-group">
            <?= Html::label(Yii::t('user', 'Название роли')); ?>
            <?= Html::textInput('name', $role->name); ?>
        </div>

        <div class="form-group">
            <?= Html::label(Yii::t('user', 'Текстовое описание')); ?>
            <?= Html::textInput('description', $role->description); ?>
        </div>

        <div class="form-group">
            <?= Html::label(Yii::t('user', 'Разрешенные доступы')); ?>
            <?= Html::checkboxList('permissions', $role_permit, $permissions, ['separator' => '<br>']); ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('user', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
