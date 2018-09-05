<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Primarc Pecan | Tax Invoice</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->

    <style>
        body {  margin:0; padding:0; letter-spacing: 0.5px; font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;}
        .debit_note {  margin:20px auto; border:0px solid #ddd; max-width:800px; }
        .header-section {text-align:center;}
        h1 { font-size:23px; font-weight:600!important; margin:0; padding:0; text-align:center; }
        h2 { font-size:23px; font-weight:600!important; margin:0; padding:0; text-align:center; padding-bottom:5px; }
        p{ padding:0; margin:0; font-size:13px; line-height:21px; }
        /*table  { margin:10px 0;   }*/
        table tr td  { border:1px solid #999; padding:3px 10px;  }
        .table-bordered { font-size:13px;  border-collapse:collapse; width:100%;}
        .table {   border-collapse:collapse; width:100%;}
        .table-bordered tr th{ border:1px solid #999; padding:3px 7px; border-collapse:collapse;  }
        .modal-body-inside { padding:10px; }
        @media print{@page {size: portrait}}
        /*.modal-body-inside table { font-size: 14px; }*/
		     
    </style>
</head>

<?php $mycomponent = Yii::$app->mycomponent; ?>

<body class="hold-transition">
<div class="debit_note">
    <table width="100%" border="0" cellspacing="0" class="table" style="border-collapse:collapse;  ">
        <tr style="">
            <td style="" colspan="6" align="center"><h2><b> Tax Invoice</b></h2></td>
        </tr>
        <tr>
            <td colspan="6" height="10" style="border:none;">&nbsp;</td>
        </tr>
        <tr valign="top"  style=" ">
            <td colspan="3"   style="border-left:none; border-bottom:none; ">
                <p>
				Primarc Pecan Retail (P) Ltd (FY <?php if(isset($debit_note[0]['date_of_transaction'])) 
                                            echo $mycomponent->get_financial_year($debit_note[0]['date_of_transaction']); ?>)
				<!-- 210A, 214, Building No 2-B,
				Mittal Industrial Estate Premises
				Co-Operative Society Limited, Marol Naka
				Andheri (East), Mumbai - 400059
				Maharashtra -->

                <?php if(isset($warehouse_details[0]['address_line_1'])) {
                    if($warehouse_details[0]['address_line_1']!='') echo $warehouse_details[0]['address_line_1'];} ?>
                <?php if(isset($warehouse_details[0]['address_line_2'])) {
                    if($warehouse_details[0]['address_line_2']!='') echo $warehouse_details[0]['address_line_2'];} ?>
                <?php if(isset($warehouse_details[0]['address_line_3'])) {
                    if($warehouse_details[0]['address_line_3']!='') echo $warehouse_details[0]['address_line_3'];} ?>
                <?php if(isset($warehouse_details[0]['city_name'])) {
                    if($warehouse_details[0]['city_name']!='') echo $warehouse_details[0]['city_name'];} ?>
                <?php if(isset($warehouse_details[0]['city_name']) && isset($warehouse_details[0]['pincode'])) {
                    if($warehouse_details[0]['city_name']!='' && $warehouse_details[0]['pincode']!='') echo '-';} ?>
                <?php if(isset($warehouse_details[0]['pincode'])) {
                    if($warehouse_details[0]['pincode']!='') echo $warehouse_details[0]['pincode'];} ?>
                <?php if(isset($warehouse_details[0]['state_name'])) {
                    if($warehouse_details[0]['state_name']!='') echo ' ' . $warehouse_details[0]['state_name'];} ?>
				</p>
            </td>
            <td colspan="2" align="center" valign="top" style=""><p><b>Invoice No.</b>
			<br>     <?php if(isset($debit_note[0]['debit_credit_note_ref'])) echo $debit_note[0]['debit_credit_note_ref']; ?></p></td>
			<td colspan="1" align="center" valign="top" style="border-right:none;"><p><b>Dated</b>
			<br>  <?php if(isset($debit_note[0]['date_of_transaction'])) 
                                            echo (($debit_note[0]['date_of_transaction']!=null && $debit_note[0]['date_of_transaction']!='')?
                                            date('d/m/Y',strtotime($debit_note[0]['date_of_transaction'])):''); ?></p></td>
        </tr>
        <tr>
            <td width="22%" style="border:none;"><p> GSTIN</p></td>
            <td width="4%" style="border:none;">:</td>
            <td width="14%" style="border:none;">
                <p>
                    <b> 
                        <?php if(isset($warehouse_details[0]['gst_id'])) echo $warehouse_details[0]['gst_id']; ?>
                    </b>
                </p>
            </td>
              <td colspan="2" align="center" valign="top" style="border-right:none;"><p><b>Delivery Note  </b>
			<br> </p></td>
			<td colspan="1" align="center" valign="top" style="border-right:none;"><p><b>Mode/ Terms Of Payment</b>
			<br> </p></td>
        </tr>
        <tr>
			<td width="22%" style="border:none;"><p>State</p></td>
            <td width="4%" style="border:none;">:</td>
            <td width="14%" style="border:none;">
                <p>
                    <b> 
                        <?php if(isset($warehouse_details[0]['state_name'])) echo $warehouse_details[0]['state_name']; ?>
                    </b>
                </p>
            </td>
              <td colspan="2"  align="center" valign="top" style="border-right:none;"><p><b>Supplier's Reference </b>
			<br> </p></td>
			<td colspan="1"  align="center" valign="top" style="border-right:none;"><p><b>Other Reference(S)</b>
			<br> </p></td>
        </tr>
		<tr>
			<td width="22%" style="border:none;"><p>State Code</p></td>
            <td width="4%" style="border:none;">:</td>
            <td width="14%" style="border:none;">
                <p>
                    <b> 
                        <?php if(isset($warehouse_details[0]['state_code'])) echo $warehouse_details[0]['state_code']; ?>
                    </b>
                </p>
            </td>
            <td colspan="3" style="border:none; border-left:1px solid #999; "></td>
        </tr>
        <tr>
			<td width="22%" style="border:none; border-bottom:1px solid #999;"><p>CIN</p></td>
            <td width="4%" style="border:none;">:</td>
            <td width="14%" style="border:none;">
                <p>
                    <b> 
                        U52100WB2006PTC111833
                    </b>
                </p>
            </td>
            <td colspan="3" style="border:none; border-left:1px solid #999; border-bottom:1px solid #999;"></td>
        </tr>
		<tr>
            <td colspan="6"  style="border-left:none;border-right:none;"><p>Details of Buyer | Billed to:</p></td>
        </tr>
		<tr valign="top"  style="border:none;">
            <td colspan="3" style="border-left:none; border-bottom:none;">
                <p>
    			<?php if(isset($vendor_details[0]['account_holder_name'])) echo $vendor_details[0]['account_holder_name']; ?>
    			<?php if(isset($vendor_details[0]['office_address_line_1'])) echo $vendor_details[0]['office_address_line_1']; ?> &nbsp;
                <?php if(isset($vendor_details[0]['office_address_line_2'])) echo $vendor_details[0]['office_address_line_2']; ?> &nbsp;
                <?php if(isset($vendor_details[0]['office_address_line_3'])) echo $vendor_details[0]['office_address_line_3']; ?> &nbsp;
                <?php if(isset($vendor_details[0]['city_name'])) echo $vendor_details[0]['city_name']; ?> <?php if(isset($vendor_details[0]['pincode'])) echo $vendor_details[0]['pincode']; ?> &nbsp;
				</p>
            </td>
            <td colspan="2" align="center" valign="top" style="border-right:none;"><p><b>Buyers Order No.</b>
			<br> </p></td>
			<td colspan="1" align="center" valign="top" style="border-right:none;"><p><b>Dated</b>
			<br> </p></td>
        </tr>
        <tr>
            <td width="22%" style="border:none;"><p> GSTIN</p></td>
            <td width="4%" style="border:none;">:</td>
            <td width="14%" style="border:none;">
                <p>
                    <b> 
                         <?php if(isset($vendor_details[0]['gst_id'])) echo $debit_note[0]['gst_id']; ?>
                    </b>
                </p>
            </td>
            <td colspan="2" align="center" valign="top" style="border-right:none;">
                <p><b>Dispatch Document No. </b><br> </p>
            </td>
			<td colspan="1" align="center" valign="top" style="border-right:none;">
                <p><b>Delivery Note Date</b><br> </p>
            </td>
        </tr>
        <tr>
			<td width="22%" style="border:none;"><p>State</p></td>
            <td width="4%" style="border:none;">:</td>
            <td width="14%" style="border:none;">
                <p>
                    <b> 
                      <?php if(isset($vendor_details[0]['state_name'])) echo $vendor_details[0]['state_name']; ?>
                    </b>
                </p>
            </td>
              <td colspan="2"  align="center" valign="top" style="border-right:none;"><p><b>Dispatch Through</b>
			<br> </p></td>
			<td colspan="1"  align="center" valign="top" style="border-right:none;"><p><b>Destination</b>
			<br> </p></td>
        </tr>
		<tr style="border-bottom:1px solid #999;">
			<td width="22%" style="border:none;"><p>State Code</p></td>
            <td width="4%" style="border:none;">:</td>
            <td width="14%" style="border:none;">
                <p>
                    <b> 
                     <?php if(isset($vendor_details[0]['state_code'])) echo $vendor_details[0]['state_code']; ?>
                    </b>
                </p>
            </td>
            <td colspan="3" align="center" valign="top" style="border:none; border-left:1px solid #999; "><p><b>Terms Of Delivery<b></p></td>
        </tr>
		
        <!-- <tr valign="bottom" >
            <td colspan="6" style="border:none;">&nbsp;   </td>
            <td colspan="3" style="border:none;"></td>
        </tr> -->
		
		<tr>
            <td colspan="6" style="border:none; padding:10px 0px;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align:center;"> Sr. No.</th>
                            <th>Perticulars</th>
                            <th>HSN/SAC</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Per	</th>
                            <th> Amount </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i=0; $i<count($invoice_details)-1; $i++){ ?>
                        <tr>
                        <td style="text-align:center;"><?php echo $invoice_details[$i]['sr_no']; ?></td>
                        <td><?php echo $invoice_details[$i]['particulars']; ?></td>
                        <td><?php echo $invoice_details[$i]['code']; ?></td>
                        <td><?php echo $invoice_details[$i]['qty']; ?></td>
                        <td><?php echo $invoice_details[$i]['rate']; ?></td>
                        <td><?php echo $invoice_details[$i]['per']; ?></td>
                        <td><?php echo $invoice_details[$i]['amount']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php $i = count($invoice_details)-1; ?>
                        <tr>
                        <td colspan="2"  align="right"> Total </td>
                        <td>  </td>
                        <td>  </td>
                        <td>  </td>
                        <td>  </td>
                        <td style=" border-right:1px solid #999;"><?php echo $invoice_details[$i]['amount']; ?>  </td>
                        </tr>
					</tbody>
                </table>  
            </td>
        </tr>	
						
        <tr>
			<td colspan="6" style="border: none;">
                <span style="  font-size:16px; font-weight:500; display: block;" > 
                    Amount Chargeable (in words): 
                </span> 
                <?php if(isset($invoice_details[$i]['amount'])) echo $mycomponent->convert_number_to_words(round($invoice_details[$i]['amount'],2)); ?>
            </td>
        </tr>

        <tr>
            <td colspan="6" style="border:none; padding:10px 0px;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align:center;" colspan="2"> HSN/SAC</th>
                            <th>Taxable Value</th>
                            <th width="150" align="center" valign="middle" style="/*border-right:1px solid #666;border-bottom:1px solid #666;*/padding:0;">
                            <table style="width: 100%;border-spacing: 0;height: 36px;">
                                <tr>
                                    <td colspan="2" style="text-align: center;border:none;">CGST</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #666;border-left:0px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;text-align: center;" width="50%">Rate</td>
                                    <td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;border-left:0px solid #666;text-align: center;" width="50%">Amount</td>
                                </tr>
                            </table>
                            </th>
                            <th width="150" align="center" valign="middle" style="/*border-right:1px solid #666;border-bottom:1px solid #666;*/padding:0;">
                            <table style="width: 100%;border-spacing: 0;height: 36px;">
                                <tr>
                                    <td colspan="2" style="text-align: center;border:none;">SGST</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;border-left:0px solid #666;text-align: center;" width="50%">Rate</td>
                                    <td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;border-left:0px solid #666;text-align: center;" width="50%">Amount</td>
                                </tr>
                            </table>
                            </th>
                            <th width="150" align="center" valign="middle" style="/*border-right:1px solid #666;border-bottom:1px solid #666;*/padding:0;">
                            <table style="width: 100%;border-spacing: 0;height: 36px;">
                                <tr>
                                    <td colspan="2" style="text-align: center;border:none;">IGST</td>
                                </tr>
                                <tr>
                                    <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;text-align: center;border-left:0px solid #666;" width="50%">Rate</td>
                                    <td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;border-left:0px solid #666;text-align: center;" width="50%">Amount</td>
                                </tr>
                            </table>
                            </th>
                            <th> Total Amount </th>
                        </tr>
                    </thead>
                    <tbody>
    					<?php for($i=0; $i<count($inv_tax_details)-1; $i++){ ?>
                        <tr>
                            <td  style="text-align:center;" colspan="2">998311</td>
                            <td align="right"><?php echo $inv_tax_details[$i]['value']; ?></td>
                            <td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;padding:0;">
    							<table style="width: 100%;border-spacing: 0; height: 17px;">
    								<tbody>
    									<tr>
    										<td style="border-right:1px solid #666;border-left:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['cgst_rate']; ?></td>
    										<td style="border: 0px solid #666;text-align: right;" width="50%"><?php if($inv_tax_details[$i]['cgst_amt']!='') echo $inv_tax_details[$i]['cgst_amt']; else echo '&nbsp;'; ?></td>
    									</tr>
    								</tbody>
    							</table>
    						</td>
    						<td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;padding:0;">
    							<table style="width: 100%;border-spacing: 0; height: 17px;">
    								<tbody>
    									<tr>
    										<td style="border-right:1px solid #666;border-left:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['sgst_rate']; ?></td>
    										<td style="border: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['sgst_amt']; ?></td>
    									</tr>
    								</tbody>
    							</table>
    						</td>
    						<td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;padding:0;">
    							<table style="width: 100%;border-spacing: 0; height: 17px;">
    								<tbody>
    									<tr>
    										<td style="border-right:1px solid #666;border-left:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['igst_rate']; ?></td>
    										<td style="border: 0px solid #666;text-align: right;" width="50%"><?php if($inv_tax_details[$i]['igst_amt']!='') echo $inv_tax_details[$i]['igst_amt']; else echo '&nbsp;'; ?></td>
    									</tr>
    								</tbody>
    							</table>
    						</td>
                            <td align="right">  
                                <?php echo $inv_tax_details[$i]['value'] + $inv_tax_details[$i]['cgst_amt'] + $inv_tax_details[$i]['sgst_amt'] + $inv_tax_details[$i]['igst_amt']; ?> 
                            </td>
                        </tr>  
                        <?php } ?>
                        <?php $i = count($inv_tax_details)-1; ?>
    					<tr>
    						<td colspan="3" align="right" valign="middle" style="border-right:none;border-bottom:1px solid #666;">Total</td>
    						
    						<td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;padding:0;">
    							<table style="width: 100%;border-spacing: 0; height: 17px;">
    								<tbody>
    									<tr>
    										<!-- <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-left:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"></td> -->
    										<td colspan="2" style="border-right:0px solid #666;border-left:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['cgst_amt']; ?></td>
    									</tr>
    								</tbody>
    							</table>
    						</td>
    						<td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;padding:0;">
    							<table style="width: 100%;border-spacing: 0; height: 17px;">
    								<tbody>
    									<tr>
    										<!-- <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-left:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"></td> -->
    										<td colspan="2" style="border-right:0px solid #666;border-left:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['sgst_amt']; ?></td>
    									</tr>
    								</tbody>
    							</table>
    						</td>
    						<td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;padding:0;">
    							<table style="width: 100%;border-spacing: 0; height: 17px;">
    								<tbody>
    									<tr>
    										<!-- <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-left:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"></td> -->
    										<td colspan="2" style="border-right:0px solid #666;border-left:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['igst_amt']; ?></td>
    									</tr>
    								</tbody>
    							</table>
    						</td>
    						<td align="right" style="border-left:1px solid #666;border-right:1px solid #666;border-bottom:1px solid #666;">
    							<p style="margin:0; "><?php echo $inv_tax_details[$i]['total_amt']; ?></p>
    						</td>
    					</tr>
    				</tbody>
                </table>
            </td>
        </tr>
            
        <tr>
            <!-- <tr> -->
                <td colspan="3" style="border: none;">
                    <br/>
                    <p style="margin:0;text-align:left;line:height:22px;font-size:18px;">
                        <span style="  font-size:16px; font-weight:500; display: block;" >Tax Amount (in words): <br> </span> 
                        <?php if(isset($inv_tax_details[$i]['total_amt'])) echo $mycomponent->convert_number_to_words(round($inv_tax_details[$i]['total_amt'],2)); ?> </p> 
                    <br/>
                    <p style="margin:0;text-align:left;line:height:22px;font-size:14px;"><span style="  font-size:16px; font-weight:500; display: block;" >Remarks: <br/><br> </span> </p> <br>
                    <p style="margin:0;text-align:left;line:height:22px;font-size:11px;font-weight:bold;"><span style="  font-size:16px; font-weight:500; display: block;" >Company's PAN: AACCT5910H </span>  </p> 
                </td>
                <td colspan="3" style="font-size:12px; font-weight:500; border: none; text-align:right;">
                    For Primarc Pecan Retail (P) Ltd Mum(FY <?php if(isset($debit_note[0]['date_of_transaction'])) 
                    echo $mycomponent->get_financial_year($debit_note[0]['date_of_transaction']); ?>) 
                    <br/> 
                    <!-- <img src="../../..//assets/invoice/stamp.jpg" height="50"  alt="Sign3 Rishit" />   -->
                    <br/><br/><br/><br/><br/><br/>
                    Authorised Signatory
                </td>
            <!-- </tr> -->
           
            <!-- <td colspan="2" style="border:none;"> &nbsp; </td>
            <td valign="bottom" colspan="2" style="border:none; text-align:center "><p> <b>Authorised Signatory</b></p></td> -->
        </tr>
        <!-- <tr valign="bottom" >
            <td colspan="6" style="border:none;">&nbsp;   </td>
        </tr> -->
    </table>
</div>

</body>
</html>
