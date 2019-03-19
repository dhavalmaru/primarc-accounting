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

<body class="hold-transition">
<div class="debit_note">
    <div class="header-section">
        <h1><b> Primarc Pecan Retail (P) Ltd</b></h1>
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
                        <?php if(isset($debit_note[0]['date_of_transaction'])) 
                                echo (($debit_note[0]['date_of_transaction']!=null && $debit_note[0]['date_of_transaction']!='')?
                                date('d/m/Y',strtotime($debit_note[0]['date_of_transaction'])):''); ?> 
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
                        Bhiwandi
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
                        27AACCT5910H1ZE
                    </b>
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="6" height="10" style="border:none;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3"  style="border-left:none;"><p>Particulars</p></td>
            <td colspan="3" align="center"  style="border-right:none;"><p>Amount In Rs</p></td>
        </tr>
        <tr valign="top"  style="border:none; ">
            <td colspan="3"   style="border-left:none; border-bottom:none; ">
                <p>
                    Being debit note raised
                </p>
            </td>
            <td colspan="3" align="center" valign="top" style="border-right:none;"><p><b><?php if(isset($debit_note[0]['debit_amt'])) echo $mycomponent->format_money($debit_note[0]['debit_amt'],2); ?></b></p></td>
        </tr>
        <tr>
            <td  style="border:none; border-right:1px solid #999;" colspan="3"><p><b> Amount (in words) </b></p></td>
            <td colspan="3" style="border:none; border-left:1px solid #999;"></td>
        </tr>
        <tr>
            <td height="40" colspan="3" valign="top" style="border:none; border-bottom:1px solid #999; border-right:1px solid #999;"> 
                <p><?php if(isset($debit_note[0]['debit_amt'])) echo $mycomponent->convert_number_to_words(round($debit_note[0]['debit_amt'],2)); ?></p>
            </td>
            <td colspan="3" style="border:none; border-left:1px solid #999; border-bottom:1px solid #999;"></td>
        </tr>
        <!-- <tr valign="bottom" >
            <td colspan="6" style="border:none;">&nbsp;   </td>
            <td colspan="3" style="border:none;"></td>
        </tr> -->
        <tr valign="bottom" >
            <td colspan="6" style="border:none;"><p style="text-align: center;">In case we do not receive any communication within 5 working days, the debit note will be treated as confirmed / accepted. <br/>As provided u/s Section 34(1) of the CGST Act, 2017 kindly provide us the credit note for the above transaction. <br/>This is a computer generated debit note. No signature required &nbsp;</p></td>
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
