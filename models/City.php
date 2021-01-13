<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property int|null $id
 * @property int|null $province_id
 * @property string|null $name
 * @property string|null $type
 * @property string|null $postal_code
 *
 * @property Province $province
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'province_id'], 'integer'],
            [['type'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['postal_code'], 'string', 'max' => 10],
            [['province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Province::className(), 'targetAttribute' => ['province_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'province_id' => 'Province ID',
            'name' => 'Name',
            'type' => 'Type',
            'postal_code' => 'Postal Code',
        ];
    }

    /**
     * Gets query for [[Province]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['id' => 'province_id']);
    }
}
