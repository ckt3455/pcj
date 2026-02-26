<?php
/**
 * 退款API接口测试脚本
 * 使用方法：php test-refund-api.php
 */

// 模拟API请求
function testApi($url, $data = [], $method = 'POST') {
    echo "测试接口: {$url}\n";
    echo "请求数据: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
    echo "--------------------------------------------------\n";
    
    // 这里只是模拟，实际需要发送HTTP请求
    return [
        'success' => true,
        'message' => '接口测试通过（模拟）',
        'data' => [
            'url' => $url,
            'method' => $method,
            'params' => $data
        ]
    ];
}

echo "=== 订单退款API接口测试 ===\n\n";

// 1. 测试获取选项接口
echo "1. 测试获取退款选项接口\n";
$result = testApi('/order/order-refund/options', []);
print_r($result);
echo "\n";

// 2. 测试检查订单接口
echo "2. 测试检查订单是否可以退款\n";
$result = testApi('/order/order-refund/check', [
    'order_sn' => '202502261500001'
]);
print_r($result);
echo "\n";

// 3. 测试申请退款接口
echo "3. 测试申请退款接口\n";
$result = testApi('/order/order-refund/apply', [
    'order_sn' => '202502261500001',
    'type' => 1,
    'name' => '张三',
    'mobile' => '13800138000',
    'money' => 199.00,
    'reason' => 2,
    'content' => '商品有质量问题',
    'image' => 'image1.jpg,image2.jpg',
    'goods_status' => 1
]);
print_r($result);
echo "\n";

// 4. 测试获取退款列表
echo "4. 测试获取退款列表\n";
$result = testApi('/order/order-refund/list', [
    'state' => 0,
    'page' => 1,
    'page_size' => 10
]);
print_r($result);
echo "\n";

// 5. 测试获取退款详情
echo "5. 测试获取退款详情\n";
$result = testApi('/order/order-refund/detail', [
    'refund_sn' => '202502261500001'
]);
print_r($result);
echo "\n";

// 6. 测试取消退款
echo "6. 测试取消退款申请\n";
$result = testApi('/order/order-refund/cancel', [
    'refund_id' => 123
]);
print_r($result);
echo "\n";

// 7. 测试填写物流信息
echo "7. 测试填写退货物流信息\n";
$result = testApi('/order/order-refund/fill-express', [
    'refund_id' => 123,
    'express_name' => '顺丰速运',
    'express_number' => 'SF1234567890'
]);
print_r($result);
echo "\n";

echo "=== 测试完成 ===\n";

// 显示API接口列表
echo "\n=== 可用的退款API接口 ===\n";
$apis = [
    'POST /order/order-refund/options' => '获取退款选项',
    'POST /order/order-refund/check' => '检查订单是否可以退款',
    'POST /order/order-refund/apply' => '申请退款',
    'POST /order/order-refund/list' => '获取退款列表',
    'POST /order/order-refund/detail' => '获取退款详情',
    'POST /order/order-refund/cancel' => '取消退款申请',
    'POST /order/order-refund/fill-express' => '填写退货物流信息',
];

foreach ($apis as $api => $desc) {
    echo "{$api} - {$desc}\n";
}

echo "\n=== 退款状态说明 ===\n";
$statuses = [
    1 => '待审核',
    2 => '审核通过',
    3 => '审核拒绝',
    4 => '退款中',
    5 => '退款成功',
    6 => '退款失败',
];

foreach ($statuses as $code => $text) {
    echo "{$code}: {$text}\n";
}

echo "\n=== 退款类型说明 ===\n";
$types = [
    1 => '仅退款',
    2 => '退货退款',
];

foreach ($types as $code => $text) {
    echo "{$code}: {$text}\n";
}
?>