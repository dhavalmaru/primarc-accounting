// ----------------- COMMON FUNCTIONS -------------------------------------
$.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Letters only please");

$.validator.addMethod("numbersonly", function(value, element) {
    return this.optional(element) || /^[0-9]+$/i.test(value);
}, "Numbers only please");

$.validator.addMethod("numbersandcommaonly", function(value, element) {
    return this.optional(element) || /^[0-9]|^,+$/i.test(value);
}, "Numbers only please");

$.validator.addMethod("checkemail", function(value, element) {
    return this.optional(element) || (/^[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,4}$/i.test(value) && /^(?=.{1,64}@.{4,64}$)(?=.{6,100}$).*/i.test(value));
}, "Please enter valid email address");

function addMultiInputNamingRules(form, field, rules, type){
    // alert(field);
    $(form).find(field).each(function(index){
        if (type=="Document") {
            var id = $(this).attr('id');
            var index = id.substr(id.lastIndexOf('_')+1);
            if($('#d_m_status_'+index).val()=="Yes"){
                $(this).attr('alt', $(this).attr('name'));
                $(this).attr('name', $(this).attr('name')+'-'+index);
                $(this).rules('add', rules);
            }
        } else {
            $(this).attr('alt', $(this).attr('name'));
            $(this).attr('name', $(this).attr('name')+'-'+index);
            $(this).rules('add', rules);
        }
    });
}

function removeMultiInputNamingRules(form, field){    
    $(form).find(field).each(function(index){
        $(this).attr('name', $(this).attr('alt'));
        $(this).removeAttr('alt');
    });
}

// function getMStatus(element){
//     var id = element.id;
//     var doc_name = element.value;
//     var index = id.substr(id.lastIndexOf('_')+1);

//     var doc_type = $('#doc_type_'+index).val();

//     $.ajax({
//             url: BASE_URL+'index.php/contacts/get_m_status',
//             data: 'doc_name='+doc_name+'&doc_type='+doc_type,
//             type: "POST",
//             dataType: 'html',
//             global: false,
//             async: false,
//             success: function (data) {
//                 $('#d_m_status_'+index).val($.trim(data));
//             },
//             error: function (xhr, ajaxOptions, thrownError) {
//                 $('#d_m_status_'+index).val("");
//             }
//         });
// }

$('.save-form').click(function(){ 
    $("#submitVal").val('1');
});
$('.submit-form').click(function(){ 
    $("#submitVal").val('0');
});




// ----------------- ACCOUNT MASTER FORM VALIDATION -------------------------------------
$("#account_master").validate({
    rules: {
        type: {
            required: true
        },
        vendor_id: {
            required: true
        },
        legal_name: {
            required: true,
            check_legal_name_availablity: true
        },
        code: {
            required: true
        },
        vendor_code: {
            required: true
        },
        account_type: {
            required: true
        },
        account_holder_name: {
            required: function(element) {
                        if($("#type").val()=="Vendor Goods" || $("#type").val()=="Bank Account" || $("#type").val()=="Vendor Expenses" || $("#type").val()=="Employee"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        acc_no: {
            required: function(element) {
                        if($("#type").val()=="Vendor Goods" || $("#type").val()=="Bank Account" || $("#type").val()=="Vendor Expenses" || $("#type").val()=="Employee"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        bank_name: {
            required: function(element) {
                        if($("#type").val()=="Vendor Goods" || $("#type").val()=="Bank Account" || $("#type").val()=="Vendor Expenses" || $("#type").val()=="Employee"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        ifsc_code: {
            required: function(element) {
                        if($("#type").val()=="Vendor Goods" || $("#type").val()=="Bank Account" || $("#type").val()=="Vendor Expenses" || $("#type").val()=="Employee"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        expense_type: {
            required: function(element) {
                        if($("#type").val()=="Vendor Expenses" || $("#type").val()=="Employee"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        location: {
            required: function(element) {
                        if($("#type").val()=="Vendor Expenses" || $("#type").val()=="Employee"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        address: {
            required: function(element) {
                        if($("#type").val()=="Employee"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        pan_no: {
            required: function(element) {
                        if($("#type").val()=="Vendor Expenses" || $("#type").val()=="Employee"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        aadhar_card_no: {
            required: function(element) {
                        if($("#type").val()=="Employee"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        agreement_details: {
            required: function(element) {
                        if($("#type").val()=="Vendor Expenses"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        ac_category_1: {
            required: true
        },
        approver_id: {
            required: true
        },
        // address_doc_file: {
        //     required: function(element) {
        //                 if($("#type").val()=="Employee" && $("#address_doc_path").val()==""){
        //                     return true;
        //                 } else {
        //                     return false;
        //                 }
        //             }
        // },
        // pan_no_doc_file: {
        //     required: function(element) {
        //                 if($("#pan_no_doc_path").val()==""){
        //                     return true;
        //                 } else {
        //                     return false;
        //                 }
        //             }
        // },
        // aadhar_card_no_doc_file: {
        //     required: function(element) {
        //                 if($("#aadhar_card_no_doc_path").val()==""){
        //                     return true;
        //                 } else {
        //                     return false;
        //                 }
        //             }
        // },
        // service_tax_no_doc_file: {
        //     required: function(element) {
        //                 if($("#service_tax_no_doc_path").val()==""){
        //                     return true;
        //                 } else {
        //                     return false;
        //                 }
        //             }
        // },
        // vat_no_doc_file: {
        //     required: function(element) {
        //                 if($("#vat_no_doc_path").val()==""){
        //                     return true;
        //                 } else {
        //                     return false;
        //                 }
        //             }
        // },
        // pf_esic_no_doc_file: {
        //     required: function(element) {
        //                 if($("#pf_esic_no_doc_path").val()==""){
        //                     return true;
        //                 } else {
        //                     return false;
        //                 }
        //             }
        // },
        // agreement_details_doc_file: {
        //     required: function(element) {
        //                 if($("#agreement_details_doc_path").val()==""){
        //                     return true;
        //                 } else {
        //                     return false;
        //                 }
        //             }
        // },
        // acc_no_doc_file: {
        //     required: function(element) {
        //                 if($("#acc_no_path").val()==""){
        //                     return true;
        //                 } else {
        //                     return false;
        //                 }
        //             }
        // },
        // other_doc_file: {
        //     required: function(element) {
        //                 if($("#other_doc_path").val()==""){
        //                     return true;
        //                 } else {
        //                     return false;
        //                 }
        //             }
        // }
    },

    ignore: ":not(:visible)",

    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    }
});

$('#account_master').submit(function() {
    removeMultiInputNamingRules('#account_master', 'select[alt="bus_category[]"]');

    addMultiInputNamingRules('#account_master', 'select[name="bus_category[]"]', { required: true });

    if (!$("#account_master").valid()) {
        return false;
    } else {
        removeMultiInputNamingRules('#account_master', 'select[alt="bus_category[]"]');

        return true;
    }
});

$.validator.addMethod("check_legal_name_availablity", function (value, element) {
    var validator = $("#account_master").validate();
    var result = 1;

    $.ajax({
        url: BASE_URL+'index.php?r=accountmaster%2Fchecklegalnameavailablity',
        type: 'post',
        data: $("#account_master").serialize(),
        dataType: 'html',
        global: false,
        async: false,
        success: function (data) {
            result = data;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });

    if (result==1) {
        return false;
    } else {
        return true;
    }
}, 'Legal Name already in use.');





// ----------------- ACCOUNT CATEGORY MASTER FORM VALIDATION -------------------------------------
$("#acc_category_master").validate({
    rules: {
        
    },

    ignore: false,

    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    }
});

$('#acc_category_master').submit(function() {
    removeMultiInputNamingRules('#acc_category_master', 'input[alt="category_1[]"]');
    // removeMultiInputNamingRules('#acc_category_master', 'input[alt="category_2[]"]');
    // removeMultiInputNamingRules('#acc_category_master', 'input[alt="category_3[]"]');

    addMultiInputNamingRules('#acc_category_master', 'input[name="category_1[]"]', { required: true });
    // addMultiInputNamingRules('#acc_category_master', 'input[name="category_2[]"]', { required: true });
    // addMultiInputNamingRules('#acc_category_master', 'input[name="category_3[]"]', { required: true });

    if (!$("#acc_category_master").valid()) {
        return false;
    } else {
        removeMultiInputNamingRules('#acc_category_master', 'input[alt="category_1[]"]');
        // removeMultiInputNamingRules('#acc_category_master', 'input[alt="category_2[]"]');
        // removeMultiInputNamingRules('#acc_category_master', 'input[alt="category_3[]"]');

        return true;
    }
});




// ----------------- DEBIT CREDIT NOTE FORM VALIDATION -------------------------------------
$("#debit_credit_note").validate({
    rules: {
        
    },

    ignore: false,

    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    }
});

$('#debit_credit_note').submit(function() {
    removeMultiInputNamingRules('#debit_credit_note', 'select[alt="transaction[]"]');
    removeMultiInputNamingRules('#debit_credit_note', 'input[alt="due_date[]"]');
    removeMultiInputNamingRules('#debit_credit_note', 'input[alt="amount[]"]');

    addMultiInputNamingRules('#debit_credit_note', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#debit_credit_note', 'input[name="due_date[]"]', { required: true });
    addMultiInputNamingRules('#debit_credit_note', 'input[name="amount[]"]', { required: true, numbersandcommaonly: true });

    if (!$("#debit_credit_note").valid()) {
        return false;
    } else {
        removeMultiInputNamingRules('#debit_credit_note', 'select[alt="transaction[]"]');
        removeMultiInputNamingRules('#debit_credit_note', 'input[alt="due_date[]"]');
        removeMultiInputNamingRules('#debit_credit_note', 'input[alt="amount[]"]');
        
        return true;
    }
});




// ----------------- BANK MASTER FORM VALIDATION -------------------------------------
$("#bank_master").validate({
    rules: {
        bank_name: {
            required: true
        },
        branch: {
            required: true
        },
        acc_type: {
            required: true
        },
        acc_no: {
            required: true
        },
        ifsc_code: {
            required: true
        },
        opening_balance: {
            required: true,
            numbersandcommaonly: true
        },
        balance_ref_date: {
            required: true
        }
    },

    ignore: false,

    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    }
});

$('#bank_master').submit(function() {
    if (!$("#bank_master").valid()) {
        return false;
    } else {
        return true;
    }
});




// ----------------- PURCHASE DETAILS FORM VALIDATION -------------------------------------
$(function() {
    $("#form_purchase_details").validate({
        rules: {
            other_charges_acc_id: {
                required: function(){
                    var blFlag = false;
                    $('.edited_other_charges').each(function() {
                        if($(this).val()!=""){
                            if(parseFloat($(this).val())>0){
                                blFlag = true;
                            }
                        }
                    });

                    return blFlag;
                }
            }
        },

        ignore: ":not(:visible)",

        errorPlacement: function (error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error);
            } else {
                error.insertAfter(element);
            }
        },

        invalidHandler: function(e,validator) {
            purchase_invalid_handler();
        }
    });

    addMultiInputNamingRules_form_purchase_details();
})

function addMultiInputNamingRules_form_purchase_details(){
    addMultiInputNamingRules('#form_purchase_details', 'select[name="invoice_cost_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_purchase_details', 'select[name="invoice_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="invoice_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="invoice_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="invoice_igst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="shortage_psku[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="shortage_invoice_no[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="shortage_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="expiry_psku[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="expiry_invoice_no[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="expiry_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="damaged_psku[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="damaged_invoice_no[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="damaged_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="margindiff_psku[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="margindiff_invoice_no[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="margindiff_cost_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_purchase_details', 'select[name="shortage_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="shortage_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="shortage_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="shortage_igst_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_purchase_details', 'select[name="expiry_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="expiry_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="expiry_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="expiry_igst_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_purchase_details', 'select[name="damaged_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="damaged_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="damaged_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="damaged_igst_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_purchase_details', 'select[name="margindiff_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="margindiff_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="margindiff_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="margindiff_igst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="shortage_qty[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="expiry_qty[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="damaged_qty[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="margindiff_qty[]"]', { required: true });
}
function removeMultiInputNamingRules_form_purchase_details(){
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="invoice_cost_acc_id[]"]');
    // removeMultiInputNamingRules('#form_purchase_details', 'select[alt="invoice_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="invoice_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="invoice_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="invoice_igst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_psku[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_invoice_no[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_psku[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_invoice_no[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_psku[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_invoice_no[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margindiff_psku[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margindiff_invoice_no[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margindiff_cost_acc_id[]"]');
    // removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_igst_acc_id[]"]');
    // removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_igst_acc_id[]"]');
    // removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_igst_acc_id[]"]');
    // removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margindiff_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margindiff_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margindiff_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margindiff_igst_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="shortage_qty[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="expiry_qty[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="damaged_qty[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="margindiff_qty[]"]');
}

$('#form_purchase_details').submit(function() {
    removeMultiInputNamingRules_form_purchase_details();
    addMultiInputNamingRules_form_purchase_details();

    if (!$("#form_purchase_details").valid()) {
        purchase_invalid_handler();
        return false;
    } else {
        if (check_purchase_details()==false) {
            purchase_invalid_handler();
            return false;
        } else {
            removeMultiInputNamingRules_form_purchase_details();
            
            return true;
        }
    }
});
function check_purchase_details() {
    var validator = $("#form_purchase_details").validate();
    var valid = true;
    var purchase_acc_id = [];
    var tax_acc_id = [];
    var cgst_acc_id = [];
    var sgst_acc_id = [];
    var igst_acc_id = [];
    var errors = {};

    $("#form_purchase_details").find('select[alt="invoice_cost_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            purchase_acc_id.push($(this).val());
        }
    });
    // $("#form_purchase_details").find('select[alt="invoice_tax_acc_id[]"]').each(function(index){
    //     if($(this).val()!=null && $(this).val()!=''){
    //         tax_acc_id.push($(this).val());
    //     }
    // });
    $("#form_purchase_details").find('select[alt="invoice_cgst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            cgst_acc_id.push($(this).val());
        }
    });
    $("#form_purchase_details").find('select[alt="invoice_sgst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            sgst_acc_id.push($(this).val());
        }
    });
    $("#form_purchase_details").find('select[alt="invoice_igst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            igst_acc_id.push($(this).val());
        }
    });

    $("#form_purchase_details").find('select[alt="shortage_cost_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), purchase_acc_id)==-1){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please select account id as per purchase.";
            // validator.showErrors(errors);
            valid = false;
        }
    });
    // $("#form_purchase_details").find('select[alt="shortage_tax_acc_id[]"]').each(function(index){
    //     if($.inArray($(this).val(), tax_acc_id)==-1){
    //         // var errors = {};
    //         var name = $(this).attr('name');
    //         errors[name] = "Please select account id as per purchase.";
    //         // validator.showErrors(errors);
    //         valid = false;
    //     }
    // });
    $("#form_purchase_details").find('select[alt="shortage_cgst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), cgst_acc_id)==-1){
            if($('#vat_cst').val()=='INTRA'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('select[alt="shortage_sgst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), sgst_acc_id)==-1){
            if($('#vat_cst').val()=='INTRA'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('select[alt="shortage_igst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), igst_acc_id)==-1){
            if($('#vat_cst').val()=='INTER'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('input[alt="shortage_qty[]"]').each(function(index){
        if(parseFloat($(this).val())==0){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please enter shortage qty.";
            // validator.showErrors(errors);
            valid = false;
        }
    });

    $("#form_purchase_details").find('select[alt="expiry_cost_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), purchase_acc_id)==-1){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please select account id as per purchase.";
            // validator.showErrors(errors);
            valid = false;
        }
    });
    // $("#form_purchase_details").find('select[alt="expiry_tax_acc_id[]"]').each(function(index){
    //     if($.inArray($(this).val(), tax_acc_id)==-1){
    //         // var errors = {};
    //         var name = $(this).attr('name');
    //         errors[name] = "Please select account id as per purchase.";
    //         // validator.showErrors(errors);
    //         valid = false;
    //     }
    // });
    $("#form_purchase_details").find('select[alt="expiry_cgst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), cgst_acc_id)==-1){
            if($('#vat_cst').val()=='INTRA'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('select[alt="expiry_sgst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), sgst_acc_id)==-1){
            if($('#vat_cst').val()=='INTRA'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('select[alt="expiry_igst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), igst_acc_id)==-1){
            if($('#vat_cst').val()=='INTER'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('input[alt="expiry_qty[]"]').each(function(index){
        if(parseFloat($(this).val())==0){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please enter expiry qty.";
            // validator.showErrors(errors);
            valid = false;
        }
    });

    $("#form_purchase_details").find('select[alt="damaged_cost_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), purchase_acc_id)==-1){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please select account id as per purchase.";
            // validator.showErrors(errors);
            valid = false;
        }
    });
    // $("#form_purchase_details").find('select[alt="damaged_tax_acc_id[]"]').each(function(index){
    //     if($.inArray($(this).val(), tax_acc_id)==-1){
    //         // var errors = {};
    //         var name = $(this).attr('name');
    //         errors[name] = "Please select account id as per purchase.";
    //         // validator.showErrors(errors);
    //         valid = false;
    //     }
    // });
    $("#form_purchase_details").find('select[alt="damaged_cgst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), cgst_acc_id)==-1){
            if($('#vat_cst').val()=='INTRA'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('select[alt="damaged_sgst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), sgst_acc_id)==-1){
            if($('#vat_cst').val()=='INTRA'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('select[alt="damaged_igst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), igst_acc_id)==-1){
            if($('#vat_cst').val()=='INTER'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('input[alt="damaged_qty[]"]').each(function(index){
        if(parseFloat($(this).val())==0){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please enter damaged qty.";
            // validator.showErrors(errors);
            valid = false;
        }
    });

    $("#form_purchase_details").find('select[alt="margindiff_cost_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), purchase_acc_id)==-1){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please select account id as per purchase.";
            // validator.showErrors(errors);
            valid = false;
        }
    });
    // $("#form_purchase_details").find('select[alt="margindiff_tax_acc_id[]"]').each(function(index){
    //     if($.inArray($(this).val(), tax_acc_id)==-1){
    //         // var errors = {};
    //         var name = $(this).attr('name');
    //         errors[name] = "Please select account id as per purchase.";
    //         // validator.showErrors(errors);
    //         valid = false;
    //     }
    // });
    $("#form_purchase_details").find('select[alt="margindiff_cgst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), cgst_acc_id)==-1){
            if($('#vat_cst').val()=='INTRA'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('select[alt="margindiff_sgst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), sgst_acc_id)==-1){
            if($('#vat_cst').val()=='INTRA'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('select[alt="margindiff_igst_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), igst_acc_id)==-1){
            if($('#vat_cst').val()=='INTER'){
                // var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Please select account id as per purchase.";
                // validator.showErrors(errors);
                valid = false;
            }
        }
    });
    $("#form_purchase_details").find('input[alt="margindiff_qty[]"]').each(function(index){
        if(parseFloat($(this).val())==0){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please enter margin difference qty.";
            // validator.showErrors(errors);
            valid = false;
        }
    });

    validator.showErrors(errors);
    return valid;
}
function purchase_invalid_handler(){
    var errors="";
    if ($('#shortage_modal').find("input.error, select.error").length>0) {
        errors=errors+"<span>Please Clear errors in Shortage details.</span> <br/>";
        $('#shortage_validation_icon').show();
    } else {
        $('#shortage_validation_icon').hide();
    }
    if ($('#expiry_modal').find("input.error, select.error").length>0) {
        errors=errors+"<span>Please Clear errors in Expiry Details.</span> <br/>";
        $('#expiry_validation_icon').show();
    } else {
        $('#expiry_validation_icon').hide();
    }
    if ($('#damaged_modal').find("input.error, select.error").length>0) {
        errors=errors+"<span>Please Clear errors in Damaged Details.</span> <br/>";
        $('#damaged_validation_icon').show();
    } else {
        $('#damaged_validation_icon').hide();
    }
    if ($('#margindiff_modal').find("input.error, select.error").length>0) {
        errors=errors+"<span>Please Clear errors in Margin Difference Details.</span> <br/>";
        $('#margindiff_validation_icon').show();
    } else {
        $('#margindiff_validation_icon').hide();
    }

    $('#form_errors').html(errors);

    if(errors!=""){
        $('#form_errors_group').show();
        $('#form_errors').show();
    } else {
        $('#form_errors_group').hide();
        $('#form_errors').hide();
    }
}




// ----------------- JOURNAL VOUCHER FORM VALIDATION -------------------------------------
$("#journal_voucher").validate({
    rules: {
        diff_amt: {
            required: true
        },
        approver_id: {
            required: true
        }
    },

    ignore: false,
    onkeyup: false,

    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    }
});

$('#journal_voucher').submit(function() {
    removeMultiInputNamingRules('#journal_voucher', 'select[alt="acc_id[]"]');
    removeMultiInputNamingRules('#journal_voucher', 'input[alt="acc_code[]"]');
    removeMultiInputNamingRules('#journal_voucher', 'select[alt="transaction[]"]');
    removeMultiInputNamingRules('#journal_voucher', 'input[alt="debit_amt[]"]');
    removeMultiInputNamingRules('#journal_voucher', 'input[alt="credit_amt[]"]');

    addMultiInputNamingRules('#journal_voucher', 'select[name="acc_id[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="acc_code[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="debit_amt[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="credit_amt[]"]', { required: true });

    if (!$("#journal_voucher").valid()) {

        return false;
    } else {
        if (check_acc_jv_details()==false) {
            return false;
        } else {
            removeMultiInputNamingRules('#journal_voucher', 'select[alt="acc_id[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="acc_code[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'select[alt="transaction[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="debit_amt[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="credit_amt[]"]');

            return true;
        }
    }
});

function check_acc_jv_details() {
    var validator = $("#journal_voucher").validate();
    var valid = true;

    if (parseFloat(get_number($('#diff_amt').val(),2))!=0) {
        var errors = {};
        var name = "diff_amt";
        errors[name] = "Differenace should be zero.";
        validator.showErrors(errors);
        valid = false;
    }

    return valid;
}



// ----------------- PAYMENT RECEIPT FORM VALIDATION -------------------------------------
$("#payment_receipt").validate({
    rules: {
        trans_type: {
            required: true
        },
        acc_id: {
            required: true
        },
        acc_code: {
            required: true
        },
        bank_id: {
            required: true
        },
        payment_type: {
            required: true
        },
        amount: {
            required: true
        },
        // ref_no: {
        //     required: true
        // },
        paying_debit_amt: {
            required: true
        },
        paying_credit_amt: {
            required: true
        },
        payment_date: {
            required: true
        },
        approver_id: {
            required: true
        }
    },

    ignore: ":not(:visible)",

    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    }
});

$('#payment_receipt').submit(function() {
    if (!$("#payment_receipt").valid()) {
        return false;
    } else {
        if (check_acc_payment_receipt()==false) {
            return false;
        }

        return true;
    }
});

function check_acc_payment_receipt() {
    var validator = $("#payment_receipt").validate();
    var valid = true;

    if($("#payment_type").val()=="Knock off"){
        if (parseFloat(get_number($('#paying_debit_amt').val(),2))==0 && parseFloat(get_number($('#paying_credit_amt').val(),2))==0) {
            var errors = {};
            var name = "paying_debit_amt";
            errors[name] = "Please select atleast one payment.";
            validator.showErrors(errors);
            valid = false;
        }
        if($("#trans_type").val()=="Payment" && parseFloat(get_number($('#payable_credit_amt').val(),2))==0) {
            var errors = {};
            var name = "payable_credit_amt";
            errors[name] = "Payable amount should be credit.";
            validator.showErrors(errors);
            valid = false;
        }
        if($("#trans_type").val()=="Receipt" && parseFloat(get_number($('#payable_debit_amt').val(),2))==0) {
            var errors = {};
            var name = "payable_debit_amt";
            errors[name] = "Payable amount should be debit.";
            validator.showErrors(errors);
            valid = false;
        }
    }

    return valid;
}




// ----------------- LEDGER REPORT FORM VALIDATION -------------------------------------
$("#ledger_report").validate({
    rules: {
        from_date: {
            required: true
        },
        to_date: {
            required: true
        },
        account: {
            required: true
        }
    },

    ignore: false,

    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    }
});

$('#ledger_report').submit(function() {
    if (!$("#ledger_report").valid()) {
        return false;
    } else {
        return true;
    }
});




// ----------------- USER ROLE DETAILS FORM VALIDATION -------------------------------------
$("#user_role").validate({
    rules: {
        role: {
            required: true,
            checkRoleAvailability: true
        }
    },

    ignore: false,
    onkeyup: false,

    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    }
});

$.validator.addMethod("checkRoleAvailability", function (value, element) {
    var result = 1;

    $.ajax({
        url: BASE_URL+'index.php?r=userrole%2Fcheckroleavailablity',
        data: $("#user_role").serialize(),
        type: "POST",
        dataType: 'html',
        global: false,
        async: false,
        success: function (data) {
            result = parseInt(data);
        },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      }
    });

    if (result) {
        return false;
    } else {
        return true;
    }
}, 'User Role already exist.');

$('#user_role').submit(function() {
    if (!$("#user_role").valid()) {
        return false;
    } else {
        if (checkRole()==false) {
            return false;
        }

        return true;
    }
});

function checkRole() {
    var validator = $("#user_role").validate();
    var valid = true;

    var result = 1;

    $('.cls_chk').each(function(){
        if ($(this).is(":checked")) result=0;
    });

    if (result) {
        var errors = {};
        var name = "role";
        errors[name] = "Please assign atleast one role.";
        validator.showErrors(errors);
        valid = false;
    }

    return valid;
}





// ----------------- ASSIGN ROLE DETAILS FORM VALIDATION -------------------------------------
$("#assign_role").validate({
    rules: {
        role: {
            required: true,
            checkUserRoleAvailability: true
        }
    },

    ignore: false,
    onkeyup: false,

    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    }
});

$.validator.addMethod("checkUserRoleAvailability", function (value, element) {
    var result = 1;

    $.ajax({
        url: BASE_URL+'index.php?r=userrole%2Fcheckroleavailablity',
        data: $("#assign_role").serialize(),
        type: "POST",
        dataType: 'html',
        global: false,
        async: false,
        success: function (data) {
            result = parseInt(data);
        },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      }
    });

    if (result) {
        return false;
    } else {
        return true;
    }
}, 'User Role already exist.');

$('#assign_role').submit(function() {
    if (!$("#assign_role").valid()) {
        return false;
    } else {
        return true;
    }
});