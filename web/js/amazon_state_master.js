$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$(document).ready(function() {
    set_view();
	$('.select2').select2();
});

function set_view(){
    if($('#action').val()=='view' || $('#status').val()=='approved'){
        $('#btn_submit').hide();
        $('#btn_reject').hide();
        $("input").attr("disabled", true);
        $("select").attr("disabled", true);
        $("textarea").attr("disabled", true);
    } else if($('#action').val()=='insert' || $('#action').val()=='edit'){
        $('#btn_submit').val("Submit");
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