<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Primarc Pecan | Debit Note</title>
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
        .table-bordered { font-size:13px;  border-collapse:collapse; width:100%;}
        .table {   border-collapse:collapse; width:100%;}
        .table-bordered tr th{ border:1px solid #999; padding:3px 7px; border-collapse:collapse;  }
        .modal-body-inside { padding:10px; }
        @media print{@page {size: portrait}}
        /*.modal-body-inside table { font-size: 14px; }*/
    </style>
</head>

<?php $mycomponent = Yii::$app->mycomponent; ?>

<?php 
    $financial_year='';
    if(isset($invoice_details[0]['gi_date'])) {
        if($invoice_details[0]['gi_date']!=null && $invoice_details[0]['gi_date']!=''){
            $month=date('m',strtotime($invoice_details[0]['gi_date']));
            $year=date('y',strtotime($invoice_details[0]['gi_date']));
            if($month<=3){
                $financial_year = '(FY '.($year-1).'-'.$year.')';
            } else {
                $financial_year = '(FY '.$year.'-'.($year+1).')';
            }
        }
    }
?>

<body class="hold-transition">
<div class="debit_note">
    <div class="header-section">
        <h1><b> Primarc Pecan Retail (P) Ltd - Mum<?php echo $financial_year; ?></b></h1>
        <p>210A, 214, Building No 2-B, <br>  Mittal Industrial Estate Premises <br> 
        Co-Operative Society Limited, Marol Naka <br>
        Andheri (East), Mumbai - 400059 <br> Maharashtra  </p>
    </div>

    <table width="100%" border="0" cellspacing="0" class="table" style="border-collapse:collapse;  ">
        <tr style="border:none;">
            <td style="border:none;" colspan="6" align="center"><h2><b>Debit Note</b></h2></td>
        </tr>
        <tr style="border:none;">
            <td width="17%" style="border:none;"><p> Ref.</p></td>
            <td width="3%" style="border:none;">:</td>
            <td width="40%" style="border:none;"><p><b> <?php if(isset($debit_note[0]['debit_note_ref'])) echo $debit_note[0]['debit_note_ref']; ?> </b></p></td>
            <td width="22%" style="border:none;"><p>Posting Date</p></td>
            <td width="4%" style="border:none;">:</td>
            <td width="14%" style="border:none;">
                <p>
                    <b> 
                        <?php if(isset($invoice_details[0]['gi_date'])) 
                                echo (($invoice_details[0]['gi_date']!=null && $invoice_details[0]['gi_date']!='')?
                                date('d/m/Y',strtotime($invoice_details[0]['gi_date'])):''); ?> 
                    </b>
                </p>
            </td>
        </tr>
        <tr style="border:none;">
            <td width="17%" style="border:none;"><p>Invoice No.</p></td>
            <td width="3%" height="25" style="border:none;">:</td>
            <td width="40%" style="border:none;"><p><b> <?php if(isset($debit_note[0]['invoice_no'])) echo $debit_note[0]['invoice_no']; ?></b></p></td>
            <td width="22%" style="border:none;"><p>Invoice Date</p></td>
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
        <tr style="border:none;">
            <td  width="17%" style="border:none; vertical-align:top;"><p>Party's Name</p></td>
            <td  width="3%" style="border:none; vertical-align:top;">:</td>
            <td  width="40%" style="border:none; vertical-align:top;">
                <p>
                    <b> <?php if(isset($vendor_details[0]['account_holder_name'])) echo $vendor_details[0]['account_holder_name']; ?> </b> <br> 
                    <?php if(isset($vendor_details[0]['office_address_line_1'])) echo $vendor_details[0]['office_address_line_1']; ?> &nbsp;
                    <?php if(isset($vendor_details[0]['office_address_line_2'])) echo $vendor_details[0]['office_address_line_2']; ?> &nbsp;
                    <?php if(isset($vendor_details[0]['office_address_line_3'])) echo $vendor_details[0]['office_address_line_3']; ?> &nbsp; <br> 
                    <?php if(isset($vendor_details[0]['city_name'])) echo $vendor_details[0]['city_name']; ?> (<?php if(isset($vendor_details[0]['city_code'])) echo $vendor_details[0]['city_code']; ?>), &nbsp;
                    <?php if(isset($vendor_details[0]['state_name'])) echo $vendor_details[0]['state_name']; ?> (<?php if(isset($vendor_details[0]['state_code'])) echo $vendor_details[0]['state_code']; ?>), &nbsp;
                </p>
            </td>
            <td width="22%" style="border:none; vertical-align:top;"><p>Warehouse</p></td>
            <td width="4%" style="border:none; vertical-align:top;">:</td>
            <td width="14%" style="border:none; vertical-align:top;">
                <p>
                    <b> 
                        <?php if(isset($grn_details[0]['warehouse_name'])) echo $grn_details[0]['warehouse_name']; ?>
                    </b>
                </p>
            </td>
        </tr>
        <tr style="border:none;">
            <td width="17%" style="border:none;"><p> Party's GSTIN</p></td>
            <td width="3%" style="border:none;">:</td>
            <td width="40%" style="border:none;"><p><b> <?php if(isset($vendor_details[0]['gst_id'])) echo $debit_note[0]['gst_id']; ?> </b></p></td>
            <td width="22%" style="border:none;"><p>Warehouse GSTIN</p></td>
            <td width="4%" style="border:none;">:</td>
            <td width="14%" style="border:none;">
                <p>
                    <b> 
                        <?php if(isset($grn_details[0]['gst_id'])) echo $grn_details[0]['gst_id']; ?>
                    </b>
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
                    Being debit note raised for 
                    <?php if(isset($debit_note[0]['ded_type'])) echo $debit_note[0]['ded_type']; ?> 
                    as per details mentioned below <br>
                    Qty - <?php if(isset($debit_note[0]['total_qty'])) echo $debit_note[0]['total_qty']; ?> Nos
                </p>
            </td>
            <td colspan="3" align="center" valign="top" style="border-right:none;"><p><b>Rs.<?php if(isset($debit_note[0]['total_deduction'])) echo $mycomponent->format_money($debit_note[0]['total_deduction'],2); ?></b></p></td>
        </tr>
        <tr>
            <td  style="border:none; border-right:1px solid #999;" colspan="3"><p><b> Amount (in words) </b></p></td>
            <td colspan="3" style="border:none; border-left:1px solid #999;"></td>
        </tr>
        <tr>
            <td height="40" colspan="3" valign="top" style="border:none; border-bottom:1px solid #999; border-right:1px solid #999;"> 
                <p><?php if(isset($debit_note[0]['total_deduction'])) echo $mycomponent->convert_number_to_words(round($debit_note[0]['total_deduction'],2)); ?></p>
            </td>
            <td colspan="3" style="border:none; border-left:1px solid #999; border-bottom:1px solid #999;"></td>
        </tr>
        <!-- <tr valign="bottom" >
            <td colspan="6" style="border:none;">&nbsp;   </td>
            <td colspan="3" style="border:none;"></td>
        </tr> -->
        <tr valign="bottom" >
            <td colspan="6" style="border:none;"><p style="text-align: center;">This is a computer generated debit note. No signature required. &nbsp;</p></td>
            <!-- <td colspan="2" style="border:none;"> &nbsp; </td>
            <td valign="bottom" colspan="2" style="border:none; text-align:center "><p> <b>Authorised Signatory</b></p></td> -->
        </tr>
        <!-- <tr valign="bottom" >
            <td colspan="6" style="border:none;">&nbsp;   </td>
        </tr> -->
    </table>
</div>



<?php 
    $deduction_type = '';
    $expiry_style = 'display: none;';
    $margindiff_style = 'display: none;';
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
        if($ded_type=='margindiff'){
            $margindiff_style = '';
            $deduction_type = 'Margin Difference';
        } else {
            $margindiff_style = 'display: none;';
        }

        $row = '<tr>
                    <td>' . $sr_no . '</td>
                    <td>' . $deduction_details[$i]['psku'] . '</td>
                    <td>' . $deduction_details[$i]['product_title'] . '</td>
                    <td>' . $deduction_details[$i]['ean'] . '</td>
                    <td>' . $deduction_details[$i]['hsn_code'] . '</td>
                    <td>' . $deduction_details[$i]['qty'] . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['box_price'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['cost_excl_vat_per_unit'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['cgst_per_unit'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['sgst_per_unit'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['igst_per_unit'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['tax_per_unit'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['total_per_unit'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['cost_excl_vat'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['cgst'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['sgst'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['igst'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['tax'],0) . '</td>
                    <td>' . $mycomponent->format_money($deduction_details[$i]['total'],0) . '</td>' . 
                    (($ded_type=='expiry')?
                    '<td style="'.$expiry_style.'">' . 
                                (($deduction_details[$i]['expiry_date']!=null && $deduction_details[$i]['expiry_date']!='')?
                                date('d/m/Y',strtotime($deduction_details[$i]['expiry_date'])):'') . '
                    </td>
                    <td style="'.$expiry_style.'">' . 
                                (($deduction_details[$i]['earliest_expected_date']!=null && $deduction_details[$i]['earliest_expected_date']!='')?
                                date('d/m/Y',strtotime($deduction_details[$i]['earliest_expected_date'])):'') . '
                    </td>':'') . 
                    (($ded_type=='margindiff')?
                    '<td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['po_mrp'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['po_cost_excl_vat'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['po_cgst'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['po_sgst'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['po_igst'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['po_tax'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['po_total'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['margin_diff_excl_tax'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['margin_diff_cgst'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['margin_diff_sgst'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['margin_diff_igst'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['margin_diff_tax'],0) . '</td>
                    <td style="'.$margindiff_style.'">' . $mycomponent->format_money($deduction_details[$i]['margin_diff_total'],0) . '</td>':'') . 
                    '<td style="word-break: break-all;">' . $deduction_details[$i]['remarks'] . '</td>
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
                                    <th colspan="5" style="text-align:center;">SKU Details</th>
                                    <th colspan="2" style="text-align:center;">Quantity Deducted</th>
                                    <th colspan="6" style="text-align:center;">Amount Deducted (Per Unit)</th>
                                    <th colspan="6" style="text-align:center;">Amount Deducted (Total)</th>' . 
                                    (($ded_type=='expiry')?
                                    '<th colspan="2" style="'.$expiry_style.'text-align:center;">Expiry Dates</th>':'') . 
                                    (($ded_type=='margindiff')?
                                    '<th colspan="7" style="'.$margindiff_style.'">Purchase Order Details</th>
                                    <th colspan="6" style="'.$margindiff_style.'">Margin Difference Details</th>':'') . 
                                    '<th rowspan="2" style="width: 15%">Remarks</th>
                                </tr>
                                <tr>
                                    <th style="text-align:center;"> Sr. No.</th>
                                    <th>SKU Code</th>
                                    <th>SKU Name</th>
                                    <th>EAN Code</th>
                                    <th>HSN Code</th>
                                    <th>Quantity</th>
                                    <th>MRP</th>
                                    <th>Cost Excl Tax</th>
                                    <th>CGST</th>
                                    <th>SGST</th>
                                    <th>IGST</th>
                                    <th>Tax</th>
                                    <th>Total</th>
                                    <th>Cost Excl Tax</th>
                                    <th>CGST</th>
                                    <th>SGST</th>
                                    <th>IGST</th>
                                    <th>Tax</th>
                                    <th>Total</th>' . 
                                    (($ded_type=='expiry')?
                                    '<th style="'.$expiry_style.'">Date Received</th>
                                    <th style="'.$expiry_style.'">Earliest Expected Date</th>':'') . 
                                    (($ded_type=='margindiff')?
                                    '<th style="'.$margindiff_style.'">MRP</th>
                                    <th style="'.$margindiff_style.'">Cost Excl Tax</th>
                                    <th style="'.$margindiff_style.'">CGST</th>
                                    <th style="'.$margindiff_style.'">SGST</th>
                                    <th style="'.$margindiff_style.'">IGST</th>
                                    <th style="'.$margindiff_style.'">Tax</th>
                                    <th style="'.$margindiff_style.'">Total</th>
                                    <th style="'.$margindiff_style.'">Difference in Cost Excl Tax</th>
                                    <th style="'.$margindiff_style.'">Difference in CGST</th>
                                    <th style="'.$margindiff_style.'">Difference in SGST</th>
                                    <th style="'.$margindiff_style.'">Difference in IGST</th>
                                    <th style="'.$margindiff_style.'">Difference in Tax</th>
                                    <th style="'.$margindiff_style.'">Difference in Total</th>':'') . 
                                '</tr>
                            </thead>
                            <tbody>' . $result . '</tbody>
                        </table>   
                    </div>';

            echo $table;
            $result = '';
            $sr_no = 1;
        }
    } 
?>

 
</body>
</html>
