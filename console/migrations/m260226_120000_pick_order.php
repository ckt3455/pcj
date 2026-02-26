<?php

use yii\db\Migration;

/**
 * 提货申请表
 */
class m260226_120000_pick_order extends Migration
{
    public function safeUp()
    {
        // 提货订单主表
        $this->createTable('{{%pick_order}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'pick_number' => $this->string(50)->notNull()->comment('提货单号'),
            'user_id' => $this->integer()->notNull()->comment('用户 ID'),
            'address_id' => $this->integer()->notNull()->comment('地址 ID'),
            'consignee' => $this->string(50)->comment('收货人'),
            'phone' => $this->string(20)->comment('联系电话'),
            'province' => $this->string(50)->comment('省'),
            'city' => $this->string(50)->comment('市'),
            'area' => $this->string(50)->comment('区'),
            'address_detail' => $this->string(255)->comment('详细地址'),
            'total_amount' => $this->decimal(10, 2)->defaultValue(0)->comment('总金额'),
            'status' => $this->smallInteger()->defaultValue(1)->comment('状态：1=待审核 2=待提货 3=已提货 4=已取消'),
            'content' => $this->text()->comment('备注'),
            'audit_time' => $this->integer()->comment('审核时间'),
            'audit_user_id' => $this->integer()->comment('审核人'),
            'pick_time' => $this->integer()->comment('提货时间'),
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer()->comment('更新时间'),
            'is_delete' => $this->smallInteger()->defaultValue(0)->comment('是否删除 0=否 1=是'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=提货订单表');

        // 提货订单详情表
        $this->createTable('{{%pick_order_detail}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'pick_order_id' => $this->integer()->notNull()->comment('提货订单 ID'),
            'pick_number' => $this->string(50)->notNull()->comment('提货单号'),
            'goods_id' => $this->integer()->notNull()->comment('商品 ID'),
            'sku_id' => $this->integer()->defaultValue(0)->comment('SKU ID'),
            'goods_name' => $this->string(255)->notNull()->comment('商品名称'),
            'goods_image' => $this->string(255)->comment('商品图片'),
            'sku_name' => $this->string(255)->comment('规格名称'),
            'quantity' => $this->integer()->notNull()->comment('数量'),
            'price' => $this->decimal(10, 2)->comment('单价'),
            'subtotal' => $this->decimal(10, 2)->comment('小计'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=提货订单详情表');

        // 创建索引
        $this->createIndex('idx_pick_number', '{{%pick_order}}', 'pick_number');
        $this->createIndex('idx_user_id', '{{%pick_order}}', 'user_id');
        $this->createIndex('idx_status', '{{%pick_order}}', 'status');
        $this->createIndex('idx_pick_order_id', '{{%pick_order_detail}}', 'pick_order_id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%pick_order_detail}}');
        $this->dropTable('{{%pick_order}}');
    }
}
