<?php

namespace app\models;

use Yii;
use yii\base\Model;

class AccReport extends Model
{
    public function getAccountDetails($id="") {
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

    public function getVendorname() {
        $sql = "select id, legal_name from acc_master where is_active = '1' and status = 'approved' and type='Vendor Goods' order by legal_name ";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getOpeningBal($acc_id, $from_date) {
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

    public function getLedger($acc_id, $from_date, $to_date) {
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
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'journal_voucher' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
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

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no from acc_ledger_entries A 
                    left join goods_inward_outward B on (A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and 
                        date(A.ref_date) >= date('$from_date') and date(A.ref_date) <= date('$to_date') and 
                        A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and 
                        A.company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no from acc_ledger_entries A 
                    left join goods_inward_outward B on (A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and 
                        date(A.ref_date) >= date('$from_date') and date(A.ref_date) <= date('$to_date') and 
                        A.ref_type = 'go_debit_details' and A.acc_id!='$acc_id' and A.ledger_type = 'Main Entry' and 
                        A.company_id = '$company_id' and A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'go_debit_details' and acc_id='$acc_id' and ledger_type = 'Main Entry' and company_id = '$company_id' and 
                    entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
                on(A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'other_debit_credit' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'other_debit_credit' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'promotion' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'promotion' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'sales_upload' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                ) AA 
                where AA.acc_id = '$acc_id' or AA.cp_acc_id = '$acc_id' 
                order by AA.ref_date, AA.id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getstatemaster() {
       $sql = "select state_name,id,state_code from state_master WHERE is_active = '1'";
       $command = Yii::$app->db->createCommand($sql);
       $reader = $command->query();
       return $reader->readAll();
    }

    public function getDetailledger($account, $vouchertype, $from_date, $to_date, $date_type, $state) {
        // if($date_type=='invoice_date')
        //     $where_condition = " date(invoice_date)>='$from_date' and date(invoice_date)<='$to_date' ";
        // else if($date_type=='grn_approved_date_time')
        //     $where_condition = " date(grn_approved_date_time)>='$from_date' and date(grn_approved_date_time)<='$to_date' ";
        // else if($date_type=='gi_date')
        //     $where_condition = " date(gi_date)>='$from_date' and date(gi_date)<='$to_date' ";
        // else if($date_type=='updated_date')
        //     $where_condition = " date(updated_date)>='$from_date' and date(updated_date)<='$to_date' ";
        // else
        //     $where_condition=' ';

        // if($date_type=='updated_date') {
        //     $where2 = " Where date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') ";
        //     $where3 = " And date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') "; 
        // } else {
        //     $where2 = "";
        //     $where3 = "";
        // }
        
        if($date_type=='invoice_date') {
            $where2 = " Where  date(invoice_date) >= date('$from_date') and date(invoice_date) <= date('$to_date') ";
        } else if($date_type=='grn_approved_date_time') {
            $where2 = " Where  date(grn_approved_date_time) >= date('$from_date') and date(grn_approved_date_time) <= date('$to_date') ";
        } else if($date_type=='gi_date') {
            $where2 = " Where  date(gi_date) >= date('$from_date') and date(gi_date) <= date('$to_date') ";
        } else if($date_type=='updated_date') {
            $where2 = " Where  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') ";
        } else {
            $where2 = "";
        }
        

        $session = Yii::$app->session;
        $company_id = $session['company_id']; 
        //$account;
        if($account!='') { 
            $sql = 'select * from (';

            if(in_array('purchase',$vouchertype)) {
                $sql.="select A.* from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, A.amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_date, A.grn_approved_date_time, A.invoice_date, gi_id as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when B.cp_acc_id IN($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, A.gi_id, A.gi_date, A.grn_approved_date_time, A.invoice_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_id, C.gi_date, C.grn_approved_date_time, D.invoice_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join grn C on (A.ref_id=C.grn_id) left join goods_inward_outward_invoices D on (C.gi_id = D.gi_go_ref_no and A.invoice_no = D.invoice_no) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account))) A  ".$where2;
            }
            
            if(in_array('journal_voucher',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select A.* from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, '' as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'journal_voucher' and A.acc_id not IN ($account) and A.company_id = $company_id) A 
                        left join 
                        (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'journal_voucher' and acc_id in ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account)  or A.cp_acc_id IN ($account))) A  ".$where2;
            }

            if(in_array('payment_receipt',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select A.* from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, '' as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, B.cp_acc_id as acc_id, B.cp_ledger_name as ledger_name, B.cp_ledger_code as ledger_code, case when A.acc_id IN($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount*-1 else A.amount end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, B.acc_type, A.acc_id as cp_acc_id, A.ledger_name as cp_ledger_name, A.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'payment_receipt' and A.ledger_type = 'Sub Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct A.voucher_id as cp_voucher_id, A.acc_id as cp_acc_id, A.ledger_name as cp_ledger_name, A.ledger_code as cp_ledger_code, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'payment_receipt' and A.ledger_type = 'Main Entry' and A.company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account))) A  ".$where2;
            }

            if(in_array('go_debit_details',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select A.* from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_go_date_time as gi_date, '' as grn_approved_date_time, A.invoice_created_date as invoice_date, A.gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when B.cp_acc_id IN ($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.gi_go_ref_no, A.gi_go_date_time, A.invoice_created_date, A.acc_type, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_go_ref_no, C.gi_go_date_time, D.invoice_created_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join goods_inward_outward C on (A.ref_id=C.gi_go_id) left join prepare_go D on (C.pre_go_ref=D.prepare_go_id and A.invoice_no = D.invoice_number) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account))) A  ".$where2;

                if($sql!='') {
                    $sql.=' UNION ALL';
                }

                $sql.=" select A.* from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_go_date_time as gi_date, '' as grn_approved_date_time, A.invoice_created_date as invoice_date, A.gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, A.gi_go_ref_no, A.gi_go_date_time, A.invoice_created_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_go_ref_no, C.gi_go_date_time, D.invoice_created_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join goods_inward_outward C on (A.ref_id=C.gi_go_id) left join prepare_go D on (C.pre_go_ref=D.prepare_go_id and A.invoice_no = D.invoice_number) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'go_debit_details' and A.acc_id not in ($account) and A.ledger_type = 'Main Entry' and A.company_id = '$company_id' and A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
                        left join 
                        (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'go_debit_details' and acc_id in ($account) and ledger_type = 'Main Entry' and company_id = '$company_id' and entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B on (A.ref_id = B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account))) A  ".$where2;
            }

            if(in_array('other_debit_credit',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select A.* from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, ref_date as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'other_debit_credit' and A.acc_id not in ($account) and A.company_id = $company_id) A 
                        left join 
                        (select * from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'other_debit_credit' and acc_id IN ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account))) A  ".$where2;
            }

            if(in_array('promotion',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select A.* from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, ref_date as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'promotion' and A.acc_id not in ($account) and A.company_id = $company_id) A 
                        left join 
                        (select * from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'promotion' and acc_id IN ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account))) A  ".$where2;
            }

            $sql.=") AA";

            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            return $reader->readAll();
        } else {
            return [];
        }
    }

    public function column_names($account, $vouchertype, $from_date, $to_date, $date_type, $state) {
        // if($date_type=='invoice_date')
        //     $where_condition = " date(invoice_date)>='$from_date' and date(invoice_date)<='$to_date' ";
        // else if($date_type=='grn_approved_date_time')
        //     $where_condition = " date(grn_approved_date_time)>='$from_date' and date(grn_approved_date_time)<='$to_date' ";
        // else if($date_type=='gi_date')
        //     $where_condition = " date(gi_date)>='$from_date' and date(gi_date)<='$to_date' ";
        // else if($date_type=='updated_date')
        //     $where_condition = " date(updated_date)>='$from_date' and date(updated_date)<='$to_date' ";
        // else
        //     $where_condition=' ';

        // if($date_type=='updated_date') {
        //     $where2 = " Where  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') ";
        //     $where3 = " And  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') "; 
        // } else {
        //     $where2 = "";
        //     $where3 = "";
        // }

        if($date_type=='invoice_date') {
            $where2 = " Where  date(invoice_date) >= date('$from_date') and date(invoice_date) <= date('$to_date') ";
        } else if($date_type=='grn_approved_date_time') {
            $where2 = " Where  date(grn_approved_date_time) >= date('$from_date') and date(grn_approved_date_time) <= date('$to_date') ";
        } else if($date_type=='gi_date') {
            $where2 = " Where  date(gi_date) >= date('$from_date') and date(gi_date) <= date('$to_date') ";
        } else if($date_type=='updated_date') {
            $where2 = " Where  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') ";
        } else {
            $where2 = "";
        }
        

        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        if($account!='' and count($vouchertype)>0) { 
            $sql = "select distinct ledger_name from (";

            if(in_array('purchase',$vouchertype)) {
                $sql.="select distinct ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, A.amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_date, A.grn_approved_date_time, A.invoice_date, gi_id as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when B.cp_acc_id IN($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, A.gi_id, A.gi_date, A.grn_approved_date_time, A.invoice_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_id, C.gi_date, C.grn_approved_date_time, D.invoice_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join grn C on (A.ref_id=C.grn_id) left join goods_inward_outward_invoices D on (C.gi_id = D.gi_go_ref_no and A.invoice_no = D.invoice_no) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Purchase', 'Goods Sales', 'CGST', 'SGST', 'IGST')) A  ".$where2;
            }
            
            if(in_array('journal_voucher',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, '' as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'journal_voucher' and A.acc_id not IN ($account) and A.company_id = $company_id) A 
                        left join 
                        (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'journal_voucher' and acc_id in ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account)  or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Purchase', 'Goods Sales', 'CGST', 'SGST', 'IGST')) A  ".$where2;
            }

            if(in_array('payment_receipt',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, '' as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, B.cp_acc_id as acc_id, B.cp_ledger_name as ledger_name, B.cp_ledger_code as ledger_code, case when A.acc_id IN($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount*-1 else A.amount end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, B.acc_type, A.acc_id as cp_acc_id, A.ledger_name as cp_ledger_name, A.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'payment_receipt' and A.ledger_type = 'Sub Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct A.voucher_id as cp_voucher_id, A.acc_id as cp_acc_id, A.ledger_name as cp_ledger_name, A.ledger_code as cp_ledger_code, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'payment_receipt' and A.ledger_type = 'Main Entry' and A.company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Purchase', 'Goods Sales', 'CGST', 'SGST', 'IGST')) A  ".$where2;
            }

            if(in_array('go_debit_details',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_go_date_time as gi_date, '' as grn_approved_date_time, A.invoice_created_date as invoice_date, A.gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when B.cp_acc_id IN ($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.gi_go_ref_no, A.gi_go_date_time, A.invoice_created_date, A.acc_type, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_go_ref_no, C.gi_go_date_time, D.invoice_created_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join goods_inward_outward C on (A.ref_id=C.gi_go_id) left join prepare_go D on (C.pre_go_ref=D.prepare_go_id and A.invoice_no = D.invoice_number) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Purchase', 'Goods Sales', 'CGST', 'SGST', 'IGST')) A  ".$where2;

                if($sql!='') {
                    $sql.=' UNION ALL';
                }

                $sql.=" select distinct ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_go_date_time as gi_date, '' as grn_approved_date_time, A.invoice_created_date as invoice_date, A.gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, A.gi_go_ref_no, A.gi_go_date_time, A.invoice_created_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_go_ref_no, C.gi_go_date_time, D.invoice_created_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join goods_inward_outward C on (A.ref_id=C.gi_go_id) left join prepare_go D on (C.pre_go_ref=D.prepare_go_id and A.invoice_no = D.invoice_number) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'go_debit_details' and A.acc_id not in ($account) and A.ledger_type = 'Main Entry' and A.company_id = '$company_id' and A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
                        left join 
                        (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'go_debit_details' and acc_id in ($account) and ledger_type = 'Main Entry' and company_id = '$company_id' and entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B on (A.ref_id = B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Purchase', 'Goods Sales', 'CGST', 'SGST', 'IGST')) A  ".$where2;
            }

            if(in_array('other_debit_credit',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, ref_date as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'other_debit_credit' and A.acc_id not in ($account) and A.company_id = $company_id) A 
                        left join 
                        (select * from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'other_debit_credit' and acc_id IN ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Purchase', 'Goods Sales', 'CGST', 'SGST', 'IGST')) A  ".$where2;
            }

            if(in_array('promotion',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, ref_date as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'promotion' and A.acc_id not in ($account) and A.company_id = $company_id) A 
                        left join 
                        (select * from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'promotion' and acc_id IN ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Purchase', 'Goods Sales', 'CGST', 'SGST', 'IGST')) A  ".$where2;
            }

            $sql.=") AA";

            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
             
            if(count($result)>0) {
                return $result = array_map("unserialize", array_unique(array_map("serialize", $result)));
            } else {
                return [];
            }
        } else {
            return [];
        }
    }
    
    public function gettaxwisebifercation($account, $vouchertype, $from_date, $to_date, $date_type, $state) {
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

        $session = Yii::$app->session;
        $company_id = $session['company_id']; 

        $sql="select * from 
            (select * from 
            (select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,G.grn_approved_date_time,
                F.updated_date,'' as debit_note_ref from 
            (select A.ref_id,'purchase' as ref_type,A.cp_acc_id, trim(A.invoice_no) as invoice_no,A.voucher_id ,A.cp_ledger_name,
                TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as cost_inc_tax,
                TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)+IFNULL(other_charges, 0)),2) as total_deduction,
                total_tax_amount, total_purchase_amount as purchase, IFNULL(other_charges,0) as other_charges,
                A.ledger_name, A.entry_type, A.percentage from 
            (select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id, cp_ledger_name,
                sum(amount)  as `total_tax_amount`,A.ledger_name,A.entry_type ,percentage from 
            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type,Replace(A.entry_type,'CGST','SGST') as entry_type ,
                CASE WHEN entry_type = 'IGST' THEN Replace(Substring_index(ledger_name, '-', -1),'%', '') 
                ELSE (2 * Replace(Substring_index(ledger_name, '-', -1), '%', '')) END as percentage, A.invoice_no, A.vendor_id, 
                A.acc_id,Replace(A.ledger_name,'CGST','SGST') as ledger_name,A.ledger_code, 
                case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
            on (A.voucher_id = B.cp_voucher_id)
            Where B.cp_acc_id IN ($account) AND entry_type IN('CGST','IGST','SGST')) A
            GROUP BY A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id,cp_ledger_name,ledger_name,entry_type,percentage) A
            left join
            (select A.ref_id, A.invoice_no, A.percentage, sum(amount) as total_purchase_amount from 
            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, 
                case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type,
                Replace(Substring_index(ledger_name, '-', -1),'%', '') as percentage, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
            on (A.voucher_id = B.cp_voucher_id)
            Where B.cp_acc_id IN ($account) AND entry_type IN('Taxable Amount')) A
            GROUP BY A.ref_id, A.invoice_no, A.percentage) D 
            on (A.ref_id=D.ref_id and A.invoice_no=D.invoice_no and A.percentage=D.percentage) 
            left join
            (select A.ref_id, A.invoice_no,sum(amount) as other_charges from 
            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                A.ledger_code, case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
            on (A.voucher_id = B.cp_voucher_id)
            Where B.cp_acc_id IN ($account) AND entry_type IN('Other Charges')) A
            GROUP BY A.ref_id, A.invoice_no) E
            on (A.ref_id=E.ref_id and A.invoice_no=E.invoice_no)) C
            left JOIN
            (select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G 
            on C.ref_id=G.grn_id
            left join
            (select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E 
            on C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no ";
            if($state!="")
            {
            $sql.=" join
            (select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
            on G.warehouse_id=W.warehouse_code ";
            }
            $sql.=" left join 
            (select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on C.ref_id=F.grn_id) D
            
            Union 

            select * from 
            (select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,G.grn_approved_date_time,F.updated_date,D.debit_note_ref from 
            (select A.ref_id,'Debit Note' as ref_type,A.cp_acc_id,trim(A.invoice_no) as invoice_no ,A.voucher_id ,A.cp_ledger_name,
                TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as cost_inc_tax,
                TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as total_deduction,
                total_tax_amount,total_purchase_amount as purchase,0 as other_charges,A.ledger_name,A.entry_type,A.percentage from 
            (select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,
                sum(amount)  as `total_tax_amount` ,A.ledger_name as ledger_name ,A.entry_type ,percentage from 
            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type,Replace(A.entry_type,'cgst','sgst') as entry_type ,
                CASE WHEN Substring_index(entry_type, '_', -1) = 'igst' THEN Replace(Substring_index(ledger_name, '-', -1),'%', '') 
                ELSE (2 * Replace(Substring_index(ledger_name, '-', -1), '%', '')) END as percentage,
                A.invoice_no, A.vendor_id, A.acc_id,Replace(ledger_name,'CGST','SGST') as ledger_name,A.ledger_code, 
                case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
            on (A.voucher_id = B.cp_voucher_id)
            Where B.cp_acc_id IN ($account) AND entry_type IN('margindiff_cgst','margindiff_sgst','margindiff_igst',
                'shortage_cgst' ,'shortage_sgst' ,'shortage_igst' , 'expiry_cgst' ,'expiry_sgst' ,'expiry_igst' ,'damage_cgst', 'damage_sgst','damage_igst')) A
            GROUP BY A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name ,ledger_name,entry_type,percentage) A
            left join
            (select A.ref_id, A.invoice_no, A.percentage, sum(amount) as total_purchase_amount from 
            (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, 
                case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                Replace(Substring_index(ledger_name, '-', -1), '%', '') as percentage, 
                A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
            on (A.voucher_id = B.cp_voucher_id)
            Where B.cp_acc_id IN ($account) AND entry_type IN('margindiff_cost','shortage_cost','expiry_cost','damage_cost') ) A
            GROUP BY A.ref_id, A.invoice_no, A.percentage) D 
            on (A.ref_id=D.ref_id and A.invoice_no=D.invoice_no and A.percentage=D.percentage)) C
            left join
            (select debit_note_ref ,grn_id,invoice_no from acc_grn_debit_notes) D 
            on (C.ref_id=D.grn_id and C.invoice_no=D.invoice_no)
            left JOIN
            (select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G 
            on (C.ref_id=G.grn_id)
            left join
            (select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E 
            on (C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no) ";
            if($state!="")
            {
                $sql.=" join
                (select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
                on G.warehouse_id=W.warehouse_code ";
            }
            $sql.=" left join 
            (select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F 
            on (C.ref_id=F.grn_id)) D) E 
            Where $where_condition ORDER BY ref_id ASC,invoice_no ASC,ref_type DESC,percentage ASC";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
    
    public function tax_wise_column_old($account, $vouchertype, $from_date, $to_date, $date_type, $state) {
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

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql="select distinct(percentage) as percentage from 
                (select * from 
                (select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,G.grn_approved_date_time,
                    F.updated_date,'' as debit_note_ref from 
                (select A.ref_id,'purchase' as ref_type,A.cp_acc_id,trim(A.invoice_no) as invoice_no,A.voucher_id,A.cp_ledger_name,
                    TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as cost_inc_tax,
                    TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)+IFNULL(other_charges, 0)),2) as total_deduction,
                    total_tax_amount, total_purchase_amount as purchase,
                    IFNULL(other_charges,0) as other_charges,A.ledger_name,A.entry_type,percentage from 
                (select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id, cp_ledger_name,
                    sum(amount)  as `total_tax_amount`,A.ledger_name,A.entry_type ,percentage from 
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type,Replace(A.entry_type,'CGST','SGST') as entry_type ,
                    CASE WHEN entry_type = 'IGST' THEN Replace(Substring_index(ledger_name, '-', -1),'%', '') 
                     ELSE (2 * Replace(Substring_index(ledger_name, '-', -1), '%', '')) END as percentage, 
                    A.invoice_no, A.vendor_id, A.acc_id,Replace(A.ledger_name,'CGST','SGST') as ledger_name,
                    A.ledger_code, case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type,
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id)
                Where B.cp_acc_id IN ($account) AND entry_type IN('CGST','IGST','SGST')) A
                GROUP BY A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name,entry_type,percentage) A
                left join 
                (select A.ref_id, A.invoice_no, sum(amount) as total_purchase_amount from 
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, 
                    case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id)
                Where B.cp_acc_id IN ($account) AND entry_type IN('Taxable Amount')) A
                GROUP BY A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name) D 
                on A.ref_id=D.ref_id and A.invoice_no=D.invoice_no
                left join
                (select A.ref_id, A.invoice_no,sum(amount) as other_charges
                    from (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id)
                Where B.cp_acc_id IN ($account) AND entry_type IN('Other Charges') ) A
                GROUP BY A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name ) E
                on A.ref_id=E.ref_id and A.invoice_no=E.invoice_no ) C
                left JOIN
                (select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G on C.ref_id=G.grn_id
                left join
                (select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no ";
                if($state!="")
                {
                $sql.=" join
                (select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
                on G.warehouse_id=W.warehouse_code ";
                }
                $sql.=" left join 
                (select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on C.ref_id=F.grn_id) D
                Union 
                select * from (
                select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,
                    G.grn_approved_date_time,F.updated_date,D.debit_note_ref from 
                    (select A.ref_id,'Debit Note' as ref_type,A.cp_acc_id,trim(A.invoice_no) as invoice_no ,A.voucher_id ,A.cp_ledger_name,
                    TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as cost_inc_tax,TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as total_deduction,
                    total_tax_amount , total_purchase_amount as purchase ,0 as other_charges,A.ledger_name,A.entry_type ,percentage
                    from (select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,
                    sum(amount)  as `total_tax_amount` ,A.ledger_name as ledger_name ,A.entry_type ,percentage
                    from (select A.id, A.ref_id, A.sub_ref_id, A.ref_type,Replace(A.entry_type,'cgst','sgst') as entry_type ,
                    CASE WHEN Substring_index(entry_type, '_', -1) = 'igst' THEN Replace(Substring_index(ledger_name, '-', -1),'%', '') 
                     ELSE (2 * Replace(Substring_index(ledger_name, '-', -1), '%', '')) END as percentage, 
                     A.invoice_no, A.vendor_id, A.acc_id,Replace(ledger_name,'CGST','SGST') as ledger_name,
                     A.ledger_code, case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                    left join 
                    (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                    on (A.voucher_id = B.cp_voucher_id)
                    Where B.cp_acc_id IN ($account) AND entry_type IN('margindiff_cgst','margindiff_sgst','margindiff_igst','shortage_cgst' ,'shortage_sgst' ,'shortage_igst' , 'expiry_cgst' ,'expiry_sgst' ,'expiry_igst' ,'damage_cgst', 'damage_sgst','damage_igst') ) A
                    GROUP BY
                    A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name ,ledger_name,entry_type,percentage)A
                left join
                (select A.ref_id, A.invoice_no ,
                    sum(amount) as total_purchase_amount
                    from (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                    on (A.voucher_id = B.cp_voucher_id)
                    Where B.cp_acc_id IN ($account) AND entry_type IN('margindiff_cost','shortage_cost','expiry_cost','damage_cost') ) A
                    GROUP BY A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name) D 
                    on A.ref_id=D.ref_id and A.invoice_no=D.invoice_no) C
                left join
                (select debit_note_ref ,grn_id,invoice_no from acc_grn_debit_notes) D  on 
                    C.ref_id=D.grn_id and C.invoice_no=D.invoice_no
                left JOIN
                (select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G on C.ref_id=G.grn_id
                left join
                (select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no ";
                if($state!="")
                {
                    $sql.=" join
                    (select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
                    on G.warehouse_id=W.warehouse_code ";
                }
                $sql.=" left join 
                (select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F 
                on C.ref_id=F.grn_id) D) E 
                Where $where_condition order by percentage";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function tax_wise_column($account, $vouchertype, $from_date, $to_date, $date_type, $state) {
        if($date_type=='invoice_date') {
            $where2 = " Where  date(invoice_date) >= date('$from_date') and date(invoice_date) <= date('$to_date') ";
        } else if($date_type=='grn_approved_date_time') {
            $where2 = " Where  date(grn_approved_date_time) >= date('$from_date') and date(grn_approved_date_time) <= date('$to_date') ";
        } else if($date_type=='gi_date') {
            $where2 = " Where  date(gi_date) >= date('$from_date') and date(gi_date) <= date('$to_date') ";
        } else if($date_type=='updated_date') {
            $where2 = " Where  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') ";
        } else {
            $where2 = "";
        }
        
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        if($account!='' and count($vouchertype)>0) { 
            $sql = "select distinct acc_type, ledger_name, percentage from 
                    (select case when acc_type in('Goods Purchase','CGST','SGST','IGST') then 'ZZPurchase' else acc_type end as acc_type, ledger_name, case when acc_type in('CGST','SGST') then percentage*2 else percentage end as percentage from 
                    (select distinct acc_type, case when acc_type in('Goods Purchase','CGST','SGST','IGST') then '' else ledger_name end as ledger_name, replace(substring_index(ledger_name,'-',-1),'%','') as percentage from (";

            if(in_array('purchase',$vouchertype)) {
                $sql.="select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, A.amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_date, A.grn_approved_date_time, A.invoice_date, gi_id as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when B.cp_acc_id IN($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, A.gi_id, A.gi_date, A.grn_approved_date_time, A.invoice_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_id, C.gi_date, C.grn_approved_date_time, D.invoice_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join grn C on (A.ref_id=C.grn_id) left join goods_inward_outward_invoices D on (C.gi_id = D.gi_go_ref_no and A.invoice_no = D.invoice_no) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }
            
            if(in_array('journal_voucher',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, '' as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'journal_voucher' and A.acc_id not IN ($account) and A.company_id = $company_id) A 
                        left join 
                        (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'journal_voucher' and acc_id in ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account)  or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }

            if(in_array('payment_receipt',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, '' as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, B.cp_acc_id as acc_id, B.cp_ledger_name as ledger_name, B.cp_ledger_code as ledger_code, case when A.acc_id IN($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount*-1 else A.amount end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, B.acc_type, A.acc_id as cp_acc_id, A.ledger_name as cp_ledger_name, A.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'payment_receipt' and A.ledger_type = 'Sub Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct A.voucher_id as cp_voucher_id, A.acc_id as cp_acc_id, A.ledger_name as cp_ledger_name, A.ledger_code as cp_ledger_code, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'payment_receipt' and A.ledger_type = 'Main Entry' and A.company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }

            if(in_array('go_debit_details',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_go_date_time as gi_date, '' as grn_approved_date_time, A.invoice_created_date as invoice_date, A.gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when B.cp_acc_id IN ($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.gi_go_ref_no, A.gi_go_date_time, A.invoice_created_date, A.acc_type, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_go_ref_no, C.gi_go_date_time, D.invoice_created_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join goods_inward_outward C on (A.ref_id=C.gi_go_id) left join prepare_go D on (C.pre_go_ref=D.prepare_go_id and A.invoice_no = D.invoice_number) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;

                if($sql!='') {
                    $sql.=' UNION ALL';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_go_date_time as gi_date, '' as grn_approved_date_time, A.invoice_created_date as invoice_date, A.gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, A.gi_go_ref_no, A.gi_go_date_time, A.invoice_created_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_go_ref_no, C.gi_go_date_time, D.invoice_created_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join goods_inward_outward C on (A.ref_id=C.gi_go_id) left join prepare_go D on (C.pre_go_ref=D.prepare_go_id and A.invoice_no = D.invoice_number) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'go_debit_details' and A.acc_id not in ($account) and A.ledger_type = 'Main Entry' and A.company_id = '$company_id' and A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
                        left join 
                        (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'go_debit_details' and acc_id in ($account) and ledger_type = 'Main Entry' and company_id = '$company_id' and entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B on (A.ref_id = B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }

            if(in_array('other_debit_credit',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, ref_date as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'other_debit_credit' and A.acc_id not in ($account) and A.company_id = $company_id) A 
                        left join 
                        (select * from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'other_debit_credit' and acc_id IN ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }

            if(in_array('promotion',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, ref_date as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'promotion' and A.acc_id not in ($account) and A.company_id = $company_id) A 
                        left join 
                        (select * from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'promotion' and acc_id IN ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }

            $sql.=") A) A) A order by acc_type, cast(percentage as signed)";

            // echo $sql.'<br/><br/>';

            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
             
            if(count($result)>0) {
                return $result = array_map("unserialize", array_unique(array_map("serialize", $result)));
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    public function getstatewisebifercation($account, $vouchertype,$from_date, $to_date,$date_type,$state) {
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
        $session = Yii::$app->session;
        $company_id = $session['company_id']; 

        $sql = "select * FROM
                (select * from 
                (select A.*,F.updated_date,B.total_amount as total_deduction from 
                (select C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,'' as debit_note_ref,E.gi_go_ref_no,G.warehouse_id from 
                (select A.*,B.total_purchase_amount as purchase ,Truncate((total_purchase_amount+total_tax_amount),2) as cost_inc_tax from 
                (select A.ref_id, A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no, A.voucher_id, B.cp_ledger_name, A.percentage, 
                    A.entry_type, A.ledger_name from 
                (select ref_id, ref_type, invoice_no, voucher_id, amount, Replace(entry_type,'SGST','CGST') as entry_type, 
                Replace(ledger_name,'SGST','CGST') as ledger_name,
                CASE WHEN entry_type = 'IGST' THEN Replace(Substring_index(ledger_name, '-', -1),'%', '') 
                    ELSE (2 * Replace(Substring_index(ledger_name, '-', -1), '%', '')) END as percentage 
                from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries 
                    where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and 
                    ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id IN ($account) AND entry_type IN('CGST','IGST','SGST') 
                GROUP By A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name, percentage, entry_type ,ledger_name) A 
                left join 
                (select A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id , cp_ledger_name,REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1, Replace(Substring_index(ledger_name, '-', -1),'%', '' ) as percentage from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) Where B.cp_acc_id In($account) AND entry_type IN('Taxable Amount') 
                GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name, ledger_name ) B 
                on A.ref_id=B.ref_id and A.invoice_no=B.invoice_no and A.percentage=B.percentage) C 
                left JOIN 
                (select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn) G on C.ref_id=G.grn_id 
                left join 
                (select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no) A ";
                if($state!="")
                {
                  $sql.="join
                    (select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                    on A.warehouse_id=G.warehouse_code ";  
                }
               $sql.= " left join 
                (select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id 
                left JOIN 
                (select ref_id,voucher_id,amount as total_amount,entry_type from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and acc_id IN($account) and company_id = '$company_id' and entry_type='Total Amount') B 
                on A.ref_id=B.ref_id and A.voucher_id=B.voucher_id) D 
                Union 

                select * from 
                (select A.*,F.updated_date,B.total_amount from 
                (select C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,D.debit_note_ref,E.gi_go_ref_no,G.warehouse_id from 
                (select A.*,B.total_purchase_amount as purchase ,Truncate((total_purchase_amount+total_tax_amount),2) as cost_inc_tax from 
                (select A.ref_id, A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no, A.voucher_id, B.cp_ledger_name, A.percentage, 
                    A.entry_type, A.ledger_name from 
                (select ref_id, 'Debit Note' as ref_type, invoice_no, voucher_id, amount, Replace(entry_type,'sgst','cgst') as entry_type, 
                Replace(ledger_name,'SGST','CGST') as ledger_name,
                CASE WHEN entry_type = 'IGST' THEN Replace(Substring_index(ledger_name, '-', -1),'%', '') 
                    ELSE (2 * Replace(Substring_index(ledger_name, '-', -1), '%', '')) END as percentage from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries 
                where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id) Where B.cp_acc_id IN ($account) 
                    AND entry_type IN('margindiff_cgst','margindiff_sgst','margindiff_igst','shortage_cgst' ,'shortage_sgst' ,'shortage_igst' , 'expiry_cgst' ,'expiry_sgst' ,'expiry_igst' ,'damage_cgst', 'damage_sgst','damage_igst') 
                GROUP By A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,percentage,entry_type ,ledger_name ) A
                     left join (select A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id , cp_ledger_name,REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1,Replace(Substring_index(ledger_name, '-', -1), '%', '') as percentage from (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                     left join (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id) 
                     Where B.cp_acc_id In($account) AND entry_type IN('margindiff_cost','shortage_cost','expiry_cost','damage_cost') GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,entry_type ,ledger_name ) B on A.ref_id=B.ref_id and A.invoice_no=B.invoice_no and A.percentage=B.percentage ) C 
                     left join (select debit_note_ref as debit_note_ref,grn_id,invoice_no from acc_grn_debit_notes ) D on C.ref_id=D.grn_id and C.invoice_no=D.invoice_no 
                     left JOIN (select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn ) G on C.ref_id=G.grn_id 
                     left join (select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no ) A ";
                    if($state!="")
                    {
                      $sql.=" join
                        (select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                        on A.warehouse_id=G.warehouse_code ";  
                    }
                    $sql.=" left join (select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id 
                     left JOIN (select ref_id,voucher_id,amount as total_amount,entry_type from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and acc_id IN($account) and company_id = '$company_id' and entry_type='Total Deduction') B on A.ref_id=B.ref_id and A.voucher_id=B.voucher_id ) E ORDER BY ref_id ASC,invoice_no ASC,ref_type DESC ,percentage ASC )A Where $where_condition";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
   
    public function state_wise_column($account, $vouchertype, $from_date, $to_date, $date_type, $state) {
        if($date_type=='invoice_date') {
            $where2 = " Where  date(invoice_date) >= date('$from_date') and date(invoice_date) <= date('$to_date') ";
        } else if($date_type=='grn_approved_date_time') {
            $where2 = " Where  date(grn_approved_date_time) >= date('$from_date') and date(grn_approved_date_time) <= date('$to_date') ";
        } else if($date_type=='gi_date') {
            $where2 = " Where  date(gi_date) >= date('$from_date') and date(gi_date) <= date('$to_date') ";
        } else if($date_type=='updated_date') {
            $where2 = " Where  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date') ";
        } else {
            $where2 = "";
        }
        
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        if($account!='' and count($vouchertype)>0) { 
            $sql = "select distinct acc_type, state, percentage from 
                    (select case when acc_type in('Goods Purchase','CGST','SGST','IGST') then 'ZZPurchase' else acc_type end as acc_type, state, case when acc_type in('CGST','SGST') then percentage*2 else percentage end as percentage from 
                    (select distinct acc_type, substring_index(substring_index(ledger_name,'-',2),'-',-1) as state, replace(substring_index(ledger_name,'-',-1),'%','') as percentage from (";

            if(in_array('purchase',$vouchertype)) {
                $sql.="select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, A.amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_date, A.grn_approved_date_time, A.invoice_date, gi_id as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when B.cp_acc_id IN($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, A.gi_id, A.gi_date, A.grn_approved_date_time, A.invoice_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_id, C.gi_date, C.grn_approved_date_time, D.invoice_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join grn C on (A.ref_id=C.grn_id) left join goods_inward_outward_invoices D on (C.gi_id = D.gi_go_ref_no and A.invoice_no = D.invoice_no) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'purchase' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }
            
            if(in_array('journal_voucher',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, '' as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'journal_voucher' and A.acc_id not IN ($account) and A.company_id = $company_id) A 
                        left join 
                        (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'journal_voucher' and acc_id in ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account)  or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }

            if(in_array('payment_receipt',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, '' as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, B.cp_acc_id as acc_id, B.cp_ledger_name as ledger_name, B.cp_ledger_code as ledger_code, case when A.acc_id IN($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount*-1 else A.amount end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, B.acc_type, A.acc_id as cp_acc_id, A.ledger_name as cp_ledger_name, A.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'payment_receipt' and A.ledger_type = 'Sub Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct A.voucher_id as cp_voucher_id, A.acc_id as cp_acc_id, A.ledger_name as cp_ledger_name, A.ledger_code as cp_ledger_code, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'payment_receipt' and A.ledger_type = 'Main Entry' and A.company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }

            if(in_array('go_debit_details',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_go_date_time as gi_date, '' as grn_approved_date_time, A.invoice_created_date as invoice_date, A.gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when B.cp_acc_id IN ($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.gi_go_ref_no, A.gi_go_date_time, A.invoice_created_date, A.acc_type, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_go_ref_no, C.gi_go_date_time, D.invoice_created_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join goods_inward_outward C on (A.ref_id=C.gi_go_id) left join prepare_go D on (C.pre_go_ref=D.prepare_go_id and A.invoice_no = D.invoice_number) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;

                if($sql!='') {
                    $sql.=' UNION ALL';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, A.gi_go_date_time as gi_date, '' as grn_approved_date_time, A.invoice_created_date as invoice_date, A.gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, A.gi_go_ref_no, A.gi_go_date_time, A.invoice_created_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select A.*, B.type as acc_type, C.gi_go_ref_no, C.gi_go_date_time, D.invoice_created_date from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) left join goods_inward_outward C on (A.ref_id=C.gi_go_id) left join prepare_go D on (C.pre_go_ref=D.prepare_go_id and A.invoice_no = D.invoice_number) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'go_debit_details' and A.acc_id not in ($account) and A.ledger_type = 'Main Entry' and A.company_id = '$company_id' and A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
                        left join 
                        (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'go_debit_details' and acc_id in ($account) and ledger_type = 'Main Entry' and company_id = '$company_id' and entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B on (A.ref_id = B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }

            if(in_array('other_debit_credit',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, ref_date as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'other_debit_credit' and A.acc_id not in ($account) and A.company_id = $company_id) A 
                        left join 
                        (select * from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'other_debit_credit' and acc_id IN ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }

            if(in_array('promotion',$vouchertype)) {
                if($sql!='') {
                    $sql.=' UNION ALL ';
                }

                $sql.=" select distinct acc_type, ledger_name from 
                        (select A.ref_id, A.ref_type, A.entry_type, A.acc_type, A.cp_acc_id, A.invoice_no, A.voucher_id, A.cp_ledger_name, A.amount as total_deduction, '' as tax_amount, amount as total_without_tax, '' as other_charges, A.ledger_name, '' as gi_date, '' as grn_approved_date_time, ref_date as invoice_date, '' as gi_go_ref_no, '' as warehouse_id, A.ref_date as updated_date, '' as debit_note_ref, A.amount, A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, case when A.type='Debit' then A.amount else A.amount*-1 end as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, A.acc_type, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                        (select A.*, B.type as acc_type from acc_ledger_entries A left join acc_master B on (A.acc_id=B.id) where A.status = 'approved' and A.is_active = '1' and A.ref_type = 'promotion' and A.acc_id not in ($account) and A.company_id = $company_id) A 
                        left join 
                        (select * from acc_ledger_entries where status = 'approved' and is_active = '1' and ref_type = 'promotion' and acc_id IN ($account) and company_id = $company_id) B on (A.ref_id=B.ref_id)) A where (A.acc_id IN ($account) or A.cp_acc_id IN ($account)) and A.acc_type not in ('Vendor Goods', 'Vendor Expenses', 'Goods Sales')) A  ".$where2;
            }

            $sql.=") A) A) A order by acc_type, state, cast(percentage as signed)";

            // echo $sql.'<br/><br/>';

            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
             
            if(count($result)>0) {
                return $result = array_map("unserialize", array_unique(array_map("serialize", $result)));
            } else {
                return [];
            }
        } else {
            return [];
        }
    }

    public function getsummeryledger($acc_id, $from_date, $to_date) {
        $status = "approved";
        
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

         $sql = "select A.type, A.voucher_id, A.ref_type, A.ref_date, A.ref_id, A.invoice_no, 
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
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'journal_voucher' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
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

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no from acc_ledger_entries A 
                    left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.is_active = '1' and 
                        A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id' and B.company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.inv_no as invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount as amount1, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date, B.gi_go_ref_no as inv_no from acc_ledger_entries A 
                    left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.is_active = '1' and 
                    A.ref_type = 'go_debit_details' and A.acc_id != '$acc_id' and A.ledger_type = 'Main Entry' and 
                    A.company_id = '$company_id' and B.company_id = '$company_id' and 
                    A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'go_debit_details' and acc_id = '$acc_id' and ledger_type = 'Main Entry' and company_id = '$company_id' and 
                    entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
                on(A.ref_id=B.ref_id) 

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

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount as amount1, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'promotion' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'promotion' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount as amount1, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'sales_upload' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and date(ref_date) >= date('$from_date') and date(ref_date) <= date('$to_date') and 
                    ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                ) AA 
                where AA.acc_id = '$acc_id' or AA.cp_acc_id = '$acc_id' 
                order by AA.ref_date, AA.id ) A 
                GROUP BY A.type, A.voucher_id, A.ref_type, A.ref_date, A.ref_id, A.invoice_no 
                order by A.ref_date, A.voucher_id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getTrialBalance($from_date, $to_date) {
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
    
    public function get_ledger_totalamount($from_date, $to_date) {
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from (
                select ae.acc_id,ae.ledger_name,sum(case when ae.type='Credit' then amount else 0 end) as debit_amount,
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

    public function getLedgerBal($acc_id, $to_date) {
        $status = "approved";

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select acc_id, sum(case when type='Debit' then amount*-1 else amount end) as opening_bal from acc_ledger_entries 
                where acc_id = '$acc_id' and status = '$status' and company_id = '$company_id' and is_active = '1' and 
                date(ref_date) <= date('$to_date') group by acc_id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getdefault($acc_id, $from_date, $to_date) {
        $status = "approved";
        
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $where_condition = " ((date(ref_date) <= date('$to_date') and payment_date IS NULL )  OR (date(payment_date) > date('$to_date')  and payment_date IS NOT NULL )) and";

        $sql = "select * from (select * from 
                (select * from 
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'journal_voucher' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'journal_voucher' and acc_id='$acc_id' and company_id = '$company_id') B 
                on(A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.payment_date from 
                (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date from acc_ledger_entries A 
                    left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.is_active = '1' and 
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
                    B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.payment_date from 
                (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date from acc_ledger_entries A 
                    left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.is_active = '1' and 
                        A.ref_type = 'go_debit_details' and A.acc_id != '$acc_id' and A.ledger_type = 'Main Entry' and 
                        A.company_id = '$company_id' and B.company_id = '$company_id' and 
                        A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'go_debit_details' and acc_id = '$acc_id' and ledger_type = 'Main Entry' and company_id = '$company_id' and 
                    entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
                on(A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'other_debit_credit' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'other_debit_credit' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'promotion' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'promotion' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'sales_upload' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                ) AA 
                where $where_condition (AA.acc_id = '$acc_id' or AA.cp_acc_id = '$acc_id') 
                order by AA.ref_date, AA.id ) A";
        $sql.=") A  order by ref_date, id
            ";

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getreconsiledonly($acc_id, $from_date, $to_date) {
        $status = "approved";
        
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $where_condition = " ((date(ref_date) <= date('$to_date') and payment_date IS NULL)  OR (date(payment_date) >= date('$from_date')  and payment_date IS NOT NULL)) and ";

        $sql = "select * from 
                (select * from 
                (select * from 
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'journal_voucher' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'journal_voucher' and acc_id='$acc_id' and company_id = '$company_id') B 
                on(A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.cp_acc_id, B.cp_ledger_name,B.cp_ledger_code,A.payment_date from 
                (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date from acc_ledger_entries A 
                    left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.is_active = '1' and 
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
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.payment_date from 
                (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date from acc_ledger_entries A 
                    left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.is_active = '1' and 
                    A.ref_type = 'go_debit_details' and A.acc_id != '$acc_id' and A.ledger_type = 'Main Entry' and 
                    A.company_id = '$company_id' and B.company_id = '$company_id' and 
                    A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'go_debit_details' and acc_id = '$acc_id' and ledger_type = 'Main Entry' and company_id = '$company_id' and 
                    entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
                on(A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'other_debit_credit' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'other_debit_credit' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'promotion' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'promotion' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'sales_upload' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                ) AA 
                where $where_condition ( AA.acc_id = '$acc_id' or AA.cp_acc_id = '$acc_id' ) 
                order by AA.ref_date, AA.id ) A";
        
        $sql.=" ) A  order by ref_date, id";

        
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getbalasperbank($acc_id, $from_date, $to_date,$view) {
        $status = "approved";
        
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        if($view=='default')
            $where_condition = " date(payment_date) <= date('$to_date') ";
        else
            $where_condition = " date(payment_date) < date('$from_date') ";


       $sql = "select sum(case when type='Debit' then amount*-1 else amount end) as asperbank  from 
                (select * from 
                (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'journal_voucher' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'journal_voucher' and acc_id='$acc_id' and company_id = '$company_id') B 
                on(A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.cp_acc_id, B.cp_ledger_name,B.cp_ledger_code,A.payment_date from 
                (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date from acc_ledger_entries A 
                    left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.is_active = '1' and 
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
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code, A.payment_date from 
                (select A.*, B.gi_go_date_time as gi_date, null as invoice_date, null as due_date from acc_ledger_entries A 
                    left join goods_inward_outward B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                    where A.status = '$status' and A.is_active = '1' and B.is_active = '1' and 
                        A.ref_type = 'go_debit_details' and A.acc_id != '$acc_id' and A.ledger_type = 'Main Entry' and 
                        A.company_id = '$company_id' and B.company_id = '$company_id' and 
                        A.entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) A 
                left join 
                (select distinct ref_id, voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'go_debit_details' and acc_id = '$acc_id' and ledger_type = 'Main Entry' and company_id = '$company_id' and 
                    entry_type in ('Purchase Stock Transfer', 'Sales Stock Transfer')) B 
                on (A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'other_debit_credit' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'other_debit_credit' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount, A.status, 
                    A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'promotion' and acc_id!='$acc_id' and company_id = '$company_id') A 
                left join 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'promotion' and acc_id='$acc_id' and company_id = '$company_id') B 
                on (A.ref_id=B.ref_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'B2B Sales' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'B2B Sales' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                union all 

                select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id = '$acc_id' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code ,A.payment_date from 
                (select * from acc_ledger_entries where status = '$status' and is_active = '1'  and 
                    ref_type = 'sales_upload' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = '$status' and is_active = '1' and 
                    ref_type = 'sales_upload' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 

                ) AA 
                where  $where_condition and payment_date  IS NOT NULL and (AA.acc_id = '$acc_id' or AA.cp_acc_id = '$acc_id')
                order by AA.ref_date, AA.id ) A";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
    
    public function update_ledger($paydate, $reconsiled) {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $date = date("Y-m-d h:i:s");
        
        if($paydate=="")
        {
            $sql = "UPDATE acc_ledger_entries SET payment_date = NULL,
                    bank_date_updated_on='$date',bank_date_updated_by='$curusr'  WHERE id = $reconsiled";
        }
        else
        {
           $sql = "UPDATE acc_ledger_entries SET payment_date = '$paydate',
                   bank_date_updated_on='$date',bank_date_updated_by='$curusr'  WHERE id = $reconsiled";
        }
        
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $command->execute();
    }
}