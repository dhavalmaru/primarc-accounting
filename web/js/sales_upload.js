$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$(document).ready(function(){
    // getTotal();
    // // $("#form_sale_upload_details").validate();

    // if($('#totalamount_acc_id_0').val()=="" || $('#totaldeduction_acc_id_0').val()==""){
    //     alert('Vendor Account code does not exist. Please Create vendor account.');
    //     window.location.href = BASE_URL + "index.php?r=salesupload%2Findex";
    // }

    $('.select2').select2();
  
    addMultiInputNamingRules('#form_sale_upload_details', 'select[name="acc_id[]"]', { required: true });

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

$("#get_ledger").click(function(){
    // $("#form_sale_upload_details").validate();

    if (!$("#form_sale_upload_details").valid()) {
        return false;
    } else {
        if($('#action').val()=='view'){
            $("select").attr("disabled", false);
        }

        $.ajax({
            url: BASE_URL+'index.php?r=salesupload%2Fgetledger',
            type: 'post',
            data: $("#form_sale_upload_details").serialize(),
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
});

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
        var index1 = elem_id.substr(elem_id.lastIndexOf("_")+1);
        var id_substr = elem_id.substr(0, elem_id.lastIndexOf("_"));
        var index2 = id_substr.substr(id_substr.lastIndexOf("_")+1);
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
                    
                    $('#ledger_name_'+index1+'_'+index2).val(data[0].legal_name);
                    $('#ledger_code_'+index1+'_'+index2).val(data[0].code);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    }
}

function freeze_file(elem){
    var elem_id = elem.id;

    if(elem_id.indexOf("_")>0){
        var index = elem_id.substr(elem_id.lastIndexOf("_")+1);
        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: BASE_URL+'index.php?r=salesupload%2Ffreezefile',
            type: 'post',
            data: {
                    file_id : index,
                    _csrf : csrfToken
                },
            dataType: 'html',
            success: function (data) {
                location.reload();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    }
}

function check_hsn(elem){
    var elem_id = elem.id;

    if(elem_id.indexOf("_")>0){
        var index = elem_id.substr(elem_id.lastIndexOf("_")+1);
        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: BASE_URL+'index.php?r=salesupload%2Fcheckhsn',
            type: 'post',
            data: {
                    file_id : index,
                    _csrf : csrfToken
                },
            dataType: 'html',
            success: function (data) {
                location.reload();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    }
}