 $('.loading').hide();   

$(document).ready(function(){
    // var div = $(".panel");
    // startAnimation();
    // function startAnimation(){
    //  div.animate({height: '100%'}, "slow");
    // }

    $('.panel').addClass('panel_height');
    $('.loading').fadeIn(1000); 
    $('#loader').fadeOut(400);
});

 $(document).ready(function(){

var csrfToken = $('meta[name="csrf-token"]').attr("content");

    $('#example').DataTable({
        // lengthChange: false,
        // buttons: [ 'copy', 'excel', 'pdf',  'csv', 'print'  ]
        "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "buttons": [ 'excel', 'csv', 'print'  ],
        "dom" : 'lBfrtip',
        //"serverSide": true,
        "bProcessing": true,
        "ajax":{
                    url :BASE_URL+'index.php?r=pendinggrn%2Fgetgrn',
                    type: "post",  // type of method  ,GET/POST/DELETE
                    data: function(data) {
                                            data._csrf = csrfToken;
                                        },
                    "dataSrc": function ( json ) {
                        //Make your callback here.
                        // console.log(json.recordsTotal);
                        $('.tab1primary').empty().append("Not Posted (" +json.recordsTotal+")" );
                        return json.data;
                     } ,                      
                    error: function(){
                        $("#example_processing").css("display","none");
                    }
                }
    });
    
    $('#example2').DataTable({
        // lengthChange: false,
        // buttons: [ 'copy', 'excel', 'pdf',  'csv', 'print'  ]
        "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "buttons": [ 'excel', 'csv', 'print'  ],
        "dom" : 'lBfrtip',
        "bProcessing": true,
        "serverSide": true,
        "ajax":{
                    url :BASE_URL+'index.php?r=pendinggrn%2Fgetapprovedgrn',
                    type: "post",  // type of method  ,GET/POST/DELETE
                    data: function(data) {
                                            data._csrf = csrfToken;
                                        },
                    "dataSrc": function ( json ) {
                        //Make your callback here.
                        // console.log(json.recordsTotal);
                        $('.tab3primary').empty().append("Posted (" +json.recordsTotal+")" );
                        return json.data;
                     } ,           
                    error: function(){
                        $("#example_processing").css("display","none");
                    }
                }
    });

    $('#example3').DataTable({
        // lengthChange: false,
        // buttons: [ 'copy', 'excel', 'pdf',  'csv', 'print'  ]
        "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "buttons": [ 'excel', 'csv', 'print'  ],
        "dom" : 'lBfrtip',
        "bProcessing": true,
        "serverSide": true,
        "ajax":{
                    url :BASE_URL+'index.php?r=pendinggrn%2Fgetallgrn',
                    type: "post",  // type of method  ,GET/POST/DELETE
                    data: function(data) {
                                            data._csrf = csrfToken;
                                        },
                    "dataSrc": function ( json ) {
                        //Make your callback here.
                        // console.log(json.recordsTotal);
                        $('.tab4primary').empty().append("ALL (" +json.recordsTotal+")" );
                        return json.data;
                     } ,           
                    error: function(){
                        $("#example_processing").css("display","none");
                    }
                }
    });

    $(".btn-group a").hide(); 
    $(".btn-group").click(function(){
        $(".btn-group").toggleClass('btn_close');
        $(".btn-group a").toggle(100);
    });
});
