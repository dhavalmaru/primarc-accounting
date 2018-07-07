<?php

namespace app\models;

use Yii;
use yii\base\Model;

class AccReport extends Model
{
    public function getAccountDetails($id=""){
        $cond = "";
        if($id!=""){
            $cond = " and id = '$id'";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_master where is_active = '1' and status = 'approved' and company_id = '$company_id'".$cond." order by legal_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getOpeningBal($acc_id, $from_date){
        $status = "approved";

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select acc_id, sum(case when type='Debit' then amount*-1 else amount end) as opening_bal from acc_ledger_entries 
                where acc_id = '$acc_id' and status = '$status' and company_id = '$company_id' and is_active = '1' and 
                date(ref_date) < date('$from_date') group by acc_id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getLedger($acc_id, $from_date, $to_date){
        $status = "approved";
        
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from 
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, 
                    B.ledger_code as cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'journal_voucher' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'journal_voucher' and acc_id='$acc_id' and company_id = '$company_id') B 
                on(A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.date_of_transaction as gi_date, null as invoice_date, null as due_date from acc_ledger_entries A 
                    left join acc_go_debit_details B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and 
                        A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id' and B.company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'other_debit_credit' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'other_debit_credit' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                ) AA 
                where AA.acc_id = '$acc_id' or AA.cp_acc_id = '$acc_id' 
                order by AA.ref_date, AA.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getsummeryledger($acc_id, $from_date, $to_date){
        $status = "approved";
        
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

         $sql = "Select A.type, A.voucher_id, A.ref_type, A.ref_date, A.ref_id, A.invoice_no, 
                    ROUND(ifnull(sum(A.amount1),0),2) as amount, GROUP_CONCAT(A.payment_ref) as group_payemt_ref from 
                (select * from 
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount as amount1, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, 
                    B.ledger_code as cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'journal_voucher' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'journal_voucher' and acc_id='$acc_id' and company_id = '$company_id') B 
                on(A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.date_of_transaction as gi_date, null as invoice_date, null as due_date from acc_ledger_entries A 
                    left join acc_go_debit_details B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and 
                        A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id' and B.company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount as amount1, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'other_debit_credit' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'other_debit_credit' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                ) AA 
                where AA.acc_id = '$acc_id' or AA.cp_acc_id = '$acc_id' 
                order by AA.ref_date, AA.id ) A 
                GROUP BY A.type, A.voucher_id, A.ref_type, A.ref_date, A.ref_id, A.invoice_no";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getTrialBalance($from_date, $to_date){
        $status = "approved";
        
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select E.id as account_id, E.code, E.legal_name, E.category_1, E.category_2, E.category_3, E.acc_category, 
                        E.bus_category, E.opening_bal, F.* from 
                (select C.id, C.code, C.legal_name, C.category_1, C.category_2, C.category_3, C.acc_category, C.bus_category, D.opening_bal from 
                (select A.*, concat_ws(',', A.category_1, A.category_2, A.category_3) as acc_category, B.bus_category from 
                (select * from acc_master where is_active = '1' and status = '$status' and company_id = '$company_id') A 
                left join 
                (select acc_id, GROUP_CONCAT(category_name) as bus_category from acc_categories 
                    where is_active = '1'and status = '$status' and company_id = '$company_id' group by acc_id) B 
                on (A.id = B.acc_id)) C 
                left join 
                (select acc_id, sum(case when type='Debit' then amount*-1 else amount end) as opening_bal from acc_ledger_entries 
                where status = '$status' and is_active = '1' and date(ref_date) < date('$from_date') and company_id = '$company_id' 
                group by acc_id) D 
                on (C.id = D.acc_id)) E 
                left join 
                (select acc_id, sum(case when type='Debit' then amount else 0 end) as debit_amt, 
                        sum(case when type='Credit' then amount else 0 end) as credit_amt 
                from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and company_id = '$company_id' 
                group by acc_id) F 
                on (E.id = F.acc_id) 
                order by E.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
    
    public function get_ledger_totalamount($from_date, $to_date){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "Select * from (
                SELECT ae.acc_id,ae.ledger_name,sum(case when ae.type='Credit' then amount else 0 end) as debit_amount,
                sum(case when ae.type='Debit' then amount else 0 end) as credit_amount
                from acc_ledger_entries ae
                left join acc_master am on am.id=ae.acc_id
                WHERE  ae.`status`='approved' and am.type='Vendor Goods' and 
                date(ae.ref_date) >= date('$from_date') and date(ae.ref_date) <= date('$to_date') and ae.company_id = '$company_id' 
                GROUP BY ae.acc_id,ae.ledger_name ) A";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function setLog($module_name, $sub_module, $action, $vendor_id, $description, $table_name, $table_id) {
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

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

    public function getVendorname(){
       $sql = "select id,legal_name from acc_master where is_active = '1' and status = 'approved' and type='Vendor Goods' order by legal_name ";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getstatemaster(){
       $sql = "select state_name,id,state_code from state_master WHERE is_active = '1'";
       $command = Yii::$app->db->createCommand($sql);
       $reader = $command->query();
       return $reader->readAll();
    }

    public function getDetailledger($account, $vouchertype,$from_date, $to_date,$date_type){
        if($date_type=='invoice_date')
            $where_condition = "date(invoice_date)>='$from_date' and date(invoice_date)<='$to_date'";
        else if($date_type=='grn_approved_date_time')
            $where_condition = "date(grn_approved_date_time)>='$from_date' and date(grn_approved_date_time)<='$to_date'";
        else if($date_type=='gi_date')
            $where_condition = "date(gi_date)>='$from_date' and date(gi_date)<='$to_date'";
        else if($date_type=='updated_date')
            $where_condition = "date(updated_date)>='$from_date' and date(updated_date)<='$to_date'";
        else
             $where_condition=' ';

        //$account;
        if($account!='')
        {
           $sql = "Select * from 
                    (Select * from 
                    (
                    Select * FROM(
                    Select A.*,F.updated_date from 
                    (
                    Select C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,'' as debit_notes_ref,E.gi_go_ref_no,G.warehouse_id from (
                    Select A.ref_id,A.ref_type,A.cp_acc_id, A.invoice_no,A.voucher_id ,A.cp_ledger_name,TRUNCATE((A.total_tax_amount+D.total_purchase_amount),2) as total_deduction,total_tax_amount as tax_amount, total_purchase_amount as total_without_tax from (
                    select  A.ref_id,A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name from 
                    (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '1') A 
                    left join 
                    (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
                    on (A.voucher_id = B.cp_voucher_id) 
                    Where B.cp_acc_id IN ($account) AND entry_type IN('CGST','IGST','SGST')  GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name
                    ) A
                    left JOIN
                    (select  A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id , cp_ledger_name from 
                    (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '1') A 
                    left join 
                    (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
                    on (A.voucher_id = B.cp_voucher_id) 
                    Where B.cp_acc_id In($account) AND entry_type IN('Taxable Amount') GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name 
                    ) D on A.ref_id=D.ref_id and A.invoice_no=D.invoice_no ) C
                    left JOIN
                    (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn ) G on C.ref_id=G.grn_id 
                    left join
                    (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no
                    ) A
                    left join 
                    (Select updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id
                    left join
                    (Select warehouse_code from internal_warehouse_master Where state_id IN(5) ) G
                     on A.warehouse_id=G.warehouse_code
                    ) D
                    UNION
                    Select * from (
                    Select B.ref_id, B.ref_type,B.cp_acc_id,B.invoice_no,B.voucher_id,B.cp_ledger_name ,B.total_deduction,
                    B.tax_amount ,B.total_without_tax ,B.gi_date,B.grn_approved_date_time,B.invoice_date,B.debit_note_ref,B. gi_go_ref_no,B.updated_date,B.warehouse_id from (
                    Select C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,E.gi_go_ref_no,F.updated_date,H.voucher_id,G.warehouse_id  from(
                    Select  B.ref_id, 'Debit Note' as ref_type,A.cp_acc_id,B.invoice_no,A.legal_name as cp_ledger_name ,B.total_deduction,
                    B.total_tax as tax_amount ,B.total_without_tax ,B.debit_note_ref from  
                    (Select id as cp_acc_id ,vendor_id,legal_name from acc_master Where id IN ($account) ) A
                    left join
                    (Select grn_id as ref_id,vendor_id ,invoice_no,total_deduction ,total_tax ,total_without_tax ,debit_note_ref from acc_grn_debit_notes) B
                     on A.vendor_id=B.vendor_id Where B.vendor_id IS NOT NULL) C
                    left JOIN
                    (Select grn_approved_date_time,gi_date,grn_id,warehouse_id  from grn ) G on C.ref_id=G.grn_id 
                    left join
                    (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no
                    left join 
                    (Select updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on C.ref_id=F.grn_id 
                    left join 
                    (Select voucher_id,ref_id from acc_ledger_entries Where entry_type = 'Total Deduction') H on C.ref_id=H.ref_id) B
                    left join 
                    (Select warehouse_code from internal_warehouse_master Where state_id IN(5) ) G
                    on B.warehouse_id=G.warehouse_code) E
                    ) A $where_condition ORDER BY ref_id ASC,invoice_no ASC,ref_type DESC ) A
                    ";
                    
            /*$sql.=" UNION  
                    Select * from 
                    (select A.ref_id, A.ref_type,A.acc_id as cp_acc_id, A.invoice_no,A.voucher_id ,A.ledger_name as cp_ledger_name,sum(A.amount) as total_deduction,0 as tax_amount ,
                    sum(A.amount) as total_without_tax,'' as gi_date,'' as grn_approved_date_time,'' as invoice_date ,'' as debit_notes_ref,'' as gi_go_ref_no , '' as updated_date from 
                    (select * from acc_ledger_entries where status = 'approved' and is_active = '1' and 
                    ref_type = 'journal_voucher' and acc_id='693' and company_id = '2' ) A 
                    Group  by A.id, A.ref_id,A.ref_type, A.entry_type, A.invoice_no,A.acc_id, A.ledger_name, A.ledger_code, A.voucher_id) B";*/
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            return $reader->readAll();
        }
        else
        {
            return [];
        }
    }

    public function get_jventries(){
        $sql ="SELECT * FROM  (
                Select A.*,B.total_amount from (select A.id, A.ref_id,  A.entry_type, A.invoice_no, A.vendor_id, A.ledger_name, case when A.type='Debit' then                   'Credit' else 'Debit' end as type, A.amount as amount1,A.voucher_id,B.ledger_name as cp_ledger_name ,0 as tax_amount ,
                '' as total_without_tax,'' as gi_date,'' as grn_approved_date_time,'' as invoice_date ,'' as debit_notes_ref,'' as gi_go_ref_no , '' as updated_date            from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'journal_voucher' and acc_id NOT IN('80',1) and company_id = '1') A 
                left join 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'journal_voucher' and acc_id IN('80',1) and company_id = '1') B 
                on(A.ref_id=B.ref_id)
                Where B.ledger_name IS NOT NULL
                GROUP by A.id, A.ref_id,  A.entry_type, A.invoice_no, A.vendor_id, A.ledger_name,A.voucher_id,B.ledger_name ) A
                left JOIN
                (select ref_id,voucher_id,amount as total_amount from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'journal_voucher'         and acc_id IN('80',1) and company_id = '1' ) B
                on A.ref_id=B.ref_id and  A.voucher_id=B.voucher_id ) A ORDER By voucher_id,ledger_name";
            $command = Yii::$app->db->createCommand($sql);
           $reader = $command->query();
           return $reader->readAll();
    }

    public function column_names(){
        $sql = "select Distinct(A.ledger_name) from (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'journal_voucher' and acc_id NOt IN('80',1) and company_id = '1') A left join (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'journal_voucher' and acc_id IN('80',1) and company_id = '1') B on(A.ref_id=B.ref_id) Where B.ledger_name IS NOT NULL";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
}