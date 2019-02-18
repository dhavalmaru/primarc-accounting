<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Scraping Log';
?>

<style>
.form-horizontal .checkbox, .form-horizontal .radio { padding:0;  margin:0; min-height:auto; line-height:20px;}
.checkbox input[type=checkbox], .checkbox-inline input[type=checkbox], .radio input[type=radio], .radio-inline input[type=radio] { position:relative; margin:0;}
.table>thead>tr>th {   vertical-align: middle;  border-bottom: 2px solid #ddd;}
.checkbox, .radio { margin:0; padding:0;}
.bold-text {    background-color: #f1f1f1; text-align:right;}
.bold-text th {text-align:right!important;}
.ad_hock{display:none;}
#knock_off{display:none;}
.form-horizontal .control-label {font-size: 12px; letter-spacing: .5px; margin-top:5px; }
.form-devident { margin-top: 10px; }
.table-hover>tbody>tr:hover {
    background:none!important;
}
table tr td { border: 1px solid #eee!important; }
/*.form-devident h3 { border-bottom: 1px dashed #ddd; padding-bottom: 10px; }*/
#report_filter { border-bottom: 1px dashed #ddd; }
#report_header label { display: block; }
.ui-datepicker {z-index: 1000!important;}
@media print {
	#report_filter, #btn_print {
		display:none;
	}
	@page {size: landscape;}
	.btn-group {display: none;}
}

#example_wrapper .row:first-child .col-md-6:last-child { padding-top: 0px; }
#example_wrapper .row:first-child .col-md-6:last-child .btn-group { margin-right: 0px; margin-top: 0px; }
#example_wrapper .row:nth-child(1) {margin-top: -30px;}
/*#example_wrapper .row:nth-child(2) {margin-top: 20px;}*/

#example2_wrapper .row:first-child .col-md-6:last-child { padding-top: 0px; }
#example2_wrapper .row:first-child .col-md-6:last-child .btn-group { margin-right: 0px; margin-top: 0px; }
#example2_wrapper .row:nth-child(1) {margin-top: -30px;}
/*#example2_wrapper .row:nth-child(2) {margin-top: 20px;}*/
</style>

<div class="grn-index">
	<div class=" col-md-12 ">  
		<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<div id="report">
				<div class="row">
					<div class="col-md-11 col-sm-10 col-xs-12">
						<div  id="loader" ></div>
						<div class="loading">
							<div class="form-group">
								<div class="col-md-12 " >
									<table id="example" class="table table-bordered display ">
										<thead>
										<tr class="sticky-row">
											<th class="text-center"> Sr No </th>
											<th class="text-center"> Date </th>
											<th class="text-center"> Actions </th>
										</tr>
									</thead>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
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
        '@web/js/scraping_log.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>