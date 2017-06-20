var format_money = function(num, decimals){
    if(num==null || num==""){
        num="0";
    }

    if(num==0){
        num = "0";
    } else {
        num = num.toString();
    }

    var x=num.toString();

    var negative=false;
    if(x.indexOf('-')!=-1){
        negative=true;
    }

    var dec="";
    if(x.indexOf('.')!=-1){
        dec=x.substring(x.indexOf('.'));
        x=x.substring(0,x.indexOf('.'));
    }

    x = x.replace(/[^0-9]/g,'');
    x = x.split(",").join("");

    if(x==null || x==""){
        x="0";
    }

    if(decimals==null || decimals==""){
        decimals = 0;
    }
 
    x = x.toString() + dec.toString();
    x=parseFloat(x).toFixed(decimals);

    x=x.toString();
    dec="";
    if(x.indexOf('.')!=-1){
        dec=x.substring(x.indexOf('.'));
        x=x.substring(0,x.indexOf('.'));
    }

    var lastThree = x.substring(x.length-3);
    var otherNumbers = x.substring(0,x.length-3);
    if(otherNumbers != '') lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + dec;

    if(negative==true){
        res = '-' + res;
    }

    return res;
}

var get_number = function(num, decimals){
    if(num==null || num==""){
        num="0";
    }
    res = parseFloat(num.replaceAll(",",""));
    return res;
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

// $(function() {
//     $(".datepicker").datepicker({  maxDate: 0,changeMonth: true,yearRange:'-100:+0', changeYear: true });
//     $(".datepicker1").datepicker({ maxDate: 0,changeMonth: true,yearRange:'-100:+0',changeYear: true });
// });

// $(document).ready(function(){
//     $(".datepicker1").datepicker({ maxDate: 0,changeMonth: true,yearRange:'-100:+0',changeYear: true });
//     $('.datepicker1').attr('readonly','true');
// });

var format_num = function(num, decimals){
    if(num==null || num==""){
        num="";
    }
    num = num.toString().replace(/[^0-9]/g,'');

    // num = num.toString().replace(/[^\d\.]/g,'');
    // num = num.match(/[\d\.]+/);


    var x=num;
    x=x.toString();
    x = x.split(",").join("");
    x = parseFloat(x);
    if(isNaN(x)) x=0;
    x=x.toString();
    var lastThree = x.substring(x.length-3);
    var otherNumbers = x.substring(0,x.length-3);
    if(otherNumbers != '') lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
    return res;
}

var format_number = function(elem){
    var res = format_num(elem.value,2);
    // var res = format_money(elem.value,2);
    $(elem).val(res);
}

// var delete_row = function(elem){
//     var id = elem.attr('id');
//     id = '#'+id.substr(0,id.lastIndexOf('_'));
//     if($(id).length>0){
//         $(id).remove();
//         $(".save-form").prop("disabled",false);
//     }
// }

$(document).ready(function(){
    $('.format_number').keyup(function(){
        format_number(this);
    });

    // $('.delete_row').click(function(event){
    //     delete_row($(this));
    // });
});
