<?php

namespace balitrip\user\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;
    const STATUS_SOCIAL = 3;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => self::className(), 'message' => Yii::t('user', 'ERROR_USERNAME_EXISTS')],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::className(), 'message' => Yii::t('user', 'ERROR_EMAIL_EXISTS')],
            ['email', 'string', 'max' => 255],

            ['status', 'integer'],
            ['status', 'default', 'value' => self::STATUS_WAIT],
            ['status', 'in', 'range' => array_keys(self::getStatusesArray())],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['username', 'email', 'status'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => Yii::t('user', 'USER_CREATED'),
            'updated_at' => Yii::t('user', 'USER_UPDATED'),
            'username' => Yii::t('user', 'USER_USERNAME'),
            'email' => Yii::t('user', 'USER_EMAIL'),
            'status' => Yii::t('user', 'USER_STATUS'),
        ];
    }

    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }

    public function getStatusName()
    {
        $statuses = self::getStatusesArray();
        return isset($statuses[$this->status]) ? $statuses[$this->status] : '';
    }

    public static function getStatusesArray()
    {
        return [
            self::STATUS_BLOCKED => Yii::t('user', 'USER_STATUS_BLOCKED'),
            self::STATUS_ACTIVE => Yii::t('user', 'USER_STATUS_ACTIVE'),
            self::STATUS_WAIT => Yii::t('user', 'USER_STATUS_WAIT'),
            self::STATUS_SOCIAL => Yii::t('user', 'USER_STATUS_SOCIAL'),
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('findIdentityByAccessToken is not implemented.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {   
        
        if (($user = static::findOne(['username' => $username])) === null) {
            $user = static::findOne(['email' => $username]);
        }
        return $user;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @param string $email_confirm_token
     * @return static|null
     */
    public static function findByEmailConfirmToken($email_confirm_token)
    {
        return static::findOne(['email_confirm_token' => $email_confirm_token]);
    }

    /**
     * Generates email confirmation token
     */
    public function generateEmailConfirmToken()
    {
        $this->email_confirm_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Removes email confirmation token
     */
    public function removeEmailConfirmToken()
    {
        $this->email_confirm_token = null;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $expire = !empty(Yii::$app->params['user.passwordResetTokenExpire']) ? 
                    Yii::$app->params['user.passwordResetTokenExpire'] :
                    60*60*24;
                    
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);

        return $timestamp + $expire >= time();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateAuthKey();
            }
            return true;
        }
        return false;
    }
}
