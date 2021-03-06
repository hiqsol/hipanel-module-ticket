<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\ticket\models\Thread;
use hiqdev\combo\StaticCombo;
use hiqdev\xeditable\widgets\ComboXEditable;
use hiqdev\xeditable\widgets\XEditable;
use yii\widgets\ActiveForm;

/**
 * @var Thread
 */
?>

<?php $form = ActiveForm::begin([
    'action' => $action,
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'leave-comment-form'],
]) ?>

<!-- Status -->

<?php if (!$model->isNewRecord) : ?>
    <ul class="list-group ticket-list-group">
        <li class="list-group-item">
                <span class="badge">
                    <?php if ($model->state === Thread::STATE_CLOSE) : ?>
                        <span class="label label-default"><?= Yii::t('hipanel:ticket', 'Closed') ?></span>
                    <?php else : ?>
                        <span class="label label-success"><?= Yii::t('hipanel:ticket', 'Opened') ?></span>
                    <?php endif; ?>
                </span>
            <?= $model->getAttributeLabel('status') ?>
            <div class="clearfix"></div>
        </li>
    </ul>
<?php endif; ?>

<!-- Topics -->
<?php if ($model->isNewRecord) : ?>
    <?= $form->field($model, 'topics')->widget(StaticCombo::class, [
        'hasId' => true,
        'data' => $topic_data,
        'multiple' => true,
    ]) ?>
<?php else : ?>
    <ul class="list-group ticket-list-group">
        <li class="list-group-item">
                <span class="badge">
                    <?= XEditable::widget([
                        'model' => $model,
                        'attribute' => 'topics',
                        'pluginOptions' => [
                            'disabled' => !Yii::$app->user->can('support'),
                            'type' => 'checklist',
                            'source' => $model->xFormater($topic_data),
                            'placement' => 'bottom',
                            'emptytext' => Yii::t('hipanel:ticket', 'Empty'),
                        ],
                    ]) ?>
                </span>
            <?= $model->getAttributeLabel('topics') ?>
            <div class="clearfix"></div>
        </li>
    </ul>
<?php endif ?>
<div class="clearfix"></div>
<!-- Priority -->
<?php if (Yii::$app->user->can('support')) : ?>
    <?php if ($model->isNewRecord) : ?>
        <?php $model->priority = 'medium' ?>
        <?= $form->field($model, 'priority')->widget(StaticCombo::class, [
            'data' => $priority_data,
            'hasId' => true,
        ]) ?>
    <?php else : ?>
        <ul class="list-group ticket-list-group">
            <li class="list-group-item">
                <span class="badge">
                    <?= XEditable::widget([
                        'model' => $model,
                        'attribute' => 'priority',
                        'pluginOptions' => [
                            'disabled' => !Yii::$app->user->can('support'),
                            'type' => 'select',
                            'source' => $priority_data,
                        ],
                    ]) ?>
                </span>
                <?= $model->getAttributeLabel('priority') ?>
            </li>
        </ul>
    <?php endif ?>
<?php endif; ?>

<?php if (Yii::$app->user->can('support')) : ?>
    <?php if ($model->isNewRecord) : ?>
        <?php $model->responsible_id = Yii::$app->user->id ?>
        <!-- Responsible -->
        <?= $form->field($model, 'responsible')->widget(ClientCombo::class, [
            'clientType' => $model->getResponsibleClientTypes(),
        ]) ?>
    <?php else : ?>
        <ul class="list-group ticket-list-group">
            <li class="list-group-item">
                <span class="badge">
                    <?= ComboXEditable::widget([
                        'model' => $model,
                        'attribute' => 'responsible',
                        'combo' => [
                            'class' => ClientCombo::class,
                            'clientType' => $model->getResponsibleClientTypes(),
                            'inputOptions' => [
                                'class' => 'hidden',
                            ],
                            'pluginOptions' => [
                                'select2Options' => [
                                    'width' => '20rem',
                                ],
                            ],
                        ],
                        'pluginOptions' => [
                            'placement' => 'bottom',
                        ],
                    ]) ?>
                </span>
                <?= $model->getAttributeLabel('responsible') ?>
            </li>
        </ul>

        <ul class="list-group ticket-list-group">
            <li class="list-group-item">
                <span class="badge"><?= Yii::$app->formatter->asDuration($model->spent * 60) ?></span>
                <?= Yii::t('hipanel:ticket', 'Spent time') ?>
            </li>
        </ul>
    <?php endif ?>

    <!-- Watchers -->
    <?php if (Yii::$app->user->can('support')) : ?>
        <?php if ($model->isNewRecord) : ?>
            <?= $form->field($model, 'watchers')->widget(ClientCombo::class, [
                'clientType' => $model->getResponsibleClientTypes(),
                'pluginOptions' => [
                    'select2Options' => [
                        'multiple' => true,
                    ],
                ],
            ]) ?>
        <?php endif ?>
    <?php endif ?>
    <?php if ($model->isNewRecord) : ?>
        <?php $model->recipient_id = Yii::$app->user->identity->id ?>
        <?= $form->field($model, 'recipient_id')->widget(ClientCombo::class) ?>
    <?php endif ?>
<?php endif ?>

<?php $form->end() ?>
