$('body').on('focus',".invoice_date", function(){
    $(this).datepicker({dateFormat: "dd/mm/yy",changeMonth: true,changeYear: true});
    $('.datepicker').css({"z-index":"99999 !important"})
});

$(document).ready(function(){
    $('.datepicker').datepicker({dateFormat: "dd/mm/yy",changeMonth: true,changeYear: true});
    set_tr();

    /*    $('#jv_modal').on('shown.bs.modal', function() {
            alert('ho zhala');
          $('.invoice_date').datepicker({
                format: "dd/mm/yyyy",
                startDate: "01-01-2015",
                endDate: "01-01-2020",
                todayBtn: "linked",
                autoclose: true,
                todayHighlight: true,
                container: '#jv_modal modal-body'
              });
         });
    */

    $('.select2').select2();
  
    addMultiInputNamingRules('#journal_voucher', 'select[name="acc_id[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="acc_code[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="debit_amt[]"]', { required: true });
    addMultiInputNamingRules('#journal_voucher', 'input[name="credit_amt[]"]', { required: true });

    set_view();
});

function set_tr() {
    var setflag = 0;
   $('.viewjv').each(function(){
        var getval = $(this).attr('style');
        if(getval!='display:none')
        {
            //alert('entered');
            setflag=1;
        }
   });

   // alert(setflag);
   if(setflag==0)
   {
     $('.viewjvtd').hide();
   }
}

function set_view(){
    if($('#action').val()=='view' || $('#status').val()=='approved'){
        $('#repeat_row').hide();
        $('#jv_doc tfoot').hide();
        $('.action_delete').hide();

        $('#btn_submit').hide();
        $('#btn_reject').hide();

        $("input").attr("disabled", true);
        $("select").attr("disabled", true);
        $("textarea").attr("disabled", true);
    } else if($('#action').val()=='insert' || $('#action').val()=='edit'){
        $('.action_delete').show();
        
        $('#btn_submit').val("Submit For Approval");
        $('#btn_submit').show();
        $('#btn_reject').hide();
    } else if($('#action').val()=='authorise'){
        $('#repeat_row').hide();
        $('#jv_doc tfoot').hide();
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
    // var $tableBody = $('#acc_jv_details').find("tbody"),
    // $trLast = $tableBody.find("tr:last"),
    // $trNew = $trLast.clone();

    // var id = $trNew.attr('id');
    // var index = id.substr(id.lastIndexOf('_')+1);
    // var newIndex = parseInt(index) + 1;
    // $trNew.attr('id', 'row_'+newIndex);
    // $trNew.find('#delete_row_'+index).attr('id', 'delete_row_'+newIndex).val("");
    // $trNew.find('#sr_no_'+index).attr('id', 'sr_no_'+newIndex).html(newIndex+1);
    // $trNew.find('#entry_id_'+index).attr('id', 'entry_id_'+newIndex).val("");
    // $trNew.find('#acc_id_'+index).attr('id', 'acc_id_'+newIndex).val("");
    // $trNew.find('#legal_name_'+index).attr('id', 'legal_name_'+newIndex).val("");
    // $trNew.find('#acc_code_'+index).attr('id', 'acc_code_'+newIndex).val("");
    // $trNew.find('#trans_'+index).attr('id', 'trans_'+newIndex).val("");
    // $trNew.find('#debit_amt_'+index).attr('id', 'debit_amt_'+newIndex).val("");
    // $trNew.find('#credit_amt_'+index).attr('id', 'credit_amt_'+newIndex).val("");

    // $trLast.after($trNew);

    var counter = $('.debit_amt').length;
    var newRow = jQuery('<tr id="row_'+counter+'" class="voucher">' + 
                            '<input class="form-control" type="hidden" name="entry_value[]" id="entry_value_'+counter+'" value="'+counter+'" /><td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_row_'+counter+'" onClick="delete_row(this);">-</button></td>' + 
                            '<td  style="text-align: center; display:none" id="sr_no_'+counter+'">'+(counter+1)+'</td>' + 
                            '<td>' + 
                                '<input class="form-control" type="hidden" name="entry_id[]" id="entry_id_'+counter+'" value="" />' + 
                                '<select class="form-control acc_detail select2" name="acc_id[]" id="acc_id_'+counter+'" onchange="get_acc_details(this);">' + 
                                    acc_details + 
                                '</select>' + 
                                '<input class="form-control" type="hidden" name="legal_name[]" id="legal_name_'+counter+'" value="" />' + 
                            '</td>' + 
                            '<td><input class="form-control" type="hidden" name="bill_wise[]" id="bill_wise_'+counter+'" value="" readonly /><input class="form-control" type="text" name="acc_code[]" id="acc_code_'+counter+'" value="" readonly /></td>' + 
                            '<td>' + 
                                '<select class="form-control select2" name="transaction[]" id="trans_'+counter+'" onchange="set_transaction(this);">' + 
                                    '<option value="">Select</option>' + 
                                    '<option value="Debit">Debit</option>' + 
                                    '<option value="Credit">Credit</option>' + 
                                '</select>' + 
                            '</td>' + 
                            '<td><input class="form-control debit_amt" type="text" name="debit_amt[]" id="debit_amt_'+counter+'" value="" onChange="get_total();" /></td>' + 
                            '<td><input class="form-control credit_amt" type="text" name="credit_amt[]" id="credit_amt_'+counter+'" value="" onChange="get_total();" /></td>' + 
                            '<td width="100"><a href="javascript:void(0)" class="btn btn-primary btn-sm pull-right viewjv" id="'+counter+'" onClick="jv_invoices(this)" style="display:none">View</a></td>'+
                        '</tr>');
    $('#acc_jv_details').append(newRow);
    $('.select2').select2();
})

function delete_row(elem){
    var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);

    if(index>1){
        $('#row_'+index).remove();
        $('#jv_'+index).remove();
        get_total();
    }
}

$("#repeat_doc").click(function(){
    var $tableBody = $('#jv_doc').find("tbody"),
    $trLast = $tableBody.find("tr:last"),
    $trNew = $trLast.clone();

    var id = $trNew.attr('id');
    var index = id.substr(id.lastIndexOf('_')+1);
    var newIndex = parseInt(index) + 1;

    $trNew.attr('id', 'jv_doc_'+newIndex);
    $trNew.find('#delete_jv_doc_'+index).attr('id', 'delete_jv_doc_'+newIndex).val("");
    $trNew.find('#doc_file_'+index).attr('id', 'doc_file_'+newIndex).attr('name', 'doc_file_'+newIndex).attr('data-error', 'doc_file_'+newIndex+'_error').val("");
    $trNew.find('#doc_file_'+index+'_error').attr('id', 'doc_file_'+newIndex+'_error').val("");
    $trNew.find('#doc_file_'+index+'_download').remove();
    $trNew.find('#description_'+index).attr('id', 'description_'+newIndex).val("");

    
    $trLast.after($trNew);
    $('#delete_jv_doc_'+newIndex).show();
})

function delete_jv_doc(elem){
    /*var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);

    if(index!=0){
        $('#jv_doc_'+index).remove();
    }*/
    
    var id = elem.id;
    var index = id.substr(id.lastIndexOf('-')+1);
    var id_index = id.substr(id.lastIndexOf('_')+1);

    var bodyrow = $(elem).parent().parent().attr('id');
    // console.log(bodyrow);
    var div_index = bodyrow.substr(bodyrow.lastIndexOf('_')+1);
    $('#'+bodyrow+' .tr').each(function() {
        if($(this).find('.counter').html()!=undefined) {
            if(parseInt($(this).find('.counter').html())>index) {
                var counter = parseInt($(this).find('.counter').html())-1;
                $(this).find('.counter').html(counter);
                $(this).find('.delete_row').attr('id','delete_row_'+div_index+'-'+counter);
                $(this).find('.invoice_no').attr('id','invoice_no_'+div_index+'-'+counter);
                $(this).find('.invoice_date').attr('id','invoice_date_'+div_index+'-'+counter);
                $(this).find('.invoice_amount').attr('id','invoice_amount_'+div_index+'-'+counter);
            }
        }
    });

    $(elem).parent().parent().remove();
    jvcalculation(div_index);

    /*if(index<1){
        $('#row_'+index).remove();
    }*/
}

function add_billwise(element) {
    var id = element.id;
    var index = id.substr(id.lastIndexOf('_')+1);
    var newIndex = parseInt(index) + 1;
    var tbody_id = $('#'+id).parent().parent().parent().attr('id'); 
    var sr_no = $('#'+tbody_id+' .tr').length;
    // console.log('sr_no'+sr_no);
    var counter = sr_no+1;
    remaining_amount = jvcalculation(index);
    var html = '<tr class="tr"><td class="counter">'+counter+'</td>'+
    '<td><button type="button" class="btn btn-sm btn-success delete_row" id="delete_row_'+index+'-'+counter+'" onClick="delete_billwise(this)">-</button></td>'+
    '<td><input class="form-control invoice_no datepicker" type="text" name="invoice_no_'+index+'[]"  id="invoice_no_'+index+'-'+counter+'"  value="" >'+
    '<td><input class="form-control  invoice_date" type="text" name="invoice_date_'+index+'[]" id="invoice_date_'+index+'-'+counter+'" " value="">'+
    '<td><input class="form-control invoice_amount" type="text" name="invoice_amount_'+index+'[]"  id="invoice_amount_'+index+'-'+counter+'" value="'+remaining_amount+'" onchange="checkamount(this)"></td>'+
    '</tr>';
    //$('#'+tbody_id).append(html);
    $('#'+tbody_id+' .table_foot').before(html);
    jvcalculation(index);
    /*$('#invoice_date_'+index+'-'+counter).datepicker();*/
}

function delete_billwise(elem){
    /*var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);

    if(index!=0){
        $('#jv_doc_'+index).remove();
    }*/
    
    var id = elem.id;
    var index = id.substr(id.lastIndexOf('-')+1);
    var id_index = id.substr(id.lastIndexOf('_')+1);

    var bodyrow = $(elem).parent().parent().parent().attr('id');
    // console.log(bodyrow);
    var div_index = bodyrow.substr(bodyrow.lastIndexOf('_')+1);
    $('#'+bodyrow+' .tr').each(function() {
        if($(this).find('.counter').html()!=undefined) {
            if(parseInt($(this).find('.counter').html())>index) {
                var counter = parseInt($(this).find('.counter').html())-1;
                $(this).find('.counter').html(counter);
                $(this).find('.delete_row').attr('id','delete_row_'+div_index+'-'+counter);
                $(this).find('.invoice_no').attr('id','invoice_no_'+div_index+'-'+counter);
                $(this).find('.invoice_date').attr('id','invoice_date_'+div_index+'-'+counter);
                $(this).find('.invoice_amount').attr('id','invoice_amount_'+div_index+'-'+counter);
            }
        }
    });

    $(elem).parent().parent().remove();
    jvcalculation(div_index);

    /*if(index<1){
        $('#row_'+index).remove();
    }*/
}

function checkamount(elem){
    /* var length = $("#jv_"+val+' .invoice_amount').length;
    var last_invoice_amount = $("#invoice_amount_"+val+'-'+length).val();*/
    // detect = jvcalculation(val);

    var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);
    index = index.substr(0, index.indexOf('-'));

    var total_amount = 0;
    $('#jv_'+index+' .invoice_amount').each(function(){
        if($(this).val()!="") {
            total_amount = total_amount+parseFloat($(this).val());
        }
    });

    var amount = 0;
    var remaining_amount = 0;
    if($('#debit_amt_'+index).val()!="" && parseInt($('#debit_amt_'+index).val())!=0) {
        amount  = get_number($('#debit_amt_'+index).val(),2);
    } else {
        amount  = get_number($('#credit_amt_'+index).val(),2);
    }

    remaining_amount = amount-total_amount;

    var new_id = '';
    $('#jv_'+index+' .invoice_amount').each(function(){
        if($(this).val()!="" && $(this).attr('id')!=id) {
            new_id = $(this).attr('id');
        }
    });

    amount = parseFloat($('#'+new_id).val());
    $('#'+new_id).val(amount+remaining_amount);
}

function get_acc_details(elem){
    var acc_id = elem.value;
    if(acc_id!="")
    {
        var id = elem.id;
        var index = id.substr(id.lastIndexOf('_')+1);
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        var flag = 0;
        $('.errors').remove();
        var count
        $('.acc_detail').each(function(){
           
           if($(this).val()==acc_id)
           {
                var attr_id = $(this).attr('id');
                if(attr_id!=id)
                {
                  flag=1;  
                }
           }
        });
        $('#'+index).hide();
        $('#jv_'+index).remove();
        if(flag==1)
        {
            $('#'+id).val(null).trigger('change');
            $('#legal_name_'+index).after('<div class="errors" style="color: #dd4b39!important;"><br>Same Account Cannot Be Selected</div>');
        }
        else
        {
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
                            // console.log('bill_wise'+data[0].bill_wise);
                            if(data[0].bill_wise==1)
                            {
                                $("#bill_wise_"+index).val(data[0].bill_wise);
                                //$("#row_"+index).append('<td width="100"><a href="javascript:void(0)" class="btn btn-primary btn-sm pull-right viewjv" id="'+index+'" onClick="jv_invoices(this)">View</a></td>');/*alert('entered');*/
                                $('.viewjvtd').show();
                                $('#'+index).show();
                                var html ='<tbody id="jv_'+index+'" style="display:none" class="jv_body_detail">'+
                                '<tr class="tr"><td>1</td>'+
                                '<td>&nbsp</td>'+
                                '<td><input class="form-control invoice_no" type="text" name="invoice_no_'+index+'[]"  id="invoice_no_'+index+'-0"value="" >'+
                                '<td><input class="form-control invoice_date datepicker" type="text" name="invoice_date_'+index+'[]"  id="invoice_date_'+index+'-0" value="">'+
                                '<td><input class="form-control invoice_amount" type="text" name="invoice_amount_'+index+'[]" id="invoice_amount_'+index+'-0" value=""  onchange="checkamount(this)"></td>'+
                                '</tr>'+
                                '<tr class="table_foot">'+
                                '<td><button type="button" class="btn btn-sm btn-success" id="jv_repeat_row_'+index+'" onclick="add_billwise(this)">+</button></td>'+            
                                '<td></td>'+  
                                '<td></td>'+  
                                '<td></td>'+  
                                '<td><span id="sum_total_'+index+'"></span></td>'+  
                                '</tr>'+
                                '</tbody>';
                                $("#jv_details").append(html);
                                
                                /*$('#invoice_no_'+index+'-0').datepicker();*/
                            }
                        }
                        
                    } else {
                        $("#acc_code_"+index).val("");
                    }

                    /*set_tr();*/
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
          }); 
        } 
    }
}

function jv_invoices(elment) {
    //$(this).val();
    var id =elment.id;
   
    var amount = 0;
    if($('#debit_amt_'+id).val()!="" && parseInt($('#debit_amt_'+id).val())!=0) {
        amount  = $('#debit_amt_'+id).val();
    } else if ($('#credit_amt_'+id).val()!="" &&  parseInt($('#credit_amt_'+id).val())!=0) {
        amount  = $('#credit_amt_'+id).val();
    }

    $('.jv_body_detail').each(function(){
        var attr_id = $(this).attr('id');
       
        var elem_id = attr_id.substr(attr_id.lastIndexOf('_')+1);
        if(id==elem_id)
        {
            $('#jv_'+id).show();
        }
        else
        {
            $('#jv_'+elem_id).hide();
        }
    });

    if(($('#debit_amt_'+id).val()!="" && parseInt($('#debit_amt_'+id).val())!=0) ||  ($('#credit_amt_'+id).val()!="" && parseInt($('#credit_amt_'+id).val())!=0)) {
        $('.errors').remove();
        if($('#invoice_amount_'+id+'-0').val()=="") {
            $('#invoice_amount_'+id+'-0').val(amount);
        }
        
        $('#jv_modal').modal('show');
    } else {
        $('.errors').remove();
        $(elment).after('<div class="errors" style="color: #dd4b39!important;"><br>Enter Debit/Credit Amount</div>');
    }

    jvcalculation(id);
}

function jvcalculation(val) {
    var total_amount = 0;
    var detect = '';
    $('#jv_'+val+' .invoice_amount').each(function(){
        if($(this).val()!="")
        {
            total_amount = total_amount+parseFloat($(this).val());
        }
        
    });
    $('#sum_total_'+val).text(total_amount);
    var amount = 0;
    var remaining_amount = 0;
    if($('#debit_amt_'+val).val()!="" && parseInt($('#debit_amt_'+val).val())!=0) {
        amount  = get_number($('#debit_amt_'+val).val(),2);
    } else {
        amount  = get_number($('#credit_amt_'+val).val(),2);
    }

    if(total_amount>amount)
    {
        detect = 'Greater';
        //alert('Total Invoice Amount Cannot be greater then Actual Amount');
    }
    else if(total_amount<amount)
    {   
        detect = 'Less';
        remaining_amount = amount-total_amount;
        /*$("#jv_repeat_row_"+val).click();
        $("#invoice_amount_"+val+'-'+(length+1)).val(remaining_amount);*/
        //alert('Total Invoice Amount Cannot be Less then Actual Amount');
    }

    return remaining_amount;
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