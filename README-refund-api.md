# 订单退款API集成指南

## 概述

基于现有的`order_refund`表，我们开发了一套完整的订单退款API接口，支持小程序商城的退款功能。本文档说明如何集成和使用这些API。

## 文件结构

```
E:\www\pcj\
├── api\
│   ├── modules\order\controllers\
│   │   └── OrderRefundController.php      # 退款API控制器（已更新）
│   ├── services\order\
│   │   └── OrderRefundService.php         # 退款API服务类（新增）
│   └── config\main.php                    # API配置
├── backend\models\
│   └── OrderRefund.php                    # 退款数据模型（已存在）
├── common\services\order\
│   └── OrderRefundCommonService.php       # 退款公共服务（已存在）
├── docs\
│   ├── order-refund-api.md               # API接口文档
│   └── refund-api-example.js             # 前端调用示例
└── test-refund-api.php                   # API测试脚本
```

## 安装步骤

### 1. 检查现有文件
确保以下文件已存在：
- `backend/models/OrderRefund.php` - 退款数据模型
- `common/services/order/OrderRefundCommonService.php` - 退款公共服务

### 2. 添加新文件
复制以下新文件到项目：
- `api/services/order/OrderRefundService.php` - 退款API服务类
- 更新 `api/modules/order/controllers/OrderRefundController.php` - 退款API控制器

### 3. 配置路由
确保order模块已正确配置。检查`api/config/main.php`中的路由配置。

## API接口列表

### 用户端接口
1. `POST /order/order-refund/options` - 获取退款选项
2. `POST /order/order-refund/check` - 检查订单是否可以退款
3. `POST /order/order-refund/apply` - 申请退款
4. `POST /order/order-refund/list` - 获取退款列表
5. `POST /order/order-refund/detail` - 获取退款详情
6. `POST /order/order-refund/cancel` - 取消退款申请
7. `POST /order/order-refund/fill-express` - 填写退货物流信息

### 管理端接口（已存在）
- `OrderRefund::auditRefund()` - 审核退款申请
- `OrderRefund::processRefund()` - 处理退款
- `OrderRefund::fillExpress()` - 填写物流信息

## 数据库要求

### order_refund表结构
确保数据库中有`order_refund`表，包含以下字段：

```sql
CREATE TABLE `order_refund` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT '订单id',
  `user_id` int(11) DEFAULT NULL COMMENT '用户',
  `type` int(11) DEFAULT NULL COMMENT '类型',
  `status` int(11) DEFAULT NULL COMMENT '状态',
  `goods_status` int(11) DEFAULT NULL COMMENT '收货状态',
  `reason` int(11) DEFAULT NULL COMMENT '理由',
  `money` decimal(10,2) DEFAULT NULL COMMENT '金额',
  `content` text COMMENT '内容',
  `image` text COMMENT '图片',
  `express_name` varchar(255) DEFAULT NULL COMMENT '快递名称',
  `express_number` varchar(255) DEFAULT NULL COMMENT '快递单号',
  `created_at` int(11) DEFAULT NULL COMMENT '添加时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间',
  `detail_id` int(11) DEFAULT '0' COMMENT '订单详情ID',
  `message` text COMMENT '备注',
  `order_number` varchar(255) DEFAULT NULL COMMENT '订单编号',
  `contact` varchar(50) DEFAULT NULL COMMENT '联系人',
  `mobile` varchar(50) DEFAULT NULL COMMENT '电话',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单退款表';
```

## 集成示例

### 前端集成（小程序）

1. **引入API服务**
```javascript
// 在项目中创建 services/refund.js
import { request } from './request';

export const refundService = {
  // 获取退款选项
  getOptions: () => request('/order/order-refund/options', 'POST'),
  
  // 申请退款
  applyRefund: (data) => request('/order/order-refund/apply', 'POST', data),
  
  // 获取退款列表
  getRefundList: (params) => request('/order/order-refund/list', 'POST', params),
  
  // 其他接口...
};
```

2. **页面中使用**
```javascript
Page({
  data: {
    refundList: []
  },
  
  onLoad() {
    this.loadRefundList();
  },
  
  async loadRefundList() {
    try {
      const res = await refundService.getRefundList({
        page: 1,
        page_size: 10
      });
      this.setData({ refundList: res.data.list });
    } catch (error) {
      wx.showToast({ title: '加载失败', icon: 'error' });
    }
  },
  
  // 申请退款
  async handleApplyRefund() {
    const params = {
      order_sn: '202502261500001',
      type: 1,
      name: '张三',
      mobile: '13800138000',
      money: 199.00,
      reason: 2,
      content: '商品质量问题'
    };
    
    try {
      const res = await refundService.applyRefund(params);
      wx.showToast({ title: '申请成功', icon: 'success' });
    } catch (error) {
      wx.showToast({ title: error, icon: 'error' });
    }
  }
});
```

### 后端集成

1. **调用退款服务**
```php
// 在控制器中调用退款服务
use api\services\order\OrderRefundService;

class SomeController extends Controller
{
    public function actionTestRefund()
    {
        // 申请退款
        $result = OrderRefundService::Apply([
            'order_sn' => '202502261500001',
            'type' => 1,
            'name' => '张三',
            'mobile' => '13800138000',
            'money' => 199.00,
            'reason' => 2
        ]);
        
        return $this->asJson($result);
    }
}
```

2. **后台管理集成**
```php
// 审核退款申请
use backend\models\OrderRefund;

$result = OrderRefund::auditRefund($refundId, 2, '审核通过');
if ($result['error'] == 0) {
    echo '审核成功';
} else {
    echo '审核失败: ' . $result['message'];
}

// 处理退款
$result = OrderRefund::processRefund($refundId, 5, '退款成功');
```

## 测试API

### 使用测试脚本
```bash
cd E:\www\pcj
php test-refund-api.php
```

### 手动测试
1. 启动项目服务器
2. 使用Postman或类似工具测试API
3. 测试顺序：
   - 先获取选项：`POST /order/order-refund/options`
   - 检查订单：`POST /order/order-refund/check`
   - 申请退款：`POST /order/order-refund/apply`
   - 查看列表：`POST /order/order-refund/list`

## 注意事项

### 1. 权限控制
- 所有用户端API需要用户登录（携带token）
- 管理端API需要管理员权限

### 2. 数据验证
- 退款金额不能超过订单支付金额
- 一个订单只能有一个处理中的退款申请
- 只有特定状态的订单可以申请退款

### 3. 状态管理
- 退款状态变更需要遵循流程：
  待审核 → 审核通过/拒绝 → 退款中 → 退款成功/失败
- 退货退款需要填写物流信息

### 4. 图片上传
- 图片需要先上传到文件服务器
- 多个图片用逗号分隔
- 支持jpg、png格式

### 5. 错误处理
- 所有API返回标准格式：`{error: 0, message: 'success', data: {...}}`
- 错误时返回：`{error: 1, message: '错误信息'}`
- HTTP状态码：200（成功）、400（参数错误）、401（未登录）、404（资源不存在）

## 常见问题

### Q1: 接口返回"未登录"错误
A: 确保请求头中包含正确的token：`Authorization: Bearer {token}`

### Q2: 申请退款时提示"订单状态不允许退款"
A: 只有状态为2（待发货）、3（待收货）、4（已完成）、5（已评价）的订单可以申请退款

### Q3: 退款金额验证失败
A: 退款金额必须大于0且不超过订单支付金额

### Q4: 无法取消退款申请
A: 只有状态为"待审核"的退款申请可以取消

### Q5: 无法填写物流信息
A: 只有"退货退款"类型且状态为"审核通过"的退款申请可以填写物流信息

## 扩展功能

### 1. 退款原因自定义
可以在`OrderRefund`模型中修改`$reason`数组，添加自定义退款原因。

### 2. 退款通知
可以集成消息通知系统，在退款状态变更时发送通知给用户。

### 3. 退款统计
可以添加统计接口，统计退款数量、金额等数据。

### 4. 导出功能
可以添加退款记录导出功能，支持Excel、CSV格式。

## 支持与反馈

如有问题或建议，请联系开发团队。

---

**文档版本**: 1.0  
**最后更新**: 2026-02-26  
**适用版本**: Yii2小程序商城系统