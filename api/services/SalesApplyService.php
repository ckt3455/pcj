<?php
namespace api\services;
use backend\models\SalesApply;
use Yii;

class SalesApplyService
{
    /**
     * 构建订单查询
     * @param array $params 查询参数
     * @return array 包含查询对象和分页数据的数组
     */
    public static function buildQuery($params = [])
    {
        $query = SalesApply::find()->where(['sales_id'=>$params['sales_id']]);
        if(isset($params['time'])){

            $start=strtotime($params['time'].'-01');

            $end=strtotime(date('Y-m-01',$start+31*24*3600));

            $query->andWhere(['>=','created_at',$start])->andWhere(['<=','created_at',$end]);
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

            $data[]=[
                'payment'=>$v->payment,
                'payment_message'=>SalesApply::$payment_message[$v->payment],
                'money'=>$v->money,
                'time'=>date('Y-m-d H:i:s',$v->created_at),
                'status'=>$v->status,
                'status_message'=>SalesApply::$status_message[$v->status],
            ];
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
}
