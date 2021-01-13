<?php

namespace app\models;
use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    // public $id;
    // public $username;
    // public $password;
    // public $authKey;
    // public $accessToken;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    // private static $users = [
    //     '100' => [
    //         'id' => '100',
    //         'username' => 'admin',
    //         'password' => 'admin',
    //         'authKey' => 'test100key',
    //         'accessToken' => '100-token',
    //     ],
    //     '101' => [
    //         'id' => '101',
    //         'username' => 'demo',
    //         'password' => 'demo',
    //         'authKey' => 'test101key',
    //         'accessToken' => '101-token',
    //     ],
    // ];

    public function setPassword($password)
{
    $this->password_hash = Yii::$app->security->generatePasswordHash($password);
}

public function generateAuthKey()
{
    $this->auth_key = Yii::$app->security->generateRandomString();
}


public static function tableName()
    {
        return '{{%user}}';
    }
    
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        // return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // foreach (self::$users as $user) {
        //     if ($user['accessToken'] === $token) {
        //         return new static($user);
        //     }
        // }

        // return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        // foreach (self::$users as $user) {
        //     if (strcasecmp($user['username'], $username) === 0) {
        //         return new static($user);
        //     }
        // }

        // return null;
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);

    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();

    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;

    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        // return $this->password === $password;
        return Yii::$app->security->validatePassword($password, $this->password_hash);

    }
    public function generatePasswordResetToken()
{
    $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
}

public function removePasswordResetToken()
{
    $this->password_reset_token = null;
}

}
