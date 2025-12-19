<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Provinces;

?>
<link href="/Public/backend/css/style2.css" rel="stylesheet">
<style type="text/css" media="screen">
    .seek_input_b{position: relative;display: inline-block;border:1px solid #ddd;}
    .seek_input_b input{border:none;}
    .seek_input_b button{position: absolute;right:0;top:0;width: 40px;height: 100%;background:#fff url(/Public/frontend/images/seek1.png) no-repeat center center;cursor: pointer;}
    .seek_res{height: auto;}
    .seek_input_b_l{display: inline-block;}
    .seek_input_b_l input{width: 100px;background-image: none;border:none;padding:2px;font-size: 13px;color:#333;}
    .f_l{float:left;clear:none;}
</style>

<?php $form = ActiveForm::begin(); ?>
<div class="col-sm-12">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>购买人信息</h5>
        </div>

        <div class="ibox-content col-sm-6 f_l">
            <?php if($model->isNewRecord){?>
                <?= $form->field($model,'user_id')->widget(\kartik\select2\Select2::className(),[
                    'data'=>\backend\models\ProvinceUser::getList([]),
                    'options' => [
                        'placeholder' => '请选择 ...',
                        'multiple'=>false
                    ],
                ])?>
            <?php }else{?>
                <label class="control-label">用户</label>
                <input type="text"  class="form-control"  value="<?php if(isset($model->user)) echo $model->user->name;?>"  readonly>

            <?php }?>
            <div class="form-group">
                <label class="control-label">帐号</label>
                <input type="text"  class="form-control"  value="<?php if(isset($model->user)) echo $model->user->username?>"  readonly>
            </div>

        </div>
    </div>
</div>
<div class="col-sm-12">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>收货人信息</h5>
        </div>

        <div class="ibox-content  col-sm-12 f_l">

            <?= $form->field($model, 'consignee')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>


            <?= $form->field($model, 'province')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'area')->textInput(['maxlength' => true]) ?>






        </div>

    </div>
</div>
<div class="col-sm-12">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>商品信息</h5>
        </div>
        <div class="ibox-content">
            <div class="quote_b">
                <div class="cen">
                    <div class="seek">
                        <div class="seek_input_b">
                            <div class="seek_input_b_l">
                            </div>

                        </div>

                    </div>

                    <div class="seek_res">
                        <div class="seek_scr">
                            <table id="table" class="tablesorter mtable">
                                <thead>
                                <tr>
                                    <th>编码</th>
                                    <th>物料名称</th>
                                    <th>选择</th>
                                    <th>规格型号</th>
                                    <th>货期</th>
                                    <th>最小包装</th>
                                    <th>价格</th>
                                </tr>
                                </thead>
                                <tbody id="add_tbody">


                                </tbody>

                                <tfoot>
                                <tr>
                                    <td colspan="7" style="text-align: center;background: #fff;padding:10px 0;">
                                        <button type="button" class="w_btn" onclick="load_more()">加载更多</button>
                                    </td>
                                </tr>
                                </tfoot>


                            </table>

                        </div>
                        <div class="aligh-right">
                            <button type="button" class="w_btn marb_20 d_btn mart_20" onclick="hide_seek()">关闭</button>
                            <button type="button" class="w_btn marb_20 d_btn mart_20" id="add_confrim" >确认添加</button>
                        </div>
                    </div>


                        <table id="table2" class="tablesorter mtable table_b">
                            <thead>
                            <tr>
                                <th>
                                    <div class="check">
                                    </div>
                                </th>
                                <th>sku编码</th>
                                <th>商品名称</th>
                                <th>型号规格</th>
                                <th>售价</th>
                                <th>金额</th>
                                <th><font class="red">*</font>订购数量</th>
                            </tr>
                            </thead>

                            <tbody id="add_tbody1">
                            <?php if(isset($model->detail)){ foreach ($model->detail as $k=>$v){?>
                                <tr>
                                    <td colspan="2">
                                        <div class="check">
                                            <div class="input_b">
<!--                                                <input type="checkbox" onclick="sel_to(this)" />-->
                                            </div><?php if(isset($v->sku)) echo  $v->sku->sku_id;?></div>
                                    </td>
                                    <td><?php if(isset($v->goods)) echo  $v->goods->title;?></td>
                                    <td><?php if(isset($v->sku)) echo  $v->sku->specifications;?></td>
                                    <td class="later_price" later_price='<?= $v->price;?>'><em class="red">￥<input  style="width: 50px" type="text" name="sku_price[<?php echo $v->sku_id?>]" value="<?= $v->price;?>" onchange="change_price($(this).val(),'<?php echo $v->sku_id?>')"></em></td>

                                    <td><em class="red">￥<i id="total_price_<?php echo $v->sku_id?>" class="line_count cout<?php echo $v->sku_id?>"><?= $v->price*$v->number;?></i></em></td>
                                    <td>
                                        <div class="opear">
                                            <div class="opear_b">
                                                <button type="button" onclick="add(this,'<?php echo $v->price?>',-1,'cout<?php echo $v->sku_id?>')">-</button>
                                                <input type="text" value="<?php echo $v->number;?>" name="sku_number[<?php echo $v->sku_id?>]" min="1" onchange="add(this,'<?php echo $v->price;?>',0,'cout<?php echo $v->sku_id;?>')">
                                                <button type="button" onclick="add(this,'<?php echo $v->price?>',1,'cout<?php echo $v->sku_id?>')">+</button>
                                            </div>
                                            <a href="javascript:;" onclick="delLine(this)"><img src="/Public/frontend/images/del.png" alt=""></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php }}?>



                            </tbody>


                            <tfoot>
                            <tr>
                                <td colspan="2"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>


                    <div class="bor text_bz">


                        <div class="pri_del">
                            <div class="align_left">
<!--                                <input type="button" class="w_btn" value="删除选择产品" onclick="del_pro(this)" />-->
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
<div class="col-sm-12">
    
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>订单信息</h5>
        </div>

            <div  class="ibox-content col-sm-6 f_l">




                    <?= $form->field($model, 'order_number')->textInput(['maxlength' => true,'readonly'=>'readonly']) ?>


                    <?= $form->field($model, 'express_name')->textInput() ?>


                    <?= $form->field($model, 'express_number')->textInput() ?>


                    <?= $form->field($model, 'freight')->textInput() ?>


                    <?= $form->field($model, 'total_price')->textInput() ?>



            </div>
            <div class="ibox-content col-sm-6 f_l">




                <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>







            </div>
    </div>
</div>



<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? '添加' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript" src="/Public/frontend/js/table/jquery.tablesorter.js"></script>
<script type="text/javascript">
    var sum = 0;
    //总价
    function sum_price(){
        var len = $('#add_tbody1 tr').length;
        for(var i=0;i<len;i++){
            var pr = $('#add_tbody1 tr').eq(i).children().eq(6).children().children('input').val();
            var pr_sl = $('#add_tbody1 tr').eq(i).children().eq(8).children().children('.opear_b').children('input').val();
            sum = sum + pr*pr_sl;
        }
        console.log(sum);
    }

    function add_prcie(a){
        console.log($(a).val())
        
    }

    $('.hy_price').on('input',function(e){
        console.log($(this).val());
    })
    function seek(){
        var val=$('#search_keywords').val();
        if(val==''){
            alert('请输入关键词');
            return false;
        }
        var user_id=$('#order-user_id').val();
        if(user_id==''){
            alert('请选择用户');
            return false;
        }
        $.ajax({
            type:"get",
            url:"<?= \yii\helpers\Url::to(['order/quick-search'])?>",
            dataType: "json",
            data: {val:val,user_id:user_id},
            success: function(data){
                $('#add_tbody').html('');
                if(data.error == 0) {
                    load_lists(data);
                    $(".seek_res").show();
                }
                else{

                }
            }
        });

    }


    function load_lists(data){
        $.each(data.data,function(index,value){
            $('#add_tbody').append("<tr>" +
                "<td>"+value.number+"</td>" +
                "<td>"+value.title+"</td>" +
                "<td>" +
                "<div class=\"check\">" +
                "<div class=\"input_b\">" +
                "<input type=\"checkbox\" onclick='sel_input(this)' value='"+value.id+"' name='sku["+value.id+"]' />" +
                "</div>" +
                "</div>" +
                "</td>" +
                "<td>"+value.specifications+"</td>" +
                "<td><font class=\"blue\">"+value.period+"</font></td>" +
                "<td>"+value.min_number+"</td>" +
                "<td><font class=\"red\">￥"+value.price+"/把</font></td>" +
                "</tr>");
        });
    }

    var page=1;
    function load_more(){
        page++;
        $.ajax({
            type:"get",
            url:"<?= \yii\helpers\Url::to(['order/quick-search'])?>",
            dataType: "json",
            data: {
                page:page,
                val:$("#search_keywords").val(),
                user_id:$('#order-user_id').val()
            },
            success: function(data){
                if(data.error == 0) {
                    load_lists(data);

                }
                else{
                    alert('暂无结果');
                }
            }
        });
    }


    function count(){
        var count=0;
        var yh_price=0;
        $("#add_tbody1 tr").each(function(){
            var flag=$(".check input",this).is(":checked");
            if(flag){
                count+=parseFloat($(".line_count",this).text());
                yh_price+=(parseFloat($(".bef_price",this).attr("bef_price"))-parseFloat($(".later_price",this).attr("later_price")))*$(".opear_b input",this).val();
            }
        })
        $("#count_all").text(count.toFixed(2));
        $("#have_c").text(yh_price.toFixed(2));
    }

    function all_check(_this){
        var if_all=$(_this).is(":checked");

        if(if_all){
            $("#add_tbody1 .check input").addClass('current');
            $("#add_tbody1 tr .check input").prop("checked",true);

        }else{
            $("#add_tbody1 .check input").removeClass('current');
            $("#add_tbody1 tr .check input").prop("checked",false);
        }


        count();

    }

    function sel_to(_this){
        $(_this).toggleClass('current');
        count();
    }
    function add(_this,price,num,cls){
        var input_obj=$(_this).parents(".opear").find("input");
        var n=parseFloat(input_obj.val());
        if(num==0){
            n=n;
        }
        else if(num>0){
            n=n+1
        }else{
            n<=1?n=1:n=parseFloat(input_obj.val())-1;
        }
        input_obj.val(n);
        var c_price=(price*n).toFixed(2);
        $("."+cls+"").text(c_price);
        count();
    }

    function delLine(_this){
        layer.confirm('确定删除？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            $(_this).parents("tr").remove();
            layer.msg('删除成功', {icon: 1});
        }, function(){
        });

    }

    function del_pro(_this){
        $("#add_tbody1 tr").each(function(){
            if($(".check input",this).is(":checked")){
                $(this).remove();
            }

        })
        count();
    }

    function hide_seek(){
        $(".seek_res").hide();
    }

    function sel_input(_this){
        $(_this).toggleClass('current');
    }

    $("#add_confrim").click(function(){
        $(".seek_res").hide();
        var sel_arr=[];
        $("#add_tbody tr").each(function(){
            if($("input[type=checkbox]",this).is(":checked")){
                sel_arr.push($("input[type=checkbox]",this).val());
            }
        });
        $.ajax({
            type:"get",
            url:"<?= \yii\helpers\Url::to(['sku'])?>",
            dataType: "json",
            data: {
                id:sel_arr,
                user_id:$('#order-user_id').val(),
            },
            success: function(data){
                if(data.error == 0) {
                    var sel_html='';
                    $.each(data.data,function(value,index){
                        var title=index.title;
                        sel_html+='<tr>\
                    <td colspan="2">\
                        <div class="check">\
                            <div class="input_b">\
                                <input type="checkbox" onclick="sel_to(this)" />\
                            </div>'+index.number+'</div>\
                    </td>\
                    <td>'+title+'</td>\
                    <td>'+index.specifications+'</td>\
                    <td>'+index.brand+'</td>\
                    <td><em class="blue2"><input type="text" style="width: 50px" name="sku_period['+index.id+']" value="'+index.period+'"></em></td>\
                    <td class="bef_price" bef_price='+index.price1+'>￥'+index.price1+' <input type="hidden" name="sku_original_price['+index.id+']" value="'+index.price1+'"></td>\
                    <td class="later_price" later_price='+index.price2+'><em class="red">￥<input oninput="add_prcie(this)" class=".hy_price" type="text" style="width: 50px" name="sku_price['+index.id+']" value="'+index.price2+'"></em></td>\
                       <td><em class="red">￥<i id="total_price_'+index.id+'" class="line_count cout\'+index.id+\'">'+index.price2+'</i></em></td>\
                    <td>\
                        <div class="opear">\
                            <div class="opear_b">\
                                <button type="button" onclick="add(this,'+index.price2+',-1,\'cout'+index.id+'\')">-</button>\
                                <input type="text" value="1" name="sku_number['+index.id+']" min="1" onchange="add(this,'+index.price2+',0,\'cout'+index.id+'\')">\
                                <button type="button" onclick="add(this,'+index.price2+',1,\'cout'+index.id+'\')">+</button>\
                            </div>\
                            <a href="javascript:;" onclick="delLine(this)"><img src="/Public/frontend/images/del.png" alt=""></a>\
                        </div>\
                    </td> </tr>';
                    });
                    $("#add_tbody1").append(sel_html);

                    //加载的总价
                    sum_price();
                    
                }
                else{
                    alert('发生错误');
                }
                $("#checked_all").prop("checked",true);
                $("#checked_all").addClass('current');
                all_check("#checked_all");
            }
        });

    })

    function change_price(price,location) {
        var number=$("input[name='sku_number["+location+"]']").val();
        var val=parseFloat(number)*parseFloat(price);
        $('#total_price_'+location+'').text(val);
    }


</script>
