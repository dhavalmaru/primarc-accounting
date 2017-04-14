<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Primac Pecan | Debit Note</title>
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
        table  { margin:10px 0;   }
        table tr td  { border:1px solid #999; padding:3px 10px;  }
        .table-bordered { font-size:12px;  border-collapse:collapse; width:100%;}
        .table {   border-collapse:collapse; width:100%;}
        .table-bordered tr th{ border:1px solid #999; padding:3px 7px; border-collapse:collapse;  }
        .modal-body-inside { padding:10px; }
        /*.modal-body-inside table { font-size: 14px; }*/
    </style>
</head>

<?php $mycomponent = Yii::$app->mycomponent; ?>

<body class="hold-transition">
<div class="debit_note">
    <div class="header-section">
        <h1><b> Primarc Pecan Retail (P) Ltd - Mum(FY 16-17)</b></h1>
        <p>210A, 214, Building No 2-B, <br>  Mittal Industrial Estate Premises <br> 
        Co-Operative Society Limited, Marol Naka <br>
        Andheri (East), Mumbai - 400059 <br> Maharashtra  </p>
    </div>

    <table width="100%" border="0" cellspacing="0" class="table" style="border-collapse:collapse;  ">
        <tr style="border:none;">
            <td style="border:none;" colspan="6" align="center"><h2><b>Debit Note</b></h2></td>
        </tr>
        <tr style="border:none;">
            <td width="17%" style="border:none;"><p>No.</p></td>
            <td width="3%" height="25" style="border:none;">:</td>
            <td width="53%" style="border:none;"><p><b> <?php if(isset($debit_note[0]['invoice_no'])) echo $debit_note[0]['invoice_no']; ?></b></p></td>
            <td width="9%" style="border:none;"><p>Dated</p></td>
            <td width="4%" style="border:none;">:</td>
            <td width="14%" style="border:none;">
                <p>
                    <b> 
                        <?php if(isset($debit_note[0]['invoice_date'])) 
                                echo (($debit_note[0]['invoice_date']!=null && $debit_note[0]['invoice_date']!='')?
                                date('d/m/Y',strtotime($debit_note[0]['invoice_date'])):''); ?> 
                    </b>
                </p>
            </td>
        </tr>
        <tr>
            <td width="17%" style="border:none;"><p> Ref.</p></td>
            <td width="3%" style="border:none;">:</td>
            <td width="53%" style="border:none;"><p><b> <?php if(isset($debit_note[0]['id'])) echo $debit_note[0]['id']; ?> </b></p></td>
            <td style="border:none;">&nbsp;</td>
            <td style="border:none;">&nbsp;</td>
            <td style="border:none;">&nbsp;</td>
        </tr>
        <tr valign="top">
            <td  width="17%" style="border:none; vertical-align:top;"><p>Party's Name</p></td>
            <td  width="3%" style="border:none; vertical-align:top;">:</td>
            <td colspan="4" style="border:none;">
                <p>
                    <b> <?php if(isset($vendor_details[0]['account_holder_name'])) echo $vendor_details[0]['account_holder_name']; ?> </b> <br> 
                    <?php if(isset($vendor_details[0]['office_address_line_1'])) echo $vendor_details[0]['office_address_line_1']; ?> &nbsp;
                    <?php if(isset($vendor_details[0]['office_address_line_2'])) echo $vendor_details[0]['office_address_line_2']; ?> &nbsp;
                    <?php if(isset($vendor_details[0]['office_address_line_3'])) echo $vendor_details[0]['office_address_line_3']; ?> &nbsp; <br> 
                    <?php if(isset($vendor_details[0]['city_name'])) echo $vendor_details[0]['city_name']; ?> (<?php if(isset($vendor_details[0]['city_code'])) echo $vendor_details[0]['city_code']; ?>), &nbsp;
                    <?php if(isset($vendor_details[0]['state_name'])) echo $vendor_details[0]['state_name']; ?> (<?php if(isset($vendor_details[0]['state_code'])) echo $vendor_details[0]['state_code']; ?>), &nbsp;
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="6" height="10" style="border:none;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3"  style="border-left:none;"><p>Particulars</p></td>
            <td colspan="3" align="center"  style="border-right:none;"><p>Amount</p></td>
        </tr>
        <tr valign="top"  style="border:none; ">
            <td colspan="3"   style="border-left:none; border-bottom:none; ">
                <p>
                    Being Debit Note Raised For 
                    <?php if(isset($debit_note[0]['ded_type'])) echo $debit_note[0]['ded_type']; ?> 
                    As Per Attached Details <br>
                    Qty - <?php if(isset($debit_note[0]['total_qty'])) echo $debit_note[0]['total_qty']; ?> Nos
                </p>
            </td>
            <td colspan="3" align="center" valign="top" style="border-right:none;"><p><b>Rs.<?php if(isset($debit_note[0]['total_deduction'])) echo $mycomponent->format_money($debit_note[0]['total_deduction'],0); ?></b></p></td>
        </tr>
        <tr>
            <td  style="border:none; border-right:1px solid #999;" colspan="3"><p><b> Amount (in words) </b></p></td>
            <td colspan="3" style="border:none; border-left:1px solid #999;"></td>
        </tr>
        <tr>
            <td height="70" colspan="3" valign="top" style="border:none; border-bottom:1px solid #999; border-right:1px solid #999;"> 
                <p><?php if(isset($debit_note[0]['total_deduction'])) echo $mycomponent->convert_number_to_words(round($debit_note[0]['total_deduction'],0)); ?></p>
            </td>
            <td colspan="3" style="border:none; border-left:1px solid #999; border-bottom:1px solid #999;"></td>
        </tr>
        <tr valign="bottom" >
            <td colspan="6" style="border:none;">&nbsp;   </td>
            <td colspan="3" style="border:none;"></td>
        </tr>
        <tr valign="bottom" >
            <td colspan="3" style="border:none;">&nbsp;  </td>
            <td valign="bottom" colspan="3" style="border:none; text-align:center "><p> <b>Authorised Signatory</b></p></td>
        </tr>
        <tr valign="bottom" >
            <td colspan="6" style="border:none;">&nbsp;   </td>
        </tr>
    </table>
</div>



<?php 
    $deduction_type = '';
    $expiry_style = 'display: none;';
    $margin_diff_style = 'display: none;';
    $result = "";
    $table = "";
    $sr_no = 1;

    for($i=0; $i<count($deduction_details); $i++) {
        $ded_type = $deduction_details[$i]['ded_type'];
        $deduction_type = ucwords(strtolower($ded_type));

        if($ded_type=='expiry'){
            $expiry_style = '';
        } else {
            $expiry_style = 'display: none;';
        }
        if($ded_type=='margin_diff'){
            $margin_diff_style = '';
            $deduction_type = 'Margin Difference';
        } else {
            $margin_diff_style = 'display: none;';
        }

        $row = '<tr>
                    <td>' . $sr_no . '</td>
                    <td>' . $deduction_details[$i]['psku'] . '</td>
                    <td>' . $deduction_details[$i]['product_title'] . '</td>
                    <td>' . $deduction_details[$i]['ean'] . '</td>
                    <td>' . ((isset($debit_note[0]['invoice_no']))? $debit_note[0]['invoice_no']:'') . '</td>
                    <td>' . (($debit_note[0]['invoice_date']!=null && $debit_note[0]['invoice_date']!='')?
                                date('d/m/Y',strtotime($debit_note[0]['invoice_date'])):'') . '</td>
                    <td>' . $deduction_details[$i]['state'] . '</td>
                    <td>' . $deduction_details[$i]['vat_cst'] . '</td>
                    <td>' . $deduction_details[$i]['vat_percen'] . '</td>
                    <td>' . $deduction_details[$i]['qty'] . '</td>
                    <td>' . $deduction_details[$i]['box_price'] . '</td>
                    <td>' . $deduction_details[$i]['cost_excl_vat_per_unit'] . '</td>
                    <td>' . $deduction_details[$i]['tax_per_unit'] . '</td>
                    <td>' . $deduction_details[$i]['total_per_unit'] . '</td>
                    <td>' . $deduction_details[$i]['cost_excl_vat'] . '</td>
                    <td>' . $deduction_details[$i]['tax'] . '</td>
                    <td>' . $deduction_details[$i]['total'] . '</td>' . 
                    (($ded_type=='expiry')?
                    '<td style="'.$expiry_style.'">' . 
                                (($deduction_details[$i]['expiry_date']!=null && $deduction_details[$i]['expiry_date']!='')?
                                date('d/m/Y',strtotime($deduction_details[$i]['expiry_date'])):'') . '
                    </td>
                    <td style="'.$expiry_style.'">' . 
                                (($deduction_details[$i]['earliest_expected_date']!=null && $deduction_details[$i]['earliest_expected_date']!='')?
                                date('d/m/Y',strtotime($deduction_details[$i]['earliest_expected_date'])):'') . '
                    </td>':'') . 
                    (($ded_type=='margin_diff')?
                    '<td style="'.$margin_diff_style.'"></td>
                    <td style="'.$margin_diff_style.'"></td>':'') . 
                    '<td></td>
                </tr>';

        $result = $result . $row;
        $sr_no = $sr_no + 1;

        $blFlag = false;
        if(($i+1)==count($deduction_details)){
            $blFlag = true;
        } else if($deduction_details[$i]["ded_type"]!=$deduction_details[$i+1]["ded_type"]){
            $blFlag = true;
        }

        if($blFlag==true){
            $table = '<div class="modal-body-inside">
                        <h1><b>' . $deduction_type . ' Deductions</b></h1>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align:center;">SKU Details</th>
                                    <th colspan="2" style="text-align:center;">Invoice Details</th>
                                    <th colspan="3" style="text-align:center;">Purchase Ledger</th>
                                    <th colspan="2" style="text-align:center;">Quantity Deducted</th>
                                    <th colspan="3" style="text-align:center;">Amount Deducted (Per Unit)</th>
                                    <th colspan="3" style="text-align:center;">Amount Deducted (Total)</th>' . 
                                    (($ded_type=='expiry')?
                                    '<th colspan="2" style="'.$expiry_style.'text-align:center;">For Expiry Only</th>':'') . 
                                    (($ded_type=='margin_diff')?
                                    '<th colspan="2" style="'.$margin_diff_style.'text-align:center;">For Margin Difference (Per Unit)</th>':'') . 
                                    '<th rowspan="2">Remarks</th>
                                </tr>
                                <tr>
                                    <th style="text-align:center;"> Sr. No.</th>
                                    <th>SKU Code</th>
                                    <th>SKU Name</th>
                                    <th>EAN Code</th>
                                    <th>Invoice Number</th>
                                    <th>Invoice Date</th>
                                    <th>Purchase State</th>
                                    <th>Tax</th>
                                    <th>Tax Rate</th>
                                    <th>Quantity</th>
                                    <th>MRP</th>
                                    <th>Cost Excl Tax</th>
                                    <th>Tax</th>
                                    <th>Total</th>
                                    <th>Cost Excl Tax</th>
                                    <th>Tax</th>
                                    <th>Total</th>' . 
                                    (($ded_type=='expiry')?
                                    '<th style="'.$expiry_style.'">Date Received</th>
                                    <th style="'.$expiry_style.'">Earliest Expected Date</th>':'') . 
                                    (($ded_type=='margin_diff')?
                                    '<th style="'.$margin_diff_style.'">Difference in Cost Excl Tax</th>
                                    <th style="'.$margin_diff_style.'">Difference in Tax</th>':'') . 
                                '</tr>
                            </thead>
                            <tbody>' . $result . '</tbody>
                        </table>   
                    </div>
                    <br clear="all"/>';

            echo $table;
            $result = '';
            $sr_no = 1;
        }
    } 
?>

 
</body>
</html>
