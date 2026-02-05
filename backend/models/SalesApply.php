<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%sales_apply}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $sales_id
 * @property float|null $money
 * @property float|null $fee_money
 * @property float|null $get_money
 * @property string|null $content
 * @property int|null $status
 * @property int|null $payment
 * @property string|null $account_info
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class SalesApply extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sales_apply}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['money', 'fee_money', 'get_money', 'content', 'account_info'], 'default', 'value' => null],
            [['updated_at'], 'default', 'value' => 0],
            [['payment'], 'default', 'value' => 1],
            [['user_id', 'sales_id', 'status', 'payment', 'created_at', 'updated_at'], 'integer'],
            [['money', 'fee_money', 'get_money'], 'number'],
            [['content', 'account_info'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'sales_id' => 'Sales ID',
            'money' => 'Money',
            'fee_money' => 'Fee Money',
            'get_money' => 'Get Money',
            'content' => 'Content',
            'status' => 'Status',
            'payment' => 'Payment',
            'account_info' => 'Account Info',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static $payment_message=[
        1=>'微信',
        2=>'支付宝',
        3=>'银行卡'
    ];


    public static $status_message=[
        1=>'待审核',
        2=>'审核通过',
        3=>'审核不通过'
    ];


    /**
     * 发起提现申请
     * @param array $data 提现数据
     * $data=['money'=>10,'payment'=>1]
     * @param int $userId 用户ID
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public function apply($data, $sales_id)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if($data['money']<0 or  $data['money'] % 10!=0){
                throw new \Exception('提现金额必须为10的倍数');
            }
            // 1. 获取用户信息并锁定行
            $sales = Sales::findOne($sales_id);
            if (!$sales) {
                throw new \Exception('用户不存在');
            }

            // 2. 验证余额是否足够
            if ($sales->money < $data['money']) {
                throw new \Exception('余额不足');
            }

            // 3. 生成提现单号
            $withdrawalCode = $this->generateWithdrawalCode();


            // 5. 创建提现记录
            $withdrawal = new SalesApply();
            $withdrawal->user_id = $sales['user_id'];
            $withdrawal->sales_id = $sales['id'];
            $withdrawal->money = $data['money'];
            $withdrawal->fee_money = $data['money']*0.1;
            $withdrawal->get_money=$withdrawal->money-$withdrawal->fee_money;
            $withdrawal->payment = $data['payment'];
            if($data['payment']==1){
                //微信提现
                if(!$sales['wx_number'] or !$sales['wx_name']){
                    throw new \Exception('请先填写提现信息');
                }
                $accountInfo='微信账号：'.$sales['wx_number'].'-'.'微信账户名：'.$sales['wx_name'];
            }else if($data['payment']==2){
                //支付宝提现
                if(!$sales['zfb_number'] or !$sales['zfb_name']){
                    throw new \Exception('请先填写提现信息');
                }
                $accountInfo='支付宝账号：'.$sales['zfb_number'].'-'.'支付宝账号账户名：'.$sales['zfb_name'];
            }elseif($data['payment']==3){
                if(!$sales['bank_number'] or !$sales['bank_name'] or !$sales['bank_kh']){
                    throw new \Exception('请先填写提现信息');
                }
                $accountInfo='银行卡号：'.$sales['bank_number'].'-'.'银行账户名：'.$sales['bank_name'].'-'.'开户行：'.$sales['bank_kh'];
            }else{
                throw new \Exception('发生错误');
            }


            $withdrawal->account_info = $accountInfo;
            $withdrawal->status =1;
            if (!$withdrawal->save()) {
                throw new \Exception('提现记录创建失败: ' . implode(', ', $withdrawal->getFirstErrors()));
            }

            $sales->money = $sales->money-$data['money'];

            if (!$sales->save()) {
                throw new \Exception('余额更新失败: ' . implode(', ', $sales->getFirstErrors()));
            }

            // 7. 记录资金变动日志
            $this->createMoneyLog([
                'user_id'=>$sales['user_id'],
                'number' => -$data['money'], // 负数表示支出
                'type' => 2, // 2表示提现
                'content' => "申请提现 {$data['money']} 元，提现单号：{$withdrawalCode}",
                'sales_id' => $sales->id,
            ]);

            $transaction->commit();

            // 8. 发送通知（可选）
//            $this->sendNotification($user, $withdrawal);

            return [
                'success' => true,
                'message' => '提现申请提交成功，请等待审核',
                'data' => [
                    'withdrawal_id' => $withdrawal->id,
                    'code' => $withdrawalCode,
                ]
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("提现申请失败: " . $e->getMessage(), 'withdrawal');

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * 生成提现单号
     */
    private function generateWithdrawalCode()
    {
        return 'TX' . date('YmdHis') . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * 创建资金变动记录
     */
    private function createMoneyLog($data)
    {
        $log = new SalesMoneyLog();
        $log->user_id = $data['user_id'];
        $log->number = $data['number'];
        $log->type = $data['type'];
        $log->content = $data['content'];
        $log->sales_id = $data['sales_id'];
        if (!$log->save()) {
            throw new \Exception('资金日志记录失败: ' . implode(', ', $log->getFirstErrors()));
        }

        return $log;
    }

    /**
     * 发送通知
     */
    private function sendNotification($user, $withdrawal)
    {
        // 发送给用户
        $userMessage = "您已成功提交提现申请{$withdrawal->money}元，提现单号：{$withdrawal->code}";
        // 这里可以实现短信、站内信等通知

        // 发送给管理员
        $adminMessage = "用户 {$user->name} 提交了{$withdrawal->money}元的提现申请，请及时处理";
        // 这里可以实现管理员通知
    }

    /**
     * 处理提现申请（管理员操作）
     */
    public function processWithdrawal($withdrawalId, $status, $adminId, $remark = '')
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $withdrawal = Withdrawal::findOne($withdrawalId);
            if (!$withdrawal) {
                throw new \Exception('提现记录不存在');
            }

            if ($withdrawal->status != Withdrawal::STATUS_PENDING) {
                throw new \Exception('该提现申请已处理，不能重复操作');
            }

            $user = User::findOne($withdrawal->user_id);
            if (!$user) {
                throw new \Exception('用户不存在');
            }

            $withdrawal->status = $status;
            $withdrawal->admin_remark = $remark;
            $withdrawal->processed_at = time();
            $withdrawal->processed_by = $adminId;
            $withdrawal->updated_at = time();

            if (!$withdrawal->save()) {
                throw new \Exception('提现记录更新失败');
            }

            // 如果是拒绝，返还金额
            if ($status == Withdrawal::STATUS_REJECTED) {
                $user->money = bcadd($user->money, $withdrawal->amount, 2);
                if (!$user->save()) {
                    throw new \Exception('余额返还失败');
                }

                // 记录返还日志
                $this->createMoneyLog([
                    'user_id' => $user->id,
                    'number' => $withdrawal->amount,
                    'type' => 3, // 3表示提现返还
                    'content' => "提现申请被拒绝，返还金额 {$withdrawal->amount} 元",
                    'sales_id' => $user->sales_id ?? 0,
                ]);
            }

            // 如果是已打款，记录打款日志
            if ($status == Withdrawal::STATUS_PAID) {
                $this->createMoneyLog([
                    'user_id' => $user->id,
                    'number' => -$withdrawal->amount,
                    'type' => 4, // 4表示提现成功扣款
                    'content' => "提现成功，实际打款 {$withdrawal->amount} 元",
                    'sales_id' => $user->sales_id ?? 0,
                ]);
            }

            $transaction->commit();

            // 发送处理结果通知
            $this->sendProcessNotification($user, $withdrawal, $status);

            return ['success' => true, 'message' => '操作成功'];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * 发送处理结果通知
     */
    private function sendProcessNotification($user, $withdrawal, $status)
    {
        $statusText = $withdrawal->getStatusText();
        $message = "您的提现申请{$withdrawal->amount}元（单号：{$withdrawal->code}）{$statusText}";

        if ($withdrawal->admin_remark) {
            $message .= "，备注：{$withdrawal->admin_remark}";
        }

        // 发送通知给用户
        // 实现通知逻辑
    }

}
