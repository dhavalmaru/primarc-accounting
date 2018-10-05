<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Primarc Pecan | Payment Advice</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <style>
        body { font-family:Muli; margin:0; padding:0;}
        .debit_note {  margin:20px auto; border:0px solid #ddd; max-width:800px; }
        .header-section {text-align:center; min-height:100px;}
        h1 { font-size:23px; font-weight:600!important; margin:0; padding:0; text-align:center; }
        h2 { font-size:23px; font-weight:600!important; margin:0; padding:0; text-align:center; padding-bottom:5px; }
        p{ padding:0; margin:0; font-size:13px; line-height:21px;}
        table  { margin:10px 0;   }
        table tr td  { border:1px solid #999; padding:3px 10px;  }
        .table-bordered { font-size:12px;  border-collapse:collapse; width:100%;}
        .table {   border-collapse:collapse; width:100%;}
        .table-bordered tr th{ border:1px solid #999; padding:3px 7px; border-collapse:collapse;  }
        .modal-body-inside {  background:#f9f9f9; padding:10px; }
    </style>
</head>

<?php $mycomponent = Yii::$app->mycomponent; ?>

<body class="hold-transition">
    <div class="debit_note">
        <div class="header-section">
            <h1><b> Primarc Pecan Retail Private Limited  </b></h1>
            <p>
                210, Building #2B, Sanjay Mittal Industrial Estat, <br>  
                Above Cafe Coffee Day, Andheri-Kurla Road, Marol Naka <br> 
                Andheri (East), Mumbai - 400059,  Maharashtra <br>
                <b>Phone No. : -</b> +91 22 61431777 <br>   <b>Email Id. : -</b>  info@primarcpecan.com 
            </p>
        </div> 
        <table width="100%" border="0" cellspacing="0" class="table" style="border-collapse:collapse;">
            <tr style="border:none;">
                <td height="59" colspan="4" align="center" style="border:none;"><h2><b>Payment Advice</b></h2></td>
            </tr>
            <tr style="border:none;">
                <td width="75%" height="25" style="border:none;">  <p><b> <?php if(isset($payment_details[0]['account_name'])) echo $payment_details[0]['account_name']; ?> </b> </p> </td>
                <td width="10%" style="border:none;"><p><b>Dated</b></p></td>
                <td width="2%" style="border:none;">:</td>
                <td width="13%" style="border:none; padding-right:0;">
                    <p> 
                        <?php if(isset($payment_details[0]['updated_date'])) 
                                echo (($payment_details[0]['updated_date']!=null && $payment_details[0]['updated_date']!='')?
                                date('d/m/Y',strtotime($payment_details[0]['updated_date'])):''); ?> 
                    </p>
                </td>
            </tr>
            <tr>
                <td style="border:none; ">
                    <p>
                        <?php if(isset($vendor_details[0]['office_address_line_1']) && $vendor_details[0]['office_address_line_1']!='') echo $vendor_details[0]['office_address_line_1']; ?> &nbsp;
                        <?php if(isset($vendor_details[0]['office_address_line_2']) && $vendor_details[0]['office_address_line_2']!='') echo '<br/>' . $vendor_details[0]['office_address_line_2']; ?> &nbsp;
                        <?php if(isset($vendor_details[0]['office_address_line_3']) && $vendor_details[0]['office_address_line_3']!='') echo '<br/>' . $vendor_details[0]['office_address_line_3']; ?> &nbsp;
                        <?php if(isset($vendor_details[0]['city_name']) && $vendor_details[0]['city_name']!='') echo '<br/>' . $vendor_details[0]['city_name']; ?> <?php if(isset($vendor_details[0]['pincode']) && $vendor_details[0]['pincode']!='') echo ' - ' . $vendor_details[0]['pincode']; ?> &nbsp;
                        <?php if(isset($vendor_details[0]['state_name']) && $vendor_details[0]['state_name']!='') echo $vendor_details[0]['state_name']; ?> &nbsp;
                        <?php if(isset($vendor_details[0]['contact_mobile']) && $vendor_details[0]['contact_mobile']!='') echo '<br/><b>Phone No. : -</b> ' . $vendor_details[0]['contact_mobile']; ?> &nbsp;
                        <?php if(isset($vendor_details[0]['contact_email']) && $vendor_details[0]['contact_email']!='') echo '<br/><b>Email Id. : -</b> ' . $vendor_details[0]['contact_email']; ?> &nbsp;
                    </p>
                </td>
                <td style="border:none;">&nbsp;</td>
                <td style="border:none;">&nbsp;</td>
                <td style="border:none;">&nbsp;</td>
            </tr>

            <tr valign="top">
                <td colspan="4" style="border:none; padding:0;">&nbsp;    </td>
            </tr>
            <tr>
                <td colspan="4" height="10" style="border:none;"> 
                    <p style="margin-bottom:5px;">Dear Sir/Madam,</p>
                    <p>Please find beleow the payment details.</p>
                </td>
            </tr>
            <tr>
                <td colspan="4" height="0" style="border:none; padding:0;">&nbsp;</td>
            </tr>

            <tr valign="bottom" >
                <td colspan="4" style="border:none;  "> 
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                <tr style="text-align:center;">
                    <td width="25%"><p> <b>Particulars </b></p></td>
                    <td width="25%"><p> <b>Reference No. </b></p></td>
                    <td width="25%"> <p> <b>Reference Date </b></p> </td>
                    <td width="25%"> <p> <b>Amount </b></p></td>
                </tr>
                <?php $credit_amt = 0; $debit_amt = 0; $blFlag = false; ?>
                <?php for($i=0; $i<count($entry_details); $i++) { ?>
                    <?php 
                        if($entry_details[$i]['type']=='Credit') {
                            $credit_amt = $credit_amt + $entry_details[$i]['tot_amount'];
                        } else if($entry_details[$i]['type']=='Debit') {
                            $debit_amt = $debit_amt + $entry_details[$i]['tot_amount'];
                        }

                        if($i==count($entry_details)-1) {
                            $blFlag = true;
                        } else if($entry_details[$i]['ledger_type']!=$entry_details[$i+1]['ledger_type'] || $entry_details[$i]['ref_no']!=$entry_details[$i+1]['ref_no']) {
                            $blFlag = true;
                        }
                    ?>
                    <?php if($blFlag == true) { ?>
                        <?php 
                            if($credit_amt>$debit_amt) {
                                $tot_amount = $credit_amt - $debit_amt;
                                $particular = 'Total Receivable Amount';
                            } else {
                                $tot_amount = $debit_amt - $credit_amt;
                                $particular = 'Total Payble Amount';
                            }
                            $ref_no = $entry_details[$i]['ref_no'];
                            if(isset($entry_details[$i]['ref_date'])) {
                                $ref_date = (($entry_details[$i]['ref_date']!=null && $entry_details[$i]['ref_date']!='')?date('d/m/Y',strtotime($entry_details[$i]['ref_date'])):'');
                            } else {
                                $ref_date = '';
                            }
                        ?>
                        <?php if($entry_details[$i]['ledger_type']=='purchase') { ?>
                        <tr style="text-align:right;">
                            <td style="text-align:left;"><p> Total Amount </p></td>
                            <td style="text-align:left;"><p style="text-align:left;"> <?php echo $ref_no; ?> </p></td>
                            <td style="text-align:left;"><p> <?php echo $ref_date; ?> </p></td>
                            <td style="text-align:right;"><p style="text-align:right;"> <?php echo $mycomponent->format_money($debit_amt,2); ?> </p></td>
                        </tr>
                        <tr style="text-align:right;">
                            <td style="text-align:left;"><p> Total Deduction </p></td>
                            <td style="text-align:left;"><p style="text-align:left;"> <?php echo $ref_no; ?> </p></td>
                            <td style="text-align:left;"><p> <?php echo $ref_date; ?> </p></td>
                            <td style="text-align:right;"><p style="text-align:right;"> <?php echo $mycomponent->format_money($credit_amt,2); ?> </p></td>
                        </tr>
                        <?php } ?>
                        <tr style="text-align:right;">
                            <td style="text-align:left;"><p> <?php echo $particular; ?> </p></td>
                            <td style="text-align:left;"><p style="text-align:left;"> <?php echo $ref_no; ?> </p></td>
                            <td style="text-align:left;"><p> <?php echo $ref_date; ?> </p></td>
                            <td style="text-align:right;"><p style="text-align:right;"> <?php echo $mycomponent->format_money($tot_amount,2); ?> </p></td>
                        </tr>
                        <?php $credit_amt = 0; $debit_amt = 0; $blFlag = false; ?>
                    <?php } ?>
                <?php } ?>

                <!-- <tr style="text-align:right;">
                    <td style="text-align:left;"><p> <?php //echo $entry_details[$i]['ledger_name']; ?> </p> </td>
                    <td style="text-align:left;"><p style="text-align:left;"> <?php //echo $entry_details[$i]['invoice_no']; ?> </p></td>
                    <td style="text-align:left;">
                        <p> 
                            <?php //if(isset($entry_details[$i]['invoice_date'])) 
                            //echo (($entry_details[$i]['invoice_date']!=null && $entry_details[$i]['invoice_date']!='')?
                            //date('d/m/Y',strtotime($entry_details[$i]['invoice_date'])):''); ?> 
                        </p>
                    </td>
                    <td style="text-align:right;"><p style="text-align:right;"> <?php //echo $mycomponent->format_money($entry_details[$i]['amount'],2); ?> </p></td>
                </tr> -->

                </table>
                </td>
            </tr>
            <tr valign="bottom"  >
                <td style="border:none;"><p style="margin-bottom:5px;">Kindly Acknowlede the receipt,</p>
                <p>Thanking You</p></td>
                <td valign="bottom" colspan="3" style="border:none; text-align:right; ">&nbsp;</td>
            </tr>
            <tr valign="bottom"  >
                <td style="border:none;">&nbsp;</td>
                <td valign="bottom" colspan="3" style="border:none; text-align:right; ">&nbsp;</td>
            </tr>
            <tr valign="bottom"  >
                <td style="border:none;"> <p> <strong>Receiver's Signature</strong></p> </td>
                <td valign="bottom" colspan="3" style="border:none; text-align:right; "><p > <b>Authorised Signatory</b></p></td>
            </tr>
            <tr valign="bottom" >
                <td colspan="4" style="border:none;">&nbsp;   </td>
            </tr>
        </table>
    </div>
</body>
</html>
