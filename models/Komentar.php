<?php
namespace app\models;

class Komentar extends \yii\base\model
{
    public $nama;
    public $pesan;

    public function rules()
    {
        return[
            [['nama','pesan'],'required'],
            
        ];
    }
}