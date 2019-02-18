var table = null;
$('.loading').hide();   

$(document).ready(function(){
    $('.loading').fadeIn(1000); 
    $('#loader').fadeOut(400);

    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    $('#example').DataTable({
        // lengthChange: false,
        // buttons: [ 'copy', 'excel', 'pdf',  'csv', 'print'  ]
        "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "buttons": [ 'excel', 'csv', 'print'  ],
        "dom" : 'lBfrtip',
        "searchDelay": 3000,
        "serverSide": true,
        "bProcessing": true,
        "ajax":{
                    url :BASE_URL+'index.php?r=uploadscraping%2Fgetscrapinglog',
                    type: "post",  // type of method  ,GET/POST/DELETE
                    data: function(data) {
                        data._csrf = csrfToken;
                    },
                    "dataSrc": function ( json ) {
                        //Make your callback here.
                        // console.log(json.recordsTotal);
                        // $('.tab1primary').empty().append("Not Posted (" +json.recordsTotal+")" );
                        return json.data;
                    },
                    error: function(){
                        $("#example_processing").css("display","none");
                    }
                }
    });
});