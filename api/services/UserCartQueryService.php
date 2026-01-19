<?php
namespace api\services;
use backend\models\Goods;
use backend\models\GoodsOption;
use backend\models\GoodsSpec;
use backend\models\UserCart;
use common\components\Helper;
use Yii;
use yii\db\ActiveQuery;

class UserCartQueryService
{
    /**
     * 构建订单查询
     * @param array $params 查询参数
     * @return array 包含查询对象和分页数据的数组
     */
    public static function buildQuery($params = [])
    {
        $query = UserCart::find()->where(['user_id'=>$params['user_id']]);

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
            $goods=Goods::findOne($v->goods_id);
            if($v->sku_id>0){
                $sku=GoodsOption::findOne($v->sku_id);
                if($goods and $sku){
                    $data[]=[
                        'cart_id'=>$v->id,
                        'title'=>$goods->title,
                        'price'=>$sku->price,
                        'crossed_price'=>$sku->crossed_price,
                        'image'=>Helper::setImg($goods['thumb']),
                        'status'=>$goods->status,
                        'number'=>$v->number,
                        'sku_title'=>$sku->title,
                    ];
                }
            }else{
                if($goods){
                    $data[]=[
                        'cart_id'=>$v->id,
                        'title'=>$goods->title,
                        'price'=>$goods->price,
                        'crossed_price'=>$goods->crossed_price,
                        'image'=>Helper::setImg($goods['thumb']),
                        'status'=>$goods->status,
                        'number'=>$v->number,
                        'sku_title'=>''
                    ];
                }
            }



        }
        return [
            'data' => $data,
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
            'image'=>Helper::setImg($goods['thumb']),
            'has_option'=>$goods->has_option,
            'sku'=>$sku


        ];
        return $detail;

    }
}
