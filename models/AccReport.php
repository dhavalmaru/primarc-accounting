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
                on (A.voucher_id = B.cp_voucher_id)) AA 
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
                on (A.voucher_id = B.cp_voucher_id)) AA 
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
                    where is_active = '1'and status = '$status' group by acc_id and company_id = '$company_id') B 
                on (A.id = B.acc_id)) C 
                left join 
                (select acc_id, sum(case when type='Debit' then amount*-1 else amount end) as opening_bal from acc_ledger_entries 
                where status = '$status' and is_active = '1' and date(ref_date) < date('$from_date') and company_id = '$company_id' group by acc_id) D 
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
                date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and company_id = '$company_id' 
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
}