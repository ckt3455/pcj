# 订单退款API接口文档

## 接口概览

基于现有的`order_refund`表，新增了完整的退款API接口，支持小程序商城的退款功能。

## 数据库表结构

### order_refund表字段说明

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | int | 主键ID |
| order_id | int | 订单ID |
| user_id | int | 用户ID |
| type | int | 退款类型：1=仅退款，2=退货退款 |
| status | int | 退款状态：1=待审核，2=审核通过，3=审核拒绝，4=退款中，5=退款成功，6=退款失败 |
| goods_status | int | 商品状态：1=未收到货，2=已收到货 |
| reason | int | 退款原因：1=不想要了，2=商品质量问题，3=商品与描述不符，4=商品损坏，5=其他 |
| money | decimal | 退款金额 |
| content | text | 退款说明 |
| image | text | 凭证图片（多个用逗号分隔） |
| express_name | varchar | 快递公司 |
| express_number | varchar | 快递单号 |
| created_at | int | 创建时间 |
| updated_at | int | 更新时间 |
| detail_id | int | 订单详情ID |
| message | text | 备注（审核/处理备注） |
| order_number | varchar | 订单编号 |
| contact | varchar | 联系人 |
| mobile | varchar | 联系电话 |

## API接口列表

### 1. 获取退款选项
- **接口**: `/order/order-refund/options`
- **方法**: POST
- **说明**: 获取退款类型、原因、状态等选项
- **返回**:
```json
{
  "error": 0,
  "message": "success",
  "data": {
    "types": {"1": "仅退款", "2": "退货退款"},
    "reasons": {"1": "不想要了", "2": "商品质量问题", "3": "商品与描述不符", "4": "商品损坏", "5": "其他"},
    "goods_statuses": {"1": "未收到货", "2": "已收到货"},
    "statuses": {"1": "待审核", "2": "审核通过", "3": "审核拒绝", "4": "退款中", "5": "退款成功", "6": "退款失败"}
  }
}
```

### 2. 检查订单是否可以退款
- **接口**: `/order/order-refund/check`
- **方法**: POST
- **参数**:
  - `order_sn` (string, 必填): 订单编号
- **返回**:
```json
{
  "error": 0,
  "message": "success",
  "data": {
    "can_refund": true,
    "order_status": 3,
    "order_status_text": "待收货",
    "pay_price": 199.00,
    "has_refund": false,
    "message": "可以申请退款"
  }
}
```

### 3. 申请退款
- **接口**: `/order/order-refund/apply`
- **方法**: POST
- **参数**:
  - `order_sn` (string, 必填): 订单编号
  - `type` (int, 必填): 退款类型（1=仅退款，2=退货退款）
  - `name` (string, 必填): 联系人
  - `mobile` (string, 必填): 联系电话
  - `money` (decimal, 必填): 退款金额
  - `reason` (int, 选填): 退款原因（默认1）
  - `content` (string, 选填): 退款说明
  - `image` (string, 选填): 凭证图片（多个用逗号分隔）
  - `goods_status` (int, 选填): 商品状态（默认1）
- **返回**:
```json
{
  "error": 0,
  "message": "退款申请提交成功",
  "data": {
    "refund_id": 123,
    "order_number": "202502261500001"
  }
}
```

### 4. 获取退款列表
- **接口**: `/order/order-refund/list`
- **方法**: POST
- **参数**:
  - `state` (int, 选填): 状态筛选（0=全部）
  - `page` (int, 选填): 页码（默认1）
  - `page_size` (int, 选填): 每页数量（默认20）
- **返回**:
```json
{
  "error": 0,
  "message": "success",
  "data": {
    "list": [
      {
        "id": 123,
        "order_id": 456,
        "order_number": "202502261500001",
        "type": 1,
        "type_text": "仅退款",
        "status": 1,
        "status_text": "待审核",
        "reason": 2,
        "reason_text": "商品质量问题",
        "money": 199.00,
        "content": "商品有瑕疵",
        "created_at": 1708934400,
        "created_at_text": "2024-02-26 15:30:00",
        "contact": "张三",
        "mobile": "13800138000"
      }
    ],
    "total": 1,
    "page": 1,
    "page_size": 20,
    "total_page": 1
  }
}
```

### 5. 获取退款详情
- **接口**: `/order/order-refund/detail`
- **方法**: POST
- **参数**:
  - `refund_sn` (string, 必填): 退款编号（即order_number）
- **返回**:
```json
{
  "error": 0,
  "message": "success",
  "data": {
    "refund": {
      "id": 123,
      "order_id": 456,
      "order_number": "202502261500001",
      "type": 1,
      "type_text": "仅退款",
      "status": 1,
      "status_text": "待审核",
      "reason": 2,
      "reason_text": "商品质量问题",
      "money": 199.00,
      "content": "商品有瑕疵",
      "images": ["image1.jpg", "image2.jpg"],
      "created_at": 1708934400,
      "created_at_text": "2024-02-26 15:30:00",
      "contact": "张三",
      "mobile": "13800138000"
    },
    "order": {
      "id": 456,
      "order_number": "202502261500001",
      "pay_price": 199.00,
      "status": 4
    }
  }
}
```

### 6. 取消退款申请
- **接口**: `/order/order-refund/cancel`
- **方法**: POST
- **参数**:
  - `refund_id` (int, 必填): 退款ID
- **返回**:
```json
{
  "error": 0,
  "message": "退款申请已取消"
}
```

### 7. 填写退货物流信息
- **接口**: `/order/order-refund/fill-express`
- **方法**: POST
- **参数**:
  - `refund_id` (int, 必填): 退款ID
  - `express_name` (string, 必填): 快递公司
  - `express_number` (string, 必填): 快递单号
- **返回**:
```json
{
  "error": 0,
  "message": "物流信息填写成功"
}
```

## 退款流程

### 仅退款流程
1. 用户提交退款申请 → 状态：待审核（1）
2. 管理员审核 → 通过：状态：审核通过（2）→ 退款处理 → 状态：退款成功（5）
3. 管理员审核 → 拒绝：状态：审核拒绝（3）

### 退货退款流程
1. 用户提交退款申请 → 状态：待审核（1）
2. 管理员审核通过 → 状态：审核通过（2）
3. 用户填写退货物流信息
4. 管理员确认收货 → 状态：退款中（4）
5. 退款处理完成 → 状态：退款成功（5）

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 0 | 成功 |
| 400 | 参数错误或业务逻辑错误 |
| 401 | 未登录 |
| 404 | 资源不存在 |

## 注意事项

1. 所有接口都需要用户登录（携带token）
2. 退款金额不能超过订单支付金额
3. 一个订单只能有一个处理中的退款申请
4. 只有待审核状态的退款申请可以取消
5. 只有退货退款类型且审核通过状态可以填写物流信息
6. 图片上传需要先调用文件上传接口，获取图片URL后再传入

## 后台管理接口

后台管理相关的审核、处理接口已在`backend\models\OrderRefund.php`中实现：
- `OrderRefund::auditRefund()` - 审核退款申请
- `OrderRefund::processRefund()` - 处理退款
- `OrderRefund::fillExpress()` - 填写物流信息（用户端也可调用）

## 文件说明

1. `api\modules\order\controllers\OrderRefundController.php` - API控制器
2. `api\services\order\OrderRefundService.php` - API服务类
3. `backend\models\OrderRefund.php` - 数据模型（已存在）
4. `common\services\order\OrderRefundCommonService.php` - 公共服务类（已存在）