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
        $data_order=[];
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
        $order = ServiceOrder::findOne($id);
        $goods=UserGoods::findOne($order->goods_id);
        $end_days = $goods->end_days;
        $image=[];
        if($order->image){
            $arr_image=explode(',',$order->image);
            foreach ($arr_image as $k=>$v){
                $image[]=Helper::setImg($v);
            }
        }
        $worker=[];
        if($order->worker_id>0){
            $worker=[
                'worker_name'=>$order->worker_name,
                'worker_image'=>Helper::setImg($order->worker_image),
                'worker_phone'=>$order->worker_phone,
                'worker_time'=>date('Y-m-d H:i',$order->worker_time),

            ];
        }
        if($order->status==3 and $order->is_evaluate==1){
            $is_evaluate=1;
        }else{
            $is_evaluate=0;
        }
        $jx_message=[];
        $hj_message=[];

        if($order->wx_type==2 and $order->status==1){
            $is_jx=1;
        }else{
            $is_jx=0;
        }
        $detail = [
            'service_order_id' => $order->id,
            'type'=>$order->type,
            'title' => $order->title,
            'order_number' => $order->order_number,
            'date' => date('Y/m/d',$order->date),
            'create_time'=>date('Y-m-d H:i',$order->created_at),
            'status' => $order->status,
            'time' => $order->time,
            'status_message'=>ServiceOrder::$status_message[$order->status],
            'contact'=>$order->contact,
            'phone'=>$order->phone,
            'goods_name'=>$order->goods_name,
            'goods_code'=>$order->goods_code,
            'goods_image'=>Helper::setImg($order->goods_image),
            'end_days'=>$end_days,
            'image'=>$image,
            'content'=>$order->content,
            'detail'=>$order->detail,
            'wx_type'=>$order->wx_type,
            'is_evaluate'=>$is_evaluate,
            'worker'=>$worker,
            'sz_order_number'=>$order->sz_order_number,
            'jx_express'=>$order->jx_express,
            'jx_express_number'=>$order->jx_express_number,
            'jx_express_image'=>Helper::setImg($order->jx_express_image),
            'hj_express'=>$order->hj_express,
            'hj_express_number'=>$order->hj_express_number,
            'jx_message'=>$jx_message,
            'hj_message'=>$hj_message,
            'is_jx'=>$is_jx



        ];
        return $detail;

    }
}
