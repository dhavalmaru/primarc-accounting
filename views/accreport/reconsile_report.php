<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;

// use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reconciliation Report';
$this->params['breadcrumbs'][] = $this->title;
$mycomponent = Yii::$app->mycomponent;
?>

<style>
	.form-horizontal .checkbox, .form-horizontal .radio { padding:0;  margin:0; min-height:auto; line-height:20px;}
	.checkbox input[type=checkbox], .checkbox-inline input[type=checkbox], .radio input[type=radio], .radio-inline input[type=radio] { position:relative; margin:0;}
	.table>thead>tr>th { vertical-align: middle;  border-bottom: 2px solid #ddd; }
	.checkbox, .radio { margin:0; padding:0; }
	.bold-text { background-color: #f1f1f1; text-align:right; }
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
</style>

<div class="grn-index"> 
	<div class=" col-md-12 "> 
		<form id="reconsilation_form" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=accreport%2Fsave" method="post" enctype="multipart/form-data">
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" /> 
			<input type="hidden" id="form_val" name="form_val" value="true" /> 

		<!-- <form id="ledger_report" class="form-horizontal" action="<?php //echo Url::base(); ?>index.php?r=accreport%2Fgetreconsile" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;">  -->
			<!-- <input type="hidden" name="_csrf" value="<?//=Yii::$app->request->getCsrfToken()?>" /> -->
			<div id="report_filter">
				<div class="form-group" >
					<div class=" col-md-2 col-sm-2 col-xs-6">
						<label class="control-label">Transaction Type</label>
						<div class=" ">
							<div class=" "> 
								<select class="form-control" id="date_criteria" name="date_criteria">
									<option value="Financial Year" <?=($date_criteria=='Financial Year'?'selected':'')?> >Financial Year</option>
									<option value="By Date" <?=($date_criteria=='By Date'?'selected':'')?> >By Date</option>
								</select>
							</div>
						</div>
					</div>
					<div class=" col-md-2 col-sm-2 col-xs-6">
						<label class="control-label">From Date</label>
						<div class=" ">
							<div class=" ">
								<input class="form-control datepicker" type="text" id="from_date" name="from_date" value="<?php if(isset($from_date)) echo (($from_date!=null && $from_date!='')?date('d/m/Y',strtotime($from_date)):''); ?>"  autocomplete="off"/>
							</div>
						</div>
					</div>
					<div class=" col-md-2 col-sm-2 col-xs-6">
						<label class="control-label">To Date</label>
					      <div class=" ">
							<div class=" ">
								<input class="form-control datepicker" type="text" id="to_date" name="to_date" value="<?php if(isset($to_date)) echo (($to_date!=null && $to_date!='')?date('d/m/Y',strtotime($to_date)):date('d/m/Y')); ?>"  autocomplete="off"/>
							</div>
						</div>
					</div>
			  
					<div class=" col-md-3 col-sm-2 col-xs-6">
						<label class="control-label">Account Name</label>
						<div class=" ">
							<div class=" ">
								<?php $legal_name = ''; ?>
								<select class="form-control select2" id="account" name="account">
									<option value="">Select</option>
									<?php for($i=0; $i<count($bank); $i++) { ?>
									<option value="<?php echo $bank[$i]['id']; ?>" 
									<?php if(isset($account)) { if($bank[$i]['id']==$account) { echo 'selected'; $legal_name = $bank[$i]['legal_name']; } } ?> > 
									<?php echo $bank[$i]['legal_name']; ?>
									</option>
									<?php  } ?>	
								</select>
							</div>
						</div>
					</div>
					<div class=" col-md-3 col-sm-2 col-xs-6">
						<label class="control-label">View</label>
						<div class=" ">
							<div class=" ">
								<select class="form-control" id="view" name="view">
									<option value="default" <?=($view=='default'?'selected':'')?>>Default</option>
									<option value="show_reconsiled" <?=($view=='show_reconsiled'?'selected':'')?>>Show Reconsiled</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group" >	
					<div class="col-md-2 col-sm-2 col-xs-6 "> 
						<label class="control-label"> </label>
						<div class="btn-container ">
							<input type="submit" class="form-control btn btn-success" id="generate" name="submit" value="Generate Report" onClick="set_form_val(this);" />
						</div>
					</div>
				</div>
			</div>
		<!-- </form> -->

			<div id="report">
				<div id="company_name" class="text-center">
					<div class="form-group">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<label class="text-center" id="lbl_company_name">Primarc Pecan Retail Pvt Ltd</label>
						</div>
					</div>
					<div class="form-group" style="<?php if($legal_name=='') echo 'display: none;'; ?>">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<label class="text-center" id="lbl_legal_name"><?php echo $legal_name; ?></label>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-3 col-sm-3 col-xs-12">
							<label id="lbl_trans_type">Trans Type: <?=($date_criteria=='By Date'?'By Date':'Financial Year')?></label>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12">
							<label id="lbl_from_date">From Date: <span id="from_date_span"><?php if(isset($from_date)) echo (($from_date!=null && $from_date!='')?date('d/m/Y',strtotime($from_date)):''); ?></span> </label>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12">
							<label id="lbl_to_date">To Date: <?php if(isset($to_date)) echo (($to_date!=null && $to_date!='')?date('d/m/Y',strtotime($to_date)):date('d/m/Y')); ?></label>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-12">
							<label id="lbl_view">View: <?=($view=='show_reconsiled'?'Show Reconsiled':'Default')?></label>
						</div>
					</div>
				</div>
				<div class="container">
					<div class="row">
						<div class="col-md-2">
							<label>Date</label>
							<input class="form-control datepicker" type="text" id="compare_date" name="" value="<?php if(isset($from_date)) echo (($from_date!=null && $from_date!='')?date('d/m/Y',strtotime($from_date)):date('d/m/Y')); ?>" autocomplete="off" />
						</div>
						<div class="col-md-2">
						   	<div class=" btn-container ">
		                    <button type="button" class="btn btn-danger" id="pb1" style="margin-top: 22px;"> Delete</button>
							<button type="button" class="btn btn-danger" id="copy" style="margin-top: 22px;">Copy</button>
							</div>
						</div>
					</div>
				</div>
				<div id="loader"> </div>
				<div class="loading">
					<!-- <form id="reconsilation_form" class="form-horizontal" action="<?php //echo Url::base(); ?>index.php?r=accreport%2Fsave" method="post" enctype="multipart/form-data"> -->
						<!-- <input type="hidden" name="_csrf" value="<?//=Yii::$app->request->getCsrfToken()?>" /> -->
						<div class="form-group">
						<div class="col-md-12"> 
							<table id="example" class="table table-bordered display">
								<thead>
									<tr>
										<th class="">
											<div class="  ">
												<input type="checkbox" id="check_all" value="" />
											</div>
										</th>
										<th class="text-center"> Sr No </th>
										<th class="text-center"> Ref ID (Voucher No) </th>
										<th class="text-center"> Date </th>
										<th class="text-center"> Ledger Name </th>
										<th class="text-center"> Ref 1 </th>
										<th class="text-center"> Payment Date </th>
										<th class="text-center"> Debit </th>
										<th class="text-center"> Credit </th>
										<!-- <th class="text-center"> Balance Value </th> -->
										<th class="text-center"> Balance Type </th>
									</tr>
								</thead>
									<?php
								        $balance = $opening_bal;
								        $debit_amt = 0;
								        $credit_amt = 0;
								        $cur_total = 0;
								        if(isset($data)){
									        if(count($data)>0){
									            for($i=0; $i<count($data); $i++){
									                $ledger_code = '';
									                $ledger_name = '';

									                if($data[$i]['type']=='Debit'){
									                    $entry_type = 'Dr';
									                    $debit_amt = floatval($data[$i]['amount']);
									                    $balance = round($balance - $debit_amt,2);
									                    $credit_amt = '';
									                    $cur_total = round($cur_total - $debit_amt,2);
									                } else {
									                    $entry_type = 'Cr';
									                    $credit_amt = floatval($data[$i]['amount']);
									                    $balance = round($balance + $credit_amt,2);
									                    $debit_amt = '';
									                    $cur_total = round($cur_total + $credit_amt,2);
									                }
									                if($balance<0){
									                    $balance_type = 'Dr';
									                    $balance_val = $balance * -1;
									                } else {
									                    $balance_type = 'Cr';
									                    $balance_val = $balance;
									                }
									                if(isset($data[$i]['cp_acc_id'])){
									                    if($data[$i]['cp_acc_id']!=$account){
									                        $ledger_code = $data[$i]['cp_ledger_code'];
									                        $ledger_name = $data[$i]['cp_ledger_name'];
									                    }
									                }
									                if($ledger_code == ''){
									                    $ledger_code = $data[$i]['ledger_code'];
									                    $ledger_name = $data[$i]['ledger_name'];
									                }

									                $payment_date = $data[$i]['payment_date'];	
									                $is_checked = '';

									                if($payment_date==NULL || $payment_date=="")
										                {
										                		$payment_date = '';
										                }
										                else
										                {
										                	$is_checked = 'checked';
										                	$payment_date = date('d/m/Y',strtotime($payment_date));
										                }

									                echo '<tr class="hello">
									                		<td class="">
									                		<input type="checkbox"  class="reconsile check" value="'.$data[$i]['id'].'" id="reconsile_date_'.$i.'"  />
									                		<input type="hidden" name="reconsile[]" value="'.$data[$i]['id'].'" >
									                		&nbsp;</td>
						                                    <td>'.($i+1).'</td>
						                                    <td>'.$data[$i]['voucher_id'].'</td>
						                                    <td class="ref_date">'.(($data[$i]['ref_date']!=null && $data[$i]['ref_date']!="")?date("d/m/Y",strtotime($data[$i]['ref_date'])):"").'</td>
						                                    <td>'.$ledger_name.'</td>
						                                    <td>'.$data[$i]['ref_id'].'</td>
						                                    <td><input class="form-control datepicker payment_date" type="text" name="payment_date[]" id="payment_date_'.$i.'" value="'.$payment_date.'" onchange="reconsile_amount()" autocomplete="off"/><span style="display: none;">'.$payment_date.'</span></td>
						                                    <td style="text-align:right;" class="debit_amt" debit_amt="'.$debit_amt.'">'.$mycomponent->format_money($debit_amt,2).'</td>
						                                    <td style="text-align:right;"  class="credit_amt"  credit_amt="'.$credit_amt.'">'.$mycomponent->format_money($credit_amt,2).'</td>
						                                    
						                                     <td> </td>
					                                  	</tr>';
									            }
									        }
									    }
									    //<td style="text-align:right;">'.$mycomponent->format_money($balance_val,2).'</td>

								        if($balance<0){
								            $balance_type = 'Dr';
								            $balance_val = $balance * -1;
								        } else {
								            $balance_type = 'Cr';
								            $balance_val = $balance;
								        }

								        if($cur_total<0){
								            $cur_total_type = 'Dr';
								            $cur_total_val = $cur_total * -1;
								        } else {
								            $cur_total_type = 'Cr';
								            $cur_total_val = $cur_total;
								        }
							        ?>
							        <?php 
										if(isset($opening_bal)){
											if($opening_bal<0){
									            $opening_bal = $opening_bal*-1;
									            $opening_bal_type = 'Dr';
									        } else {
									            $opening_bal_type = 'Cr';
									        }
										} else {
											$opening_bal = 0;
											$opening_bal_type = 'Cr';
										}
								    ?>
								    <tr>
										<td>&nbsp;</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
				                  	</tr>
		                          	<tr>
			                            <td></td>
			                            <td></td>
			                            <td></td>
			                            <td></td>
			                            <td></td>
			                            <td> <input type="hidden" id="total_as_per_book" value="<?php if(isset($ledg_balance)) echo $ledg_balance; ?>" /></td>
			                            <td>Total As per Book Total</td>

			                            <!-- <td style="text-align:right;"><?php //echo (($cur_total < 0)?$mycomponent->format_money($cur_total_val,2):" "); ?></td>
			                            <td style="text-align:right;"><?php //echo (($cur_total >= 0)?$mycomponent->format_money($cur_total_val,2):" "); ?></td>
			                            <td><?php //echo $cur_total_type; ?></td> -->

			                            <?php 
											if(isset($ledg_balance)){
												if($ledg_balance<0){
										            $ledg_balance = $ledg_balance*-1;
										            $ledg_balance_type = 'Dr';
										        } else {
										            $ledg_balance_type = 'Cr';
										        }
											} else {
												$ledg_balance = 0;
												$ledg_balance_type = 'Cr';
											}
									    ?>

			                            <td style="text-align:right;" id="total_as_per_book_debit"><?php echo (($ledg_balance < 0)?$mycomponent->format_money($ledg_balance,2):" "); ?></td>
			                            <td style="text-align:right;" id="total_as_per_book_credit"><?php echo (($ledg_balance >= 0)?$mycomponent->format_money($ledg_balance,2):" "); ?></td>
			                            <td><?php echo $ledg_balance_type; ?></td>
		                          	</tr>
		                          	<tr>
			                            <td></td>
			                            <td></td>
			                            <td></td>
			                            <td></td>
			                            <td></td>
			                            <td></td>
			                            <td>Difference</td>
			                            <td style="text-align:right;" id="difference_bal_debit">0.00</td>
			                            <td style="text-align:right;" id="difference_bal_credit">0.00</td>
			                            <td id="difference_type"></td>
		                          	</tr>	
		                          	<tr>
			                            <td></td>
			                            <td></td>
			                            <td></td>
			                            <td></td>
			                            <td></td>
			                            <td id=""></td>
			                            <td>Total as per bank</td>
			                            <td style="text-align:right;" id="asperbank_debit">0.00</td>
			                            <td style="text-align:right;" id="asperbank_credit">0.00</td>
			                            <td id="asperbanktype"></td>
		                          	</tr>
		                          	
									</tbody>
							</table>
						</div>
						<div class=" btn-container ">
		                    <!-- <button type="submit" class="btn btn-success btn-sm" id="btn_submit">Submit For Approval  </button> -->
		                    <input type="submit" name="submit" value="submit" class="btn btn-success btn-sm" style="margin-top: 25px;float: right;" onClick="set_form_val(this);" />
						</div>
					</div>
					<!-- </form> -->
				</div>
			</div>

		</form>
	</div>
</div>

<script>
	var BASE_URL="<?php echo Url::base(); ?>";
	var csrfToken = "<?=Yii::$app->request->getCsrfToken()?>";
</script>

<!--  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		reconsile_amount();
		$("#pb1").click(function(){
		var count = $(".payment_date").length;
		var compare_date=$("#compare_date").val();
		var payment_date=$(".payment_date").val();

		for(var i=0;i<count;i++)
		{
			
		  if(compare_date==($("#payment_date_"+i).val()))
			{
				//$("#payment_date_"+i).val('');
				var ifchecked = $("#payment_date_"+i).closest('tr').children('td').find('.reconsile').is(":checked");
				if(ifchecked==true)
				{
					$("#payment_date_"+i).val('');
					$("#payment_date_"+i).closest('tr').children('td').find('.reconsile').prop("checked",false);
				}
			}				
		}
		reconsile_amount();
	});

	$("#copy").click(function(){
		var count = $(".reconsile").length;
		var compare_date=$("#compare_date").val();

		for(var i=0;i<count;i++)
		{
			var ifchecked = $("#reconsile_date_"+i).closest('tr').children('td').find('.reconsile').is(":checked");

			if(ifchecked)
			{
				$("#payment_date_"+i).val(compare_date);
			}
								
		}

		reconsile_amount();
	});

	
  	$(".payment_date").on('change', function(){

  		var date = $(this).val();

		if(date!="")
		{
			$(this).closest('tr').children('td').find('.reconsile').prop("checked",true);
		}
		else
		{
			$(this).closest('tr').children('td').find('.reconsile').prop("checked",false);
		}
    });
});
</script>

<script type="text/javascript">
	var reconsile_amount = function() {
    var count = $(".payment_date").length;
    var total = $("#total_as_per_book").val();
    total = get_number(total);
    var credit = 0;
    var debit = 0;

    for(var i=0;i<count;i++)
    {
        paymentdate = $("#payment_date_"+i);
        if(paymentdate.val()!="" && paymentdate.val()!=undefined)
        {
            var debit_amt = paymentdate.closest('tr').children('td.debit_amt').text();
            var credit_amount = paymentdate.closest('tr').children('td.credit_amt').text();
            debit_amt = get_number(debit_amt);
            credit_amount = get_number(credit_amount);
            
            debit = debit+debit_amt;
            
            credit = credit+credit_amount;  
            
        }
    }
    /*console.log('debit'+debit)+"<br>";
    console.log('credit'+credit)+"<br>";    */
   	var differnce='';
   	var differnce_type='';
    if(credit>debit) {
        var differnce = (credit-debit);
        var differnce_type = "Cr";
    } else {
        var differnce = (debit-credit);
        var differnce_type = "Dr";
    }

    $("#difference_type").text(differnce_type); 
    $("#difference_bal").text(format_money(Math.abs(differnce),2));

    var ajaxval = 0;
    var csrfToken = "<?//=Yii::$app->request->getCsrfToken()?>";
    console.log(csrfToken);
    $.ajax({
            url: BASE_URL+'index.php?r=accreport%2Fgetasperbank',
            data: 'account='+$("#account").val()+"&_csrf="+csrfToken+'&from_date='+$("#from_date").val()+'&to_date='+$("#to_date").val(),
            type: "POST",
            dataType: 'html',
            global: false,
            async: false,
            success: function (data) {
                result = parseInt(data);
                ajaxval = result;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    console.log('ajaxval'+ajaxval);



    if(differnce_type=="Dr")
    {
        ajaxval = ajaxval-differnce;
    }
    else
    {
        ajaxval =  ajaxval+differnce;
    }


    if(ajaxval>0)
    {
        var ajaxtype = "Cr";
    }
    else
    {
        ajaxval=(ajaxval*-1);
        var ajaxtype = "Dr";
    }

    $("#asperbanktype").text(ajaxtype); 
    $("#asperbank").text(format_money(ajaxval,2));
}

var format_money = function(num, decimals){
    if(num==null || num==""){
        num="";
    }
    num = num.toString().replace(/[^0-9]/g,'');
    var x=num;
    x=x.toString();
    x = x.split(",").join("");
    var lastThree = x.substring(x.length-3);
    var otherNumbers = x.substring(0,x.length-3);
    if(otherNumbers != '') lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
    return res;
}

var get_number = function(num, decimals){
    if(num==null || num==""){
        num="0";
    }
    res = parseFloat(num.replaceAll(",",""));
    return res;
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

var format_number = function(elem){
    var res = format_money(elem.value,2);
    $(elem).val(res);
}


$(document).ready(function(){
    $("#pb1").click(function(){
        var count = $(".payment_date").length;
        var compare_date=$("#compare_date").val();
        var payment_date=$(".payment_date").val();

        for(var i=0;i<count;i++)
        {
            
          if(compare_date==($("#payment_date_"+i).val()))
            {
                //$("#payment_date_"+i).val('');
                var ifchecked = $("#payment_date_"+i).closest('tr').children('td').find('.reconsile').is(":checked");
                if(ifchecked==true)
                {
                    $("#payment_date_"+i).val('');
                    $("#payment_date_"+i).closest('tr').children('td').find('.reconsile').prop("checked",false);
                }
            }               
        }
        reconsile_amount();
    });

    $("#copy").click(function(){
        var count = $(".reconsile").length;
        var compare_date=$("#compare_date").val();

        for(var i=0;i<count;i++)
        {
            var ifchecked = $("#reconsile_date_"+i).closest('tr').children('td').find('.reconsile').is(":checked");

            if(ifchecked)
            {
                $("#payment_date_"+i).val(compare_date);
            }
                                
        }

        reconsile_amount();
    });

    
    $(".payment_date").on('change', function(){

        var date = $(this).val();

        if(date!="")
        {
            $(this).closest('tr').children('td').find('.reconsile').prop("checked",true);
        }
        else
        {
            $(this).closest('tr').children('td').find('.reconsile').prop("checked",false);
        }
    });
});
</script> -->

<?php 
	$this->registerJsFile(
	    '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
	    ['depends' => [\yii\web\JqueryAsset::className()]]
	);
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

<?php 
    $this->registerJsFile(
        '@web/js/reconsile.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>