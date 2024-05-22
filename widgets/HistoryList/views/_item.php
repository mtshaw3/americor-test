<?php
use app\models\Call;
use app\models\Customer;
use app\models\Fax;
use app\models\Sms;
use app\models\Task;
use yii\helpers\Html;

/** @var $model HistorySearch */

switch ($model->event->name) {
    case Task::EVENT_CREATED_TASK:
    case Task::EVENT_COMPLETED_TASK:
    case Task::EVENT_UPDATED_TASK:
        $task = $model->task;

        echo $this->render('_item_common', [
            'user' => $model->user,
            'body' => $model->getHistoryBody(),
            'iconClass' => 'fa-check-square bg-yellow',
            'footerDatetime' => $model->ins_ts,
            'footer' => isset($task->customerCreditor->name) ? "Creditor: " . $task->customerCreditor->name : ''
        ]);
        break;
    case Sms::EVENT_INCOMING_SMS:
    case Sms::EVENT_OUTGOING_SMS:
        echo $this->render('_item_common', [
            'user' => $model->user,
            'body' => $model->getHistoryBody(),
            'footer' => $model->sms->direction == Sms::DIRECTION_INCOMING ?
                Yii::t('app', 'Incoming message from {number}', [
                    'number' => $model->sms->phone_from ?? ''
                ]) : Yii::t('app', 'Sent message to {number}', [
                    'number' => $model->sms->phone_to ?? ''
                ]),
            'iconIncome' => $model->sms->direction == Sms::DIRECTION_INCOMING,
            'footerDatetime' => $model->ins_ts,
            'iconClass' => 'icon-sms bg-dark-blue'
        ]);
        break;
    case Fax::EVENT_OUTGOING_FAX:
    case Fax::EVENT_INCOMING_FAX:
        $fax = $model->fax;

        echo $this->render('_item_common', [
            'user' => $model->user,
            'body' => $model->getHistoryBody() .
                ' - ' .
                (isset($fax->document) ? Html::a(
                    Yii::t('app', 'view document'),
                    $fax->document->getViewUrl(),
                    [
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]
                ) : ''),
            'footer' => Yii::t('app', '{type} was sent to {group}', [
                'type' => $fax ? $fax->getTypeText() : 'Fax',
                'group' => isset($fax->creditorGroup) ? Html::a($fax->creditorGroup->name, ['creditors/groups'], ['data-pjax' => 0]) : ''
            ]),
            'footerDatetime' => $model->ins_ts,
            'iconClass' => 'fa-fax bg-green'
        ]);
        break;
    case Customer::EVENT_CUSTOMER_CHANGE_TYPE:
        echo $this->render('_item_statuses_change', [
            'model' => $model,
            'oldValue' => Customer::getTypeTextByType($model->getDetailOldValue('type')),
            'newValue' => Customer::getTypeTextByType($model->getDetailNewValue('type'))
        ]);
        break;
    case Customer::EVENT_CUSTOMER_CHANGE_QUALITY:
        echo $this->render('_item_statuses_change', [
            'model' => $model,
            'oldValue' => Customer::getQualityTextByQuality($model->getDetailOldValue('quality')),
            'newValue' => Customer::getQualityTextByQuality($model->getDetailNewValue('quality')),
        ]);
        break;
    case Call::EVENT_INCOMING_CALL:
    case Call::EVENT_OUTGOING_CALL:
        /** @var Call $call */
        $call = $model->call;
        $answered = $call && $call->status == Call::STATUS_ANSWERED;

        echo $this->render('_item_common', [
            'user' => $model->user,
            'content' => $call->comment ?? '',
            'body' => $call ? $model->getHistoryBody() : '<i>Deleted</i> ',
            'footerDatetime' => $model->ins_ts,
            'footer' => isset($call->applicant) ? "Called <span>{$call->applicant->name}</span>" : null,
            'iconClass' => $answered ? 'md-phone bg-green' : 'md-phone-missed bg-red',
            'iconIncome' => $answered && $call->direction == Call::DIRECTION_INCOMING
        ]);
        break;
    default:
        echo $this->render('_item_common', [
            'user' => $model->user,
            'body' => $model->getHistoryBody(),
            'bodyDatetime' => $model->ins_ts,
            'iconClass' => 'fa-gear bg-purple-light'
        ]);
        break;
}
