$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$(document).ready(function(){
    getTotal();
    // $("#form_purchase_details").validate();

    if($('#totalamount_acc_id_0').val()=="" || $('#totaldeduction_acc_id_0').val()==""){
        alert('Vendor Account code does not exist. Please Create vendor account.');
        window.location.href = BASE_URL + "index.php?r=pendinggrn%2Findex";
    }

    // $('.edit-text').each(function(){
    //     var id = $(this).attr('id');
    //     getDifference(document.getElementById(id));
    // });
    $('.edit-sku').each(function(){
        var id = $(this).attr('id');
        set_sku_details(document.getElementById(id));
    });

    set_view();
});

function set_view(){
    if($('#action').val()=='view'){
        $('#btn_submit').hide();
        // $("[id$=repeat_sku]").hide();
        // $("[id*=shortage]").hide();
        // $("[id*=expiry]").hide();
        // $("[id*=damaged]").hide();
        // $("[id*=margindiff]").hide();

        $("[id*=repeat]").hide();
        $("[id*=delete]").hide();

        $("input").attr("readonly", true);
        $("select").attr("disabled", true);
        $("textarea").attr("disabled", true);
    }
}

function calcDifference(elem){
    var id = elem.id;
    var invoiceId = id.replace("edited", "invoice");
    var diffId = id.replace("edited", "diff");
    var invoiceAmt = get_number($("#"+invoiceId).val(),2);
    var editedAmt = get_number($("#"+id).val(),2);
    var diffAmt = invoiceAmt-editedAmt;
    $("#"+diffId).val(format_money(diffAmt,2));
}

function getDifference(elem){
    var id = elem.id;

    if(id.indexOf("cost")!=-1){
        var ded_type = id.substr(0, id.indexOf("_"));
        var index = id.substr(id.lastIndexOf("_")+1);
        var vat_percen = $("#vat_percen_"+index).val();
        var cgst_rate = $("#cgst_rate_"+index).val();
        var sgst_rate = $("#sgst_rate_"+index).val();
        var igst_rate = $("#igst_rate_"+index).val();

        var editedAmt = get_number($("#"+id).val(),2);

        // var cgstAmt = Math.round(((editedAmt*cgst_rate)/100)*100)/100;
        // var sgstAmt = Math.round(((editedAmt*sgst_rate)/100)*100)/100;
        // var igstAmt = Math.round(((editedAmt*igst_rate)/100)*100)/100;
        // var taxAmt = (editedAmt*vat_percen)/100;
        var cgstAmt = Math.round(((editedAmt*cgst_rate)/100)*100)/100;
        var sgstAmt = Math.round(((editedAmt*sgst_rate)/100)*100)/100;
        var igstAmt = Math.round(((editedAmt*igst_rate)/100)*100)/100;
        var taxAmt = cgstAmt+sgstAmt+igstAmt;

        var editedTaxId = id.replace("cost", "tax");
        var editedCgstId = id.replace("cost", "cgst");
        var editedSgstId = id.replace("cost", "sgst");
        var editedIgstId = id.replace("cost", "igst");

        $("#"+editedTaxId).val(format_money(taxAmt,2));
        $("#"+editedCgstId).val(format_money(cgstAmt,2));
        $("#"+editedSgstId).val(format_money(sgstAmt,2));
        $("#"+editedIgstId).val(format_money(igstAmt,2));

        // calcDifference($("#"+editedTaxId));
        // calcDifference($("#"+editedCgstId));
        // calcDifference($("#"+editedSgstId));
        // calcDifference($("#"+editedIgstId));

        getDifference(document.getElementById(editedTaxId));
        getDifference(document.getElementById(editedCgstId));
        getDifference(document.getElementById(editedSgstId));
        getDifference(document.getElementById(editedIgstId));
    }

    calcDifference(elem);

    getTotal();
}

function getTotal(){
    // var taxable_amount = get_number($("#taxable_amount").val(),2);
    // var total_tax = get_number($("#total_tax").val(),2);

    var taxable_amount = 0;
    var total_cgst = 0;
    var total_sgst = 0;
    var total_igst = 0;
    var total_tax = 0;
    var other_charges = get_number($("#other_charges").val(),2);

    for(var i=0; i<taxes; i++){
        taxable_amount = taxable_amount + get_number($("#total_cost_"+i).val(),2);
        // total_tax = total_tax + get_number($("#total_tax_"+i).val(),2);
        total_cgst = total_cgst + get_number($("#total_cgst_"+i).val(),2);
        total_sgst = total_sgst + get_number($("#total_sgst_"+i).val(),2);
        total_igst = total_igst + get_number($("#total_igst_"+i).val(),2);
        total_tax = total_cgst + total_sgst + total_igst;
    }

    var total_amount = taxable_amount + total_tax + other_charges;
    $("#total_amount").val(format_money(total_amount,2));

    var shortage_amount = get_number($("#shortage_amount").val(),2);
    var expiry_amount = get_number($("#expiry_amount").val(),2);
    var damaged_amount = get_number($("#damaged_amount").val(),2);
    var margindiff_amount = get_number($("#margindiff_amount").val(),2);
    var total_deduction = shortage_amount + expiry_amount + damaged_amount + margindiff_amount;
    $("#total_deduction").val(format_money(total_deduction,2));

    var total_payable_amount = total_amount - total_deduction;
    $("#total_payable_amount").html(format_money(total_payable_amount,2));

    var invoices = $("#no_of_invoices").val();
    // console.log(invoices);

    for(var i=0; i<invoices; i++){
        // taxable_amount = get_number($("#invoice_taxable_amount_"+i).val(),2);
        // total_tax = get_number($("#invoice_total_tax_"+i).val(),2);
        taxable_amount = 0;
        total_cgst = 0;
        total_sgst = 0;
        total_igst = 0;
        total_tax = 0;
        for(var j=0; j<taxes; j++){
            taxable_amount = taxable_amount + get_number($("#invoice_"+i+"_cost_"+j).val(),2);
            // total_tax = total_tax + get_number($("#invoice_"+i+"_tax_"+j).val(),2);
            total_cgst = total_cgst + get_number($("#invoice_"+i+"_cgst_"+j).val(),2);
            total_sgst = total_sgst + get_number($("#invoice_"+i+"_sgst_"+j).val(),2);
            total_igst = total_igst + get_number($("#invoice_"+i+"_igst_"+j).val(),2);
            total_tax = total_cgst + total_sgst + total_igst;
        }
        other_charges = get_number($("#invoice_other_charges_"+i).val(),2);
        total_amount = taxable_amount + total_tax + other_charges;
        $("#invoice_total_amount_"+i).val(format_money(total_amount,2));

        shortage_amount = get_number($("#invoice_shortage_amount_"+i).val(),2);
        expiry_amount = get_number($("#invoice_expiry_amount_"+i).val(),2);
        damaged_amount = get_number($("#invoice_damaged_amount_"+i).val(),2);
        margindiff_amount = get_number($("#invoice_margindiff_amount_"+i).val(),2);
        total_deduction = shortage_amount + expiry_amount + damaged_amount + margindiff_amount;
        $("#invoice_total_deduction_"+i).val(format_money(total_deduction,2));

        total_payable_amount = total_amount - total_deduction;
        $("#invoice_total_payable_amount_"+i).html(format_money(total_payable_amount,2));

        // taxable_amount = get_number($("#edited_taxable_amount_"+i).val(),2);
        // total_tax = get_number($("#edited_total_tax_"+i).val(),2);
        taxable_amount = 0;
        total_cgst = 0;
        total_sgst = 0;
        total_igst = 0;
        total_tax = 0;
        for(var j=0; j<taxes; j++){
            taxable_amount = taxable_amount + get_number($("#edited_"+i+"_cost_"+j).val(),2);
            // total_tax = total_tax + get_number($("#edited_"+i+"_tax_"+j).val(),2);
            total_cgst = total_cgst + get_number($("#edited_"+i+"_cgst_"+j).val(),2);
            total_sgst = total_sgst + get_number($("#edited_"+i+"_sgst_"+j).val(),2);
            total_igst = total_igst + get_number($("#edited_"+i+"_igst_"+j).val(),2);
            total_tax = total_cgst + total_sgst + total_igst;
        }
        other_charges = get_number($("#edited_other_charges_"+i).val(),2);
        total_amount = taxable_amount + total_tax + other_charges;
        $("#edited_total_amount_"+i).val(format_money(total_amount,2));

        shortage_amount = get_number($("#edited_shortage_amount_"+i).val(),2);
        expiry_amount = get_number($("#edited_expiry_amount_"+i).val(),2);
        damaged_amount = get_number($("#edited_damaged_amount_"+i).val(),2);
        margindiff_amount = get_number($("#edited_margindiff_amount_"+i).val(),2);
        total_deduction = shortage_amount + expiry_amount + damaged_amount + margindiff_amount;
        $("#edited_total_deduction_"+i).val(format_money(total_deduction,2));

        total_payable_amount = total_amount - total_deduction;
        $("#edited_total_payable_amount_"+i).val(format_money(total_payable_amount,2));

        // taxable_amount = get_number($("#diff_taxable_amount_"+i).val(),2);
        // total_tax = get_number($("#diff_total_tax_"+i).val(),2);
        taxable_amount = 0;
        total_cgst = 0;
        total_sgst = 0;
        total_igst = 0;
        total_tax = 0;
        for(var j=0; j<taxes; j++){
            taxable_amount = taxable_amount + get_number($("#diff_"+i+"_cost_"+j).val(),2);
            // total_tax = total_tax + get_number($("#diff_"+i+"_tax_"+j).val(),2);
            total_cgst = total_cgst + get_number($("#diff_"+i+"_cgst_"+j).val(),2);
            total_sgst = total_sgst + get_number($("#diff_"+i+"_sgst_"+j).val(),2);
            total_igst = total_igst + get_number($("#diff_"+i+"_igst_"+j).val(),2);
            total_tax = total_cgst + total_sgst + total_igst;
        }
        other_charges = get_number($("#diff_other_charges_"+i).val(),2);
        total_amount = taxable_amount + total_tax + other_charges;
        $("#diff_total_amount_"+i).val(format_money(total_amount,2));

        shortage_amount = get_number($("#diff_shortage_amount_"+i).val(),2);
        expiry_amount = get_number($("#diff_expiry_amount_"+i).val(),2);
        damaged_amount = get_number($("#diff_damaged_amount_"+i).val(),2);
        margindiff_amount = get_number($("#diff_margindiff_amount_"+i).val(),2);
        total_deduction = shortage_amount + expiry_amount + damaged_amount + margindiff_amount;
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
$("#get_margindiff_qty").click(function(){
    $("#margindiff_modal").modal('show');
});

$("#close_shortage_modal").click(function(){
    if($("#form_purchase_details").valid()){
        check_purchase_details();
    }
    purchase_invalid_handler();
    
    if ($('#shortage_modal').find("input.error, select.error").length>0) {
        return false;
    } else {
        $("#shortage_modal").modal('hide');
    }
});
$("#close_expiry_modal").click(function(){
    if($("#form_purchase_details").valid()){
        check_purchase_details();
    }
    purchase_invalid_handler();
    
    if ($('#expiry_modal').find("input.error, select.error").length>0) {
        return false;
    } else {
        $("#expiry_modal").modal('hide');
    }
});
$("#close_damaged_modal").click(function(){
    if($("#form_purchase_details").valid()){
        check_purchase_details();
    }
    purchase_invalid_handler();
    
    if ($('#damaged_modal').find("input.error, select.error").length>0) {
        return false;
    } else {
        $("#damaged_modal").modal('hide');
    }
});
$("#close_margindiff_modal").click(function(){
    if($("#form_purchase_details").valid()){
        check_purchase_details();
    }
    purchase_invalid_handler();
    
    if ($('#margindiff_modal').find("input.error, select.error").length>0) {
        return false;
    } else {
        $("#margindiff_modal").modal('hide');
    }
});

$("#get_ledger").click(function(){
    // $("#form_purchase_details").validate();

    if (!$("#form_purchase_details").valid()) {
        purchase_invalid_handler();
        return false;
    } else {
        if (check_purchase_details()==false) {
            purchase_invalid_handler();
            return false;
        } else {
            if($('#action').val()=='view'){
                $("select").attr("disabled", false);
            }

            $.ajax({
                url: BASE_URL+'index.php?r=pendinggrn%2Fgetledger',
                type: 'post',
                data: $("#form_purchase_details").serialize(),
                dataType: 'json',
                success: function (data) {
                    var result = '';

                    for(var i=0; i<data.length; i++){
                        result = result + data[i];
                    }

                    $("#ledger_details").html(result);
                    
                    $("#ledger_modal").modal('show');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });

            if($('#action').val()=='view'){
                $("select").attr("disabled", true);
            }
        }
    }
});

function add_sku_details(elem){
    var elem_id = elem.id;
    var ded_type = elem_id.substr(0, elem_id.indexOf("_"));
    var grn_id = $("#grn_id").val();
    var tax_zone_code = $("#vat_cst").val();
    var sr_no = parseInt($('#'+ded_type+'_total_rows').val());
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    $.ajax({
        url: BASE_URL+'index.php?r=pendinggrn%2Fgetnewrow',
        type: 'post',
        data: {
                    ded_type : ded_type,
                    grn_id : grn_id,
                    sr_no : sr_no,
                    tax_zone_code: tax_zone_code,
                    _csrf : csrfToken
                },
        dataType: 'html',
        success: function (data) {
            $('#'+ded_type+'_sku_details tr:last').before(data);

            $('.format_number').keyup(function(){
                format_number(this);
            });
            removeMultiInputNamingRules_form_purchase_details();
            addMultiInputNamingRules_form_purchase_details();

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
        } else if(ded_type=="margindiff"){
            col_qty = "margindiff_qty";
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
                    var qty = get_number($('#'+ded_type+'_qty_'+index_val).val(),4);
                    var state = data[0].tax_zone_code;
                    var invoice_date = data[0].invoice_date;
                    var vat_cst = data[0].vat_cst;
                    var vat_percen = parseFloat(data[0].vat_percen);
                    var cgst_rate = parseFloat(data[0].cgst_rate);
                    var sgst_rate = parseFloat(data[0].sgst_rate);
                    var igst_rate = parseFloat(data[0].igst_rate);

                    var cost_excl_tax_per_unit = 0;
                    cost_excl_tax_per_unit = parseFloat(data[0].cost_excl_vat);
                    // var cgst_per_unit = Math.round(((cost_excl_tax_per_unit*cgst_rate)/100)*100)/100;
                    // var sgst_per_unit = Math.round(((cost_excl_tax_per_unit*sgst_rate)/100)*100)/100;
                    // var igst_per_unit = Math.round(((cost_excl_tax_per_unit*igst_rate)/100)*100)/100;
                    // var tax_per_unit = (cost_excl_tax_per_unit*vat_percen)/100;
                    var cgst_per_unit = (cost_excl_tax_per_unit*cgst_rate)/100;
                    var sgst_per_unit = (cost_excl_tax_per_unit*sgst_rate)/100;
                    var igst_per_unit = (cost_excl_tax_per_unit*igst_rate)/100;
                    var tax_per_unit = cgst_per_unit+sgst_per_unit+igst_per_unit;
                    var total_per_unit = parseFloat(data[0].cost_incl_vat_cst);

                    var cost_excl_tax = Math.round(qty*cost_excl_tax_per_unit*100)/100;
                    var cgst = Math.round(((cost_excl_tax*cgst_rate)/100)*100)/100;
                    var sgst = Math.round(((cost_excl_tax*sgst_rate)/100)*100)/100;
                    var igst = Math.round(((cost_excl_tax*igst_rate)/100)*100)/100;
                    // var tax = qty*tax_per_unit;
                    var tax = cgst+sgst+igst;
                    var total = cost_excl_tax + tax;

                    // var po_mrp = parseFloat(data[0].po_mrp);
                    // var po_cost_excl_tax = parseFloat(data[0].po_unit_rate_excl_tax);
                    // // var po_cgst = Math.round(((po_cost_excl_tax*cgst_rate)/100)*100)/100;
                    // // var po_sgst = Math.round(((po_cost_excl_tax*sgst_rate)/100)*100)/100;
                    // // var po_igst = Math.round(((po_cost_excl_tax*igst_rate)/100)*100)/100;
                    // // var tax_per_unit = (po_cost_excl_tax*vat_percen)/100;
                    // var po_cgst = (po_cost_excl_tax*cgst_rate)/100;
                    // var po_sgst = (po_cost_excl_tax*sgst_rate)/100;
                    // var po_igst = (po_cost_excl_tax*igst_rate)/100;
                    // var po_tax = po_cgst+po_sgst+po_igst;
                    // var po_total = po_cost_excl_tax + po_tax;

                    
                    var po_mrp = parseFloat(data[0].po_mrp);
                    var po_total = parseFloat(data[0].po_unit_rate_incl_tax);
                    var po_cost_excl_tax = po_total/(1+(vat_percen/100));
                    // var po_tax = (po_cost_excl_tax*vat_percen)/100;
                    var po_cgst = (po_cost_excl_tax*cgst_rate)/100;
                    var po_sgst = (po_cost_excl_tax*sgst_rate)/100;
                    var po_igst = (po_cost_excl_tax*igst_rate)/100;
                    var po_tax = po_cgst+po_sgst+po_igst;

                    $('#'+ded_type+'_product_title_'+index_val).val(data[0].product_title);
                    $('#'+ded_type+'_ean_'+index_val).val(data[0].ean);
                    $('#'+ded_type+'_hsn_code_'+index_val).val(data[0].hsn_code);
                    $('#'+ded_type+'_invoice_no_'+index_val).val(data[0].invoice_no);
                    $('#'+ded_type+'_state_'+index_val).val(state);
                    $('#'+ded_type+'_invoice_date_'+index_val).html(invoice_date);
                    $('#'+ded_type+'_vat_cst_'+index_val).val(vat_cst);
                    $('#'+ded_type+'_cgst_rate_'+index_val).val(cgst_rate);
                    $('#'+ded_type+'_sgst_rate_'+index_val).val(sgst_rate);
                    $('#'+ded_type+'_igst_rate_'+index_val).val(igst_rate);
                    $('#'+ded_type+'_vat_percen_'+index_val).val(vat_percen);
                    $('#'+ded_type+'_qty_'+index_val).val(format_money(qty,4));
                    $('#'+ded_type+'_box_price_'+index_val).val(format_money(data[0].box_price,4));
                    $('#'+ded_type+'_cost_excl_tax_per_unit_'+index_val).val(format_money(cost_excl_tax_per_unit,4));
                    $('#'+ded_type+'_cgst_per_unit_'+index_val).val(format_money(cgst_per_unit,4));
                    $('#'+ded_type+'_sgst_per_unit_'+index_val).val(format_money(sgst_per_unit,4));
                    $('#'+ded_type+'_igst_per_unit_'+index_val).val(format_money(igst_per_unit,4));
                    $('#'+ded_type+'_tax_per_unit_'+index_val).val(format_money(tax_per_unit,4));
                    $('#'+ded_type+'_total_per_unit_'+index_val).val(format_money(total_per_unit,4));
                    $('#'+ded_type+'_cost_excl_tax_'+index_val).val(format_money(cost_excl_tax,2));
                    $('#'+ded_type+'_cgst_'+index_val).val(format_money(cgst,2));
                    $('#'+ded_type+'_sgst_'+index_val).val(format_money(sgst,2));
                    $('#'+ded_type+'_igst_'+index_val).val(format_money(igst,2));
                    $('#'+ded_type+'_tax_'+index_val).val(format_money(tax,2));
                    $('#'+ded_type+'_total_'+index_val).val(format_money(total,2));

                    $('#'+ded_type+'_po_mrp_'+index_val).val(format_money(po_mrp,4));
                    $('#'+ded_type+'_po_cost_excl_tax_'+index_val).val(format_money(po_cost_excl_tax,4));
                    $('#'+ded_type+'_po_cgst_'+index_val).val(format_money(po_cgst,4));
                    $('#'+ded_type+'_po_sgst_'+index_val).val(format_money(po_sgst,4));
                    $('#'+ded_type+'_po_igst_'+index_val).val(format_money(po_igst,4));
                    $('#'+ded_type+'_po_tax_'+index_val).val(format_money(po_tax,4));
                    $('#'+ded_type+'_po_total_'+index_val).val(format_money(po_total,4));

                    set_sku_details(document.getElementById(ded_type+'_qty_'+index_val));
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

        var sku_qty = get_number($("#"+ded_type+"_qty_"+index).val(),4);
        var sku_per_unit_cost = get_number($("#"+ded_type+"_cost_excl_tax_per_unit_"+index).val(),4);
        var cgst_rate = get_number($("#"+ded_type+"_cgst_rate_"+index).val(),4);
        var sgst_rate = get_number($("#"+ded_type+"_sgst_rate_"+index).val(),4);
        var igst_rate = get_number($("#"+ded_type+"_igst_rate_"+index).val(),4);
        var vat_percen = get_number($("#"+ded_type+"_vat_percen_"+index).val(),4);
        var sku_per_unit_total = get_number($("#"+ded_type+"_total_per_unit_"+index).val(),4);

        // var po_cost_excl_tax = get_number($("#"+ded_type+"_po_cost_excl_tax_"+index).val(),4);
        var po_total = get_number($("#"+ded_type+"_po_total_"+index).val(),4);

        if (sku_qty==0) sku_qty=0;
        if (sku_per_unit_cost==0) sku_per_unit_cost=0;
        if (vat_percen==0) vat_percen=0;

        // var sku_per_unit_cgst = Math.round(((sku_per_unit_cost*cgst_rate)/100)*100)/100;
        // var sku_per_unit_sgst = Math.round(((sku_per_unit_cost*sgst_rate)/100)*100)/100;
        // var sku_per_unit_igst = Math.round(((sku_per_unit_cost*igst_rate)/100)*100)/100;
        // var sku_per_unit_tax = (sku_per_unit_cost*vat_percen)/100;
        var sku_per_unit_cgst = (sku_per_unit_cost*cgst_rate)/100;
        var sku_per_unit_sgst = (sku_per_unit_cost*sgst_rate)/100;
        var sku_per_unit_igst = (sku_per_unit_cost*igst_rate)/100;
        var sku_per_unit_tax = sku_per_unit_cgst+sku_per_unit_sgst+sku_per_unit_igst;
        // var sku_per_unit_total = sku_per_unit_cost + sku_per_unit_tax;

        // var sku_cost = sku_qty * sku_per_unit_cost;
        // // var sku_tax = sku_qty * sku_per_unit_tax;
        // var sku_cgst = sku_qty * sku_per_unit_cgst;
        // var sku_sgst = sku_qty * sku_per_unit_sgst;
        // var sku_igst = sku_qty * sku_per_unit_igst;
        // var sku_tax = sku_cgst+sku_sgst+sku_igst;
        // var sku_total = sku_cost + sku_tax;

        var sku_cost = Math.round(sku_qty*sku_per_unit_cost*100)/100;
        // var sku_tax = sku_qty * sku_per_unit_tax;
        var sku_cgst = Math.round(((sku_cost*cgst_rate)/100)*100)/100;
        var sku_sgst = Math.round(((sku_cost*sgst_rate)/100)*100)/100;
        var sku_igst = Math.round(((sku_cost*igst_rate)/100)*100)/100;
        var sku_tax = sku_cgst+sku_sgst+sku_igst;
        var sku_total = sku_cost + sku_tax;

        $("#"+ded_type+"_tax_per_unit_"+index).val(format_money(sku_per_unit_tax,4));
        $("#"+ded_type+"_total_per_unit_"+index).val(format_money(sku_per_unit_total,4));
        $("#"+ded_type+"_cost_excl_tax_"+index).val(format_money(sku_cost,2));
        $("#"+ded_type+"_cgst_"+index).val(format_money(sku_cgst,2));
        $("#"+ded_type+"_sgst_"+index).val(format_money(sku_sgst,2));
        $("#"+ded_type+"_igst_"+index).val(format_money(sku_igst,2));
        $("#"+ded_type+"_tax_"+index).val(format_money(sku_tax,2));
        $("#"+ded_type+"_total_"+index).val(format_money(sku_total,2));

        // console.log(sku_cost-po_cost_excl_tax);
        // console.log(Math.round(sku_cost-po_cost_excl_tax,4));

        // $("#"+ded_type+"_diff_cost_excl_tax_"+index).val(format_money(Math.round((sku_cost-po_cost_excl_tax)*100)/100,4));
        // $("#"+ded_type+"_diff_cgst_"+index).val(format_money(Math.round((sku_cgst-po_cgst)*100)/100,4));
        // $("#"+ded_type+"_diff_sgst_"+index).val(format_money(Math.round((sku_sgst-po_sgst)*100)/100,4));
        // $("#"+ded_type+"_diff_igst_"+index).val(format_money(Math.round((sku_igst-po_igst)*100)/100,4));
        // $("#"+ded_type+"_diff_tax_"+index).val(format_money(Math.round((sku_tax-po_tax)*100)/100,4));
        // $("#"+ded_type+"_diff_total_"+index).val(format_money(Math.round((sku_total-po_total)*100)/100,4));

        // // var po_cgst = Math.round(((po_cost_excl_tax*cgst_rate)/100)*100)/100;
        // // var po_sgst = Math.round(((po_cost_excl_tax*sgst_rate)/100)*100)/100;
        // // var po_igst = Math.round(((po_cost_excl_tax*igst_rate)/100)*100)/100;
        // // var po_tax = (po_cost_excl_tax*vat_percen)/100;
        // var po_cgst = (po_cost_excl_tax*cgst_rate)/100;
        // var po_sgst = (po_cost_excl_tax*sgst_rate)/100;
        // var po_igst = (po_cost_excl_tax*igst_rate)/100;
        // var po_tax = po_cgst+po_sgst+po_igst;
        // var po_total = po_cost_excl_tax + po_tax;

        var po_cost_excl_tax = po_total/(1+(vat_percen/100));
        // var po_tax = (po_cost_excl_tax*vat_percen)/100;
        var po_cgst = (po_cost_excl_tax*cgst_rate)/100;
        var po_sgst = (po_cost_excl_tax*sgst_rate)/100;
        var po_igst = (po_cost_excl_tax*igst_rate)/100;
        var po_tax = po_cgst+po_sgst+po_igst;

        $("#"+ded_type+"_po_cost_excl_tax_"+index).val(format_money(po_cost_excl_tax,4));
        $("#"+ded_type+"_po_cgst_"+index).val(format_money(po_cgst,4));
        $("#"+ded_type+"_po_sgst_"+index).val(format_money(po_sgst,4));
        $("#"+ded_type+"_po_igst_"+index).val(format_money(po_igst,4));
        $("#"+ded_type+"_po_tax_"+index).val(format_money(po_tax,4));
        $("#"+ded_type+"_po_total_"+index).val(format_money(po_total,4));

        var po_mrp = get_number($("#"+ded_type+"_po_mrp_"+index).val(),4);
        var box_price = get_number($("#"+ded_type+"_box_price_"+index).val(),4);

        var margin_from_po = 0;
        var margin_from_scan = 0;

        if(po_mrp!=0){
            margin_from_po = parseInt(((po_mrp-po_total)/po_mrp*100)*100)/100;
        }
        if(box_price!=0){
            margin_from_scan = parseInt(((box_price-sku_per_unit_total)/box_price*100)*100)/100;
        }

        // console.log(po_mrp);
        // console.log(po_cost_excl_tax);
        // console.log(box_price);
        // console.log(sku_per_unit_cost);
        // console.log(sku_qty);
        // console.log(vat_percen);
        // console.log(margin_from_po);
        // console.log(margin_from_scan);

        var diff_cost_excl_tax = 0;
        if(box_price==0){
            diff_cost_excl_tax = 0;
        } else if(sku_qty==0){
            diff_cost_excl_tax = 0;
        // } else if(margin_from_po==0){
        //     diff_cost_excl_tax = 0;
        // } else if(margin_from_scan==0){
        //     diff_cost_excl_tax = 0;
        } else {
            diff_cost_excl_tax = Math.round((((margin_from_po-margin_from_scan)/100*box_price*sku_qty)/(1+(vat_percen/100)))*100)/100;
        }
        
        // var diff_tax = (diff_cost_excl_tax*vat_percen)/100;
        var diff_cgst = Math.round(((diff_cost_excl_tax*cgst_rate)/100)*100)/100;
        var diff_sgst = Math.round(((diff_cost_excl_tax*sgst_rate)/100)*100)/100;
        var diff_igst = Math.round(((diff_cost_excl_tax*igst_rate)/100)*100)/100;
        var diff_tax = diff_cgst + diff_sgst + diff_igst;
        var diff_total = diff_cost_excl_tax + diff_tax;

        // $("#"+ded_type+"_diff_cost_excl_tax_"+index).val(format_money(Math.round((diff_cost_excl_tax)*100)/100,4));
        // $("#"+ded_type+"_diff_cgst_"+index).val(format_money(Math.round((diff_cgst)*100)/100,4));
        // $("#"+ded_type+"_diff_sgst_"+index).val(format_money(Math.round((diff_sgst)*100)/100,4));
        // $("#"+ded_type+"_diff_igst_"+index).val(format_money(Math.round((diff_igst)*100)/100,4));
        // $("#"+ded_type+"_diff_tax_"+index).val(format_money(Math.round((diff_tax)*100)/100,4));
        // $("#"+ded_type+"_diff_total_"+index).val(format_money(Math.round((diff_total)*100)/100,4));

        $("#"+ded_type+"_diff_cost_excl_tax_"+index).val(format_money(diff_cost_excl_tax,2));
        $("#"+ded_type+"_diff_cgst_"+index).val(format_money(diff_cgst,2));
        $("#"+ded_type+"_diff_sgst_"+index).val(format_money(diff_sgst,2));
        $("#"+ded_type+"_diff_igst_"+index).val(format_money(diff_igst,2));
        $("#"+ded_type+"_diff_tax_"+index).val(format_money(diff_tax,2));
        $("#"+ded_type+"_diff_total_"+index).val(format_money(diff_total,2));

        setDeductionTotal(ded_type);

        // var total_rows = $('#'+ded_type+'_total_rows').val();
        // var grand_total = 0;
        // var invoices = $("#no_of_invoices").val();
        // for(var i=0; i<invoices; i++){
        //     var invoice_no = $('#invoice_no_'+i).val();
        //     var invoice_total = 0;
        //     for(var j=0; j<total_rows; j++){
        //         if(invoice_no==$('#'+ded_type+'_invoice_no_'+j).val()){
        //             invoice_total = invoice_total + get_number($('#'+ded_type+'_total_'+j).val(),4);
        //         }
        //     }
        //     grand_total = grand_total + invoice_total;
        //     $('#edited_'+ded_type+'_amount_'+i).val(format_money(invoice_total,4));
        //     getDifference(document.getElementById("edited_"+ded_type+"_amount_"+i));
        // }
        // $('#'+ded_type+'_grand_total').html(format_money(grand_total,4));
    }
}

function setDeductionTotal(ded_type){
    var total_rows = $('#'+ded_type+'_total_rows').val();
    var grand_total = 0;
    var po_grand_total = 0;
    var diff_grand_total = 0;
    var invoices = $("#no_of_invoices").val();
    var invoice_no = '';
    var invoice_total = 0;
    var po_invoice_total = 0;
    var diff_invoice_total = 0;

    for(var i=0; i<invoices; i++){
        invoice_no = $('#invoice_no_'+i).val();
        invoice_total = 0;
        po_invoice_total = 0;
        diff_invoice_total = 0;

        for(var j=0; j<total_rows; j++){
            if(invoice_no==$('#'+ded_type+'_invoice_no_'+j).val()){
                invoice_total = invoice_total + get_number($('#'+ded_type+'_total_'+j).val(),2);
                po_invoice_total = po_invoice_total + get_number($('#'+ded_type+'_po_total_'+j).val(),2);
                diff_invoice_total = diff_invoice_total + get_number($('#'+ded_type+'_diff_total_'+j).val(),2);
            }
        }

        grand_total = grand_total + invoice_total;
        po_grand_total = po_grand_total + po_invoice_total;
        diff_grand_total = diff_grand_total + diff_invoice_total;

        if(ded_type=="margindiff"){
            $('#edited_'+ded_type+'_amount_'+i).val(format_money(diff_invoice_total,2));
        } else {
            $('#edited_'+ded_type+'_amount_'+i).val(format_money(invoice_total,2));
        }
        
        getDifference(document.getElementById("edited_"+ded_type+"_amount_"+i));
    }

    $('#'+ded_type+'_grand_total').html(format_money(grand_total,2));
    $('#'+ded_type+'_po_grand_total').html(format_money(po_grand_total,2));
    $('#'+ded_type+'_diff_grand_total').html(format_money(diff_grand_total,2));
}

function delete_row(elem){
    var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);
    var ded_type = id.substr(0,id.indexOf('_'));

    $('#'+ded_type+'_row_'+index).remove();
    // console.log(ded_type);
    setDeductionTotal(ded_type);
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