<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\grid\GridView;
    use kartik\date\DatePicker;

    $this->title = 'My Yii Application';
?>

<div class="site-index">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="row">
        <div class="col-xs-12 col-sm-3">
            <?= $form->field($model, 'github_url')->textInput() ?>
        </div>
        <div class="col-xs-12 col-sm-3">
            <?= $form->field($model, 'date_start')->widget(DatePicker::classname(), [
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]) ?>
        </div>
        <div class="col-xs-12 col-sm-3">
            <?= $form->field($model, 'date_end')->widget(DatePicker::classname(), [
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]) ?>
        </div>
        <div class="col-xs-12 col-sm-3" style="padding-top: 25px">
            <?= Html::submitButton('Get!', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <div>
        <?= GridView::widget([
            'dataProvider' => $provider,
        ]); ?>
    </div>
</div>
