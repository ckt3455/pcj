# 提货申请功能 - 安装使用说明

## 已完成的工作

### 1. 数据库迁移文件
- 位置：`console/migrations/m260226_120000_pick_order.php`
- 创建两个表：
  - `pick_order` - 提货订单主表
  - `pick_order_detail` - 提货订单详情表

### 2. Model 模型
- `backend/models/PickOrder.php` - 提货订单模型
- `backend/models/PickOrderDetail.php` - 提货订单详情模型

### 3. Search 搜索
- `backend/search/PickOrderSearch.php` - 后台列表搜索

### 4. Controller 控制器
- `backend/controllers/PickOrderController.php`
- **后台管理功能：**
  - `actionIndex` - 列表页
  - `actionView` - 详情页
  - `actionAudit` - 审核
  - `actionConfirmPick` - 确认提货
  - `actionCancel` - 取消订单
  - `actionDelete` - 删除
- **API 接口：**
  - `api-create` - 创建提货订单
  - `api-list` - 获取订单列表
  - `api-view` - 获取订单详情
  - `api-audit` - 审核订单
  - `api-confirm-pick` - 确认提货
  - `api-cancel` - 取消订单
  - `api-goods-list` - 获取商品列表
  - `api-address-list` - 获取地址列表

### 5. Views 视图
- `backend/views/pick-order/index.php` - 列表页
- `backend/views/pick-order/view.php` - 详情页
- `backend/views/pick-order/audit.php` - 审核页

### 6. API 文档
- 位置：`docs/pick-order-api.md`
- 格式：Markdown（可导入 Apifox）

---

## 安装步骤

### 步骤 1：运行数据库迁移

确保 MySQL 服务已启动，然后执行：

```bash
cd E:\www\pcj
php yii migrate/up --migrationPath=@console/migrations
```

或者直接在 MySQL 中执行 SQL（如果迁移无法运行）：

```sql
-- 提货订单主表
CREATE TABLE `pick_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pick_number` varchar(50) NOT NULL COMMENT '提货单号',
  `user_id` int(11) NOT NULL COMMENT '用户 ID',
  `address_id` int(11) NOT NULL COMMENT '地址 ID',
  `consignee` varchar(50) DEFAULT NULL COMMENT '收货人',
  `phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `province` varchar(50) DEFAULT NULL COMMENT '省',
  `city` varchar(50) DEFAULT NULL COMMENT '市',
  `area` varchar(50) DEFAULT NULL COMMENT '区',
  `address_detail` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `total_amount` decimal(10,2) DEFAULT '0.00' COMMENT '总金额',
  `status` smallint(6) DEFAULT '1' COMMENT '状态：1=待审核 2=待提货 3=已提货 4=已取消',
  `content` text COMMENT '备注',
  `audit_time` int(11) DEFAULT NULL COMMENT '审核时间',
  `audit_user_id` int(11) DEFAULT NULL COMMENT '审核人',
  `pick_time` int(11) DEFAULT NULL COMMENT '提货时间',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间',
  `is_delete` smallint(6) DEFAULT '0' COMMENT '是否删除 0=否 1=是',
  PRIMARY KEY (`id`),
  KEY `idx_pick_number` (`pick_number`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提货订单表';

-- 提货订单详情表
CREATE TABLE `pick_order_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pick_order_id` int(11) NOT NULL COMMENT '提货订单 ID',
  `pick_number` varchar(50) NOT NULL COMMENT '提货单号',
  `goods_id` int(11) NOT NULL COMMENT '商品 ID',
  `sku_id` int(11) DEFAULT '0' COMMENT 'SKU ID',
  `goods_name` varchar(255) NOT NULL COMMENT '商品名称',
  `goods_image` varchar(255) DEFAULT NULL COMMENT '商品图片',
  `sku_name` varchar(255) DEFAULT NULL COMMENT '规格名称',
  `quantity` int(11) NOT NULL COMMENT '数量',
  `price` decimal(10,2) DEFAULT NULL COMMENT '单价',
  `subtotal` decimal(10,2) DEFAULT NULL COMMENT '小计',
  PRIMARY KEY (`id`),
  KEY `idx_pick_order_id` (`pick_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提货订单详情表';
```

### 步骤 2：配置后台菜单

在后台管理系统中添加菜单项：

- **菜单名称：** 提货订单管理
- **路由：** `/pick-order/index`
- **图标：** 可选

### 步骤 3：导入 API 文档到 Apifox

1. 打开 Apifox
2. 选择项目 → 导入数据
3. 选择 `docs/pick-order-api.md` 文件
4. 或者手动创建接口，参考文档内容

---

## API 使用示例

### 创建提货订单

```javascript
// 请求
POST /backend/pick-order/api-create
Content-Type: application/json

{
    "user_id": 1,
    "address_id": 1,
    "items": {
        "1": 2,
        "3": 1
    },
    "content": "请尽快发货"
}

// 响应
{
    "error": 0,
    "pick_order_id": 123,
    "message": ""
}
```

### 获取订单列表

```javascript
// 请求
GET /backend/pick-order/api-list?page=1&page_size=20&status=1

// 响应
{
    "error": 0,
    "data": {
        "list": [...],
        "total": 100,
        "page": 1,
        "page_size": 20
    }
}
```

### 审核订单

```javascript
// 请求
POST /backend/pick-order/api-audit
Content-Type: application/json

{
    "id": 123,
    "status": 2
}

// 响应
{
    "error": 0,
    "message": "审核成功"
}
```

---

## 订单状态流程

```
┌─────────────┐
│  待审核 (1)  │
└──────┬──────┘
       │
   ┌───┴───┐
   ↓       ↓
┌─────┐  ┌─────┐
│待提货│  │已取消│
│ (2) │  │ (4) │
└──┬──┘  └─────┘
   │
   ↓
┌─────────┐
│ 已提货 (3)│
└─────────┘
```

---

## 注意事项

1. **库存检查：** 创建订单时会自动检查商品库存
2. **取消限制：** 只有待审核和待提货状态的订单可以取消
3. **提货单号：** 格式为 `P` + 时间戳 + 随机数，确保唯一性
4. **金额精度：** 所有金额字段保留 2 位小数

---

## 文件清单

```
E:\www\pcj\
├── console\migrations\
│   └── m260226_120000_pick_order.php    # 数据库迁移
├── backend\
│   ├── models\
│   │   ├── PickOrder.php                # 提货订单模型
│   │   └── PickOrderDetail.php          # 提货订单详情模型
│   ├── search\
│   │   └── PickOrderSearch.php          # 搜索模型
│   ├── controllers\
│   │   └── PickOrderController.php      # 控制器
│   └── views\pick-order\
│       ├── index.php                    # 列表页
│       ├── view.php                     # 详情页
│       └── audit.php                    # 审核页
└── docs\
    └── pick-order-api.md                # API 文档
```
