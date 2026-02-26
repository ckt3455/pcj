# 提货申请 API 文档

## 基本信息

- **小程序接口 Base URL:** `http://your-domain.com/api/pick-order`
- **后台管理 Base URL:** `http://your-domain.com/backend/pick-order`
- **数据格式:** JSON
- **字符编码:** UTF-8

---

## 一、小程序接口 (api/controllers/PickOrderController.php)

所有小程序接口需要 token 认证，POST 请求。

### 1. 创建提货订单

**接口地址:** `POST /create`

**请求参数:**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| token | string | 是 | 用户登录 token |
| address_id | integer | 是 | 地址 ID |
| items | object | 是 | 商品列表 {商品 ID: 数量} |
| content | string | 否 | 备注 |

**请求示例:**
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "address_id": 1,
    "items": {"1": 2, "3": 1},
    "content": "请尽快发货"
}
```

**响应示例:**
```json
{
    "code": 0,
    "message": "提货申请提交成功",
    "data": {"pick_order_id": 123}
}
```

---

### 2. 提货订单列表

**接口地址:** `POST /list`

**请求参数:**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| token | string | 是 | 用户登录 token |
| page | integer | 否 | 页码，默认 1 |
| page_size | integer | 否 | 每页数量，默认 20 |
| status | integer | 否 | 状态筛选 |

**响应示例:**
```json
{
    "code": 0,
    "message": "请求成功",
    "data": {
        "list": [
            {
                "id": 123,
                "pick_number": "P202602261200001234",
                "consignee": "张三",
                "phone": "13800138000",
                "address": "广东省深圳市南山区详细地址",
                "total_amount": "199.00",
                "status": 1,
                "status_text": "待审核",
                "created_at": "2026-02-26 12:00:00"
            }
        ],
        "total": 100,
        "page": 1,
        "page_size": 20
    }
}
```

---

### 3. 提货订单详情

**接口地址:** `POST /detail`

**请求参数:**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| token | string | 是 | 用户登录 token |
| id | integer | 是 | 订单 ID |

**响应示例:**
```json
{
    "code": 0,
    "message": "请求成功",
    "data": {
        "id": 123,
        "pick_number": "P202602261200001234",
        "consignee": "张三",
        "phone": "13800138000",
        "province": "广东省",
        "city": "深圳市",
        "area": "南山区",
        "address_detail": "科技园南区",
        "total_amount": "199.00",
        "status": 1,
        "status_text": "待审核",
        "content": "请尽快发货",
        "created_at": "2026-02-26 12:00:00",
        "items": [
            {
                "goods_id": 1,
                "goods_name": "测试商品",
                "goods_image": "http://domain.com/uploads/goods/1.jpg",
                "quantity": 2,
                "price": "99.50",
                "subtotal": "199.00"
            }
        ]
    }
}
```

---

### 4. 取消提货订单

**接口地址:** `POST /cancel`

**请求参数:**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| token | string | 是 | 用户登录 token |
| id | integer | 是 | 订单 ID |

**响应示例:**
```json
{
    "code": 0,
    "message": "取消成功",
    "data": []
}
```

---

### 5. 获取可提货商品列表

**接口地址:** `POST /goods-list`

**请求参数:**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| token | string | 是 | 用户登录 token |

**响应示例:**
```json
{
    "code": 0,
    "message": "请求成功",
    "data": {
        "list": [
            {
                "id": 1,
                "title": "测试商品",
                "price": "99.50",
                "thumb": "http://domain.com/uploads/goods/1.jpg",
                "stock": 100,
                "has_option": 0
            }
        ]
    }
}
```

---

### 6. 获取用户地址列表

**接口地址:** `POST /address-list`

**请求参数:**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| token | string | 是 | 用户登录 token |

**响应示例:**
```json
{
    "code": 0,
    "message": "请求成功",
    "data": {
        "list": [
            {
                "id": 1,
                "provinces": "广东省",
                "city": "深圳市",
                "area": "南山区",
                "content": "科技园南区",
                "user": "张三",
                "phone": "13800138000",
                "is_default": 1,
                "full_address": "广东省深圳市南山区科技园南区"
            }
        ]
    }
}
```

---

## 二、后台管理接口 (backend/controllers/PickOrderController.php)

### 1. 创建提货订单

**接口地址:** `POST /api-create`

**请求参数:** user_id, address_id, items, content

---

### 2. 订单列表

**接口地址:** `GET /api-list?page=1&page_size=20&status=1`

---

### 3. 订单详情

**接口地址:** `GET /api-view?id=123`

---

### 4. 审核订单

**接口地址:** `POST /api-audit`

**请求参数:**

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| id | integer | 是 | 订单 ID |
| status | integer | 是 | 2=通过，4=拒绝 |

---

### 5. 确认提货

**接口地址:** `POST /api-confirm-pick`

**请求参数:** id

---

### 6. 取消订单

**接口地址:** `POST /api-cancel`

**请求参数:** id

---

## 三、订单状态说明

| 状态 | 说明 |
|------|------|
| 1 | 待审核 |
| 2 | 待提货 |
| 3 | 已提货 |
| 4 | 已取消 |

---

## 四、业务流程

```
用户提交申请 (status=1)
        ↓
管理员审核
   ↙         ↘
通过 (status=2)  拒绝 (status=4)
   ↓
用户提货
   ↓
管理员确认 (status=3)
```

---

## 五、文件清单

```
E:\www\pcj\
├── api\controllers\PickOrderController.php      # 小程序接口
├── backend\controllers\PickOrderController.php  # 后台接口
├── backend\models\PickOrder.php                 # 订单模型
├── backend\models\PickOrderDetail.php           # 订单详情模型
├── backend\search\PickOrderSearch.php           # 搜索模型
├── backend\views\pick-order\                    # 后台视图
└── console\migrations\m260226_120000_pick_order.php  # 数据库迁移
```
