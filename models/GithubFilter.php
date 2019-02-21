<?php

namespace app\models;

use Yii;
use yii\base\Model;

class GithubFilter extends Model
{
    public $filename;
    public $commits;
    public $authors;

    public function rules()
    {
        return [
            [['filename', 'commits', 'authors'], 'string']
        ];
    }

    public function search($data = [], $params = [])
    {
        $result = [];
        if ((is_array($data) && !empty($data)) && (is_array($params) && !empty($params))) {
            $enabledParams = 0;
            foreach($params as $p) {
                $enabledParams = $p ? $enabledParams + 1 : $enabledParams;
            }
            foreach ($data as $d) {
                $isTrue = 0;
                foreach ($params as $key => $val) {
                    if ($params[$key] && !(strpos($d[$key], $val) === false)) {
                        $isTrue += 1;
                    }
                }
                if ($isTrue === $enabledParams) {
                    $result[] = $d;
                }
            }
            return $result;
        } else {
            return $data;
        }
    }
}