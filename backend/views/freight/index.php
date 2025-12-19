<?php
/**
 * Created by PhpStorm.
 * User: JianYan
 * Date: 2016/4/11
 * Time: 14:24
 */
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '运费模板';
$this->params['breadcrumbs'][] = ['label' =>  $this->title];

?>

<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <p>
        <a href="<?= Url::to(['create'])?>"  class="btn btn-primary">
            <i class="fa fa-plus"></i>
            新增
        </a>
    </p>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>运费模板列表</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>标题</th>
                            <th>排序</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($models as $model){ ?>
                            <tr id = <?= $model['id']?> >
                                <td><?= $model['id']?></td>
                                <td><?= $model['title']?></td>
                                <td class="col-md-1"><input type="text" class="form-control" value="<?= $model['sort']?>" onblur="sort(this)"></td>
                                <td>
                                    <a href="<?= Url::to(['update','id'=>$model['id']])?>" ><span class="btn btn-info btn-sm">编辑</span></a>
                                    <a href="<?= Url::to(['delete','id'=>$model['id']])?>"  onclick="deleted(this);return false;"><span class="btn btn-warning btn-sm">删除</span></a>&nbsp
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <?= LinkPager::widget([
                                                      'pagination'        => $pages,
                                                      'maxButtonCount'    => 5,
                                                      'firstPageLabel'    => "首页",
                                                      'lastPageLabel'     => "尾页",
                                                      'nextPageLabel'     => "下一页",
                                                      'prevPageLabel'     => "上一页",
                                                  ]);?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

<script type="text/javascript">
    function sort(obj){
        var id = $(obj).parent().parent().attr('id');
        var sort = $(obj).val();

        if(isNaN(sort)){
            alert('排序只能为数字');
            return false;
        }else{
            $.ajax({
                type:"get",
                url:"<?= Url::to(['update-ajax'])?>",
                dataType: "json",
                data: {id:id,sort:sort},
                success: function(data){

                    if(data.flg == 2) {
                        alert(data.msg);
                    }
                }
            });
        }
    }

    //status => 1:启用;-1禁用;
    function status(obj){
        var status = "";
        var id = $(obj).parent().parent().attr('id');
        var self = $(obj);

        if(self.hasClass("btn-primary")){
            status = 1;
        } else {
            status = 2;
        }

        $.ajax({
            type     :"get",
            url      :"<?= Url::to(['update-ajax'])?>",
            dataType : "json",
            data     : {id:id,status:status},
            success: function(data){
                if(data.flg == 1) {
                    if(self.hasClass("btn-primary")){
                        self.removeClass("btn-primary").addClass("btn-default");
                        self.text('禁用');
                    } else {
                        self.removeClass("btn-default").addClass("btn-primary");
                        self.text('启用');
                    }
                }else{
                    alert(data.msg);
                }
            }
        });
    }
</script>