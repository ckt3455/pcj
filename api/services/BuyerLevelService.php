<?php
namespace api\services;
use backend\models\Buyer;
use backend\models\BuyerLevel;
use backend\models\BuyerLevelDetail;
use common\components\Helper;
use Yii;

class BuyerLevelService
{

    /**
     * 构建查询
     * @param array $params 查询参数
     * @return array 包含查询对象和分页数据的数组
     */
    public static function buildQuery($params = [])
    {
        $query = BuyerLevel::find();

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
            1 => 'level asc',
            2 => 'id ASC',
        ];

        return $sortMap[$sortType] ?? 'level asc';
    }


    /**
     * 执行查询
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

        // 构建查询
        $query = self::buildQuery($params);



        // 执行查询
        $models = $query
            ->orderBy(self::getSortValue($sortType))
            ->all();
        $data=[];

        foreach ($models as $k=>$v){
            $level_message=[];
            $detail=BuyerLevelDetail::find()->where(['level_id'=>$v['id']])->all();
            foreach ($detail as $k2=>$v2){
                $level_message[]=[
                    'title'=>$v2['title'],
                    'now_number'=>0,
                    'all_number'=>$v2['number'],
                ];
            }
            $data[]=[
                'level_id'=>$v['id'],
                'title'=>$v['title'],
                'image'=>Helper::setImg($v->image),
                'content'=>$v->content,
                'award'=>$v->award,
                'level_detail'=>$level_message,
            ];
        }
        return [
            'data' => $data,
        ];
    }



    public static function GetOne($id)
    {

        $model=BuyerLevel::findOne($id);
        $detail=BuyerLevelDetail::find()->where(['level_id'=>$id])->all();
        $level_message=[];
        foreach ($detail as $k2=>$v2){
            $level_message[]=[
                'title'=>$v2['title'],
                'now_number'=>0,
                'all_number'=>$v2['number'],
            ];
        }
        $data=[
            'level_id'=>$model['id'],
            'title'=>$model['title'],
            'image'=>Helper::setImg($model->image),
            'content'=>$model->content,
            'award'=>$model->award,
            'level_detail'=>$level_message,
        ];
        return [
            'data' => $data,
        ];
    }
}
