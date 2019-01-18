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

$.validator.addMethod("numbersandcommaanddotonly", function(value, element) {
    return this.optional(element) || /^(0*[1-9][0-9.,]*)$/i.test(value);
}, "Not Valid Input");

jQuery.validator.addMethod("validDate", function(value, element) {
    return this.optional(element) || moment(value,"DD/MM/YYYY").isValid();
}, "Please enter a valid date in the format DD/MM/YYYY");

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
        customer_id: {
            required: true
        },
        legal_name: {
            required: true,
            check_legal_name_availablity: true,
            check_legal_name_availablity_in_acc_master: true
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
        sub_account_type: {
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
        // ac_category_1: {
        //     required: true
        // },
        approver_id: {
            required: true
        },
        state_id: {
            required: function(element) {
                        if($("#type").val()=="Goods Purchase" ||$("#type").val()=="Goods Sales"||$("#type").val()=="GST Tax"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        
        gst_rate: {
            required: function(element) {
                        if($("#type").val()=="Goods Purchase" ||$("#type").val()=="Goods Sales"||$("#type").val()=="GST Tax"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        
        tax_id: {
            required: function(element) {
                        if($("#type").val()=="GST Tax"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        input_output: {
            required: function(element) {
                        if($("#type").val()=="GST Tax"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        state_type: {
            required: function(element) {
                        if($("#type").val()=="Goods Purchase" ||$("#type").val()=="Goods Sales"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        
        bus_type: {
            required: function(element) {
                        if($("#type").val()=="Goods Sales"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        
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

$.validator.addMethod("check_legal_name_availablity_in_acc_master", function (value, element) {
    var validator = $("#account_master").validate();
    var result = 1;

    $.ajax({
        url: BASE_URL+'index.php?r=accountmaster%2Fchecklegalnameavailablityinaccmaster',
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
}, 'Legal Name already in use in group master.');


$("#payment_upload").validate({
    rules: {
        acc_id: {
            required: true
        },
        to_date: {
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


$('#payment_upload').submit(function() {
    removeMultiInputNamingRules('#payment_upload', 'select[alt="acc_id[]"]');

    addMultiInputNamingRules('#payment_upload', 'select[name="acc_id[]"]', { required: true });

    if (!$("#payment_upload").valid()) {

        return false;
    } else {
         removeMultiInputNamingRules('#payment_upload', 'select[alt="acc_id[]"]');

    }
});

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

$("#scraping_upload").validate({
    rules: {
        scraping_file: {
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
    removeMultiInputNamingRules('#journal_voucher', 'input[alt="invoice_no[]"]');
    removeMultiInputNamingRules('#journal_voucher', 'input[alt="invoice_date[]"]');
    removeMultiInputNamingRules('#journal_voucher', 'input[alt="invoice_amount[]"]');

    addMultiInputNamingRules('#journal_voucher', 'select[name="acc_id[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="acc_code[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="debit_amt[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="credit_amt[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="invoice_no[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="invoice_date[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="invoice_amount[]"]', { required: true });
   
    $('.invoice_no,.invoice_date,.invoice_amount').each(function(){
        $(this).rules('add', { required: true });
    });  

      

    if (!$("#journal_voucher").valid()) {
        jv_invalid_handler();
        return false;
    } else {
        if (check_acc_jv_details()==false) {
            jv_invalid_handler();
            return false;
        }else if(check_jv_invoice_details()==false)
        {
            jv_invalid_handler();
            return false;
        }else {

            removeMultiInputNamingRules('#journal_voucher', 'select[alt="acc_id[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="acc_code[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'select[alt="transaction[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="debit_amt[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="credit_amt[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="invoice_no[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="invoice_date[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="invoice_amount[]"]');

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
        errors[name] = "Difference should be zero.";
        validator.showErrors(errors);
        valid = false;
    }

    return valid;
}

function check_jv_invoice_details(){
    var validator = $("#journal_voucher").validate();
    var valid = true;
    $('.voucher .debit_amt , .credit_amt').each(function(){
        var element_val = get_number($(this).val(),2);
        if(parseInt(element_val)!=0 && element_val!="")
        {
            var id = $(this).attr('id');
            // console.log('id'+id);
            var elem_id = id.substr(id.lastIndexOf('_')+1);
            // console.log('elem_id'+elem_id);
            var total_val = $('#sum_total_'+elem_id).text();
            // console.log('sum_total'+total_val);
            if(total_val!=undefined && total_val!="")
            {
                if(parseInt(element_val)!=parseInt(total_val))
                {
                    // console.log('total_val'+total_val);
                    var errors = {};
                    var name = $(this).attr('name');
                    // console.log('name'+elem_id);
                    errors[name] = "Invoice Amount and Actual Amount Should Be Same";
                    validator.showErrors(errors);
                    valid = false;
                }   
            }
        }
        
    });

    /*$('.voucher  .credit_amt').each(function(){
        var element_val = $(this).val();
        var id = $(this).attr('id');
        var elem_id = id.substr(id.lastIndexOf('_')+1);
        var total_val = $('#sum_total_'+elem_id).val();
        if(total_val!=undefined)
        {
            if(parseInt(element_val)!=parseInt(total_val))
            {
                var errors = {};
                var name = $(this).attr('name');
                errors[name] = "Invoice Amount and Debit Amount Should Be Same";
                validator.showErrors(errors);
                valid = false;
            }   
        }  
    });*/

     return valid;
}

function jv_invalid_handler() {
    $('.errors').remove();
    $('.jv_body_detail').each(function(){
        var id = $(this).attr('id');
       
        var index = id.substr(id.lastIndexOf('_')+1);
        if ($('#jv_'+index).find("input.error").length>0) {
           var errors = "<span>Please Clear Errors</span> <br/>";
           
            $('#'+index).after('<div class="errors" style="color: #dd4b39!important;"><br>'+errors+'</div>');
        }
    });
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
            required: true,
            numbersandcommaanddotonly: true
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
        // if (parseFloat(get_number($('#paying_debit_amt').val(),2))==0 && parseFloat(get_number($('#paying_credit_amt').val(),2))==0) {
        //     var errors = {};
        //     var name = "paying_debit_amt";
        //     errors[name] = "Please select atleast one payment.";
        //     validator.showErrors(errors);
        //     valid = false;
        // }
        // if($("#trans_type").val()=="Payment" && parseFloat(get_number($('#payable_credit_amt').val(),2))==0) {
        //     var errors = {};
        //     var name = "payable_credit_amt";
        //     errors[name] = "Payable amount should be credit.";
        //     validator.showErrors(errors);
        //     valid = false;
        // }
        // if($("#trans_type").val()=="Receipt" && parseFloat(get_number($('#payable_debit_amt').val(),2))==0) {
        //     var errors = {};
        //     var name = "payable_debit_amt";
        //     errors[name] = "Payable amount should be debit.";
        //     validator.showErrors(errors);
        //     valid = false;
        // }
        if (parseFloat(get_number($('#paying_amount_total').val(),2))==0 || $('#paying_amount_total').val()=='' || $('#paying_amount_total').val()==null) {
            var errors = {};
            var name = "paying_amount_total";
            errors[name] = "Please select atleast one payment.";
            validator.showErrors(errors);
            valid = false;
        }
        if($("#trans_type").val()=="Payment" && $('#paying_transaction').val()=="Debit") {
            var errors = {};
            var name = "paying_amount_total";
            errors[name] = "Payable amount should be credit.";
            validator.showErrors(errors);
            valid = false;
        }
        if($("#trans_type").val()=="Receipt" && $('#paying_transaction').val()=="Credit") {
            var errors = {};
            var name = "paying_amount_total";
            errors[name] = "Payable amount should be debit.";
            validator.showErrors(errors);
            valid = false;
        }
    } else {
        if(parseFloat(get_number($('#amount').val(),2))==0){
            var errors = {};
            var name = "amount";
            errors[name] = "Amount should be greater than zero.";
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
        user_id: {
            required: true,
            checkUserRoleAvailability: true
        },
        role_id: {
            required: true
        },
        company_id: {
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

$.validator.addMethod("checkUserRoleAvailability", function (value, element) {
    var result = 1;

    $.ajax({
        url: BASE_URL+'index.php?r=assignrole%2Fcheckuserroleavailability',
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
}, 'User already have a role in selected company.');

$('#assign_role').submit(function() {
    if (!$("#assign_role").valid()) {
        return false;
    } else {
        return true;
    }
});





// ----------------- GOODS OUTWARD DEBIT DETAILS FORM VALIDATION -------------------------------------
$("#go_debit_details").validate({
    rules: {
        diff_amt: {
            required: true
        },
        // approver_id: {
        //     required: true
        // }
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

$('#go_debit_details').submit(function() {
    removeMultiInputNamingRules('#go_debit_details', 'select[alt="acc_id[]"]');
    removeMultiInputNamingRules('#go_debit_details', 'input[alt="acc_code[]"]');
    removeMultiInputNamingRules('#go_debit_details', 'select[alt="transaction[]"]');
    removeMultiInputNamingRules('#go_debit_details', 'input[alt="debit_amt[]"]');
    removeMultiInputNamingRules('#go_debit_details', 'input[alt="credit_amt[]"]');

    addMultiInputNamingRules('#go_debit_details', 'select[name="acc_id[]"]', { required: true });
    addMultiInputNamingRules('#go_debit_details', 'input[name="acc_code[]"]', { required: true });
    addMultiInputNamingRules('#go_debit_details', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#go_debit_details', 'input[name="debit_amt[]"]', { required: true });
    addMultiInputNamingRules('#go_debit_details', 'input[name="credit_amt[]"]', { required: true });

    if (!$("#go_debit_details").valid()) {

        return false;
    } else {
        if (check_acc_debit_details()==false) {
            return false;
        } else {
            removeMultiInputNamingRules('#go_debit_details', 'select[alt="acc_id[]"]');
            removeMultiInputNamingRules('#go_debit_details', 'input[alt="acc_code[]"]');
            removeMultiInputNamingRules('#go_debit_details', 'select[alt="transaction[]"]');
            removeMultiInputNamingRules('#go_debit_details', 'input[alt="debit_amt[]"]');
            removeMultiInputNamingRules('#go_debit_details', 'input[alt="credit_amt[]"]');

            return true;
        }
    }
});

function check_acc_debit_details() {
    var validator = $("#go_debit_details").validate();
    var valid = true;

    if (parseFloat(get_number($('#diff_amt').val(),2))!=0) {
        var errors = {};
        var name = "diff_amt";
        errors[name] = "Difference should be zero.";
        validator.showErrors(errors);
        valid = false;
    }

    return valid;
}





// ----------------- OTHER DEBIT CREDIT FORM VALIDATION -------------------------------------
$("#other_debit_credit").validate({
    rules: {
        vendor_id: {
            required: true
        },
        vendor_warehouse_id: {
            required: true
        },
        trans_type: {
            required: true
        },
        warehouse_id: {
            required: function(element) {
                        if($("#trans_type").val()=="Invoice"){
                            return true;
                        } else {
                            return false;
                        }
                    }
        },
        date_of_transaction: {
            required: true
        },
        transaction: {
            required: true
        },
        diff_amt: {
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

$('#other_debit_credit').submit(function() {
    removeMultiInputNamingRules('#other_debit_credit', 'select[alt="acc_id[]"]');
    removeMultiInputNamingRules('#other_debit_credit', 'input[alt="acc_code[]"]');
    removeMultiInputNamingRules('#other_debit_credit', 'select[alt="transaction[]"]');
    removeMultiInputNamingRules('#other_debit_credit', 'input[alt="debit_amt[]"]');
    removeMultiInputNamingRules('#other_debit_credit', 'input[alt="credit_amt[]"]');

    addMultiInputNamingRules('#other_debit_credit', 'select[name="acc_id[]"]', { required: true });
    addMultiInputNamingRules('#other_debit_credit', 'input[name="acc_code[]"]', { required: true });
    addMultiInputNamingRules('#other_debit_credit', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#other_debit_credit', 'input[name="debit_amt[]"]', { required: true });
    addMultiInputNamingRules('#other_debit_credit', 'input[name="credit_amt[]"]', { required: true });

    if (!$("#other_debit_credit").valid()) {

        return false;
    } else {
        if (check_acc_other_debit_credit_details()==false) {
            return false;
        } else {
            removeMultiInputNamingRules('#other_debit_credit', 'select[alt="acc_id[]"]');
            removeMultiInputNamingRules('#other_debit_credit', 'input[alt="acc_code[]"]');
            removeMultiInputNamingRules('#other_debit_credit', 'select[alt="transaction[]"]');
            removeMultiInputNamingRules('#other_debit_credit', 'input[alt="debit_amt[]"]');
            removeMultiInputNamingRules('#other_debit_credit', 'input[alt="credit_amt[]"]');

            return true;
        }
    }
});

function check_acc_other_debit_credit_details() {
    var validator = $("#other_debit_credit").validate();
    var valid = true;

    if (parseFloat(get_number($('#diff_amt').val(),2))!=0) {
        var errors = {};
        var name = "diff_amt";
        errors[name] = "Difference should be zero.";
        validator.showErrors(errors);
        valid = false;
    }

    return valid;
}

$("#detailedinvoice_report").validate({
    rules: {
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

$('#detailedinvoice_report').submit(function() {
    removeMultiInputNamingRules('#detailedinvoice_report', 'select[alt="group[]"]');
    removeMultiInputNamingRules('#detailedinvoice_report', 'select[alt="ledger_name[]"]');

    addMultiInputNamingRules('#detailedinvoice_report', 'select[name="group[]"]', { required: true });
    addMultiInputNamingRules('#detailedinvoice_report', 'select[name="ledger_name[]"]', { required: true });

    if (!$("#detailedinvoice_report").valid()) {

        return false;
    } else {
         removeMultiInputNamingRules('#detailedinvoice_report', 'select[alt="group[]"]');
         removeMultiInputNamingRules('#detailedinvoice_report', 'select[alt="ledger_name[]"]');

    }
});



// ----------------- PROMOTION FORM VALIDATION -------------------------------------
$("#promotion").validate({
    rules: {
        vendor_id: {
            required: true
        },
        promotion_type: {
            required: true
        },
        trans_type: {
            required: true
        },
        date_of_transaction: {
            required: true
        },
        transaction: {
            required: true
        },
        diff_amt: {
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

$('#promotion').submit(function() {
    removeMultiInputNamingRules('#promotion', 'select[alt="acc_id[]"]');
    removeMultiInputNamingRules('#promotion', 'input[alt="acc_code[]"]');
    removeMultiInputNamingRules('#promotion', 'select[alt="transaction[]"]');
    removeMultiInputNamingRules('#promotion', 'input[alt="debit_amt[]"]');
    removeMultiInputNamingRules('#promotion', 'input[alt="credit_amt[]"]');

    addMultiInputNamingRules('#promotion', 'select[name="acc_id[]"]', { required: true });
    addMultiInputNamingRules('#promotion', 'input[name="acc_code[]"]', { required: true });
    addMultiInputNamingRules('#promotion', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#promotion', 'input[name="debit_amt[]"]', { required: true });
    addMultiInputNamingRules('#promotion', 'input[name="credit_amt[]"]', { required: true });

    if (!$("#promotion").valid()) {

        return false;
    } else {
        if (check_acc_promotion_details()==false) {
            return false;
        } else {
            removeMultiInputNamingRules('#promotion', 'select[alt="acc_id[]"]');
            removeMultiInputNamingRules('#promotion', 'input[alt="acc_code[]"]');
            removeMultiInputNamingRules('#promotion', 'select[alt="transaction[]"]');
            removeMultiInputNamingRules('#promotion', 'input[alt="debit_amt[]"]');
            removeMultiInputNamingRules('#promotion', 'input[alt="credit_amt[]"]');

            return true;
        }
    }
});

function check_acc_promotion_details() {
    var validator = $("#promotion").validate();
    var valid = true;

    if (parseFloat(get_number($('#diff_amt').val(),2))!=0) {
        var errors = {};
        var name = "diff_amt";
        errors[name] = "Difference should be zero.";
        validator.showErrors(errors);
        valid = false;
    }

    return valid;
}





// ----------------- PURCHASE LEDGER REPORT VALIDATION -------------------------------------
$("#detailledger_report").validate({
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

$('#detailledger_report').submit(function() {
    // removeMultiInputNamingRules('#detailledger_report', 'select[alt="account[]"]');
    // removeMultiInputNamingRules('#detailledger_report', 'select[alt="vouchertype[]"]');
    // removeMultiInputNamingRules('#detailledger_report', 'select[alt="state[]"]');

    // addMultiInputNamingRules('#detailledger_report', 'select[name="account[]"]', { required: true }, "");
    // addMultiInputNamingRules('#detailledger_report', 'select[name="vouchertype[]"]', { required: true }, "");
    // addMultiInputNamingRules('#detailledger_report', 'select[name="state[]"]', { required: true }, "");

    if (!$("#detailledger_report").valid()) {
        return false;
    } else {
        // removeMultiInputNamingRules('#detailledger_report', 'select[alt="account[]"]');
        // removeMultiInputNamingRules('#detailledger_report', 'select[alt="vouchertype[]"]');
        // removeMultiInputNamingRules('#detailledger_report', 'select[alt="state[]"]');

        return true;
    }
});






// ----------------- RECONSILATION REPORT VALIDATION -------------------------------------
$("#reconsilation_form").validate({
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

$("#reconsilation_form").submit(function() {
    removeMultiInputNamingRules('#reconsilation_form', 'input[alt="payment_date[]"]');
    addMultiInputNamingRules('#reconsilation_form', 'input[name="payment_date[]"]', { validDate: true }, "");
    
    if($('#form_val').val()=='true'){
        if (!$("#reconsilation_form").valid()) {
            return false;
        } else {
            if(reconsiled()==false) {
                return false;
            } else {
                removeMultiInputNamingRules('#reconsilation_form', 'input[alt="payment_date[]"]');
                return true;
            }
        }
    }
});

function set_form_val(elem){
    if(elem.value=="submit"){
        $('#form_val').val('true');
    } else {
        $('#form_val').val('false');
    }
}

function reconsiled()
{
    var validator = $("#reconsilation_form").validate();
    var count = $(".payment_date").length;
    var valid = true;
    for(var i=0;i<count;i++)
    {
        paymentdate = $("#payment_date_"+i).val();
        var ref_date = $("#payment_date_"+i).closest('tr').children('td.ref_date').text();   
            ref_date = splidate(ref_date);
            ref_date = Date.parse(ref_date); 


        var todate = $("#to_date").val();
        todate = splidate(todate);
        todate = Date.parse(todate);

        var from_date = $("#from_date").val();
        from_date = splidate(from_date);
        from_date = Date.parse(from_date);

        var bool = true;

        if(paymentdate!="")
        {
            if(paymentdate!="")
            {
                paymentdate = splidate(paymentdate);
                paymentdate = Date.parse(paymentdate);
            }


            /*if (to_date > Date.now())
             {
                if(paymentdate >Date.now()){
                    bool = false;
                    var errors = {};
                    var name = $("#payment_date_"+i).attr('name');
                    errors[name] = "Date Should be smaller than Today's Date";
                    validator.showErrors(errors);
                    valid = false;
                 }
            }*/
            /*else if(to_date < Date.now())
            {
                if(paymentdate > to_date){
                     bool = false;
                    var errors = {};
                    var name = $("#payment_date_"+i).attr('name');
                    errors[name] = "Date Should be smaller than To Date";
                    validator.showErrors(errors);
                    valid = false;
                } 
            }*/
            if(paymentdate>Date.now())
            {
                var errors = {};
                var name = $('#payment_date_'+i).attr('name');
                errors[name] = "Date Should Be Less Then Todays Date";
                validator.showErrors(errors);
                valid = false;
            }
            if(paymentdate>todate)
            {
                var errors = {};
                var name = $('#payment_date_'+i).attr('name');
                errors[name] = "Date Should Be Less Then Selected Todate Date";
                validator.showErrors(errors);
                valid = false;
            }

            if(paymentdate<ref_date)
                {
                        var errors = {};
                        var name = $('#payment_date_'+i).attr('name');
                        errors[name] = "Date Should Be Greater Then Payment Date";
                        validator.showErrors(errors);
                        valid = false;
                }       
        }
    }

    return valid;
}

function splidate(dateStr) {
  var parts = dateStr.split("/")
  return new Date(parts[2], parts[1] - 1, parts[0])
}





// ----------------- TAX MASTER FORM VALIDATION -------------------------------------
$("#tax_type").validate({
    rules: {
        tax_name: {
            required: true,
            check_tax_type_availablity: true
        },
        // tax_details: {
        //     required: true
        // },
        approver_id: {
            required: true
        },
        // remarks: {
        //     required: true
        // }
      
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

$('#tax_type').submit(function() {
    if (!$("#tax_type").valid()) {
        return false;
    } else {
        return true;
    }
});

$.validator.addMethod("check_tax_type_availablity", function (value, element) {
    var validator = $("#tax_type").validate();
    var result = 1;

    $.ajax({
        url: BASE_URL+'index.php?r=taxtype%2Fchecktaxtypeavailablity',
        type: 'post',
        data: $("#tax_type").serialize(),
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
}, 'Tax type already in use.');





// ----------------- Sale DETAILS FORM VALIDATION -------------------------------------
$(function() {
    $("#form_sale_details").validate({
        rules: {
            other_charges_acc_id: {
                required: function(){
                    var blFlag = false;
                    $('.edited_other_charges').each(function() {
                        if($(this).val()!=""){
                            if(parseFloat($(this).val())>0){
                                // console.log("entered"+diffval);
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
            sale_invalid_handler();
        }
    });

    addMultiInputNamingRules_form_sale_details();
})

function addMultiInputNamingRules_form_sale_details(){
    addMultiInputNamingRules('#form_sale_details', 'select[name="invoice_cost_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_sale_details', 'select[name="invoice_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="invoice_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="invoice_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="invoice_igst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="shortage_psku[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="shortage_invoice_no[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="shortage_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="expiry_psku[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="expiry_invoice_no[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="expiry_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="damaged_psku[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="damaged_invoice_no[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="damaged_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="margindiff_psku[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="margindiff_invoice_no[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="margindiff_cost_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_sale_details', 'select[name="shortage_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="shortage_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="shortage_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="shortage_igst_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_sale_details', 'select[name="expiry_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="expiry_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="expiry_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="expiry_igst_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_sale_details', 'select[name="damaged_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="damaged_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="damaged_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="damaged_igst_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_sale_details', 'select[name="margindiff_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="margindiff_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="margindiff_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'select[name="margindiff_igst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'input[name="shortage_qty[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'input[name="expiry_qty[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'input[name="damaged_qty[]"]', { required: true });
    addMultiInputNamingRules('#form_sale_details', 'input[name="margindiff_qty[]"]', { required: true });
}

function removeMultiInputNamingRules_form_sale_details(){
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="invoice_cost_acc_id[]"]');
    // removeMultiInputNamingRules('#form_sale_details', 'select[alt="invoice_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="invoice_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="invoice_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="invoice_igst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="shortage_psku[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="shortage_invoice_no[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="shortage_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="expiry_psku[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="expiry_invoice_no[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="expiry_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="damaged_psku[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="damaged_invoice_no[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="damaged_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="margindiff_psku[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="margindiff_invoice_no[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="margindiff_cost_acc_id[]"]');
    // removeMultiInputNamingRules('#form_sale_details', 'select[alt="shortage_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="shortage_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="shortage_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="shortage_igst_acc_id[]"]');
    // removeMultiInputNamingRules('#form_sale_details', 'select[alt="expiry_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="expiry_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="expiry_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="expiry_igst_acc_id[]"]');
    // removeMultiInputNamingRules('#form_sale_details', 'select[alt="damaged_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="damaged_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="damaged_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="damaged_igst_acc_id[]"]');
    // removeMultiInputNamingRules('#form_sale_details', 'select[alt="margindiff_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="margindiff_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="margindiff_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'select[alt="margindiff_igst_acc_id[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'input[alt="shortage_qty[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'input[alt="expiry_qty[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'input[alt="damaged_qty[]"]');
    removeMultiInputNamingRules('#form_sale_details', 'input[alt="margindiff_qty[]"]');
}

$('#form_sale_details').submit(function() {
    removeMultiInputNamingRules_form_sale_details();
    addMultiInputNamingRules_form_sale_details();
    removeMultiInputNamingRules_sale_details();
    addMultiInputNamingRules_sale_details();
    // console.log('form submit');

    if (!$("#form_sale_details").valid()) {
        // console.log('error');
        sale_invalid_handler();
        return false;
    } else {
        if (check_sale_details()==false) {
            
            sale_invalid_handler();
            return false;
        } else {
            //removeMultiInputNamingRules_sale_details();
            removeMultiInputNamingRules_form_sale_details();
            return true;
        }
    } 
});

function addMultiInputNamingRules_sale_details(){
    // console.log('multinaming rule');
    removeMultiInputNamingRules_sale_details();

    $('.narration').each(function(){
        var th= $(this);
        var name1 = th.attr('id');
        var splited = name1.split("_");
        var lastname = 'diff'+'_'+splited[2]+'_'+splited[1]+'_'+splited[2];
        var diffval = $("#"+lastname).val();

        // console.log(lastname);
       
        if(parseFloat(diffval)!==parseFloat(0) && $(this).val()=="")
        {
            var name = $(this).attr('name');
            // console.log('after multinaming rule'+name);
            addMultiInputNamingRules('#form_sale_details', 'input[name="'+name+'"]', { required: true });
        }
    });

    if(parseFloat($("#diff_other_charges_0").val())!==parseFloat(0) && $("#diff_other_charges_0").val()=="")
    {
        addMultiInputNamingRules('#form_sale_details', 'input[name="narration_other_charges"]', { required: true });
    }
}

function removeMultiInputNamingRules_sale_details(){
    $('.narration').each(function(){
        // var th= $(this);
        // var name1 = th.attr('id');
        // var splited = name1.split("_");
        // var lastname = 'diff'+'_'+splited[2]+'_'+splited[1]+'_'+splited[2];
        // var diffval = $("#"+lastname).val();
        var name = $(this).attr('name');

        // console.log(name);
        
        //removeMultiInputNamingRules('#form_sale_details', 'input[alt="'+name+'"]');
        removeMultiInputNamingRules('#form_sale_details', 'input[name='+name+']');
        /*if(parseFloat(diffval)!==parseFloat(0))
        {
            var name = $(this).attr('name');
            console.log('enteredremove'+name);
            removeMultiInputNamingRules('#form_sale_details', 'input[alt="'+name+'"]');
        }*/
        /*if(parseFloat($("#diff_other_charges_0").val())!==parseFloat(0))
        {
            removeMultiInputNamingRules('#form_sale_details', 'input[alt="narration_other_charges"]');
        }*/
    });

    removeMultiInputNamingRules('#form_sale_details', 'input[name="narration_other_charges"]');
}

function check_sale_details() {
    var validator = $("#form_sale_details").validate();
    var valid = true;
    var purchase_acc_id = [];
    var tax_acc_id = [];
    var cgst_acc_id = [];
    var sgst_acc_id = [];
    var igst_acc_id = [];
    var errors = {};


    /*$('.narration').each(function(){
        var th= $(this);
        var name1 = th.attr('id');

        var splited = name1.split("_");

        var lastname = 'diff'+'_'+splited[2]+'_'+splited[1]+'_'+splited[2];
        var diffval = $("#"+lastname).val();
        // console.log(diffval);
        if(parseFloat(diffval)!=0)
        {
            // var errors = {};
            var name = $(this).attr('name');
            // console.log(name);
            errors[name] = "Narration is required";
            // validator.showErrors(errors);
            valid = false;
        }
    });*/

    $("#form_sale_details").find('select[alt="invoice_cost_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            purchase_acc_id.push($(this).val());
        }
    });
    // $("#form_sale_details").find('select[alt="invoice_tax_acc_id[]"]').each(function(index){
    //     if($(this).val()!=null && $(this).val()!=''){
    //         tax_acc_id.push($(this).val());
    //     }
    // });
    $("#form_sale_details").find('select[alt="invoice_cgst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            cgst_acc_id.push($(this).val());
        }
    });
    $("#form_sale_details").find('select[alt="invoice_sgst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            sgst_acc_id.push($(this).val());
        }
    });
    $("#form_sale_details").find('select[alt="invoice_igst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            igst_acc_id.push($(this).val());
        }
    });

    $("#form_sale_details").find('select[alt="shortage_cost_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), purchase_acc_id)==-1){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please select account id as per purchase.";
            // validator.showErrors(errors);
            valid = false;
        }
    });
    // $("#form_sale_details").find('select[alt="shortage_tax_acc_id[]"]').each(function(index){
    //     if($.inArray($(this).val(), tax_acc_id)==-1){
    //         // var errors = {};
    //         var name = $(this).attr('name');
    //         errors[name] = "Please select account id as per purchase.";
    //         // validator.showErrors(errors);
    //         valid = false;
    //     }
    // });
    $("#form_sale_details").find('select[alt="shortage_cgst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('select[alt="shortage_sgst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('select[alt="shortage_igst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('input[alt="shortage_qty[]"]').each(function(index){
        if(parseFloat($(this).val())==0){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please enter shortage qty.";
            // validator.showErrors(errors);
            valid = false;
        }
    });

    $("#form_sale_details").find('select[alt="expiry_cost_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), purchase_acc_id)==-1){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please select account id as per purchase.";
            // validator.showErrors(errors);
            valid = false;
        }
    });
    // $("#form_sale_details").find('select[alt="expiry_tax_acc_id[]"]').each(function(index){
    //     if($.inArray($(this).val(), tax_acc_id)==-1){
    //         // var errors = {};
    //         var name = $(this).attr('name');
    //         errors[name] = "Please select account id as per purchase.";
    //         // validator.showErrors(errors);
    //         valid = false;
    //     }
    // });
    $("#form_sale_details").find('select[alt="expiry_cgst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('select[alt="expiry_sgst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('select[alt="expiry_igst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('input[alt="expiry_qty[]"]').each(function(index){
        if(parseFloat($(this).val())==0){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please enter expiry qty.";
            // validator.showErrors(errors);
            valid = false;
        }
    });

    $("#form_sale_details").find('select[alt="damaged_cost_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), purchase_acc_id)==-1){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please select account id as per purchase.";
            // validator.showErrors(errors);
            valid = false;
        }
    });
    // $("#form_sale_details").find('select[alt="damaged_tax_acc_id[]"]').each(function(index){
    //     if($.inArray($(this).val(), tax_acc_id)==-1){
    //         // var errors = {};
    //         var name = $(this).attr('name');
    //         errors[name] = "Please select account id as per purchase.";
    //         // validator.showErrors(errors);
    //         valid = false;
    //     }
    // });
    $("#form_sale_details").find('select[alt="damaged_cgst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('select[alt="damaged_sgst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('select[alt="damaged_igst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('input[alt="damaged_qty[]"]').each(function(index){
        if(parseFloat($(this).val())==0){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please enter damaged qty.";
            // validator.showErrors(errors);
            valid = false;
        }
    });

    $("#form_sale_details").find('select[alt="margindiff_cost_acc_id[]"]').each(function(index){
        if($.inArray($(this).val(), purchase_acc_id)==-1){
            // var errors = {};
            var name = $(this).attr('name');
            errors[name] = "Please select account id as per purchase.";
            // validator.showErrors(errors);
            valid = false;
        }
    });
    // $("#form_sale_details").find('select[alt="margindiff_tax_acc_id[]"]').each(function(index){
    //     if($.inArray($(this).val(), tax_acc_id)==-1){
    //         // var errors = {};
    //         var name = $(this).attr('name');
    //         errors[name] = "Please select account id as per purchase.";
    //         // validator.showErrors(errors);
    //         valid = false;
    //     }
    // });
    $("#form_sale_details").find('select[alt="margindiff_cgst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('select[alt="margindiff_sgst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('select[alt="margindiff_igst_acc_id[]"]').each(function(index){
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
    $("#form_sale_details").find('input[alt="margindiff_qty[]"]').each(function(index){
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

function sale_invalid_handler(){
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




// ----------------- GO INTER DEPOT DETAILS FORM VALIDATION -------------------------------------
$(function() {
    $("#form_go_inter_depot_details").validate({
        rules: {
            sales_other_charges_acc_id: {
                required: function(){
                    var blFlag = false;
                    $('.sales_edited_other_charges').each(function() {
                        if($(this).val()!=""){
                            if(parseFloat($(this).val())>0){
                                blFlag = true;
                            }
                        }
                    });

                    return blFlag;
                }
            },
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
            },
            sales_stock_transfer_acc_id: {
                required: function(){
                    var blFlag = false;
                    $('.edited_sales_stock_transfer').each(function() {
                        if($(this).val()!=""){
                            if(parseFloat($(this).val())>0){
                                blFlag = true;
                            }
                        }
                    });

                    return blFlag;
                }
            },
            purchase_stock_transfer_acc_id: {
                required: function(){
                    var blFlag = false;
                    $('.edited_purchase_stock_transfer').each(function() {
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
            go_inter_depot_invalid_handler();
        }
    });

    addMultiInputNamingRules_form_go_inter_depot_details();
})

function addMultiInputNamingRules_form_go_inter_depot_details(){
    addMultiInputNamingRules('#form_go_inter_depot_details', 'select[name="invoice_cost_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_go_inter_depot_details', 'select[name="invoice_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_go_inter_depot_details', 'select[name="invoice_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_go_inter_depot_details', 'select[name="invoice_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_go_inter_depot_details', 'select[name="invoice_igst_acc_id[]"]', { required: true });

    addMultiInputNamingRules('#form_go_inter_depot_details', 'select[name="sales_invoice_cost_acc_id[]"]', { required: true });
    // addMultiInputNamingRules('#form_go_inter_depot_details', 'select[name="sales_invoice_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_go_inter_depot_details', 'select[name="sales_invoice_cgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_go_inter_depot_details', 'select[name="sales_invoice_sgst_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_go_inter_depot_details', 'select[name="sales_invoice_igst_acc_id[]"]', { required: true });
}
function removeMultiInputNamingRules_form_go_inter_depot_details(){
    removeMultiInputNamingRules('#form_go_inter_depot_details', 'select[alt="invoice_cost_acc_id[]"]');
    // removeMultiInputNamingRules('#form_go_inter_depot_details', 'select[alt="invoice_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_go_inter_depot_details', 'select[alt="invoice_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_go_inter_depot_details', 'select[alt="invoice_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_go_inter_depot_details', 'select[alt="invoice_igst_acc_id[]"]');

    removeMultiInputNamingRules('#form_go_inter_depot_details', 'select[alt="sales_invoice_cost_acc_id[]"]');
    // removeMultiInputNamingRules('#form_go_inter_depot_details', 'select[alt="sales_invoice_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_go_inter_depot_details', 'select[alt="sales_invoice_cgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_go_inter_depot_details', 'select[alt="sales_invoice_sgst_acc_id[]"]');
    removeMultiInputNamingRules('#form_go_inter_depot_details', 'select[alt="sales_invoice_igst_acc_id[]"]');
}

$('#form_go_inter_depot_details').submit(function() {
    removeMultiInputNamingRules_form_go_inter_depot_details();
    addMultiInputNamingRules_form_go_inter_depot_details();

    if (!$("#form_go_inter_depot_details").valid()) {
        go_inter_depot_invalid_handler();
        return false;
    } else {
        if (check_go_inter_depot_details()==false) {
            go_inter_depot_invalid_handler();
            return false;
        } else {
            removeMultiInputNamingRules_form_go_inter_depot_details();
            
            return true;
        }
    }
});
function check_go_inter_depot_details() {
    var validator = $("#form_go_inter_depot_details").validate();
    var valid = true;
    var purchase_acc_id = [];
    var tax_acc_id = [];
    var cgst_acc_id = [];
    var sgst_acc_id = [];
    var igst_acc_id = [];
    var errors = {};

    $("#form_go_inter_depot_details").find('select[alt="invoice_cost_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            purchase_acc_id.push($(this).val());
        }
    });
    // $("#form_go_inter_depot_details").find('select[alt="invoice_tax_acc_id[]"]').each(function(index){
    //     if($(this).val()!=null && $(this).val()!=''){
    //         tax_acc_id.push($(this).val());
    //     }
    // });
    $("#form_go_inter_depot_details").find('select[alt="invoice_cgst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            cgst_acc_id.push($(this).val());
        }
    });
    $("#form_go_inter_depot_details").find('select[alt="invoice_sgst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            sgst_acc_id.push($(this).val());
        }
    });
    $("#form_go_inter_depot_details").find('select[alt="invoice_igst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            igst_acc_id.push($(this).val());
        }
    });

    $("#form_go_inter_depot_details").find('select[alt="sales_invoice_cost_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            purchase_acc_id.push($(this).val());
        }
    });
    // $("#form_go_inter_depot_details").find('select[alt="sales_invoice_tax_acc_id[]"]').each(function(index){
    //     if($(this).val()!=null && $(this).val()!=''){
    //         tax_acc_id.push($(this).val());
    //     }
    // });
    $("#form_go_inter_depot_details").find('select[alt="sales_invoice_cgst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            cgst_acc_id.push($(this).val());
        }
    });
    $("#form_go_inter_depot_details").find('select[alt="sales_invoice_sgst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            sgst_acc_id.push($(this).val());
        }
    });
    $("#form_go_inter_depot_details").find('select[alt="sales_invoice_igst_acc_id[]"]').each(function(index){
        if($(this).val()!=null && $(this).val()!=''){
            igst_acc_id.push($(this).val());
        }
    });

    validator.showErrors(errors);
    return valid;
}
function go_inter_depot_invalid_handler(){
    var errors="";
    if ($('#gointerdepot_modal').find("input.error, select.error").length>0) {
        errors=errors+"<span>Please Clear errors in Go Inter Depot details.</span> <br/>";
        $('#gointerdepot_validation_icon').show();
    } else {
        $('#gointerdepot_validation_icon').hide();
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





// ----------------- TAX MASTER FORM VALIDATION -------------------------------------
$("#amazon_state_form").validate({
    rules: {
        amazon_state: {
            required: true,
            check_amazon_state_availablity: true
        },
        erp_state: {
            required: true
        },
        // approver_id: {
        //     required: true
        // },
        // remarks: {
        //     required: true
        // }
      
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

$('#amazon_state_form').submit(function() {
    if (!$("#amazon_state_form").valid()) {
        return false;
    } else {
        return true;
    }
});

$.validator.addMethod("check_amazon_state_availablity", function (value, element) {
    var validator = $("#amazon_state_form").validate();
    var result = 1;

    $.ajax({
        url: BASE_URL+'index.php?r=amazonstate%2Fcheckamazonstateavailablity',
        type: 'post',
        data: $("#amazon_state_form").serialize(),
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
}, 'Amazon State already in use.');




// ----------------- Sale Upload File Form Validation -------------------------------------
$(function() {
    $("#sales_upload_form").validate({
        rules: {
            sales_file: {
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
})

$('#sales_upload_form').submit(function() {
    if (!$("#sales_upload_form").valid()) {
        return false;
    } else {
        return true;
    } 
});




// ----------------- Sale Upload DETAILS FORM VALIDATION -------------------------------------
$(function() {
    $("#form_sale_upload_details").validate({
        rules: {
            
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

    addMultiInputNamingRules_form_sale_upload_details();
})

function addMultiInputNamingRules_form_sale_upload_details(){
    addMultiInputNamingRules('#form_sale_upload_details', 'select[name="acc_id[]"]', { required: true });
}

function removeMultiInputNamingRules_form_sale_upload_details(){
    removeMultiInputNamingRules('#form_sale_upload_details', 'select[alt="acc_id[]"]');
}

$('#form_sale_upload_details').submit(function() {
    removeMultiInputNamingRules_form_sale_upload_details();
    addMultiInputNamingRules_form_sale_upload_details();

    if (!$("#form_sale_upload_details").valid()) {
        return false;
    } else {
        removeMultiInputNamingRules_form_sale_upload_details();
        return true;
    } 
});