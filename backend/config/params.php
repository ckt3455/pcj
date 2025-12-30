<?php
return [
    /**-------------------总管理员配置-------------------**/
    'adminAccount'           => 1,//系统管理员账号id
    'adminEmail'             => '751393839@qq.com',

    /**-------------------后台网站基础配置-------------------**/
    'siteTitle'              => "后台系统",      //后台系统名称
    'abbreviation'           => "后台",              //缩写
    'acronym'                => "ceshi",                       //拼音缩写
    'AK'=>'qrbLzhHHjKSO3s0j3EvyK-XUXcmac2qUFcue7764',//七牛云密匙AK
    'SK'=>'7ZKF6qS26Iw4PopuZO8TPQrgKVB4vk9M9z3UruvD',//七牛云密匙SK

    /**-------------------备份配置配置-------------------**/
    'dataBackupPath'              => Yii::getAlias('@rootPath') . '/data/backup',   //数据库备份根路径
    'dataBackPartSize'            => 20971520,                                      //数据库备份卷大小
    'dataBackCompress'            => 1,                                             //压缩级别
    'dataBackCompressLevel'       => 9,                                             //数据库备份文件压缩级别
    'dataBackLock'                => 'backup.lock',                                 //数据库备份缓存文件名

    /**-------------------配置管理类型-------------------**/
    'configTypeList'       => [
        '1'   => [
            'id'   => 1,
            'title' => "文本框"
        ],
        '2'   => [
            'id'   => 2,
            'title' => "密码框"
        ],
        '3'   => [
            'id'   => 3,
            'title' => "文本域"
        ],
        '4'   => [
            'id'   => 4,
            'title' => "下拉文本框"
        ],
        '5'   => [
            'id'   => 5,
            'title' => "单选按钮"
        ],
        '6'   => [
            'id'   => 6,
            'title' => "富文本编辑器"
        ],
        '7'   => [
            'id'   => 7,
            'title' => "图片上传"
        ],
        '8'=>[
            'id'=>8,
            'title'=>'日期'
        ],
    ],


    /**-------------------配置管理分组-------------------**/
    'configGroupList'       => [
        '1'   => [
            'id'   => 1,
            'title' => "基本配置"
        ],
        '2'=>[
            'id'=>2,
            'title'=>'参数设置'
        ],

    ],







];
