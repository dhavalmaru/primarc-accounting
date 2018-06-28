<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use mPDF;

class Promotion extends Model
{
    public function getAccess(){
        $session = Yii::$app->session;
        $session_id = $session['session_id'];
        $role_id = $session['role_id'];

        $sql = "select A.*, '".$session_id."' as session_id from acc_user_role_options A 
                where A.role_id = '$role_id' and A.r_section = 'S_Purchase'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getApprover($action){
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
                where B.company_id = '$company_id' and C.r_section = 'S_Purchase' and 
                        C.r_approval = '1' and C.r_approval is not null" . $cond;
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    // public function getAccountDetails($id=""){
    //     $cond = "";
    //     if($id!=""){
    //         $cond = " and id = '$id'";
    //     }

    //     $session = Yii::$app->session;
    //     $company_id = $session['company_id'];

    //     $sql = "select * from acc_master where is_active = '1' and status = 'approved' and company_id = '$company_id' ".$cond." order by legal_name";
    //     $command = Yii::$app->db->createCommand($sql);
    //     $reader = $command->query();
    //     return $reader->readAll();
    // }

    public function getAccountDetails($id="", $status="", $tax_code=""){
        $cond = "";
        $cond2 = "";
        if($id!=""){
            $cond = " and id = '$id'";
            $cond2 = " and acc_id = '$id'";
        }

        if($status!=""){
            $cond = $cond . " and status = '$status'";
        }

        if($tax_code!=""){
            $cond = $cond . " and legal_name like '%".$tax_code."%'";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, concat_ws(',', A.category_1, A.category_2, A.category_3) as acc_category, B.bus_category from 
                (select * from acc_master where is_active = '1' and company_id = '$company_id' " . $cond . ") A 
                left join 
                (select acc_id, GROUP_CONCAT(category_name) as bus_category from acc_categories where is_active = '1'" . $cond2 . " 
                    group by acc_id) B 
                on (A.id = B.acc_id) order by legal_name";

        // echo $sql;
        // echo '<br/>';
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getVendors($debit_note_ref=""){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $cond = "";
        if($debit_note_ref!=""){
            $cond = " and (B.dn_ref_no is null or B.dn_ref_no = '$debit_note_ref') ";
        } else {
            $cond = " and B.dn_ref_no is null ";
        }

        $sql = "select distinct D.id, D.vendor_name from 
                (select A.promotion_type, B.promotion_id, C.* from vendor_promotions A 
                    left join debit_notes B on (A.id = B.promotion_id) 
                    left join vendor_master C on (A.parent_vendor_id = C.id) 
                where A.company_id = '$company_id' and A.approve_status = 'Approved' and A.is_active = '1' and 
                    A.promo_status = 'Closed' and B.company_id = '$company_id' and B.promo_status = 'Closed'".$cond.") D 
                where D.promotion_id is not null order by D.vendor_name";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getVendorDetails($vendor_id=""){
        $request = Yii::$app->request;
        $company_id = $request->post('company_id');

        $sql = "select A.*, B.* from 
                (select AA.*, BB.legal_entity_name from vendor_master AA left join legal_entity_type_master BB 
                    on (AA.legal_entity_type_id = BB.id) where AA.id = '$vendor_id' and BB.is_active = '1') A 
                left join 
                (select AA.vendor_id, AA.office_address_line_1, AA.office_address_line_2, AA.office_address_line_3, 
                        AA.pincode, BB.city_name, CC.state_name, DD.country_name from 
                vendor_office_address AA left join city_master BB on (AA.city_id = BB.id) left join 
                state_master CC on (AA.state_id = CC.id) left join country_master DD on (AA.country_id = DD.id) 
                where AA.vendor_id = '$vendor_id' and AA.is_active = '1' and BB.is_active = '1' 
                        and CC.is_active = '1' and DD.is_active = '1') B 
                on (A.id = B.vendor_id)";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();;
    }

    public function getPromoTypes($vendor_id="", $debit_note_ref=""){
        $cond = "";
        if($vendor_id!=""){
            $cond = " and A.parent_vendor_id = '$vendor_id' and B.parent_vendor_id = '$vendor_id' ";
        }
        $cond2 = "";
        if($debit_note_ref!=""){
            $cond2 = " and (B.dn_ref_no is null or B.dn_ref_no = '$debit_note_ref') ";
        } else {
            $cond2 = " and B.dn_ref_no is null ";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select distinct C.promotion_type from 
                (select A.promotion_type, B.promotion_id from vendor_promotions A left join debit_notes B on (A.id = B.promotion_id) 
                where A.company_id = '$company_id' and A.approve_status = 'Approved' and A.is_active = '1' and 
                    A.promo_status = 'Closed' and B.company_id = '$company_id' and B.promo_status = 'Closed'".$cond.$cond2.") C 
                where C.promotion_id is not null order by C.promotion_type";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getPromoCodes($vendor_id="", $promotion_type="", $debit_note_ref=""){
        $cond = "";
        if($vendor_id!=""){
            $cond = " and A.parent_vendor_id = '$vendor_id' and B.parent_vendor_id = '$vendor_id' ";
        }
        if($promotion_type!=""){
            $cond = $cond . " and A.promotion_type = '$promotion_type' ";
        }
        $cond2 = "";
        if($debit_note_ref!=""){
            $cond2 = " and (B.dn_ref_no is null or B.dn_ref_no = '$debit_note_ref') ";
        } else {
            $cond2 = " and B.dn_ref_no is null ";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select distinct C.promotion_code from 
                (select A.promotion_code, B.promotion_id from vendor_promotions A left join debit_notes B on (A.id = B.promotion_id) 
                where A.company_id = '$company_id' and A.approve_status = 'Approved' and A.is_active = '1' and 
                    A.promo_status = 'Closed' and B.company_id = '$company_id' and B.promo_status = 'Closed'".$cond.$cond2.") C 
                where C.promotion_id is not null order by C.promotion_code";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getDetails($vendor_id="", $promotion_type="", $promotion_code=array(), $debit_note_ref=""){
        $cond = "";
        if($vendor_id!=""){
            $cond = " and A.parent_vendor_id = '$vendor_id' and B.parent_vendor_id = '$vendor_id' ";
        }
        if($promotion_type!=""){
            $cond = $cond . " and A.promotion_type = '$promotion_type' ";
        }
        $cond2 = "";
        if($debit_note_ref!=""){
            $cond2 = " and (B.dn_ref_no is null or B.dn_ref_no = '$debit_note_ref') ";
        } else {
            $cond2 = " and B.dn_ref_no is null ";
        }
        $cond3 = "";
        $promotion_codes = "";
        if(count($promotion_code)>0){
            for($i=0; $i<count($promotion_code); $i++){
                $promotion_codes = $promotion_codes . "'" . $promotion_code[$i] . "', ";
            }
            if($promotion_codes!=''){
                $promotion_codes = substr($promotion_codes, 0, strrpos($promotion_codes, ", "));

                $cond3 = $cond3 . " and A.promotion_code in (".$promotion_codes.")";
            }
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select sum(C.debit_amount) as total_amount from 
                (select A.promotion_code, B.promotion_id, debit_amount from vendor_promotions A 
                    left join debit_notes B on (A.id = B.promotion_id) 
                where A.company_id = '$company_id' and A.approve_status = 'Approved' and A.is_active = '1' and 
                    A.promo_status = 'Closed' and B.company_id = '$company_id' and B.promo_status = 'Closed'".$cond.$cond2.$cond3.") C 
                where C.promotion_id is not null";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getWarehouseDetails($warehouse_id=""){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $cond = "";
        if($warehouse_id!=""){
            $cond = $cond . " and A.id = '$warehouse_id'";
        }

        $sql = "select distinct C.id, C.warehouse_gst, C.state_name from 
                (select A.id, concat(A.warehouse_name, '-', A.gst_id) as warehouse_gst, B.state_name 
                from internal_warehouse_master A left join state_master B on (A.state_id = B.id) 
                where A.company_id = '$company_id' and A.is_active = '1'".$cond.") C order by C.warehouse_gst";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function getTransaction($acc_details, $acc_type, $acc_id, $i, $amount, $transaction){
        $mycomponent = Yii::$app->mycomponent;

        $acc_master = '<option value="">Select</option>';
        $account_name = '';
        $account_code = '';
        for($j=0; $j<count($acc_details); $j++) {
            if($acc_details[$j]['type']==$acc_type){
                if($acc_id==$acc_details[$j]['id']){
                    $acc_master = $acc_master . '<option value="'.$acc_details[$j]['id'].'" selected>'.$acc_details[$j]['legal_name'].'</option>';
                    $account_name = $acc_details[$j]['legal_name'];
                    $account_code = $acc_details[$j]['code'];
                } else {
                    $acc_master = $acc_master . '<option value="'.$acc_details[$j]['id'].'">'.$acc_details[$j]['legal_name'].'</option>';
                }
            }
        }

        $result = '<tr id="row_'.$i.'">
                        <td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_row_'.$i.'" onClick="delete_row(this);">-</button></td>
                        <td  style="text-align: center; display: none;" id="sr_no_'.$i.'">1</td>
                        <td>
                            <input class="form-control" type="hidden" name="entry_id[]" id="entry_id_'.$i.'" value="" />
                            <select class="form-control" name="acc_id[]" id="acc_id_'.$i.'" onchange="get_acc_details(this);">
                                '.$acc_master.'
                            </select>
                            <input class="form-control" type="hidden" name="legal_name[]" id="legal_name_'.$i.'" value="'.$account_name.'" />
                        </td>
                        <td><input class="form-control" type="text" name="acc_code[]" id="acc_code_'.$i.'" value="'.$account_code.'" readonly /></td>
                        <td>
                            <select class="form-control" name="transaction[]" id="trans_'.$i.'" onchange="set_transaction(this);">
                                <option value="">Select</option>
                                <option value="Debit" '.($transaction=="Debit"?"selected":"").'>Debit</option>
                                <option value="Credit" '.($transaction=="Credit"?"selected":"").'>Credit</option>
                            </select>
                        </td>
                        <td><input class="form-control debit_amt" type="text" name="debit_amt[]" id="debit_amt_'.$i.'" value="'.($transaction=="Debit"?$mycomponent->format_money($amount,2):"0.00").'" onChange="get_total();" '.($transaction=="Debit"?"":"readonly").' /></td>
                        <td><input class="form-control credit_amt" type="text" name="credit_amt[]" id="credit_amt_'.$i.'" value="'.($transaction=="Credit"?$mycomponent->format_money($amount,2):"0.00").'" onChange="get_total();" '.($transaction=="Credit"?"":"readonly").' /></td>
                    </tr>';

        return $result;
    }

    public function getPromotionDetails($id="", $status=""){
        $cond = "";
        if($id!=""){
            $cond = " and A.id = '$id'";
        }
        if($status!=""){
            $cond = $cond . " and A.status = '$status'";
        }

        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select A.*, B.username as updater, C.username as approver 
                from acc_promotion_details A left join user B on (A.updated_by = B.id) left join user C on (A.approved_by = C.id) 
                where A.is_active='1' and A.company_id = '$company_id' " . $cond . " 
                order by UNIX_TIMESTAMP(A.updated_date) desc, A.id desc";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function gerPromotionEntries($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_promotion_entries where promotion_id='$id' and company_id = '$company_id' and is_active='1' order by id";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        return $reader->readAll();
    }

    public function save(){
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

    public function saveEdit(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');

        $id = $request->post('id');
        $vendor_id = $request->post('vendor_id');
        $promotion_type = $request->post('promotion_type');
        $promotion_code = $request->post('promotion_code');
        if(count($promotion_code)>0){
            $promotion_codes = implode(", ", $promotion_code);
        } else {
            $promotion_codes = '';
        }
        $voucher_id = $request->post('voucher_id');
        $ledger_type = $request->post('ledger_type');
        $debit_note_ref = $request->post('debit_note_ref');
        $entry_id = $request->post('entry_id');
        $acc_id = $request->post('acc_id');
        $legal_name = $request->post('legal_name');
        $acc_code = $request->post('acc_code');
        $transaction = $request->post('transaction');
        $debit_amt = $request->post('debit_amt');
        $credit_amt = $request->post('credit_amt');
        $total_debit_amt = $request->post('total_debit_amt');
        $total_credit_amt = $request->post('total_credit_amt');
        $diff_amt = $request->post('diff_amt');
        $trans_type = $request->post('trans_type');
        $warehouse_id = $request->post('warehouse_id');
        $date_of_transaction = $request->post('date_of_transaction');
        if($date_of_transaction==''){
            $date_of_transaction=NULL;
        } else {
            $date_of_transaction=$mycomponent->formatdate($date_of_transaction);
        }
        $remarks = $request->post('remarks');
        // $approver_id = $request->post('approver_id');

        $debit_acc = "";
        $credit_acc = "";
        for($i=0; $i<count($legal_name); $i++){
            if($transaction[$i]=='Debit'){
                $debit_acc = $debit_acc . $legal_name[$i] . ', ';
            } else {
                $credit_acc = $credit_acc . $legal_name[$i] . ', ';
            }
        }
        if(strlen($debit_acc)>0){
            $debit_acc = substr($debit_acc, 0, strrpos($debit_acc, ', '));
        }
        if(strlen($credit_acc)>0){
            $credit_acc = substr($credit_acc, 0, strrpos($credit_acc, ', '));
        }

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
            $ledger_type = 'Main Entry';
        }

        if(!isset($debit_note_ref) || $debit_note_ref==''){
            $debit_note_ref = $this->getDebitNoteRef($date_of_transaction, 'Maharashtra');
        }

        $array = array('vendor_id' => $vendor_id, 
                        'promotion_type' => $promotion_type, 
                        'promotion_code' => $promotion_codes, 
                        'voucher_id' => $voucher_id, 
                        'ledger_type' => $ledger_type, 
                        'trans_type' => $trans_type,
                        'warehouse_id' => $warehouse_id, 
                        'debit_acc' => $debit_acc, 
                        'credit_acc' => $credit_acc, 
                        'debit_amt' => $mycomponent->format_number($total_debit_amt,2), 
                        'credit_amt' => $mycomponent->format_number($total_credit_amt,2), 
                        'diff_amt' => $mycomponent->format_number($diff_amt,2),
                        // 'status' => 'pending',
                        'status' => 'approved',
                        'is_active' => '1',
                        'updated_by'=>$curusr,
                        'updated_date'=>$now,
                        'date_of_transaction'=>$date_of_transaction,
                        'approver_comments'=>$remarks,
                        // 'approver_id'=>$approver_id,
                        'company_id'=>$company_id,
                        'debit_note_ref'=>$debit_note_ref
                    );

        echo json_encode($array);
        echo '<br/>';

        // if(count($array)>0){
        //     if (isset($id) && $id!=""){
        //         $count = Yii::$app->db->createCommand()
        //                     ->update("acc_promotion_details", $array, "id = '".$id."'")
        //                     ->execute();

        //         $this->setLog('Promotion', '', 'Save', '', 'Update Promotion Details', 'acc_promotion_details', $id);
        //     } else {
        //         $array['created_by'] = $curusr;
        //         $array['created_date'] = $now;
        //         $count = Yii::$app->db->createCommand()
        //                     ->insert("acc_promotion_details", $array)
        //                     ->execute();
        //         $id = Yii::$app->db->getLastInsertID();

        //         $this->setLog('Promotion', '', 'Save', '', 'Insert Promotion Details', 'acc_promotion_details', $id);
        //     }
        // }



        $acc_promotion_entries = array();

        $sql = "delete from acc_promotion_entries where promotion_id = '$id'";
        Yii::$app->db->createCommand($sql)->execute();

        $sql = "delete from acc_ledger_entries where ref_id = '".$id."' and ref_type = 'promotion'";
        Yii::$app->db->createCommand($sql)->execute();

        for($i=0; $i<count($acc_id); $i++){
            $acc_promotion_entries = array('promotion_id' => $id, 
                                    'account_id' => $acc_id[$i], 
                                    'account_name' => $legal_name[$i], 
                                    'account_code' => $acc_code[$i], 
                                    'transaction' => $transaction[$i], 
                                    'debit_amt' => $mycomponent->format_number($debit_amt[$i],2), 
                                    'credit_amt' => $mycomponent->format_number($credit_amt[$i],2),
                                    // 'status' => 'pending',
                                    'status' => 'approved',
                                    'is_active' => '1',
                                    'updated_by'=>$curusr,
                                    'updated_date'=>$now,
                                    'approver_comments'=>$remarks,
                                    'company_id'=>$company_id
                                );

            echo json_encode($acc_promotion_entries);
            echo '<br/>';

            // $acc_promotion_entries['created_by'] = $curusr;
            // $acc_promotion_entries['created_date'] = $now;
            // $count = Yii::$app->db->createCommand()
            //             ->insert("acc_promotion_entries", $acc_promotion_entries)
            //             ->execute();
            // $entry_id[$i] = Yii::$app->db->getLastInsertID();

            // if (isset($entry_id[$i]) && $entry_id[$i]!=""){
            //     $count = Yii::$app->db->createCommand()
            //                 ->update("acc_promotion_entries", $acc_promotion_entries, "id = '".$entry_id[$i]."'")
            //                 ->execute();
            // } else {
            //     $acc_promotion_entries['created_by'] = $curusr;
            //     $acc_promotion_entries['created_date'] = $now;

            //     $count = Yii::$app->db->createCommand()
            //                 ->insert("acc_promotion_entries", $acc_promotion_entries)
            //                 ->execute();
            //     $entry_id[$i] = Yii::$app->db->getLastInsertID();
            // }

            if($transaction[$i]=="Debit"){
                $amount = $debit_amt[$i];
            } else {
                $amount = $credit_amt[$i];
            }

            $ledgerArray=[
                            'ref_id'=>$id,
                            'sub_ref_id'=>$entry_id[$i],
                            'ref_type'=>'promotion',
                            'entry_type'=>'Promotion',
                            'invoice_no'=>$reference,
                            // 'vendor_id'=>$vendor_id,
                            'voucher_id' => $voucher_id, 
                            'ledger_type' => $ledger_type, 
                            'acc_id'=>$acc_id[$i],
                            'ledger_name'=>$legal_name[$i],
                            'ledger_code'=>$acc_code[$i],
                            'type'=>$transaction[$i],
                            'amount'=>$mycomponent->format_number($amount,2),
                            // 'status'=>'pending',
                            'status'=>'approved',
                            'is_active'=>'1',
                            'updated_by'=>$curusr,
                            'updated_date'=>$now,
                            'ref_date'=>$date_of_transaction,
                            'approver_comments'=>$remarks,
                            'company_id'=>$company_id
                        ];

            echo json_encode($ledgerArray);
            echo '<br/>';

            // $ledgerArray['created_by'] = $curusr;
            // $ledgerArray['created_date'] = $now;
            // $count = Yii::$app->db->createCommand()
            //             ->insert("acc_ledger_entries", $ledgerArray)
            //             ->execute();

            // $count = Yii::$app->db->createCommand()
            //             ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and sub_ref_id = '".$entry_id[$i]."' and ref_type = 'promotion'")
            //             ->execute();

            // if ($count==0){
            //     $ledgerArray['created_by'] = $curusr;
            //     $ledgerArray['created_date'] = $now;

            //     $count = Yii::$app->db->createCommand()
            //                 ->insert("acc_ledger_entries", $ledgerArray)
            //                 ->execute();
            // }
        }

        return true;
    }

    public function getDebitNoteRef($date_of_transaction, $warehouse_state){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        // $date_of_transaction = "2018-05-25";
        // $warehouse_state = 'Maharashtra';
        $code = '';

        $dateTime = \DateTime::createFromFormat('Y-m-d', $date_of_transaction);
        $from = $dateTime->format('Y');
        $to = $dateTime->format('Y');
        if (date('m') > 3) {
            $to = (int)($dateTime->format('Y')) +1;
        } else {
            $from = (int)($dateTime->format('Y')) -1;
        }
        $year = substr($from, 2) . substr($to, 2);

        $month = $dateTime->format('M');

        $state_code = '';
        $sql = "select * from state_master where state_name = '$warehouse_state'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if (count($data)>0){
            $state_code = $data[0]['state_code'];
        }

        $sql = "select * from acc_series_master where type = 'debit_note' and company_id = '$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $data = $reader->readAll();
        if (count($data)>0){
            $series = intval($data[0]['series']) + 1;

            $sql = "update acc_series_master set series = '$series' where type = 'debit_note' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();
        } else {
            $series = 1;

            $sql = "insert into acc_series_master (type, series, company_id) values ('debit_note', '".$series."', '".$company_id."')";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();
        }

        $code = $year . "/" . $month . "/" . $state_code;
        $code = $code . "/" . str_pad($series, 3, "0", STR_PAD_LEFT);

        // echo $code;
        return $code;
    }

    public function getDebitNoteDetails($id){
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $sql = "select * from acc_promotion_details where id = '$id' and is_active = '1' and company_id = '$company_id'";
        $command = Yii::$app->db->createCommand($sql);
        $reader = $command->query();
        $debit_note = $reader->readAll();

        if(count($debit_note)>0) {
            $trans_type = $debit_note[0]['trans_type'];
            $vendor_code = '';

            $sql = "select * from acc_promotion_entries where promotion_id = '$id' and 
                    transaction = '$trans_type' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
            if(count($result)>0){
                $vendor_code = $result[0]['account_code'];
            }

            $vendor_id = '';
            $sql = "select * from vendor_master where vendor_code like '%".$vendor_code."%' and 
                    is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $result = $reader->readAll();
            if(count($result)>0){
                $vendor_id = $result[0]['id'];
            }

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

            $mpdf=new mPDF();
            $mpdf->WriteHTML(Yii::$app->controller->renderPartial('debit_note', ['debit_note' => $debit_note, 'vendor_details' => $vendor_details]));

            $upload_path = './uploads';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/promotion_debit_notes';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }
            $upload_path = './uploads/promotion_debit_notes/'.$id;
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }

            $file_name = $upload_path . '/promotion_debit_note_' . $id . '.pdf';
            $file_path = 'uploads/promotion_debit_notes/' . $id . '/promotion_debit_note_' . $id . '.pdf';

            // $mpdf->Output('MyPDF.pdf', 'D');
            $mpdf->Output($file_name, 'F');
            // exit;

            $sql = "update acc_promotion_details set debit_credit_note_path = '$file_path' where id = '$id' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $count = $command->execute();

            $sql = "select * from acc_promotion_details where id = '$id' and is_active = '1' and company_id = '$company_id'";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $debit_note = $reader->readAll();

        } else {
            $debit_note = array();
            $vendor_details = array();
        }

        $data['debit_note'] = $debit_note;
        $data['vendor_details'] = $vendor_details;
        
        return $data;
    }
    
    public function setLog($module_name, $sub_module, $action, $vendor_id, $description, $table_name, $table_id) {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $company_id = $session['company_id'];
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
                        'company_id'=>$company_id);
        $count = Yii::$app->db->createCommand()
                            ->insert("acc_user_log", $array)
                            ->execute();

        return true;
    }
    
    public function setEmailLog($vendor_name, $from_email_id, $to_recipient, $reference_number, $email_content, 
                                $email_attachment, $attachment_type, $email_sent_status, $error_message, $company_id)
    {
        $session = Yii::$app->session;
        $curusr = $session['session_id'];
        $username = $session['username'];
        $now = date('Y-m-d H:i:s');
        $company_id = $session['company_id'];

        $array = array('module' => 'DN', 
                        'email_type' => 'Debit Note Email', 
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
}