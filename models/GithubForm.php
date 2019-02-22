<?php

namespace app\models;

use Yii;
use app\models\Commit;
use linslin\yii2\curl;
use yii\base\Model;

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
        $commit = null;
        foreach ($commits as $c) {
            $commit = Commit::find()->where(['sha' => $c['sha']])->one();
            $files = [];
            if ($commit === NULL) {
                $curl = new curl\Curl();
                $response = $curl->setHeaders([
                    'Authorization' => 'token ' . Yii::$app->params['github_token']
                ])->get('https://api.github.com/repos/' . $username . '/' . $reponame . '/commits/' . $c['sha']);
                if ($curl->errorCode === null) {
                    $commit = json_decode($response);
                    if ($commit && property_exists($commit, 'files')) {
                        foreach ($commit->files as $f) {
                            $files[] = [
                                'filename' => $f->filename
                            ];
                        }
                    }
                    $newCommit = new Commit();
                    $newCommit->sha = $c['sha'];
                    $newCommit->files = json_encode($files);
                    $newCommit->save();
                } else {
                    // TODO Error Handling $curl->errorCode
                }
            } else {
                $tmp_files = json_decode($commit->files);
                foreach ($tmp_files as $f) {
                    $files[] = [
                        'filename' => $f->filename
                    ];
                }
            }
            if ($commit && is_array($files) && !empty($files)) {
                foreach ($files as $r) {
                    $id = $r['filename'];
                    $files[] = $r['filename'];
                    if (!isset($result[$id])) {
                        $result[$id] = [
                            'filename' => $r['filename'],
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