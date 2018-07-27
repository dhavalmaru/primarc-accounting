$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$(document).ready(function(){
	$('.select2').select2();
    addMultiInputNamingRules('#other_debit_credit', 'select[name="acc_id[]"]', { required: true });
    addMultiInputNamingRules('#other_debit_credit', 'input[name="acc_code[]"]', { required: true });
    addMultiInputNamingRules('#other_debit_credit', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#other_debit_credit', 'input[name="debit_amt[]"]', { required: true });
    addMultiInputNamingRules('#other_debit_credit', 'input[name="credit_amt[]"]', { required: true });

    set_view();
})

function set_view(){
    if($('#action').val()=='view'){
        $('#repeat_row').hide();
        $('.action_delete').hide();

        $('#btn_submit').hide();
        $('#btn_reject').hide();

        $("input").attr("disabled", true);
        $("select").attr("disabled", true);
        $("textarea").attr("disabled", true);
    } else if($('#action').val()=='insert' || $('#action').val()=='edit'){
        $('.action_delete').show();
        
        $('#btn_submit').val("Submit");
        // $('#btn_submit').val("Submit For Approval");
        $('#btn_submit').show();
        // $('#btn_reject').hide();
    } else if($('#action').val()=='authorise'){
        $('#repeat_row').hide();
        $('.action_delete').hide();

        $("input[type!='hidden']").attr("disabled", true);
        $("select").attr("disabled", true);
        $("textarea").attr("disabled", true);

        $('#btn_submit').val("Approve");
        $('#btn_submit').show();
        $('#btn_reject').show();

        $('#remarks').attr("disabled", false);
        $('#btn_submit').attr("disabled", false);
        $('#btn_reject').attr("disabled", false);
    }
}

$("#repeat_row").click(function(){
	var $tableBody = $('#other_debit_credit').find("tbody"),
	$trLast = $tableBody.find("tr:last"),
	$trNew = $trLast.clone();

	var id = $trNew.attr('id');
	var index = id.substr(id.lastIndexOf('_')+1);
	var newIndex = parseInt(index) + 1;
	$trNew.attr('id', 'row_'+newIndex);
    $trNew.find('#delete_row_'+index).attr('id', 'delete_row_'+newIndex).val("");
	$trNew.find('#sr_no_'+index).attr('id', 'sr_no_'+newIndex).html(newIndex+1);
    $trNew.find('#entry_id_'+index).attr('id', 'entry_id_'+newIndex).val("");
	$trNew.find('#acc_id_'+index).attr('id', 'acc_id_'+newIndex).val("");
	$trNew.find('#legal_name_'+index).attr('id', 'legal_name_'+newIndex).val("");
	$trNew.find('#acc_code_'+index).attr('id', 'acc_code_'+newIndex).val("");
	$trNew.find('#trans_'+index).attr('id', 'trans_'+newIndex).val("");
	$trNew.find('#debit_amt_'+index).attr('id', 'debit_amt_'+newIndex).val("");
	$trNew.find('#credit_amt_'+index).attr('id', 'credit_amt_'+newIndex).val("");

	$trLast.after($trNew);
})

function delete_row(elem){
    var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);

    if(index>1){
        $('#row_'+index).remove();

        get_total();
    }
}

function get_acc_details(elem){
	var acc_id = elem.value;
    var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

	$.ajax({
        url: BASE_URL+'index.php?r=journalvoucher%2Fgetaccdetails',
        type: 'post',
        data: {
                acc_id : acc_id,
                _csrf : csrfToken
             },
        dataType: 'json',
        success: function (data) {
            if(data != null){
                if(data.length>0){
                    $("#acc_code_"+index).val(data[0].code);
                    $("#legal_name_"+index).val(data[0].legal_name);
                }
                
            } else {
                $("#acc_code_"+index).val("");
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

    get_total();
}

function set_trans_type(){
    var trans_type = $('#trans_type').val();
    
    if(trans_type=="Invoice") {
        $('#warehouse_gst_div').show();
    } else {
        $('#warehouse_gst_div').hide();
    }
}
