$("#login_form").validate({
    rules: {
        uname: {
            required: true,
            check_credentials: true
        },
        upass: {
            required: true
        }
    },

    ignore: false,

    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    }
});

$.validator.addMethod("check_credentials", function (value, element) {
    var result = 1;
    // var csrfToken = $('meta[name="csrf-token"]').attr("content");
    // var csrfToken = $('csrf_token"]').val();

    // console.log($("#uname").val());
    // console.log($("#upass").val());
    // console.log(csrfToken);

    $.ajax({
        url: BASE_URL+'index.php?r=login%2Fcheckcredentials',
        // data: {
        //         uname : $("#uname").val(),
        //         upass : $("#upass").val(),
        //         _csrf : csrfToken
        //      },
        data: $("#login_form").serialize(),
        type: "POST",
        dataType: 'html',
        global: false,
        async: false,
        success: function (data) {

            result = parseInt(data);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // alert(xhr.status);
            // alert(thrownError);
        }
    });

    if (result) {
        return false;
    } else {
        return true;
    }
}, 'Email id and password does not match.');

$('#login_form').submit(function() {
    if (!$("#login_form").valid()) {
        return false;
    } else {
        return true;
    }
});