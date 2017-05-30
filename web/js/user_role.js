function selectall(num) {
	if(num == 1 ) {
		var view_check = document.getElementById('view').checked;
		document.getElementById('account_master_vw').checked = view_check;
		document.getElementById('purchase_vw').checked = view_check;
		document.getElementById('journal_voucher_vw').checked = view_check;
		document.getElementById('payment_receipt_vw').checked = view_check;
		document.getElementById('user_roles_vw').checked = view_check;
		document.getElementById('rep_vw').checked = view_check;
	} else if(num == 2 ) {
		var insert_check = document.getElementById('insert').checked;
		document.getElementById('account_master_ins').checked = insert_check;
		document.getElementById('purchase_ins').checked = insert_check;
		document.getElementById('journal_voucher_ins').checked = insert_check;
		document.getElementById('payment_receipt_ins').checked = insert_check;
		document.getElementById('user_roles_ins').checked = insert_check;
	} else if(num == 3 ) {
		var update_check = document.getElementById('update').checked;
		document.getElementById('account_master_upd').checked = update_check;
		document.getElementById('purchase_upd').checked = update_check;
		document.getElementById('journal_voucher_upd').checked = update_check;
		document.getElementById('payment_receipt_upd').checked = update_check;
		document.getElementById('user_roles_upd').checked = update_check;
	} else if(num == 4 ) {
		var delete_check = document.getElementById('delete').checked;
		document.getElementById('account_master_del').checked = delete_check;
		document.getElementById('purchase_del').checked = delete_check;
		document.getElementById('journal_voucher_del').checked = delete_check;
		document.getElementById('payment_receipt_del').checked = delete_check;
		document.getElementById('user_roles_del').checked = delete_check;
	} else if(num == 5 ) {
		var approve_check = document.getElementById('approval').checked;
		document.getElementById('account_master_app').checked = approve_check;
		document.getElementById('purchase_app').checked = approve_check;
		document.getElementById('journal_voucher_app').checked = approve_check;
		document.getElementById('payment_receipt_app').checked = approve_check;
		document.getElementById('user_roles_app').checked = approve_check;
	} else if(num == 6 ) {
		var export_check = document.getElementById('export').checked;
		document.getElementById('account_master_exp').checked = export_check;
		document.getElementById('purchase_exp').checked = export_check;
		document.getElementById('journal_voucher_exp').checked = export_check;
		document.getElementById('payment_receipt_exp').checked = export_check;
		document.getElementById('user_roles_exp').checked = export_check;
	}
}

$(document).ready(function(){
    $(".reports").click(function(){
        $(".report-expand").slideToggle();
    });
});

$("#checkAll").change(function () {
	$('.selectreport').find('input[type=checkbox]').prop('checked', $(this).prop("checked"));
});

$('#selectall-1').change(function() {      
    $('#friendslist-1').find('input[type=checkbox]').prop('checked', $(this).prop("checked"));      
});

 $('#selectall-2').change(function() {      
    $('#friendslist-2').find('input[type=checkbox]').prop('checked', $(this).prop("checked"));      
});

 $('#selectall-3').change(function() {      
    $('#friendslist-3').find('input[type=checkbox]').prop('checked', $(this).prop("checked"));      
});

 $('#selectall-4').change(function() {      
    $('#friendslist-4').find('input[type=checkbox]').prop('checked', $(this).prop("checked"));      
});

 $('#selectall-5').change(function() {      
    $('#friendslist-5').find('input[type=checkbox]').prop('checked', $(this).prop("checked"));      
});

 $('#selectall-6').change(function() {      
    $('#friendslist-6').find('input[type=checkbox]').prop('checked', $(this).prop("checked"));      
});