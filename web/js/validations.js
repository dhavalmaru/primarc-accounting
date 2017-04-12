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
            required: true
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
    if (!$("#account_master").valid()) {
        return false;
    } else {
        return true;
    }
});




// ----------------- ACCOUNT CATEGORY MASTER FORM VALIDATION -------------------------------------
// $("#account_category_master").validate({
//     rules: {
        
//     },

//     ignore: false,

//     errorPlacement: function (error, element) {
//         var placement = $(element).data('error');
//         if (placement) {
//             $(placement).append(error);
//         } else {
//             error.insertAfter(element);
//         }
//     }
// });

// $('#account_category_master').submit(function() {
//     removeMultiInputNamingRules('#account_category_master', 'input[alt="category_1[]"]');
//     removeMultiInputNamingRules('#account_category_master', 'input[alt="category_2[]"]');
//     removeMultiInputNamingRules('#account_category_master', 'input[alt="category_3[]"]');

//     addMultiInputNamingRules('#account_category_master', 'input[name="category_1[]"]', { required: true });
//     addMultiInputNamingRules('#account_category_master', 'input[name="category_2[]"]', { required: true });
//     addMultiInputNamingRules('#account_category_master', 'input[name="category_3[]"]', { required: true });

//     if (!$("#account_category_master").valid()) {
//         return false;
//     } else {
//         removeMultiInputNamingRules('#account_category_master', 'input[alt="category_1[]"]');
//         removeMultiInputNamingRules('#account_category_master', 'input[alt="category_2[]"]');
//         removeMultiInputNamingRules('#account_category_master', 'input[alt="category_3[]"]');

//         return true;
//     }
// });




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
$("#form_purchase_details").validate({
    rules: {
        other_charges_acc_id: {
            required: true
        },
        other_charges_ledger_code: {
            required: true
        },
        total_amount_acc_id: {
            required: true
        },
        total_amount_ledger_code: {
            required: true
        },
        total_deduction_acc_id: {
            required: true
        },
        total_deduction_ledger_code: {
            required: true
        },
        shortage_acc_id: {
            required: true
        },
        shortage_ledger_code: {
            required: true
        },
        expiry_acc_id: {
            required: true
        },
        expiry_ledger_code: {
            required: true
        },
        damaged_acc_id: {
            required: true
        },
        damaged_ledger_code: {
            required: true
        },
        margin_diff_acc_id: {
            required: true
        },
        margin_diff_ledger_code: {
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

$('#form_purchase_details').submit(function() {
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="invoice_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="invoice_cost_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="invoice_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="invoice_tax_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="shortage_cost_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="expiry_cost_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="damaged_cost_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margin_diff_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="margin_diff_cost_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="shortage_tax_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="expiry_tax_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="damaged_tax_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margin_diff_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="margin_diff_tax_ledger_code[]"]');

    addMultiInputNamingRules('#form_purchase_details', 'select[name="invoice_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="invoice_cost_ledger_code[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="invoice_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="invoice_tax_ledger_code[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="shortage_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="shortage_cost_ledger_code[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="expiry_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="expiry_cost_ledger_code[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="damaged_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="damaged_cost_ledger_code[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="margin_diff_cost_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="margin_diff_cost_ledger_code[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="shortage_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="shortage_tax_ledger_code[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="expiry_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="expiry_tax_ledger_code[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="damaged_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="damaged_tax_ledger_code[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'select[name="margin_diff_tax_acc_id[]"]', { required: true });
    addMultiInputNamingRules('#form_purchase_details', 'input[name="margin_diff_tax_ledger_code[]"]', { required: true });

    if (!$("#form_purchase_details").valid()) {
        return false;
    } else {
        removeMultiInputNamingRules('#form_purchase_details', 'select[alt="invoice_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="invoice_cost_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="invoice_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="invoice_tax_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="shortage_cost_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="expiry_cost_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="damaged_cost_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margin_diff_cost_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="margin_diff_cost_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="shortage_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="shortage_tax_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="expiry_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="expiry_tax_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="damaged_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="damaged_tax_ledger_code[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'select[alt="margin_diff_tax_acc_id[]"]');
    removeMultiInputNamingRules('#form_purchase_details', 'input[alt="margin_diff_tax_ledger_code[]"]');
        
        return true;
    }
});



// ----------------- JOURNAL VOUCHER FORM VALIDATION -------------------------------------
$("#journal_voucher").validate({
    rules: {
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
        removeMultiInputNamingRules('#journal_voucher', 'select[alt="acc_id[]"]');
        removeMultiInputNamingRules('#journal_voucher', 'input[alt="acc_code[]"]');
        removeMultiInputNamingRules('#journal_voucher', 'select[alt="transaction[]"]');
        removeMultiInputNamingRules('#journal_voucher', 'input[alt="debit_amt[]"]');
        removeMultiInputNamingRules('#journal_voucher', 'input[alt="credit_amt[]"]');

        return false;
    } else {
        if (check_journal_voucher_details()==false) {
            removeMultiInputNamingRules('#journal_voucher', 'select[alt="acc_id[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="acc_code[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'select[alt="transaction[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="debit_amt[]"]');
            removeMultiInputNamingRules('#journal_voucher', 'input[alt="credit_amt[]"]');

            return false;
        }

        return true;
    }
});

function check_journal_voucher_details() {
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
        ref_no: {
            required: true
        },
        paying_debit_amt: {
            required: true
        },
        paying_credit_amt: {
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
        if (check_payment_receipt_details()==false) {
            return false;
        }

        return true;
    }
});

function check_payment_receipt_details() {
    var validator = $("#payment_receipt").validate();
    var valid = true;

    if (parseFloat(get_number($('#paying_debit_amt').val(),2))==0 && parseFloat(get_number($('#paying_credit_amt').val(),2))==0) {
        var errors = {};
        var name = "paying_debit_amt";
        errors[name] = "Please select atleast one payment.";
        validator.showErrors(errors);
        valid = false;
    }

    return valid;
}