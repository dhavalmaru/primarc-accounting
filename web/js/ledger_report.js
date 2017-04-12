$('.datepicker').datepicker({changeMonth: true,changeYear: true});

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
})

$("#generate").click(function(){
    var acc_id = $('#account').val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // console.log(acc_id);
    // console.log(from_date);
    // console.log(to_date);

    $.ajax({
        url: BASE_URL+'index.php?r=accreport%2Fgetledger',
        type: 'post',
        data: {
                acc_id : acc_id,
                from_date : from_date,
                to_date : to_date,
                _csrf : csrfToken
             },
        dataType: 'html',
        success: function (data) {
            if(data != null){
                $('#tab_report tbody').html(data);
                $("#account_name").val($("#account option:selected").text());
                $("#company_name").val("Primarc Pecan Retail Pvt Ltd");
                $("#from").val(from_date);
                $("#to").val(to_date);
            } else {
                $('#tab_report tbody').html("");
                $("#company_name").val("");
                $("#account_name").val("");
                $("#from").val("");
                $("#to").val("");
            }
            show_narration();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
})

$('#narration').change(function(){
    show_narration();
})

function show_narration(){
    if($('#narration').prop('checked')==true) {
        $('.show_narration').show();
    } else {
        $('.show_narration').hide();
    }
}