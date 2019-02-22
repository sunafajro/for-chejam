<?php

namespace app\models;

use Yii;

class Commit extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{commits}}';
    }

    public function rules()
    {
        return [
            [['sha', 'files'], 'required'],
            [['sha'], 'string', 'max' => 256],
            [['files'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'sha' => 'Идентификатор',
            'files' => 'Файлы',
        ];
    }
}