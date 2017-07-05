<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;

$this->title = 'Account Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
input:-webkit-autofill {
    background-color: white !important;
}
select {
	width: 100%;
}
.form-horizontal .control-label { font-size: 12px; letter-spacing: .5px; margin-top:5px; }
.form-devident { margin-top: 10px; }
.form-devident h4 { border-bottom: 1px dashed #ddd; padding-bottom: 10px; }
.download_file {display: block;}
</style>
<div class="grn-index">
	<div class=" col-md-12">  
		<form id="account_master" class="form-horizontal" action="<?php echo Url::base(); ?>index.php?r=accountmaster%2Fsave" method="post" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;"> 
			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			<div class="form-group">
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Type</label>
					<input type="hidden" id="action" name="action" value="<?php if(isset($action)) echo $action; ?>">
					<input type="hidden" id="id" name="id" value="<?php if(isset($data)) echo $data[0]['id']; ?>" />
					<input type="hidden" id="status" name="status" value="<?php if(isset($data)) echo $data[0]['status']; ?>" />
					<input type="hidden" id="type_val" name="type_val" value="<?php if(isset($data)) echo $data[0]['type']; ?>" />
					<select class="form-control" id="type" name="type" <?php if(isset($data)) echo 'disabled'; ?>>
						<option value="">Select</option>
						<option value="Vendor Goods" <?php if(isset($data)) { if($data[0]['type']=="Vendor Goods") echo "selected"; } ?>>Vendor Goods</option>
						<option value="Vendor Expenses" <?php if(isset($data)) { if($data[0]['type']=="Vendor Expenses") echo "selected"; } ?>>Vendor Expenses</option>
						<option value="Bank Account" <?php if(isset($data)) { if($data[0]['type']=="Bank Account") echo "selected"; } ?>>Bank Account</option>
						<option value="Goods Purchase" <?php if(isset($data)) { if($data[0]['type']=="Goods Purchase") echo "selected"; } ?>>Goods Purchase</option>
						<option value="Tax" <?php if(isset($data)) { if($data[0]['type']=="Tax") echo "selected"; } ?>>Tax</option>
						<option value="CGST" <?php if(isset($data)) { if($data[0]['type']=="CGST") echo "selected"; } ?>>CGST</option>
						<option value="SGST" <?php if(isset($data)) { if($data[0]['type']=="SGST") echo "selected"; } ?>>SGST</option>
						<option value="IGST" <?php if(isset($data)) { if($data[0]['type']=="IGST") echo "selected"; } ?>>IGST</option>
						<option value="Goods Sales" <?php if(isset($data)) { if($data[0]['type']=="Goods Sales") echo "selected"; } ?>>Goods Sales</option>
						<option value="Employee" <?php if(isset($data)) { if($data[0]['type']=="Employee") echo "selected"; } ?>>Employee</option>
						<option value="Others" <?php if(isset($data)) { if($data[0]['type']=="Others") echo "selected"; } ?>>Others</option>
					</select>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Legal Name</label>
					<select id="vendor_id" name="vendor_id" class="form-control" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods') echo 'display: none;'; } else { echo 'display: none;'; } ?>">
						<option value="">Select</option>
						<?php for($i=0; $i<count($vendor); $i++) { ?>
							<option value="<?php echo $vendor[$i]['id']; ?>" <?php if(isset($data[0])) { if($data[0]['vendor_id']==$vendor[$i]['id']) echo "selected"; } ?>><?php echo $vendor[$i]['vendor_name']; ?></option>
						<?php } ?>
					</select>
					<input id="legal_name" name="legal_name" class="form-control" type="text" value="<?php if(isset($data[0])) echo $data[0]['legal_name']; ?>" style="<?php if(isset($data[0])) { if($data[0]['type'] == 'Vendor Goods') echo 'display: none;'; } ?>" />
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Code</label>
					<input id="code" name="code" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['code']; ?>" style="<?php if(isset($data[0])) { if($data[0]['type'] == 'Vendor Goods') echo 'display: none;'; } ?>" readonly />
					<input id="vendor_code" name="vendor_code" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['code']; ?>" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods') echo 'display: none;'; } else { echo 'display: none;'; } ?>" readonly />
				</div>
				<div class=" col-md-3 col-sm-3 col-xs-6 vendor_goods" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<label class="control-label">Type Of Company</label>
					<input id="legal_entity_name" name="legal_entity_name" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['legal_entity_name']; ?>" readonly />
				</div>
				<div class=" col-md-3 col-sm-3 col-xs-6 employee" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Employee') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<label class="control-label">Department</label>
					<input id="department" name="department" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['department']; ?>" readonly />
				</div>
			</div>

		 	<div class="form-group">
			  	<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Account Type</label>
					<select class="form-control" id="account_type" name="account_type">
						<option value="">Select</option>
						<option value="Income" <?php if(isset($data)) { if($data[0]['account_type']=="Income") echo "selected"; } ?>>Income</option>
						<option value="Expense" <?php if(isset($data)) { if($data[0]['account_type']=="Expense") echo "selected"; } ?>>Expense</option>
						<option value="Asset" <?php if(isset($data)) { if($data[0]['account_type']=="Asset") echo "selected"; } ?>>Asset</option>
						<option value="Liability" <?php if(isset($data)) { if($data[0]['account_type']=="Liability") echo "selected"; } ?>>Liability</option>
					</select>
				</div>
	         	<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Details</label>
					<input id="details" name="details" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['details']; ?>" />
				</div>
	         	<div class="col-md-3 col-sm-3 col-xs-6 vendor_goods">
					<label class="control-label">GSTIN</label>
					<input id="gst_id" name="gst_id" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['gst_id']; ?>" readonly />
				</div>
	         	<div class="col-md-3 col-sm-3 col-xs-6 vendor_expenses employee" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Expenses' && $data[0]['type'] != 'Employee') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<label class="control-label">Expences Type</label>
					<input id="expense_type" name="expense_type" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['expense_type']; ?>" />
				</div>
				<div class=" col-md-3 col-sm-3 col-xs-6 vendor_expenses employee" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Expenses' && $data[0]['type'] != 'Employee') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<label class="control-label">Location</label>
					<input id="location" name="location" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['location']; ?>" />
				</div>
			</div>
			
			<div class="form-group">
				<div class="vendor_goods vendor_expenses employee" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods' && $data[0]['type'] != 'Vendor Expenses' && $data[0]['type'] != 'Employee') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Addres</label>
						<input id="address" name="address" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['address']; ?>" />
					</div>
					<div class="col-md-2 col-sm-2 col-xs-4">
						<label class="control-label">&nbsp;</label>
	                    <!-- <input type="hidden" class="form-control" name="address_doc_name" value="<?php //if(isset($data)) echo $data[0]['address_doc_name']; ?>" /> -->
	                    <input type="hidden" class="form-control" id="address_doc_path" vname="address_doc_path" value="<?php if(isset($data)) echo $data[0]['address_doc_path']; ?>" />
	                    <input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="address_doc_file" id="address_doc_file" data-error="#address_doc_file_error"/>
	                    <div id="address_doc_file_error"></div>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2">
						<label class="control-label">&nbsp;</label>
	                    <?php if(isset($data)) { if($data[0]['address_doc_path']!= '') { ?><a target="_blank" id="address_doc_file_download" class="download_file" href="<?php if(isset($data)) echo Url::base().$data[0]['address_doc_path']; ?>">
	                    <span class="fa download fa-download" ></span></a><?php }} ?>
					</div>
				</div>
				<div class="vendor_goods vendor_expenses employee" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods' && $data[0]['type'] != 'Vendor Expenses' && $data[0]['type'] != 'Employee') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Pan No</label>
						<input id="pan_no" name="pan_no" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['pan_no']; ?>" />
					</div>
					<div class="col-md-2 col-sm-2 col-xs-4">
						<label class="control-label">&nbsp;</label>
	                    <!-- <input type="hidden" class="form-control" name="pan_no_doc_name" value="<?php //if(isset($data)) echo $data[0]['pan_no_doc_name']; ?>" /> -->
	                    <input type="hidden" class="form-control" id="pan_no_doc_path" name="pan_no_doc_path" value="<?php if(isset($data)) echo $data[0]['pan_no_doc_path']; ?>" />
	                    <input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="pan_no_doc_file" id="pan_no_doc_file" data-error="#pan_no_doc_file_error"/>
	                    <div id="pan_no_doc_file_error"></div>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2">
						<label class="control-label">&nbsp;</label>
	                    <?php if(isset($data)) { if($data[0]['pan_no_doc_path']!= '') { ?><a target="_blank" id="pan_no_doc_file_download" class="download_file" href="<?php if(isset($data)) echo Url::base().$data[0]['pan_no_doc_path']; ?>">
	                    <span class="fa download fa-download" ></span></a><?php }} ?>
					</div>
				</div>
			</div>

			<div class="form-group vendor_expenses employee" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Expenses' && $data[0]['type'] != 'Employee') echo 'display: none;'; } else echo 'display: none;'; ?>">
				<div class="employee" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Employee') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Aadhar Card No</label>
						<input id="aadhar_card_no" name="aadhar_card_no" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['aadhar_card_no']; ?>" />
					</div>
					<div class="col-md-2 col-sm-2 col-xs-4">
						<label class="control-label">&nbsp;</label>
	                    <!-- <input type="hidden" class="form-control" name="aadhar_card_no_doc_name" value="<?php //if(isset($data)) echo $data[0]['aadhar_card_no_doc_name']; ?>" /> -->
	                    <input type="hidden" class="form-control" id="aadhar_card_no_doc_path" name="aadhar_card_no_doc_path" value="<?php if(isset($data)) echo $data[0]['aadhar_card_no_doc_path']; ?>" />
	                    <input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="aadhar_card_no_doc_file" id="aadhar_card_no_doc_file" data-error="#aadhar_card_no_doc_file_error"/>
	                    <div id="aadhar_card_no_doc_file_error"></div>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2">
						<label class="control-label">&nbsp;</label>
	                    <?php if(isset($data)) { if($data[0]['aadhar_card_no_doc_path']!= '') { ?><a target="_blank" id="aadhar_card_no_doc_file_download" class="download_file" href="<?php if(isset($data)) echo Url::base().$data[0]['aadhar_card_no_doc_path']; ?>">
	                    <span class="fa download fa-download" ></span></a><?php }} ?>
					</div>
				</div>
				<div class="vendor_expenses" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Expenses') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Service Tax No</label>
						<input id="service_tax_no" name="service_tax_no" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['service_tax_no']; ?>" />
					</div>
					<div class="col-md-2 col-sm-2 col-xs-4">
						<label class="control-label">&nbsp;</label>
	                    <!-- <input type="hidden" class="form-control" name="service_tax_no_doc_name" value="<?php //if(isset($data)) echo $data[0]['service_tax_no_doc_name']; ?>" /> -->
	                    <input type="hidden" class="form-control" id="service_tax_no_doc_path" name="service_tax_no_doc_path" value="<?php if(isset($data)) echo $data[0]['service_tax_no_doc_path']; ?>" />
	                    <input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="service_tax_no_doc_file" id="service_tax_no_doc_file" data-error="#service_tax_no_doc_file_error"/>
	                    <div id="service_tax_no_doc_file_error"></div>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2">
						<label class="control-label">&nbsp;</label>
	                    <?php if(isset($data)) { if($data[0]['service_tax_no_doc_path']!= '') { ?><a target="_blank" id="service_tax_no_doc_file_download" class="download_file" href="<?php if(isset($data)) echo Url::base().$data[0]['service_tax_no_doc_path']; ?>">
	                    <span class="fa download fa-download" ></span></a><?php }} ?>
					</div>
				</div>
				<div class="vendor_expenses" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Expenses') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Agreement Details</label>
						<input id="agreement_details" name="agreement_details" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['agreement_details']; ?>" />
					</div>
					<div class="col-md-2 col-sm-2 col-xs-4">
						<label class="control-label">&nbsp;</label>
	                    <!-- <input type="hidden" class="form-control" name="agreement_details_doc_name" value="<?php //if(isset($data)) echo $data[0]['agreement_details_doc_name']; ?>" /> -->
	                    <input type="hidden" class="form-control" id="agreement_details_doc_path" name="agreement_details_doc_path" value="<?php if(isset($data)) echo $data[0]['agreement_details_doc_path']; ?>" />
	                    <input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="agreement_details_doc_file" id="agreement_details_doc_file" data-error="#agreement_details_doc_file_error"/>
	                    <div id="agreement_details_doc_file_error"></div>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2">
						<label class="control-label">&nbsp;</label>
	                    <?php if(isset($data)) { if($data[0]['agreement_details_doc_path']!= '') { ?><a target="_blank" id="agreement_details_doc_file_download" class="download_file" href="<?php if(isset($data)) echo Url::base().$data[0]['agreement_details_doc_path']; ?>">
	                    <span class="fa download fa-download" ></span></a><?php }} ?>
					</div>
				</div>
			</div>

			<div class="form-group" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods' && $data[0]['type'] != 'Vendor Expenses') echo 'display: none;'; } else echo 'display: none;'; ?>">
				<div class="vendor_goods vendor_expenses" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods' && $data[0]['type'] != 'Vendor Expenses') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">Vat/Tin No</label>
						<input id="vat_no" name="vat_no" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['vat_no']; ?>" />
					</div>
					<div class="col-md-2 col-sm-2 col-xs-4">
						<label class="control-label">&nbsp;</label>
	                    <!-- <input type="hidden" class="form-control" name="vat_no_doc_name" value="<?php //if(isset($data)) echo $data[0]['vat_no_doc_name']; ?>" /> -->
	                    <input type="hidden" class="form-control" id="vat_no_doc_path" name="vat_no_doc_path" value="<?php if(isset($data)) echo $data[0]['vat_no_doc_path']; ?>" />
	                    <input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="vat_no_doc_file" id="vat_no_doc_file" data-error="#vat_no_doc_file_error"/>
	                    <div id="vat_no_doc_file_error"></div>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2">
						<label class="control-label">&nbsp;</label>
	                    <?php if(isset($data)) { if($data[0]['vat_no_doc_path']!= '') { ?><a target="_blank" id="vat_no_doc_file_download" class="download_file" href="<?php if(isset($data)) echo Url::base().$data[0]['vat_no_doc_path']; ?>">
	                    <span class="fa download fa-download" ></span></a><?php }} ?>
					</div>
				</div>
				<div class="vendor_expenses" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Expenses') echo 'display: none;'; } else echo 'display: none;'; ?>">
					<div class="col-md-3 col-sm-3 col-xs-6">
						<label class="control-label">PF/ESIC No</label>
						<input id="pf_esic_no" name="pf_esic_no" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['pf_esic_no']; ?>" />
					</div>
					<div class="col-md-2 col-sm-2 col-xs-4">
						<label class="control-label">&nbsp;</label>
	                    <!-- <input type="hidden" class="form-control" name="pf_esic_no_doc_name" value="<?php //if(isset($data)) echo $data[0]['pf_esic_no_doc_name']; ?>" /> -->
	                    <input type="hidden" class="form-control" id="pf_esic_no_doc_path" name="pf_esic_no_doc_path" value="<?php if(isset($data)) echo $data[0]['pf_esic_no_doc_path']; ?>" />
	                    <input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="pf_esic_no_doc_file" id="pf_esic_no_doc_file" data-error="#pf_esic_no_doc_file_error"/>
	                    <div id="pf_esic_no_doc_file_error"></div>
					</div>
					<div class="col-md-1 col-sm-1 col-xs-2">
						<label class="control-label">&nbsp;</label>
	                    <?php if(isset($data)) { if($data[0]['pf_esic_no_doc_path']!= '') { ?><a target="_blank" id="pf_esic_no_doc_file_download" class="download_file" href="<?php if(isset($data)) echo Url::base().$data[0]['pf_esic_no_doc_path']; ?>">
	                    <span class="fa download fa-download" ></span></a><?php }} ?>
					</div>
				</div>
			</div>

			<div class="form-group vendor_goods vendor_expenses bank_account employee" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods' && $data[0]['type'] != 'Vendor Expenses' && $data[0]['type'] != 'Employee' && $data[0]['type'] != 'Bank Account') echo 'display: none;'; } else echo 'display: none;'; ?>">
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Account Number</label>
					<input id="acc_no" name="acc_no" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['acc_no']; ?>" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-4">
					<label class="control-label">&nbsp;</label>
                    <!-- <input type="hidden" class="form-control" name="acc_no_doc_name" value="<?php //if(isset($data)) echo $data[0]['acc_no_doc_name']; ?>" /> -->
                    <input type="hidden" class="form-control" id="acc_no_doc_path" name="acc_no_doc_path" value="<?php if(isset($data)) echo $data[0]['acc_no_doc_path']; ?>" />
                    <input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="acc_no_doc_file" id="acc_no_doc_file" data-error="#acc_no_doc_file_error"/>
                    <div id="acc_no_doc_file_error"></div>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2">
					<label class="control-label">&nbsp;</label>
                    <?php if(isset($data)) { if($data[0]['acc_no_doc_path']!= '') { ?><a target="_blank" id="acc_no_doc_file_download" class="download_file" href="<?php if(isset($data)) echo Url::base().$data[0]['acc_no_doc_path']; ?>">
                    <span class="fa download fa-download" ></span></a><?php }} ?>
				</div>
			</div>

			<div class="form-group vendor_goods vendor_expenses bank_account employee" id="bank_details" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods' && $data[0]['type'] != 'Vendor Expenses' && $data[0]['type'] != 'Employee' && $data[0]['type'] != 'Bank Account') echo 'display: none;'; } else echo 'display: none;'; ?>">
				<div class="col-md-3 col-sm-3 col-xs-6" id="acc_hold_name">
					<label class="control-label">Account Holder Name</label>
					<input id="account_holder_name" name="account_holder_name" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['account_holder_name']; ?>" />
				</div>
				<div class="  col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Bank Name</label>
					<input id="bank_name" name="bank_name" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['bank_name']; ?>" />
				</div>
				<div class=" col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Branch</label>
					<input id="branch" name="branch" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['branch']; ?>" />
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">IFSC Code</label>
					<input id="ifsc_code" name="ifsc_code" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['ifsc_code']; ?>" />
				</div>
			</div>

			<div class="form-group vendor_expenses employee" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Expenses' && $data[0]['type'] != 'Employee') echo 'display: none;'; } else echo 'display: none;'; ?>">
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Others</label>
					<input id="other" name="other" class="form-control" type="text" value="<?php if(isset($data)) echo $data[0]['other']; ?>" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-4">
					<label class="control-label">&nbsp;</label>
                    <!-- <input type="hidden" class="form-control" name="other_doc_name" value="<?php //if(isset($data)) echo $data[0]['other_doc_name']; ?>" /> -->
                    <input type="hidden" class="form-control" id="other_doc_path" name="other_doc_path" value="<?php if(isset($data)) echo $data[0]['other_doc_path']; ?>" />
                    <input  style="padding:1px;" type="file" class="fileinput form-control doc_file" name="other_doc_file" id="other_doc_file" data-error="#other_doc_file_error"/>
                    <div id="other_doc_file_error"></div>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2">
					<label class="control-label">&nbsp;</label>
                    <?php if(isset($data)) { if($data[0]['other_doc_path']!= '') { ?><a target="_blank" id="other_doc_file_download" class="download_file" href="<?php if(isset($data)) echo Url::base().$data[0]['other_doc_path']; ?>">
                    <span class="fa download fa-download" ></span></a><?php }} ?>
				</div>
			</div>

			<div class="form-devident">
				<div class=" col-md-12 col-sm-12 col-xs-12">
					<h4>Accounting Category</h4>
				</div>
			</div>
			<div class="form-group">
				<div class=" col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Category 1</label>
					<select class="form-control" id="category_1" name="ac_category_1">
						<option value="">Select</option>
					</select>
				</div>
				<div class=" col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Category 2</label>
					<select class="form-control" id="category_2" name="ac_category_2">
						<option value="">Select</option>
					</select>
				</div>
				<div class=" col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Category 3</label>
					<select class="form-control" id="category_3" name="ac_category_3">
						<option value="">Select</option>
					</select>
				</div>
				<div class=" col-md-3 col-sm-3 col-xs-6" id="add_category_div">
					<label class="control-label">  </label>
					<div class=" ">
						<div class="form-group btn-container  ">  
							<button type="button" class="btn  btn-sm btn-success" id="add_category">Add New Category</button>
						</div>
					</div>
				</div>
			</div>

			<div class="form-devident vendor_goods" id="business_category_label" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods') echo 'display: none;'; } else { echo 'display: none;'; } ?>">
				<div class="  col-md-12 col-sm-12 col-xs-12">
					<h4>Business Category</h4>
				</div>
			</div>
			<div class="form-devident vendor_goods" id="category_details" style="<?php if(isset($data[0])) { if($data[0]['type'] != 'Vendor Goods') echo 'display: none;'; } else { echo 'display: none;'; } ?>">
				<div class="form-group col-md-4 col-sm-5 col-xs-12">
					<table class="table table-bordered" id="business_category">
                		<thead>
                			<tr>
	                			<th width="55px;" style="text-align: center;" class="action_delete">Action</th>
	                			<th width="55px;" style="text-align: center;">Sr. No.</th>
	                			<th width="250px;">Category</th>
                			</tr>
                		</thead>
                		<tbody>
                			<?php $blFlag = false; if(isset($acc_category)) { if(count($acc_category)>0) { $blFlag = true;
                					for($i=0; $i<count($acc_category); $i++) { ?>
		                				<tr id="cat_row_<?php echo $i; ?>">
		                					<td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_row_<?php echo $i; ?>" onClick="delete_row(this);">-</button></td>
				                			<td style="text-align: center;"><?php echo $i+1; ?></td>
				                			<td>
				                				<select id="cat_id_<?php echo $i; ?>" name="bus_category[]" onChange="set_bus_category(this);" class="form-control">
				                					<option value="">Select</option>
				                					<?php if(isset($category_list)) { for($j=0; $j<count($category_list); $j++) { ?>
				                						<option value="<?php echo $category_list[$j]['id']; ?>" <?php if($acc_category[$i]['category_id']==$category_list[$j]['id']) echo 'selected'; ?>><?php echo $category_list[$j]['category_name']; ?></option>
				                					<?php }} ?>
				                				</select>
				                				<input type="hidden" id="cat_name_<?php echo $i; ?>" name="bus_category_name[]" value="<?php echo $acc_category[$i]['category_name']; ?>" />
				                			</td>
			                			</tr>
                			<?php }}} if($blFlag == false) { ?>
                						<tr id="cat_row_0">
				                			<td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_row_0" onClick="delete_row(this);">-</button></td>
				                			<td style="text-align: center;">1</td>
				                			<td>
				                				<select id="cat_id_0" name="bus_category[]" class="form-control" onChange="set_bus_category(this);">
				                					<option value="">Select</option>
				                					<?php if(isset($category_list)) { for($j=0; $j<count($category_list); $j++) { ?>
				                						<option value="<?php echo $category_list[$j]['id']; ?>"><?php echo $category_list[$j]['category_name']; ?></option>
				                					<?php }} ?>
				                				</select>
				                				<input type="hidden" id="cat_name_0" name="bus_category_name[]" value="" />
				                			</td>
			                			</tr>
                			<?php } ?>
                		</tbody>
                		<tfoot>
                			<tr>
                				<td style="text-align: center;"><button type="button" class="btn btn-success btn-sm" id="repeat_business_category">+</button></td>
                				<td colspan="2"> </td>
                			</tr>
                		</tfoot>
                	</table>
				</div>
			</div>

			<div class="form-group">
	         	<div class="col-md-6 col-sm-6 col-xs-6">
					<label class="control-label">Remarks</label>
					<textarea id="remarks" name="remarks" class="form-control" rows="2" maxlength="1000"><?php if(isset($data)) echo $data[0]['approver_comments']; ?></textarea>
				</div>
				<div class="col-md-3 col-sm-3 col-xs-6">
					<label class="control-label">Approver</label>
					<select id="approver_id" name="approver_id" class="form-control">
						<option value="">Select</option>
						<?php for($i=0; $i<count($approver_list); $i++) { ?>
							<option value="<?php echo $approver_list[$i]['id']; ?>" <?php if(isset($data[0])) { if($data[0]['approver_id']==$approver_list[$i]['id']) echo "selected"; } ?>><?php echo $approver_list[$i]['username']; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="form-group btn-container"> 
				<div class="col-md-12">
					<!-- <button type="submit" class="btn btn-success btn-sm" id="btn_submit">Submit For Approval  </button> -->
					<input type="submit" class="btn btn-success btn-sm" id="btn_submit" name="btn_submit" value="Submit For Approval" />
					<input type="submit" class="btn btn-danger btn-sm" id="btn_reject" name="btn_reject" value="Reject" />
					<a href="<?php echo Url::base(); ?>index.php?r=accountmaster%2Findex" class="btn btn-primary btn-sm pull-right">Cancel</a>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" id="account_category_modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Category Master</h4>
            </div>
            <div class="modal-body" style=" ">
			  	<div class="modal-body-inside">
			  		<form id="acc_category_master" class="form-horizontal"> 
			  			<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
			  			<!-- <div class="bs-example grn-index" data-example-id="bordered-table"> -->
	                	<table class="table table-bordered">
	                		<thead>
	                			<tr>
		                			<th width="55px;" style="text-align: center;">Sr. No.</th>
		                			<th width="250px;">Category 1</th>
		                			<th width="250px;">Category 2</th>
		                			<th width="250px;">Category 3</th>
	                			</tr>
	                		</thead>
	                		<tbody id="category_body">
	                			
	                		</tbody>
	                		<tfoot>
	                			<tr>
	                				<td style="text-align: center;"><button type="button" class="btn btn-sm btn-success" id="repeat_category">+</button></td>
	                				<td colspan="3"></td>
	                			</tr>
	                		</tfoot>
	                	</table>
	                	<!-- </div> -->
                	</form>
            	</div>
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm  btn-danger  pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-success" id="btn_save_category">Save</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var BASE_URL="<?php echo Url::base(); ?>";
    var cat_cnt = parseFloat("<?php if(isset($category)) echo count($category); else echo 0; ?>");
    var cat_1 = "<?php if(isset($data)) echo $data[0]['category_1']; ?>";
    var cat_2 = "<?php if(isset($data)) echo $data[0]['category_2']; ?>";
    var cat_3 = "<?php if(isset($data)) echo $data[0]['category_3']; ?>";
</script>


<?php 
	$this->registerJsFile(
	    '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
	    ['depends' => [\yii\web\JqueryAsset::className()]]
	);
    $this->registerJsFile(
        '@web/js/account_master.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
    // $this->registerJsFile(
    //     '@web/js/datatable.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
?>