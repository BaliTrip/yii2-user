<?php

use worstinme\uikit\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\ChangePasswordForm */

$this->title = Yii::t('user', 'TITLE_CHANGE_PASSWORD');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'TITLE_PROFILE'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profile-change-password">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

        <?php $form = ActiveForm::begin(['layout'=>'stacked','field_width'=>'medium','field_size'=>'large']); ?>

        <?= $form->field($model, 'currentPassword')->passwordInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'newPassword')->passwordInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'newPasswordRepeat')->passwordInput(['maxlength' => true]) ?>

        <div class="uk-form-row">
            <?= Html::submitButton(Yii::t('user', 'BUTTON_SAVE'), ['class' => 'uk-button uk-button-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>