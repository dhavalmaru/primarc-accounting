<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Grn */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grn-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php $formatter = \Yii::$app->formatter; ?>

    <div class="row">
        <div class="form-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <label class="col-md-2 col-sm-2 col-xs-12 control-label">Scan Date </label>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <!-- <input type="text" class="form-control" name="gi_date" id="gi_date" placeholder="Scan Date" value="<?//= $grn_details[0]['gi_date'] ?>" readonly/> -->
                    <label><?= $grn_details[0]['gi_date'] ?></label>
                </div>
                <label class="col-md-2 col-sm-2 col-xs-12 control-label">Grn No </label>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <!-- <input type="text" class="form-control" name="gi_id" id="gi_id" placeholder="Grn No" value="<?//= $grn_details[0]['gi_id'] ?>" readonly/> -->
                    <label><?= $grn_details[0]['gi_id'] ?></label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <label class="col-md-2 col-sm-2 col-xs-12 control-label">Vendor Name </label>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <!-- <input type="text" class="form-control" name="vendor_name" id="vendor_name" placeholder="Vendor Name" value="<?//= $grn_details[0]['vendor_name'] ?>" readonly/> -->
                    <label><?= $grn_details[0]['vendor_name'] ?></label>
                </div>
                <label class="col-md-2 col-sm-2 col-xs-12 control-label">Category Name </label>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <!-- <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Category Name" value="<?//= $grn_details[0]['category_name'] ?>" readonly/> -->
                    <label><?= $grn_details[0]['category_name'] ?></label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <label class="col-md-2 col-sm-2 col-xs-12 control-label">Location </label>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <!-- <input type="text" class="form-control" name="location" id="location" placeholder="Location" value="<?//= $grn_details[0]['location'] ?>" readonly/> -->
                    <label><?= $grn_details[0]['location'] ?></label>
                </div>
                <label class="col-md-2 col-sm-2 col-xs-12 control-label">Vat/Cst </label>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <!-- <input type="text" class="form-control" name="vat_cst" id="vat_cst" placeholder="Vat/Cst" value="<?//= $grn_details[0]['vat_cst'] ?>" readonly/> -->
                    <label><?= $grn_details[0]['vat_cst'] ?></label>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>Sr No</th>
            <th>Particulars</th>
            <th>Sub Particulars</th>
            <th>Value</th>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <th class="text-center">
                    <input type="hidden" id="invoice_no_<?php echo $i;?>" name="invoice_no[]" value="<?php echo $invoice_details[$i]['invoice_no']; ?>" />
                    Invoice <br/> <?php echo $invoice_details[$i]['invoice_no']; ?>
                </th>
                <th>Edited <br/> <?php echo $invoice_details[$i]['invoice_no']; ?></th>
                <th>Difference <br/> <?php echo $invoice_details[$i]['invoice_no']; ?></th>
            <?php } ?>
            <th>Narration</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Taxable Amount</td>
            <td></td>
            <td>
                <input type="text" class="text-right" id="taxable_amount" name="taxable_amount" value="<?php echo $formatter->asDecimal($total_val[0]['total_cost'], 2); ?>" readonly />
            </td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td>
                    <input type="text" class="text-right" id="invoice_taxable_amount_<?php echo $i;?>" name="invoice_taxable_amount[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['total_cost'], 2); ?>" readonly />
                </td>
                <td>
                    <input type="text" class="text-right" id="edited_taxable_amount_<?php echo $i;?>" name="edited_taxable_amount[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['total_cost'], 2); ?>" onChange="getDifference(this);" />
                </td>
                <td>
                    <input type="text" class="text-right" id="diff_taxable_amount_<?php echo $i;?>" name="diff_taxable_amount[]" value="" readonly />
                </td>
            <?php } ?>
            <td></td>
        </tr>
        <tr>
            <td>2</td>
            <td>Tax</td>
            <td></td>
            <td>
                <input type="text" class="text-right" id="total_tax" name="total_tax" value="<?php echo $formatter->asDecimal($total_val[0]['total_tax'], 2); ?>" readonly />
            </td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td>
                    <input type="text" class="text-right" id="invoice_total_tax_<?php echo $i;?>" name="invoice_total_tax[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['total_tax'], 2); ?>" readonly />
                </td>
                <td>
                    <input type="text" class="text-right" id="edited_total_tax_<?php echo $i;?>" name="edited_total_tax[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['total_tax'], 2); ?>" onChange="getDifference(this);" />
                </td>
                <td>
                    <input type="text" class="text-right" id="diff_total_tax_<?php echo $i;?>" name="diff_total_tax[]" value="" readonly />
                </td>
            <?php } ?>
            <td></td>
        </tr>
        <?php for($j=0; $j<count($total_tax); $j++) { ?>
        <tr>
            <td></td>
            <td></td>
            <td>
                <input type="hidden" id="vat_cst_<?php echo $j;?>" name="vat_cst[]" value="<?php echo $total_tax[$j]['vat_cst']; ?>" />
                <input type="hidden" id="vat_percen_<?php echo $j;?>" name="vat_percen[]" value="<?php echo $total_tax[$j]['vat_percen']; ?>" />
                <?php echo $total_tax[$j]['vat_cst'].'@'.$total_tax[$j]['vat_percen'].'%'; ?>
            </td>
            <td class="text-right">
                <input type="text" class="text-right" id="total_tax_<?php echo $j;?>" name="total_tax_<?php echo $j;?>[]" value="<?php echo $formatter->asDecimal($total_tax[$j]['total_tax'], 2); ?>" readonly />
            </td>
            <?php for($i=0; $i<count($invoice_tax); $i++) { 
                    if($total_tax[$j]['vat_cst'] == $invoice_tax[$i]['vat_cst'] && $total_tax[$j]['vat_percen'] == $invoice_tax[$i]['vat_percen']) { ?>
                        <td>
                            <input type="text" class="text-right" id="invoice_<?php echo $i;?>_tax_<?php echo $j;?>" name="invoice_tax_<?php echo $j;?>[]" value="<?php echo $formatter->asDecimal($invoice_tax[$i]['total_tax'], 2); ?>" readonly />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="edited_<?php echo $i;?>_tax_<?php echo $j;?>" name="edited_tax_<?php echo $j;?>[]" value="<?php echo $formatter->asDecimal($invoice_tax[$i]['total_tax'], 2); ?>" onChange="getDifference(this);" />
                        </td>
                        <td>
                            <input type="text" class="text-right" id="diff_<?php echo $i;?>_tax_<?php echo $j;?>" name="diff_tax_<?php echo $j;?>[]" value="" readonly />
                        </td>
            <?php }} ?>
            <td></td>
        </tr>
        <?php } ?>
        <tr>
            <td>3</td>
            <td>Other Charges</td>
            <td></td>
            <td>
                <input type="text" class="text-right" id="other_amount" name="other_amount" value="<?php //echo $formatter->asDecimal($total_val[0]['other_amount'], 2); ?>" readonly />
            </td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td>
                    <input type="text" class="text-right" id="invoice_other_charges_<?php echo $i;?>" name="invoice_other_charges[]" value="<?php //echo $formatter->asDecimal($invoice_details[$i]['other_charges'], 2); ?>" readonly />
                </td>
                <td>
                    <input type="text" class="text-right" id="edited_other_charges_<?php echo $i;?>" name="edited_other_charges[]" value="<?php //echo $formatter->asDecimal($invoice_details[$i]['other_charges'], 2); ?>" onChange="getDifference(this);" />
                </td>
                <td>
                    <input type="text" class="text-right" id="diff_other_charges_<?php echo $i;?>" name="diff_other_charges[]" value="" readonly />
                </td>
            <?php } ?>
            <td></td>
        </tr>
        <tr style="background-color:#ffe699;">
            <td></td>
            <td>Total Amount</td>
            <td></td>
            <td id="total_amount" class="text-right">

            </td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td id="invoice_total_amount_<?php echo $i;?>" class="text-right">

                </td>
                <td id="edited_total_amount_<?php echo $i;?>" class="text-right">

                </td>
                <td id="diff_total_amount_<?php echo $i;?>" class="text-right">

                </td>
            <?php } ?>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td></td>
                <td></td>
                <td></td>
            <?php } ?>
            <td></td>
        </tr>
        <tr>
            <td>4</td>
            <td>Less Amount - Shortage</td>
            <td></td>
            <td>
                <input type="text" class="text-right" id="shortage_amount" name="shortage_amount" value="<?php echo $formatter->asDecimal($total_val[0]['shortage_amount'], 2); ?>" readonly />
            </td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td>
                    <input type="text" class="text-right" id="invoice_shortage_amount_<?php echo $i;?>" name="invoice_shortage_amount[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['shortage_amount'], 2); ?>" readonly />
                </td>
                <td>
                    <input type="text" class="text-right" id="edited_shortage_amount_<?php echo $i;?>" name="edited_shortage_amount[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['shortage_amount'], 2); ?>" onChange="getDifference(this);" />
                </td>
                <td>
                    <input type="text" class="text-right" id="diff_shortage_amount_<?php echo $i;?>" name="diff_shortage_amount[]" value="" readonly />
                </td>
            <?php } ?>
            <td></td>
        </tr>
        <tr>
            <td>5</td>
            <td>Less Amount - Expiry</td>
            <td></td>
            <td>
                <input type="text" class="text-right" id="expiry_amount" name="expiry_amount" value="<?php echo $formatter->asDecimal($total_val[0]['expiry_amount'], 2); ?>" readonly />
            </td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td>
                    <input type="text" class="text-right" id="invoice_expiry_amount_<?php echo $i;?>" name="invoice_expiry_amount[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['expiry_amount'], 2); ?>" readonly />
                </td>
                <td>
                    <input type="text" class="text-right" id="edited_expiry_amount_<?php echo $i;?>" name="edited_expiry_amount[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['expiry_amount'], 2); ?>" onChange="getDifference(this);" />
                </td>
                <td>
                    <input type="text" class="text-right" id="diff_expiry_amount_<?php echo $i;?>" name="diff_expiry_amount[]" value="" readonly />
                </td>
            <?php } ?>
            <td></td>
        </tr>
        <tr>
            <td>6</td>
            <td>Less Amount - Damage</td>
            <td></td>
            <td>
                <input type="text" class="text-right" id="damaged_amount" name="damaged_amount" value="<?php echo $formatter->asDecimal($total_val[0]['damaged_amount'], 2); ?>" readonly />
            </td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td>
                    <input type="text" class="text-right" id="invoice_damaged_amount_<?php echo $i;?>" name="invoice_damaged_amount[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['damaged_amount'], 2); ?>" readonly />
                </td>
                <td>
                    <input type="text" class="text-right" id="edited_damaged_amount_<?php echo $i;?>" name="edited_damaged_amount[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['damaged_amount'], 2); ?>" onChange="getDifference(this);" />
                </td>
                <td>
                    <input type="text" class="text-right" id="diff_damaged_amount_<?php echo $i;?>" name="diff_damaged_amount[]" value="" readonly />
                </td>
            <?php } ?>
            <td></td>
        </tr>
        <tr>
            <td>7</td>
            <td>Less Amount - Margin Diff</td>
            <td></td>
            <td>
                <input type="text" class="text-right" id="mrp_issue_amount" name="mrp_issue_amount" value="<?php echo $formatter->asDecimal($total_val[0]['mrp_issue_amount'], 2); ?>" readonly />
            </td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td>
                    <input type="text" class="text-right" id="invoice_mrp_issue_amount_<?php echo $i;?>" name="invoice_mrp_issue_amount[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['mrp_issue_amount'], 2); ?>" readonly />
                </td>
                <td>
                    <input type="text" class="text-right" id="edited_mrp_issue_amount_<?php echo $i;?>" name="edited_mrp_issue_amount[]" value="<?php echo $formatter->asDecimal($invoice_details[$i]['mrp_issue_amount'], 2); ?>" onChange="getDifference(this);" />
                </td>
                <td>
                    <input type="text" class="text-right" id="diff_mrp_issue_amount_<?php echo $i;?>" name="diff_mrp_issue_amount[]" value="" readonly />
                </td>
            <?php } ?>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td></td>
                <td></td>
                <td></td>
            <?php } ?>
            <td></td>
        </tr>
        <tr style="background-color:#ffe699;">
            <td></td>
            <td>Total Deduction</td>
            <td></td>
            <td id="total_deduction" class="text-right">

            </td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td id="invoice_total_deduction_<?php echo $i;?>" class="text-right">

                </td>
                <td id="edited_total_deduction_<?php echo $i;?>" class="text-right">
                    
                </td>
                <td id="diff_total_deduction_<?php echo $i;?>" class="text-right">

                </td>
            <?php } ?>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td></td>
                <td></td>
                <td></td>
            <?php } ?>
            <td></td>
        </tr>
        <tr style="background-color:#ffe699;">
            <td></td>
            <td>Total Payable Amount</td>
            <td></td>
            <td id="total_payable_amount" class="text-right">

            </td>
            <?php for($i=0; $i<count($invoice_details); $i++) { ?>
                <td id="invoice_total_payable_amount_<?php echo $i;?>" class="text-right">

                </td>
                <td id="edited_total_payable_amount_<?php echo $i;?>" class="text-right">
                    
                </td>
                <td id="diff_total_payable_amount_<?php echo $i;?>" class="text-right">

                </td>
            <?php } ?>
            <td></td>
        </tr>
    </table>

    <div class="form-group">
        <?= Html::submitButton($grn_details['isNewRecord'] ? 'Create' : 'Update', ['class' => $grn_details['isNewRecord'] ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php //$baseUrl = Yii::app->request->baseUrl; ?>
    <?php //echo $baseUrl; ?>

    <script type="text/javascript" src="<?php echo Url::base(); ?>js/jquery/jquery.min.js"></script>

    <script>
        console.log("<?php echo Url::base(); ?>");


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

        $(document).ready(function(){
            getTotal();
        });

        function getDifference(a){
            var id = a.id;
            var index = id.substr(id.lastIndexOf("_")+1);
            var invoiceId = id.replace("edited", "invoice");
            var diffId = id.replace("edited", "diff");
            var invoiceAmt = get_number($("#"+invoiceId).val(),2);
            var editedAmt = get_number($("#"+id).val(),2);
            var diffAmt = invoiceAmt-editedAmt;
            $("#"+diffId).val(diffAmt.toFixed(2));

            getTotal();
        }

        function getTotal(){
            var taxable_amount = get_number($("#taxable_amount").val(),2);
            var total_tax = get_number($("#total_tax").val(),2);
            var other_amount = get_number($("#other_amount").val(),2);
            var total_amount = taxable_amount + total_tax + other_amount;
            $("#total_amount").html(total_amount);

            var shortage_amount = get_number($("#shortage_amount").val(),2);
            var expiry_amount = get_number($("#expiry_amount").val(),2);
            var damaged_amount = get_number($("#damaged_amount").val(),2);
            var margin_diff_amount = get_number($("#margin_diff_amount").val(),2);
            var total_deduction = shortage_amount + expiry_amount + damaged_amount + margin_diff_amount;
            $("#total_deduction").html(total_deduction);

            var total_payable_amount = total_amount - total_deduction;
            $("#total_payable_amount").html(total_payable_amount.toFixed(2));

            var invoices = <?php echo count($invoice_details);?>

            for(var i=0; i<invoices; i++){
                taxable_amount = get_number($("#invoice_taxable_amount_"+i).val(),2);
                total_tax = get_number($("#invoice_total_tax_"+i).val(),2);
                other_amount = get_number($("#invoice_other_amount_"+i).val(),2);
                total_amount = taxable_amount + total_tax + other_amount;
                $("#invoice_total_amount_"+i).html(total_amount);

                shortage_amount = get_number($("#invoice_shortage_amount_"+i).val(),2);
                expiry_amount = get_number($("#invoice_expiry_amount_"+i).val(),2);
                damaged_amount = get_number($("#invoice_damaged_amount_"+i).val(),2);
                margin_diff_amount = get_number($("#invoice_margin_diff_amount_"+i).val(),2);
                total_deduction = shortage_amount + expiry_amount + damaged_amount + margin_diff_amount;
                $("#invoice_total_deduction_"+i).html(total_deduction);

                total_payable_amount = total_amount - total_deduction;
                $("#invoice_total_payable_amount_"+i).html(total_payable_amount.toFixed(2));

                taxable_amount = get_number($("#edited_taxable_amount_"+i).val(),2);
                total_tax = get_number($("#edited_total_tax_"+i).val(),2);
                other_amount = get_number($("#edited_other_amount_"+i).val(),2);
                total_amount = taxable_amount + total_tax + other_amount;
                $("#edited_total_amount_"+i).html(total_amount);

                shortage_amount = get_number($("#edited_shortage_amount_"+i).val(),2);
                expiry_amount = get_number($("#edited_expiry_amount_"+i).val(),2);
                damaged_amount = get_number($("#edited_damaged_amount_"+i).val(),2);
                margin_diff_amount = get_number($("#edited_margin_diff_amount_"+i).val(),2);
                total_deduction = shortage_amount + expiry_amount + damaged_amount + margin_diff_amount;
                $("#edited_total_deduction_"+i).html(total_deduction);

                total_payable_amount = total_amount - total_deduction;
                $("#edited_total_payable_amount_"+i).html(total_payable_amount.toFixed(2));

                taxable_amount = get_number($("#diff_taxable_amount_"+i).val(),2);
                total_tax = get_number($("#diff_total_tax_"+i).val(),2);
                other_amount = get_number($("#diff_other_amount_"+i).val(),2);
                total_amount = taxable_amount + total_tax + other_amount;
                $("#diff_total_amount_"+i).html(total_amount);

                shortage_amount = get_number($("#diff_shortage_amount_"+i).val(),2);
                expiry_amount = get_number($("#diff_expiry_amount_"+i).val(),2);
                damaged_amount = get_number($("#diff_damaged_amount_"+i).val(),2);
                margin_diff_amount = get_number($("#diff_margin_diff_amount_"+i).val(),2);
                total_deduction = shortage_amount + expiry_amount + damaged_amount + margin_diff_amount;
                $("#diff_total_deduction_"+i).html(total_deduction);

                total_payable_amount = total_amount - total_deduction;
                $("#diff_total_payable_amount_"+i).html(total_payable_amount.toFixed(2));
            }
        }
    </script>

    <?php ActiveForm::end(); ?>

</div>
