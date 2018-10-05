<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;
use yii\web\Session;

$this->title = 'Group Details';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
?>
<style type="text/css">
input:-webkit-autofill {
    background-color: white !important;
}
/*select {
    width: 100%;
}*/
.form-horizontal .control-label { font-size: 12px; letter-spacing: .5px; margin-top:5px; }
.form-devident { margin-top: 10px; }
.form-devident h4 { border-bottom: 1px dashed #ddd; padding-bottom: 10px; }
.download_file {display: block;}
.glyphicon-file {display:none!important;}
/*body {
    background: #eee;
}*/
</style>
<link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css" crossorigin="anonymous">
<link rel="stylesheet" href="http://cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css" type="text/css" media="screen" title="no title" charset="utf-8"/>
<!-- <link rel="stylesheet" href="@web/css/easy_tree.css"> -->
<?php 
    $this->registerCssFile(
        '@web/css/easy_tree.css',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>

<div class="grn-index">
    <div class=" col-md-12">  
        <!-- <form id="group_master" class="form-horizontal" action="<?php //echo Url::base(); ?>index.php?r=groupmaster%2Fsave" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;">  -->
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
            <div class="form-group">
                <div class="col-md-12">
                    <div class="easy-tree">
                        <?php echo $list; ?>

                        <!-- <ul>
                            <li>Parent 1</li>
                            <li>Parent 2</li>
                            <li>Parent 3
                                <ul>
                                    <li>Child 1</li>
                                    <li>Child 2
                                        <ul>
                                            <li>Grand-child 1</li>
                                            <li>Grand-child 2</li>
                                            <li>Grand-child 3</li>
                                            <li>Grand-child 4</li>
                                        </ul>
                                    </li>
                                    <li>Child 3</li>
                                    <li>Child 4</li>
                                </ul>
                            </li>
                            <li>Parent 4
                                <ul>
                                    <li>Parent 4 Child 1</li>
                                    <li>Parent 4 Child 2</li>
                                    <li>Parent 4 Child 3</li>
                                    <li>Parent 4 Child 4
                                        <ul>
                                            <li>Grand-child 1</li>
                                            <li>Grand-child 2</li>
                                            <li>Grand-child 3</li>
                                            <li>Grand-child 4</li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul> -->
                    </div>
                </div>
            </div>

            <!-- <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <label class="control-label">Remarks</label>
                    <textarea id="remarks" name="remarks" class="form-control" rows="2" maxlength="1000"><?php //if(isset($data)) echo $data[0]['approver_comments']; ?></textarea>
                </div>
            </div> -->

            <div class="form-group btn-container"> 
                <div class="col-md-12">
                    <!-- <button type="submit" class="btn btn-success btn-sm" id="btn_submit">Submit For Approval  </button> -->
                    <a href="<?php echo Url::base(); ?>index.php?r=groupmaster%2Findex" class="btn btn-primary btn-sm pull-right">Cancel</a>
                </div>
            </div>
        <!-- </form> -->
    </div>
</div>

<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
</script>

<?php 
    $this->registerJsFile(
        '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
    $this->registerJsFile(
        '@web/js/group_master.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>

</body>
</html>

