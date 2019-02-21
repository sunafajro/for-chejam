<?php

namespace app\models;

use Yii;
use yii\base\Model;
use linslin\yii2\curl;

class GithubForm extends Model
{
    public $github_url;
    public $date_start;
    public $date_end;

    public function rules()
    {
        return [
            [['github_url', 'date_start', 'date_end'], 'required'],
            [['date_start', 'date_end'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'github_url' => 'Адрес репозитория',
            'date_start' => 'Начало выборки',
            'date_end' => 'Конец выборки',
        ];
    }

    public static function getGithubRepoCommits($username = null, $reponame = null, $since = null, $until = null)
    {
        if (!($username && $reponame && $since && $until)) {
            return null;
        }
        $result = [];
        $curl = new curl\Curl();
        $response = $curl->setGetParams([
            'branch' => 'master',
            'since' => date('Y-m-d\TH:i:s\Z', strtotime($since . ' 00:00:00')),
            'until' => date('Y-m-d\TH:i:s\Z', strtotime($until . ' 23:59:59'))
        ])->setHeaders([
            'Authorization' => 'token ' . Yii::$app->params['github_token']
        ])->get('https://api.github.com/repos/' . $username . '/' . $reponame . '/commits');
        if ($curl->errorCode === null) {
            $raw_result = json_decode($response);
            if (is_array($raw_result) && !empty($raw_result)) {
                foreach ($raw_result as $r) {
                    if ($r) {
                        $result[] = [
                            'sha' => $r->sha,
                            'author' => $r->commit->author->name,
                            'author_email' => $r->commit->author->email
                        ];
                    }
                }
            }
        } else {
            // TODO Error Handling $curl->errorCode
        }
        return $result;
    }

    public static function getGithubRepoCommitsFiles($username = null, $reponame = null, $commits = [])
    {
        if (!($username && $reponame && is_array($commits) && !empty($commits))) {
            return null;
        }
        $result = [];
        foreach ($commits as $c) {
            $curl = new curl\Curl();
            $response = $curl->setHeaders([
                'Authorization' => 'token ' . Yii::$app->params['github_token']
            ])->get('https://api.github.com/repos/' . $username . '/' . $reponame . '/commits/' . $c['sha']);
            if ($curl->errorCode === null) {
                $raw_result = json_decode($response);
                if ($raw_result && property_exists($raw_result, 'files')) {
                    foreach ($raw_result->files as $r) {
                        $id = $r->filename;
                        if (!isset($result[$id])) {
                            $result[$id] = [
                                'filename' => $r->filename,
                                'commits' => 1,
                                'authors' => [
                                    $c['author_email'] => [
                                        'name' => $c['author'],
                                        'email' => $c['author_email'],
                                        'count' => 1
                                    ]
                                ]
                            ];                        
                        } else {
                            $result[$id]['commits'] += 1;
                            if (!isset($result[$id]['authors'][$c['author_email']])) {
                                $result[$id]['authors'][$c['author_email']] = [
                                    'name' => $c['author'],
                                    'email' => $c['author_email'],
                                    'count' => 1
                                ];
                            } else {
                                $result[$id]['authors'][$c['author_email']]['count'] += 1;
                            }
                        }
                    }
                }
            } else {
                // TODO Error Handling $curl->errorCode
            }
        }
        $preparedResult = [];
        if (is_array($result) && !empty($result)) {
            foreach ($result as $r) {
                $authors = [];
                foreach ($r['authors'] as $a) {
                    $authors[] = $a['name'] . ' <' . $a['email'] . '> [' . $a['count'] . ']';
                }
                $preparedResult[] = [
                    'filename' => $r['filename'],
                    'commits' => $r['commits'],
                    'authors' => implode('; ', $authors)
                ];
            }
        }
        return $preparedResult;
    }
}