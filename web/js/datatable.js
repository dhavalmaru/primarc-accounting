$('.loading').hide();   

$(document).ready(function(){
	// var div = $(".panel");
	// startAnimation();
	// function startAnimation(){
	// 	div.animate({height: '100%'}, "slow");
	// }

    $('.panel').addClass('panel_height');
	$('.loading').fadeIn(1000); 
	$('#loader').fadeOut(400);
});

$(document).ready(function() {
	var table = $('#example').DataTable({
        // lengthChange: false,
        // buttons: [ 'copy', 'excel', 'pdf',  'csv', 'print'  ]
        buttons: [ 'excel', 'csv', 'print'  ]
    });
    table.buttons().container().appendTo('#example_wrapper .col-md-6:eq(1)');

	var table = $('#example1').DataTable({
        buttons: [ 'excel', 'csv', 'print'  ]
    });
    table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(1)');

	var table = $('#example2').DataTable({
        buttons: [ 'excel', 'csv', 'print'  ]
    });
    table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(1)');

	var table = $('#example3').DataTable({
        buttons: [ 'excel', 'csv', 'print'  ]
    });
    table.buttons().container().appendTo('#example3_wrapper .col-md-6:eq(1)');

    $(".btn-group a").hide(); 
	$(".btn-group").click(function(){
        $(".btn-group").toggleClass('btn_close');
		$(".btn-group a").toggle(100);
    });
});