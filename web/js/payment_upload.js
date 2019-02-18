$('.datepicker').datepicker({changeMonth: true,changeYear: true});

$(document).ready(function() {
    $('#example_payment').DataTable({
        searching: false,
        ordering: false
    });
    $('.select2').select2();
});