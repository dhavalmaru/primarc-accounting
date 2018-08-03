$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$(document).ready(function(){
	$('.select2').select2();
    addMultiInputNamingRules('#promotion', 'select[name="acc_id[]"]', { required: true });
    addMultiInputNamingRules('#promotion', 'input[name="acc_code[]"]', { required: true });
    addMultiInputNamingRules('#promotion', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#promotion', 'input[name="debit_amt[]"]', { required: true });
    addMultiInputNamingRules('#promotion', 'input[name="credit_amt[]"]', { required: true });

    get_promo_codes();
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
    // var $tableBody = $('#promotion').find("tbody"),
    // $trLast = $tableBody.find("tr:last"),
    // $trNew = $trLast.clone();

    // var id = $trNew.attr('id');
    // var index = id.substr(id.lastIndexOf('_')+1);
    // var newIndex = parseInt(index) + 1;
    // $trNew.attr('id', 'row_'+newIndex);
    // $trNew.find('#delete_row_'+index).attr('id', 'delete_row_'+newIndex).val("");
    // $trNew.find('#sr_no_'+index).attr('id', 'sr_no_'+newIndex).html(newIndex+1);
    // $trNew.find('#entry_id_'+index).attr('id', 'entry_id_'+newIndex).val("");
    // // $trNew.find('#acc_id_'+index).attr('id', 'acc_id_'+newIndex).val("");
    // $trNew.find('#acc_id_'+index).attr('id', 'acc_id_'+newIndex).html(acc_details);
    // $trNew.find('#legal_name_'+index).attr('id', 'legal_name_'+newIndex).val("");
    // $trNew.find('#acc_code_'+index).attr('id', 'acc_code_'+newIndex).val("");
    // $trNew.find('#trans_'+index).attr('id', 'trans_'+newIndex).val("");
    // $trNew.find('#debit_amt_'+index).attr('id', 'debit_amt_'+newIndex).val("");
    // $trNew.find('#credit_amt_'+index).attr('id', 'credit_amt_'+newIndex).val("");

    // $trLast.after($trNew);

    var counter = $('.debit_amt').length;
    var newRow = jQuery('<tr id="row_'+counter+'">' + 
                            '<td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_row_'+counter+'" onClick="delete_row(this);">-</button></td>' + 
                            '<td  style="text-align: center; display: none;" id="sr_no_'+counter+'">'+(counter+1)+'</td>' + 
                            '<td>' + 
                                '<input class="form-control" type="hidden" name="entry_id[]" id="entry_id_'+counter+'" value="" />' + 
                                '<select class="form-control select2" name="acc_id[]" id="acc_id_'+counter+'" onchange="get_acc_details(this);">' + 
                                    acc_details + 
                                '</select>' + 
                                '<input class="form-control" type="hidden" name="legal_name[]" id="legal_name_'+counter+'" value="" />' + 
                            '</td>' + 
                            '<td><input class="form-control" type="text" name="acc_code[]" id="acc_code_'+counter+'" value="" readonly /></td>' + 
                            '<td>' + 
                                '<select class="form-control select2" name="transaction[]" id="trans_'+counter+'" onchange="set_transaction(this);">' + 
                                    '<option value="">Select</option>' + 
                                    '<option value="Debit">Debit</option>' + 
                                    '<option value="Credit">Credit</option>' + 
                                '</select>' + 
                            '</td>' + 
                            '<td><input class="form-control debit_amt" type="text" name="debit_amt[]" id="debit_amt_'+counter+'" value="" onChange="get_total();" /></td>' + 
                            '<td><input class="form-control credit_amt" type="text" name="credit_amt[]" id="credit_amt_'+counter+'" value="" onChange="get_total();" /></td>' + 
                        '</tr>');
    var $tableBody = $('#acc_promotion_details').find("tbody");
    $trLast = $tableBody.find("tr:last");
    $trLast.after(newRow);
    $('.select2').select2();
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
        url: BASE_URL+'index.php?r=promotion%2Fgetaccdetails',
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
    // console.log('Hiii');
    var debit_amt = 0;
    var credit_amt = 0;
    $('.debit_amt').each(function() {
        debit_amt = debit_amt + get_number(this.value,2);
    });
    $('.credit_amt').each(function() {
        credit_amt = credit_amt + get_number(this.value,2);
    });

    // jQuery('.debit_amt').each(function() {
    //     var percent = jQuery(this).text();
    //     debit_amt = debit_amt + get_number(percent,2);
    // });
    // jQuery('.credit_amt').each(function() {
    //     var percent = jQuery(this).text();
    //     credit_amt = credit_amt + get_number(percent,2);
    // });

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

function get_promo_types(){
    var vendor_id = $('#vendor_id').val();
    var debit_note_ref = $('#debit_note_ref').val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
        url: BASE_URL+'index.php?r=promotion%2Fgetpromotypes',
        type: 'post',
        data: {
                vendor_id : vendor_id,
                debit_note_ref : debit_note_ref,
                _csrf : csrfToken
             },
        dataType: 'json',
        success: function (data) {
            if(data != null){
                $("#promotion_type").html(data.promotion_type);
                $("#promotion_code").html("");
            } else {
                $("#promotion_type").html("");
                $("#promotion_code").html("");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}

function get_promo_codes(){
    var id = $('#id').val();
    var vendor_id = $('#vendor_id').val();
    var promotion_type = $('#promotion_type').val();
    var debit_note_ref = $('#debit_note_ref').val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
        url: BASE_URL+'index.php?r=promotion%2Fgetpromocodes',
        type: 'post',
        data: {
                id : id,
                vendor_id : vendor_id,
                promotion_type : promotion_type,
                debit_note_ref : debit_note_ref,
                _csrf : csrfToken
             },
        dataType: 'json',
        success: function (data) {
            if(data != null){
                $("#promotion_code").html(data.promotion_code);
            } else {
                $("#promotion_code").html("");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}

function set_trans_type(){
    var trans_type = $('#trans_type').val();
    
    if(trans_type=="Debit Note") {
        $('#warehouse_gst_div').hide();
    } else {
        $('#warehouse_gst_div').show();
    }
}

$("#btn_get_details").click(function(){
    var vendor_id = $('#vendor_id').val();
    var promotion_type = $('#promotion_type').val();
    var promotion_code = $('#promotion_code').val();
    var debit_note_ref = $('#debit_note_ref').val();
    var trans_type = $('#trans_type').val();
    var warehouse_id = $('#warehouse_id').val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
        url: BASE_URL+'index.php?r=promotion%2Fgetdetails',
        type: 'post',
        data: {
                vendor_id : vendor_id,
                promotion_type : promotion_type,
                promotion_code : promotion_code,
                debit_note_ref : debit_note_ref,
                trans_type : trans_type,
                warehouse_id : warehouse_id,
                _csrf : csrfToken
             },
        dataType: 'json',
        success: function (data) {
            // console.log(data.result);
            $('#acc_promotion_details tbody').html(data.result);
            get_total();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
});