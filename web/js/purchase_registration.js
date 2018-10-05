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

    var account_name = $("#account option:selected").text();
    var voucher_type = $("#vouchertype option:selected").text();
    var state = $("#state option:selected").text();
    var date_criteria = $("#date_criteria option:selected").text();
    var view = $("#view option:selected").text();

    // $("#account_name").html("All");
    // $("#voucher_type_name").html("All");
    // $("#state_name").html("All");

    // console.log('eee');

    // if(account_name!=""){
    //     $("#account_name").html(account_name);
    // }
        
    // $("#company_name").html("Primarc Pecan Retail Pvt Ltd");

    if($("#account option:selected").length==0){
        $("#account_name").html("All");
    } else {
        $("#account_name").html(account_name);
    }
    if($("#vouchertype option:selected").text()==' '){
        $("#voucher_type_name").html("All");
    } else {
        $("#voucher_type_name").html(voucher_type);
    }
    if(state==' '){
        $("#state_name").html("All");
    } else {
        $("#state_name").html(state);
    }
    
    $('.loading').fadeIn(1000); 
    $('#loader').fadeOut(400);

    if($('#from_date').val()==""){
        change_date_criteria();
    }

    set_table();
})

$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$("#date_criteria").change(function(){
    change_date_criteria();
})

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
        $('#from_date').val(from_date);
        $('#to_date').val(to_date);
    }
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

$('#narration').change(function(){
    show_narration();
})

$('.page-item').click(function(){
    show_narration();
    console.log('dd');
})

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
    var table = $('#example').DataTable({
        "scrollY": 200,
        "scrollX": true,
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
    /*
    table = $('#example').DataTable();
    table.buttons().container().appendTo('#example_wrapper .col-md-6:eq(1)');
    table.fixedHeader.disable();*/

    $(".btn-group a").hide(); 
    $(".btn-group").click(function(){
        $(".btn-group").toggleClass('btn_close');
        $(".btn-group a").toggle(100);
    });

    show_narration();
}