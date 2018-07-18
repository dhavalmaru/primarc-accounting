$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$(document).ready(function(){
    addMultiInputNamingRules('#go_debit_details', 'select[name="acc_id[]"]', { required: true });
    addMultiInputNamingRules('#go_debit_details', 'input[name="acc_code[]"]', { required: true });
    addMultiInputNamingRules('#go_debit_details', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#go_debit_details', 'input[name="debit_amt[]"]', { required: true });
    addMultiInputNamingRules('#go_debit_details', 'input[name="credit_amt[]"]', { required: true });
	$('.select2').select2();
    set_view();
})

function set_view(){
    if($('#action').val()=='view'){
        // $('#repeat_row').hide();
        // $('#jv_doc tfoot').hide();
        // $('.action_delete').hide();

        $('#btn_submit').hide();
        // $('#btn_reject').hide();

        $("input").attr("disabled", true);
        $("select").attr("disabled", true);
        $("textarea").attr("disabled", true);
    } else if($('#action').val()=='insert' || $('#action').val()=='edit'){
        // $('.action_delete').show();
        
        $('#btn_submit').val("Submit");
        // $('#btn_submit').val("Submit For Approval");
        $('#btn_submit').show();
        // $('#btn_reject').hide();
    } else if($('#action').val()=='authorise'){
        // $('#repeat_row').hide();
        // $('#jv_doc tfoot').hide();
        // $('.action_delete').hide();

        $("input[type!='hidden']").attr("disabled", true);
        $("select").attr("disabled", true);
        $("textarea").attr("disabled", true);

        $('#btn_submit').val("Approve");
        $('#btn_submit').show();
        // $('#btn_reject').show();

        $('#remarks').attr("disabled", false);
        $('#btn_submit').attr("disabled", false);
        // $('#btn_reject').attr("disabled", false);
    }
}

function get_acc_details(elem){
	var acc_id = elem.value;
    var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

	$.ajax({
        url: BASE_URL+'index.php?r=goodsoutward%2Fgetaccdetails',
        type: 'post',
        data: {
                acc_id : acc_id,
                _csrf : csrfToken
             },
        dataType: 'json',
        success: function (data) {
            if(data != null){
                if(data.length>0){
                    $("#acc_type_"+index).val(data[0].type);
                    $("#ledger_code_"+index).val(data[0].code);
                    $("#ledger_name_"+index).val(data[0].legal_name);
                }
                
            } else {
                $("#ledger_code_"+index).val("");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}

function get_total(){
    var debit_amt = 0;
    var credit_amt = 0;
    jQuery('.debit_amt').each(function() {
        debit_amt = debit_amt + get_number(this.value,2);
    });
    jQuery('.credit_amt').each(function() {
        credit_amt = credit_amt + get_number(this.value,2);
    });
    $('#total_debit_amt').val(format_money(debit_amt,2));
    $('#total_credit_amt').val(format_money(credit_amt,2));
    var diff_amt = debit_amt - credit_amt;
    $('#diff_amt').val(format_money(diff_amt,2));
}

function set_transaction(elem){
    var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);
    
    if(elem.value=="Debit") {
        $('#debit_amt_'+index).attr('readonly', false);
        $('#credit_amt_'+index).val('0.00');
        $('#credit_amt_'+index).attr('readonly', true);
    } else {
        $('#credit_amt_'+index).attr('readonly', false);
        $('#debit_amt_'+index).val('0.00');
        $('#debit_amt_'+index).attr('readonly', true);
    }
}