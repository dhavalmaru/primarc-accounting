$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$("#date_type").change(function(){
    if($("#date_type").val()=="Date Range"){
        // $("#date_range_div").show();
        // $("#date_div").hide();
        // $("#tran_type_div").show();
        $(".date_range_div").show();
        $(".as_of_date_div").hide();
    } else {
        // $("#date_range_div").hide();
        // $("#date_div").show();
        // $("#tran_type_div").hide();
        $(".date_range_div").hide();
        $(".as_of_date_div").show();
        // $('#as_of_date').val(new Date());
    }

    set_table();
})

$("#date_criteria").change(function(){
    if($("#date_criteria").val()=="By Date"){
        $('#from_date').val("");
        $('#to_date').val("");
    } else {
        var today = new Date();
        var curMonth = today.getMonth()+1;
        var curYear = today.getFullYear();
        var from_date = "01/04/";
        var to_date = "31/03/";
        if (curMonth > 3) {
            from_date = from_date + curYear;
            to_date = to_date + (curYear+1);
        } else {
            from_date = from_date + (curYear-1);
            to_date = to_date + curYear;
        }
        $('#from_date').val(from_date);
        $('#to_date').val(to_date);
    }
})

$("#generate").click(function(){
    generate_report();
})

var table = null;
var table2 = null;
function generate_report(){
    var date_type = $('#date_type').val();
    var as_of_date = $('#as_of_date').val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // console.log(date_type);
    // console.log(from_date);
    // console.log(to_date);

    if($("#date_type").val()=="Date Range"){
        var as_of_date = to_date;
        $('#as_of_date').val(as_of_date);
        $('#as_of').html('Date: '+as_of_date);
    } else {
        $('#as_of').html('Date: '+as_of_date);
    }

    $.ajax({
        url: BASE_URL+'index.php?r=accreport%2Fgettrialbalance',
        type: 'post',
        data: {
                date_type : date_type,
                as_of_date : as_of_date,
                from_date : from_date,
                to_date : to_date,
                _csrf : csrfToken
             },
        dataType: 'json',
        success: function (data) {
            if(data != null){
                $('#example').html(data.tbody);
                $('#example2').html(data.tbody2);
                // $('#example tbody').html(data.tbody);
                // $('#example2 tbody').html(data.tbody2);
                $("#company_name").html("Primarc Pecan Retail Pvt Ltd");
                $("#from").html('From: '+from_date);
                $("#to").html('To: '+to_date);

                // $("#example").fixHeader();

                // fix_header();
            } else {
                $('#example').html("");
                $('#example2').html("");
                // $('#example tbody').html("");
                // $('#example2 tbody').html("");
                $("#company_name").html("");
                $("#from").html("");
                $("#to").html("");
            }

            set_table();

        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}

$('#business_category').change(function(){
    change_bus_cat();
})

function change_bus_cat(){
    if($('#business_category').prop('checked')){
        $('.bus_cat').show();
    } else {
        $('.bus_cat').hide();
    }
}

$('#accounts_category').change(function(){
    change_acc_cat();
})

function change_acc_cat(){
    if($('#accounts_category').prop('checked')){
        $('.acc_cat').show();
    } else {
        $('.acc_cat').hide();
    }
}

function set_table(){
    if ($.fn.dataTable.isDataTable('#example')) {
        table.destroy();
    }

    table = $('#example').DataTable({
        scrollX: '50vh',
        scrollY: '50vh',
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

    $("#example_wrapper .btn-group a").hide(); 
    $("#example_wrapper .btn-group").click(function(){
        $("#example_wrapper .btn-group").toggleClass('btn_close');
        $("#example_wrapper .btn-group a").toggle(100);
    });

    if ($.fn.dataTable.isDataTable('#example2')) {
        table2.destroy();
    }

    table2 = $('#example2').DataTable({
        scrollY: '50vh',
        scrollCollapse: true,
        lengthChange: false,
        ordering: false,
        autoWidth: false,
        searching: false,
        paging: false,
        bInfo: false,
        buttons: [ 'excel', 'csv', 'print'  ]
    });

    table2.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(1)');

    $("#example2_wrapper .btn-group a").hide(); 
    $("#example2_wrapper .btn-group").click(function(){
        $("#example2_wrapper .btn-group").toggleClass('btn_close');
        $("#example2_wrapper .btn-group a").toggle(100);
    });

    change_bus_cat();
    change_acc_cat();
}