<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Users".
 *
 * @property integer $id
 * @property string $name
 * @property string $password
 *
 * @property Dirs[] $dirs
 * @property Files[] $files
 */
class Users extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'password'], 'required'],
            [['name', 'password'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '帳號',
            'password' => '密碼',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirs()
    {
        return $this->hasMany(Dirs::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(Files::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return self::findone($id);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::findone(['name' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
}
