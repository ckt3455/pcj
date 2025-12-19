<?php
use yii\widgets\ActiveForm;
use backend\models\IntegralGoods;
use backend\models\Goods;

/* @var $this yii\web\View */
/* @var $model backend\models\menu */

$this->title = $model->isNewRecord ? '创建' : '编辑';
$this->params['breadcrumbs'][] = ['label' => '商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .multi-container2{
        width: 160px;!important;
    }

    .element-invisible{
        width: 100%;
        height: 100%;
        display: block;
        opacity: 0;
    }
</style>
<link href="/Public/css/plugins/chosen/chosen.css" rel="stylesheet">
<style>
    .phoneShow{
        width: 375px;
        height: 667px;
        margin-top: 16px;
        border: 1px solid
        #dadada;
        border-radius: 4px;
        margin: 0 auto;
        padding-bottom: 16px;
        overflow: hidden;
        position: relative;
    }
    .topImg{
        width: 100%;
        margin-top: 5px;
    }
    .showContent{
        height: 500px;
        overflow-y: auto;
        margin: 8px;
        padding-bottom: 10px;
        word-break: break-word;
    }
    .bottomImg{
        width: 100%;
        position: absolute;
        bottom: 0;
    }
    .showContent div img {
        max-width: 100%; /*图片自适应宽度*/
    }
    .ivu-card-body {
        padding: 20px;
    }
    .goods-param-content {
        background:
                #fff;
        zoom: .7;
        height: 640px;
        overflow: auto;
    }
    .bor-b {
        border-bottom: 1px solid
        #ededed;
    }
    .goodsParam {
        background:
                #fff;
        font-size: 12px;
        color:
                #666;
    }
    .bor-t {
        border-top: 1px solid #ededed;
    }
    .bor-l {
        border-left: 1px solid
        #ededed;
        min-height: 44px;
    }
    .goodsParam .flex div:first-of-type {
        padding: 9px 14px;
        width: 170px;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
    }
    .goodsParam .flex div:nth-of-type(2) {
        padding: 9px 20px;
    }
    .bor-r {
        border-right: 1px solid
        #ededed;
    }
    .flex{
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
    }
    .flex1 {
        -webkit-box-flex: 1;
        -webkit-flex: 1;
        -ms-flex: 1;
        flex: 1;
    }
    .bor-title{
        border-right: 0px none; font-weight: bold;
    }
</style>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">商品信息</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">规格设置</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false">其他设置</a>
                            </li>
                            <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false">商品详情</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="tabs-container">
                <div class="tab-content">
                    <div class="ibox float-e-margins tab-pane active" id="tab-1">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>基础信息</h5>
                            </div>
                            <div class="ibox-content" >
                                <?= $form->field($model, 'title')->textInput() ?>

                                <?= $form->field($model, 'sub_title')->textInput() ?>

                                <?= $form->field($model, 'thumb')->widget('backend\widgets\webuploader\Image', [
                                    'boxId' => 'thumb',
                                    'options' => [
                                        'multiple'   => false,
                                    ]
                                ])->hint('作用于商城列表、分享头图；建议尺寸：750*750像素。')?>
                                <?= $form->field($model, 'thumbs[]')->widget('backend\widgets\webuploader\Image', [
                                    'boxId' => 'thumbs',
                                    'options' => [
                                        'multiple'   => true,
                                    ]
                                ])->hint('作用于商品详情页顶部轮播；建议尺寸：750*750像素，轮播图可以拖拽图片调整顺序，最多上传10张（至少1张）')?>

                                <?= $form->field($model, 'thumb_video')->widget('backend\widgets\webuploader\Videos', [
                                    'boxId' => 'thumb_video',
                                    'options' => [
                                        'multiple'   => false,
                                    ]
                                ])->hint('作用于商品详情；建议尺寸：750*750像素。')?>

                                <?= $form->field($model, 'category_id')->widget('\kartik\select2\Select2', [
                                    'data' =>\backend\models\GoodsCategory::getList(),
                                    'options' => ['placeholder' => '请选择分类', 'allowClear' => true],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'multiple'=>false,
                                    ]
                                ]);  ?>

                                <?= $form->field($model, 'intro')->textarea() ?>

                            </div>
                        </div>
                    </div>
                    <div class="ibox float-e-margins tab-pane " id="tab-2">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>规格设置</h5>
                            </div>
                            <div class="ibox-content" >

                                <?php if(!Goods::GetOpenCRM() || $model->isNewRecord){?>
                                    <?= $form->field($model, 'has_option')->radioList(Goods::$has_option,['onchange'=>'sku_show()']) ?>
                                <?php }else{ ?>
                                    <?= $form->field($model, 'has_option')->textInput(['value'=>Goods::$has_option[$model->has_option],'disabled'=>'disabled']) ?>
                                <?php } ?>
                                <div id="sku_attribute" class="control-group" style="display: <?php if( $model->has_option==1){ echo "block";}else{ echo "none";}?>">

                                    <label class="control-label">商品规格 </label>
                                    <div class="controls" id="cont_save">
                                        <button id="add_lv1" class="btn btn-primary" type="button">添加规格项</button>
                                        <button id="update_table" class="btn btn-success" type="button">刷新规格项目表</button>
                                        <!--如果存在属性显示属性-->
                                        <?php
                                        foreach ($model->getSpecData() as $k => $v) {
                                            ?>
                                            <div class="control-group lv1 get_input_c">
                                                <label class="control-label">规格名称</label>
                                                <div class="controls lv2s_1">
                                                    <input type="text" name="lv1" value="<?php echo $v['title'] ?>" placeholder="规格名称">
                                                    <button class="btn btn-primary add_lv2" type="button">添加参数</button>
                                                    <button class="btn btn-danger remove_lv1" type="button">删除规格</button>
                                                </div>
                                                <div class="controls lv2s">
                                                    <?php foreach ($v['list'] as $k2 => $v2) { ?>
                                                        <div style="margin-top: 5px;">
                                                            <input type="text" name="lv2" placeholder="参数名称"
                                                                   value="<?php echo $v2['title']; ?>">
                                                            <button class="btn btn-danger remove_lv2" type="button">删除参数</button>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php }
                                        ?>

                                    </div>

                                </div>

                                <div id="sku_value" class="control-group"  style="display:<?php if($model->has_option==1){ echo "block";}else{ echo "none";}?>">

                                </div>

                                <div id="lv_table_con" class="control-group"  style="display:<?php if($model->has_option==1){ echo "block";}else{ echo "none";}?>">
                                    <label class="control-label">规格项目表</label>
                                    <div class="controls">
                                        <div id="lv_table">


                                        </div>
                                    </div>
                                </div>

                                <div id="sku_one" class="control-group"  style="display:<?php if($model->has_option==2)
                                { echo "block";}else{ echo "none";}?>">

                                    <?= $form->field($model, 'price',['template' => '{label}<div class="input-group m-b">{input}<span class="input-group-addon">元</span></div>{hint}{error}',])->textInput() ?>
                                    <?= $form->field($model, 'crossed_price',['template' => '{label}<div class="input-group m-b">{input}<span class="input-group-addon">元</span></div>{hint}{error}',])->textInput() ?>

                                    <?= $form->field($model, 'upc_code')->textInput() ?>
                                    <?= $form->field($model, 'weight',['template' => '{label}<div class="input-group m-b">{input}<span class="input-group-addon">KG</span></div>{hint}{error}',])->textInput() ?>
                                    <?php if(!Goods::GetOpenCRM()){?>
                                        <?= $form->field($model, 'stock')->textInput() ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ibox float-e-margins tab-pane " id="tab-3">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>其他设置</h5>
                            </div>
                            <div class="ibox-content">

                                <?= $form->field($model, 'sales')->textInput() ?>
                                <?= $form->field($model, 'units')->textInput() ?>
                                <?= $form->field($model, 'stock_warning')->textInput() ?>
                                <?= $form->field($model, 'sort')->textInput() ?>

                                <?= $form->field($model, 'freight_model_id')->widget('\kartik\select2\Select2', [
                                    'data' =>\backend\models\FreightModel::getList(),
                                    'options' => ['placeholder' => '请选择运费模板', 'allowClear' => true],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'multiple'=>false,
                                    ]
                                ])->hint('选择无则使用默认模板')?>

                                <?= $form->field($model, 'hot')->checkboxList(Goods::$hot) ?>

                                <?= $form->field($model, 'sort')->textInput() ?>

                                <?= $form->field($model, 'status')->radioList(\backend\models\Goods::$status) ?>

                            </div>
                        </div>
                    </div>
                    <div class="ibox float-e-margins tab-pane" id="tab-4">

                        <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-title">
                                <h5>编辑详情</h5>
                            </div>
                            <div class="ibox-content">

                                <?= $form->field($model, 'associated_goods')->widget('\kartik\select2\Select2', [
                                    'data' =>Goods::getList2($model->associated_goods),
                                    'options' => ['placeholder' => '请选择关联商品', 'allowClear' => true],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'multiple'=>true,
                                        'maximumSelectionLength'=>10,
                                        'ajax' => [
                                            'url' => \yii\helpers\Url::to(['goods/get-list','notid'=>$model->id]),
                                            'dataType' => 'json',
                                            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                                        ],
                                    ]
                                ])->hint('请输入要添加商品名称,最多选择10件')  ?>

                                <?= $form->field($model,'content')->widget('kucha\ueditor\UEditor',[
                                    'id'=>'goodsContent',
                                    'clientOptions' => [
                                        //编辑区域大小
                                        'initialFrameHeight' => '400',
                                        'autoHeightEnabled' => false,
                                        //定制菜单
                                        'toolbars' => [
                                            [
                                                'fullscreen', 'source', '|', 'undo', 'redo', '|',
                                                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
                                                'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                                                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                                                'directionalityltr', 'directionalityrtl', 'indent', '|',
                                                'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
                                                'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                                                'simpleupload', 'insertimage', 'emotion', 'insertvideo', 'music', 'attachment', 'map', 'insertframe', 'insertcode', 'pagebreak', 'template', 'background', '|',
                                                'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
                                                'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
                                                'searchreplace', 'help', 'drafts'
                                            ],
                                        ],
                                    ]
                                ]);?>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2">
                    <button class="btn btn-primary" type="submit">保存内容</button>
                    <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</body>

<script>
    //更多设置
    function show_more(_this,id) {
        let show = $(_this).attr('data-show');
        if(show=='true'){
            $(_this).text('展开更多设置 ');
            $(_this).attr('data-show','false');
            $("#"+id).slideToggle(200);
        }else{
            $(_this).text('收起更多设置');
            $(_this).attr('data-show','true');
            $("#"+id).slideToggle(200);
        }
    }

</script>

<script>
    //选择多规格
    function sku_show() {
        var sku_show=$('#goods-has_option').find("input[type=radio]:checked").val();
        if(sku_show==1){
            $('#sku_attribute').show();
            $('#sku_value').show();
            $('#lv_table_con').show();
            $('#sku_one').hide();
        }else{
            $('#sku_attribute').hide();
            $('#sku_value').hide();
            $('#lv_table_con').hide();
            $('#sku_one').show();
        }
    }

</script>


<!--规格生成-->
<script>
    var lv1HTML = '<div class="control-group lv1 mar_l30">' +
        '<label class="control-label">规格名称</label>' +
        '<div class="controls controls1">' +
        '<input type="text" name="lv1" placeholder="规格名称">' +
        '<button class="btn btn-primary add_lv2" type="button">添加参数</button>' +
        '<button class="btn btn-danger remove_lv1" type="button">删除规格</button>' +
        '</div>' +
        '<div class="controls lv2s"></div>' +
        '</div>';

    var lv2HTML = '<div style="margin-top: 5px;" class="s_input">' +
        '<input type="text" name="lv2" placeholder="参数名称" data-img="">' +
        '<button class="btn btn-danger remove_lv2" type="button">删除参数</button>' +
        '</div>';

    $(document).ready(function () {
        $('#add_lv1').on('click', function () {
            var last = $('.control-group.lv1:last');
            if (!last || last.length == 0) {
                $('#sku_value').append(lv1HTML);
            } else {
                last.after(lv1HTML);
            }
        });

        $(document).on('click', '.remove_lv1', function () {
            $(this).parents('.lv1').remove();
        });

        $(document).on('click', '.add_lv2', function () {
            $(this).parents('.lv1').find('.lv2s').append(lv2HTML);
        });

        $(document).on('click', '.remove_lv2', function () {
            $(this).parent().remove();
        });
        $('#update_table').on('click', function () {
            var lv1Arr = $('input[name="lv1"]');

            var n_con_arr = [];
            var n_con_arr2 = [];
            var n_str = '';
            var n_str2='';
            for(var i=0;i<lv1Arr.length;i++){
                var lv2Arr = $(lv1Arr[i]).parents('.lv1').find('input[name="lv2"]');
                var lv2ArrLen = lv2Arr.length;
                n_str='';
                n_str2='';
                for(var x=0;x<lv2ArrLen;x++){
                    n_str2+=lv2Arr.eq(x).val()+':'+lv2Arr.eq(x).attr('data-img')+',';
                    n_str+=lv2Arr.eq(x).val()+',';
                }
                n_str = n_str.substring(0,n_str.length-1);
                n_con_arr.push(n_str);
                n_str2 = n_str2.substring(0,n_str2.length-1);
                n_con_arr2.push(n_str2);
            }
            if (!lv1Arr || lv1Arr.length == 0) {
                $('#lv_table_con').hide();
                $('#lv_table').html('');
                return;
            }

            for (var i = 0; i < lv1Arr.length; i++) {
                var lv2Arr = $(lv1Arr[i]).parents('.lv1').find('input[name="lv2"]');
                if (!lv2Arr || lv2Arr.length == 0) {
                    alert('请先删除无参数的规格项！');
                    return;
                }
            }
            var tableHTML = '';
            tableHTML += '<table class="table table-bordered">';
            tableHTML += '    <thead>';
            tableHTML += '        <tr>';
            for (var i = 0; i < lv1Arr.length; i++) {
                tableHTML += '<th width="50">' + $(lv1Arr[i]).val() + '</th>';
                tableHTML += '<input type="hidden"  name="title['+i+']" value="' + $(lv1Arr[i]).val() + '">';
            }
            tableHTML += '            <th width="10">封面</th>';
            tableHTML += '            <th width="10">零售价(元)</th>';
            tableHTML += '            <th width="10">划线价(元)</th>';
            tableHTML += '            <th width="10">商品条码</th>';
            tableHTML += '            <th width="10">重量(KG)</th>';
            tableHTML += '            <th width="10">库存</th>';


            tableHTML += '        </tr>';
            tableHTML += '    </thead>';
            tableHTML += '    <tbody>';

            var numsArr = new Array();
            var idxArr = new Array();
            for (var i = 0; i < lv1Arr.length; i++) {
                numsArr.push($(lv1Arr[i]).parents('.lv1').find('input[name="lv2"]').length);
                idxArr[i] = 0;
            }
            var len = 1;
            var rowsArr = new Array();
            for (var i = 0; i < numsArr.length; i++) {
                len = len * numsArr[i];

                var tmpnum = 1;
                for (var j = numsArr.length - 1; j > i; j--) {
                    tmpnum = tmpnum * numsArr[j];
                }
                rowsArr.push(tmpnum);
            }

            for (var i = 0; i < len; i++) {

                tableHTML += '        <tr data-row="' + (i + 1) + '">';

                var name = '';
                for (var j = 0; j < lv1Arr.length; j++) {
                    var n = parseInt(i / rowsArr[j]);
                    if (j == 0) {
                    } else if (j == lv1Arr.length - 1) {
                        n = idxArr[j];
                        if (idxArr[j] + 1 >= numsArr[j]) {
                            idxArr[j] = 0;
                        } else {
                            idxArr[j]++;
                        }
                    } else {
                        var m = parseInt(i / rowsArr[j]);
                        n = m % numsArr[j];
                    }

                    var text = $(lv1Arr[j]).parents('.lv1').find('input[name="lv2"]').eq(n).val();
                    if (j != lv1Arr.length - 1) {
                        name += text + '_';
                    } else {
                        name += text;
                    }
                    if (i % rowsArr[j] == 0) {
                        tableHTML += '<td width="50" rowspan="' + rowsArr[j] + '"  data-rc="' + (i + 1) + '_' + (j + 1) + '">' + text + '</td>';
                    }
                }

                tableHTML += '            <td width="10">\n' +
                '            <div class="multi-container multi-container2 model-imgage">\n' +
                '            <div class="photo-list clearfix">\n' +
                '            <ul class="ui-sortable">\n' +
                '        <li class="upload-box social-avatar" >\n' +
                '        <div class="upload-btn webuploader-container" >\n' +
                '            <div class="webuploader-pick" ></div>\n' +
                '            <div style="position: absolute; inset: 0px auto auto 0px; width: 110px; height: 110px; overflow: hidden;">\n' +
                '            <input type="file" name="file" class="element-invisible" onchange="uploadImage(this,\''+name+'\')" accept="image/*">\n' +
                '            <label style="opacity: 0; width: 100%; height: 100%; display: block; cursor: pointer; background: rgb(255, 255, 255) none repeat scroll 0% 0%;"></label>\n' +
                '            </div>\n' +
                '            </div>\n' +
                '            </li>\n' +
                '            </ul>\n' +
                '            </div>\n' +
                '            </div>\n' +
                '            </td>\n';
                tableHTML += '<td width="10"><input type="text" name="price[' + name + ']" required="" onchange="reg(this)" value="0" /></td>';
                tableHTML += '<td width="10"><input type="text" name="crossed_price[' + name + ']" required="" onchange="reg(this)" value="0" /></td>';
                tableHTML += '<td width="10"><input type="text" name="upc_code[' + name + ']" required="" /></td>';
                tableHTML += '<td width="10"><input type="text" name="weight[' + name + ']"   onchange="reg(this)" value="0" /></td>';
                tableHTML += '<td width="10"><input type="text" name="stock[' + name + ']" onchange="reg(this)" value="0" /></td>';

                tableHTML += '</tr>';

            }

            for(var y=0;y<n_con_arr.length;y++){
                tableHTML += '<tr><input type="hidden" name="group[' + y + ']" value="' + n_con_arr[y] + '"></tr>';
            }
            tableHTML += '</tbody>';
            tableHTML += '</table>';

            tableHTML += '<div id="imgdata">';
            for(var y=0;y<n_con_arr2.length;y++){
                tableHTML += '<input type="hidden" name="imgarr[' + y + ']" value="' + n_con_arr2[y] + '">';
            }
            tableHTML += '</div>';

            var is_sku=<?=$model['has_option']?>;
            if(is_sku==1){
                $('#lv_table_con').show();
            }
            $('#lv_table').html(tableHTML);
        });

        $('#update_table').click();

        //存在sku,覆盖数据
        $.ajax({
            type: "get",
            url: "<?=\yii\helpers\Url::to(['goods/get-sku'])?>",
            dataType: "json",
            data: {id: "<?=$model->id?>"},
            success: function (result) {
                var arrays=result;
                for (var i = 0; i < arrays.length; i++) {
                    $("input[name='price[" + arrays[i].title + "]']").val(arrays[i].price);
                    $("input[name='crossed_price[" + arrays[i].title + "]']").val(arrays[i].crossed_price);
                    $("input[name='weight[" + arrays[i].title + "]']").val(arrays[i].weight);
                    $("input[name='upc_code[" + arrays[i].title + "]']").val(arrays[i].upc_code);
                    $("input[name='stock[" + arrays[i].title + "]']").val(arrays[i].stock);
                }

            }

        });

    });

    function reg(_this){
        var val =parseFloat($(_this).val());
        if(isNaN(val)||val<0){
            alert('请输入合法值');
            $(_this).val('');
            $(_this).focus();
            return false;
        }
    }
    function reg2(_this){
        var val =parseFloat($(_this).val());
        if(isNaN(val)|| val<0 || val>100){
            alert('请输入合法值');
            $(_this).val(100);
            $(_this).focus();
            return false;
        }

    }

    function doUpload(_this) {
        var formData = new FormData();
        //知识拓展
        //  jquery选择器 $(#id) 返回的是jquery对象，用document.getElementById( id )返回的是DOM对象。
        // （1）jquery对象可以使用两种方式转换为DOM对象， [index]和.get(index)
        //  $(#id)[0]   得到DOM对象
        //  $(#id).get( 0 )   -----》  DOM对象
        // （2）DOM对象转成jquery对象：
        //  $(DOM对象）

        formData.append("fileupload", $(_this)[0].files[0]);
        $.ajax({
            url: "<?=\yii\helpers\Url::to(['add-img'])?>" ,
            type: 'post',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            async: false,
            dataType: 'json',
            success : function (data) {
                if (data.error == 0) {
                    $(_this).parent().find('input[name="lv2"]').attr('data-img',data.url);
                    addImgHtml();
                    alert(data.msg);
                } else {
                    alert(data.msg);
                }
            }
        })
    }

    function addImgHtml() {
        var html = $("#imgdata").html();
        if(html!='') {
            var lv1Arr = $('input[name="lv1"]');

            var n_con_arr = [];
            var n_str = '';
            for (var i = 0; i < lv1Arr.length; i++) {
                var lv2Arr = $(lv1Arr[i]).parents('.lv1').find('input[name="lv2"]');
                var lv2ArrLen = lv2Arr.length;
                n_str = '';
                for (var x = 0; x < lv2ArrLen; x++) {
                    n_str += lv2Arr.eq(x).val() + ':' + lv2Arr.eq(x).attr('data-img') + ',';
                }
                n_str = n_str.substring(0, n_str.length - 1);
                n_con_arr.push(n_str);
            }
            var tableHTML = '';
            for(var y=0;y<n_con_arr.length;y++){
                tableHTML += '<input type="hidden" name="imgarr[' + y + ']" value="' + n_con_arr[y] + '">';
            }
            $("#imgdata").html(tableHTML);
        }
    }

    /**
     * 上传图片
     * @param _this
     */
    function uploadImage(_this,key) {

        let loading = layer.load(0, {
            shade: false,
        });

        let formData = new FormData();
        formData.append("file",_this.files[0]);
        let _this2 = $(_this).parent().parent().parent();

        $.ajax({
            type:"post",
            url:"<?=\yii\helpers\Url::to(['file2/upload-images'])?>",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            jsonp: "jsoncallback",
            dataType: "json",
            success: function(data){
                if(data.flg == 2) {
                    alert(data.msg);
                }else{
                    $("#head_portrait").val(data.url);
                    _this2.hide();
                    let htm = '<li class="social-avatar">\n' +
                        '<input type="hidden" name="thumb['+key+']" value="'+data.url+'">\n' +
                        '<div class="img-box">\n' +
                        '<a class="fancybox" href="'+data.url+'">\n' +
                        '<img src="'+data.url+'">\n' +
                        '</a>\n' +
                        '<i class="delimg" data-multiple=""></i>\n' +
                        '</div>\n' +
                        '</li>';
                    _this2.parent().append(htm);

                }
                layer.close(loading);
            }
        });
    }

</script>
<!--规格生成-->
<?php $this->registerJsFile('/Public/js/plugins/chosen/chosen.jquery.js',['depends'=>['backend\assets\AppAsset']]);?>
<?php $this->registerJs(<<<Js

$(document).ready(function () {
    //拖动排序
    $("#sortList").sortable({
        connectWith: ".sortData",
        update: function( event, ui ) {
            sortDor();
        }
    }).disableSelection();
});

Js
);
?>









