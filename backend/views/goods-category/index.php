<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\widgets\Bar;
use kartik\switchinput\SwitchInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\GoodsCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>商品分类</h5>
                </div>

                <div class="ibox-content">
                    <table class="table table-hover">
                        <a class="btn btn-white btn-sm" href="javascript:void(0);" title="添加" data-pjax="0" onclick="viewLayer('/backend/index.php/goods-category/edit',$(this))"><i class="fa fa-plus"></i> 添加</a>
                        <thead>
                        <tr>
                            <th>标题</th>
                            <th></th>
                            <th>排序</th>
                            <th>操作</th>
                        </tr>
                        </thead>



                        <tbody>

                        <?php foreach ($models as $k => $v) { ?>
                            <tr id="<?php echo $v->id ?>" class="0">
                                <td>
                                    <?php if($v->children){?>
                                        <div onclick="get_children('<?php echo $v->id?>',$(this))" class="fa cf fa-plus-square" style="cursor:pointer;"></div>
                                    <?php }?>
                                </td>
                                <td>
                                    <b><?php echo $v->title; ?></b>&nbsp;
                                </td>
                                <td class="col-md-1"><input type="text" class="form-control" value="<?= $v->sort?>" onblur="sort(this)"></td>
                                <td>

                                    <a type="button" class="btn btn-info btn-sm" href="javascript:void(0);" onclick="viewLayer('<?= Url::to(['edit','parent_id'=>$v->id,'level'=>$v->level+1])?>',$(this))">
                                        添加下级
                                    </a>
                                    <a href='javascript:void(0);'  type=\"button\" class="btn btn-primary btn-sm"  onclick="viewLayer('<?= Url::to(['edit','id'=>$v->id])?>',$(this))" data-pjax='0' > 编辑</a>

                                    <a type="button" class="btn btn-warning btn-sm" href="<?= Url::to(['delete','id'=>$v->id])?>" data-method="post" data-pjax="0" data-confirm="确定要删除吗？"> 删除</a>

                                </td>
                            </tr>
                        <?php } ?>


                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function sort(obj) {
        var id = $(obj).parent().parent().attr('id');
        var sort = $(obj).val();
        if (isNaN(sort)) {
            alert('排序只能为数字');
            return false;
        } else {
            $.ajax({
                type: "get",
                url: "<?= Url::to(['update-ajax'])?>",
                dataType: "json",
                data: {id: id, sort: sort},
                success: function (data) {

                    if (data.flg == 2) {
                        alert(data.msg);
                    }
                }
            });
        }
    }

    //获取下级
    function get_children(id,self) {
        if(  $('.'+id+'').length>0){
        }
        else{
            $.ajax({
                type: "get",
                url: "<?php echo Url::to(['goods-category/get-children'])?>",
                dataType: "json",
                data: {"id":id},
                success: function (result) {
                    $('#'+id+'').after(result);
                }
            });

        }
        if (self.hasClass("fa-minus-square")) {
            $('.' + id).hide();
            self.removeClass("fa-minus-square").addClass("fa-plus-square");
        } else {
            $('.' + id).show();
            self.removeClass("fa-plus-square").addClass("fa-minus-square");
        }

    }
</script>

