$(".transaction").change(function(){
	var result = 1;
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    var id = $(this).attr('id');
    var trans = id.substring(0, id.indexOf('_'));
    
    $.ajax({
        url: BASE_URL+'index.php?r=debitcreditnote%2Fgetaccountdetails',
        type: 'post',
        data: {
                transaction : $("#"+id).val(),
                _csrf : csrfToken
             },
        success: function (data) {
            if(data != null){
            	$("#"+trans+"_acc_code").val(data);
            } else {
                $("#"+trans+"_acc_code").val("");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });

    // if (result) {
    //     return false;
    // } else {
    //     return true;
    // }
});

$(document).ready(function() {
    addMultiInputNamingRules('#debit_credit_note', 'select[name="transaction[]"]', { required: true });
    addMultiInputNamingRules('#debit_credit_note', 'input[name="due_date[]"]', { required: true });
    addMultiInputNamingRules('#debit_credit_note', 'input[name="amount[]"]', { required: true, numbersandcommaonly: true });
});