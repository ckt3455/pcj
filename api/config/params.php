<?php
return [
    'adminEmail' => 'admin@example.com',

    'imagesUpload' => [
        'imgMaxSize'    => 5242880,//图片最大上传大小,默认2M
        'imgMaxExc'     => [".png", ".jpg", ".jpeg", ".gif", ".bmp",".webp"],//
        'imgPath'       => 'user_image/',//图片创建路径
        'imgThumbPath'  => 'thumb/',//图片创建缩略图路径
        'imgSubName'    => 'Y/m-d',//图片上传子目录规则
        'imgPrefix'     => 'img_',//图片名称前缀
        // 压缩配置
        'compress' => [
            'enabled' => true, // 是否启用压缩
            'maxWidth' => 1920, // 最大宽度
            'maxHeight' => 1080, // 最大高度
            'quality' => 85, // JPEG质量 (1-100)
            'pngQuality' => 9, // PNG压缩级别 (0-9)
            'webpQuality' => 80, // WebP质量 (1-100)
            'minFileSize' => 102400, // 100KB以下不压缩
            'types' => ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.webp'], // 支持压缩的类型
        ],

        // 缩略图配置
        'thumbnail' => [
            'enabled' => false, // 是否生成缩略图
            'width' => 300, // 缩略图宽度
            'height' => 200, // 缩略图高度
            'quality' => 80, // JPEG质量
            'suffix' => '_thumb', // 缩略图后缀
            'types' => ['.jpg', '.jpeg', '.png'], // 生成缩略图的类型
        ],
    ],
];
