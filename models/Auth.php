<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth".
 *
 * @property string|null $module
 * @property string|null $controller
 * @property string|null $action
 * @property int|null $user_id
 */
class Auth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['module', 'controller', 'action'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'module' => 'Module',
            'controller' => 'Controller',
            'action' => 'Action',
            'user_id' => 'User ID',
        ];
    }
}
