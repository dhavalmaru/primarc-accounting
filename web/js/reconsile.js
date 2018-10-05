var table = null;
$('.loading').hide();   

// $(document).ready(function(){
//     // var div = $(".panel");
//     // startAnimation();
//     // function startAnimation(){
//     //  div.animate({height: '100%'}, "slow");
//     // }

//     $('.panel').addClass('panel_height');
//     $('.loading').fadeIn(1000); 
//     $('#loader').fadeOut(400);
// });

$(document).ready(function(){
    $('.select2').select2();
    $('.loading').fadeIn(1000); 
    $('#loader').fadeOut(400);

    if($('#from_date').val()==""){
        change_date_criteria();
    }

    // $("#company_name").html("Primarc Pecan Retail Pvt Ltd");
    $("#account_name").html($("#account option:selected").text());

    // set_table();

    reconsile_amount();
});

$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$("#date_criteria").change(function(){
    change_date_criteria();
});

$("#check_all").change(function(){
    var blChecked = false;
    if($(this).prop('checked')==true) {
        blChecked = true;
    }

    $(".check").each(function( index ) {
        $(this).prop('checked', blChecked);
    });
});

function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}

function change_date_criteria(){
    if($("#date_criteria").val()=="By Date"){
        $('#from_date').val("");
        $('#to_date').val("");
    } else {
        var today = new Date();
        var curMonth = today.getMonth()+1;
        var curYear = today.getFullYear();
        var from_date = "01/04/";
        var to_date = "31/03/";

        // console.log(today);
        // console.log(curMonth);
        // console.log(curYear);

        if (parseInt(curMonth) > 3) {
            from_date = from_date + curYear;
            to_date = to_date + (curYear+1);
        } else {
            from_date = from_date + (curYear-1);
            to_date = to_date + curYear;
        }

        to_date = pad(today.getDate(),2)+'/'+pad(today.getMonth()+1,2)+'/'+today.getFullYear();

        $('#from_date').val(from_date);
        $('#from_date_span').html(from_date);
        $('#to_date').val(to_date);
    }
}

$('#narration').change(function(){
    show_narration();
});

$('.page-item').click(function(){
    show_narration();
    console.log('dd');
});

function show_narration(){
    if($('#narration').prop('checked')==true) {
        $('.show_narration').show();
    } else {
        $('.show_narration').hide();
    }
}

function set_table(){
    if ($.fn.dataTable.isDataTable('#example')) {
        table.destroy();
        show_narration();
    }

    // $('#example').DataTable.destroy();

    table = $('#example').DataTable({
        scrollY: '90vh',
        scrollCollapse: true,
        lengthChange: false,
        ordering: false,
        autoWidth: false,
        searching: false,
        paging: false,
        bInfo: false,
        buttons: [ 'excel', 'csv', 'print'  ]
    });

    table.buttons().container().appendTo('#example_wrapper .col-md-6:eq(1)');

    $(".btn-group a").hide(); 
    $(".btn-group").click(function(){
        $(".btn-group").toggleClass('btn_close');
        $(".btn-group a").toggle(100);
    });

    show_narration();
}

$("#pb1").click(function(){
    var count = $(".payment_date").length;
    var compare_date=$("#compare_date").val();
    var payment_date=$(".payment_date").val();

    for(var i=0;i<count;i++)
    {
        
      if(compare_date==($("#payment_date_"+i).val()))
        {
            //$("#payment_date_"+i).val('');
            var ifchecked = $("#payment_date_"+i).closest('tr').children('td').find('.reconsile').is(":checked");
            if(ifchecked==true)
            {
                $("#payment_date_"+i).val('');
                $("#payment_date_"+i).closest('tr').children('td').find('.reconsile').prop("checked",false);
            }
        }               
    }
    reconsile_amount();
    $("#check_all").prop("checked",false);
});

$("#copy").click(function(){
    var count = $(".reconsile").length;
    var compare_date=$("#compare_date").val();

    for(var i=0;i<count;i++)
    {
        var ifchecked = $("#reconsile_date_"+i).closest('tr').children('td').find('.reconsile').is(":checked");

        if(ifchecked)
        {
            $("#payment_date_"+i).val(compare_date);
        }
                            
    }

    reconsile_amount();
});

$(".payment_date").on('change', function(){

    var date = $(this).val();

    if(date!="")
    {
        $(this).closest('tr').children('td').find('.reconsile').prop("checked",true);
    }
    else
    {
        $(this).closest('tr').children('td').find('.reconsile').prop("checked",false);
    }
});

var reconsile_amount = function() {
    var count = $(".payment_date").length;
    var total = $("#total_as_per_book").val();
    var from_date = $("#from_date").val();
        from_date = splidate(from_date);
        from_date = Date.parse(from_date);
    var todate = $("#to_date").val();
        todate = splidate(todate);
        todate = Date.parse(todate);
    total = get_number(total);
    var credit = 0;
    var debit = 0;
    var bool = false;
    var i=0;

    // console.log("Hiii");

    for(var i=0;i<count;i++) {
        bool = false;

        if($("#payment_date_"+i).val()!="" && $("#payment_date_"+i).val()!=undefined) {
            // console.log(i++);

            paymentdate = $("#payment_date_"+i).val();
            var ref_date = $("#payment_date_"+i).closest('tr').children('td.ref_date').text();   
                ref_date = splidate(ref_date);
                ref_date = Date.parse(ref_date);
            
            if(paymentdate!="") {
                if(paymentdate!="") {
                    paymentdate = splidate(paymentdate);
                    paymentdate = Date.parse(paymentdate);
                }
                if(paymentdate>Date.now()) {
                    bool = true;
                }
                if(paymentdate>todate) {
                    bool = true;
                }
                if(paymentdate<ref_date) {
                    bool = true;
                }
                // if(paymentdate<Date.now() && paymentdate<todate && paymentdate>ref_date) {
                //     bool = true;
                // }
            }
        } else {
            bool = true;
        }

        if(bool == true){
            var debit_amt = $("#payment_date_"+i).closest('tr').children('td.debit_amt').text();
            var credit_amount = $("#payment_date_"+i).closest('tr').children('td.credit_amt').text();
            debit_amt = get_number(debit_amt);
            credit_amount = get_number(credit_amount);
            
            debit = debit+debit_amt;
            credit = credit+credit_amount;
        }
    }

    /*console.log('debit'+debit)+"<br>";
    console.log('credit'+credit)+"<br>";    */
    var differnce='';
    var differnce_type='';
    if(credit>debit) {
        var differnce = (credit-debit);
        var differnce_type = "Cr";
        $("#difference_bal_debit").empty();
        $("#difference_bal_credit").text(format_money(Math.abs(differnce),2));
    } else {
        var differnce = (debit-credit);
        var differnce_type = "Dr";
        $("#difference_bal_credit").empty();
        $("#difference_bal_debit").text(format_money(Math.abs(differnce),2));
    }
    $("#difference_type").text(differnce_type);
    

    var ajaxval = 0;
    //console.log(csrfToken);
    // $.ajax({
    //         url: BASE_URL+'index.php?r=accreport%2Fgetasperbank',
    //         data: 'account='+$("#account").val()+"&_csrf="+csrfToken+'&from_date='+$("#from_date").val()+
    //         '&to_date='+$("#to_date").val()+'&view='+$("#view").val(),
    //         type: "POST",
    //         dataType: 'html',
    //         global: false,
    //         async: false,
    //         success: function (data) {
    //             result = parseInt(data);
    //             ajaxval = result;
    //         },
    //         error: function (xhr, ajaxOptions, thrownError) {
    //             alert(xhr.status);
    //             alert(thrownError);
    //         }
    // });
    //console.log('ajaxval'+ajaxval);

    ajaxval = total;

    if(differnce_type=="Dr")
    {
        ajaxval = ajaxval+differnce;
    }
    else
    {
        ajaxval =  ajaxval-differnce;
    }


    if(ajaxval>0)
    {
       var ajaxtype = "Cr";
       $("#asperbank_debit").empty();
       $("#asperbank_credit").text(format_money(Math.abs(ajaxval),2));
    }
    else
    {
        var ajaxtype = "Dr";
        $("#asperbank_credit").empty();
        $("#asperbank_debit").text(format_money(Math.abs(ajaxval),2));
    }

    $("#asperbanktype").text(ajaxtype); 
    $("#asperbank").text(format_money(ajaxval,2));

    set_table();
}

var format_money = function(num, decimals){
    if(num==null || num==""){
        num="";
    }
    num = num.toString().replace(/[^0-9]/g,'');
    var x=num;
    x=x.toString();
    x = x.split(",").join("");
    var lastThree = x.substring(x.length-3);
    var otherNumbers = x.substring(0,x.length-3);
    if(otherNumbers != '') lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
    return res;
}

var get_number = function(num, decimals){
    if(num==null || num==""){
        num="0";
    }
    res = parseFloat(num.replaceAll(",",""));
    return res;
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

var format_number = function(elem){
    var res = format_money(elem.value,2);
    $(elem).val(res);
}

// $("#generate").click(function(){
    //     var account = $('#account').val();
    //     var from_date = $('#from_date').val();
    //     var to_date = $('#to_date').val();
    //     var csrfToken = $('meta[name="csrf-token"]').attr("content");

    //     // console.log(account);
    //     // console.log(from_date);
    //     // console.log(to_date);

    //     $.ajax({
    //         url: BASE_URL+'index.php?r=accreport%2Fgetledger',
    //         type: 'post',
    //         async: false,
    //         data: {
    //                 account : account,
    //                 from_date : from_date,
    //                 to_date : to_date,
    //                 _csrf : csrfToken
    //              },
    //         dataType: 'html',
    //         success: function (data) {
    //             if(data != null){
    //                 $('#example').html(data);
    //                 $("#company_name").html("Primarc Pecan Retail Pvt Ltd");
    //                 $("#account_name").html($("#account option:selected").text());
    //                 $("#from").html('From: '+from_date);
    //                 $("#to").html('To: '+to_date);

    //                 show_narration();
    //             } else {
    //                 $('#example').html("");
    //                 $("#company_name").html("Primarc Pecan Retail Pvt Ltd");
    //                 $("#account_name").html("");
    //                 $("#from").html("");
    //                 $("#to").html("");
    //             }

    //             set_table();
    //             show_narration();
    //         },
    //         error: function (xhr, ajaxOptions, thrownError) {
    //             alert(xhr.status);
    //             alert(thrownError);
    //         }
    //     });
// })