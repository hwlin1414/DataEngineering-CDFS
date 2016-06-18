<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Dirs".
 *
 * @property integer $id
 * @property string $name
 * @property integer $user_id
 * @property integer $dir_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Users $user
 * @property Dirs $dir
 * @property Dirs[] $dirs
 * @property Files[] $files
 */
class Dirs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Dirs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'user_id'], 'required'],
            [['user_id', 'dir_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['dir_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dirs::className(), 'targetAttribute' => ['dir_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        return [
            'create' => ['name', 'user_id'],
            'update' => ['name'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '目錄名稱',
            'user_id' => 'User ID',
            'dir_id' => 'Dir ID',
            'created_at' => '新增時間',
            'updated_at' => '修改時間',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDir()
    {
        return $this->hasOne(Dirs::className(), ['id' => 'dir_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirs()
    {
        return $this->hasMany(Dirs::className(), ['dir_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(Files::className(), ['dir_id' => 'id']);
    }

    public function getPath()
    {
        return $this->getParent($this->name);
    }

    public function getParent($path = '')
    {
        $model = $this;
        while($model = $model->dir) $path = $model->name . '/' . $path;
        return $path;
    }
}
