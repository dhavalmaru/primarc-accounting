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

    public function getVendorname()
    {
       $sql = "select id,legal_name from acc_master where is_active = '1' and status = 'approved' and type='Vendor Goods' order by legal_name ";
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

    public function getstatemaster()
    {
       $sql = "select state_name,id,state_code from state_master WHERE is_active = '1'";
       $command = Yii::$app->db->createCommand($sql);
       $reader = $command->query();
       return $reader->readAll();
    }

    public function getDetailledger_old($account, $vouchertype,$from_date, $to_date,$date_type,$state)
    {
        
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

        if($date_type=='updated_date')
        {
           $where2 = "Where  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')";
            $where3 = "And  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')"; 
        }
        else
        {
            $where2 = "";
            $where3 = "";
        }
        

        $session = Yii::$app->session;
        $company_id = $session['company_id']; 
        //$account;
        if($account!='')
        { 
            $sql = '';
            if(in_array('purchase',$vouchertype))
            {
                $sql.= "Select * from (Select * FROM(
                        Select A.*,F.updated_date,'' as amount1 from 
                        (
                        Select C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,'' as debit_note_ref,E.gi_go_ref_no,G.warehouse_id from (
                        Select A.ref_id,A.ref_type,A.cp_acc_id, A.invoice_no,A.voucher_id ,A.cp_ledger_name,TRUNCATE((A.total_tax_amount+D.total_purchase_amount),2) as total_deduction,total_tax_amount as tax_amount, total_purchase_amount as total_without_tax,A.ledger_name from (
                        select  A.ref_id,A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                                        ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                                        ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                        on (A.voucher_id = B.cp_voucher_id) 
                        Where B.cp_acc_id IN ($account) AND entry_type IN('CGST','IGST','SGST')  GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name
                        ) A
                        left JOIN
                        (select  A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id , cp_ledger_name,ledger_name from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                                        ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                                        ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                                        ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                        on (A.voucher_id = B.cp_voucher_id) 
                        Where B.cp_acc_id In($account) AND entry_type IN('Taxable Amount') GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name
                        ) D on A.ref_id=D.ref_id and A.invoice_no=D.invoice_no ) C
                        left JOIN
                        (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn ) G on C.ref_id=G.grn_id 
                        left join
                        (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no
                        ) A
                        left join 
                        (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id";
                        if($state!="")
                        {
                            $sql.=" join
                                (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                                 on A.warehouse_id=G.warehouse_code
                                "; 
                        }
                        $sql.=" ) D UNION
                            Select * FROM(
                                Select A.*,F.updated_date,'' as amount1 from 
                                (
                                Select C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,D.debit_note_ref,E.gi_go_ref_no,G.warehouse_id from (
                                Select A.ref_id,'Debit Note' as ref_type,A.cp_acc_id, A.invoice_no,A.voucher_id ,A.cp_ledger_name,TRUNCATE((A.total_tax_amount+D.total_purchase_amount),2) as total_deduction,total_tax_amount as tax_amount, total_purchase_amount as total_without_tax,A.ledger_name from (
                                select  A.ref_id,A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name from 
                                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                                left join 
                                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                                on (A.voucher_id = B.cp_voucher_id) 
                                Where B.cp_acc_id IN ($account) AND entry_type IN('margindiff_cgst','margindiff_sgst','margindiff_igst','shortage_cgst' ,'shortage_sgst' ,'shortage_igst' , 'expiry_cgst' ,'expiry_sgst' ,'expiry_igst' ,'damage_cgst', 'damage_sgst','damage_igst')  GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name
                                ) A
                                left JOIN
                                (select  A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id , cp_ledger_name,ledger_name from 
                                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                                    ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                                left join 
                                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                                    ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                                on (A.voucher_id = B.cp_voucher_id) 
                                Where B.cp_acc_id In($account) AND entry_type IN('margindiff_cost','shortage_cost','expiry_cost','damage_cost') GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name
                                ) D on A.ref_id=D.ref_id and A.invoice_no=D.invoice_no ) C
                                left join
                                (Select debit_note_ref as debit_note_ref,grn_id,invoice_no from acc_grn_debit_notes ) D  on C.ref_id=D.grn_id and C.invoice_no=D.invoice_no
                                left JOIN
                                (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn ) G on C.ref_id=G.grn_id 
                                left join
                                (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no
                                ) A
                                left join 
                                (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id";
                                 if($state!="")
                                    {
                                      $sql.=" join
                                                (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                                                on A.warehouse_id=G.warehouse_code"; 
                                    }
                                $sql.="
                                ) E  ORDER BY ref_id ASC,invoice_no ASC,ref_type DESC
                         )A Where $where_condition";
            }
            
            
           if(in_array('journal_voucher',$vouchertype))        
            {
                if($sql!='')
                {
                    $sql.=' UNION ';
                }
                $sql.=" Select * from 
                    (SELECT A.ref_id,A.ref_type,A.cp_acc_id,A.invoice_no,A.voucher_id,A.cp_ledger_name,A.total_amount as total_deduction,'' as tax_amount,total_amount as total_without_tax,A.ledger_name ,'' as gi_date,'' as grn_approved_date_time,'' as invoice_date,'' as debit_note_ref ,'' as gi_go_ref_no ,'' as warehouse_id , A.ref_date as updated_date ,A.amount1 FROM  (
                    Select A.*,B.total_amount from (select A.id, A.ref_id,  A.ref_type, A.invoice_no,B.acc_id as cp_acc_id, A.vendor_id,  A.ledger_name, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount as amount1,A.voucher_id,B.ledger_name as cp_ledger_name , A.ref_date  from 
                    (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'journal_voucher' and acc_id NOT IN($account) and company_id = '$company_id') A 
                    left join 
                    (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'journal_voucher' and acc_id IN($account) and company_id = '$company_id') B 
                    on(A.ref_id=B.ref_id)
                    Where B.ledger_name IS NOT NULL
                    GROUP by A.id, A.ref_id,  A.entry_type, A.invoice_no, A.vendor_id, A.ledger_name,A.voucher_id,B.ledger_name ) A
                    left JOIN
                    (select ref_id,voucher_id,amount as total_amount from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'journal_voucher'  and acc_id IN($account) and company_id = '$company_id' ) B
                    on A.ref_id=B.ref_id and  A.voucher_id=B.voucher_id ) A ORDER By voucher_id,ledger_name ) B ".$where2;
            }
            
            if(in_array('payment_receipt',$vouchertype))
            {
                if($sql!='')
                {
                    $sql.=' UNION ';
                }

                $sql.="Select * from (Select * from
                    (Select  B.ref_id, B.ref_type,B.cp_acc_id,B.invoice_no,B.voucher_id,B.cp_ledger_name ,B.total_deduction,'' as tax_amount ,B.total_deduction as total_without_tax,B.ledger_name,'' as gi_date,'' as grn_approved_date_time,'' as invoice_date,'' as debit_note_ref,'' as gi_go_ref_no,B.ref_date as updated_date ,'' as warehouse_id ,total_deduction as amount1 from (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id IN ($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount as total_deduction, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                    A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                    (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = '$company_id') A 
                    left join 
                    (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = '$company_id') B 
                    on (A.voucher_id = B.cp_voucher_id) 
                    ) B  
                    where  B.entry_type IN('Bank Entry') and (B.acc_id IN  ($account) OR B.cp_acc_id IN ($account) ) )
                    A ORDER By voucher_id,ledger_name   )C ".$where2;
            }
            
            
            if(in_array('go_debit_details',$vouchertype))
            {
                if($sql!='')
                {
                    $sql.=' UNION ';
                }
                $sql.="Select  B.ref_id, B.ref_type,B.cp_acc_id,B.invoice_no,B.voucher_id,B.cp_ledger_name ,B.total_deduction,'' as tax_amount ,B.total_deduction as total_without_tax,
                    B.ledger_name,gi_date,'' as grn_approved_date_time,'' as invoice_date,D.debit_note_ref,'' as gi_go_ref_no,B.ref_date as updated_date ,
                    warehouse_id ,total_deduction as amount1  from 
                    (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                    A.ledger_code, case when B.cp_acc_id IN ('$account') then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                    A.amount as total_deduction, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                    A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                    B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code,A.warehouse_code as warehouse_id,gi_date from 
                    (select A.*, B.date_of_transaction as gi_date, null as invoice_date, null as due_date,C.warehouse_code from acc_ledger_entries A 
                    left join acc_go_debit_details B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details')
                    left join goods_inward_outward C on (B.gi_go_id=C.gi_go_id) 
                    where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and 
                    A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and A.company_id = '$company_id' and B.company_id = '$company_id') A 
                    left join 
                    (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                    ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                    on (A.voucher_id = B.cp_voucher_id)";
                   /* if($state!="")
                    {
                      $sql.=" join
                              (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                               on A.warehouse_code=G.warehouse_code "; 
                    }*/
                    $sql.=" ) B
                    left join
                    (Select debit_note_ref as debit_note_ref,gi_go_id from acc_go_debit_details ) D  on B.ref_id=D.gi_go_id
                    where (B.acc_id IN  ('$account') OR B.cp_acc_id IN ('$account')) ".$where3;
            }
            
            if(in_array('other_debit_credit',$vouchertype))
            {
                if($sql!='')
                {
                    $sql.=' UNION ';
                }

                $sql .="Select * from (Select * from 
                    (SELECT  A.ref_id,A.ref_type,A.cp_acc_id,A.invoice_no,A.voucher_id,A.cp_ledger_name,B.total_amount as total_deduction,'' as tax_amount,B.total_amount as total_without_tax,A.ledger_name ,'' as gi_date,'' as grn_approved_date_time,'' as invoice_date,'' as debit_note_ref ,'' as gi_go_ref_no , A.ref_date as updated_date ,'' as warehouse_id ,A.amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                        A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount as amount1, A.status, 
                        A.created_by, A.updated_by, A.created_date, A.updated_date, 
                        A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                        B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                        ref_type = 'other_debit_credit' and acc_id!='$account' and company_id = '$company_id') A 
                        left join 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                        ref_type = 'other_debit_credit' and acc_id='$account' and company_id = '$company_id') B 
                        on (A.ref_id=B.ref_id) ) A
                        left join
                        (select ref_id,voucher_id,amount as total_amount from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'other_debit_credit' and acc_id IN('$account') and company_id = '$company_id' ) B
                        on A.ref_id=B.ref_id and  A.voucher_id=B.voucher_id
                        where acc_id IN  ($account) OR cp_acc_id IN ($account) 
                       )B ORDER By voucher_id,ledger_name ) A ".$where2;
            }
            
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            return $reader->readAll();
        }
        else
        {
            return [];
        }
    }

    public function column_names_old($account, $vouchertype,$from_date, $to_date,$date_type,$state)
    {
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        if($date_type=='updated_date')
        {
           $where2 = "Where  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')";
            $where3 = "And  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')"; 
        }
        else
        {
            $where2 = "";
            $where3 = "";
        }

        $sql=" Select DISTINCT ledger_name from 
                    (SELECT A.ref_id,A.ref_type,A.cp_acc_id,A.invoice_no,A.voucher_id,A.cp_ledger_name,A.total_amount as total_deduction,'' as tax_amount,total_amount as total_without_tax,A.ledger_name ,'' as gi_date,'' as grn_approved_date_time,'' as invoice_date,'' as debit_note_ref ,'' as gi_go_ref_no ,'' as warehouse_id , A.ref_date as updated_date ,A.amount1 FROM  (
                    Select A.*,B.total_amount from (select A.id, A.ref_id,  A.ref_type, A.invoice_no,B.acc_id as cp_acc_id, A.vendor_id,  A.ledger_name, case when A.type='Debit' then 'Credit' else 'Debit' end as type, A.amount as amount1,A.voucher_id,B.ledger_name as cp_ledger_name , A.ref_date  from 
                    (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'journal_voucher' and acc_id NOT IN($account) and company_id = '$company_id') A 
                    left join 
                    (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                    ref_type = 'journal_voucher' and acc_id IN($account) and company_id = '$company_id') B 
                    on(A.ref_id=B.ref_id)
                    Where B.ledger_name IS NOT NULL
                    GROUP by A.id, A.ref_id,  A.entry_type, A.invoice_no, A.vendor_id, A.ledger_name,A.voucher_id,B.ledger_name ) A
                    left JOIN
                    (select ref_id,voucher_id,amount as total_amount from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'journal_voucher'  and acc_id IN($account) and company_id = '$company_id' ) B
                    on A.ref_id=B.ref_id and  A.voucher_id=B.voucher_id ) A ORDER By voucher_id,ledger_name ) B ".$where2;

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getDetailledger($account, $vouchertype,$from_date, $to_date,$date_type,$state)
    {
        
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

        if($date_type=='updated_date')
        {
           $where2 = "Where  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')";
            $where3 = "And  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')"; 
        }
        else
        {
            $where2 = "";
            $where3 = "";
        }
        

        $session = Yii::$app->session;
        $company_id = $session['company_id']; 
        //$account;
        if($account!='')
        { 
            $sql = '';
            if(in_array('purchase',$vouchertype))
            {
                $sql.="Select * from 
                        (Select * from 
                        (Select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,G.grn_approved_date_time,
                            F.updated_date,'' as debit_note_ref,other_charges as amount1 from 
                        (Select A.ref_id,'purchase' as ref_type,A.cp_acc_id,A.invoice_no,A.voucher_id,A.cp_ledger_name,
                            TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)+IFNULL(other_charges, 0)),2) as total_deduction,
                            Truncate(total_tax_amount,2) as tax_amount, Truncate(total_purchase_amount,2) as total_without_tax,
                            IFNULL(other_charges,0) as other_charges ,E.ledger_name from 
                        (Select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,
                            sum(amount)  as `total_tax_amount` ,'' as ledger_name from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                            A.ledger_code, case when B.cp_acc_id IN($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                            A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                            A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                            ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '1') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                            ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                            ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
                        on (A.voucher_id = B.cp_voucher_id)
                        Where B.cp_acc_id IN ($account) AND entry_type IN('CGST','IGST','SGST')) A
                        GROUP BY A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name)A
                        left join
                        (Select A.ref_id, A.invoice_no, sum(amount) as total_purchase_amount ,'' as ledger_name from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                            A.ledger_code, case when B.cp_acc_id  IN ($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                            A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                            A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                            A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                            ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '1') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                            ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                            ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
                        on (A.voucher_id = B.cp_voucher_id)
                        Where B.cp_acc_id IN ($account) AND entry_type IN('Taxable Amount')) A
                        GROUP BY A.ref_id,A.invoice_no) D 
                        on (A.ref_id=D.ref_id and A.invoice_no=D.invoice_no)
                        left join 
                        (Select A.ref_id, A.invoice_no,sum(amount) as other_charges,A.ledger_name from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                            A.ledger_code, case when B.cp_acc_id IN ($account)  then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                            A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                            A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                            A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                            ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '1') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                            ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                            ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
                        on (A.voucher_id = B.cp_voucher_id)
                        Where B.cp_acc_id IN ($account) AND entry_type IN('Other Charges')) A
                        GROUP BY A.ref_id,A.invoice_no,ledger_name ) E
                        on A.ref_id=E.ref_id and A.invoice_no=E.invoice_no ) C
                        left JOIN
                        (Select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G on C.ref_id=G.grn_id
                        left join
                        (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no " ;
                        if($state!="")
                        {
                            $sql.=" join
                            (Select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
                            on G.warehouse_id=W.warehouse_code " ;
                        }
                        $sql.=" left join 
                        (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on C.ref_id=F.grn_id) D

                        Union All

                        Select * from 
                        (Select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,
                            G.grn_approved_date_time,F.updated_date,D.debit_note_ref ,other_charges as amount1 from 
                        (Select A.ref_id,'Debit Note' as ref_type,A.cp_acc_id, A.invoice_no,A.voucher_id, A.cp_ledger_name,
                            (TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2)*-1) as total_deduction,
                            (Truncate(total_tax_amount,2)*-1) as tax_amount, (Truncate(total_purchase_amount,2)*-1) as total_without_tax,
                            0 as other_charges , '' as ledger_name from 
                        (Select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,
                            sum(amount)  as `total_tax_amount` ,'' as ledger_name from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                            A.ledger_code, case when B.cp_acc_id IN ($account)  then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                            case when A.type='Debit' then A.amount*-1 else A.amount end as amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                            A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                            A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                            ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '1') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                            ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                            ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
                        on (A.voucher_id = B.cp_voucher_id)
                        Where B.cp_acc_id IN ($account) AND entry_type IN('margindiff_cgst','margindiff_sgst','margindiff_igst',
                            'shortage_cgst' ,'shortage_sgst' ,'shortage_igst' , 'expiry_cgst' ,'expiry_sgst' ,'expiry_igst' ,'damage_cgst', 'damage_sgst','damage_igst') ) A
                        GROUP BY A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name)A
                        left join
                        (Select A.ref_id, A.invoice_no, sum(amount) as total_purchase_amount from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                            A.ledger_code, case when B.cp_acc_id  IN($account)  then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                            case when A.type='Debit' then A.amount*-1 else A.amount end as amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                            A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                        ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '1') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                            ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                            ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
                        on (A.voucher_id = B.cp_voucher_id) 
                        Where B.cp_acc_id IN ($account) AND entry_type IN('margindiff_cost','shortage_cost','expiry_cost','damage_cost') ) A
                        GROUP BY A.ref_id,A.invoice_no) D 
                        on A.ref_id=D.ref_id and A.invoice_no=D.invoice_no) C
                        left join
                        (Select debit_note_ref as debit_note_ref,grn_id,invoice_no from acc_grn_debit_notes ) D  on 
                        C.ref_id=D.grn_id and C.invoice_no=D.invoice_no
                        left JOIN
                        (Select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G on C.ref_id=G.grn_id
                        left join
                        (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no ";
                        if($state!="")
                        {
                            $sql.=" join
                            (Select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
                            on G.warehouse_id=W.warehouse_code  ";
                        }
                        $sql.= "left join 
                        (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on C.ref_id=F.grn_id) D 
                        Where $where_condition 
                        ORDER BY ref_id ASC, invoice_no ASC, voucher_id ASC, ref_type DESC) A ";
            }
            
            if(in_array('journal_voucher',$vouchertype))        
            {
                if($sql!='')
                {
                    $sql.=' UNION ';
                }

                $sql.=" Select * from 
                        (Select A.ref_id,A.ref_type,A.cp_acc_id,A.invoice_no,A.voucher_id,A.cp_ledger_name ,
                        Truncate(A.amount,2) as total_deduction,'' as tax_amount,amount as total_without_tax,
                        '' as other_charges ,A.ledger_name ,'' as gi_date,'' as grn_approved_date_time,
                        '' as invoice_date,'' as gi_go_ref_no ,
                        '' as warehouse_id , A.ref_date as updated_date,'' as debit_note_ref , Truncate(A.amount,2) as amount1 from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                        A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type,case when A.type='Debit' then A.amount*-1 else A.amount end as amount, A.status, 
                        A.created_by, A.updated_by, A.created_date, A.updated_date, 
                        A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                        A.narration, A.ref_date, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, 
                        B.ledger_code as cp_ledger_code from 
                         (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                             ref_type = 'journal_voucher' and acc_id NOT IN ($account) and company_id = $company_id) A 
                         left join 
                         (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                             ref_type = 'journal_voucher' and acc_id IN ($account) and company_id = $company_id) B 
                         on(A.ref_id=B.ref_id)  ) A
                        where A.acc_id IN ($account)  or A.cp_acc_id IN ($account) ) A  ".$where2;
            }

            if(in_array('payment_receipt',$vouchertype))
            {
                if($sql!='')
                {
                    $sql.=' UNION ';
                }

                $sql.=" Select * from 
                    (Select A.ref_id,'Payment' as ref_type,A.cp_acc_id,A.invoice_no,A.voucher_id,A.cp_ledger_name as  ledger_name,
                    Truncate(A.amount,2) as total_deduction,'' as tax_amount,amount as total_without_tax,
                    '' as other_charges ,A.ledger_name as cp_ledger_name,'' as gi_date,'' as grn_approved_date_time,
                    '' as invoice_date,'' as gi_go_ref_no ,
                    '' as warehouse_id , A.ref_date as updated_date,'' as debit_note_ref,Truncate(A.amount,2) as amount1  from 
                        (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                            A.ledger_code, case when B.cp_acc_id IN ($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                            case when A.type='Debit' then A.amount*-1 else A.amount end as amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                            A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                            A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                            ref_type = 'payment_receipt' and ledger_type = 'Main Entry' and company_id = $company_id) A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                            ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                            ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and company_id = $company_id) B 
                        on (A.voucher_id = B.cp_voucher_id)
                         ) A
                        where A.acc_id IN($account) or A.cp_acc_id IN ($account) ) A ".$where2;
            }
            
            if(in_array('go_debit_details',$vouchertype))
            {
                if($sql!='')
                {
                    $sql.=' UNION ';
                }
                $sql.="Select * from (
                        SELECT  A.ref_id,'Other Debit Detail' as ref_type,A.cp_acc_id,A.invoice_no,A.voucher_id,A.cp_ledger_name ,
                        Truncate(A.amount,2) as total_deduction,'' as tax_amount,amount as total_without_tax,
                        '' as other_charges ,A.ledger_name ,'' as gi_date,'' as grn_approved_date_time,
                        '' as invoice_date ,'' as gi_go_ref_no ,
                        '' as warehouse_id , A.ref_date as updated_date,'' as debit_note_ref,Truncate(A.amount,2) as amount1 from 
                        (
                            select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                            A.ledger_code, case when B.cp_acc_id IN ($account) then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                            case when A.type='Debit' then A.amount*-1 else A.amount end as amount,A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                            A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                            B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                            (select A.*, B.date_of_transaction as gi_date, null as invoice_date, null as due_date from acc_ledger_entries A 
                            left join acc_go_debit_details B on(A.ref_id = B.gi_go_id and A.ref_type = 'go_debit_details') 
                            where A.status = 'Approved' and A.is_active = '1' and B.status = 'Approved' and B.is_active = '1' and 
                                A.ref_type = 'go_debit_details' and A.ledger_type != 'Main Entry' and A.company_id = $company_id and B.company_id = $company_id) A 
                            left join 
                            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                            ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                            ref_type = 'go_debit_details' and ledger_type = 'Main Entry' and company_id = $company_id) B 
                            on (A.voucher_id = B.cp_voucher_id)
                        ) A
                        where A.acc_id IN($account) or A.cp_acc_id IN ($account) 
                        ) A ".$where3;
            }
            
            if(in_array('other_debit_credit',$vouchertype))
            {
                if($sql!='')
                {
                    $sql.=' UNION ';
                }

                $sql .="Select  A.ref_id,'Other Debit Note' as ref_type,A.cp_acc_id,A.invoice_no,A.voucher_id,A.cp_ledger_name , Truncate(A.amount,2) as total_deduction,'' as tax_amount,amount as total_without_tax,
                    '' as other_charges ,A.ledger_name ,'' as gi_date,'' as grn_approved_date_time,
                    '' as invoice_date ,'' as gi_go_ref_no ,
                    '' as warehouse_id , A.ref_date as updated_date,'' as debit_note_ref,Truncate(A.amount,2) as amount1 from 
                    (
                                    select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                                        A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type, case when A.type='Debit' then A.amount*-1 else A.amount end as amount, A.status, 
                                        A.created_by, A.updated_by, A.created_date, A.updated_date, 
                                        A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, A.narration, A.ref_date, 
                                        B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, B.ledger_code as cp_ledger_code from 
                                    (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                                        ref_type = 'other_debit_credit' and acc_id NOT IN ($account) and company_id = $company_id) A 
                                    left join 
                                    (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                                        ref_type = 'other_debit_credit' and acc_id IN($account) and company_id = $company_id) B 
                                    on (A.ref_id=B.ref_id) 
                    ) A
                    where A.acc_id IN($account) or A.cp_acc_id IN ($account) ".$where2;
            }

            $sql;
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            return $reader->readAll();
        }
        else
        {
            return [];
        }
    }

    public function column_names($account, $vouchertype,$from_date, $to_date,$date_type,$state)
    {
        
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

        if($date_type=='updated_date')
        {
           $where2 = "Where  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')";
            $where3 = "And  date(updated_date) >= date('$from_date') and date(updated_date) <= date('$to_date')"; 
        }
        else
        {
            $where2 = "";
            $where3 = "";
        }
        

        $session = Yii::$app->session;
        $company_id = $session['company_id']; 
        //$account;
        if($account!='')
        { 
            $sql = "";
            if(in_array('purchase',$vouchertype))
            {
                $sql.="Select Distinct ledger_name from ( Select * from (
                        Select * from (
                        Select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,G.grn_approved_date_time,F.updated_date,'' as debit_note_ref,other_charges as amount1  from (Select A.ref_id,'Purchase' as ref_type,A.cp_acc_id, A.invoice_no,A.voucher_id ,A.cp_ledger_name,
                        TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)+IFNULL(other_charges, 0)),2) as total_deduction,
                        total_tax_amount as tax_amount, total_purchase_amount as total_without_tax ,IFNULL(other_charges,0) as other_charges ,E.ledger_name
                        from (Select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,
                        sum(amount)  as `total_tax_amount` ,'' as ledger_name
                        from (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                        A.ledger_code, case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                        A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                        A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                        A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                        ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '1') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                        ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                        ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
                        on (A.voucher_id = B.cp_voucher_id)
                        Where B.cp_acc_id IN ($account) AND entry_type IN('CGST','IGST','SGST') ) A
                        GROUP BY
                        A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name )A
                        left join
                        (Select A.ref_id, A.invoice_no ,
                        sum(amount) as total_purchase_amount ,'' as ledger_name
                        from (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                        A.ledger_code, case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                        A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                        A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                        A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                        ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '1') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                        ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                        ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
                        on (A.voucher_id = B.cp_voucher_id)
                        Where B.cp_acc_id IN ($account) AND entry_type IN('Taxable Amount') ) A
                        GROUP BY
                        A.ref_id,A.invoice_no)
                        D on A.ref_id=D.ref_id and A.invoice_no=D.invoice_no
                        left join
                        (Select A.ref_id, A.invoice_no,sum(amount) as other_charges,A.ledger_name
                        from (select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                        A.ledger_code, case when B.cp_acc_id = '$account' then case when A.type='Debit' then 'Credit' else 'Debit' end else A.type end as type, 
                        A.amount, A.status, A.created_by, A.updated_by, A.created_date, A.updated_date, 
                        A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                        A.narration, A.ref_date, B.cp_acc_id, B.cp_ledger_name, B.cp_ledger_code from 
                        (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                        ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '1') A 
                        left join 
                        (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                        ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                        ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '1') B 
                        on (A.voucher_id = B.cp_voucher_id)
                        Where B.cp_acc_id IN ($account) AND entry_type IN('Other Charges') ) A
                        GROUP BY
                        A.ref_id,A.invoice_no,ledger_name ) E
                        on A.ref_id=E.ref_id and A.invoice_no=E.invoice_no ) C
                        left JOIN
                        (Select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G on C.ref_id=G.grn_id
                        left join
                        (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no " ;
                        if($state!="")
                        {
                            $sql.=" join
                            (Select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
                            on G.warehouse_id=W.warehouse_code " ;
                        }
                        $sql.=" left join 
                        (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on C.ref_id=F.grn_id

                        ) D
                        ) E ORDER BY ref_id ASC,invoice_no ASC,ref_type DESC
                        )A Where $where_condition and ledger_name IS NOT NULL 
                     ";
            }
            
            if(in_array('journal_voucher',$vouchertype))        
            {
                if($sql!='')
                {
                    $sql.=' UNION ALL';
                }

                $sql.=" SELECT Distinct ledger_name  from 
                        (Select A.ref_id,A.ref_type,A.cp_acc_id,A.invoice_no,A.voucher_id,A.cp_ledger_name ,
                        A.amount as total_deduction,'' as tax_amount,amount as total_without_tax,
                        '' as other_charges ,A.ledger_name ,'' as gi_date,'' as grn_approved_date_time,
                        '' as invoice_date,'' as gi_go_ref_no ,
                        '' as warehouse_id , A.ref_date as updated_date,'' as debit_note_ref ,A.amount as amount1 from (    
                        select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, 
                        A.ledger_code, case when A.type='Debit' then 'Credit' else 'Debit' end as type,case when A.type='Debit' then A.amount*-1 else A.amount end as amount, A.status, 
                        A.created_by, A.updated_by, A.created_date, A.updated_date, 
                        A.is_paid, A.payment_ref, A.voucher_id, A.ledger_type, 
                        A.narration, A.ref_date, B.acc_id as cp_acc_id, B.ledger_name as cp_ledger_name, 
                        B.ledger_code as cp_ledger_code from 
                         (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                             ref_type = 'journal_voucher' and acc_id NOT IN ($account) and company_id = $company_id) A 
                         left join 
                         (select * from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                             ref_type = 'journal_voucher' and acc_id IN ($account) and company_id = $company_id) B 
                         on(A.ref_id=B.ref_id)  ) A
                        where A.acc_id IN ($account)  or A.cp_acc_id IN ($account) ) A  ".$where2;
            }

            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
             
            if(count($result)>0)
            {
                
                return $result = array_map("unserialize", array_unique(array_map("serialize", $result)));


            }
            else
            {
                return [];
            }
           
        }
        else
        {
            return [];
        }
    }
    
    public function gettaxwisebifercation_old($account, $vouchertype,$from_date, $to_date,$date_type,$state)
    {
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

        $sql= "Select DISTINCT ref_id,ref_type,total_tax_amount,cp_acc_id,invoice_no,
                voucher_id,cp_ledger_name,percentage,purchase,cost_inc_tax,
                gi_date,grn_approved_date_time,invoice_date,debit_note_ref,gi_go_ref_no,warehouse_id,updated_date,total_deduction FROM(
                Select * from (
                Select A.*,F.updated_date,B.total_amount as total_deduction from 
                (
                SELECT C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,'' as debit_note_ref,E.gi_go_ref_no,G.warehouse_id from  
                (SELECT A.*,B.total_purchase_amount as purchase ,Truncate((total_purchase_amount+total_tax_amount),2) as cost_inc_tax from 
                (select  A.ref_id,A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no,A.voucher_id , 
                 cp_ledger_name, REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1
                  ,  CASE WHEN A.entry_type = 'IGST' THEN Replace 
                    ( Substring_index(ledger_name, '-', -1),'%', '' ) 
                    ELSE ( 2 * Replace(Substring_index(ledger_name, '-', -1), '%', '') ) END  as percentage ,entry_type   from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id IN ($account) AND entry_type IN('CGST','IGST','SGST')
                GROUP By A.ref_id,A.ref_type,  B.cp_acc_id, A.invoice_no,A.voucher_id , 
                 cp_ledger_name,ledger_name,entry_type ) A
                left join 
                (select  A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id 
                , cp_ledger_name,REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1,REPLACE(SUBSTRING_INDEX(ledger_name, '-', -1),'%','' )as percentage from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id In($account) AND entry_type IN('Taxable Amount') 
                GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name ) B
                on A.ref_id=B.ref_id and A.invoice_no=B.invoice_no and A.percentage=B.percentage
                ) C
                left JOIN
                (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn ) G on C.ref_id=G.grn_id 
                left join
                (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no
                 ) A
                left join 
                (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id ";
                if($state!="")
                {
                    $sql.="join
                    (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                    on A.warehouse_id=G.warehouse_code ";
                }
                $sql.="left JOIN
                (select ref_id,voucher_id,amount as total_amount,entry_type  from acc_ledger_entries
                where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' 
                and acc_id IN($account) and company_id = '$company_id' and entry_type='Total Amount' ) B 
                on A.ref_id=B.ref_id and  A.voucher_id=B.voucher_id
                ) D
                Union
                Select * from 
                (Select A.*,F.updated_date,B.total_amount from 
                (
                SELECT C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,D.debit_note_ref,E.gi_go_ref_no,G.warehouse_id from  
                (SELECT A.*,B.total_purchase_amount as purchase ,Truncate((total_purchase_amount+total_tax_amount),2) as cost_inc_tax from 
                (select  A.ref_id,'Debit Note' as ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no,A.voucher_id , 
                 cp_ledger_name, REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1 ,
                 case when A.entry_type='IGST' then  REPLACE(REPLACE(RIGHT(ledger_name,3),'-',''),'%','' )  else (2*REPLACE(REPLACE(RIGHT(ledger_name,3),'-',''),'%','' ) ) end as percentage,entry_type  from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id IN ($account) AND entry_type IN('margindiff_cgst','margindiff_sgst','margindiff_igst','shortage_cgst' ,'shortage_sgst' ,'shortage_igst' , 
                'expiry_cgst' ,'expiry_sgst' ,'expiry_igst' ,'damage_cgst', 'damage_sgst','damage_igst')
                GROUP By A.ref_id,A.ref_type,  B.cp_acc_id, A.invoice_no,A.voucher_id , 
                 cp_ledger_name,ledger_name,entry_type ) A
                left join 
                (select  A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id 
                , cp_ledger_name,REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1,REPLACE(REPLACE(RIGHT(ledger_name,3),'-',''),'%','' )as percentage from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id In($account) AND entry_type IN('margindiff_cost','shortage_cost','expiry_cost','damage_cost') 
                GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name ) B
                on A.ref_id=B.ref_id and A.invoice_no=B.invoice_no and A.percentage=B.percentage
                ) C
                left join
                (Select debit_note_ref as debit_note_ref,grn_id,invoice_no from acc_grn_debit_notes ) D  on C.ref_id=D.grn_id and C.invoice_no=D.invoice_no
                left JOIN
                (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn ) G on C.ref_id=G.grn_id 
                left join
                (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no
                 ) A
                left join 
                (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id ";
                if($state!="")
                {
                  $sql.="join
                    (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                    on A.warehouse_id=G.warehouse_code ";
                }
                $sql.="left JOIN
                (select ref_id,voucher_id,amount as total_amount,entry_type  from acc_ledger_entries
                where status = 'Approved' 
                and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry'  and acc_id IN($account) and company_id = '$company_id' and 
                 entry_type='Total Deduction') B
                on A.ref_id=B.ref_id and  A.voucher_id=B.voucher_id
                )  E 
                ORDER BY ref_id ASC,invoice_no ASC,ref_type DESC ,percentage ASC 
                )A  Where $where_condition
              ";
        
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function tax_wise_column_old($account, $vouchertype,$from_date, $to_date,$date_type,$state)
    {
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

        $sql= "Select DISTINCT(percentage) from (Select DISTINCT ref_id,ref_type,total_tax_amount,cp_acc_id,invoice_no,
                voucher_id,cp_ledger_name,percentage,purchase,cost_inc_tax,
                gi_date,grn_approved_date_time,invoice_date,debit_note_ref,gi_go_ref_no,warehouse_id,updated_date,total_deduction FROM(
                Select * from (
                Select A.*,F.updated_date,B.total_amount as total_deduction from 
                (
                SELECT C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,'' as debit_note_ref,E.gi_go_ref_no,G.warehouse_id from  
                (SELECT A.*,B.total_purchase_amount as purchase ,Truncate((total_purchase_amount+total_tax_amount),2) as cost_inc_tax from 
                (select  A.ref_id,A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no,A.voucher_id , 
                 cp_ledger_name, REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1
                  ,  CASE WHEN A.entry_type = 'IGST' THEN Replace 
                    ( Substring_index(ledger_name, '-', -1),'%', '' ) 
                    ELSE ( 2 * Replace(Substring_index(ledger_name, '-', -1), '%', '') ) END  as percentage ,entry_type   from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id IN ($account) AND entry_type IN('CGST','IGST','SGST')
                GROUP By A.ref_id,A.ref_type,  B.cp_acc_id, A.invoice_no,A.voucher_id , 
                 cp_ledger_name,ledger_name,entry_type ) A
                left join 
                (select  A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id 
                , cp_ledger_name,REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1,REPLACE(SUBSTRING_INDEX(ledger_name, '-', -1),'%','' )as percentage from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id In($account) AND entry_type IN('Taxable Amount') 
                GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name ) B
                on A.ref_id=B.ref_id and A.invoice_no=B.invoice_no and A.percentage=B.percentage
                ) C
                left JOIN
                (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn ) G on C.ref_id=G.grn_id 
                left join
                (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no
                 ) A
                left join 
                (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id ";
                if($state!="")
                {
                    $sql.="join
                    (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                    on A.warehouse_id=G.warehouse_code ";
                }
                $sql.="left JOIN
                (select ref_id,voucher_id,amount as total_amount,entry_type  from acc_ledger_entries
                where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' 
                and acc_id IN($account) and company_id = '$company_id' and entry_type='Total Amount' ) B 
                on A.ref_id=B.ref_id and  A.voucher_id=B.voucher_id
                ) D
                Union
                Select * from 
                (Select A.*,F.updated_date,B.total_amount from 
                (
                SELECT C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,D.debit_note_ref,E.gi_go_ref_no,G.warehouse_id from  
                (SELECT A.*,B.total_purchase_amount as purchase ,Truncate((total_purchase_amount+total_tax_amount),2) as cost_inc_tax from 
                (select  A.ref_id,'Debit Note' as ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no,A.voucher_id , 
                 cp_ledger_name, REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1 ,
                 case when A.entry_type='IGST' then  REPLACE(REPLACE(RIGHT(ledger_name,3),'-',''),'%','' )  else (2*REPLACE(REPLACE(RIGHT(ledger_name,3),'-',''),'%','' ) ) end as percentage,entry_type  from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id IN ($account) AND entry_type IN('margindiff_cgst','margindiff_sgst','margindiff_igst','shortage_cgst' ,'shortage_sgst' ,'shortage_igst' , 
                'expiry_cgst' ,'expiry_sgst' ,'expiry_igst' ,'damage_cgst', 'damage_sgst','damage_igst')
                GROUP By A.ref_id,A.ref_type,  B.cp_acc_id, A.invoice_no,A.voucher_id , 
                 cp_ledger_name,ledger_name,entry_type ) A
                left join 
                (select  A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id 
                , cp_ledger_name,REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1,REPLACE(REPLACE(RIGHT(ledger_name,3),'-',''),'%','' )as percentage from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
                ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
                ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
                ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id In($account) AND entry_type IN('margindiff_cost','shortage_cost','expiry_cost','damage_cost') 
                GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,ledger_name ) B
                on A.ref_id=B.ref_id and A.invoice_no=B.invoice_no and A.percentage=B.percentage
                ) C
                left join
                (Select debit_note_ref as debit_note_ref,grn_id,invoice_no from acc_grn_debit_notes ) D  on C.ref_id=D.grn_id and C.invoice_no=D.invoice_no
                left JOIN
                (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn ) G on C.ref_id=G.grn_id 
                left join
                (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no
                 ) A
                left join 
                (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id ";
                if($state!="")
                {
                  $sql.="join
                    (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                    on A.warehouse_id=G.warehouse_code ";
                }
                $sql.="left JOIN
                (select ref_id,voucher_id,amount as total_amount,entry_type  from acc_ledger_entries
                where status = 'Approved' 
                and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry'  and acc_id IN($account) and company_id = '$company_id' and 
                 entry_type='Total Deduction') B
                on A.ref_id=B.ref_id and  A.voucher_id=B.voucher_id
                )  E 
                ORDER BY ref_id ASC,invoice_no ASC,ref_type DESC ,percentage ASC 
                )A  Where $where_condition )B
              ";
        

        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function gettaxwisebifercation($account, $vouchertype,$from_date, $to_date,$date_type,$state)
    {
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

        $sql="Select * from 
            (Select * from 
            (Select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,G.grn_approved_date_time,
                F.updated_date,'' as debit_note_ref from 
            (Select A.ref_id,'purchase' as ref_type,A.cp_acc_id, trim(A.invoice_no) as invoice_no,A.voucher_id ,A.cp_ledger_name,
                TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as cost_inc_tax,
                TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)+IFNULL(other_charges, 0)),2) as total_deduction,
                total_tax_amount, total_purchase_amount as purchase, IFNULL(other_charges,0) as other_charges,
                A.ledger_name, A.entry_type, A.percentage from 
            (Select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id, cp_ledger_name,
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
            (Select A.ref_id, A.invoice_no, A.percentage, sum(amount) as total_purchase_amount from 
            (Select A.id, A.ref_id, A.sub_ref_id, A.ref_type, A.entry_type, A.invoice_no, A.vendor_id, A.acc_id, A.ledger_name, A.ledger_code, 
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
            (Select A.ref_id, A.invoice_no,sum(amount) as other_charges from 
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
            (Select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G 
            on C.ref_id=G.grn_id
            left join
            (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E 
            on C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no ";
            if($state!="")
            {
            $sql.=" join
            (Select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
            on G.warehouse_id=W.warehouse_code ";
            }
            $sql.=" left join 
            (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on C.ref_id=F.grn_id) D
            
            Union 

            Select * from 
            (Select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,G.grn_approved_date_time,F.updated_date,D.debit_note_ref from 
            (Select A.ref_id,'Debit Note' as ref_type,A.cp_acc_id,trim(A.invoice_no) as invoice_no ,A.voucher_id ,A.cp_ledger_name,
                TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as cost_inc_tax,
                TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as total_deduction,
                total_tax_amount,total_purchase_amount as purchase,0 as other_charges,A.ledger_name,A.entry_type,A.percentage from 
            (Select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,
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
            (Select A.ref_id, A.invoice_no, A.percentage, sum(amount) as total_purchase_amount from 
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
            (Select debit_note_ref ,grn_id,invoice_no from acc_grn_debit_notes) D 
            on (C.ref_id=D.grn_id and C.invoice_no=D.invoice_no)
            left JOIN
            (Select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G 
            on (C.ref_id=G.grn_id)
            left join
            (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E 
            on (C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no) ";
            if($state!="")
            {
                $sql.=" join
                (Select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
                on G.warehouse_id=W.warehouse_code ";
            }
            $sql.=" left join 
            (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F 
            on (C.ref_id=F.grn_id)) D) E 
            Where $where_condition ORDER BY ref_id ASC,invoice_no ASC,ref_type DESC,percentage ASC";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
    
    public function tax_wise_column($account, $vouchertype,$from_date, $to_date,$date_type,$state)
    {
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

        $sql="Select Distinct(percentage) as percentage from 
                (Select * from 
                (Select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,G.grn_approved_date_time,
                    F.updated_date,'' as debit_note_ref from 
                (Select A.ref_id,'purchase' as ref_type,A.cp_acc_id,trim(A.invoice_no) as invoice_no,A.voucher_id,A.cp_ledger_name,
                    TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as cost_inc_tax,
                    TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)+IFNULL(other_charges, 0)),2) as total_deduction,
                    total_tax_amount, total_purchase_amount as purchase,
                    IFNULL(other_charges,0) as other_charges,A.ledger_name,A.entry_type,percentage from 
                (Select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id, cp_ledger_name,
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
                (Select A.ref_id, A.invoice_no, sum(amount) as total_purchase_amount from 
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
                (Select A.ref_id, A.invoice_no,sum(amount) as other_charges
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
                (Select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G on C.ref_id=G.grn_id
                left join
                (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no ";
                if($state!="")
                {
                $sql.=" join
                (Select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
                on G.warehouse_id=W.warehouse_code ";
                }
                $sql.=" left join 
                (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on C.ref_id=F.grn_id) D
                Union 
                Select * from (
                Select C.*,G.gi_go_ref_no,G.warehouse_id,G.gi_date,E.invoice_date,
                    G.grn_approved_date_time,F.updated_date,D.debit_note_ref from 
                    (Select A.ref_id,'Debit Note' as ref_type,A.cp_acc_id,trim(A.invoice_no) as invoice_no ,A.voucher_id ,A.cp_ledger_name,
                    TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as cost_inc_tax,TRUNCATE((IFNULL(total_tax_amount,0)+IFNULL(total_purchase_amount,0)),2) as total_deduction,
                    total_tax_amount , total_purchase_amount as purchase ,0 as other_charges,A.ledger_name,A.entry_type ,percentage
                    from (Select A.ref_id,A.ref_type, A.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,
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
                (Select A.ref_id, A.invoice_no ,
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
                (Select debit_note_ref ,grn_id,invoice_no from acc_grn_debit_notes) D  on 
                    C.ref_id=D.grn_id and C.invoice_no=D.invoice_no
                left JOIN
                (Select grn_approved_date_time,gi_date,grn_id,warehouse_id ,gi_id as gi_go_ref_no from grn ) G on C.ref_id=G.grn_id
                left join
                (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no and G.gi_go_ref_no=E.gi_go_ref_no ";
                if($state!="")
                {
                    $sql.=" join
                    (Select warehouse_code from internal_warehouse_master Where state_id IN($state) and company_id=$company_id) W
                    on G.warehouse_id=W.warehouse_code ";
                }
                $sql.=" left join 
                (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F 
                on C.ref_id=F.grn_id) D) E 
                Where $where_condition order by percentage";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getstatewisebifercation($account, $vouchertype,$from_date, $to_date,$date_type,$state)
    {
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

        $sql = "Select * FROM
                (Select * from 
                (Select A.*,F.updated_date,B.total_amount as total_deduction from 
                (SELECT C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,'' as debit_note_ref,E.gi_go_ref_no,G.warehouse_id from 
                (SELECT A.*,B.total_purchase_amount as purchase ,Truncate((total_purchase_amount+total_tax_amount),2) as cost_inc_tax from 
                (SELECT A.ref_id, A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no, A.voucher_id, B.cp_ledger_name, A.percentage, 
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
                (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn) G on C.ref_id=G.grn_id 
                left join 
                (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no) A ";
                if($state!="")
                {
                  $sql.="join
                    (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                    on A.warehouse_id=G.warehouse_code ";  
                }
               $sql.= " left join 
                (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id 
                left JOIN 
                (select ref_id,voucher_id,amount as total_amount,entry_type from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and acc_id IN($account) and company_id = '$company_id' and entry_type='Total Amount') B 
                on A.ref_id=B.ref_id and A.voucher_id=B.voucher_id) D 
                Union 

                Select * from 
                (Select A.*,F.updated_date,B.total_amount from 
                (SELECT C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,D.debit_note_ref,E.gi_go_ref_no,G.warehouse_id from 
                (SELECT A.*,B.total_purchase_amount as purchase ,Truncate((total_purchase_amount+total_tax_amount),2) as cost_inc_tax from 
                (SELECT A.ref_id, A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no, A.voucher_id, B.cp_ledger_name, A.percentage, 
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
                     left join (Select debit_note_ref as debit_note_ref,grn_id,invoice_no from acc_grn_debit_notes ) D on C.ref_id=D.grn_id and C.invoice_no=D.invoice_no 
                     left JOIN (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn ) G on C.ref_id=G.grn_id 
                     left join (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no ) A ";
                    if($state!="")
                    {
                      $sql.=" join
                        (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                        on A.warehouse_id=G.warehouse_code ";  
                    }
                    $sql.=" left join (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id 
                     left JOIN (select ref_id,voucher_id,amount as total_amount,entry_type from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and acc_id IN($account) and company_id = '$company_id' and entry_type='Total Deduction') B on A.ref_id=B.ref_id and A.voucher_id=B.voucher_id ) E ORDER BY ref_id ASC,invoice_no ASC,ref_type DESC ,percentage ASC )A Where $where_condition";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }
   
    public function state_wise_column($account, $vouchertype,$from_date, $to_date,$date_type,$state)
    {
        /*$sql ="Select Distinct(ledger_name) ,REPLACE(REPLACE(RIGHT(ledger_name,3),'-',''),'%','' )as percentage
            from (
            select  A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id ,
            cp_ledger_name,ledger_name from 
            (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and 
            ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
            left join 
            (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, 
            ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1'  and 
            ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
            on (A.voucher_id = B.cp_voucher_id) 
            Where B.cp_acc_id In($account) GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id,cp_ledger_name) A";*/

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
        

        $sql = "Select DISTINCT percentage ,ledger_name FROM
                (Select * from 
                (Select A.*,F.updated_date,B.total_amount as total_deduction from 
                (SELECT C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,'' as debit_note_ref,E.gi_go_ref_no,G.warehouse_id from 
                (SELECT A.*,B.total_purchase_amount as purchase ,Truncate((total_purchase_amount+total_tax_amount),2) as cost_inc_tax from 
                (SELECT A.ref_id, A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no, A.voucher_id, B.cp_ledger_name, A.percentage, 
                    A.entry_type, A.ledger_name from 
                (select ref_id, ref_type, invoice_no, voucher_id, amount, Replace(entry_type,'SGST','CGST') as entry_type, 
                Replace(ledger_name,'SGST','CGST') as ledger_name,
                CASE WHEN entry_type = 'IGST' THEN Replace(Substring_index(ledger_name, '-', -1),'%', '') 
                    ELSE (2 * Replace(Substring_index(ledger_name, '-', -1), '%', '')) END as percentage 
                from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id IN ($account) AND entry_type IN('CGST','IGST','SGST') 
                GROUP By A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name, percentage, entry_type ,ledger_name) A 
                left join 
                (select A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id , cp_ledger_name,REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1, Replace(Substring_index(ledger_name, '-', -1),'%', '' ) as percentage from 
                (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries 
                    where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B 
                on (A.voucher_id = B.cp_voucher_id) 
                Where B.cp_acc_id In($account) AND entry_type IN('Taxable Amount') 
                GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,entry_type ,ledger_name ) B 
                on A.ref_id=B.ref_id and A.invoice_no=B.invoice_no and A.percentage=B.percentage) C 
                left JOIN 
                (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn) G on C.ref_id=G.grn_id 
                left join 
                (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no) A ";
                if($state!="")
                {
                  $sql.="join
                    (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                    on A.warehouse_id=G.warehouse_code ";  
                }
               $sql.= " left join 
                (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id 
                left JOIN 
                (select ref_id,voucher_id,amount as total_amount,entry_type from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and acc_id IN($account) and company_id = '$company_id' and entry_type='Total Amount') B 
                on A.ref_id=B.ref_id and A.voucher_id=B.voucher_id) D 
                Union 

                Select * from 
                (Select A.*,F.updated_date,B.total_amount from 
                (SELECT C.*,G.gi_date,G.grn_approved_date_time,E.invoice_date,D.debit_note_ref,E.gi_go_ref_no,G.warehouse_id from 
                (SELECT A.*,B.total_purchase_amount as purchase ,Truncate((total_purchase_amount+total_tax_amount),2) as cost_inc_tax from 
                (SELECT A.ref_id, A.ref_type, sum(A.amount) as total_tax_amount, B.cp_acc_id, A.invoice_no, A.voucher_id, B.cp_ledger_name, A.percentage, 
                    A.entry_type, A.ledger_name from 
                (select ref_id, 'Debit Note' as ref_type, invoice_no, voucher_id, amount, Replace(entry_type,'sgst','cgst') as entry_type, 
                Replace(ledger_name,'SGST','CGST') as ledger_name,
                CASE WHEN entry_type = 'IGST' THEN Replace(Substring_index(ledger_name, '-', -1),'%', '') 
                    ELSE (2 * Replace(Substring_index(ledger_name, '-', -1), '%', '')) END as percentage from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                left join 
                (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id) Where B.cp_acc_id IN ($account) 
                    AND entry_type IN('margindiff_cgst','margindiff_sgst','margindiff_igst','shortage_cgst' ,'shortage_sgst' ,'shortage_igst' , 'expiry_cgst' ,'expiry_sgst' ,'expiry_igst' ,'damage_cgst', 'damage_sgst','damage_igst') 
                GROUP By A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,entry_type ,ledger_name, percentage) A
                     left join (select A.ref_id,A.ref_type, sum(A.amount) as total_purchase_amount, B.cp_acc_id, A.invoice_no ,A.voucher_id , cp_ledger_name,REPLACE(RIGHT(ledger_name,3),'-','') as ledger_name1,Replace(Substring_index(ledger_name, '-', -1), '%', '') as percentage from (select * from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type != 'Main Entry' and company_id = '$company_id') A 
                     left join (select distinct voucher_id as cp_voucher_id, acc_id as cp_acc_id, ledger_name as cp_ledger_name, ledger_code as cp_ledger_code from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and company_id = '$company_id') B on (A.voucher_id = B.cp_voucher_id) 
                     Where B.cp_acc_id In($account) AND entry_type IN('margindiff_cost','shortage_cost','expiry_cost','damage_cost') GROUP BY A.ref_id,A.ref_type, B.cp_acc_id, A.invoice_no,A.voucher_id , cp_ledger_name,entry_type ,ledger_name ) B on A.ref_id=B.ref_id and A.invoice_no=B.invoice_no and A.percentage=B.percentage ) C 
                     left join (Select debit_note_ref as debit_note_ref,grn_id,invoice_no from acc_grn_debit_notes ) D on C.ref_id=D.grn_id and C.invoice_no=D.invoice_no 
                     left JOIN (Select grn_approved_date_time,gi_date,grn_id,warehouse_id from grn ) G on C.ref_id=G.grn_id 
                     left join (Select invoice_date,invoice_no,gi_go_ref_no from goods_inward_outward_invoices ) E on C.invoice_no=E.invoice_no ) A ";
                    if($state!="")
                    {
                      $sql.=" join
                        (Select warehouse_code from internal_warehouse_master Where state_id IN($state) ) G
                        on A.warehouse_id=G.warehouse_code ";  
                    }
                    $sql.=" left join (Select min(updated_date) as updated_date,grn_id from acc_grn_entries GROUP BY grn_id) F on A.ref_id=F.grn_id 
                     left JOIN (select ref_id,voucher_id,amount as total_amount,entry_type from acc_ledger_entries where status = 'Approved' and is_active = '1' and ref_type = 'purchase' and ledger_type = 'Main Entry' and acc_id IN($account) and company_id = '$company_id' and entry_type='Total Deduction') B on A.ref_id=B.ref_id and A.voucher_id=B.voucher_id ) E ORDER BY ref_id ASC,invoice_no ASC,ref_type DESC ,percentage ASC )A Where $where_condition";
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
                (select acc_id, sum(case when type='Debit' then amount*-1 else 0 end) as debit_amt, 
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
}