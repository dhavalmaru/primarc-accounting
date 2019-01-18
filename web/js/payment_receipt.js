$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$(document).ready(function() {
    setPaymentType();
    setPaymentType1();

    getLedger();
	$('.select2').select2();
	//$("#trans_type").on('select2:selecting', function(e) {
	$("#trans_type").change (function() {
    	var csrfToken = $('meta[name="csrf-token"]').attr("content");
        setPaymentType1();
    	setPaymentType2();
    });

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
            async: false,
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
        
        var bank_id = $("#bank_id").val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: BASE_URL+'index.php?r=paymentreceipt%2Fgetaccbankdetails',
            type: 'post',
            data: {
                    bank_id : bank_id,
                    _csrf : csrfToken
                 },
            dataType: 'json',
            async: false,
            success: function (data) {
                if(data != null){
                    if(data.length>0){
                        $("#acc_code1").val(data[0].code);
                        $("#bank_name").val(data[0].bank_name);
                    }
                } else {
                    $("#acc_code1").val("");
                    $("#bank_name").val("");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
        // getLedger();
    });

    //$(document.body).on("change","#search_code",function(){

    $("#payment_type").change(function(){
        setPaymentType();
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

    set_view();
	$('.select2').select2();
	//$("#trans_type").on('select2:selecting', function(e) {
	$("#trans_type").change (function() {
    	var csrfToken = $('meta[name="csrf-token"]').attr("content");
        setPaymentType1();
    	setPaymentType2();
    });

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
        
        var bank_id = $("#bank_id").val();
        var csrfToken = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
            url: BASE_URL+'index.php?r=paymentreceipt%2Fgetaccbankdetails',
            type: 'post',
            data: {
                    bank_id : bank_id,
                    _csrf : csrfToken
                 },
            dataType: 'json',
            success: function (data) {
                if(data != null){
                    if(data.length>0){
                        $("#acc_code1").val(data[0].code);
                        $("#bank_name").val(data[0].bank_name);
                    }
                } else {
                    $("#acc_code1").val("");
                    $("#bank_name").val("");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
        getLedger();
    });

    //$(document.body).on("change","#search_code",function(){

    $("#payment_type").change(function(){
        setPaymentType();
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

function set_view(){
    if($('#action').val()=='view' || $('#status').val()=='approved'){
        $('#btn_submit').hide();
        $('#btn_reject').hide();
        $("input").attr("disabled", true);
        $("select").attr("disabled", true);
        $("textarea").attr("disabled", true);
    } else if($('#action').val()=='insert' || $('#action').val()=='edit'){
        $('#btn_submit').val("Submit For Approval");
        $('#btn_submit').show();
        $('#btn_reject').hide();
    } else if($('#action').val()=='authorise'){
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

function setPaymentType1() {
    if($("#trans_type").val()=="Contra Entry"){
        $(".ad_hock").show();
        $(".bank_acc_code").show();
        $(".payment_type1").hide();
        $('#acc_label').html('Bank & Cash account (Receipt)');
        $('#bank_label').html('Bank & Cash account (Paid)');
        $("#payment_type").val('Adhoc');
    } else {
        if($("#payment_type").val()=="Adhoc") {
            $(".ad_hock").show();
            $("#knock_off").hide();
        } else if($("#payment_type").val()=="Knock off") {
            $(".ad_hock").hide();
            $("#knock_off").show();
        } else {
            $(".ad_hock").hide();
            $("#knock_off").hide();
        }
        $(".bank_acc_code").hide();
        $(".payment_type1").show();
        $('#acc_label').html('Account Name');
        $('#bank_label').html('Bank Name');
    }
}

function setPaymentType2() {
    var trans_type = $('#trans_type').val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // if( trans_type == 'Contra Entry')
    // {

    $.ajax({
        url: BASE_URL+'index.php?r=paymentreceipt%2Fgetotheraccdetails',
        method: 'post',
        data: {trans_type: trans_type , _csrf : csrfToken},
        dataType: 'html',
<<<<<<< HEAD
        async: false,
=======
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
        success: function(response){
            $('#acc_id').html(response);
            // $('#account_id').find('option').not(':first').remove();
            // $.each(response,function(index,data){
            //    $('#account_id').append('<option value="'+data['id']+'">'+data['legal_name']+'</option>');
            // });
        }
    });
<<<<<<< HEAD
=======

    // }
    // else
    //  {
    // $.ajax({
    // url: BASE_URL+'index.php?r=paymentreceipt%2Fgetotheraccdetails1',
    //       method: 'post',
    //      data: {trans_type: trans_type , _csrf : csrfToken},
    //       dataType: 'json',
    //       success: function(response){
    //         $('#account_id').find('option').not(':first').remove();
    //         // Add options
    //   // response = $.parseJSON(response);
    //   // console.log(response);
    //          $.each(response,function(index,data){
    //            $('#account_id').append('<option value="'+data['id']+'">'+data['legal_name']+'</option>');
    //         });
    //       }
    //    });
    //  }
>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
}

function getLedger(){
    var result = 1;
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    
    $.ajax({
        url: BASE_URL+'index.php?r=paymentreceipt%2Fgetledger',
        type: 'post',
        async: false,
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
}

function getLedgerTotal(){
    $(".check").each(function( index ) {
        var id = $(this).attr("id");
        var index = id.substring(id.indexOf("_")+1);
        // console.log(index);

        var amount = get_number($("#total_amount_"+index).val());
        var paid_amount = get_number($("#total_paid_amount_"+index).val());

        if($(this).prop('checked')==true) {
            $("#amount_to_pay_"+index).val(Math.round((amount-paid_amount)*100)/100);
            $("#chk_val_"+index).val("1");
        } else {
            $("#amount_to_pay_"+index).val('0.00');
            $("#chk_val_"+index).val("0");
        }
    });

    getTotal();
}

function setAmount(elem){
    var id = elem.id;
    var index = id.substring(id.indexOf("_")+1);

    var amount = get_number($("#total_amount_"+index).val());
    var paid_amount = get_number($("#total_paid_amount_"+index).val());

    if($("#"+id).prop('checked')==true) {
        $("#amount_to_pay_"+index).val(Math.round((amount-paid_amount)*100)/100);
        $("#chk_val_"+index).val("1");
    } else {
        $("#amount_to_pay_"+index).val('0.00');
        $("#chk_val_"+index).val("0");
    }

    getTotal();
}

function getTotal(){
    var total_transaction = '';
    var total_amount_total = 0;
    var total_paid_transaction = '';
    var total_paid_amount_total = 0;
    var paying_transaction = '';
    var paying_amount_total = 0;
    var bal_transaction = '';
    var bal_amount_total = 0;

    $(".check").each(function( index ) {
        var id = $(this).attr("id");
        var index = id.substring(id.indexOf("_")+1);
        // console.log(index);

        var transaction = $("#transaction_"+index).val();
        var amount = get_number($("#total_amount_"+index).val());
        var total_paid_amount = get_number($("#total_paid_amount_"+index).val());
        var amount_to_pay = get_number($("#amount_to_pay_"+index).val());
        var bal_amount = Math.round((amount-total_paid_amount-amount_to_pay)*100)/100;
        $("#bal_amount_"+index).val(format_money(bal_amount,2));

        // if(transaction.toUpperCase().trim()=="DEBIT"){
        //     total_amount_total = total_amount_total-amount;
        //     total_paid_amount_total = total_paid_amount_total-total_paid_amount;
        //     paying_amount_total = paying_amount_total-amount_to_pay;
        //     bal_amount_total = bal_amount_total-bal_amount;
        // } else {
        //     total_amount_total = total_amount_total+amount;
        //     total_paid_amount_total = total_paid_amount_total+total_paid_amount;
        //     paying_amount_total = paying_amount_total+amount_to_pay;
        //     bal_amount_total = bal_amount_total+bal_amount;
        // }

        total_amount_total = total_amount_total+amount;
        total_paid_amount_total = total_paid_amount_total+total_paid_amount;
        paying_amount_total = paying_amount_total+amount_to_pay;
        bal_amount_total = bal_amount_total+bal_amount;
    });

    if(total_amount_total<0){
        // total_amount_total = total_amount_total*-1;
        total_transaction = 'Debit';
    } else {
        total_transaction = 'Credit';
    }
    if(total_paid_amount_total<0){
        // total_paid_amount_total = total_paid_amount_total*-1;
        total_paid_transaction = 'Debit';
    } else {
        total_paid_transaction = 'Credit';
    }
    if(paying_amount_total<0){
        // paying_amount_total = paying_amount_total*-1;
        paying_transaction = 'Debit';
    } else {
        paying_transaction = 'Credit';
    }
    if(bal_amount_total<0){
        // bal_amount_total = bal_amount_total*-1;
        bal_transaction = 'Debit';
    } else {
        bal_transaction = 'Credit';
    }

<<<<<<< HEAD
    $('#total_transaction').val(total_transaction);
    $('#total_amount_total').val(format_money(total_amount_total,2));
    $('#total_paid_transaction').val(total_paid_transaction);
    $('#total_paid_amount_total').val(format_money(total_paid_amount_total,2));
    $('#paying_transaction').val(paying_transaction);
    $('#paying_amount_total').val(format_money(paying_amount_total,2));
    $('#bal_transaction').val(bal_transaction);
    $('#bal_amount_total').val(format_money(bal_amount_total,2));
}
=======
    $("#total_debit_amt").val(format_money(total_debit_amt,2));
    $("#total_credit_amt").val(format_money(total_credit_amt,2));
    $("#paying_debit_amt").val(format_money(paying_debit_amt,2));
    $("#paying_credit_amt").val(format_money(paying_credit_amt,2));
    $("#net_debit_amt").val(format_money(net_debit_amt,2));
    $("#net_credit_amt").val(format_money(net_credit_amt,2));
    $("#payable_debit_amt").val(format_money(payable_debit_amt,2));
    $("#payable_credit_amt").val(format_money(payable_credit_amt,2));
}

// $("#acc_id").change(function(){
    // var acc_id = $("#acc_id").val();
    // var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // $.ajax({
        // url: BASE_URL+'index.php?r=paymentreceipt%2Fgetaccdetails',
        // type: 'post',
        // data: {
                // acc_id : acc_id,
                // _csrf : csrfToken
             // },
        // dataType: 'json',
        // success: function (data) {
            // if(data != null){
                // if(data.length>0){
                    // $("#acc_code").val(data[0].code);
                    // $("#legal_name").val(data[0].legal_name);
                // }
            // } else {
                // $("#acc_code").val("");
                // $("#legal_name").val("");
            // }
        // },
        // error: function (xhr, ajaxOptions, thrownError) {
            // alert(xhr.status);
            // alert(thrownError);
        // }
    // });

    // getLedger();
// });

>>>>>>> 40251667d20641f61579b49c4e0131e7351baf6f
