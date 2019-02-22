<?php

namespace app\models;

use Yii;

class File extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'files';
    }

    public function rules()
    {
        return [
            [['filename', 'commits', 'authors'], 'required'],
            [['commits'], 'integer'],
            [['filename', 'authors'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'filename' => 'Файл',
            'commits' => 'Количество коммитов',
            'authors' => 'Авторы',
        ];
    }
}