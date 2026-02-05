<?php
namespace api\services;

use backend\models\Icon;
use backend\models\Order;
use backend\models\ServiceOrder;
use backend\models\UserGoods;
use common\components\Helper;
use Yii;

class OrderQueryService
{
    /**
     * 构建订单查询
     * @param array $params 查询参数
     * @return array 包含查询对象和分页数据的数组
     */
    public static function buildQuery($params = [])
    {
        $query = Order::find();

        // 用户ID筛选
        if (isset($params['user_id'])) {
            $query->andWhere(['user_id' => $params['user_id']]);
        }
        //订单状态筛选
        if(isset($params['status'])){
            $query->andWhere(['status' => $params['status']]);
        }
        //几个月内
        if(isset($params['time']) and $params['time']>0){
            $start_time=strtotime('-'.$params['time'].' months');
            $query->andWhere(['>=', 'created_at',$start_time]);
        }

        if(isset($params['keywords'])){
            // 使用joinWith搜索产品名称 - 注意这里是通过$query对象调用
            $query->joinWith(['detail' => function($query2) use ($params) {
                $query2->andWhere(['or',['like', 'detail.title', $params['keywords']],['like','detail.order_number', $params['keywords']]]);
            }]);
        }

        return $query;
    }

    /**
     * 获取排序条件
     * @param int $sortType 排序类型
     * @return string 排序字符串
     */
    public static function getSortValue($sortType)
    {
        $sortMap = [
            1 => 'id DESC',
            2 => 'id ASC',
        ];

        return $sortMap[$sortType] ?? 'id DESC';
    }

    /**
     * 执行订单查询
     * @param array $params 查询参数
     * @return array 查询结果
     */
    public static function searchModel($params_data = [])
    {
        $params=[];
        foreach ($params_data as $k=>$v){
            //空的参数默认为全部,所以去除筛选
            if($v){
                $params[$k] = $v;
            }
        }

        $sortType = $params['sort'] ?? 1;
        $page = $params['page'] ?? 1;
        $pageSize = $params['page_number'] ?? 10;

        // 构建查询
        $query = self::buildQuery($params);

        // 计算分页
        $totalCount = $query->count();
        $totalPage = ceil($totalCount / $pageSize);
        $offset = ($page - 1) * $pageSize;

        // 执行查询
        $models = $query
            ->orderBy(self::getSortValue($sortType))
            ->offset($offset)
            ->limit($pageSize)
            ->all();
        $data=[];
        foreach ($models as $k=>$v){

            $goods=[];
            foreach ($v->detail as $k1=>$v1){
                $goods[]=[
                    'title'=>$v1['title'],
                    'image'=>Helper::setImg($v1['image']),
                    'goods_id'=>$v1['goods_id'],
                    'sku_value'=>$v1['sku_value'],
                    'number'=>$v1['number'],
                    'price'=>$v1['price'],
                ];
            }
            $end_time=0;
            if($v->status==1){
                $end_time=$v->created_at+15*60-time();
                if($end_time<0){
                    $end_time=0;
                }
            }
            $data[]=[
                'order_id' => $v->id,
                'order_number'=>$v->order_number,
                'status'=>$v->status,
                'status_message'=>Order::$status[$v->status],
                'goods'=>$goods,
                'end_time'=>$end_time,
                'total_price'=>$v->total_price,
            ];
        }

        return [
            'model' => $data,
            'pagination' => [
                'total_count' => $totalCount,
                'total_page' => $totalPage,
                'current_page' => $page,
                'page_size' => $pageSize
            ]
        ];
    }


    //获取单条数据
    public static function get_one($id)
    {
        $order = Order::findOne($id);
        $goods=[];
        foreach ($order->detail as $k1=>$v1){
            $goods[]=[
                'title'=>$v1['title'],
                'image'=>Helper::setImg($v1['image']),
                'goods_id'=>$v1['goods_id'],
                'sku_value'=>$v1['sku_value'],
                'number'=>$v1['number'],
                'price'=>$v1['price'],
            ];
        }
        $end_time=0;
        if($order->status==1){
            $end_time=$order->created_at+15*60-time();
            if($end_time<0){
                $end_time=0;
            }
        }
        $detail = [
            'order_id' => $order->id,
            'type'=>$order->type,
            'order_number' => $order->order_number,
            'create_time'=>date('Y-m-d H:i',$order->created_at),
            'status' => $order->status,
            'status_message'=>Order::$status[$order->status],
            'contact'=>$order->consignee,
            'phone'=>$order->phone,
            'content'=>$order->content,
            'goods'=>$goods,
            'province'=>$order->province,
            'city'=>$order->city,
            'area'=>$order->area,
            'address'=>$order->address_detail,
            'freight'=>$order->freight,
            'pay_price'=>$order->pay_price,
            'total_price'=>$order->total_price,
            'yh_money'=>$order->yh_money,
            'integral'=>$order->integral,
            'pay_time'=>$order->paid_time>0?date('Y-m-d H:i:s',$order->paid_time):'',
            'delivery_time'=>$order->delivery_time>0?date('Y-m-d H:i:s',$order->delivery_time):'',
            'confirm_time'=>$order->confirm_time>0?date('Y-m-d H:i:s',$order->confirm_time):'',
            'pay_method'=>$order->pay_method,
            'pay_method_message'=>Order::$pay_method[$order->pay_method],
            'end_time'=>$end_time,




        ];
        return $detail;

    }
}
