$(document).ready(function(){
    getTotal();
});

function getDifference(elem){
    var id = elem.id;
    var ded_type = id.substr(0, id.indexOf("_"));
    var invoiceId = id.replace("edited", "invoice");
    var diffId = id.replace("edited", "diff");
    var invoiceAmt = get_number($("#"+invoiceId).val(),2);
    var editedAmt = get_number($("#"+id).val(),2);
    var diffAmt = invoiceAmt-editedAmt;
    $("#"+diffId).val(format_money(diffAmt,2));

    getTotal(ded_type);
}

function getTotal(ded_type){
    // var taxable_amount = get_number($("#taxable_amount").val(),2);
    // var total_tax = get_number($("#total_tax").val(),2);

    var taxable_amount = 0;
    var total_tax = 0;
    var other_charges = get_number($("#other_charges").val(),2);

    for(var i=0; i<taxes; i++){
        taxable_amount = taxable_amount + get_number($("#total_cost_"+i).val(),2);
        total_tax = total_tax + get_number($("#total_tax_"+i).val(),2);
    }

    var total_amount = taxable_amount + total_tax + other_charges;
    $("#total_amount").val(format_money(total_amount,2));

    var shortage_amount = get_number($("#shortage_amount").val(),2);
    var expiry_amount = get_number($("#expiry_amount").val(),2);
    var damaged_amount = get_number($("#damaged_amount").val(),2);
    var margin_diff_amount = get_number($("#margin_diff_amount").val(),2);
    var total_deduction = shortage_amount + expiry_amount + damaged_amount + margin_diff_amount;
    $("#total_deduction").val(format_money(total_deduction,2));

    var total_payable_amount = total_amount - total_deduction;
    $("#total_payable_amount").html(format_money(total_payable_amount,2));

    // var invoices = <?php //echo count($invoice_details);?>

    for(var i=0; i<invoices; i++){
        // taxable_amount = get_number($("#invoice_taxable_amount_"+i).val(),2);
        // total_tax = get_number($("#invoice_total_tax_"+i).val(),2);
        taxable_amount = 0;
        total_tax = 0;
        for(var j=0; j<taxes; j++){
            taxable_amount = taxable_amount + get_number($("#invoice_"+i+"_cost_"+j).val(),2);
            total_tax = total_tax + get_number($("#invoice_"+i+"_tax_"+j).val(),2);
        }
        other_charges = get_number($("#invoice_other_charges_"+i).val(),2);
        total_amount = taxable_amount + total_tax + other_charges;
        $("#invoice_total_amount_"+i).val(format_money(total_amount,2));

        shortage_amount = get_number($("#invoice_shortage_amount_"+i).val(),2);
        expiry_amount = get_number($("#invoice_expiry_amount_"+i).val(),2);
        damaged_amount = get_number($("#invoice_damaged_amount_"+i).val(),2);
        margin_diff_amount = get_number($("#invoice_margin_diff_amount_"+i).val(),2);
        total_deduction = shortage_amount + expiry_amount + damaged_amount + margin_diff_amount;
        $("#invoice_total_deduction_"+i).val(format_money(total_deduction,2));

        total_payable_amount = total_amount - total_deduction;
        $("#invoice_total_payable_amount_"+i).html(format_money(total_payable_amount,2));

        // taxable_amount = get_number($("#edited_taxable_amount_"+i).val(),2);
        // total_tax = get_number($("#edited_total_tax_"+i).val(),2);
        taxable_amount = 0;
        total_tax = 0;
        for(var j=0; j<taxes; j++){
            taxable_amount = taxable_amount + get_number($("#edited_"+i+"_cost_"+j).val(),2);
            total_tax = total_tax + get_number($("#edited_"+i+"_tax_"+j).val(),2);
        }
        other_charges = get_number($("#edited_other_charges_"+i).val(),2);
        total_amount = taxable_amount + total_tax + other_charges;
        $("#edited_total_amount_"+i).val(format_money(total_amount,2));

        shortage_amount = get_number($("#edited_shortage_amount_"+i).val(),2);
        expiry_amount = get_number($("#edited_expiry_amount_"+i).val(),2);
        damaged_amount = get_number($("#edited_damaged_amount_"+i).val(),2);
        margin_diff_amount = get_number($("#edited_margin_diff_amount_"+i).val(),2);
        total_deduction = shortage_amount + expiry_amount + damaged_amount + margin_diff_amount;
        $("#edited_total_deduction_"+i).val(format_money(total_deduction,2));

        total_payable_amount = total_amount - total_deduction;
        $("#edited_total_payable_amount_"+i).val(format_money(total_payable_amount,2));

        // taxable_amount = get_number($("#diff_taxable_amount_"+i).val(),2);
        // total_tax = get_number($("#diff_total_tax_"+i).val(),2);
        taxable_amount = 0;
        total_tax = 0;
        for(var j=0; j<taxes; j++){
            taxable_amount = taxable_amount + get_number($("#diff_"+i+"_cost_"+j).val(),2);
            total_tax = total_tax + get_number($("#diff_"+i+"_tax_"+j).val(),2);
        }
        other_charges = get_number($("#diff_other_charges_"+i).val(),2);
        total_amount = taxable_amount + total_tax + other_charges;
        $("#diff_total_amount_"+i).val(format_money(total_amount,2));

        shortage_amount = get_number($("#diff_shortage_amount_"+i).val(),2);
        expiry_amount = get_number($("#diff_expiry_amount_"+i).val(),2);
        damaged_amount = get_number($("#diff_damaged_amount_"+i).val(),2);
        margin_diff_amount = get_number($("#diff_margin_diff_amount_"+i).val(),2);
        total_deduction = shortage_amount + expiry_amount + damaged_amount + margin_diff_amount;
        $("#diff_total_deduction_"+i).val(format_money(total_deduction,2));

        total_payable_amount = total_amount - total_deduction;
        $("#diff_total_payable_amount_"+i).html(format_money(total_payable_amount,2));
    }
}

$("#get_shortage_qty").click(function(){
    $("#shortage_modal").modal('show');
});
$("#get_expiry_qty").click(function(){
    $("#expiry_modal").modal('show');
});
$("#get_damaged_qty").click(function(){
    $("#damaged_modal").modal('show');
});
$("#get_margin_diff_qty").click(function(){
    $("#margin_diff_modal").modal('show');
});

$("#get_ledger").click(function(){
    $.ajax({
        url: BASE_URL+'index.php?r=pendinggrn%2Fgetledger',
        type: 'post',
        data: $("#form_purchase_details").serialize(),
        dataType: 'json',
        success: function (data) {
            // if (parseInt(data)) {
            //     $("#account_category_modal").modal('hide');
            // }
            // update_categories(data);
            // $("#account_category_modal").modal('hide');

            // console.log(data);
            var result = '';

            for(var i=0; i<data.length; i++){
                result = result + data[i];
            }

            // console.log(result);

            $("#ledger_details").html(result);
            
            $("#ledger_modal").modal('show');
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
});

function add_sku_details(elem){
    var elem_id = elem.id;
    var ded_type = elem_id.substr(0, elem_id.indexOf("_"));
    var grn_id = $("#grn_id").val();
    var sr_no = parseInt($('#'+ded_type+'_total_rows').val());
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    $.ajax({
        url: BASE_URL+'index.php?r=pendinggrn%2Fgetnewrow',
        type: 'post',
        data: {
                    ded_type : ded_type,
                    grn_id : grn_id,
                    sr_no : sr_no,
                    _csrf : csrfToken
                },
        dataType: 'html',
        success: function (data) {

            // console.log(data);

            // if (parseInt(data)) {
            //     $("#account_category_modal").modal('hide');
            // }
            // update_categories(data);
            // $("#account_category_modal").modal('hide');

            // console.log(data);
            // var result = '';

            // for(var i=0; i<data.length; i++){
            //     result = result + data[i];
            // }

            // console.log(result);

            // $("#ledger_details").html(result);
            
            // $("#ledger_modal").modal('show');

            $('#'+ded_type+'_sku_details tr:last').before(data);

            $('#'+ded_type+'_total_rows').val(sr_no+1);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}


function get_sku_details(elem){
    var elem_id = elem.id;
    if(elem_id.indexOf("_")>0){
        var index_val = elem_id.substr(elem_id.lastIndexOf("_")+1);
        var ded_type = elem_id.substr(0, elem_id.indexOf("_"));
        var psku = elem.value;
        var grn_id = $("#grn_id").val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        var col_qty = "";
        if(ded_type=="shortage"){
            col_qty = "shortage_qty";
        } else if(ded_type=="expiry"){
            col_qty = "expiry_qty";
        } else if(ded_type=="damaged"){
            col_qty = "damaged_qty";
        } else if(ded_type=="margin_diff"){
            col_qty = "mrp_issue_qty";
        }

        $.ajax({
            url: BASE_URL+'index.php?r=pendinggrn%2Fgetskudetails',
            type: 'post',
            data: {
                    psku : psku,
                    grn_id : grn_id,
                    _csrf : csrfToken
                },
            dataType: 'json',
            success: function (data) {
                // console.log(data);
                if(data.length>0){
                    var qty = get_number($('#'+ded_type+'_qty_'+index_val).val(),2);
                    var state = data[0].tax_zone_code;
                    var vat_cst = data[0].vat_cst;
                    var vat_percen = parseFloat(data[0].vat_percen);
                    var cost_excl_tax_per_unit = 0;
                    cost_excl_tax_per_unit = parseFloat(data[0].cost_excl_vat);
                    var tax_per_unit = (cost_excl_tax_per_unit*vat_percen)/100;
                    var total_per_unit = cost_excl_tax_per_unit + tax_per_unit;
                    var cost_excl_tax = qty*cost_excl_tax_per_unit;
                    var tax = qty*tax_per_unit;
                    var total = cost_excl_tax + tax;

                    console.log(state);

                    $('#'+ded_type+'_product_title_'+index_val).val(data[0].product_type);
                    $('#'+ded_type+'_ean_'+index_val).val(data[0].ean);
                    $('#'+ded_type+'_invoice_no_'+index_val).val(data[0].invoice_no);
                    $('#'+ded_type+'_state_'+index_val).val(state);
                    $('#'+ded_type+'_vat_cst_'+index_val).val(vat_cst);
                    $('#'+ded_type+'_vat_percen_'+index_val).val(vat_percen);
                    $('#'+ded_type+'_qty_'+index_val).val(format_money(qty,2));
                    $('#'+ded_type+'_box_price_'+index_val).val(format_money(data[0].box_price,2));
                    $('#'+ded_type+'_cost_excl_tax_per_unit_'+index_val).val(format_money(cost_excl_tax_per_unit,2));
                    $('#'+ded_type+'_tax_per_unit_'+index_val).val(format_money(tax_per_unit,2));
                    $('#'+ded_type+'_total_per_unit_'+index_val).val(format_money(total_per_unit,2));
                    $('#'+ded_type+'_cost_excl_tax_'+index_val).val(format_money(cost_excl_tax,2));
                    $('#'+ded_type+'_tax_'+index_val).val(format_money(tax,2));
                    $('#'+ded_type+'_total_'+index_val).val(format_money(total,2));
                }
                // if(data != null){
                //     $("#code").val(data);
                // } else {
                //     $("#code").val("");
                // }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    }
}

function set_sku_details(elem){
    var elem_id = elem.id;
    if(elem_id.indexOf("_")>0){
        var index = elem_id.substr(elem_id.lastIndexOf("_")+1);
        var ded_type = elem_id.substr(0, elem_id.indexOf("_"));

        // console.log(index);
        // console.log(ded_type);

        var sku_qty = get_number($("#"+ded_type+"_qty_"+index).val(),2);
        var sku_per_unit_cost = get_number($("#"+ded_type+"_cost_excl_tax_per_unit_"+index).val(),2);
        var vat_percen = get_number($("#"+ded_type+"_vat_percen_"+index).val(),2);

        if (sku_qty==0) sku_qty=0;
        if (sku_per_unit_cost==0) sku_per_unit_cost=0;
        if (vat_percen==0) vat_percen=0;

        // console.log(sku_qty);
        // console.log(sku_per_unit_cost);
        // console.log(vat_percen);

        var sku_per_unit_tax = (sku_per_unit_cost*vat_percen)/100;
        var sku_per_unit_total = sku_per_unit_cost + sku_per_unit_tax;

        var sku_cost = sku_qty * sku_per_unit_cost;
        var sku_tax = (sku_cost*vat_percen)/100;
        var sku_total = sku_cost + sku_tax;

        $("#"+ded_type+"_tax_per_unit_"+index).val(format_money(sku_per_unit_tax,2));
        $("#"+ded_type+"_total_per_unit_"+index).val(format_money(sku_per_unit_total,2));
        $("#"+ded_type+"_cost_excl_tax_"+index).val(format_money(sku_cost,2));
        $("#"+ded_type+"_tax_"+index).val(format_money(sku_tax,2));
        $("#"+ded_type+"_total_"+index).val(format_money(sku_total,2));

        // var elem_class_name = elem.className;
        // var index = elem_class_name.substr(elem_class_name.lastIndexOf("_")+1);

        // var sku_total_elem = document.getElementsByClassName(ded_type+"_total_"+index);
        // var inv_total = 0;
        // for(var i = 0; i < sku_total_elem.length; i++) {
        //     inv_total = inv_total + parseFloat(get_number(sku_total_elem[i].value,2));
        // }

        // $("#"+ded_type+"_invoice_total_"+index).html(format_money(inv_total,2));
        // $("#edited_"+ded_type+"_amount_"+(index-1)).val(format_money(inv_total,2));
        // getDifference(document.getElementById("edited_"+ded_type+"_amount_"+(index-1)));

        // console.log(ded_type);

        var total_rows = $('#'+ded_type+'_total_rows').val();
        var grand_total = 0;
        for(var i=0; i<invoices; i++){
            var invoice_no = $('#invoice_no_'+i).val();
            var invoice_total = 0;
            for(var j=0; j<total_rows; j++){
                if(invoice_no==$('#'+ded_type+'_invoice_no_'+j).val()){
                    invoice_total = invoice_total + get_number($('#'+ded_type+'_total_'+j).val(),2);
                }
            }
            grand_total = grand_total + invoice_total;
            $('#edited_'+ded_type+'_amount_'+i).val(format_money(invoice_total,2));
            getDifference(document.getElementById("edited_"+ded_type+"_amount_"+i));
        }
        $('#'+ded_type+'_grand_total').html(format_money(grand_total,2));
    }
    
    // var no_of_invoices = $("#"+ded_type+"_total_no_of_invoice").val();
    // var grand_total = 0;
    // for(var i=1; i<=no_of_invoices; i++){
    //     grand_total = grand_total + parseFloat(get_number($("#"+ded_type+"_invoice_total_"+i).html(),2));
    // }
    // $("#"+ded_type+"_grand_total").html(format_money(grand_total,2));

    // getTotal();
}

function get_acc_details(elem){
    var elem_id = elem.id;
    // console.log(elem_id);
    if(elem_id.indexOf("_")>0){
        var index_val = elem_id.substr(elem_id.lastIndexOf("_")+1);
        var ded_type = elem_id.substr(0, elem_id.indexOf("_"));
        var acc_id = elem.value;
        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: BASE_URL+'index.php?r=pendinggrn%2Fgetaccdetails',
            type: 'post',
            data: {
                    acc_id : acc_id,
                    _csrf : csrfToken
                },
            dataType: 'json',
            success: function (data) {
                // console.log(data);
                if(data.length>0){
                    // console.log('#'+ded_type+'_ledger_name_'+index_val);
                    // console.log(data[0].legal_name);
                    // console.log(data[0].code);
                    
                    $('#'+ded_type+'_ledger_name_'+index_val).val(data[0].legal_name);
                    $('#'+ded_type+'_ledger_code_'+index_val).val(data[0].code);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    }
}

// jQuery(function(){
//     var counter = $('.box').length;
//     $('#repeat-box').click(function(event){
//         event.preventDefault();
//         var newRow = jQuery('<tr id="box_'+counter+'_row">'+
//                                 '<td>'+
//                                     '<select name="box[]" class="form-control box" id="box_'+counter+'">'+
//                                         '<option value="">Select</option>'+
//                                         '<?php if(isset($box)) { for ($k=0; $k < count($box) ; $k++) { ?>'+
//                                                 '<option value="<?php echo $box[$k]->id; ?>"><?php echo $box[$k]->box_name; ?></option>'+
//                                         '<?php }} ?>'+
//                                     '</select>'+
//                                 '</td>'+
//                                 '<td>'+
//                                     '<input type="text" class="form-control format_number qty" name="qty[]" id="qty_'+counter+'" placeholder="Qty" value=""/>'+
//                                 '</td>'+
//                                 '<td style="display:none;">'+
//                                     '<input type="text" class="form-control format_number grams" name="grams[]" id="grams_'+counter+'" placeholder="Grams" value="" readonly />'+
//                                     '<!-- <span id="grams_label_'+counter+'"></span> -->'+
//                                 '</td>'+
//                                 '<td style="display:none;">'+
//                                     '<input type="text" class="form-control format_number rate" name="rate[]" id="rate_'+counter+'" placeholder="Rate" value="" readonly />'+
//                                     '<!-- <span id="rate_label_'+counter+'"></span> -->'+
//                                 '</td>'+
//                                 '<td style="display:none;">'+
//                                     '<input type="text" class="form-control format_number amount" name="amount[]" id="amount_'+counter+'" placeholder="Amount" value="" readonly />'+
//                                     '<!-- <span id="amount_label_'+counter+'"></span> -->'+
//                                 '</td>'+
//                                 '  <td style="text-align:center;     vertical-align: middle;">'+
//                                     '<a id="box_'+counter+'_row_delete" class="delete_row" href="#"><span class="fa trash fa-trash-o"  ></span></a>'+
//                                 '</td>'+
//                             '</tr>');
//         $('#box_details').append(newRow);
//         $('.format_number').keyup(function(){
//             format_number(this);
//         });
//         $(".box").change(function(){
//             get_box_details($(this));
//         });
//         $(".qty").blur(function(){
//             get_amount($(this));
//         });
//         $('.delete_row').click(function(event){
//             delete_row($(this));
//             get_total();
//         });
//         counter++;
//     });
// });