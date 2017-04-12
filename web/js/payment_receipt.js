// $("#vendor_id").change(function(){
//     $("#vendor_name").val($("#vendor_id option:selected").text());
//     getLedger();
// });

$("#acc_id").change(function(){
    var acc_id = $("#acc_id").val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
        url: BASE_URL+'index.php?r=paymentreceipt%2Fgetaccdetails',
        type: 'post',
        data: {
                acc_id : acc_id,
                _csrf : csrfToken
             },
        dataType: 'json',
        success: function (data) {
            if(data != null){
                if(data.length>0){
                    $("#acc_code").val(data[0].code);
                    $("#legal_name").val(data[0].legal_name);
                }
            } else {
                $("#acc_code").val("");
                $("#legal_name").val("");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });

    getLedger();
});

$("#bank_id").change(function(){
    $("#bank_name").val($("#bank_id option:selected").text());
    getLedger();
});

$("#payment_type").change(function(){
    setPaymentType();
});

function setPaymentType(){
    if($("#payment_type").val()=="Adhoc"){
        $(".ad_hock").show();
        $("#knock_off").hide();
    } else if($("#payment_type").val()=="Knock off") {
        $(".ad_hock").hide();
        $("#knock_off").show();
    } else {
        $(".ad_hock").hide();
        $("#knock_off").hide();
    }
}

function getLedger(){
    var result = 1;
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    
    $.ajax({
        url: BASE_URL+'index.php?r=paymentreceipt%2Fgetledger',
        type: 'post',
        data: {
                acc_id : $("#acc_id").val(),
                id : $("#id").val(),
                _csrf : csrfToken
             },
        success: function (data) {
            if(data != null){
                $("#ledger_details").html(data);
            } else {
                $("#ledger_details").html("");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });

    // $(".check").each(function( index ) {
    //     console.log($(this).attr("id"));
    //     $(this).change(function(){
    //         getLedgerTotal();
    //     });
    // });

    // if (result) {
    //     return false;
    // } else {
    //     return true;
    // }
}

$(document).ready(function() {
    setPaymentType();
    getLedger();

    // $(".check").each(function( index ) {
    //     $(this).change(function(){
    //         getLedgerTotal();
    //     });
    // });

    // addMultiInputNamingRules('#debit_credit_note', 'select[name="transaction[]"]', { required: true });
    // addMultiInputNamingRules('#debit_credit_note', 'input[name="due_date[]"]', { required: true });
    // addMultiInputNamingRules('#debit_credit_note', 'input[name="amount[]"]', { required: true, numbersandcommaonly: true });
});

$("#check_all").change(function(){
    var blChecked = false;
    if($(this).prop('checked')==true) {
        blChecked = true;
    }

    $(".check").each(function( index ) {
        $(this).prop('checked', blChecked);
    });

    getLedgerTotal();
});

function getLedgerTotal(){
    var total_debit_amt = 0;
    var total_credit_amt = 0;
    var paying_debit_amt = 0;
    var paying_credit_amt = 0;
    var net_debit_amt = 0;
    var net_credit_amt = 0;

    $(".check").each(function( index ) {
        var id = $(this).attr("id");
        var index = id.substring(id.indexOf("_")+1);
        // console.log(index);

        var debit = get_number($("#debit_amt_"+index).val(),2);
        var credit = get_number($("#credit_amt_"+index).val(),2);

        total_debit_amt = total_debit_amt + debit;
        total_credit_amt = total_credit_amt + credit;

        if($(this).prop('checked')==true) {
            paying_debit_amt = paying_debit_amt + debit;
            paying_credit_amt = paying_credit_amt + credit;
            $("#chk_val_"+index).val("1");
        } else {
            $("#chk_val_"+index).val("0");
        }
    });

    net_debit_amt = total_debit_amt - paying_debit_amt;
    net_credit_amt = total_credit_amt - paying_credit_amt;

    $("#total_debit_amt").val(format_money(total_debit_amt,2));
    $("#total_credit_amt").val(format_money(total_credit_amt,2));
    $("#paying_debit_amt").val(format_money(paying_debit_amt,2));
    $("#paying_credit_amt").val(format_money(paying_credit_amt,2));
    $("#net_debit_amt").val(format_money(net_debit_amt,2));
    $("#net_credit_amt").val(format_money(net_credit_amt,2));
}