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
    $('.loading').fadeIn(1000); 
    $('#loader').fadeOut(400);

    if($('#from_date').val()==""){
        change_date_criteria();
    }

    // $("#company_name").html("Primarc Pecan Retail Pvt Ltd");
    $("#account_name").html($("#account option:selected").text());
    $('.select2').select2();

    $('#narration').change(function(){
        show_narration();
    });
    set_table();
	$("#date_criteria").change(function(){
        change_date_criteria();
    });
});

$('.datepicker').datepicker({changeMonth: true,changeYear: true});



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

    table = $('#example').DataTable({
        scrollY: '38vh',
        scrollCollapse: true,
        lengthChange: false,
        ordering: false,
        autoWidth: false,
        searching: false,
        paging: false,
        bInfo: false,
        buttons: [ 'excel', 'csv', 'print'  ]
        // buttons: [
        //     // {
        //     //     extend: 'excel',
        //     //     title: 'Ledger_Report_3',
        //     //     // message: $('#company_name').html()+" \n"+$('#account_name').html()+" \n From:"+$('#from').html()+"      To:"+$('#to').html()
        //     //     // customize: function (excel) {
        //     //     //     return "Any heading for the csv file can be separated with , and for new line use \n";
        //     //     // }
        //     //     // messageTop: "Primarc Pecan Retail Pvt Ltd"
        //     //     customize: function (excel) {
        //     //         return $('#company_name').html()+" \n"+$('#account_name').html()+" \n "+$('#from').html()+","+$('#to').html()+" \n"+excel
        //     //     }
        //     // },
        //     {
        //         extend: 'excel',
        //         title: 'Ledger_Report_4',
        //         messageTop: 'Primarc Pecan'
        //     },
        //     {
        //         extend: 'csv',
        //         title: 'CSV_file_1',
        //         customize: function (csv) {
        //             return $('#company_name').html()+" \n"+$('#account_name').html()+" \n "+$('#from').html()+","+$('#to').html()+" \n"+csv
        //         }
        //     },
        //     {
        //         extend: 'pdf',
        //         title: 'Pdf_file',
        //         customize: function ( doc ) {
        //             doc.content.splice( 0, 0, {
        //                 text: "custom header\n my header"
        //             });
        //         }
        //     }
        // ]
        // buttons: [
        //     {
        //         extend: 'excelHtml5',

        //         title: 'Any title for file',
        //         message: "Any message for header inside the file. I am not able to put message in next row in excel file but you can use \n"


        //     },
        //     {
        //         extend: 'csvHtml5',
        //         title: 'Any title for the file',
        //          customize: function (csv) {
        //              return "Any heading for the csv file can be separated with , and for new line use \n"+csv;
        //           }
        //     },
        //     {
        //         extend: 'pdfHtml5',
        //         title: 'Any title for file',
        //         customize: function ( doc ) {
        //                         doc.content.splice( 0, 0, {
        //                             text: "custom header\n my header"
        //                         } );
        //         }
        //     }
        // ]
    });

    table.buttons().container().appendTo('#example_wrapper .col-md-6:eq(1)');

    $(".btn-group a").hide(); 
    $(".btn-group").click(function(){
        $(".btn-group").toggleClass('btn_close');
        $(".btn-group a").toggle(100);
    });

    show_narration();
}