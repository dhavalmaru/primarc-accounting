$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$(document).ready(function(){
    set_acc_type();

	addMultiInputNamingRules('#acc_category_master', 'input[name="category_1[]"]', { required: true });
    // addMultiInputNamingRules('#acc_category_master', 'input[name="category_2[]"]', { required: true });
    // addMultiInputNamingRules('#acc_category_master', 'input[name="category_3[]"]', { required: true });

    get_categories();
    set_view();
})

function set_view(){
    if($('#action').val()=='view' || $('#status').val()=='approved'){
        $('#add_category_div').hide();
        $('#business_category tfoot').hide();
        $('#btn_submit').hide();
        $('#btn_reject').hide();
        $('.action_delete').hide();

        $("input").attr("disabled", true);
        $("select").attr("disabled", true);
        $("textarea").attr("disabled", true);
    } else if($('#action').val()=='insert' || $('#action').val()=='edit'){
        $('#btn_submit').val("Submit For Approval");
        $('#btn_submit').show();
        $('#btn_reject').hide();
        $('.action_delete').show();
    } else if($('#action').val()=='authorise'){
        $('#add_category_div').hide();
        $('#business_category tfoot').hide();

        $("input[type!='hidden']").attr("disabled", true);
        $("select").attr("disabled", true);
        $("textarea").attr("disabled", true);

        $('#btn_submit').val("Approve");
        $('#btn_submit').show();
        $('#btn_reject').show();
        $('.action_delete').hide();

        $('#remarks').attr("disabled", false);
        $('#btn_submit').attr("disabled", false);
        $('#btn_reject').attr("disabled", false);
    }
}

function delete_row(elem){
    var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);

    if(index!=0){
        $('#cat_row_'+index).remove();
    }
}

$("#type").change(function(){
    set_acc_type();

    $("#details").val("");
    $("#vendor_id").val("");
    $("#legal_name").val("");
    $("#code").val("");
    $("#vendor_code").val("");
    $("#pan_no").val("");
    $("#address").val("");
    $("#legal_entity_name").val("");
    $("#vat_no").val("");
    $("#account_type").val("");
    $("#account_holder_name").val("");
    $("#bank_name").val("");
    $("#branch").val("");
    $("#acc_no").val("");
    $("#ifsc_code").val("");
    $("#category_1").val("");
    $("#category_2").val("");
    $("#category_3").val("");

    $("#type_val").val($("#type").val());
    get_code();
})

$("#vendor_id").change(function(){
    $("#legal_name").val($("#vendor_id option:selected").text());
    get_code();
});

function set_acc_type(){
    if($("#type").val()=="Vendor Goods"){
        $("#vendor_id").show();
        $("#legal_name").hide();
        $("#vendor_code").show();
        $("#code").hide();
        $(".vendor_expenses").hide();
        $(".employee").hide();
        $(".vendor_goods").show();
        // $("#vendor_details").show();
        // $("#acc_hold_name").show();
        // $("#bank_details").show();
        // $("#business_category_label").show();
        // $("#category_details").show();
        // $("#account_holder_name").attr('readonly',true);
        // $("#bank_name").attr('readonly',true);
        // $("#branch").attr('readonly',true);
        // $("#acc_no").attr('readonly',true);
        // $("#ifsc_code").attr('readonly',true);
    } else if($("#type").val()=="Vendor Expenses"){
        $("#vendor_id").hide();
        $("#legal_name").show();
        $("#vendor_code").hide();
        $("#code").show();
        $(".vendor_goods").hide();
        $(".employee").hide();
        $(".vendor_expenses").show();
        // $("#vendor_details").hide();
        // $("#acc_hold_name").show();
        // $("#bank_details").show();
        // $("#business_category_label").hide();
        // $("#category_details").hide();
        // $("#account_holder_name").attr('readonly',false);
        // $("#bank_name").attr('readonly',false);
        // $("#branch").attr('readonly',false);
        // $("#acc_no").attr('readonly',false);
        // $("#ifsc_code").attr('readonly',false);
    } else if($("#type").val()=="Bank Account"){
        $("#vendor_id").hide();
        $("#legal_name").show();
        $("#vendor_code").hide();
        $("#code").show();
        $(".vendor_goods").hide();
        $(".vendor_expenses").hide();
        $(".employee").hide();
        $(".bank_account").show();
        // $("#vendor_details").hide();
        // $("#acc_hold_name").show();
        // $("#bank_details").show();
        // $("#business_category_label").hide();
        // $("#category_details").hide();
        // $("#account_holder_name").attr('readonly',false);
        // $("#bank_name").attr('readonly',false);
        // $("#branch").attr('readonly',false);
        // $("#acc_no").attr('readonly',false);
        // $("#ifsc_code").attr('readonly',false);
    } else if($("#type").val()=="Employee"){
        $("#vendor_id").hide();
        $("#legal_name").show();
        $("#vendor_code").hide();
        $("#code").show();
        $(".vendor_goods").hide();
        $(".vendor_expenses").hide();
        $(".bank_account").hide();
        $(".employee").show();
        // $("#vendor_details").hide();
        // $("#acc_hold_name").show();
        // $("#bank_details").show();
        // $("#business_category_label").hide();
        // $("#category_details").hide();
        // $("#account_holder_name").attr('readonly',false);
        // $("#bank_name").attr('readonly',false);
        // $("#branch").attr('readonly',false);
        // $("#acc_no").attr('readonly',false);
        // $("#ifsc_code").attr('readonly',false);
    } else {
        $("#vendor_id").hide();
        $("#legal_name").show();
        $("#vendor_code").hide();
        $("#code").show();
        $(".vendor_goods").hide();
        $(".vendor_expenses").hide();
        $(".bank_account").hide();
        $(".employee").hide();
        // $("#vendor_details").hide();
        // $("#acc_hold_name").hide();
        // $("#bank_details").hide();
        // $("#business_category_label").hide();
        // $("#category_details").hide();
        // $("#account_holder_name").attr('readonly',false);
        // $("#bank_name").attr('readonly',false);
        // $("#branch").attr('readonly',false);
        // $("#acc_no").attr('readonly',false);
        // $("#ifsc_code").attr('readonly',false);
    }
}

function get_code(){
    var result = 1;
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    
    if($("#type").val()=="Vendor Goods"){
        $.ajax({
            url: BASE_URL+'index.php?r=accountmaster%2Fgetvendordetails',
            type: 'post',
            data: {
                    vendor_id : $("#vendor_id").val(),
                    _csrf : csrfToken
                },
            dataType: 'json',
            success: function (data) {
                if(data != null){
                    var vendor_details = data['vendor_details'];
                    var category_details = data['category_details'];

                    if(vendor_details.length>0){
                        $("#vendor_code").val(vendor_details[0].vendor_code);
                        $("#pan_no").val(vendor_details[0].pan_or_tin_no);

                        var address = vendor_details[0].office_address_line_1 + ' ' + vendor_details[0].office_address_line_2 + ' ' + 
                                        vendor_details[0].office_address_line_3 + ' ' + vendor_details[0].city_name + ' ' + vendor_details[0].pincode + ' ' + 
                                        vendor_details[0].state_name + ' ' + vendor_details[0].country_name;

                        $("#address").val(address);
                        $("#legal_entity_name").val(vendor_details[0].legal_entity_name);
                        $("#vat_no").val(vendor_details[0].vat_no);
                        $("#account_holder_name").val(vendor_details[0].account_holder_name);
                        $("#bank_name").val(vendor_details[0].bank_name);
                        $("#branch").val(vendor_details[0].branch);
                        $("#acc_no").val(vendor_details[0].account_number);
                        $("#ifsc_code").val(vendor_details[0].ifsc_code);
                        $("#gst_id").val(vendor_details[0].gst_id);
                    }

                    // var bus_cat_list = '<option value="">Select</option>';
                    // if(category_details.length>0){
                    //     for(var i=0; i<category_details.length; i++){
                    //         bus_cat_list = bus_cat_list + '<option value="'+category_details[i].id+'">'+category_details[i].category_name+'</option>';
                    //     }
                    // }
                    // jQuery('select[name="bus_category[]"]').each(function() {
                    //     var cat_val = this.value;
                    //     // var id = this.id;
                    //     // $('#'+id).html(bus_cat_list);
                    //     // $('#'+id).val(cat_val);

                    //     // console.log(bus_cat_list);

                    //     this.innerHTML = bus_cat_list;
                    //     this.value = cat_val;
                    // });
                } else {
                    $("#vendor_code").val("");
                    $("#pan_no").val("");
                    $("#address").val("");
                    $("#legal_entity_name").val("");
                    $("#vat_no").val("");
                    $("#account_holder_name").val("");
                    $("#bank_name").val("");
                    $("#branch").val("");
                    $("#acc_no").val("");
                    $("#ifsc_code").val("");

                    jQuery('select[name="bus_category[]"]').each(function() {
                        // // var cat_val = this.value;
                        // var id = this.id;
                        // // $('#'+id).html(bus_cat_list);
                        // $('#'+id).val('');

                        this.value = '';
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    } else {
        $.ajax({
            url: BASE_URL+'index.php?r=accountmaster%2Fgetcode',
            type: 'post',
            data: {
                    type : $("#type").val(),
                    _csrf : csrfToken
                },
            success: function (data) {
                if(data != null){
                    $("#code").val(data);
                } else {
                    $("#code").val("");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    }
}

// $(".auto_vendor").autocomplete({
//     source: BASE_URL+'index.php?r=accountmaster%2Fgetvendors',
//     focus: function(event, ui) {
//             // prevent autocomplete from updating the textbox
//             event.preventDefault();
//             // manually update the textbox
//             $(this).val(ui.item.vendor_name);
//     },
//     select: function(event, ui) {
//             // prevent autocomplete from updating the textbox
//             event.preventDefault();
//             // manually update the textbox and hidden field
//             $(this).val(ui.item.vendor_name);
//             // var id = this.id;
//             // $("#" + id + "_id").val(ui.item.value);
//     },
//     change: function(event, ui) {
//             // var id = this.id;
//             // $("#" + id + "_id").val('');
//             // var con_name = $(this).val();
//             // $(this).val('');

//             // if (con_name!="" && con_name!=null) {
//             //   $.ajax({
//             //     method:"GET",
//             //     url:BASE_URL+'index.php/owners/loadcontacts',
//             //     data:{term : con_name},
//             //     dataType:"json",
//             //     success:function(responsdata){
//             //       $("#"+id).val(responsdata[0].label);
//             //       $("#" + id + "_id").val(responsdata[0].value);
//             //     }   
//             //   });
//             // }
//     },
//     minLength: 1
// });

$("#add_category").click(function(){
    $("#account_category_modal").modal('show');
})

$("#repeat_category").click(function(){
  	var tr = '<tr>' + 
				'<td>'+(cat_cnt+1).toString()+'<input type="hidden" class="form-control" name="category_id[]" id="category_id_'+cat_cnt.toString()+'" value=""></td>' + 
				'<td><input type="text" class="form-control" name="category_1[]" id="category_1_'+cat_cnt.toString()+'" value=""></td>' + 
				'<td><input type="text" class="form-control" name="category_2[]" id="category_2_'+cat_cnt.toString()+'" value=""></td>' + 
				'<td><input type="text" class="form-control" name="category_3[]" id="category_3_'+cat_cnt.toString()+'" value=""></td>' + 
			'</tr>';
	$("#category_body").append(tr);

	removeMultiInputNamingRules('#acc_category_master', 'input[alt="category_1[]"]');
    // removeMultiInputNamingRules('#acc_category_master', 'input[alt="category_2[]"]');
    // removeMultiInputNamingRules('#acc_category_master', 'input[alt="category_3[]"]');

    addMultiInputNamingRules('#acc_category_master', 'input[name="category_1[]"]', { required: true });
    // addMultiInputNamingRules('#acc_category_master', 'input[name="category_2[]"]', { required: true });
    // addMultiInputNamingRules('#acc_category_master', 'input[name="category_3[]"]', { required: true });

	cat_cnt++;
})

$("#btn_save_category").click(function(){
	if ($("#acc_category_master").valid()) {
	    save_categories();
	    // get_categories();
	}
})

$("#category_1").change(function(){
	cat_1 = $("#category_1").val();
})

$("#category_2").change(function(){
	cat_2 = $("#category_2").val();
})

$("#category_3").change(function(){
	cat_3 = $("#category_3").val();
})

function save_categories(){
	$.ajax({
        url: BASE_URL+'index.php?r=accountmaster%2Fsavecategories',
        type: 'post',
        data: $("#acc_category_master").serialize(),
        dataType: 'json',
        success: function (data) {
      		// if (parseInt(data)) {
		    //     $("#account_category_modal").modal('hide');
		    // }
		    set_categories(data);
		    $("#account_category_modal").modal('hide');
            get_categories();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}

function get_categories(){
	$.ajax({
        url: BASE_URL+'index.php?r=accountmaster%2Fgetcategories',
        type: 'post',
        data: $("#acc_category_master").serialize(),
        dataType: 'json',
        success: function (data) {
            update_categories(data);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}

function update_categories(data){
	var cat_data = '';
    var category = '';
    var category_1 = '<option value="">Select</option>';
    var category_2 = '<option value="">Select</option>';
    var category_3 = '<option value="">Select</option>';

    for(var i=0; i<data.length; i++){
    	cat_data = data[i];

    	category = category + '<tr>' + 
	                				'<td>'+(i+1)+'<input type="hidden" class="form-control" name="category_id[]" id="category_id_'+i+'" value="'+cat_data.id+'"></td>' + 
	                				'<td><input type="text" class="form-control" name="category_1[]" id="category_1_'+i+'" value="'+cat_data.category_1+'" readonly /></td>' + 
	                				'<td><input type="text" class="form-control" name="category_2[]" id="category_2_'+i+'" value="'+cat_data.category_2+'" readonly /></td>' + 
	                				'<td><input type="text" class="form-control" name="category_3[]" id="category_3_'+i+'" value="'+cat_data.category_3+'" readonly /></td>' + 
	                			'</tr>';

        if(cat_data.category_1!=null && cat_data.category_1!=''){
            category_1 = category_1 + '<option value="'+cat_data.category_1+'">'+cat_data.category_1+'</option>';
        }
    	if(cat_data.category_2!=null && cat_data.category_2!=''){
    	   category_2 = category_2 + '<option value="'+cat_data.category_2+'">'+cat_data.category_2+'</option>';
        }
        if(cat_data.category_3!=null && cat_data.category_3!=''){
    	   category_3 = category_3 + '<option value="'+cat_data.category_3+'">'+cat_data.category_3+'</option>';
        }
    }

    $("#category_body").html(category);

    $("#category_1").html(category_1);
    $("#category_2").html(category_2);
    $("#category_3").html(category_3);

    $("#category_1").val(cat_1);
    $("#category_2").val(cat_2);
    $("#category_3").val(cat_3);
}

function set_categories(data){
    var cat_data = '';
    var category = '';
    var category_1 = '<option value="">Select</option>';
    var category_2 = '<option value="">Select</option>';
    var category_3 = '<option value="">Select</option>';

    for(var i=0; i<data.length; i++){
        cat_data = data[i];

        // category = category + '<tr>' + 
        //                             '<td>'+(i+1)+'<input type="hidden" name="category_id[]" id="category_id_'+i+'" value="'+cat_data.id+'"></td>' + 
        //                             '<td><input type="text" class="form-control" name="category_1[]" id="category_1_'+i+'" value="'+cat_data.category_1+'" readonly /></td>' + 
        //                             '<td><input type="text" class="form-control" name="category_2[]" id="category_2_'+i+'" value="'+cat_data.category_2+'" readonly /></td>' + 
        //                             '<td><input type="text" class="form-control" name="category_3[]" id="category_3_'+i+'" value="'+cat_data.category_3+'" readonly /></td>' + 
        //                         '</tr>';

        if(cat_data.category_1!=null && cat_data.category_1!=''){
            category_1 = category_1 + '<option value="'+cat_data.category_1+'">'+cat_data.category_1+'</option>';
        }
        if(cat_data.category_2!=null && cat_data.category_2!=''){
           category_2 = category_2 + '<option value="'+cat_data.category_2+'">'+cat_data.category_2+'</option>';
        }
        if(cat_data.category_3!=null && cat_data.category_3!=''){
           category_3 = category_3 + '<option value="'+cat_data.category_3+'">'+cat_data.category_3+'</option>';
        }
    }

    // $("#category_body").html(category);

    $("#category_1").html(category_1);
    $("#category_2").html(category_2);
    $("#category_3").html(category_3);

    $("#category_1").val(cat_1);
    $("#category_2").val(cat_2);
    $("#category_3").val(cat_3);
}

$('#repeat_business_category').click(function(){
    var $tableBody = $('#business_category').find("tbody"),
    $trLast = $tableBody.find("tr:last"),
    $trNew = $trLast.clone();

    var id = $trNew.attr('id');
    var index = id.substr(id.lastIndexOf('_')+1);
    var newIndex = parseInt(index) + 1;
    // console.log(index);
    $trNew.attr('id', 'cat_row_'+newIndex);
    $trNew.find('td:nth-child(2)').html(newIndex+1);
    $trNew.find('#delete_row_'+index).attr('id', 'delete_row_'+newIndex);
    $trNew.find('#cat_id_'+index).attr('id', 'cat_id_'+newIndex).val("");
    $trNew.find('#cat_name_'+index).attr('id', 'cat_name_'+newIndex).val("");
    // $trNew.find('#acc_code_'+index).attr('id', 'acc_code_'+newIndex).val("");
    // $trNew.find('#trans_'+index).attr('id', 'trans_'+newIndex).val("");
    // $trNew.find('#debit_amt_'+index).attr('id', 'debit_amt_'+newIndex).val("");
    // $trNew.find('#credit_amt_'+index).attr('id', 'credit_amt_'+newIndex).val("");

    $trLast.after($trNew);
})

function set_bus_category(elem){
    var id = elem.id;
    var index = id.substr(id.lastIndexOf('_')+1);
    $('#cat_name_'+index).val($('#cat_id_'+index+' option:selected').text());
}