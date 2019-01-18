<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Invoice Wise Report';
$mycomponent = Yii::$app->mycomponent;
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
#report_header label { display: block; padding-bottom: 0px; margin-bottom: 0px;}
.ui-datepicker {z-index: 1000!important;}
.show_narration {word-break: break-all;}
@media print {
	#report_filter, #btn_print {
		display:none;
	}
	@page {size: landscape;}
	.btn-group {display: none;}
}
#example_wrapper .row:first-child .col-md-6:last-child .btn-group { margin-right: 0px; }
#example_wrapper .row:nth-child(1) {margin-top: -60px;}
#example_wrapper .row:nth-child(2) {margin-top: 20px;}
#example {
	width: 1040px !important;
}

#example_filter,#example2_filter{
	float: left;
}

div.dataTables_wrapper div.dataTables_paginate {
    margin: 0;
    white-space: nowrap;
    text-align: left;
}

/*tbody{
   	height:150px;display:block;overflow:scroll
}*/
/*table {
    width: 100%;
    display:block;
}
thead {
    display: inline-block;
    width: 100%;
    height: 30px;
}
tbody {
    height: 300px;
    display: inline-block;
    width: 100%;
    overflow: auto;
}*/

#example_wrapper .row:first-child .col-md-6:last-child .btn-group {
	margin-bottom:94px; 
	float: right;
	margin-right: -178px;
	float: left!important;
}
.form-control{
	height: 32px!important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
	background-color: #3c8dbc!important;
}
.error {
	font-weight: 500;
	color: #f95353!important;
	font-size: 12px;
	letter-spacing: .5px;
	border: 0px solid #f95353;
	margin: 0; 
}
.dataTables_scroll {
	overflow:auto;
}
#example_wrapper .row:first-child .col-md-6:last-child .btn-group {
	margin-bottom: 0px!important; 
	float: right!important; 
	margin-right: 0px!important; 
}
#example_wrapper .row:first-child{
	margin-top: 0px;
}
#example_wrapper .row:nth-child(2){
	margin-top: 0px;
}

</style><!-- 
 <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" /> -->
<?php 
$this->registerCssFile(
        '@web/css/select2.min.css',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>
<div class="grn-index container">
	<form id="detailedinvoice_report" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=accreport%2Finvoice_wise" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;">
		<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
		<div id="report_filter">
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<div id="report_filter">
				<div class="row">
					<div class="col-md-4 col-sm-2 col-xs-6">
						<label class="control-label">Group</label>
						<?=$select;?>
						<span id="accounterror"></span>
					</div>
					<div class="col-md-4 col-sm-2 col-xs-6">
						<label class="control-label">Ledger Name</label>
						<select  id="ledger_name" class="form-control " name="ledger_name[]" multiple="multiple" data-error="#ledger_nameerror">
						<?php
							 if(in_array('ALL',$leder_id))
							 {
							 	echo "<option value='ALL' selected>ALL</option>";
							 }
						?>
						<?php

							for ($i=0; $i <count($ledger_name) ; $i++) { 
								 $select = '';
								 if(in_array($ledger_name[$i]['id'],$leder_id))
								 {
								 	$select = 'selected';
								 }

								 echo "<option value='".$ledger_name[$i]['id']."' ".$select.">".$ledger_name[$i]['legal_name']."</option>";
							}
						?>
						</select>
						<span id="ledger_nameerror"></span>
					</div>
					<div class=" col-md-3 col-sm-2 col-xs-6">
						<label class="control-label">View</label>
						<div class=" ">
							<div class=" ">
								<select class="form-control valid" id="view" name="view" aria-invalid="false">
									<option value="Summary" <?=($view=='Summary'?'selected':'');?>>Summary</option>
									<option value="Detailed" <?=($view=='Detailed'?'selected':'');?>>Detailed</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class=" col-md-4  col-sm-2 col-xs-6">
						<label class="control-label">From Date</label>
						<div class=" ">
							<div class=" ">
								<input class="form-control datepicker" type="text" id="from_date" name="from_date" value="<?php if(isset($from_date)) echo (($from_date!=null && $from_date!='')?$from_date:''); ?>" readonly />
							</div>
						</div>
					</div>
					<div class=" col-md-4  col-sm-2 col-xs-6">
						<label class="control-label">To Date</label>
					      <div class=" ">
							<div class=" ">
								<input class="form-control datepicker" type="text" id="to_date" name="to_date" value="<?php if(isset($to_date)) echo (($to_date!=null && $to_date!='')?$to_date:'');  ?>" readonly />
							</div>
						</div>
					</div>
					<div class=" col-md-3 col-sm-2 col-xs-6">
						<label class="control-label">Type</label>
						<div class=" ">
							<div class=" ">
								<select class="form-control valid" id="type" name="type" aria-invalid="false">
									<option value="Pending" <?=($type=='Pending'?'selected':'');?>>Pending</option>
									<option value="ALL" <?=($type=='ALL'?'selected':'');?>>ALL</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2 col-sm-2 col-xs-6 "> 
						<label class="control-label"> </label>
						<div class="btn-container ">
							<input type="submit" class="form-control btn btn-success" id="submit" name="submit" value="submit" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	
	<div id="report">
		<div class="form-group">
			<div class="col-md-12"> 
				<table id="example" class="table datatable table-bordered display" cellspacing="0" width="100%" style="display: none">
									<thead> 
										<tr>
											<th>Date</th> 
											<th>Invoice Number</th> 
											<th>Opening Amount</th>
											<th>Pending Amount</th> 
											<th>Due On</th> 
											<th>Overdue by days</th> 
										</tr>  
									</thead>
					</table>
					<table id="example2" class="table datatable table-bordered display" cellspacing="0" width="94%" style="display: none">
											<thead> 
												<tr> 
													<th>Date</th> 
													<th>Invoice Number</th> 
													<th>Detail</th>
													<th>GO Number</th> 
													<th>GRN Number</th> 
													<th>Debit Note Number</th>
													<th>Ref No / Cheque No</th> 
													<th>Amount</th> 
													<th>Opening Amount</th> 
													<th>Pending Amount</th>
													<th>Due On</th>
													<th>Overdue By days</th> 
												</tr>  
											</thead>
						</table>
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
    // $this->registerJsFile(
    //     '@web/js/datatable.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
?>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script> 
<script type="text/javascript">
$("#vouchertype").select2();
$("#account").select2();
$("#state").select2();
</script>
<script type="text/javascript">
	var csrfToken = $('meta[name="csrf-token"]').attr("content");
	$(document.body).on("change",".group",function(){
		if($(this).val()!=null)
		{	
			$("#target").val($("#target option:first").val());

			$.ajax({
	        url: BASE_URL+'index.php?r=accreport%2Fgetledger_name',
	        type: 'post',
	        data: {
	                group : $(this).val(),
		                _csrf : csrfToken
		             },
		        success: function (data) {
		        	var len  = data.length;
		        	var option;
		        	if(len>0)
		        	{
		        		$('#ledger_name').empty();
			        	var obj = JSON.parse(data);
			        	option+="<option value='ALL'>ALL</option>";
			            $.each(obj, function(i, val){
						    console.log(val.legal_name);
						    option+="<option value='"+val.id+"'>"+val.legal_name+"</option>";
						});	

			            $('#ledger_name').append(option).select2();
		        	}
		        },
		        error: function (xhr, ajaxOptions, thrownError) {
		            alert(xhr.status);
		            alert(thrownError);
		        }
	    	});
		}
	});



	$(document).ready(function(){
		$('#ledger_name').select2();

		$('#ledger_name').change(function(){
			if($(this).val()=='ALL' && $('#account').val()!='ALL')
			{
				$("#ledger_name > option").prop("selected","selected");
        		$("#ledger_name").trigger("change");
			}
		});

		$('.loading').fadeIn(1000); 
    	$('#loader').fadeOut(400);
    	$('.datepicker').datepicker({changeMonth: true,changeYear: true});

    	if($('#from_date').val()==""){
	        change_date_criteria();
	    }

	    function change_date_criteria(){
	    if($("#date_criteria").val()=="By Date"){
	        $('#from_date').val("");
	        $('#to_date').val("");
	    } else {
	        var today = new Date();
	        var curMonth = today.getMonth()+1;
	        var curYear = today.getFullYear();
	        var from_date = "01/04/";
	        var to_date = "31/03/";

	        // console.log(today);
	        // console.log(curMonth);
	        // console.log(curYear);

	        if (parseInt(curMonth) > 3) {
	            from_date = from_date + curYear;
	            to_date = to_date + (curYear+1);
	        } else {
	            from_date = from_date + (curYear-1);
	            to_date = to_date + curYear;
	        }
	        $('#from_date').val(from_date);
	        $('#to_date').val(to_date);
	    }
	}


    	if($('#ledger_name').val()!=null && $('#view').val()=='Summary')
    	{
    		$('#example').show();
    		$('#example2').hide();
    		$('#example').DataTable({
		        "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		        "buttons": [ 'excel', 'csv', 'print'  ],
		        "dom" : 'lBfrtip',
		        "order": [[ 0, "desc" ]],
		        "searchDelay": 3000,
		        /*"serverSide": true,
		        "bProcessing": true,*/
		        "ajax":{
		                    url :BASE_URL+'index.php?r=accreport%2Fgetinvoice_detail',
		                    type: "post",  // type of method  ,GET/POST/DELETE
		                    data: function(data) {
		                        data._csrf = csrfToken;
                                data.group=$('#account').val();
                                data.ledger_name = $('#ledger_name').val();
                                data.view = $('#view').val();
                                data.from_date = $('#from_date').val();
                                data.to_date = $('#to_date').val();
                                data.type = $('#type').val();	
		                    },
		                    "dataSrc": function ( json ) {
		                        $('.tab1primary').empty().append("Not Posted (" +json.recordsTotal+")" );
		                        return json.data;
		                     } ,                      
		                    error: function(){
		                        $("#example_processing").css("display","none");
		                    }
		                }
   			});	
    	}
    	else if($('#ledger_name').val()!=null && $('#view').val()=='Detailed')
    	{
    		$('#example').hide();
    		$('#example2').show();
			$('#example2').DataTable({
		        "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		        "buttons": [ 'excel', 'csv', 'print'  ],
		        "dom" : 'lBfrtip',
		        ordering: false,
		        "searchDelay": 3000,
		        /*"serverSide": true,
		        "bProcessing": true,*/
		        "ajax":{
		                    url :BASE_URL+'index.php?r=accreport%2Fgetinvoice_detail',
		                    type: "post",  // type of method  ,GET/POST/DELETE
		                    data: function(data) {
		                        data._csrf = csrfToken;
	                            data.group=$('#account').val();
	                            data.ledger_name = $('#ledger_name').val();
	                            data.view = $('#view').val();
	                            data.from_date = $('#from_date').val();
	                            data.to_date = $('#to_date').val();
	                            data.type = $('#type').val();	
		                    },
		                    "dataSrc": function ( json ) {
		                        $('.tab1primary').empty().append("Not Posted (" +json.recordsTotal+")" );
		                        return json.data;
		                     } ,                      
		                    error: function(){
		                        $("#example_processing").css("display","none");
		                    }
		                }
				});		
    	}
		
    

	});
</script>
