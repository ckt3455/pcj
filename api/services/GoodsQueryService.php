<?php
namespace api\services;
use backend\models\Goods;
use backend\models\GoodsSpec;
use common\components\Helper;
use Yii;
use yii\db\ActiveQuery;

class GoodsQueryService
{
    /**
     * 构建订单查询
     * @param array $params 查询参数
     * @return array 包含查询对象和分页数据的数组
     */
    public static function buildQuery($params = [])
    {
        $query = Goods::find()->where(['type'=>1,'status'=>1]);
        if(isset($params['category_id'])){
            $query->andWhere(['category_id' => $params['category_id']]);
        }
        if(isset($params['keywords'])){
            $query->andWhere(['like', 'title', $params['keywords']]);
        }
        if(isset($params['hot'])){
            $query->andWhere(['like', 'hot', $params['hot']]);
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
        $data_goods=[];
        foreach ($models as $k=>$v){

            $data_goods[]=[
                'goods_id'=>$v->id,
                'title'=>$v->title,
                'price'=>$v->price,
                'sales'=>$v->sales,
                'crossed_price'=>$v->crossed_price,
                'image'=>Helper::setImg($v['thumb']),
            ];
        }
        return [
            'goods' => $data_goods,
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

        $goods=Goods::findOne($id);
        if($goods->has_option==1){
            $sku=$goods->getSpecData();
        }else{
            $sku=[];
        }
        $detail = [
            'goods_id' => $goods->id,
            'title'=>$goods->title,
            'price'=>$goods->price,
            'crossed_price'=>$goods->crossed_price,
            'sales'=>$goods->sales,
            'image'=>Helper::setImg($goods['thumb']),
            'has_option'=>$goods->has_option,
            'sku'=>$sku,
            'user_money'=>0,
            'intro'=>$goods->intro,
            'fahuo_message'=>'下单后7天内发货',
            'service_message'=>'支持7天无理由退货',
            'active_message'=>'暂无活动',
            'content'=>Helper::imageUrl($goods->content,Yii::$app->request->hostInfo),


        ];
        return $detail;

    }
}
