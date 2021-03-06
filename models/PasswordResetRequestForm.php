<?php

namespace balitrip\user\models;

use yii\base\Model;
use Yii;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => 'balitrip\user\models\User',
                //'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('user', 'USER_EMAIL'),
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'email' => $this->email,
        ]);

        if ($user && $user->status == User::STATUS_BLOCKED) {
            Yii::$app->session->setFlash('error', Yii::t('user','ERROR_PROFILE_BLOCKED'));
        }
        elseif ($user) {

            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                return \Yii::$app->mailer->compose('@balitrip/user/mail/passwordResetToken', ['user' => $user])
                    ->setFrom([Yii::$app->params['adminEmail'] => 'robot'])
                    ->setTo($this->email)
                    ->setSubject(Yii::t('user','EMAIL_TITLE_PASSWORD_RESET',['sitename'=>\Yii::$app->name]))
                    ->send();
            }
        }

        return false;
    }
}
