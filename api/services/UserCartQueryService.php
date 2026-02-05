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
 /**
 * 搜索模型数据并返回分页结果
 *
 * 该函数根据传入的参数构建查询条件，执行分页查询，并返回格式化的商品数据及分页信息。
 *
 * @param array $params_data 可选参数数组，用于筛选和排序查询条件。默认为空数组。
 *                           支持的键包括：
 *                           - sort: 排序类型（默认为1）
 *                           - page: 当前页码（默认为1）
 *                           - page_number: 每页显示数量（默认为10）
 *                           其他自定义筛选条件将被过滤掉空值后使用。
 *
 * @return array 返回包含以下结构的数据：
 *               - data: 商品列表，每个元素包含商品详细信息（如标题、价格、图片等）。
 *               - pagination: 分页信息，包括总记录数、总页数、当前页码和每页大小。
 */
public static function searchModel($params_data = [])
{
    // 初始化参数数组，过滤掉空值
    $params = [];
    foreach ($params_data as $k => $v) {
        // 空的参数默认为全部,所以去除筛选
        if ($v) {
            $params[$k] = $v;
        }
    }

    // 设置默认排序、页码和每页数量
    $sortType = $params['sort'] ?? 1;
    $page = $params['page'] ?? 1;
    $pageSize = $params['page_number'] ?? 10;

    // 构建查询对象
    $query = self::buildQuery($params);

    // 计算分页相关数据
    $totalCount = $query->count();
    $totalPage = ceil($totalCount / $pageSize);
    $offset = ($page - 1) * $pageSize;

    // 执行查询并按指定规则排序和分页
    $models = $query
        ->orderBy(self::getSortValue($sortType))
        ->offset($offset)
        ->limit($pageSize)
        ->all();

    // 处理查询结果，组装商品数据
    $data = [];
    foreach ($models as $k => $v) {
        $goods = Goods::findOne($v->goods_id);
        if ($v->sku_id > 0) {
            $sku = GoodsOption::findOne($v->sku_id);
            if ($goods and $sku) {
                $data[] = [
                    'cart_id' => $v->id,
                    'title' => $goods->title,
                    'price' => $sku->price,
                    'crossed_price' => $sku->crossed_price,
                    'image' => Helper::setImg($goods['thumb']),
                    'status' => $goods->status,
                    'number' => $v->number,
                    'sku_title' => $sku->title,
                ];
            }
        } else {
            if ($goods) {
                $data[] = [
                    'cart_id' => $v->id,
                    'title' => $goods->title,
                    'price' => $goods->price,
                    'crossed_price' => $goods->crossed_price,
                    'image' => Helper::setImg($goods['thumb']),
                    'status' => $goods->status,
                    'number' => $v->number,
                    'sku_title' => ''
                ];
            }
        }
    }

    // 返回最终结果，包括商品数据和分页信息
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
