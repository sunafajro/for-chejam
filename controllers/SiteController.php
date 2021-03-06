<?php

namespace app\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\GithubFilter;
use app\models\GithubForm;
use app\models\ContactForm;
use app\models\LoginForm;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new GithubForm();
        $result = [];
        if ($model->load(Yii::$app->request->get())) {
            $url = str_replace('https://github.com/', '', $model->github_url);
            $params = explode('/', $url);
            if (count($params) === 2) {
                $commits = GithubForm::getGithubRepoCommits($params[0], $params[1], $model->date_start, $model->date_end);
                if (is_array($commits) && !empty($commits)) {
                    $result = GithubForm::getGithubRepoCommitsFiles($params[0], $params[1], $commits);
                }
            }
        }
        $resultData = [];
        $filterModel = new GithubFilter();
        if ($filterModel->load(Yii::$app->request->get())) {
            $resultData = $filterModel->search($result, Yii::$app->request->get('GithubFilter'));
        } else {
            $resultData = $result;
        }
        
        $provider = new ArrayDataProvider([
            'allModels' => $resultData,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['filename', 'commits', 'authors'],
            ],
        ]);

        return $this->render('index', [
            'model' => $model,
            'provider' => $provider,
            'filterModel' => $filterModel
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
