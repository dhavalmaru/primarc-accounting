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
        $('#as_of').val(as_of_date);
    } else {
        $('#as_of').val(as_of_date);
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
                $('#tab_report tbody').html(data.tbody);
                $('#tab_report2 tbody').html(data.tbody2);
                $("#company_name").val("Primarc Pecan Retail Pvt Ltd");
                $("#from").val(from_date);
                $("#to").val(to_date);
            } else {
                $('#tab_report tbody').html("");
                $("#company_name").val("");
                $("#from").val("");
                $("#to").val("");
            }
            change_bus_cat();
            change_acc_cat();
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