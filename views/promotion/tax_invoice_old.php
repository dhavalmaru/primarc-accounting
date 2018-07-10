<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Tax Invoice</title>
        <style>
        @font-face {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        @media print{@page {size: portrait}}
        body { font-family: "verdana"; font-size:10px; font-weight:500; margin:0; line-height:11px;}
        tr,td {
           font-size:11px!important;
        }
       </style>
    </head>

    <?php $mycomponent = Yii::$app->mycomponent; ?>

    <body style="margin: 1px;">
    <div style="width:100%;  float:left; margin-right:20px;">
        <table cellspacing="0" cellpadding="5" border="1" style="border-collapse: collapse; width:70%; margin:auto; font-family:Arcon-Regular, OpenSans-Regular, Arcon, Verdana, Geneva, sans-serif; font-size:8px; font-weight:400; border:1px solid #666;">
            <col width="43" />
            <col width="115" />
            <col width="110" />
            <col width="112" />
            <col width="83" />
            <col width="92" />
            <col width="95" />
            <col width="64" />
            <tr>
                <td colspan="6" align="center" valign="top" style="padding:5px;border-spacing: 5px;">
                    <table width="100%">
                        <tr>
                            <td><h1 style="padding:0; margin:0; font-size:20px; text-align: center;"> Tax Invoice</h1></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="6" valign="top" style="line-height:12px; padding:0;"> 
                    <table width="100%"  border="0" cellspacing="0" cellpadding="2"  style="border-collapse: collapse;   ">
                        <tr>
                            <td colspan="10" valign="top" style="line-height:20px; padding:0; border:0;"> 
                                <table width="100%"  border="0" cellspacing="0" cellpadding="5">
                                    <tr style="border-bottom:1px solid #666;">
                                    <td width="50%" rowspan="3" style="line-height:12px; border-bottom:0px solid #666; ">
                                        <table width="100%"  border="0" cellspacing="0" cellpadding="2"  style="border-collapse: collapse;    ">
                                        <tr>
                                            <td>Primarc Pecan Retail (P) Ltd Mum(FY <?php if(isset($debit_note[0]['date_of_transaction'])) 
                                            echo $mycomponent->get_financial_year($debit_note[0]['date_of_transaction']); ?>)
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>210,214 Building No.2B, Sanjay Mittal Industrial Estate, Above Cafe Coffee Day, 
                                            Andheri-Kurla Road, Marol Naka, Andheri (East), Mumbai – 400059</td>
                                        </tr>
                                        <tr>
                                            <td>GSTIN:   27AACCT5910H1ZE</td>
                                        </tr>
                                        <tr>
                                            <td>State:  Maharashtra
                                            <table style="float: right;border-collapse:collapse;margin-right:10px;">
                                            <tr style="border-top:1px solid #666;">
                                                <td style="border-left:1px solid #666;border-right:1px solid #666;width:80px;" align="right">State Code: &nbsp;</td>
                                                <td style="border-right:1px solid #666;width:20px;text-align:center;">27</td>
                                            </tr>
                                            </table>
                                            <tr>
                                                <td>CIN:  U52100WB2006PTC111833</td>
                                            </tr>
                                            </td>
                                        </tr>
                                        </table>
                                    </td>
                                    <td width="32%" valign="top" style="line-height:20px;  border-right:1px solid #666;  border-bottom:1px solid #666; border-left:1px solid #666; padding-top: 0px; padding-bottom: 0px;">
                                        <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" > Invoice No.</span> <br /> 
                                        <?php if(isset($debit_note[0]['debit_note_ref'])) echo $debit_note[0]['debit_note_ref']; ?>
                                        </p>
                                    </td>
                                    <td width="30%" valign="top" style="line-height:20px; padding-top: 0px; padding-bottom: 0px;border-bottom:1px solid #666;">
                                        <p style="margin: 0px;">  <span style=" font-size:12px; font-weight:500;" >Dated </span>  <br />
                                            <?php if(isset($debit_note[0]['date_of_transaction'])) 
                                            echo (($debit_note[0]['date_of_transaction']!=null && $debit_note[0]['date_of_transaction']!='')?
                                            date('d/m/Y',strtotime($debit_note[0]['date_of_transaction'])):''); ?>
                                        </p>
                                    </td>
                                    </tr>
                                    <tr style="border-bottom:0px solid #666;">
                                        <td valign="top" style="line-height:20px; border-bottom:1px solid #666; border-right:1px solid #666;  border-left:1px solid #666; padding-top: 0px; padding-bottom: 0px;">
                                            <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" >Delivery Note   </span> <br /> 

                                            </p>
                                        </td>
                                        <td  valign="top" style="line-height:20px; padding-top: 0px; padding-bottom: 0px; border-bottom:1px solid #666;">
                                            <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" >Mode/ Terms Of Payment</span> <br /> 

                                            </p>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom:1px solid #666;">
                                        <td valign="top" style="line-height:20px; border-bottom:0px solid #666; border-right:1px solid #666;  border-left:1px solid #666; padding-top: 0px; padding-bottom: 0px;">
                                            <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" >Supplier's Reference   </span> <br /> 

                                            </p>
                                        </td>
                                        <td  valign="top" style="line-height:20px; padding-top: 0px; padding-bottom: 0px;">
                                            <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" >Other Reference(S) </span> <br /> 

                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>  
                </td>
            </tr>

            <tr><td colspan="6"></td></tr>

            <tr>
                <td colspan="6" valign="top" style="line-height:12px; padding:0;border-bottom:none;" > 
                    <table width="100%"  border="0" cellspacing="0" cellpadding="2" align="left" style="border-collapse: collapse;    ">
                        <tr style="border-bottom:0px solid #666; height: 20px;"  >
                        <td   style="line-height:12px; border-bottom:0px solid #666; text-align:left;font-weight:bolder;padding-left:10px">
                        Details of Buyer | Billed to:
                        </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="6" valign="top" style="line-height:12px; padding:0;"> 
                    <table width="100%"  border="0" cellspacing="0" cellpadding="2"  style="border-collapse: collapse;    ">
                        <tr>
                        <td colspan="10" valign="top" style="line-height:20px; padding:0; border:0;"> 
                            <table width="100%"  border="0" cellspacing="0" cellpadding="5">
                                <tr style="border-bottom:1px solid #666;">
                                <td width="50%" rowspan="5" style="line-height:12px; border-bottom:0px solid #666; ">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="2"  style="border-collapse: collapse;    ">
                                    <tr>
                                        <td><?php if(isset($vendor_details[0]['account_holder_name'])) echo $vendor_details[0]['account_holder_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php if(isset($vendor_details[0]['office_address_line_1'])) echo $vendor_details[0]['office_address_line_1']; ?> &nbsp;
                                            <?php if(isset($vendor_details[0]['office_address_line_2'])) echo $vendor_details[0]['office_address_line_2']; ?> &nbsp;
                                            <?php if(isset($vendor_details[0]['office_address_line_3'])) echo $vendor_details[0]['office_address_line_3']; ?> &nbsp;
                                            <?php if(isset($vendor_details[0]['city_name'])) echo $vendor_details[0]['city_name']; ?> <?php if(isset($vendor_details[0]['pincode'])) echo $vendor_details[0]['pincode']; ?> &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>GSTIN:   <?php if(isset($vendor_details[0]['gst_id'])) echo $debit_note[0]['gst_id']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>State:  <?php if(isset($vendor_details[0]['state_name'])) echo $vendor_details[0]['state_name']; ?>
                                            <table style="float: right;border-collapse:collapse;margin-right:10px;">
                                            <tr style="border-top:1px solid #666;">
                                                <td style="border-left:1px solid #666;border-right:1px solid #666;width:80px;" align="right">State Code: &nbsp;</td>
                                                <td style="border-right:1px solid #666;width:20px;text-align:center;"><?php if(isset($vendor_details[0]['state_code'])) echo $vendor_details[0]['state_code']; ?></td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                </td>
                                <td width="32%" valign="top" style="line-height:20px;  border-right:1px solid #666;  border-bottom:1px solid #666; border-left:1px solid #666; padding-top: 0px; padding-bottom: 0px;">
                                    <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" > Buyers Order No.</span> <br /> 

                                    </p>
                                </td>
                                <td width="30%" valign="top" style="line-height:20px; padding-top: 0px; padding-bottom: 1px;border-bottom:1px solid #666;">
                                    <p style="margin: 0px;">  <span style=" font-size:12px; font-weight:500;" >Dated </span>  <br />

                                    </p>
                                </td>
                                </tr>
                                <tr style="border-bottom:0px solid #666;">
                                    <td valign="top" style="line-height:20px; border-bottom:1px solid #666; border-right:1px solid #666;  border-left:1px solid #666; padding-top: 0px; padding-bottom: 0px;">
                                        <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" >Dispatch Document No.   </span> <br /> 

                                        </p>
                                    </td>
                                    <td  valign="top" style="line-height:20px; padding-top: 0px;  padding-bottom: 0px; border-bottom:1px solid #666;" >
                                        <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" >Delivery Note Date</span> <br /> 

                                        </p>
                                    </td>
                                </tr>
                                <tr style="border-bottom:1px solid #666;">
                                    <td valign="top" style="line-height:20px; border-bottom:1px solid #666; border-right:1px solid #666;  border-left:1px solid #666; padding-top: 0px; padding-bottom: 0px;">
                                        <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" >Dispatch Through   </span> <br /> 

                                        </p>
                                    </td>
                                    <td  valign="top" style="line-height:20px; padding-top: 0px; padding-bottom: 0px; border-bottom:1px solid #666;">
                                        <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" >Destination </span> <br /> 

                                        </p>
                                    </td>
                                </tr>
                                <tr style="border-bottom:1px solid #666;">
                                    <td  colspan="2"  valign="top" style="line-height:20px; border-bottom:0px solid #666;  border-left:1px solid #666; padding-top: 0px; padding-bottom: 0px;">
                                        <p style="margin: 0px;"> <span style=" font-size:12px; font-weight:500;" >Terms Of Delivery   </span> <br /> 

                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        </tr>
                    </table>  
                </td>
            </tr>

            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>

            <tr style="font-size:8px; font-weight:500;">
                <td colspan="6" style="padding: 0;">
                    <table style="width:100%;" width="100%"  border="0" cellspacing="0" cellpadding="2"  style="border-collapse: collapse;    ">
                        <tr style="background:#ececec;">
                            <td width="20" align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;">Sr No.</td>
                            <td width="600" align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;">Perticulars</td>
                            <td width="50" align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;">HSN/SAC</td>
                            <td width="30" align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;">Qty</td>
                            <td width="60"  align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;">Rate</td>
                            <td width="20" align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;">Per</td>
                            <td width="60" align="center" valign="middle" style="border-bottom:1px solid #666;"> Amount </td>
                        </tr>

                        <?php for($i=0; $i<count($invoice_details)-1; $i++){ ?>
                        <tr valign="top">
                            <td valign="top" align="center" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;border-right:1px solid #666; "><?php echo $invoice_details[$i]['sr_no']; ?></td>
                            <td valign="top" align="left" style="border-left:1px solid #666;  border-top: none; border-bottom:none;border:none;border-right:1px solid #666;"><?php echo $invoice_details[$i]['particulars']; ?></td>
                            <td width="130" valign="top" align="right" style="border-left:1px solid #666;  border-top: none; border-bottom:none;border:none;border-right:1px solid #666;"><p style="margin:0; "><?php echo $invoice_details[$i]['code']; ?></p></td>
                            <td width="30" valign="top" align="right" style="border-left:1px solid #666;  border-top: none; border-bottom:none;border:none;border-right:1px solid #666;"><p style="margin:0; "><?php echo $invoice_details[$i]['qty']; ?></p></td>
                            <td valign="top" align="right" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;border-right:1px solid #666; "><p style="margin:0; "><?php echo $invoice_details[$i]['rate']; ?></p></td>
                            <td valign="top" align="right" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;border-right:1px solid #666;"><p style="margin:0; "><?php echo $invoice_details[$i]['per']; ?></p></td>
                            <td width="150" valign="top" align="right" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;"><p style="margin:0; "><?php echo $invoice_details[$i]['amount']; ?></p></td>
                        </tr>
                        <?php } ?>

                        <?php $i = count($invoice_details)-1; ?>
                        <tr valign="top" style="border-top:1px solid #666;background:#ececec;">
                            <td valign="top" align="right" colspan="2" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;border-right:1px solid #666;border-top:1px solid #666; ">Total</td>
                            <td valign="top" align="right" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;border-right:1px solid #666;border-top:1px solid #666; "><p style="margin:0; "></p></td>
                            <td valign="top" align="right" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;border-right:1px solid #666;border-top:1px solid #666; "><p style="margin:0; "></p></td>
                            <td align="right" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;border-top:1px solid #666;padding:0;"><p style="margin:0; "></p></td>
                            <td align="right" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;border-top:1px solid #666;padding:0;"><p style="margin:0; "></p></td>
                            <td  width="150" valign="top" align="right" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;border-top:1px solid #666;"><p style="margin:0; "></p><?php echo $invoice_details[$i]['amount']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr><td colspan="6"></td></tr>

            <tr style="border-top: 1px solid #666;">
                <td colspan="6"   valign="top" style="padding:10px;">
                    <p style="margin:0;text-align:left;line:height:22px;font-size:18px;">
                        <span style="  font-size:16px; font-weight:500; display: block;" > 
                            Amount Chargeable (in words): <br/><br/> 
                        </span> <?php if(isset($invoice_details[$i]['amount'])) echo $mycomponent->convert_number_to_words(round($invoice_details[$i]['amount'],2)); ?>
                    </p> 
                </td>
            </tr>

            <tr style="border-top:1px solid #666;background:#ececec;">
                <td width="60" align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;">HSN/SAC</td>
                <td width="60" align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;">Taxable Value</td>
                <td width="150" align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;padding:0;">
                    <table style="width: 100%;border-spacing: 0;height: 36px;"><tr><td colspan="2" style="text-align: center;">CGST</td></tr><tr><td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;text-align: center;" width="50%">Rate</td><td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;text-align: center;" width="50%">Amount</td></tr></table>
                </td>
                <td width="150" align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;padding:0;">
                    <table style="width: 100%;border-spacing: 0;height: 36px;"><tr><td colspan="2" style="text-align: center;">SGST</td></tr><tr><td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;text-align: center;" width="50%">Rate</td><td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;text-align: center;" width="50%">Amount</td></tr></table>
                </td>
                <td width="150" align="center" valign="middle" style="border-right:1px solid #666;border-bottom:1px solid #666;padding:0;">
                    <table style="width: 100%;border-spacing: 0;height: 36px;"><tr><td colspan="2" style="text-align: center;">IGST</td></tr><tr><td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;text-align: center;" width="50%">Rate</td><td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 1px solid #666;text-align: center;" width="50%">Amount</td></tr></table>
                </td>
                <td width="60" align="center" valign="middle" style="border-right:none;border-bottom:1px solid #666;">Total</td>
            </tr>

            <?php for($i=0; $i<count($inv_tax_details)-1; $i++){ ?>
            <tr valign="top" style="border: none;">
                <td valign="top" align="right" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;border-right:1px solid #666;"><p style="margin:0; ">998311</p></td>
                <td valign="top" align="right" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;border-right:1px solid #666;"><p style="margin:0; "><?php echo $inv_tax_details[$i]['value']; ?></p></td>
                <td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;padding:0;">
                    <table style="width: 100%;border-spacing: 0; height: 17px;">
                        <tbody>
                            <tr>
                                <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['cgst_rate']; ?></td>
                                <td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['cgst_amt']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;padding:0;">
                    <table style="width: 100%;border-spacing: 0; height: 17px;">
                        <tbody>
                            <tr>
                                <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['sgst_rate']; ?></td>
                                <td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['sgst_amt']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;padding:0;">
                    <table style="width: 100%;border-spacing: 0; height: 17px;">
                        <tbody>
                            <tr>
                                <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['igst_rate']; ?></td>
                                <td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['igst_amt']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td valign="top" align="right" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;">
                    <p style="margin:0; ">
                        <?php echo $inv_tax_details[$i]['value'] + $inv_tax_details[$i]['cgst_amt'] + 
                                    $inv_tax_details[$i]['sgst_amt'] + $inv_tax_details[$i]['igst_amt']; ?>
                    </p>
                </td>
            </tr>
            <?php } ?>

            <?php $i = count($inv_tax_details)-1; ?>
            <tr style="border-top:1px solid #666;background:#ececec;">
                <td colspan="2" align="right" valign="middle" style="border-right:none;border-bottom:1px solid #666;">Total</td>
                <td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;padding:0;">
                    <table style="width: 100%;border-spacing: 0; height: 17px;">
                        <tbody>
                            <tr>
                                <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"></td>
                                <td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['cgst_amt']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;padding:0;">
                    <table style="width: 100%;border-spacing: 0; height: 17px;">
                        <tbody>
                            <tr>
                                <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"></td>
                                <td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['sgst_amt']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td align="center" valign="middle" style="border-right:1px solid #666;border-bottom:0px solid #666;padding:0;">
                    <table style="width: 100%;border-spacing: 0; height: 17px;">
                        <tbody>
                            <tr>
                                <td style="border-right:1px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"></td>
                                <td style="border-right:0px solid #666;border-bottom:0px solid #666;border-top: 0px solid #666;text-align: right;" width="50%"><?php echo $inv_tax_details[$i]['igst_amt']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td valign="top" align="right" style="border-left:1px solid #666; border-top: none; border-bottom:none;border:none;">
                    <p style="margin:0; "><?php echo $inv_tax_details[$i]['total_amt']; ?></p>
                </td>
            </tr>

            <tr> <td colspan="6"></td></tr>
            <tr>
                <td colspan="4"   valign="top" style="padding:5px;">
                    <p style="margin:0;text-align:left;line:height:22px;font-size:18px;"><span style="  font-size:16px; font-weight:500; display: block;" >Tax Amount (in words): <br/><br> </span> <?php if(isset($inv_tax_details[$i]['total_amt'])) echo $mycomponent->convert_number_to_words(round($inv_tax_details[$i]['total_amt'],2)); ?> </p> <br>
                    <p style="margin:0;text-align:left;line:height:22px;font-size:14px;"><span style="  font-size:16px; font-weight:500; display: block;" >Remarks: <br/><br> </span> </p> <br>
                    <p style="margin:0;text-align:left;line:height:22px;font-size:11px;font-weight:bold;"><span style="  font-size:16px; font-weight:500; display: block;" >Company's PAN: <br/><br> </span> AACCT5910H </p> 
                </td>
                <td colspan="2" align="center" valign="top" style=" font-size:8px; font-weight:500;"> 
                    For Primarc Pecan Retail (P) Ltd Mum(FY <?php if(isset($debit_note[0]['date_of_transaction'])) 
                    echo $mycomponent->get_financial_year($debit_note[0]['date_of_transaction']); ?>) 
                    <br/> 
                    <!-- <img src="../../..//assets/invoice/stamp.jpg" height="50"  alt="Sign3 Rishit" />  -->
                    <br/><br/><br/><br/><br/><br/><br/><br/>
                    Authorised Signatory
                </td>
            </tr>

        </table>
        <p style="text-align:center; font-family:OpenSans-Regular, Arcon,Verdana, Geneva, sans-serif; font-size:8px; line-height:11px; margin-top:3px; margin-bottom:0;  ">SUBJECT TO MUMBAI JURISDICTION <br />
        This is a Computer Generated Invoice</p>
    </div>
    </body>
</html>