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

        $sql = "select * from acc_master where is_active = '1'".$cond." order by legal_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getOpeningBal($acc_id, $from_date){
        $status = "pending";
        // $sql = "select * from 
        //         (select A.id as ref_id, A.narration, B.id as entry_id, B.account_code as ledger_code, B.account_name as ledger_name, 
        //             B.transaction as type, case when B.transaction='Debit' then B.debit_amt else B.credit_amt end as amount, 
        //             null as is_paid, null as payment_ref, B.updated_date from 
        //         (select * from journal_voucher_details where status = '$status' and is_active = '1') A 
        //         left join 
        //         (select * from journal_voucher_entries where status = '$status' and is_active = '1' and account_code = '$acc_code') B 
        //         on (A.id = B.jv_id)) AA order by AA.ref_id, AA.entry_id limit 1";

        $sql = "select acc_id, sum(case when type='Debit' then amount*-1 else amount end) as opening_bal from ledger_entries 
                where acc_id = '$acc_id' and status = '$status' and is_active = '1' and date(updated_date) < date('$from_date') 
                group by acc_id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    // public function getLedgerTillDate($acc_code, $date, $opening_bal_id){
    //     $status = "pending";
    //     $sql = "select sum(case when AA.type='Debit' then AA.amount*-1 else AA.amount end) as tot_amount from 
    //             (select A.grn_id as ref_id, null as narration, A.code as ledger_code, A.particular as ledger_name, A.type, 
    //                 A.amount, A.is_paid, A.payment_ref, A.updated_date from 
    //             (select * from grn_acc_ledger_entries where status = '$status' and is_active = '1' and code = '$acc_code' and 
    //                 date(updated_date) < date('$date')) A 
    //             union all 
    //             select A.id as ref_id, A.narration, B.account_code as ledger_code, B.account_name as ledger_name, 
    //                 B.transaction as type, case when B.transaction='Debit' then B.debit_amt else B.credit_amt end as amount, 
    //                 null as is_paid, null as payment_ref, B.updated_date from 
    //             (select * from journal_voucher_details where status = '$status' and is_active = '1' and 
    //                 date(updated_date) < date('$date')) A 
    //             left join 
    //             (select * from journal_voucher_entries where status = '$status' and is_active = '1' and account_code = '$acc_code' and 
    //                 id != '$opening_bal_id') B 
    //             on (A.id = B.jv_id)) AA";
    //     $command = Yii::$app->db->createCommand($sql);
    //     $reader = $command->query();
    //     return $reader->readAll();
    // }

    public function getLedger($acc_id, $from_date, $to_date){
        $status = "pending";
        // $sql = "select * from 
        //         (select A.grn_id as ref_id, null as narration, A.code as ledger_code, A.particular as ledger_name, A.type, 
        //             A.amount, A.is_paid, A.payment_ref, A.updated_date from 
        //         (select * from grn_acc_ledger_entries where status = '$status' and is_active = '1' and code = '$acc_code' and 
        //             date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')) A 
        //         union all 
        //         select A.id as ref_id, A.narration, B.account_code as ledger_code, B.account_name as ledger_name, 
        //             B.transaction as type, case when B.transaction='Debit' then B.debit_amt else B.credit_amt end as amount, 
        //             null as is_paid, null as payment_ref, B.updated_date from 
        //         (select * from journal_voucher_details where status = '$status' and is_active = '1' and 
        //             date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')) A 
        //         left join 
        //         (select * from journal_voucher_entries where status = '$status' and is_active = '1' and account_code = '$acc_code' and 
        //             id != '$opening_bal_id') B 
        //         on (A.id = B.jv_id)) AA where AA.amount is not null order by AA.updated_date";

        // $sql = "select * from 
        //         (select id, ref_id, sub_ref_id, ref_type, entry_type, invoice_no, vendor_id, acc_id, ledger_name, ledger_code, 
        //             type, amount, status, created_by, updated_by, created_date, updated_date, is_paid, payment_ref, voucher_id, 
        //             ledger_type from ledger_entries where acc_id = '$acc_id' and status = '$status' and is_active = '1' and 
        //             date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') and 
        //             ref_type != 'purchase' and entry_type = 'Journal Voucher' 
        //         union all 
        //         select id, ref_id, sub_ref_id, ref_type, entry_type, invoice_no, vendor_id, acc_id, ledger_name, ledger_code, 
        //             case when type = 'Debit' then 'Credit' else 'Debit' end as type, amount, status, created_by, updated_by, 
        //             created_date, updated_date, is_paid, payment_ref, voucher_id, 
        //             ledger_type from ledger_entries where acc_id != '$acc_id' and status = '$status' and is_active = '1' and 
        //             date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') and 
        //             ref_type = 'purchase' and ledger_type = 'Sub Entry' and 
        //             voucher_id in (select distinct voucher_id from ledger_entries where acc_id = '$acc_id' and 
        //                 status = '$status' and is_active = '1' and date(updated_date) >= date('$from_date') and 
        //                 date(updated_date) <= date('$to_date') and ref_type = 'purchase' and ledger_type = 'Main Entry') 
        //         union all 
        //         select id, ref_id, sub_ref_id, ref_type, entry_type, invoice_no, vendor_id, acc_id, ledger_name, ledger_code, 
        //             type, amount, status, created_by, updated_by, created_date, updated_date, is_paid, payment_ref, voucher_id, 
        //             ledger_type from ledger_entries where acc_id = '$acc_id' and status = '$status' and is_active = '1' and 
        //             date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') and 
        //             ref_type = 'purchase' and ledger_type = 'Sub Entry' 
        //         union all 
        //         select id, ref_id, sub_ref_id, ref_type, entry_type, invoice_no, vendor_id, acc_id, ledger_name, ledger_code, 
        //             type, amount, status, created_by, updated_by, created_date, updated_date, is_paid, payment_ref, voucher_id, 
        //             ledger_type from ledger_entries where status = '$status' and is_active = '1' and 
        //             ref_type = 'payment_receipt' and ledger_type= 'Main Entry' and ref_id in 
        //             (select distinct payment_ref from ledger_entries where acc_id = '$acc_id' and status = '$status' and 
        //             is_active = '1' and is_paid = '1' and ref_type != 'payment_receipt' and payment_ref is not null)) A order by A.id";

        $sql = "select * from 
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, A.type, A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from ledger_entries where status = '$status' and is_active = '1' and 
                    date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from ledger_entries where status = '$status' and is_active = '1' and 
                    date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry') B 
                on (A.voucher_id = B.cp_voucher_id) 
                union all 
                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, A.type, A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.acc_id as cp_acc_id, A.ledger_name as cp_ledger_name, 
                    A.ledger_code as cp_ledger_code from 
                (select * from ledger_entries where status = '$status' and is_active = '1' and 
                    date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') and 
                    ref_type = 'journal_voucher') A 
                union all 
                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, A.type, A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from ledger_entries where status = '$status' and is_active = '1' and 
                    date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') and 
                    ref_type = 'payment_receipt' and ledger_type = 'Main Entry') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from ledger_entries where status = '$status' and is_active = '1' and 
                    date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') and 
                    ref_type = 'payment_receipt' and ledger_type = 'Sub Entry') B 
                on (A.voucher_id = B.cp_voucher_id)) AA 
                where AA.acc_id = '$acc_id' or AA.cp_acc_id = '$acc_id' 
                order by AA.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();

        // $sql = "select * from ledger_entries where status = '$status' and is_active = '1' and 
        //             date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') and 
        //             ref_type = 'purchase' and ledger_type = 'Sub Entry' and 
        //             voucher_id in (select distinct voucher_id from ledger_entries where status = '$status' and is_active = '1' and 
        //                 date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') and 
        //                 ref_type = 'purchase' and ledger_type = 'Main Entry')";
        // $command = Yii::$app->db->createCommand($sql);
        // $reader = $command->query();
        // $data2 = $reader->readAll();

        // $data = array_merge($data, $data2);

        return $data;
    }

    public function getTrialBalance($from_date, $to_date){
        $status = "pending";
        // $sql = "select E.*, F.type, F.debit_amt, F.credit_amt, F.amount from 
        //         (select C.*, ifnull(D.tot_amount,0) as tot_amount from 
        //         (select A.*, ifnull(B.opening_bal,0) as opening_bal from 
        //         (select * from acc_master where status = '$status' and is_active = '1') A
        //         left join 
        //         (select account_code, case when transaction='Debit' then debit_amt*-1 else credit_amt end as opening_bal from journal_voucher_entries where id in (select min(id) from journal_voucher_entries where status = 'pending' and is_active = '1' group by account_code)) B 
        //         on (A.code = B.account_code)) C
        //         left join 
        //         (select ledger_code, sum(case when AA.type='Debit' then AA.amount*-1 else AA.amount end) as tot_amount from 
        //         (select A.grn_id as ref_id, null as narration, A.code as ledger_code, A.particular as ledger_name, A.type, 
        //             A.amount, A.is_paid, A.payment_ref, A.updated_date from 
        //         (select * from grn_acc_ledger_entries where status = '$status' and is_active = '1' and 
        //             date(updated_date) < date('$from_date')) A 
        //         union all 
        //         select A.id as ref_id, A.narration, B.account_code as ledger_code, B.account_name as ledger_name, 
        //             B.transaction as type, case when B.transaction='Debit' then B.debit_amt else B.credit_amt end as amount, 
        //             null as is_paid, null as payment_ref, B.updated_date from 
        //         (select * from journal_voucher_details where status = '$status' and is_active = '1' and 
        //             date(updated_date) < date('$from_date')) A 
        //         left join 
        //         (select * from journal_voucher_entries where status = '$status' and is_active = '1' and id not in (select min(id) from journal_voucher_entries where status = 'pending' and is_active = '1' group by account_code)) B 
        //         on (A.id = B.jv_id)) AA group by ledger_code) D 
        //         on (C.code = D.ledger_code)) E 
        //         left join 
        //         (select ledger_code, AA.type, AA.debit_amt, AA.credit_amt, case when AA.type='Debit' then AA.amount*-1 else AA.amount end as amount from 
        //         (select A.grn_id as ref_id, null as narration, A.code as ledger_code, A.particular as ledger_name, A.type, 
        //             case when A.type = 'Debit' then A.amount else 0 end as debit_amt, case when A.type = 'Credit' then A.amount else 0 end as credit_amt, 
        //             A.amount, A.is_paid, A.payment_ref, A.updated_date from 
        //         (select * from grn_acc_ledger_entries where status = '$status' and is_active = '1' and 
        //             date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')) A 
        //         union all 
        //         select A.id as ref_id, A.narration, B.account_code as ledger_code, B.account_name as ledger_name, 
        //             B.transaction as type, B.debit_amt, B.credit_amt, case when B.transaction='Debit' then B.debit_amt else B.credit_amt end as amount, 
        //             null as is_paid, null as payment_ref, B.updated_date from 
        //         (select * from journal_voucher_details where status = '$status' and is_active = '1' and 
        //             date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')) A 
        //         left join 
        //         (select * from journal_voucher_entries where status = '$status' and is_active = '1' and id not in (select min(id) from journal_voucher_entries where status = 'pending' and is_active = '1' group by account_code)) B 
        //         on (A.id = B.jv_id)) AA where AA.amount<>0) F 
        //         on (E.code = F.ledger_code)
        //         order by E.code";

        // $sql = "select A.opening_bal, B.* from 
        //         (select acc_id, sum(case when type='Debit' then amount*-1 else amount end) as opening_bal from ledger_entries 
        //         where status = '$status' and is_active = '1' and date(updated_date) < date('$from_date') group by acc_id) A 
        //         left join 
        //         (select * from ledger_entries where status = '$status' and is_active = '1' and 
        //             date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')) B 
        //         on (A.acc_id = B.acc_id) 
        //         order by B.acc_id, B.id";

        $sql = "select E.id as account_id, E.code, E.legal_name, E.category_1, E.category_2, E.category_3, E.acc_category, 
                        E.bus_category, E.opening_bal, F.* from 
                (select C.id, C.code, C.legal_name, C.category_1, C.category_2, C.category_3, C.acc_category, C.bus_category, D.opening_bal from 
                (select A.*, concat_ws(',', A.category_1, A.category_2, A.category_3) as acc_category, B.bus_category from 
                (select * from acc_master where is_active = '1' and status = '$status') A 
                left join 
                (select acc_id, GROUP_CONCAT(category_name) as bus_category from acc_categories 
                    where is_active = '1'and status = '$status' group by acc_id) B 
                on (A.id = B.acc_id)) C 
                left join 
                (select acc_id, sum(case when type='Debit' then amount*-1 else amount end) as opening_bal from ledger_entries 
                where status = '$status' and is_active = '1' and date(updated_date) < date('$from_date') group by acc_id) D 
                on (C.id = D.acc_id)) E 
                left join 
                (select acc_id, sum(case when type='Debit' then amount else 0 end) as debit_amt, 
                        sum(case when type='Credit' then amount else 0 end) as credit_amt 
                from ledger_entries where status = '$status' and is_active = '1' and 
                    date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')
                group by acc_id) F 
                on (E.id = F.acc_id) 
                order by E.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
}