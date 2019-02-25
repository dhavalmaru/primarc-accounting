<?php

namespace app\models;

use Yii;
use yii\base\Model;
use mPDF;
use yii\web\UploadedFile;
use phpoffice\phpexcel\Classes\PHPExcel as PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\PHPExcel_IOFactory as PHPExcel_IOFactory;
use phpoffice\phpexcel\Classes\PHPExcel\Cell\PHPExcel_Cell_DataValidation as PHPExcel_Cell_DataValidation;
use phpoffice\phpexcel\Classes\PHPExcel\Style\PHPExcel_Style_Protection as PHPExcel_Worksheet_Protection;
use phpoffice\phpexcel\Classes\PHPExcel\Style\PHPExcel_Style_NumberFormat as PHPExcel_Style_NumberFormat;

class PaymentReceipt extends Model
{
    public function getAccess() {
        $session = Yii::$app->session;
        $session_id = $session['session_id'];
        $role_id = $session['role_id'];

        $sql = "select A.*, '".$session_id."' as session_id from acc_user_role_options A 
                where A.role_id = '$role_id' and A.r_section = 'S_Payment_Receipt'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getApprover($action) {
        $session = Yii::$app->session;
        $session_id = $session['session_id'];
        $company_id = $session['company_id'];

        $cond = "";
        if($action!="authorise" && $action!="view"){
            $cond = " and A.id!='".$session_id."'";
        } 

        $sql = "select distinct A.id, A.username, C.r_approval from user A 
                left join acc_user_roles B on (A.id = B.user_id) 
                left join acc_user_role_options C on (B.role_id = C.role_id) 
                where B.company_id = '$company_id' and C.r_section = 'S_Payment_Receipt' and 
                        C.r_approval = '1' and C.r_approval is not null" . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getDetails($trans_id="", $status="") {
        $cond = "";
        if($trans_id!=""){
            $cond = " and A.id = '$trans_id'";
        }
        if($status!=""){
            if($cond==""){
                $cond = " and A.status = '$status'";
            } else {
                $cond = $cond . " and A.status = '$status'";
            }
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, B.username as updater, C.username as approver from acc_payment_receipt A 
                left join user B on (A.updated_by = B.id) 
                left join user C on (A.approved_by = C.id) where A.company_id = '$company_id' " . $cond . " 
                order by UNIX_TIMESTAMP(A.updated_date) desc, A.id desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

	public function getAccountDetails($id="" ,$acc_name='') {
        $cond = "";
        if($id!=""){
            $cond = " and id = '$id'";
        }

        if($acc_name!="")
        {
            if($cond!='')
                $cond.= " and legal_name='$acc_name'";
            else
                $cond= " and legal_name='$acc_name'";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_master where is_active = '1' and status = 'approved' and 
                company_id = '$company_id' ".$cond." order by legal_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getAccounts($to_date='') {
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        if($to_date!='')
            $cond = " and date(A.ref_date)<=date('$to_date')";

        $sql = "select distinct A.acc_id from acc_ledger_entries A left join acc_master B on (A.acc_id = B.id) 
                where A.is_active = '1' and A.status = 'approved' and A.company_id = '$company_id' and 
                    B.type in ('Vendor Goods', 'Customer', 'Marketplace') and date(A.ref_date)>date('2018-03-30') ".$cond." 
                order by A.acc_id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getPayment_upload() {
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $sql = "select A.*, B.username from acc_payment_upload A left join user B on (A.uploaded_by = B.id) 
                Where company_id='$company_id' Order By A.id desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
	
	public function getacc1($trans_type="") {
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

		$sql = "select * from acc_master where is_active = '1' and status = 'approved' and 
                type = 'Vendor Goods' and company_id = '$company_id' order by legal_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getaccbank1($trans_type="") {
        $cond = "";
        if(strtoupper(trim($trans_type))=="CONTRA ENTRY") {
            $cond = " and type = 'Bank Account' ";
        } else {
            // $cond = " and (type = 'Vendor Goods' or type = 'Customer') ";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];
		$sql = "select * from acc_master where is_active = '1' and status = 'approved' and 
                company_id = '$company_id' ".$cond." order by legal_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
	
    public function getVendors() {
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from vendor_master where is_active = '1' and company_id = '$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getBanks($id="" ,$legal_name="") {
        $cond = "";
        if($id!=""){
            $cond = " and id = '$id'";
        }

        if($legal_name!="")
        {
            if($cond!='')
                $cond.= " and legal_name='$legal_name'";
            else
                $cond= " and legal_name='$legal_name'";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_master where is_active = '1' and status = 'approved' and 
                company_id = '$company_id' and type = 'Bank Account' ".$cond." order by bank_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getLedger($id, $acc_id, $to_date='', $ref_id='') {
        $status = 'approved';

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        if($acc_id=='') $acc_id = 0;

        $cond = '';
        $cond1 = '';
        $cond2 = '';

        if($ref_id!='')
            $cond = " and A.id IN ($ref_id)";

        if($to_date!='')
            $cond1 = " and date(A.ref_date)<=date('$to_date')";

        if($to_date!='')
            $cond2 = " and date(ref_date)<=date('$to_date')";

        $sql = "select AA.* from 
                (select A.*, case when A.ref_date='2018-03-31' then A.acc_id else A.cp_acc_id end as account_id from 
                (select group_concat(A.id) as id, A.ref_id, group_concat(A.sub_ref_id) as sub_ref_id, A.ref_type, 
                    group_concat(A.entry_type) as entry_type, A.invoice_no, A.vendor_id, 
                    group_concat(A.acc_id) as acc_id, group_concat(A.ledger_name) as ledger_name, group_concat(A.ledger_code) as ledger_code, 
                    A.type, sum(A.amount) as amount, sum(A.paid_amount) as paid_amount, sum(A.pending_paid_amount) as pending_paid_amount, 
                    sum(A.total_paid_amount) as total_paid_amount, sum(A.amount_to_pay) as amount_to_pay, sum(A.bal_amount) as bal_amount, 
                    A.status, A.voucher_id, A.ledger_type, A.ref_date, A.gi_date, A.invoice_date, A.due_date, 
                    A.cp_acc_id, A.cp_ledger_name, A.cp_ledger_code from 
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, 
                    A.acc_id, A.ledger_name, A.ledger_code, A.type, A.amount, A.paid_amount, A.pending_paid_amount, 
                    A.total_paid_amount, A.amount_to_pay, A.bal_amount, A.status, A.created_by, A.updated_by, 
                    A.created_date, A.updated_date, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, A.gi_date, A.invoice_date, A.due_date, 
                    A.cp_acc_id, A.cp_ledger_name, A.cp_ledger_code, 
                    case when (instr(A.ledger_name,'-CGST')!=0 or instr(A.ledger_name,'-SGST')!=0 or 
                        instr(A.ledger_name,'-IGST')!=0 or instr(A.ledger_name,'Purchase-')!=0 or 
                        instr(A.ledger_name,'Sales-')!=0) then A.new_ledger_name 
                        else A.ledger_name end as new_ledger_name from 
                (select AA.*, (AA.paid_amount+AA.pending_paid_amount) as total_paid_amount, 
                    round((AA.amount-AA.paid_amount-AA.pending_paid_amount),2) as bal_amount, 
                    replace(replace(replace(replace(replace(replace(replace(replace(replace(AA.ledger_name,'Input-','Purchase-'),
                        'Output-','Sales-'),'CGST','Local'),'SGST','Local'),'IGST','Inter State'),'-B2B',''),'-B2C',''),'--',''),
                        Substring_index(AA.ledger_name, '-', -1),'') as new_ledger_name, 
                    case when (instr(AA.ledger_name,'-CGST')!=0 or instr(AA.ledger_name,'-SGST')!=0) 
                    then (2 * Replace(Substring_index(AA.ledger_name, '-', -1),'%', '')) 
                    else Replace(Substring_index(AA.ledger_name, '-', -1), '%', '') end as percentage from 
                (

                select A.*, ifnull(C.paid_amount,0) as paid_amount, ifnull(C.pending_paid_amount,0) as pending_paid_amount, 
                    ifnull(C.amount_to_pay,0) as amount_to_pay from 
                    
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.gi_date, C.invoice_date, D.due_date from acc_ledger_entries A 
                    left join grn B on(A.ref_id = B.grn_id and A.ref_type = 'purchase') 
                    left join goods_inward_outward_invoices C on(A.invoice_no = C.invoice_no and A.ref_type = 'purchase' and B.gi_id = C.gi_go_ref_no) 
                    left join invoice_tracker D on(A.ref_id=D.grn_id and C.gi_go_invoice_id=D.invoice_id) 
                    where A.status = '$status' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and 
                        A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id' and B.company_id = '$company_id' ".$cond1.") A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and acc_id in ($acc_id) and 
                    date(ref_date)>date('2018-04-01') and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id' ".$cond2.") B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    A.gi_date, A.invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                    left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                    left join acc_jv_invoices_entries C 
                    on (A.ref_id=C.jv_details_id and A.sub_ref_id=C.jv_entries_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                    where A.status = '$status' and A.is_active = '1' and date(A.ref_date)>date('2018-04-01') and 
                        A.ref_type = 'journal_voucher' and A.company_id = '$company_id' ".$cond1.") A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date)>date('2018-04-01') and ref_type = 'journal_voucher' and acc_id in ($acc_id) and company_id = '$company_id' ".$cond2.") B 
                on (A.ref_id=B.ref_id) 
                where (A.acc_id<>B.cp_acc_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    null as gi_date, null as invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and date(ref_date)>date('2018-04-01') and 
                    ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '$company_id' ".$cond2.") A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and acc_id in ($acc_id) and 
                    date(ref_date)>date('2018-04-01') and ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '$company_id' ".$cond2.") B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                    from acc_ledger_entries A 
                    left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.is_active = '1' and 
                        date(A.ref_date)>date('2018-04-01') and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and 
                        A.company_id = '$company_id' and B.company_id = '$company_id' ".$cond1.") A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and acc_id in ($acc_id) and 
                    date(ref_date)>date('2018-04-01') and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '$company_id' ".$cond2.") B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                    from acc_ledger_entries A 
                    left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and 
                        A.ref_type = 'go_debit_details' and A.ledger_type = 'Main Entry' and 
                        A.company_id = '$company_id' and B.company_id = '$company_id' and 
                        A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer') ".$cond1.") A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and date(ref_date)>date('2018-04-01') and 
                    ref_type = 'go_debit_details' and acc_id in ($acc_id) and ledger_type = 'Main Entry' and company_id = '$company_id' and 
                    entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer') ".$cond2.") B 
                on(A.ref_id=B.ref_id) 
                where (A.acc_id<>B.cp_acc_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    null as gi_date, null as invoice_date, null as due_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and date(ref_date)>date('2018-04-01') and 
                    ref_type = 'other_debit_credit' and company_id = '$company_id' ".$cond2.") A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and date(ref_date)>date('2018-04-01') and 
                    ref_type = 'other_debit_credit' and acc_id in ($acc_id) and company_id = '$company_id' ".$cond2.") B 
                on (A.ref_id=B.ref_id) 
                where (A.acc_id<>B.acc_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    null as gi_date, null as invoice_date, null as due_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and date(ref_date)>date('2018-04-01') and 
                    ref_type = 'promotion' and company_id = '$company_id' ".$cond2.") A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and date(ref_date)>date('2018-04-01') and 
                    ref_type = 'promotion' and acc_id in ($acc_id) and company_id = '$company_id' ".$cond2.") B 
                on (A.ref_id=B.ref_id) 
                where (A.acc_id<>B.acc_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    null as gi_date, null as invoice_date, null as due_date, 
                    B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and date(ref_date)>date('2018-04-01') and 
                    ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '$company_id' ".$cond2.") A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and acc_id in ($acc_id) and 
                    date(ref_date)>date('2018-04-01') and ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '$company_id' ".$cond2.") B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.date_of_upload as gi_date, C.invoice_date, null as due_date from acc_ledger_entries A 
                    left join acc_sales_files B on(A.ref_id = B.id and A.ref_type = 'sales_upload') 
                    left join 
                    (select distinct ref_file_id, invoice_no, invoice_date from acc_sales_file_items where company_id='$company_id' and is_active='1') C 
                    on(A.invoice_no = C.invoice_no and A.ref_type = 'sales_upload' and A.ref_id = C.ref_file_id) 
                    where A.status = '$status' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and 
                        A.ref_type = 'sales_upload' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id' and B.company_id = '$company_id' ".$cond1.") A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and acc_id in ($acc_id) and 
                    date(ref_date)>date('2018-04-01') and ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '$company_id' ".$cond2.") B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, A.type, A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    A.gi_date, A.invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                    left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                    left join acc_jv_invoices_entries C 
                    on (A.ref_id=C.jv_details_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                    where A.status = '$status' and A.is_active = '1' and date(A.ref_date)<date('2018-04-01') and date(A.ref_date)>date('2018-03-30') and 
                        A.ref_type = 'journal_voucher' and A.acc_id in ($acc_id) and A.company_id = '$company_id') A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date)<date('2018-04-01') and date(ref_date)>date('2018-03-30') and ref_type = 'journal_voucher' and 
                    acc_id='716' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id)) A 

                left join 

                (select sub_ref_id, sum(case when (ref_id != '$id' and status = 'approved') then amount else 0 end) as paid_amount, 
                    sum(case when (ref_id != '$id' and status = 'pending') then amount else 0 end) as pending_paid_amount, 
                    sum(case when ref_id = '$id' then amount else 0 end) as amount_to_pay from acc_ledger_entries 
                    where is_active = '1' and company_id = '$company_id' and sub_ref_id is not null and 
                        date(ref_date)>date('2018-04-01') and ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' ".$cond2." group by sub_ref_id) C 
                on (A.id = C.sub_ref_id) 

                ) AA 
                where (AA.acc_id in ($acc_id) or AA.cp_acc_id in ($acc_id)) and AA.amount!=0 and 
                        (AA.ref_type!='payment_receipt' or AA.entry_type='Bank Entry' or AA.entry_type='Payment' or AA.entry_type='Receipt')) A 
                where A.bal_amount!=0 ".$cond.") A 
                group by A.ref_id, A.ref_type, A.invoice_no, A.vendor_id, A.type, A.status, A.voucher_id, A.ledger_type, A.ref_date, 
                    A.gi_date, A.invoice_date, A.due_date, A.cp_acc_id, A.cp_ledger_name, A.cp_ledger_code) A) AA 
                order by AA.account_id, AA.ref_date";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function get_paid_amount($sub_ref_id='',$invoice_no='') {
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $sql = "select sum(amount) as paid_amount from acc_ledger_entries 
                Where sub_ref_id IN ($sub_ref_id) and is_active = '1' and ref_type='payment' and  company_id = '$company_id' ORDER BY id desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function save() {
        $request = Yii::$app->request;

        $action = $request->post('action');
        if($action=="authorise"){
            if($request->post('btn_reject')!==null){
                $action = "reject";
            } else {
                $action = "approve";
            }
        }

        if($action=="edit" || $action=="insert"){
            $this->saveEdit();
        } else if($action=="approve"){
            $this->authorise("approved");
        } else if($action=="reject"){
            $this->authorise("rejected");
        }
        
        return true;
    }

    public function saveEdit() {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $id = $request->post('id');
        $voucher_id = $request->post('voucher_id');
        $ledger_type = $request->post('ledger_type');
        $trans_type = $request->post('trans_type');
        $acc_id = $request->post('acc_id');
        $legal_name = $request->post('legal_name');
        $acc_code = $request->post('acc_code');
        $acc_code1 = $request->post('acc_code1');
        $bank_id = $request->post('bank_id');
        $bank_name = $request->post('bank_name');
        $payment_type = $request->post('payment_type');
        $narration = $request->post('narration');
        $payment_date = $request->post('payment_date');
        $remarks = $request->post('remarks');
        $approver_id = $request->post('approver_id');
        
        if($payment_date==''){
            $payment_date=NULL;
        } else {
            $payment_date=$mycomponent->formatdate($payment_date);
        }
        $ref_no = $request->post('ref_no');

        $amount = 0;
        if(strtoupper(trim($payment_type)) == "ADHOC"){
            $amount = $mycomponent->format_number($request->post('amount'),2);
            $paying_transaction = 'Credit';
        } else {
            $paying_transaction = $request->post('paying_transaction');
            $paying_amount_total = $mycomponent->format_number($request->post('paying_amount_total'),2);

            if(strtoupper(trim($paying_transaction))=='DEBIT'){
                // $amount = $paying_amount_total*-1;
                $amount = $paying_amount_total;
            } else {
                $amount = $paying_amount_total;
            }
        }

        $transaction_id = "";

        if(!isset($voucher_id) || $voucher_id==''){
            $series = 1;
            $sql = "select * from acc_series_master where type = 'Voucher' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $data = $reader->readAll();
            if (count($data)>0){
                $series = intval($data[0]['series']) + 1;

                $sql = "update acc_series_master set series = '$series' where type = 'Voucher' and company_id = '$company_id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            } else {
                $series = 1;

                $sql = "insert into acc_series_master (type, series, company_id) values ('Voucher', '".$series."', '".$company_id."')";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }

            $voucher_id = $series;
        }
        
        $array=[
                'trans_type'=>$trans_type,
                'voucher_id' => $voucher_id, 
                'ledger_type' => 'Sub Entry', 
                'account_id'=>$acc_id,
                'account_name'=>$legal_name,
                'account_code'=>$acc_code,
                'account_code1'=>$acc_code1,
                'bank_id'=>$bank_id,
                'bank_name'=>$bank_name,
                'payment_type'=>$payment_type,
                'amount'=>((strtoupper(trim($paying_transaction))=='DEBIT')?$amount*-1:$amount),
                'ref_no'=>$ref_no,
                'narration'=>$narration,
                'status'=>'pending',
                'is_active'=>'1',
                'updated_by'=>$curusr,
                'updated_date'=>$now,
                'payment_date'=>$payment_date,
                'approver_comments'=>$remarks,
                'approver_id'=>$approver_id,
                'company_id'=>$company_id
            ];

        if (isset($id) && $id!=""){
            $count = Yii::$app->db->createCommand()
                        ->update("acc_payment_receipt", $array, "id = '".$id."'")
                        ->execute();

            $this->setLog('PaymentReceipt', '', 'Save', '', 'Update Payment Receipt Details', 'acc_payment_receipt', $id);
        } else {
            $array['created_by']=$curusr;
            $array['created_date']=$now;

            $count = Yii::$app->db->createCommand()
                        ->insert("acc_payment_receipt", $array)
                        ->execute();
            $id = Yii::$app->db->getLastInsertID();

            $this->setLog('PaymentReceipt', '', 'Save', '', 'Insert Payment Receipt Details', 'acc_payment_receipt', $id);
        }

        if(strtoupper(trim($payment_type)) == "ADHOC") {
            $data = $this->getAccountDetails($acc_id);
            if(count($data)>0){
                $vendor_id = $data[0]['vendor_id'];
            }
            if(strtoupper(trim($trans_type))=="PAYMENT"){
                $type = "Debit";
            } else {
                $type = "Credit";
            }

            $ledgerArray=[
                            'ref_id'=>$id,
                            'sub_ref_id'=>null,
                            'ref_type'=>'payment_receipt',
                            'entry_type'=>$trans_type,
                            'invoice_no'=>$ref_no,
                            'vendor_id'=>$vendor_id,
                            'voucher_id' => $voucher_id, 
                            'ledger_type' => 'Main Entry', 
                            'acc_id'=>$acc_id,
                            'ledger_name'=>$legal_name,
                            'ledger_code'=>$acc_code,
                            'type'=>$type,
                            'amount'=>$amount,
                            'narration'=>$narration,
                            'status'=>'pending',
                            'is_active'=>'1',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'ref_date'=>$payment_date,
                            'approver_comments'=>$remarks,
                            'company_id'=>$company_id
                        ];

            $count = Yii::$app->db->createCommand()
                        ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and ref_type = 'payment_receipt' and 
                                    entry_type = '".$trans_type."' and voucher_id = '".$voucher_id."'")
                        ->execute();

            if ($count==0){
                $ledgerArray['created_by']=$curusr;
                $ledgerArray['created_date']=$now;

                $count = Yii::$app->db->createCommand()
                            ->insert("acc_ledger_entries", $ledgerArray)
                            ->execute();
            }

            $data = $this->getBanks($bank_id);
            if(count($data)>0){
                $bank_legal_name = $data[0]['legal_name'];
                $bank_acc_code = $data[0]['code'];
            } else {
                $bank_legal_name = '';
                $bank_acc_code = '';
            }
            if(strtoupper(trim($trans_type))=="PAYMENT"){
                $type = "Credit";
            } else {
                $type = "Debit";
            }
            $ledgerArray=[
                            'ref_id'=>$id,
                            'sub_ref_id'=>null,
                            'ref_type'=>'payment_receipt',
                            'entry_type'=>'Bank Entry',
                            'invoice_no'=>$ref_no,
                            'vendor_id'=>null,
                            'voucher_id' => $voucher_id, 
                            'ledger_type' => 'Sub Entry', 
                            'acc_id'=>$bank_id,
                            'ledger_name'=>$bank_legal_name,
                            'ledger_code'=>$bank_acc_code,
                            'type'=>$type,
                            'amount'=>$amount,
                            'narration'=>$narration,
                            'status'=>'pending',
                            'is_active'=>'1',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'ref_date'=>$payment_date,
                            'approver_comments'=>$remarks,
                            'company_id'=>$company_id
                        ];

            $count = Yii::$app->db->createCommand()
                        ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and ref_type = 'payment_receipt' and 
                                    entry_type = 'Bank Entry' and voucher_id = '".$voucher_id."'")
                        ->execute();

            if ($count==0){
                $ledgerArray['created_by']=$curusr;
                $ledgerArray['created_date']=$now;

                $count = Yii::$app->db->createCommand()
                            ->insert("acc_ledger_entries", $ledgerArray)
                            ->execute();
            }
        } else {
            $chk = $request->post('chk');
            $ledger_id = $request->post('ledger_id');
            $ledger_type = $request->post('ledger_type');
            // $debit_amt = $request->post('debit_amt');
            // $credit_amt = $request->post('credit_amt');
            $invoice_no = $request->post('invoice_no');
            $vendor_id = $request->post('vendor_id');
            $transaction = $request->post('transaction');
            $amount_to_pay = $request->post('amount_to_pay');
            $total_amount = $request->post('total_amount');
            $total_paid_amount = $request->post('total_paid_amount');

            if (isset($ledger_id)){
                for($i=0; $i<count($ledger_id); $i++){
                    if($amount_to_pay[$i]!="" && $amount_to_pay[$i]!="0" && $amount_to_pay[$i]!=null){
                        $type = $transaction[$i];
                        $amt = $mycomponent->format_number($amount_to_pay[$i],2);
                        $tot_amt = $mycomponent->format_number($total_amount[$i],2);
                        $tot_paid_amt = $mycomponent->format_number($total_paid_amount[$i],2);
                        if(strtoupper(trim($type))=='DEBIT'){
                            $amt = $amt * -1;
                            $tot_amt = $tot_amt * -1;
                            $tot_paid_amt = $tot_paid_amt * -1;
                        }
                        $tot_bal_amt = $tot_amt - $tot_paid_amt;

                        $led_id = explode(',', $ledger_id[$i]);

                        for($j=0; $j<count($led_id); $j++){
                            $led_id[$j] = trim($led_id[$j]);
                            $led_amt = $amt;

                            if($led_id[$j]!="" && $led_id[$j]!=null){
                                $sql = "select A.*, B.paid_amount, B.pending_paid_amount, B.amount_to_pay from 
                                    (select * from acc_ledger_entries where id = '".$led_id[$j]."') A 
                                    left join 
                                    (select sub_ref_id, sum(case when (ref_id != '$id' and status = 'approved') then amount else 0 end) as paid_amount, 
                                        sum(case when (ref_id != '$id' and status = 'pending') then amount else 0 end) as pending_paid_amount, 
                                        sum(case when ref_id = '$id' then amount else 0 end) as amount_to_pay from acc_ledger_entries 
                                    where is_active = '1' and company_id = '$company_id' and sub_ref_id is not null and date(ref_date)>date('2018-04-01') and 
                                        ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and sub_ref_id = '".$led_id[$j]."' 
                                    group by sub_ref_id) B 
                                    on (A.id = B.sub_ref_id)";
                                $command = Yii::$app->db->createCommand($sql);
                                $reader = $command->query();
                                $result = $reader->readAll();

                                if(count($result)>0){
                                    $led_acc_name = $result[0]['ledger_name'];
                                    $led_amount = $result[0]['amount'];
                                    $led_per = 0;

                                    $paid_amount = 0;
                                    if(isset($result[0]['paid_amount'])){
                                        if($result[0]['paid_amount']!=''){
                                            $paid_amount = $result[0]['paid_amount'];
                                        }
                                    }
                                    $pending_paid_amount = 0;
                                    if(isset($result[0]['pending_paid_amount'])){
                                        if($result[0]['pending_paid_amount']!=''){
                                            $pending_paid_amount = $result[0]['pending_paid_amount'];
                                        }
                                    }
                                    $tot_paid_amount = $paid_amount + $pending_paid_amount;

                                    // if(strpos($led_acc_name, 'Purchase-')!==false || strpos($led_acc_name, 'Sales-')!==false || 
                                    //    strpos($led_acc_name, 'CGST-')!==false || strpos($led_acc_name, 'SGST-')!==false || 
                                    //    strpos($led_acc_name, 'IGST-')!==false){
                                    //     // $led_per = substr($led_acc_name, strrpos($led_acc_name, '-')+1);
                                    //     // $led_per = str_replace('%', '', $led_per);
                                    //     // $led_per = floatval($led_per);

                                    //     // if(strpos($led_acc_name, 'CGST-')!==false || strpos($led_acc_name, 'SGST-')!==false){
                                    //     //     $led_amt = $amt/(1+(($led_per*2)/100));
                                    //     //     $led_amt = round($led_amt*$led_per/100, 4);
                                    //     // } else if(strpos($led_acc_name, 'IGST-')!==false){
                                    //     //     $led_amt = $amt/(1+($led_per/100));
                                    //     //     $led_amt = round($led_amt*$led_per/100, 4);
                                    //     // } else {
                                    //     //     $led_amt = round($amt/(1+($led_per/100)), 4);
                                    //     // }

                                    //     $led_amt = $led_amount - $tot_paid_amount;
                                    //     echo $led_amt;
                                    //     echo '<br/>';
                                    //     $led_amt = round($led_amt*$amt/$tot_bal_amt,4);

                                    //     echo $amt;
                                    //     echo '<br/>';
                                    //     echo $tot_bal_amt;
                                    //     echo '<br/>';

                                    //     // $led_amt_diff = $led_amount - $led_amt;
                                    //     // if($led_amt_diff<0.5 && $led_amt_diff>-0.5){
                                    //     //     $led_amt = $led_amount;
                                    //     // }
                                    // }

                                    $led_amt = $led_amount - $tot_paid_amount;
                                    $led_amt = round($led_amt*$amt/$tot_bal_amt,4);

                                    // echo $led_acc_name;
                                    // echo '<br/>';
                                    // echo $led_per;
                                    // echo '<br/>';
                                    // echo $led_amt;
                                    // echo '<br/>';
                                }

                                $ledgerArray=[
                                                'ref_id'=>$id,
                                                'sub_ref_id'=>$led_id[$j],
                                                'ref_type'=>'payment_receipt',
                                                'entry_type'=>$ledger_type[$i],
                                                'invoice_no'=>$invoice_no[$i],
                                                'vendor_id'=>$vendor_id[$i],
                                                'voucher_id' => $voucher_id, 
                                                'ledger_type' => 'Sub Entry', 
                                                'acc_id'=>$acc_id,
                                                'ledger_name'=>$legal_name,
                                                'ledger_code'=>$acc_code,
                                                'type'=>$type,
                                                'amount'=>$led_amt,
                                                'narration'=>$narration,
                                                'status'=>'pending',
                                                'is_active'=>'1',
                                                'updated_by'=>$curusr,
                                                'updated_date'=>$now,
                                                'ref_date'=>$payment_date,
                                                'approver_comments'=>$remarks,
                                                'company_id'=>$company_id
                                            ];

                                $count = Yii::$app->db->createCommand()
                                            ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and sub_ref_id = '".$led_id[$j]."' and ref_type = 'payment_receipt'")
                                            ->execute();

                                if ($count==0){
                                    $ledgerArray['created_by']=$curusr;
                                    $ledgerArray['created_date']=$now;

                                    $count = Yii::$app->db->createCommand()
                                                ->insert("acc_ledger_entries", $ledgerArray)
                                                ->execute();
                                }

                                // echo json_encode($ledgerArray);
                                // echo '<br/>';
                            }
                        }
                    } else {
                        if($ledger_id[$i]!="" && $ledger_id[$i]!=null){
                            $count = Yii::$app->db->createCommand()
                                    ->delete("acc_ledger_entries", "ref_id = '".$id."' and 
                                                sub_ref_id in (".$ledger_id[$i].") and 
                                                ref_type = 'payment_receipt'")
                                    ->execute();
                        }
                    }
                }
            }

            $data = $this->getBanks($bank_id);
            if(count($data)>0){
                $bank_legal_name = $data[0]['legal_name'];
                $bank_acc_code = $data[0]['code'];
            } else {
                $bank_legal_name = '';
                $bank_acc_code = '';
            }
            if($amount>0){
                $type = 'Credit';
                $amount = $amount;
            } else {
                $type = 'Debit';
                $amount = $amount*-1;
            }
            $ledgerArray=[
                            'ref_id'=>$id,
                            'sub_ref_id'=>null,
                            'ref_type'=>'payment_receipt',
                            'entry_type'=>'Bank Entry',
                            'invoice_no'=>$ref_no,
                            'vendor_id'=>null,
                            'voucher_id' => $voucher_id, 
                            'ledger_type' => 'Main Entry', 
                            'acc_id'=>$bank_id,
                            'ledger_name'=>$bank_legal_name,
                            'ledger_code'=>$bank_acc_code,
                            'type'=>$type,
                            'amount'=>$amount,
                            'narration'=>$narration,
                            'status'=>'pending',
                            'is_active'=>'1',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'ref_date'=>$payment_date,
                            'payment_ref'=>$id,
                            'approver_comments'=>$remarks,
                            'company_id'=>$company_id
                        ];

            $count = Yii::$app->db->createCommand()
                        ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and ref_type = 'payment_receipt' and entry_type = 'Bank Entry'")
                        ->execute();

            if ($count==0){
                $ledgerArray['created_by']=$curusr;
                $ledgerArray['created_date']=$now;

                $count = Yii::$app->db->createCommand()
                            ->insert("acc_ledger_entries", $ledgerArray)
                            ->execute();
            }
        }

        $this->authorise('approved', $id, $voucher_id);
        
        return true;
    }

    public function saveExcelreceipt($value='') {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        
        $ledger_type = $request->post('ledger_type');

        $voucher_id = $objPHPExcel[0][0]['Voucher id'];
        $trans_type = $objPHPExcel[0][0]['Type (Payment / Receipt)'];
        $acc_id = $acc_name[0]['id'];
        $legal_name = $objPHPExcel[0][0]['Account name'];
        $acc_code = $objPHPExcel[0][0]['Account code'];
        $acc_code1 = $acc_code;
        $bank_id = $bank[0]['id'];
        $bank_name = $objPHPExcel[0][0]['Bank name'];
        $payment_type = 'Knock off';
        $narration = $objPHPExcel[0][0]['Narration'];
        $payment_date = date("Y-m-d");
        $remarks = '';
        $approver_id = $curusr;
        $payment_date=$payment_date;
        $ref_no = 0;
        $sum_amount = 0;
        $paying_amount_total = 0;
        $paying_transaction = '';


        if(strtoupper(trim($paying_transaction))=='DEBIT'){
            // $amount = $paying_amount_total*-1;
            $amount = $paying_amount_total;
        } else {
            $amount = $paying_amount_total;
        }

        $array['created_by']=$curusr;
        $array['created_date']=$now;

        $count = Yii::$app->db->createCommand()
                    ->insert("acc_payment_receipt", $array)
                    ->execute();
        $id = Yii::$app->db->getLastInsertID();

        if (isset($id) && $id!=""){
            $count = Yii::$app->db->createCommand()
                        ->update("acc_payment_receipt", $array, "id = '".$id."'")
                        ->execute();

            $this->setLog('PaymentReceipt', '', 'Save', '', 'Update Payment Receipt Details', 'acc_payment_receipt', $id);
        } else {
            $array['created_by']=$curusr;
            $array['created_date']=$now;

            $count = Yii::$app->db->createCommand()
                        ->insert("acc_payment_receipt", $array)
                        ->execute();
            $id = Yii::$app->db->getLastInsertID();

            $this->setLog('PaymentReceipt', '', 'Save', '', 'Insert Payment Receipt Details', 'acc_payment_receipt', $id);
        }

        $ledger_id = $request->post('ledger_id');
        $ledger_type = $request->post('ledger_type');
        $invoice_no = $request->post('invoice_no');
        $vendor_id = $request->post('vendor_id');
        $transaction = $request->post('transaction');
        $amount_to_pay = $request->post('amount_to_pay');
        $total_amount = $request->post('total_amount');
        $total_paid_amount = $request->post('total_paid_amount');

        if (isset($ledger_id)){
            for($i=0; $i<count($ledger_id); $i++){
                if($amount_to_pay[$i]!="" && $amount_to_pay[$i]!="0" && $amount_to_pay[$i]!=null){
                    $type = $transaction[$i];
                    $amt = $mycomponent->format_number($amount_to_pay[$i],2);
                    $tot_amt = $mycomponent->format_number($total_amount[$i],2);
                    $tot_paid_amt = $mycomponent->format_number($total_paid_amount[$i],2);
                    if(strtoupper(trim($type))=='DEBIT'){
                        $amt = $amt * -1;
                        $tot_amt = $tot_amt * -1;
                        $tot_paid_amt = $tot_paid_amt * -1;
                    }
                    $tot_bal_amt = $tot_amt - $tot_paid_amt;

                    $led_id = explode(',', $ledger_id[$i]);

                    for($j=0; $j<count($led_id); $j++){
                        $led_id[$j] = trim($led_id[$j]);
                        $led_amt = $amt;

                        if($led_id[$j]!="" && $led_id[$j]!=null){
                            $sql = "select A.*, B.paid_amount, B.pending_paid_amount, B.amount_to_pay from 
                                (select * from acc_ledger_entries where id = '".$led_id[$j]."') A 
                                left join 
                                (select sub_ref_id, sum(case when (ref_id != '$id' and status = 'approved') then amount else 0 end) as paid_amount, 
                                    sum(case when (ref_id != '$id' and status = 'pending') then amount else 0 end) as pending_paid_amount, 
                                    sum(case when ref_id = '$id' then amount else 0 end) as amount_to_pay from acc_ledger_entries 
                                where is_active = '1' and company_id = '$company_id' and sub_ref_id is not null and date(ref_date)>date('2018-04-01') and 
                                    ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and sub_ref_id = '".$led_id[$j]."' 
                                group by sub_ref_id) B 
                                on (A.id = B.sub_ref_id)";
                            $command = Yii::$app->db->createCommand($sql);
                            $reader = $command->query();
                            $result = $reader->readAll();

                            if(count($result)>0){
                                $led_acc_name = $result[0]['ledger_name'];
                                $led_amount = $result[0]['amount'];
                                $led_per = 0;

                                $paid_amount = 0;
                                if(isset($result[0]['paid_amount'])){
                                    if($result[0]['paid_amount']!=''){
                                        $paid_amount = $result[0]['paid_amount'];
                                    }
                                }
                                $pending_paid_amount = 0;
                                if(isset($result[0]['pending_paid_amount'])){
                                    if($result[0]['pending_paid_amount']!=''){
                                        $pending_paid_amount = $result[0]['pending_paid_amount'];
                                    }
                                }
                                $tot_paid_amount = $paid_amount + $pending_paid_amount;

                                // if(strpos($led_acc_name, 'Purchase-')!==false || strpos($led_acc_name, 'Sales-')!==false || 
                                //    strpos($led_acc_name, 'CGST-')!==false || strpos($led_acc_name, 'SGST-')!==false || 
                                //    strpos($led_acc_name, 'IGST-')!==false){
                                //     // $led_per = substr($led_acc_name, strrpos($led_acc_name, '-')+1);
                                //     // $led_per = str_replace('%', '', $led_per);
                                //     // $led_per = floatval($led_per);

                                //     // if(strpos($led_acc_name, 'CGST-')!==false || strpos($led_acc_name, 'SGST-')!==false){
                                //     //     $led_amt = $amt/(1+(($led_per*2)/100));
                                //     //     $led_amt = round($led_amt*$led_per/100, 4);
                                //     // } else if(strpos($led_acc_name, 'IGST-')!==false){
                                //     //     $led_amt = $amt/(1+($led_per/100));
                                //     //     $led_amt = round($led_amt*$led_per/100, 4);
                                //     // } else {
                                //     //     $led_amt = round($amt/(1+($led_per/100)), 4);
                                //     // }

                                //     $led_amt = $led_amount - $tot_paid_amount;
                                //     echo $led_amt;
                                //     echo '<br/>';
                                //     $led_amt = round($led_amt*$amt/$tot_bal_amt,4);

                                //     echo $amt;
                                //     echo '<br/>';
                                //     echo $tot_bal_amt;
                                //     echo '<br/>';

                                //     // $led_amt_diff = $led_amount - $led_amt;
                                //     // if($led_amt_diff<0.5 && $led_amt_diff>-0.5){
                                //     //     $led_amt = $led_amount;
                                //     // }
                                // }

                                $led_amt = $led_amount - $tot_paid_amount;
                                $led_amt = round($led_amt*$amt/$tot_bal_amt,4);

                                // echo $led_acc_name;
                                // echo '<br/>';
                                // echo $led_per;
                                // echo '<br/>';
                                // echo $led_amt;
                                // echo '<br/>';
                            }

                            $ledgerArray=[
                                            'ref_id'=>$id,
                                            'sub_ref_id'=>$led_id[$j],
                                            'ref_type'=>'payment_receipt',
                                            'entry_type'=>$ledger_type[$i],
                                            'invoice_no'=>$invoice_no[$i],
                                            'vendor_id'=>$vendor_id[$i],
                                            'voucher_id' => $voucher_id, 
                                            'ledger_type' => 'Sub Entry', 
                                            'acc_id'=>$acc_id,
                                            'ledger_name'=>$legal_name,
                                            'ledger_code'=>$acc_code,
                                            'type'=>$type,
                                            'amount'=>$led_amt,
                                            'narration'=>$narration,
                                            'status'=>'pending',
                                            'is_active'=>'1',
                                            'updated_by'=>$curusr,
                                            'updated_date'=>$now,
                                            'ref_date'=>$payment_date,
                                            'approver_comments'=>$remarks,
                                            'company_id'=>$company_id
                                        ];

                            $count = Yii::$app->db->createCommand()
                                        ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and sub_ref_id = '".$led_id[$j]."' and ref_type = 'payment_receipt'")
                                        ->execute();

                            if ($count==0){
                                $ledgerArray['created_by']=$curusr;
                                $ledgerArray['created_date']=$now;

                                $count = Yii::$app->db->createCommand()
                                            ->insert("acc_ledger_entries", $ledgerArray)
                                            ->execute();
                            }

                            // echo json_encode($ledgerArray);
                            // echo '<br/>';
                        }
                    }
                } else {
                    if($ledger_id[$i]!="" && $ledger_id[$i]!=null){
                        $count = Yii::$app->db->createCommand()
                                ->delete("acc_ledger_entries", "ref_id = '".$id."' and 
                                            sub_ref_id in (".$ledger_id[$i].") and 
                                            ref_type = 'payment_receipt'")
                                ->execute();
                    }
                }
            }
        }

        $data = $this->getBanks($bank_id);
        if(count($data)>0){
            $bank_legal_name = $data[0]['legal_name'];
            $bank_acc_code = $data[0]['code'];
        } else {
            $bank_legal_name = '';
            $bank_acc_code = '';
        }
        if($amount>0){
            $type = 'Credit';
            $amount = $amount;
        } else {
            $type = 'Debit';
            $amount = $amount*-1;
        }
        $ledgerArray=[
                        'ref_id'=>$id,
                        'sub_ref_id'=>null,
                        'ref_type'=>'payment_receipt',
                        'entry_type'=>'Bank Entry',
                        'invoice_no'=>$ref_no,
                        'vendor_id'=>null,
                        'voucher_id' => $voucher_id, 
                        'ledger_type' => 'Main Entry', 
                        'acc_id'=>$bank_id,
                        'ledger_name'=>$bank_legal_name,
                        'ledger_code'=>$bank_acc_code,
                        'type'=>$type,
                        'amount'=>$amount,
                        'narration'=>$narration,
                        'status'=>'pending',
                        'is_active'=>'1',
                        'updated_by'=>$curusr,
                        'updated_date'=>$now,
                        'ref_date'=>$payment_date,
                        'payment_ref'=>$id,
                        'approver_comments'=>$remarks,
                        'company_id'=>$company_id
                    ];

        $count = Yii::$app->db->createCommand()
                    ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and ref_type = 'payment_receipt' and entry_type = 'Bank Entry'")
                    ->execute();

        if ($count==0){
            $ledgerArray['created_by']=$curusr;
            $ledgerArray['created_date']=$now;

            $count = Yii::$app->db->createCommand()
                        ->insert("acc_ledger_entries", $ledgerArray)
                        ->execute();
        }
    }

    public function authorise($status, $id, $voucher_id) {
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        // $id = $request->post('id');
        // $voucher_id = $request->post('voucher_id');
        $remarks = $request->post('remarks');
        // $payment_type = $request->post('payment_type');

        $sql = "select * from acc_payment_receipt where id = '$id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $result = $reader->readAll();
        if(count($result)>0){
            $payment_type = $result[0]['payment_type'];
        } else {
            $payment_type = '';
        }

        $array = array('status' => $status, 
                        'approved_by' => $curusr, 
                        'approved_date' => $now,
                        'approver_comments'=>$remarks);

        $count = Yii::$app->db->createCommand()
                        ->update("acc_payment_receipt", $array, "id = '".$id."'")
                        ->execute();

        $count = Yii::$app->db->createCommand()
                        ->update("acc_ledger_entries", $array, "ref_id = '".$id."' and ref_type = 'payment_receipt' and 
                                    voucher_id = '".$voucher_id."'")
                        ->execute();

        if($status=='approved'){
            if(strtoupper(trim($payment_type))=='KNOCK OFF'){
                $sql = "select group_concat(distinct sub_ref_id) as pay_id from acc_ledger_entries 
                        where ref_id = '".$id."' and ref_type = 'payment_receipt' and 
                        voucher_id = '".$voucher_id."' and sub_ref_id is not null";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $result = $reader->readAll();

                if(count($result)>0){
                    $pay_id = $result[0]['pay_id'];
                    $sql = "update acc_ledger_entries set is_paid = '1', payment_ref = (case when payment_ref is null or payment_ref = '' 
                            then '".$id."' else concat(payment_ref,', ','".$id."') end) 
                            where id in (".$pay_id.")";
                    $command = Yii::$app->db->createCommand($sql);
                    $count = $command->execute();
                }
            }
            
            $this->setLog('PaymentReceipt', '', 'Approve', '', 'Approve Payment Receipt Details', 'acc_payment_receipt', $id);
        } else {
            $this->setLog('PaymentReceipt', '', 'Reject', '', 'Reject Payment Receipt Details', 'acc_payment_receipt', $id);
        }
    }

    public function setLog($module_name, $sub_module, $action, $vendor_id, $description, $table_name, $table_id) {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $company_id = $session['company_id'];

        $array = array('module_name' => $module_name, 
                        'sub_module' => $sub_module, 
                        'action' => $action, 
                        'vendor_id' => $vendor_id, 
                        'user_id' => $curusr, 
                        'description' => $description, 
                        'log_activity_date' => $now, 
                        'table_name' => $table_name, 
                        'table_id' => $table_id,
                        'company_id' => $company_id);
        $count = Yii::$app->db->createCommand()
                            ->insert("acc_user_log", $array)
                            ->execute();

        return true;
    }

    public function getPaymentAdviceDetails($id) {
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, B.bank_name, B.acc_no, B.branch 
                from acc_payment_receipt A left join acc_master B on (A.bank_id = B.id) 
                where A.id = '$id' and A.company_id = '$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $payment_details = $reader->readAll();

        if(count($payment_details)>0) {
            $acc_details = $this->getAccountDetails($payment_details[0]['account_id']);
            $status = $payment_details[0]['status'];

            if(count($acc_details)>0){
                $vendor_id = $acc_details[0]['vendor_id'];
                $sql = "select C.*, D.contact_name, D.contact_email, D.contact_phone, D.contact_mobile, D.contact_fax from 
                        (select A.*, B.* from 
                        (select AA.*, BB.legal_entity_name from vendor_master AA left join legal_entity_type_master BB 
                            on (AA.legal_entity_type_id = BB.id) where AA.id = '$vendor_id' and AA.company_id = '$company_id' and BB.is_active = '1') A 
                        left join 
                        (select AA.vendor_id, AA.office_address_line_1, AA.office_address_line_2, AA.office_address_line_3, 
                                AA.pincode, BB.city_code, BB.city_name, CC.state_code, CC.state_name, 
                                DD.country_code, DD.country_name from 
                        vendor_office_address AA left join city_master BB on (AA.city_id = BB.id) left join 
                        state_master CC on (AA.state_id = CC.id) left join country_master DD on (AA.country_id = DD.id) 
                        where AA.vendor_id = '$vendor_id' and AA.is_active = '1' and BB.is_active = '1' 
                                and CC.is_active = '1' and DD.is_active = '1') B 
                        on (A.id = B.vendor_id)) C 
                        left join 
                        (select * from vendor_contacts where vendor_id = '$vendor_id' and is_active = '1' and 
                            (is_purchase_related = 'yes' or is_accounts_related = 'yes') limit 1) D 
                        on (C.vendor_id = D.vendor_id)";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $vendor_details = $reader->readAll();
            } else {
                $vendor_details = array();
            }

            if(strtoupper(trim($payment_details[0]['payment_type']))=='KNOCK OFF'){
                // $sql = "select B.id, B.invoice_no, B.vendor_id, B.acc_id as account_id, B.ledger_name as account_name, 
                //             B.ledger_code as account_code, B.ledger_name, 
                //             B.type, B.amount, B.is_paid, B.payment_ref, B.ref_type as ledger_type, 
                //             C.gi_date, D.invoice_date from acc_ledger_entries A 
                //         left join acc_ledger_entries B on(A.sub_ref_id=B.id) 
                //         left join grn C on(B.ref_id = C.grn_id and B.ref_type = 'purchase') 
                //         left join goods_inward_outward_invoices D on(B.invoice_no = D.invoice_no and 
                //             B.ref_type = 'purchase' and C.gi_id = D.gi_go_ref_no) 
                //         where A.is_active = '1' and A.ref_type = 'payment_receipt' and 
                //             A.ref_id = '$id' and A.entry_type != 'payment_entry' and B.is_active = '1'";

                $sql = "select sr_no, ledger_type, type, ref_no, ref_date, po_no, ded_type, debit_note_ref, sum(amount) as tot_amount from 
                        (select B.id, B.invoice_no as ref_no, B.vendor_id, B.acc_id as account_id, B.ledger_name as account_name, 
                            B.ledger_code as account_code, B.ledger_name, 
                            B.type, A.amount, B.is_paid, B.payment_ref, C.gi_date, C.po_no, 
                            case when B.ref_type ='purchase' then 'Purchase' when B.ref_type ='journal_voucher' then 'Journal Voucher' 
                                when B.ref_type ='payment_receipt' then 'Payment Adhoc' 
                                else 'Purchase' end as ledger_type, 
                            case when B.ref_type ='purchase' then D.invoice_date when B.ref_type ='journal_voucher' then E.jv_date 
                                when B.ref_type ='payment_receipt' then F.payment_date else D.invoice_date end as ref_date, 
                            case when B.ref_type ='purchase' then 1 when B.ref_type ='journal_voucher' then 2 
                                when B.ref_type ='payment_receipt' then 3 else 4 end as sr_no, 
                            G.ded_type, G.debit_note_ref 
                        from acc_ledger_entries A 
                        left join acc_ledger_entries B on(A.sub_ref_id=B.id) 
                        left join grn C on(B.ref_id = C.grn_id and B.ref_type = 'purchase') 
                        left join goods_inward_outward_invoices D on(B.invoice_no = D.invoice_no and B.ref_type = 'purchase' and C.gi_id = D.gi_go_ref_no) 
                        left join acc_jv_details E on(B.ref_id = E.id and B.ref_type = 'journal_voucher') 
                        left join acc_payment_receipt F on(B.ref_id = F.id and B.ref_type = 'payment_receipt') 
                        left join acc_grn_debit_notes G on(B.invoice_no=G.invoice_no and B.ref_id=G.grn_id and B.ref_type = 'purchase') 
                        where A.is_active = '1' and A.ref_type = 'payment_receipt' and A.company_id = '$company_id' and 
                            date(A.ref_date)>date('2018-04-01') and A.ref_id = '$id' and A.entry_type != 'payment_entry' and B.is_active = '1') C 
                        group by sr_no, ledger_type, type, ref_no, ref_date, po_no, ded_type, debit_note_ref 
                        order by sr_no, ledger_type, type, ref_no, ref_date, po_no, ded_type, debit_note_ref";
            } else {
                // $sql = "select A.id, null as invoice_no, null as vendor_id, A.account_id, A.account_name, 
                //             A.account_code, A.account_name as ledger_name, 
                //             null as type, A.amount, A.is_paid, A.payment_ref, null as ledger_type, 
                //             null as gi_date, null as invoice_date from acc_payment_receipt A 
                //         where A.is_active = '1' and A.id = '$id'";

                $sql = "select A.id as sr_no, 'payment_receipt' as ledger_type, 
                            case when A.trans_type='Payment' then 'Debit' else 'Credit' end as type, 
                            A.ref_no, A.payment_date as ref_date, A.amount as tot_amount 
                        from acc_payment_receipt A 
                        where A.is_active = '1' and A.id = '$id' and A.company_id = '$company_id'";
            }
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $entry_details = $reader->readAll();
            

            $sql = "select * from acc_payment_advices where is_active = '1' and payment_id = '$id' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $payment_advice = $reader->readAll();
            if(count($payment_advice)==0){
                $session = Yii::$app->session;

                $curusr = $session['session_id'];
                $now = date('Y-m-d H:i:s');

                $array=[
                    'payment_id'=>$id,
                    'account_id'=>$payment_details[0]['account_id'],
                    'status'=>$status,
                    'is_active'=>'1',
                    'updated_by'=>$curusr,
                    'updated_date'=>$now,
                    'company_id'=>$company_id
                ];

                $count = Yii::$app->db->createCommand()
                            ->insert("acc_payment_advices", $array)
                            ->execute();
                $ref_id = Yii::$app->db->getLastInsertID();
            } else {
                $ref_id = $payment_advice[0]['id'];

                $sql = "update acc_payment_advices set status = '$status' where is_active = '1' and payment_id = '$id'";
                $command = Yii::$app->db->createCommand($sql);
                $count = $command->execute();
            }

            $sql = "select * from acc_payment_advices where is_active = '1' and id = '$ref_id' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $payment_advice = $reader->readAll();
            
            $mpdf=new mPDF();
            $mpdf->WriteHTML(Yii::$app->controller->renderPartial('payment_advice', [
                'payment_details' => $payment_details,
                'entry_details' => $entry_details,
                'vendor_details' => $vendor_details
            ]));

            $upload_path = './uploads';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/acc_payment_advices';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }

            $file_name = $upload_path . '/payment_advice_' . $id . '.pdf';
            $file_path = 'uploads/acc_payment_advices/payment_advice_' . $id . '.pdf';

            // $mpdf->Output('MyPDF.pdf', 'D');
            $mpdf->Output($file_name, 'F');
            // exit;

            $sql = "update acc_payment_advices set payment_advice_path = '$file_path' where id = '$ref_id'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();

            $sql = "select * from acc_payment_advices where is_active = '1' and id = '$ref_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $payment_advice = $reader->readAll();

        } else {
            $payment_advice = array();
            $vendor_details = array();
            $entry_details = array();
        }

        $data['payment_details'] = $payment_details;
        $data['vendor_details'] = $vendor_details;
        $data['entry_details'] = $entry_details;
        $data['payment_advice'] = $payment_advice;
        
        return $data;
    }

    public function setEmailLog($vendor_name, $from_email_id, $to_recipient, $reference_number, $email_content, 
                                $email_attachment, $attachment_type, $email_sent_status, $error_message, $company_id)
    {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $username = $session['username'];
        $now = date('Y-m-d H:i:s');
        $company_id = $session['company_id'];

        $array = array('module' => 'PA', 
                        'email_type' => 'Payment Advice Email', 
                        'warehouse_code' => '', 
                        'vendor_name' => $vendor_name, 
                        'from_email_id' => $from_email_id, 
                        'to_recipient' => $to_recipient, 
                        'cc_recipient' => '', 
                        'supporting_user' => $username, 
                        'reference_number' => $reference_number, 
                        'reference_status' => 'approved', 
                        'email_date' => $now, 
                        'email_content' => $email_content, 
                        'email_attachment' => $email_attachment, 
                        'attachment_type' => $attachment_type, 
                        'email_sent_status' => $email_sent_status, 
                        'error_message' => $error_message, 
                        'company_id' => $company_id);
        $count = Yii::$app->db->createCommand()
                            ->insert("acc_email_log", $array)
                            ->execute();

        return true;
    }

    public function getInvoicewiseLedger($acc_id,$from_date,$to_date,$type) {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $cond = '';
        if($type=='Pending')
        {
            /*$cond = 'AND CAST(BB.bal_amount AS float)>CAST(0 AS float)';*/
            $cond = 'Where (BB.bal_amount>0 and BB.bal_amount>0.00)';
        }

        $sql = "select AA.invoice_no as inv_no, AA.amount as opening_amount,AA.type as openingtype , BB.* from 
            (select AA.* from 

            (select A.* from 

            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.gi_date, C.invoice_date, D.due_date from acc_ledger_entries A 
                left join grn B on(A.ref_id = B.grn_id and A.ref_type = 'purchase') 
                left join goods_inward_outward_invoices C on(A.invoice_no = C.invoice_no and A.ref_type = 'purchase' and B.gi_id = C.gi_go_ref_no) 
                left join invoice_tracker D on(A.ref_id=D.grn_id and C.gi_go_invoice_id=D.invoice_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and 
                    date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.sub_ref_id=C.jv_entries_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'journal_voucher' and A.acc_id NOT IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'journal_voucher' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and 
                    date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'go_debit_details' and A.acc_id NOT IN ($acc_id) and A.ledger_type = 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1' and 
                    A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'go_debit_details' and acc_id IN ($acc_id) and ledger_type = 'Main Entry' and company_id = '1' and 
                entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
            on(A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.date_of_upload as gi_date, C.invoice_date, null as due_date from acc_ledger_entries A 
                left join acc_sales_files B on(A.ref_id = B.id and A.ref_type = 'sales_upload') 
                left join 
                (select distinct ref_file_id, invoice_no, invoice_date from acc_sales_file_items where company_id='1' and is_active='1') C 
                on(A.invoice_no = C.invoice_no and A.ref_type = 'sales_upload' and A.ref_id = C.ref_file_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'sales_upload' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, A.type, A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)<date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'journal_voucher' and A.acc_id IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)<date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'journal_voucher' and acc_id NOT IN ($acc_id)  and company_id = '1') B 
            on (A.ref_id=B.ref_id)) A 
            where (A.acc_id IN ($acc_id) or A.cp_acc_id IN ($acc_id))) AA 

            left join 

            (select A.* from 
            (select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select A.*, B.gi_date, C.invoice_date, D.due_date from acc_ledger_entries A 
                left join grn B on(A.ref_id = B.grn_id and A.ref_type = 'purchase') 
                left join goods_inward_outward_invoices C on(A.invoice_no = C.invoice_no and A.ref_type = 'purchase' and B.gi_id = C.gi_go_ref_no) 
                left join invoice_tracker D on(A.ref_id=D.grn_id and C.gi_go_invoice_id=D.invoice_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.sub_ref_id=C.jv_entries_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'journal_voucher' and A.acc_id NOT IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'journal_voucher' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.inv_no as invoice_no from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and 
                    date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 
            group by A.inv_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.inv_no as invoice_no from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'go_debit_details' and A.acc_id NOT IN ($acc_id) and A.ledger_type = 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1' and 
                    A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'go_debit_details' and acc_id IN ($acc_id) and ledger_type = 'Main Entry' and company_id = '1' and 
                entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
            on(A.ref_id=B.ref_id) 
            group by A.inv_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.acc_id as cp_acc_id, A.invoice_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 
            group by A.invoice_no, A.acc_id, B.acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.acc_id as cp_acc_id, A.invoice_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 
            group by A.invoice_no, A.acc_id, B.acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select A.*, B.date_of_upload as gi_date, C.invoice_date, null as due_date from acc_ledger_entries A 
                left join acc_sales_files B on(A.ref_id = B.id and A.ref_type = 'sales_upload') 
                left join 
                (select distinct ref_file_id, invoice_no, invoice_date from acc_sales_file_items where company_id='1' and is_active='1') C 
                on(A.invoice_no = C.invoice_no and A.ref_type = 'sales_upload' and A.ref_id = C.ref_file_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'sales_upload' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)<date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'journal_voucher' and A.acc_id IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)<date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'journal_voucher' and acc_id NOT IN ($acc_id)  and company_id = '1') B 
            on (A.ref_id=B.ref_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id) A 
            where (A.acc_id IN ($acc_id) or A.cp_acc_id IN ($acc_id))) BB 

            on (AA.id = BB.min_id) 

            where BB.min_id is not null) AA

            left join 

            (select A.* from 
            (select group_concat(A.id) as id, A.ref_id, group_concat(A.sub_ref_id) as sub_ref_id, A.ref_type, 
                group_concat(A.entry_type) as entry_type, A.invoice_no, A.vendor_id, 
                group_concat(A.acc_id) as acc_id, group_concat(A.ledger_name) as ledger_name, group_concat(A.ledger_code) as ledger_code, 
                A.type, sum(A.amount) as amount, sum(A.paid_amount) as paid_amount, 
                sum(A.total_paid_amount) as total_paid_amount, sum(A.bal_amount) as bal_amount, 
                A.status, A.voucher_id, A.ledger_type, A.ref_date, A.gi_date, A.invoice_date, A.due_date, 
                A.cp_acc_id, A.cp_ledger_name, A.cp_ledger_code,
                Case When A.due_date IS NOT NULL Then 
                DATEDIFF('$from_date',A.ref_date) Else '' end as overdueby, A.gi_id, A.gi_go_ref_no from 
            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, 
                A.acc_id, A.ledger_name, A.ledger_code, A.type, A.amount, A.paid_amount, 
                A.total_paid_amount, A.bal_amount, A.status, A.created_by, A.updated_by, 
                A.created_date, A.updated_date, A.voucher_id, A.ledger_type, 
                A.narration, A.ref_date, A.gi_date, A.invoice_date, A.due_date, 
                A.cp_acc_id, A.cp_ledger_name, A.cp_ledger_code,A.gi_id,A.gi_go_ref_no, 
                case when (instr(A.ledger_name,'-CGST')!=0 or instr(A.ledger_name,'-SGST')!=0 or 
                    instr(A.ledger_name,'-IGST')!=0 or instr(A.ledger_name,'Purchase-')!=0 or 
                    instr(A.ledger_name,'Sales-')!=0) then A.new_ledger_name 
                    else A.ledger_name end as new_ledger_name from 
            (select AA.*, (AA.paid_amount) as total_paid_amount, 
                round((AA.amount-AA.paid_amount),2) as bal_amount, 
                replace(replace(replace(replace(replace(replace(replace(replace(replace(AA.ledger_name,'Input-','Purchase-'),
                    'Output-','Sales-'),'CGST','Local'),'SGST','Local'),'IGST','Inter State'),'-B2B',''),'-B2C',''),'--',''),
                    Substring_index(AA.ledger_name, '-', -1),'') as new_ledger_name, 
                case when (instr(AA.ledger_name,'-CGST')!=0 or instr(AA.ledger_name,'-SGST')!=0) 
                then (2 * Replace(Substring_index(AA.ledger_name, '-', -1),'%', '')) 
                else Replace(Substring_index(AA.ledger_name, '-', -1), '%', '') end as percentage from 
            (

            select A.*, ifnull(C.paid_amount,0) as paid_amount from 
                
            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.gi_id,A.gi_go_ref_no from 
            (select A.*, B.gi_date, C.invoice_date, D.due_date, B.grn_id as gi_id,C.gi_go_ref_no from acc_ledger_entries A 
                left join grn B on(A.ref_id = B.grn_id and A.ref_type = 'purchase') 
                left join goods_inward_outward_invoices C on(A.invoice_no = C.invoice_no and A.ref_type = 'purchase' and B.gi_id = C.gi_go_ref_no) 
                left join invoice_tracker D on(A.ref_id=D.grn_id and C.gi_go_invoice_id=D.invoice_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.sub_ref_id=C.jv_entries_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'journal_voucher' and A.acc_id NOT IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'journal_voucher' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.inv_no as gi_id,'' as gi_go_ref_no from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and 
                    date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.inv_no as gi_id,'' as gi_go_ref_no from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'go_debit_details' and A.acc_id NOT IN ($acc_id) and A.ledger_type = 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1' and 
                    A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'go_debit_details' and acc_id IN ($acc_id) and ledger_type = 'Main Entry' and company_id = '1' and 
                entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
            on(A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select A.*, B.date_of_upload as gi_date, C.invoice_date, null as due_date from acc_ledger_entries A 
                left join acc_sales_files B on(A.ref_id = B.id and A.ref_type = 'sales_upload') 
                left join 
                (select distinct ref_file_id, invoice_no, invoice_date from acc_sales_file_items where company_id='1' and is_active='1') C 
                on(A.invoice_no = C.invoice_no and A.ref_type = 'sales_upload' and A.ref_id = C.ref_file_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'sales_upload' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, A.type, A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)<date('2018-04-01') and 
                    A.ref_type = 'journal_voucher' and A.acc_id IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)<date('2018-04-01') and ref_type = 'journal_voucher' and acc_id!='968'  and company_id = '1') B 
            on (A.ref_id=B.ref_id)) A 

            left join 

            (select sub_ref_id, sum(case when (status = 'approved') then amount else 0 end) as paid_amount from acc_ledger_entries 
                where is_active = '1' and company_id = '1' and sub_ref_id is not null and 
                    date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' group by sub_ref_id) C 
            on (A.id = C.sub_ref_id) 

            ) AA 
            where (AA.acc_id IN ($acc_id) or AA.cp_acc_id IN ($acc_id)) and AA.amount!=0 and 
                    (AA.ref_type!='payment_receipt' or AA.entry_type='Bank Entry' or AA.entry_type='Payment' or AA.entry_type='Receipt')) A) A 
            group by A.ref_id, A.ref_type, A.invoice_no, A.vendor_id, A.type, A.status, A.voucher_id, A.ledger_type, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, A.cp_acc_id, A.cp_ledger_name, A.cp_ledger_code, A.gi_id, A.gi_go_ref_no) A 
            order by A.ref_date) BB 

            on (AA.ref_id=BB.ref_id and AA.invoice_no=BB.invoice_no) " .$cond."
              Order By AA.ref_date ASC
            ";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getInvoicewiseLedger_detail($acc_id,$from_date,$to_date,$type) {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        if($from_date==''){
            $from_date=NULL;
        } else {
            $from_date=$mycomponent->formatdate($from_date);
        }

        if($to_date==''){
            $to_date=NULL;
        } else {
            $to_date=$mycomponent->formatdate($to_date);
        }

        $cond = '';
        if($type=='Pending')
        {
            /*$cond = 'AND CAST(BB.bal_amount AS float)>CAST(0 AS float)';*/
            $cond = 'Where (BB.bal_amount>0 and BB.bal_amount>0.00)';
        }

        $sql = "select AA.invoice_no as inv_no, AA.amount as opening_amount,AA.type as openingtype , BB.* from 
            (select AA.* from 

            (select A.* from 

            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.gi_date, C.invoice_date, D.due_date from acc_ledger_entries A 
                left join grn B on(A.ref_id = B.grn_id and A.ref_type = 'purchase') 
                left join goods_inward_outward_invoices C on(A.invoice_no = C.invoice_no and A.ref_type = 'purchase' and B.gi_id = C.gi_go_ref_no) 
                left join invoice_tracker D on(A.ref_id=D.grn_id and C.gi_go_invoice_id=D.invoice_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.sub_ref_id=C.jv_entries_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'journal_voucher' and A.acc_id NOT IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'journal_voucher' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and 
                    date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'go_debit_details' and A.acc_id NOT IN ($acc_id) and A.ledger_type = 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1' and 
                    A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'go_debit_details' and acc_id IN ($acc_id) and ledger_type = 'Main Entry' and company_id = '1' and 
                entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
            on(A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.date_of_upload as gi_date, C.invoice_date, null as due_date from acc_ledger_entries A 
                left join acc_sales_files B on(A.ref_id = B.id and A.ref_type = 'sales_upload') 
                left join 
                (select distinct ref_file_id, invoice_no, invoice_date from acc_sales_file_items where company_id='1' and is_active='1') C 
                on(A.invoice_no = C.invoice_no and A.ref_type = 'sales_upload' and A.ref_id = C.ref_file_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'sales_upload' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, A.type, A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)<date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'journal_voucher' and A.acc_id IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)<date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'journal_voucher' and acc_id NOT IN ($acc_id)  and company_id = '1') B 
            on (A.ref_id=B.ref_id)) A 
            where (A.acc_id IN ($acc_id) or A.cp_acc_id IN ($acc_id))) AA 

            left join 

            (select A.* from 
            (select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select A.*, B.gi_date, C.invoice_date, D.due_date from acc_ledger_entries A 
                left join grn B on(A.ref_id = B.grn_id and A.ref_type = 'purchase') 
                left join goods_inward_outward_invoices C on(A.invoice_no = C.invoice_no and A.ref_type = 'purchase' and B.gi_id = C.gi_go_ref_no) 
                left join invoice_tracker D on(A.ref_id=D.grn_id and C.gi_go_invoice_id=D.invoice_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.sub_ref_id=C.jv_entries_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'journal_voucher' and A.acc_id NOT IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'journal_voucher' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.inv_no as invoice_no from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and 
                    date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 
            group by A.inv_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.inv_no as invoice_no from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'go_debit_details' and A.acc_id NOT IN ($acc_id) and A.ledger_type = 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1' and 
                    A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'go_debit_details' and acc_id IN ($acc_id) and ledger_type = 'Main Entry' and company_id = '1' and 
                entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
            on(A.ref_id=B.ref_id) 
            group by A.inv_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.acc_id as cp_acc_id, A.invoice_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 
            group by A.invoice_no, A.acc_id, B.acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.acc_id as cp_acc_id, A.invoice_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 
            group by A.invoice_no, A.acc_id, B.acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select A.*, B.date_of_upload as gi_date, C.invoice_date, null as due_date from acc_ledger_entries A 
                left join acc_sales_files B on(A.ref_id = B.id and A.ref_type = 'sales_upload') 
                left join 
                (select distinct ref_file_id, invoice_no, invoice_date from acc_sales_file_items where company_id='1' and is_active='1') C 
                on(A.invoice_no = C.invoice_no and A.ref_type = 'sales_upload' and A.ref_id = C.ref_file_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'sales_upload' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id 

            union all 

            select min(A.id) as min_id, A.acc_id, B.cp_acc_id, A.invoice_no from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)<date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'journal_voucher' and A.acc_id IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)<date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'journal_voucher' and acc_id NOT IN ($acc_id)  and company_id = '1') B 
            on (A.ref_id=B.ref_id) 
            group by A.invoice_no, A.acc_id, B.cp_acc_id) A 
            where (A.acc_id IN ($acc_id) or A.cp_acc_id IN ($acc_id))) BB 

            on (AA.id = BB.min_id) 

            where BB.min_id is not null) AA

            left join 

            (select A.* from 
            (select A.id as id, A.ref_id, A.sub_ref_id as sub_ref_id, A.ref_type, 
                A.entry_type as entry_type, A.invoice_no, A.vendor_id, 
                A.acc_id as acc_id, A.ledger_name as ledger_name, A.ledger_code as ledger_code, 
                A.type, A.amount as amount, A.paid_amount as paid_amount, 
                A.total_paid_amount as total_paid_amount, A.bal_amount as bal_amount, 
                A.status, A.voucher_id, A.ledger_type, A.ref_date, A.gi_date, A.invoice_date, A.due_date, 
                A.cp_acc_id, A.cp_ledger_name, A.cp_ledger_code,DATEDIFF('$from_date',A.ref_date) as overdueby,A.gi_id,A.gi_go_ref_no from 
            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, 
                A.acc_id, A.ledger_name, A.ledger_code, A.type, A.amount, A.paid_amount, 
                A.total_paid_amount, A.bal_amount, A.status, A.created_by, A.updated_by, 
                A.created_date, A.updated_date, A.voucher_id, A.ledger_type, 
                A.narration, A.ref_date, A.gi_date, A.invoice_date, A.due_date, 
                A.cp_acc_id, A.cp_ledger_name, A.cp_ledger_code, 
                case when (instr(A.ledger_name,'-CGST')!=0 or instr(A.ledger_name,'-SGST')!=0 or 
                    instr(A.ledger_name,'-IGST')!=0 or instr(A.ledger_name,'Purchase-')!=0 or 
                    instr(A.ledger_name,'Sales-')!=0) then A.new_ledger_name 
                    else A.ledger_name end as new_ledger_name,A.gi_id,A.gi_go_ref_no from 
            (select AA.*, (AA.paid_amount) as total_paid_amount, 
                round((AA.amount-AA.paid_amount),2) as bal_amount, 
                replace(replace(replace(replace(replace(replace(replace(replace(replace(AA.ledger_name,'Input-','Purchase-'),
                    'Output-','Sales-'),'CGST','Local'),'SGST','Local'),'IGST','Inter State'),'-B2B',''),'-B2C',''),'--',''),
                    Substring_index(AA.ledger_name, '-', -1),'') as new_ledger_name, 
                case when (instr(AA.ledger_name,'-CGST')!=0 or instr(AA.ledger_name,'-SGST')!=0) 
                then (2 * Replace(Substring_index(AA.ledger_name, '-', -1),'%', '')) 
                else Replace(Substring_index(AA.ledger_name, '-', -1), '%', '') end as percentage from 
            (

            select A.*, ifnull(C.paid_amount,0) as paid_amount from 
                
            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.gi_id,A.gi_go_ref_no from 
            (select A.*, B.gi_date, C.invoice_date, D.due_date, B.grn_id as gi_id,C.gi_go_ref_no from acc_ledger_entries A 
                left join grn B on(A.ref_id = B.grn_id and A.ref_type = 'purchase') 
                left join goods_inward_outward_invoices C on(A.invoice_no = C.invoice_no and A.ref_type = 'purchase' and B.gi_id = C.gi_go_ref_no) 
                left join invoice_tracker D on(A.ref_id=D.grn_id and C.gi_go_invoice_id=D.invoice_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.sub_ref_id=C.jv_entries_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'journal_voucher' and A.acc_id NOT IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'journal_voucher' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and 
                    date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no 
                from acc_ledger_entries A 
                left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                where A.status = 'Approved' and A.is_active = '1' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'go_debit_details' and A.acc_id NOT IN ($acc_id) and A.ledger_type = 'Main Entry' and 
                    A.company_id = '1' and B.company_id = '1' and 
                    A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'go_debit_details' and acc_id IN ($acc_id) and ledger_type = 'Main Entry' and company_id = '1' and 
                entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
            on(A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'other_debit_credit' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code ,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id NOT IN ($acc_id) and company_id = '1') A 
            left join 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'promotion' and acc_id IN ($acc_id) and company_id = '1') B 
            on (A.ref_id=B.ref_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                null as gi_date, null as invoice_date, null as due_date, 
                B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and 
                ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id IN ($acc_id) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, A.due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select A.*, B.date_of_upload as gi_date, C.invoice_date, null as due_date from acc_ledger_entries A 
                left join acc_sales_files B on(A.ref_id = B.id and A.ref_type = 'sales_upload') 
                left join 
                (select distinct ref_file_id, invoice_no, invoice_date from acc_sales_file_items where company_id='1' and is_active='1') C 
                on(A.invoice_no = C.invoice_no and A.ref_type = 'sales_upload' and A.ref_id = C.ref_file_id) 
                where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and date(A.ref_date)>date('2018-04-01') and date(A.ref_date)>'$from_date' and date(A.ref_date)<'$to_date' and 
                    A.ref_type = 'sales_upload' and A.ledger_type != 'Main Entry' and A.company_id = '1' and B.company_id = '1') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '1') B 
            on (A.voucher_id = B.cp_voucher_id) 

            union all 

            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, A.type, A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                A.gi_date, A.invoice_date, null as due_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.invoice_no as gi_id,'' as gi_go_ref_no from 
            (select A.*, B.jv_date as gi_date, C.invoice_date from acc_ledger_entries A 
                left join acc_jv_details B on(A.ref_id=B.id and A.ref_type='journal_voucher') 
                left join acc_jv_invoices_entries C 
                on (A.ref_id=C.jv_details_id and A.ref_type='journal_voucher' and A.invoice_no = C.invoice_number and A.amount = C.invoice_amount) 
                where A.status = 'Approved' and A.is_active = '1' and date(A.ref_date)<date('2018-04-01') and 
                    A.ref_type = 'journal_voucher' and A.acc_id IN ($acc_id) and A.company_id = '1') A 
            left join 
            (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                date(ref_date)<date('2018-04-01') and ref_type = 'journal_voucher' and acc_id!='968'  and company_id = '1') B 
            on (A.ref_id=B.ref_id)) A 

            left join 

            (select sub_ref_id, sum(case when (status = 'approved') then amount else 0 end) as paid_amount from acc_ledger_entries 
                where is_active = '1' and company_id = '1' and sub_ref_id is not null and 
                    date(ref_date)>date('2018-04-01') and date(ref_date)>'$from_date' and date(ref_date)<'$to_date' and ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' group by sub_ref_id) C 
            on (A.id = C.sub_ref_id) 

            ) AA 
            where (AA.acc_id IN ($acc_id) or AA.cp_acc_id IN ($acc_id)) and AA.amount!=0 and 
                    (AA.ref_type!='payment_receipt' or AA.entry_type='Bank Entry' or AA.entry_type='Payment' or AA.entry_type='Receipt')) A) A 
            ) A 
            order by A.ref_date) BB 

            on (AA.ref_id=BB.ref_id and AA.invoice_no=BB.invoice_no) 
            Order 
            " .$cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function uploadPayment() {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $mycomponent = Yii::$app->mycomponent;
        $status = '';

        $payment_file = $request->post('payment_file');
        $upload_path = './uploads';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $upload_path = './uploads/payment_file/';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $fetched_file='';
        $uploadedFile = UploadedFile::getInstanceByName('payment_file');
        if(!empty($uploadedFile)) {
            $src_filename = $_FILES['payment_file'];
            $fetched_file=$filename = $src_filename['name'];
            $filePath = $upload_path.'/'.$filename;
            $uploadedFile->saveAs($filePath);
            $original_file = 'uploads/payment_file/'.$filename;
        }

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel = \PHPExcel_IOFactory::load($original_file);

        $sheet_count = $objPHPExcel->getSheetCount();
        if($sheet_count<3) {
            $objPHPExcel->setActiveSheetIndex(0);
            $count_val = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            $error = 'Sheet Is Not Valid ,Sheet Count Is Less Then Actual Sheet';
            $objPHPExcel->getActiveSheet(0)->setCellValue('V'.($count_val+1),$error);
            $boolerror=1;
            return false;
        } else {
            $objPHPExcel->setActiveSheetIndex(2);
            $highestrow3 = $objPHPExcel->setActiveSheetIndex(2)->getHighestRow(); 
            $primarpecan = $objPHPExcel->getActiveSheet()->getCell('A1')->getValue();
            if($primarpecan!='Primarc Pecan') {
                $error = 'Hidden Sheet Value Does Not Match';
                $objPHPExcel->getActiveSheet()->setCellValue('V'.($highestrow3+1),$error);
                $boolerror=1;

                return false;
            } else {
                $objPHPExcel->setActiveSheetIndex(0);
                $bank = $this->getBanks();
               
                $array = array();
                $prev_type = '';
                $prev_date = '';

                $r_row = 2;
                $boolerror=0;
                $bank_name_tem = '';    
                $highestrow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

                if($highestrow>0) {
                    $prev_id='';
                    $prev_id2='';    
                    $gross_paying_amount_total=0;

                    $ledger_id = array();
                    $ledger_type = array();
                    $invoice_no = array();
                    $vendor_id = array();
                    $amount_to_pay = array();
                    $total_amount = array();
                    $total_paid_amount = array();
                    $p_type = '';
                    $payment_type = 'Knock off';
                    $payment_date = date("Y-m-d");
                    $remarks = '';
                    $approver_id = $curusr;
                    /*$payment_date=$payment_date;*/
                    $sum_amount = 0;
                    $paying_amount_total = 0;
                    $paying_transaction = '';
                    $ref_no = '';
                    $payment_receipt = '';
                    $narration = '';
                    $bank_name = '';
                    $bank = '';
                    $acc_code1 = '';
                    $bank_id = '';
                    $bank_name_tem = '';
                    $trans_type = '';
                    $payment_date = '';
                    $to_date = $objPHPExcel->getActiveSheet()->getCell('B1')->getValue();
                    $to_date = \PHPExcel_Style_NumberFormat::toFormattedString($to_date, 'YYYY-MM-DD');

                    for($k=3; $k<=$highestrow; $k++){
                        $error = '';
                        $temp_flag = 0;

                        $acc_name = $objPHPExcel->getActiveSheet()->getCell('B'.$k)->getValue();

                        $ids = $objPHPExcel->getActiveSheet()->getCell('D'.$k)->getValue();
                        $explode = explode("&&" ,$ids);
                        $ledger_ids= $explode[0];
                        $voucher_id = $objPHPExcel->getActiveSheet()->getCell('E'.$k)->getValue();
                        $particular =  $objPHPExcel->getActiveSheet()->getCell('F'.$k)->getValue();
                        $ref_type = $objPHPExcel->getActiveSheet()->getCell('G'.$k)->getValue();
                        $invoice_ref=  $objPHPExcel->getActiveSheet()->getCell('H'.$k)->getValue();
                        $gi_date = $objPHPExcel->getActiveSheet()->getCell('I'.$k)->getValue();
                        $invoice_date = $objPHPExcel->getActiveSheet()->getCell('J'.$k)->getValue();
                        $due_date = $objPHPExcel->getActiveSheet()->getCell('K'.$k)->getValue();
                        $transaction1 = $objPHPExcel->getActiveSheet()->getCell('L'.$k)->getValue();
                        $amount =  $objPHPExcel->getActiveSheet()->getCell('M'.$k)->getValue();;
                        $sum_amount = ($sum_amount+$amount);
                        $paid_amount =  $objPHPExcel->getActiveSheet()->getCell('N'.$k)->getValue();;
                        $balance_amount =  $objPHPExcel->getActiveSheet()->getCell('O'.$k)->getValue();

                        $amount_to_pay1 = $objPHPExcel->getActiveSheet()->getCell('P'.$k)->getValue();

                        if($objPHPExcel->getActiveSheet()->getCell('P'.$k)->getValue()=='' && 
                            $objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue()=='' &&
                            $objPHPExcel->getActiveSheet()->getCell('R'.$k)->getValue()=='' &&
                            $objPHPExcel->getActiveSheet()->getCell('T'.$k)->getValue()==''
                            )
                        {
                            $temp_flag=0;
                        } 
                        else if($objPHPExcel->getActiveSheet()->getCell('P'.$k)->getValue()!='' && 
                            $objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue()!='' &&
                            $objPHPExcel->getActiveSheet()->getCell('R'.$k)->getValue()!='' &&
                            $objPHPExcel->getActiveSheet()->getCell('T'.$k)->getValue()!=''&&
                            $objPHPExcel->getActiveSheet()->getCell('U'.$k)->getValue()!=''   
                            )
                        {
                            $temp_flag=0;
                        } else {
                            $temp_flag=1;
                        }

                        if($temp_flag==1) {
                            $boolerror=1;
                            if($error!='')
                                $error.=' , ';
                            $error .= 'Please Enter Required Details';
                        }

                        if($amount_to_pay1!='') {
                            if(!is_numeric($amount_to_pay1)) {
                                $boolerror=1;
                                if($error!='')
                                    $error.=' , ';
                                $error .= 'Number Should Be Integer';
                            } else {
                                /*$result = $this->get_paid_amount($ledger_ids,$invoice_ref);*/
                                $acc_name_temp = $this->getAccountDetails('' ,$objPHPExcel->getActiveSheet()->getCell('B'.$k)->getValue());
                                $acc_id_temp = $acc_name_temp[0]['id'];
                                // $result = $this->getLedger('', $acc_id_temp,$to_date,$ledger_ids);

                                if($ledger_ids=='') $ledger_ids = 0;
                                $sql = "select A.temp_col, (ifnull(A.total_amount,0)-ifnull(B.paid_amount,0)) as bal_amount from 
                                        (select '1' as temp_col, sum(amount) as total_amount from acc_ledger_entries where id in (".$ledger_ids.")) A 
                                        left join 
                                        (select '1' as temp_col, sum(amount) as paid_amount from acc_ledger_entries 
                                        where status = 'approved' and is_active = '1' and company_id = '$company_id' and 
                                            sub_ref_id is not null and date(ref_date)>date('2018-04-01') and 
                                            ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and sub_ref_id in (".$ledger_ids.")) B 
                                        on (A.temp_col=B.temp_col)";
                                $command = Yii::$app->db->createCommand($sql);
                                $reader = $command->query();
                                $result = $reader->readAll();

                                if(count($result)>0) {
                                    $fetched_bal_amount = $result[0]['bal_amount'];
                                    // $actual_amount  = $objPHPExcel->getActiveSheet()->getCell('M'.$k)->getValue();
                                    if($fetched_bal_amount==NULL || $fetched_bal_amount==0)
                                        $fetched_bal_amount=0;

                                    /*$fetched_bal_amount = $actual_amount-$fetched_paid_amount;*/

                                    $amount_to_pay11 = $amount_to_pay1;
                                    $fetched_bal_amount1 = (abs($fetched_bal_amount)+0.5);

                                    $fetched_bal_amount2 = ($fetched_bal_amount-0.5);
                                    $flag=0;

                                    if((abs($amount_to_pay11)<=abs($fetched_bal_amount1))) {
                                        $flag=0;
                                    } else {
                                        $flag=1;
                                    } 

                                    if($flag==1) {
                                        $boolerror=1;
                                        if($error!='')
                                            $error.=' , ';

                                        if($fetched_bal_amount==0)
                                            $error .= 'Payment is already Done';
                                        else
                                            $error .= 'Amount To Pay Should Be Less Then OR Equal To : '.$fetched_bal_amount;
                                    } 
                                } else {
                                    $boolerror=1;
                                    if($error!='')
                                        $error.=' , ';
                                    $error .= 'Balance Amount Not Found';
                                }

                                $paying_amount_total = ($paying_amount_total+$amount_to_pay1);
                                $gross_paying_amount_total = ($gross_paying_amount_total+$amount_to_pay1);

                                /*$bank_name = $objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue();*/
                                /*$ref_no = $objPHPExcel->getActiveSheet()->getCell('R'.$k)->getValue();*/
                                /*$narration = $objPHPExcel->getActiveSheet()->getCell('S'.$k)->getValue();*/

                                if($objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue()!='') {
                                    $bank_name = $objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue();
                                    if($bank_name!="") {
                                        $bank = $this->getBanks('' , $objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue());
                                        $acc_code1 = $bank[0]['code'];
                                        $bank_id = $bank[0]['id'];
                                        $bank_name_tem =  $objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue();    
                                    }                   
                                }

                                if($objPHPExcel->getActiveSheet()->getCell('T'.$k)->getValue()!='') {
                                    $payment_receipt = $objPHPExcel->getActiveSheet()->getCell('T'.$k)->getValue();
                                }

                                if($objPHPExcel->getActiveSheet()->getCell('U'.$k)->getValue()!='') {
                                    $payment_date = $objPHPExcel->getActiveSheet()->getCell('U'.$k)->getValue();
                                    $payment_date = \PHPExcel_Style_NumberFormat::toFormattedString($payment_date, 'YYYY-MM-DD');
                                }

                                if($objPHPExcel->getActiveSheet()->getCell('R'.$k)->getValue()!='') {
                                    $ref_no = $objPHPExcel->getActiveSheet()->getCell('R'.$k)->getValue();
                                }

                                if($objPHPExcel->getActiveSheet()->getCell('S'.$k)->getValue()!='') {
                                    $narration = $objPHPExcel->getActiveSheet()->getCell('S'.$k)->getValue();
                                }

                                if($objPHPExcel->getActiveSheet()->getCell('T'.$k)->getValue()!='') {
                                    $trans_type = $objPHPExcel->getActiveSheet()->getCell('T'.$k)->getValue();
                                }

                                if($transaction1=='Debit' && $amount_to_pay1>0) {
                                    $boolerror=1;
                                    if($error!='')
                                        $error.=' , ';
                                    $error .= ' Debit Amount Should Be Negative';
                                }

                                if($objPHPExcel->getActiveSheet()->getCell('P'.$k)->getValue()!="" && 
                                    $objPHPExcel->getActiveSheet()->getCell('O'.$k)->getValue()!="")
                                {
                                    if(abs($objPHPExcel->getActiveSheet()->getCell('P'.$k)->getValue())>abs($objPHPExcel->getActiveSheet()->getCell('O'.$k)->getValue()))
                                    {
                                        $boolerror=1;
                                        if($error!='')
                                            $error.=' , ';
                                        $error .= ' Amount Should Be Less Than Or Equal To Balance Amount';
                                    }
                                }

                                $p_type = $payment_receipt;
                                $acc_no = $objPHPExcel->getActiveSheet()->getCell('C'.$k)->getValue();
                                $temp_total_amount = 0;

                                if($objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue()!=''){
                                    for($z=3; $z<=$highestrow; $z++) {
                                        $temp_bank_name = $objPHPExcel->getActiveSheet()->getCell('Q'.$z)->getValue();

                                        if($temp_bank_name!='') {
                                            $temp_acc_no = $objPHPExcel->getActiveSheet()->getCell('C'.$z)->getValue();
                                            $temp_amount = $objPHPExcel->getActiveSheet()->getCell('P'.$z)->getValue();
                                            $temp_ref_no = $objPHPExcel->getActiveSheet()->getCell('R'.$z)->getValue();
                                            $temp_trans_type = $objPHPExcel->getActiveSheet()->getCell('T'.$z)->getValue();
                                            $temp_payment_date = $objPHPExcel->getActiveSheet()->getCell('U'.$z)->getValue();
                                            $temp_payment_date = \PHPExcel_Style_NumberFormat::toFormattedString($temp_payment_date, 'YYYY-MM-DD');

                                            if(strtoupper(trim($temp_acc_no))==strtoupper(trim($acc_no)) && 
                                                strtoupper(trim($temp_bank_name))==strtoupper(trim($bank_name)) && 
                                                strtoupper(trim($temp_ref_no))==strtoupper(trim($ref_no))) 
                                            {
                                                if($trans_type!=$temp_trans_type) {
                                                    $boolerror=1;
                                                    if($error!='')
                                                        $error.=' , ';
                                                    $error .= 'Type (Payment / Receipt) Should Be Same In All column';
                                                }

                                                if($payment_date!=$temp_payment_date) {
                                                    $boolerror=1;
                                                    if($error!='')
                                                        $error.=' , ';
                                                    $error .= 'Payment Date Should Be Same In All column';
                                                }

                                                if(is_numeric($temp_amount)){
                                                    $temp_total_amount = $temp_total_amount + $temp_amount;
                                                }
                                            }
                                        }
                                    }

                                    if(strtoupper(trim($trans_type))=='PAYMENT') {
                                        if($temp_total_amount<0) {
                                            $boolerror=1;
                                            if($error!='')
                                                $error.=' , ';
                                            $error .= ' Total Payable amount should be credit';
                                        }
                                    }

                                    if(strtoupper(trim($trans_type))=='RECEIPT') {
                                        if($temp_total_amount>0) {
                                            $boolerror=1;
                                            if($error!='')
                                                $error.=' , ';
                                            $error .= ' Total Payable amount should be debit';
                                        }
                                    }

                                    $legal_name = $objPHPExcel->getActiveSheet()->getCell('B'.$k)->getValue();
                                    $acc_name = $this->getAccountDetails('', $legal_name);
                                    if(count($acc_name)==0){
                                        $boolerror=1;
                                        if($error!='')
                                            $error.=' , ';
                                        $error .= ' Account Name Not Found';
                                    }
                                }
                                
                            }
                        }

                        if($error!='')
                            $objPHPExcel->getActiveSheet()->setCellValue('V'.($r_row+1),$error);
                        $r_row = $r_row+1;

                        

                        $remarks = '';
                        $approver_id = $curusr;
                        $sum_amount = 0;
                        $paying_amount_total = 0;
                        $paying_transaction = '';
                        $ref_no = '';
                        $p_type = '';
                        $payment_receipt = '';
                        $narration = '';
                        $bank_name = '';
                        $bank = '';
                        $acc_code1 = '';
                        $bank_id = '';
                        $bank_name_tem = '';
                    }
                }

                $efilename = '';
                if($boolerror==1) {
                    $status = 'Failed';
                    $upload_path = './uploads';
                    if(!is_dir($upload_path)) {
                        mkdir($upload_path, 0777, TRUE);
                    }

                    $upload_path = './uploads/payment_file/';
                    if(!is_dir($upload_path)) {
                        mkdir($upload_path, 0777, TRUE);
                    }

                    $efilename='payment_receipt_'.time().'.xlsx';
                    $file_name = $upload_path . '/' . $efilename;

                    $objPHPExcel->getActiveSheet()->getProtection()->setPassword('dhaval1234');
                    $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
                    
                    $bank = $this->getBanks();
                    $row_t = 1;  
                    
                    $objPHPExcel->setActiveSheetIndex(1);
                    for($i=0; $i <count($bank); $i++) { 
                        $objPHPExcel->getActiveSheet()->setCellValue('A'.$row_t,$bank[$i]['legal_name']);
                        $row_t = $row_t+1;
                    }

                    $objPHPExcel->setActiveSheetIndex(0);

                    for($j=2;$j<=1000;$j++) {
                        $objValidation = $objPHPExcel->getActiveSheet(0)->getCell('Q'.$j)->getDataValidation();
                        $this->common_excel($objValidation);
                        $objValidation->setFormula1('\'Sheet2\'!$A$1:$A$'.($row_t-1));
                    }

                    for($k=2;$k<=1000;$k++) {
                        $objValidation = $objPHPExcel->getActiveSheet(0)->getCell('T'.$k)->getDataValidation();
                        $this->common_excel($objValidation);
                        $objValidation->setFormula1('\'Sheet2\'!$B$1:$B$2');
                    }

                    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    $objWriter->save($file_name);
                    ob_clean();
                    ob_flush();

                    $filename='payment_receipt_'.time().'.xlsx';
                    $file_name =  $filename;
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="'.$file_name.'"');
                    header('Cache-Control: max-age=0');
                    // If you're serving to IE 9, then the following may be needed
                    header('Cache-Control: max-age=1');

                    // If you're serving to IE over SSL, then the following may be needed
                    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
                    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
                    header ('Pragma: public'); // HTTP/1.0

                    $insert_array = array("uploaded_file"=>$fetched_file,
                                          "date_of_upload"=>date('Y-m-d H:i:s'),
                                          "error_file"=>$efilename,
                                          "status"=>$status,
                                          "uploaded_by"=>$curusr,
                                          "bank_cash_ledger"=>$bank_name_tem,
                                          "final_amount"=>$gross_paying_amount_total,
                                          "payment_receipt"=>$p_type,
                                          "company_id"=>$company_id);

                    Yii::$app->db->createCommand()->insert("acc_payment_upload", $insert_array)->execute();

                    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    $objWriter->save('php://output');
                } else {
                    $highestrow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                    $random_code = 0;

                    if($highestrow>0) {
                        $payment_recept_array = [];
                        $random_code =  $this->random_code(5);
                        for($k=3;$k<=$highestrow;$k++){
                            $due_date = $objPHPExcel->getActiveSheet()->getCell('K'.$k)->getValue();
                            if($due_date=='')
                                $due_date = NULL;
                            else
                                $due_date = $due_date;

                            $payment_recept_array = [];
                            $payment_recept_array['acc_name'] = $objPHPExcel->getActiveSheet()->getCell('B'.$k)->getValue();
                            $payment_recept_array['acc_code'] = $objPHPExcel->getActiveSheet()->getCell('C'.$k)->getValue();
                            $payment_recept_array['ids']  = $objPHPExcel->getActiveSheet()->getCell('D'.$k)->getValue();
                            $payment_recept_array['voucher_id']  = $objPHPExcel->getActiveSheet()->getCell('E'.$k)->getValue();
                            $payment_recept_array['particular'] =  $objPHPExcel->getActiveSheet()->getCell('F'.$k)->getValue();
                            $payment_recept_array['ref_type'] = $objPHPExcel->getActiveSheet()->getCell('G'.$k)->getValue();
                            $payment_recept_array['ref_no']= $objPHPExcel->getActiveSheet()->getCell('H'.$k)->getValue();
                            $gi_date = $objPHPExcel->getActiveSheet()->getCell('I'.$k)->getValue(); 
                            $gi_date = \PHPExcel_Style_NumberFormat::toFormattedString($gi_date, 'YYYY-MM-DD');

                            $payment_recept_array['gi_date'] = $gi_date ;

                            $invoice_date = $objPHPExcel->getActiveSheet()->getCell('I'.$k)->getValue(); 
                            $invoice_date = \PHPExcel_Style_NumberFormat::toFormattedString($invoice_date, 'YYYY-MM-DD');
                            $payment_recept_array['invoice_date'] = $invoice_date;
                            $payment_recept_array['due_date'] = $due_date;
                            $payment_recept_array['transaction'] = $objPHPExcel->getActiveSheet()->getCell('L'.$k)->getValue();
                            $payment_recept_array['amount'] = $objPHPExcel->getActiveSheet()->getCell('M'.$k)->getValue();
                            $payment_recept_array['paid_amount'] = $objPHPExcel->getActiveSheet()->getCell('N'.$k)->getValue();
                            $payment_recept_array['bal_amount'] = $objPHPExcel->getActiveSheet()->getCell('O'.$k)->getValue();
                            $payment_recept_array['amount_to_pay'] = ($objPHPExcel->getActiveSheet()->getCell('P'.$k)->getValue()==''?NUll:$objPHPExcel->getActiveSheet()->getCell('P'.$k)->getValue());
                            $payment_recept_array['bank_name'] = ($objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue()==''?NUll:$objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue());
                            $payment_recept_array['check_no'] = ($objPHPExcel->getActiveSheet()->getCell('R'.$k)->getValue()==''?NUll:$objPHPExcel->getActiveSheet()->getCell('R'.$k)->getValue());
                            $payment_recept_array['narration'] = ($objPHPExcel->getActiveSheet()->getCell('S'.$k)->getValue()==''?NULL:$objPHPExcel->getActiveSheet()->getCell('S'.$k)->getValue());
                            $payment_recept_array['type'] = ($objPHPExcel->getActiveSheet()->getCell('T'.$k)->getValue()==''?NULL:$objPHPExcel->getActiveSheet()->getCell('T'.$k)->getValue());

                            $payment_date = $objPHPExcel->getActiveSheet()->getCell('U'.$k)->getValue(); 
                            $payment_date = \PHPExcel_Style_NumberFormat::toFormattedString($payment_date, 'YYYY-MM-DD');

                            $payment_recept_array['payment_date'] = ($objPHPExcel->getActiveSheet()->getCell('U'.$k)->getValue()==''?NULL:$payment_date);

                            $payment_recept_array['random_code'] = $random_code;

                            if($objPHPExcel->getActiveSheet()->getCell('P'.$k)->getValue()!='' && 
                                $objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue()!='' &&
                                $objPHPExcel->getActiveSheet()->getCell('R'.$k)->getValue()!='' &&
                                $objPHPExcel->getActiveSheet()->getCell('T'.$k)->getValue()!=''
                                &&
                                $objPHPExcel->getActiveSheet()->getCell('U'.$k)->getValue()!=''
                                )
                            {
                                Yii::$app->db->createCommand()->insert("acc_temp_payment_detail", $payment_recept_array)->execute();  
                            }
                        }
                    }

                    $command = Yii::$app->db->createCommand("SELECT * FROM acc_temp_payment_detail Where random_code='$random_code' Order By acc_code,bank_name,check_no ");
                    $reader = $command->query();
                    $temp_result = $reader->readAll();

                    /*echo "<pre>";
                    print_r($temp_result);
                    echo "</pre>";

                    die();*/

                    if(count($temp_result)>0) {
                        $prev_id='';
                        $prev_id2='';    
                        $gross_paying_amount_total=0;

                        $ledger_id = array();
                        $ledger_type = array();
                        $invoice_no = array();
                        $vendor_id = array();
                        $amount_to_pay = array();
                        $total_amount = array();
                        $total_paid_amount = array();
                        $transaction = array();
                        $p_type = '';
                        $payment_type = 'Knock off';
                        $payment_date = date("Y-m-d");
                        $remarks = '';
                        $approver_id = $curusr;
                        /*$payment_date=$payment_date;*/
                        $sum_amount = 0;
                        $paying_amount_total = 0;
                        $paying_transaction = '';
                        $ref_no = '';
                        $payment_receipt = '';
                        $narration = '';
                        $bank_name = '';
                        $bank = '';
                        $acc_code1 = '';
                        $bank_id = '';
                        $bank_name_tem = '';
                        $trans_type = '';
                        $payment_date = '';

                        $highestrow = (count($temp_result)-1);
                        for($h=0;$h<count($temp_result);$h++){
                            $error = '';
                            $temp_flag = 0;

                            $acc_name = $temp_result[$h]['acc_name'];
                           
                            $ids = $temp_result[$h]['ids'];
                            $explode = explode("&&" ,$ids);
                            $ledger_id[]=$ledger_ids= $explode[0];
                            $ledger_type[] = $explode[1];
                            $vendor_id[] = $explode[2];
                            $voucher_id = $temp_result[$h]['voucher_id'];
                            $particular =  $temp_result[$h]['particular'];
                            $ref_type = $temp_result[$h]['ref_type'];
                            $invoice_no[]=$invoice_ref=  $temp_result[$h]['ref_no'];
                            $gi_date = $temp_result[$h]['gi_date'];
                            $invoice_date = $temp_result[$h]['invoice_date'];
                            $due_date = $temp_result[$h]['due_date'];
                            $transaction[]=$transaction1 = $temp_result[$h]['transaction'];
                            $total_amount[]=$amount =  $temp_result[$h]['amount'];
                            $sum_amount = ($sum_amount+$amount);
                            $total_paid_amount[]=$paid_amount =  $temp_result[$h]['paid_amount'];;
                            $balance_amount =  $temp_result[$h]['bal_amount'];

                            $amount_to_pay[]=$amount_to_pay1 = $temp_result[$h]['amount_to_pay'];

                            // echo 'ledger_id - '.implode(",",$ledger_id).'<br/>';
                            // echo 'transaction - '.implode(",",$transaction).'<br/>';
                            // echo 'total_amount - '.implode(",",$total_amount).'<br/>';
                            // echo 'total_paid_amount - '.implode(",",$total_paid_amount).'<br/>';

                            if($temp_result[$h]['amount_to_pay']=='' && 
                               $temp_result[$h]['bank_name']=='' &&
                               $temp_result[$h]['check_no']=='' &&
                               $temp_result[$h]['type']==''
                              )
                            {
                                $temp_flag=0;
                            }
                            else if($temp_result[$h]['amount_to_pay']!='' && 
                               $temp_result[$h]['bank_name']!='' &&
                               $temp_result[$h]['check_no']!='' &&
                               $temp_result[$h]['type']!=''&&
                               $temp_result[$h]['payment_date']!=''   
                              )
                            {
                                $temp_flag=0;
                            }
                            else
                            {
                                $temp_flag=1;
                            }

                            // if($temp_flag!=1)
                            // {
                            //   if($amount_to_pay1!='')
                            //     {
                            //          $acc_name_temp = $this->getAccountDetails('' ,$temp_result[$h]['acc_name']);
                            //           $acc_id_temp = $acc_name_temp[0]['id'];
                            //           $result = $this->getLedger('', $acc_id_temp,$to_date,$ledger_ids);

                            //           $fetched_bal_amount = $result[0]['bal_amount'];
                            //           $actual_amount  = $temp_result[$h]['amount'];
                            //           if($fetched_bal_amount==NULL || $fetched_bal_amount==0)
                            //               $fetched_bal_amount=0;

                            //           /*$fetched_bal_amount = $actual_amount-$fetched_paid_amount;*/

                            //          $amount_to_pay11 = $amount_to_pay1;
                            //         $fetched_bal_amount1 = (abs($fetched_bal_amount)+0.5);
                            //         $fetched_bal_amount2 = ($fetched_bal_amount-0.5);
                            //         $flag=0;
                                    
                            //     }
                            // }


                            $paying_amount_total = ($paying_amount_total+$amount_to_pay1);
                            $gross_paying_amount_total = ($gross_paying_amount_total+$amount_to_pay1);

                            /*$bank_name = $temp_result[$h]['bank_name'];*/
                            /*$ref_no = $temp_result[$h]['check_no'];*/
                            /*$narration = $objPHPExcel->getActiveSheet()->getCell('S'.$k)->getValue();*/
                            if($temp_result[$h]['bank_name']!='') {
                                $bank_name = $temp_result[$h]['bank_name'];
                                if($bank_name!="") {
                                    $bank = $this->getBanks('' , $temp_result[$h]['bank_name']);
                                    $acc_code1 = $bank[0]['code'];
                                    $bank_id = $bank[0]['id'];
                                    $bank_name_tem =  $temp_result[$h]['bank_name'];    
                                }
                            }

                            if($temp_result[$h]['type']!='') {
                                $payment_receipt = $temp_result[$h]['type'];
                            }
                            if($temp_result[$h]['payment_date']!='') {
                                $payment_date = $temp_result[$h]['payment_date'];
                                $payment_date = date("Y-m-d",strtotime($payment_date));
                            }
                            if($temp_result[$h]['check_no']!='') {
                                $ref_no = $temp_result[$h]['check_no'];
                            }
                            if($temp_result[$h]['narration']!='') {
                                $narration = $temp_result[$h]['narration'];
                            }
                            if($temp_result[$h]['type']!=''){
                                $trans_type = $temp_result[$h]['type'];
                            }

                            $prev_date = $payment_date;
                            $prev_type = $payment_receipt;

                            $acc_no = $temp_result[$h]['acc_code'];

                            if($h==$highestrow){
                                $next_acc_no = '';
                                $next_bank_name = '';
                                $next_check_no = '';
                            } else {
                                $next_acc_no = $temp_result[$h+1]['acc_code'];
                                $next_bank_name = $temp_result[$h+1]['bank_name'];
                                $next_check_no = $temp_result[$h+1]['check_no'];
                            }

                            if($acc_no!=$next_acc_no || $bank_name!=$next_bank_name || $ref_no!=$next_check_no) {
                                $temp_result[$h]['bank_name'];

                                $acc_name = $this->getAccountDetails('' ,$temp_result[$h]['acc_name']);
                                $id = '';
                                $voucher_id = '';/*$temp_result[$h]['voucher_id'];*/                           
                                $acc_id = $acc_name[0]['id'];
                                $legal_name = $temp_result[$h]['acc_name'];
                                $acc_code = $temp_result[$h]['acc_code'];

                                $payment_type = 'Knock off';                             
                                $bal_amount = ($sum_amount-$paying_amount_total);
                                if($paying_amount_total<0) {
                                    $paying_transaction = 'Debit';
                                } else {
                                    $paying_transaction = 'Credit';
                                }

                                if(strtoupper(trim($paying_transaction))=='DEBIT') {
                                    // $amount = $paying_amount_total*-1;
                                    $amount = $paying_amount_total;
                                } else {
                                    $amount = $paying_amount_total;
                                }

                                if($boolerror==0) {
                                    $status = 'Inserted';
                                    $transaction_id = "";

                                    if(!isset($voucher_id) || $voucher_id=='') {
                                        $series = 1;
                                        $sql = "select * from acc_series_master where type = 'Voucher' and company_id = '$company_id'";
                                        $command = Yii::$app->db->createCommand($sql);
                                        $reader = $command->query();
                                        $data = $reader->readAll();
                                        if (count($data)>0) {
                                            $series = intval($data[0]['series']) + 1;

                                            $sql = "update acc_series_master set series = '$series' where type = 'Voucher' and company_id = '$company_id'";
                                            $command = Yii::$app->db->createCommand($sql);
                                            $count = $command->execute();
                                        } else {
                                            $series = 1;

                                            $sql = "insert into acc_series_master (type, series, company_id) values ('Voucher', '".$series."', '".$company_id."')";
                                            $command = Yii::$app->db->createCommand($sql);
                                            $count = $command->execute();
                                        }

                                        $voucher_id = $series;
                                    }

                                    $array=[
                                        'trans_type'=>$trans_type,
                                        'voucher_id' => $voucher_id, 
                                        'ledger_type' => 'Sub Entry', 
                                        'account_id'=>$acc_id,
                                        'account_name'=>$legal_name,
                                        'account_code'=>$acc_code,
                                        'account_code1'=>$acc_code1,
                                        'bank_id'=>$bank_id,
                                        'bank_name'=>$bank_name,
                                        'payment_type'=>$payment_type,
                                        'amount'=>((strtoupper(trim($paying_transaction))=='DEBIT')?$amount*-1:$amount),
                                        'ref_no'=>$ref_no,
                                        'narration'=>$narration,
                                        'status'=>'approved',
                                        'is_active'=>'1',
                                        'updated_by'=>$curusr,
                                        'updated_date'=>$now,
                                        'payment_date'=>$payment_date,
                                        'approver_comments'=>$remarks,
                                        'approver_id'=>$approver_id,
                                        'approved_by'=>$approver_id,
                                        'approved_date'=>$now,
                                        'company_id'=>$company_id
                                    ];

                                    if (isset($id) && $id!=""){
                                        $count = Yii::$app->db->createCommand()
                                        ->update("acc_payment_receipt", $array, "id = '".$id."'")
                                        ->execute();
                                    } else {
                                        $array['created_by']=$curusr;
                                        $array['created_date']=$now;

                                        $count = Yii::$app->db->createCommand()
                                        ->insert("acc_payment_receipt", $array)
                                        ->execute();
                                        $id = Yii::$app->db->getLastInsertID();
                                    }

                                    if (isset($ledger_id)){
                                        for($i=0; $i<count($ledger_id); $i++){
                                            if($amount_to_pay[$i]!="" && $amount_to_pay[$i]!="0" && $amount_to_pay[$i]!=null){
                                                $type = $transaction[$i];
                                                $amt = $mycomponent->format_number($amount_to_pay[$i],2);
                                                $tot_amt = $mycomponent->format_number($total_amount[$i],2);
                                                $tot_paid_amt = $mycomponent->format_number($total_paid_amount[$i],2);
                                                if(strtoupper(trim($type))=='DEBIT'){
                                                    $amt = $amt * -1;
                                                    $tot_amt = $tot_amt * -1;
                                                    $tot_paid_amt = $tot_paid_amt * -1;
                                                    // echo 'type1 - '.$type.'<br/>';
                                                }
                                                $tot_bal_amt = $tot_amt - $tot_paid_amt;

                                                // echo 'type - '.$type.'<br/>';
                                                // echo 'tot_bal_amt - '.$tot_bal_amt.'<br/>';
                                                // echo 'tot_amt - '.$tot_amt.'<br/>';
                                                // echo 'tot_paid_amt - '.$tot_paid_amt.'<br/>';

                                                $led_id = explode(',', $ledger_id[$i]);
                                                $tot_round_off_bal_amt = 0;

                                                for($j=0; $j<count($led_id); $j++){
                                                    $led_id[$j] = trim($led_id[$j]);
                                                    $led_amt = $amt;

                                                    if($led_id[$j]!="" && $led_id[$j]!=null){
                                                        $sql = "select A.*, B.paid_amount, B.pending_paid_amount, B.amount_to_pay from 
                                                                (select * from acc_ledger_entries where id = '".$led_id[$j]."') A 
                                                                left join 
                                                                (select sub_ref_id, sum(case when (ref_id != '$id' and status = 'approved') then amount else 0 end) as paid_amount, 
                                                                sum(case when (ref_id != '$id' and status = 'pending') then amount else 0 end) as pending_paid_amount, 
                                                                sum(case when ref_id = '$id' then amount else 0 end) as amount_to_pay from acc_ledger_entries 
                                                                where is_active = '1' and company_id = '$company_id' and sub_ref_id is not null and date(ref_date)>date('2018-04-01') and 
                                                                ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and sub_ref_id = '".$led_id[$j]."' 
                                                                group by sub_ref_id) B 
                                                                on (A.id = B.sub_ref_id)";
                                                        $command = Yii::$app->db->createCommand($sql);
                                                        $reader = $command->query();
                                                        $result = $reader->readAll();

                                                        if(count($result)>0){
                                                            $led_acc_name = $result[0]['ledger_name'];
                                                            $led_amount = $result[0]['amount'];
                                                            $led_per = 0;

                                                            $paid_amount = 0;
                                                            if(isset($result[0]['paid_amount'])){
                                                                if($result[0]['paid_amount']!=''){
                                                                    $paid_amount = $result[0]['paid_amount'];
                                                                }
                                                            }
                                                            $pending_paid_amount = 0;
                                                            if(isset($result[0]['pending_paid_amount'])){
                                                                if($result[0]['pending_paid_amount']!=''){
                                                                    $pending_paid_amount = $result[0]['pending_paid_amount'];
                                                                }
                                                            }
                                                            $tot_paid_amount = $paid_amount + $pending_paid_amount;


                                                            $led_amt = $led_amount - $tot_paid_amount;
                                                            $led_amt = round(($led_amt*$amt/$tot_bal_amt),2); 

                                                            $tot_round_off_bal_amt = $tot_round_off_bal_amt + $led_amt;
                                                            if($j==count($led_id)-1){
                                                                if($amt!=$tot_round_off_bal_amt){
                                                                    $led_amt = round(($led_amt + ($amt-$tot_round_off_bal_amt)),2);
                                                                }
                                                            }
                                                        }

                                                        // echo 'led_amt - '.$led_amt.'<br/>';

                                                        $ledgerArray=[
                                                                        'ref_id'=>$id,
                                                                        'sub_ref_id'=>$led_id[$j],
                                                                        'ref_type'=>'payment_receipt',
                                                                        'entry_type'=>$ledger_type[$i],
                                                                        'invoice_no'=>$invoice_no[$i],
                                                                        'vendor_id'=>$vendor_id[$i],
                                                                        'voucher_id' => $voucher_id, 
                                                                        'ledger_type' => 'Sub Entry', 
                                                                        'acc_id'=>$acc_id,
                                                                        'ledger_name'=>$legal_name,
                                                                        'ledger_code'=>$acc_code,
                                                                        'type'=>$type,
                                                                        'amount'=>round($led_amt,2),
                                                                        'narration'=>$narration,
                                                                        'status'=>'approved',
                                                                        'is_active'=>'1',
                                                                        'updated_by'=>$curusr,
                                                                        'updated_date'=>$now,
                                                                        'approved_by'=>$approver_id,
                                                                        'approved_date'=>$now,
                                                                        'ref_date'=>$payment_date,
                                                                        'approver_comments'=>$remarks,
                                                                        'company_id'=>$company_id
                                                                    ];

                                                        $count = Yii::$app->db->createCommand()
                                                        ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and sub_ref_id = '".$led_id[$j]."' and ref_type = 'payment_receipt'")
                                                        ->execute();

                                                        if ($count==0) {
                                                            $ledgerArray['created_by']=$curusr;
                                                            $ledgerArray['created_date']=$now;

                                                            $count = Yii::$app->db->createCommand()
                                                            ->insert("acc_ledger_entries", $ledgerArray)
                                                            ->execute();
                                                        }
                                                    }
                                                }
                                            } else {
                                                if($ledger_id[$i]!="" && $ledger_id[$i]!=null){
                                                    $count = Yii::$app->db->createCommand()
                                                    ->delete("acc_ledger_entries", "ref_id = '".$id."' and 
                                                        sub_ref_id in (".$ledger_id[$i].") and 
                                                        ref_type = 'payment_receipt'")
                                                    ->execute();
                                                }
                                            }
                                        }
                                    }

                                    $data = $this->getBanks($bank_id);

                                    if(count($data)>0){
                                        $bank_legal_name = $data[0]['legal_name'];
                                        $bank_acc_code = $data[0]['code'];
                                    } else {
                                        $bank_legal_name = '';
                                        $bank_acc_code = '';
                                    }

                                    if($amount>0){
                                        $type = 'Credit';
                                        $amount = $amount;
                                    } else {
                                        $type = 'Debit';
                                        $amount = $amount*-1;
                                    }

                                    $ledgerArray=[
                                                    'ref_id'=>$id,
                                                    'sub_ref_id'=>null,
                                                    'ref_type'=>'payment_receipt',
                                                    'entry_type'=>'Bank Entry',
                                                    'invoice_no'=>$ref_no,
                                                    'vendor_id'=>null,
                                                    'voucher_id' => $voucher_id, 
                                                    'ledger_type' => 'Main Entry', 
                                                    'acc_id'=>$bank_id,
                                                    'ledger_name'=>$bank_legal_name,
                                                    'ledger_code'=>$bank_acc_code,
                                                    'type'=>$type,
                                                    'amount'=>$amount,
                                                    'narration'=>$narration,
                                                    'status'=>'approved',
                                                    'is_active'=>'1',
                                                    'updated_by'=>$curusr,
                                                    'updated_date'=>$now,
                                                    'ref_date'=>$payment_date,
                                                    'payment_ref'=>$id,
                                                    'approved_by'=>$approver_id,
                                                    'approved_date'=>$now,
                                                    'approver_comments'=>$remarks,
                                                    'company_id'=>$company_id
                                                ];

                                    $count = Yii::$app->db->createCommand()
                                    ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and ref_type = 'payment_receipt' and entry_type = 'Bank Entry'")
                                    ->execute();
                                    if ($count==0){
                                        $ledgerArray['created_by']=$curusr;
                                        $ledgerArray['created_date']=$now;

                                        $count = Yii::$app->db->createCommand()
                                        ->insert("acc_ledger_entries", $ledgerArray)
                                        ->execute();
                                    } 

                                    $this->authorise('approved', $id, $voucher_id);
                                }

                                $remarks = '';
                                $approver_id = $curusr;
                                $payment_date=$payment_date;
                                $sum_amount = 0;
                                $paying_amount_total = 0;
                                $paying_transaction = '';
                                $ref_no = ''; 
                                $ledger_id = array();
                                $ledger_type = array();
                                $invoice_no = array();
                                $vendor_id = array();
                                $amount_to_pay = array();
                                $total_amount = array();
                                $total_paid_amount = array();
                                $transaction = array();
                                $p_type = '';
                                $ref_no = '';
                                $payment_receipt = '';
                                $narration = '';
                                $bank_name = '';
                                $bank = '';
                                $acc_code1 = '';
                                $bank_id = '';
                                $bank_name_tem = '';
                            }
                        }
                    }

                    $insert_array = array("uploaded_file"=>$fetched_file,
                                          "date_of_upload"=>date('Y-m-d H:i:s'),
                                          "error_file"=>$efilename,
                                          "status"=>$status,
                                          "uploaded_by"=>$curusr,
                                          "bank_cash_ledger"=>$bank_name_tem,
                                          "final_amount"=>$gross_paying_amount_total,
                                          "payment_receipt"=>$p_type,
                                          "company_id"=>$company_id);

                    Yii::$app->db->createCommand()->insert("acc_payment_upload", $insert_array)->execute();
                }

                Yii::$app->db->createCommand()
                                ->delete("acc_temp_payment_detail", "random_code = '$random_code'")
                                ->execute();

                return true;
            }
        }
    }

    public function common_excel($objValidation) {
        $objValidation->setType(\PHPExcel_Cell_DataValidation::TYPE_LIST );
        $objValidation->setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowErrorMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->getShowDropDown(true);
        $objValidation->setErrorTitle('Input error');
        $objValidation->setError('Value is not in list.');
        $objValidation->setPromptTitle('Pick from list');
        $objValidation->setPrompt('Please pick a value from the drop-down list.');/*
        $objValidation->setFormula1('"'.$distname.'"');*/
    }

    function random_code($limit) {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }
}