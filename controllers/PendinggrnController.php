<?php

namespace app\controllers;

use Yii;
use app\models\PendingGrn;
use app\models\AccountMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Session;

/**
 * GrnController implements the CRUD actions for Grn model.
 */
class PendinggrnController extends Controller
{
    public function actionIndex()
    {
        $grn_cnt = new PendingGrn();
        $grn = $grn_cnt->getNewGrnDetails();
        $pending = $grn_cnt->getPurchaseDetails('pending');
        $approved = $grn_cnt->getPurchaseDetails('approved');
        $all = $grn_cnt->getAllGrnDetails();
        return $this->render('pending_grn', [
            'grn' => $grn, 'pending' => $pending, 'approved' => $approved, 'all' => $all
        ]);
    }

    public function actionViewdebitnote($invoice_id){
        $model = new PendingGrn();
        $data = $model->getDebitNoteDetails($invoice_id);
        
        $this->layout = false;
        return $this->render('debit_note', [
            'invoice_details' => $data['invoice_details'], 'debit_note' => $data['debit_note'], 
            'deduction_details' => $data['deduction_details'], 'vendor_details' => $data['vendor_details']
        ]);
    }

    public function actionDownload($invoice_id){
        $model = new PendingGrn();
        $data = $model->getDebitNoteDetails($invoice_id);
        $file = "";

        if(isset($data['debit_note'])){
            if(count($data['debit_note'])>0){
                $debot_note = $data['debit_note'];
                $file = $debot_note[0]['debit_note_path'];
            }
        }

        if( file_exists( $file ) ){
            Yii::$app->response->sendFile($file);
        } else {
            echo $file;
        }
    }

    public function actionEmaildebitnote($invoice_id){
        $model = new PendingGrn();
        $data = $model->getDebitNoteDetails($invoice_id);
        $file = "";

        return $this->render('email', [
            'invoice_details' => $data['invoice_details'], 'debit_note' => $data['debit_note'], 
            'deduction_details' => $data['deduction_details'], 'vendor_details' => $data['vendor_details']
        ]);
    }

    public function actionEmail(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

        $id = $request->post('id');
        $grn_id = $request->post('grn_id');
        $invoice_id = $request->post('invoice_id');
        // $from = $request->post('from');
        $from = 'dhaval.maru@otbconsulting.co.in';
        $to = $request->post('to');
        // $to = 'prasad.bhisale@otbconsulting.co.in';
        $subject = $request->post('subject');
        $attachment = $request->post('attachment');
        $body = $request->post('body');

        // $grn_id = '28';
        // $from = 'dhaval.maru@otbconsulting.co.in';
        // $to = 'prasad.bhisale@otbconsulting.co.in';
        // $subject = 'Test Email';
        // $attachment = 'uploads/debit_notes/28/debit_note_invoice_90.pdf';
        // $body = 'Testing';

        $message = Yii::$app->mailer->compose();
        $message->setFrom($from);
        $message->setTo($to);
        $message->setSubject($subject);
        $message->setTextBody($body);
        // $message->setHtmlBody($body);
        $message->attach($attachment);

        $response = $message->send();
        if($response=='1'){
            $data['response'] = 'Mail Sent.';
        } else {
            $data['response'] = 'Mail Sending Failed.';
        }
        $data['id'] = $id;
        $data['grn_id'] = $grn_id;
        $data['invoice_id'] = $invoice_id;

        return $this->render('email_response', ['data' => $data]);
    }

    public function actionUpdate($id)
    {
        $model = new PendingGrn();
        $account_master = new AccountMaster();

        $grn_acc_entries = $model->getGrnAccEntries($id);
        $grn_details = $model->getGrnDetails($id);
        $total_val = $model->getTotalValue($id);
        $total_tax = $model->getTotalTax($id);

        $acc_master = $account_master->getAccountDetails('', 'pending');

        if (count($grn_acc_entries) > 0){
            // echo json_encode($grn_acc_entries);

            $num = -1;
            $prev_invoice_no = "";
            $invoice_no = "";
            $invoice_details = array();
            $narration = array();
            $invoice_tax = array();
            $acc = array();
            $prev_tax = "";
            $tax = "";
            $tax_num = 0;

            for($i=0; $i<count($grn_acc_entries); $i++){
                $invoice_no = $grn_acc_entries[$i]['invoice_no'];

                if($prev_invoice_no != $invoice_no){
                    $num = $num + 1;
                    $invoice_details[$num] = array();
                    // array_push($invoice_details[$num], array('invoice_no'=>$invoice_no));
                    // $invoice_details[] = array('invoice_no'=>$invoice_no);
                    $invoice_details[$num]['invoice_no'] = $invoice_no;
                    $prev_invoice_no = $invoice_no;
                    // $tax_num = 0;
                }
                
                // if($grn_acc_entries[$i]['particular']=="Taxable Amount"){
                //     $invoice_details[$num]['invoice_total_cost'] = $grn_acc_entries[$i]['invoice_val'];
                //     $invoice_details[$num]['edited_total_cost'] = $grn_acc_entries[$i]['edited_val'];
                //     $invoice_details[$num]['diff_total_cost'] = $grn_acc_entries[$i]['difference_val'];
                //     $narration['narration_taxable_amount'] = $grn_acc_entries[$i]['narration'];
                // }

                // if($grn_acc_entries[$i]['particular']=="Tax"){
                //     $invoice_details[$num]['invoice_total_tax'] = $grn_acc_entries[$i]['invoice_val'];
                //     $invoice_details[$num]['edited_total_tax'] = $grn_acc_entries[$i]['edited_val'];
                //     $invoice_details[$num]['diff_total_tax'] = $grn_acc_entries[$i]['difference_val'];
                //     $narration['narration_total_tax'] = $grn_acc_entries[$i]['narration'];
                // }

                if($grn_acc_entries[$i]['particular']=="Taxable Amount" || $grn_acc_entries[$i]['particular']=="Tax"){
                    $blFlag = false;

                    // if($grn_acc_entries[$i]['particular']=="Tax"){
                    //     $blFlag = true;
                    //     $invoice_tax[$tax_num]['invoice_tax_acc_id'] = $grn_acc_entries[$i]['acc_id'];
                    //     $invoice_tax[$tax_num]['invoice_tax_ledger_name'] = $grn_acc_entries[$i]['ledger_name'];
                    //     $invoice_tax[$tax_num]['invoice_tax_ledger_code'] = $grn_acc_entries[$i]['ledger_code'];
                    //     $invoice_tax[$tax_num]['invoice_tax'] = $grn_acc_entries[$i]['invoice_val'];
                    //     $invoice_tax[$tax_num]['edited_tax'] = $grn_acc_entries[$i]['edited_val'];
                    //     $invoice_tax[$tax_num]['diff_tax'] = $grn_acc_entries[$i]['difference_val'];
                    //     $narration[$tax_num]['tax'] = $grn_acc_entries[$i]['narration'];
                    // }

                    if($grn_acc_entries[$i]['particular']=="Taxable Amount" || $grn_acc_entries[$i]['particular']=="Tax"){
                        for($k=0; $k<count($invoice_tax); $k++){
                            if($invoice_tax[$k]['invoice_no']==$grn_acc_entries[$i]['invoice_no'] && 
                                $invoice_tax[$k]['vat_cst']==$grn_acc_entries[$i]['vat_cst'] && 
                                $invoice_tax[$k]['vat_percen']==$grn_acc_entries[$i]['vat_percen']){
                                $blFlag = true;
                                if($grn_acc_entries[$i]['particular']=="Taxable Amount"){
                                    $invoice_tax[$k]['invoice_cost_acc_id'] = $grn_acc_entries[$i]['acc_id'];
                                    $invoice_tax[$k]['invoice_cost_ledger_name'] = $grn_acc_entries[$i]['ledger_name'];
                                    $invoice_tax[$k]['invoice_cost_ledger_code'] = $grn_acc_entries[$i]['ledger_code'];
                                    // $invoice_tax[$k]['invoice_cost_voucher_id'] = $grn_acc_entries[$i]['voucher_id'];
                                    // $invoice_tax[$k]['invoice_cost_ledger_type'] = $grn_acc_entries[$i]['ledger_type'];
                                    $invoice_tax[$k]['invoice_cost'] = $grn_acc_entries[$i]['invoice_val'];
                                    $invoice_tax[$k]['edited_cost'] = $grn_acc_entries[$i]['edited_val'];
                                    $invoice_tax[$k]['diff_cost'] = $grn_acc_entries[$i]['difference_val'];
                                    $narration[$k]['cost'] = $grn_acc_entries[$i]['narration'];
                                } else {
                                    $invoice_tax[$k]['invoice_tax_acc_id'] = $grn_acc_entries[$i]['acc_id'];
                                    $invoice_tax[$k]['invoice_tax_ledger_name'] = $grn_acc_entries[$i]['ledger_name'];
                                    $invoice_tax[$k]['invoice_tax_ledger_code'] = $grn_acc_entries[$i]['ledger_code'];
                                    // $invoice_tax[$k]['invoice_tax_voucher_id'] = $grn_acc_entries[$i]['voucher_id'];
                                    // $invoice_tax[$k]['invoice_tax_ledger_type'] = $grn_acc_entries[$i]['ledger_type'];
                                    $invoice_tax[$k]['invoice_tax'] = $grn_acc_entries[$i]['invoice_val'];
                                    $invoice_tax[$k]['edited_tax'] = $grn_acc_entries[$i]['edited_val'];
                                    $invoice_tax[$k]['diff_tax'] = $grn_acc_entries[$i]['difference_val'];
                                    $narration[$k]['tax'] = $grn_acc_entries[$i]['narration'];
                                }

                                // echo json_encode($invoice_tax);
                                // echo '<br/>';
                            }
                        }
                    }
                    
                    if($blFlag==false){
                        $invoice_tax[$tax_num]['particular'] = $grn_acc_entries[$i]['particular'];
                        $invoice_tax[$tax_num]['tax_zone_code'] = $grn_acc_entries[$i]['tax_zone_code'];
                        $invoice_tax[$tax_num]['invoice_no'] = $grn_acc_entries[$i]['invoice_no'];
                        $invoice_tax[$tax_num]['sub_particular_cost'] = $grn_acc_entries[$i]['sub_particular'];
                        $invoice_tax[$tax_num]['vat_cst'] = $grn_acc_entries[$i]['vat_cst'];
                        $invoice_tax[$tax_num]['vat_percen'] = $grn_acc_entries[$i]['vat_percen'];

                        if($grn_acc_entries[$i]['particular']=="Taxable Amount"){
                            $invoice_tax[$tax_num]['invoice_cost_acc_id'] = $grn_acc_entries[$i]['acc_id'];
                            $invoice_tax[$tax_num]['invoice_cost_ledger_name'] = $grn_acc_entries[$i]['ledger_name'];
                            $invoice_tax[$tax_num]['invoice_cost_ledger_code'] = $grn_acc_entries[$i]['ledger_code'];
                            // $invoice_tax[$tax_num]['invoice_cost_voucher_id'] = $grn_acc_entries[$i]['voucher_id'];
                            // $invoice_tax[$tax_num]['invoice_cost_ledger_type'] = $grn_acc_entries[$i]['ledger_type'];
                            $invoice_tax[$tax_num]['invoice_cost'] = $grn_acc_entries[$i]['invoice_val'];
                            $invoice_tax[$tax_num]['edited_cost'] = $grn_acc_entries[$i]['edited_val'];
                            $invoice_tax[$tax_num]['diff_cost'] = $grn_acc_entries[$i]['difference_val'];
                            $narration[$tax_num]['cost'] = $grn_acc_entries[$i]['narration'];
                        } else {
                            $invoice_tax[$tax_num]['invoice_tax_acc_id'] = $grn_acc_entries[$i]['acc_id'];
                            $invoice_tax[$tax_num]['invoice_tax_ledger_name'] = $grn_acc_entries[$i]['ledger_name'];
                            $invoice_tax[$tax_num]['invoice_tax_ledger_code'] = $grn_acc_entries[$i]['ledger_code'];
                            // $invoice_tax[$tax_num]['invoice_tax_voucher_id'] = $grn_acc_entries[$i]['voucher_id'];
                            // $invoice_tax[$tax_num]['invoice_tax_ledger_type'] = $grn_acc_entries[$i]['ledger_type'];
                            $invoice_tax[$tax_num]['invoice_tax'] = $grn_acc_entries[$i]['invoice_val'];
                            $invoice_tax[$tax_num]['edited_tax'] = $grn_acc_entries[$i]['edited_val'];
                            $invoice_tax[$tax_num]['diff_tax'] = $grn_acc_entries[$i]['difference_val'];
                            $narration[$tax_num]['tax'] = $grn_acc_entries[$i]['narration'];
                        }
                        
                        $tax_num = $tax_num + 1;
                        // echo json_encode($invoice_tax);
                        // echo '<br/>';
                    }
                }

                // if($grn_acc_entries[$i]['particular']=="Tax"){
                //     $invoice_tax[$tax_num]['tax_zone_code'] = $grn_acc_entries[$i]['tax_zone_code'];
                //     $invoice_tax[$tax_num]['invoice_no'] = $grn_acc_entries[$i]['invoice_no'];
                //     $invoice_tax[$tax_num]['sub_particular'] = $grn_acc_entries[$i]['sub_particular'];
                //     $invoice_tax[$tax_num]['vat_cst'] = $grn_acc_entries[$i]['vat_cst'];
                //     $invoice_tax[$tax_num]['vat_percen'] = $grn_acc_entries[$i]['vat_percen'];
                //     $invoice_tax[$tax_num]['invoice_tax'] = $grn_acc_entries[$i]['invoice_val'];
                //     $invoice_tax[$tax_num]['edited_tax'] = $grn_acc_entries[$i]['edited_val'];
                //     $invoice_tax[$tax_num]['diff_tax'] = $grn_acc_entries[$i]['difference_val'];
                //     $narration[$tax_num] = $grn_acc_entries[$i]['narration'];
                //     $tax_num = $tax_num + 1;
                // }

                if($grn_acc_entries[$i]['particular']=="Other Charges"){
                    $acc['other_charges_acc_id'] = $grn_acc_entries[$i]['acc_id'];
                    $acc['other_charges_ledger_name'] = $grn_acc_entries[$i]['ledger_name'];
                    $acc['other_charges_ledger_code'] = $grn_acc_entries[$i]['ledger_code'];
                    // $acc['other_charges_voucher_id'] = $grn_acc_entries[$i]['voucher_id'];
                    // $acc['other_charges_ledger_type'] = $grn_acc_entries[$i]['ledger_type'];
                    $invoice_details[$num]['invoice_other_charges'] = $grn_acc_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_other_charges'] = $grn_acc_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_other_charges'] = $grn_acc_entries[$i]['difference_val'];
                    $narration['narration_other_charges'] = $grn_acc_entries[$i]['narration'];
                }

                if($grn_acc_entries[$i]['particular']=="Total Amount"){
                    $acc['total_amount_acc_id'] = $grn_acc_entries[$i]['acc_id'];
                    $acc['total_amount_ledger_name'] = $grn_acc_entries[$i]['ledger_name'];
                    $acc['total_amount_ledger_code'] = $grn_acc_entries[$i]['ledger_code'];
                    $invoice_details[$num]['invoice_total_amount'] = $grn_acc_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_total_amount'] = $grn_acc_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_total_amount'] = $grn_acc_entries[$i]['difference_val'];
                    $invoice_details[$num]['total_amount_voucher_id'] = $grn_acc_entries[$i]['voucher_id'];
                    $invoice_details[$num]['total_amount_ledger_type'] = $grn_acc_entries[$i]['ledger_type'];
                    $narration['narration_total_amount'] = $grn_acc_entries[$i]['narration'];
                }

                if($grn_acc_entries[$i]['particular']=="Shortage Amount"){
                    $invoice_details[$num]['invoice_shortage_amount'] = $grn_acc_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_shortage_amount'] = $grn_acc_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_shortage_amount'] = $grn_acc_entries[$i]['difference_val'];
                    $narration['narration_shortage_amount'] = $grn_acc_entries[$i]['narration'];
                }

                if($grn_acc_entries[$i]['particular']=="Expiry Amount"){
                    $invoice_details[$num]['invoice_expiry_amount'] = $grn_acc_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_expiry_amount'] = $grn_acc_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_expiry_amount'] = $grn_acc_entries[$i]['difference_val'];
                    $narration['narration_expiry_amount'] = $grn_acc_entries[$i]['narration'];
                }

                if($grn_acc_entries[$i]['particular']=="Damaged Amount"){
                    $invoice_details[$num]['invoice_damaged_amount'] = $grn_acc_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_damaged_amount'] = $grn_acc_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_damaged_amount'] = $grn_acc_entries[$i]['difference_val'];
                    $narration['narration_damaged_amount'] = $grn_acc_entries[$i]['narration'];
                }

                if($grn_acc_entries[$i]['particular']=="Margin Diff Amount"){
                    $invoice_details[$num]['invoice_margin_diff_amount'] = $grn_acc_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_margin_diff_amount'] = $grn_acc_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_margin_diff_amount'] = $grn_acc_entries[$i]['difference_val'];
                    $narration['narration_margin_diff_amount'] = $grn_acc_entries[$i]['narration'];
                }

                if($grn_acc_entries[$i]['particular']=="Total Deduction"){
                    $acc['total_deduction_acc_id'] = $grn_acc_entries[$i]['acc_id'];
                    $acc['total_deduction_ledger_name'] = $grn_acc_entries[$i]['ledger_name'];
                    $acc['total_deduction_ledger_code'] = $grn_acc_entries[$i]['ledger_code'];
                    $invoice_details[$num]['invoice_total_deduction'] = $grn_acc_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_total_deduction'] = $grn_acc_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_total_deduction'] = $grn_acc_entries[$i]['difference_val'];
                    $invoice_details[$num]['total_deduction_voucher_id'] = $grn_acc_entries[$i]['voucher_id'];
                    $invoice_details[$num]['total_deduction_ledger_type'] = $grn_acc_entries[$i]['ledger_type'];
                    $narration['narration_total_deduction'] = $grn_acc_entries[$i]['narration'];
                }
            }

            $grn_details['isNewRecord']=0;

            $sql = "select A.gi_go_invoice_id, A.invoice_no, A.invoice_date, B.grn_id, B.vendor_id, 
                    C.edited_val as total_deduction from goods_inward_outward_invoices A 
                    left join grn B on (A.gi_go_ref_no = B.gi_id) left join grn_acc_entries C 
                    on (B.grn_id = C.grn_id and A.invoice_no = C.invoice_no) 
                    where B.grn_id = '$id' and B.status = 'approved' and B.is_active = '1' and 
                    C.status = 'pending' and C.is_active = '1' and C.particular = 'Total Deduction' and 
                    C.edited_val>0";
            $command = Yii::$app->db->createCommand($sql);
            $reader = $command->query();
            $debit_note = $reader->readAll();

        } else {
            $invoice_details = $model->getInvoiceDetails($id);
            // $invoice_tax = $model->getInvoiceTax($id);
            $invoice_tax = $model->getInvoiceTaxDetails($id);

            for($i=0; $i<count($invoice_details); $i++) {
                $series = 2;
                $sql = "select * from series_master where type = 'Voucher'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $data = $reader->readAll();
                if (count($data)>0){
                    $series = intval($data[0]['series']) + 2;

                    $sql = "update series_master set series = '$series' where type = 'Voucher'";
                    $command = Yii::$app->db->createCommand($sql);
                    $count = $command->execute();
                } else {
                    $series = 2;

                    $sql = "insert into series_master (type, series) values ('Voucher', '".$series."')";
                    $command = Yii::$app->db->createCommand($sql);
                    $count = $command->execute();
                }

                // $code = $code . str_pad($series, 4, "0", STR_PAD_LEFT);

                $invoice_details[$i]['total_amount_voucher_id'] = $series-1;
                $invoice_details[$i]['total_amount_ledger_type'] = 'Main Entry';
                $invoice_details[$i]['total_deduction_voucher_id'] = $series;
                $invoice_details[$i]['total_deduction_ledger_type'] = 'Main Entry';
            }

            $acc['other_charges_acc_id'] = "";
            $acc['other_charges_ledger_name'] = "";
            $acc['other_charges_ledger_code'] = "";
            // $acc['other_charges_voucher_id'] = "";
            // $acc['other_charges_ledger_type'] = "";
            $acc['total_amount_acc_id'] = "";
            $acc['total_amount_ledger_name'] = "";
            $acc['total_amount_ledger_code'] = "";
            // $acc['total_amount_voucher_id'] = "";
            // $acc['total_amount_ledger_type'] = "";
            $acc['total_deduction_acc_id'] = "";
            $acc['total_deduction_ledger_name'] = "";
            $acc['total_deduction_ledger_code'] = "";
            // $acc['total_deduction_voucher_id'] = "";
            // $acc['total_deduction_ledger_type'] = "";

            $narration['narration_taxable_amount'] = "";
            $narration['narration_total_tax'] = "";
            $narration['narration_other_charges'] = "";
            $narration['narration_total_amount'] = "";
            $narration['narration_shortage_amount'] = "";
            $narration['narration_expiry_amount'] = "";
            $narration['narration_damaged_amount'] = "";
            $narration['narration_margin_diff_amount'] = "";
            $narration['narration_total_deduction'] = "";

            for($i=0; $i<count($total_tax); $i++){
                $narration[$i]['cost'] = "";
                $narration[$i]['tax'] = "";
            }

            $grn_details['isNewRecord']=1;
            $debit_note = array();
        }

        $deductions['shortage'] = $this->actionGetinvoicedeductiondetails($id, "shortage");
        $deductions['expiry'] = $this->actionGetinvoicedeductiondetails($id, "expiry");
        $deductions['damaged'] = $this->actionGetinvoicedeductiondetails($id, "damaged");
        $deductions['margin_diff'] = $this->actionGetinvoicedeductiondetails($id, "margin_diff");

        if (count($grn_details)>0) {
            return $this->render('update', ['grn_details' => $grn_details, 'total_val' => $total_val, 'total_tax' => $total_tax, 
                                'invoice_details' => $invoice_details, 'invoice_tax' => $invoice_tax, 'narration' => $narration, 
                                'deductions' => $deductions, 'acc_master' => $acc_master, 'acc' => $acc, 'debit_note' => $debit_note]);
        }

        // echo json_encode($invoice_details);
        // echo json_encode($total_tax);
        // echo json_encode($invoice_tax);
    }



    public function actionLedger($id)
    {
        //http://localhost/primarc_pecan/web/index.php?r=pendinggrn%2Fledger&id=4
        // echo "Hii";
        // $model = $this->findModel($id);
        // $id = 4;

        $model = new PendingGrn();

        $grn_acc_ledger_entries = $model->getGrnAccLedgerEntries($id);
        $grn_details = $model->getGrnDetails($id);

        return $this->render('ledger', ['grn_details' => $grn_details, 'grn_acc_ledger_entries' => $grn_acc_ledger_entries]);
    }

    public function actionGetledger(){
        $model = new PendingGrn();
        $mycomponent = Yii::$app->mycomponent;

        $data = $model->getGrnParticulars();
        $grn_acc_ledger_entries = $data['ledgerArray'];

        $rows = ""; $new_invoice_no = ""; $invoice_no = ""; $debit_amt=0; $credit_amt=0; $sr_no=1;
        $total_debit_amt=0; $total_credit_amt=0; 
        $table_arr = array(); $table_cnt = 0;

        for($i=0; $i<count($grn_acc_ledger_entries); $i++) {
            $rows = $rows . '<tr>
                                <td>' . ($sr_no++) . '</td>
                                <td>' . $grn_acc_ledger_entries[$i]["voucher_id"] . '</td>
                                <td>' . $grn_acc_ledger_entries[$i]["ledger_name"] . '</td>
                                <td>' . $grn_acc_ledger_entries[$i]["ledger_code"] . '</td>';

            if($grn_acc_ledger_entries[$i]["type"]=="Debit") {
                $debit_amt = $debit_amt + $grn_acc_ledger_entries[$i]["amount"];
                $total_debit_amt = $total_debit_amt + $grn_acc_ledger_entries[$i]["amount"];
                $rows = $rows . '<td class="text-right">'.$mycomponent->format_money($grn_acc_ledger_entries[$i]["amount"],2).'</td>';
            } else {
                $rows = $rows . '<td></td>';
            }

            if($grn_acc_ledger_entries[$i]["type"]=="Credit") {
                $credit_amt = $credit_amt + $grn_acc_ledger_entries[$i]["amount"];
                $total_credit_amt = $total_credit_amt + $grn_acc_ledger_entries[$i]["amount"];
                $rows = $rows . '<td class="text-right">'.$mycomponent->format_money($grn_acc_ledger_entries[$i]["amount"],2).'</td>';
            } else {
                $rows = $rows . '<td></td>';
            }

            $rows = $rows . '</tr>';

            if($grn_acc_ledger_entries[$i]["entry_type"]=="Total Amount" || $grn_acc_ledger_entries[$i]["entry_type"]=="Total Deduction"){
                if($grn_acc_ledger_entries[$i]["entry_type"]=="Total Amount"){
                    $particular = "Total Purchase Amount";
                } else {
                    $particular = "Total Deduction Amount";

                    $debit_amt = $debit_amt - ($total_debit_amt*2);
                    $credit_amt = $credit_amt - ($total_credit_amt*2);
                }

                $rows = $rows . '<tr class="bold-text text-right">
                                    <td colspan="4" style="text-align:right;">'.$particular.'</td>
                                    <td class="bold-text text-right">'.$mycomponent->format_money($total_debit_amt,2).'</td>
                                    <td class="bold-text text-right">'.$mycomponent->format_money($total_credit_amt,2).'</td>';
                $rows = $rows . '<tr><td colspan="6"></td></tr>';

                $total_debit_amt = 0;
                $total_credit_amt = 0;
                $sr_no=1;

                if($grn_acc_ledger_entries[$i]["entry_type"]=="Total Amount"){
                    $rows = $rows . '<tr class="bold-text text-right">
                                        <td colspan="6" style="text-align:left;">Deduction Entry</td>
                                    </tr>';
                }
            }

            $blFlag = false;
            if(($i+1)==count($grn_acc_ledger_entries)){
                $blFlag = true;
            } else if($grn_acc_ledger_entries[$i]["invoice_no"]!=$grn_acc_ledger_entries[$i+1]["invoice_no"]){
                $blFlag = true;
            }

            if($blFlag == true){
                $rows = '<tr class="bold-text text-right">
                            <td colspan="6" style="text-align:left;">Purchase Entry</td>
                        </tr>' . $rows;

                $table = '<div class="diversion"><h4 class=" ">Invoice No: ' . $grn_acc_ledger_entries[$i]["invoice_no"] . '</h4>
                        <table class="table table-bordered">
                            <tr class="table-head">
                                <th>Sr. No.</th>
                                <th>Voucher No</th>
                                <th>Ledger Name</th>
                                <th>Ledger Code</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                            ' . $rows . '
                            <tr class="bold-text text-right">
                                <td colspan="4" style="text-align:right;">Total Amount</td>
                                <td>' . $mycomponent->format_money($debit_amt,2) . '</td>
                                <td>' . $mycomponent->format_money($credit_amt,2) . '</td>
                            </tr>
                        </table></div>';

                // echo $table;
                $table_arr[$table_cnt] = $table;
                $table_cnt = $table_cnt + 1;

                $rows=""; $debit_amt=0; $credit_amt=0; $sr_no=1;
            }
        }

        echo json_encode($table_arr);
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $model = new PendingGrn();
        $mycomponent = Yii::$app->mycomponent;

        $gi_id = $request->post('gi_id');
        $invoice_no = $request->post('invoice_no');

        $data = $model->getGrnParticulars();

        // echo json_encode($data['bulkInsertArray']);

        $bulkInsertArray = $data['bulkInsertArray'];
        $grnAccEntries = $data['grnAccEntries'];
        $ledgerArray = $data['ledgerArray'];

        // echo count($bulkInsertArray);
        // echo '<br/>';

        if(count($bulkInsertArray)>0){
            $sql = "delete from grn_acc_entries where grn_id = '$gi_id'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['grn_id','vendor_id','particular','sub_particular','acc_id','ledger_name','ledger_code',
                                'voucher_id','ledger_type','vat_cst','vat_percen','invoice_no','total_val',
                                'invoice_val','edited_val','difference_val','narration','status','is_active',
                                'updated_by','updated_date'];
            // below line insert all your record and return number of rows inserted
            $tableName = "grn_acc_entries";
            $insertCount = Yii::$app->db->createCommand()
                           ->batchInsert(
                                 $tableName, $columnNameArray, $bulkInsertArray
                             )
                           ->execute();

            // echo $insertCount;
            // echo '<br/>';
            // echo 'hii';
        }

        if(count($ledgerArray)>0){
            $sql = "delete from ledger_entries where ref_id = '$gi_id' and ref_type='purchase'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['ref_id','ref_type','entry_type','invoice_no','vendor_id','acc_id','ledger_name','ledger_code',
                                'voucher_id','ledger_type','type','amount','status','is_active','updated_by','updated_date'];
            // below line insert all your record and return number of rows inserted
            $tableName = "ledger_entries";
            $insertCount = Yii::$app->db->createCommand()
                           ->batchInsert(
                                 $tableName, $columnNameArray, $ledgerArray
                             )
                           ->execute();

            // echo $insertCount;
            // echo '<br/>';
            // echo 'hii';
        }

        // $this->actionSaveskudetails($gi_id, $request, "shortage");
        // $this->actionSaveskudetails($gi_id, $request, "expiry");
        // $this->actionSaveskudetails($gi_id, $request, "damaged");
        // $this->actionSaveskudetails($gi_id, $request, "margin_diff");

        if(count($grnAccEntries)>0){
            $sql = "delete from grn_acc_sku_entries where grn_id = '$gi_id'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['grn_id','vendor_id','ded_type','cost_acc_id','cost_ledger_name','cost_ledger_code','tax_acc_id','tax_ledger_name','tax_ledger_code','invoice_no','state',
                                'vat_cst','vat_percen','ean','psku','product_title','qty','box_price','cost_excl_vat_per_unit',
                                'tax_per_unit','total_per_unit','cost_excl_vat','tax','total','expiry_date','earliest_expected_date',
                                'status','is_active'];
            // below line insert all your record and return number of rows inserted
            $tableName = "grn_acc_sku_entries";
            $insertCount = Yii::$app->db->createCommand()
                           ->batchInsert(
                                $tableName, $columnNameArray, $grnAccEntries
                            )
                           ->execute();

            // echo $insertCount;
            // echo '<br/>';
            // echo 'hii';
        }

        $this->redirect(array('pendinggrn/ledger', 'id'=>$gi_id));
    }


    // public function actionSaveskudetails($gi_id, $request, $ded_type){
    //     $invoice_no = $request->post($ded_type.'_invoice_no');
    //     $state = $request->post($ded_type.'_state');
    //     $vat_cst = $request->post($ded_type.'_vat_cst');
    //     $vat_percen = $request->post($ded_type.'_vat_percen');
    //     $ean = $request->post($ded_type.'_ean');
    //     $psku = $request->post($ded_type.'_psku');
    //     $product_title = $request->post($ded_type.'_product_title');
    //     $qty = $request->post($ded_type.'_qty');
    //     $box_price = $request->post($ded_type.'_box_price');
    //     $cost_excl_tax_per_unit = $request->post($ded_type.'_cost_excl_tax_per_unit');
    //     $tax_per_unit = $request->post($ded_type.'_tax_per_unit');
    //     $total_per_unit = $request->post($ded_type.'_total_per_unit');
    //     $cost_excl_tax = $request->post($ded_type.'_cost_excl_tax');
    //     $tax = $request->post($ded_type.'_tax');
    //     $total = $request->post($ded_type.'_total');

    //     $bulkInsertArray = array();
    //     $mycomponent = Yii::$app->mycomponent;
    //     for($i=0; $i<count($invoice_no); $i++){
    //         $qty_val = $mycomponent->format_number($qty[$i],2);
    //         if($qty_val>0){
    //             $bulkInsertArray[$i]=[
    //                 'grn_id'=>$gi_id,
    //                 'ded_type'=>$ded_type,
    //                 'invoice_no'=>$invoice_no[$i],
    //                 'state'=>$state[$i],
    //                 'vat_cst'=>$vat_cst[$i],
    //                 'vat_percen'=>$vat_percen[$i],
    //                 'ean'=>$ean[$i],
    //                 'psku'=>$psku[$i],
    //                 'product_title'=>$product_title[$i],
    //                 'qty'=>$qty_val,
    //                 'box_price'=>$mycomponent->format_number($box_price[$i],2),
    //                 'cost_excl_vat_per_unit'=>$mycomponent->format_number($cost_excl_tax_per_unit[$i],2),
    //                 'tax_per_unit'=>$mycomponent->format_number($tax_per_unit[$i],2),
    //                 'total_per_unit'=>$mycomponent->format_number($total_per_unit[$i],2),
    //                 'cost_excl_vat'=>$mycomponent->format_number($cost_excl_tax[$i],2),
    //                 'tax'=>$mycomponent->format_number($tax[$i],2),
    //                 'total'=>$mycomponent->format_number($total[$i],2),
    //                 'status'=>'pending',
    //                 'is_active'=>'1'
    //             ];
    //         }
    //     }

    //     // echo count($bulkInsertArray);
    //     // echo '<br/>';

    //     if(count($bulkInsertArray)>0){
    //         $sql = "delete from grn_acc_sku_entries where grn_id = '$gi_id' and ded_type = '$ded_type'";
    //         Yii::$app->db->createCommand($sql)->execute();

    //         $columnNameArray=['grn_id','ded_type','invoice_no','state','vat_cst','vat_percen','ean','psku','product_title','qty','box_price','cost_excl_vat_per_unit','tax_per_unit','total_per_unit','cost_excl_vat','tax','total','status','is_active'];
    //         // below line insert all your record and return number of rows inserted
    //         $tableName = "grn_acc_sku_entries";
    //         $insertCount = Yii::$app->db->createCommand()
    //                        ->batchInsert(
    //                             $tableName, $columnNameArray, $bulkInsertArray
    //                         )
    //                        ->execute();

    //         // echo $insertCount;
    //         // echo '<br/>';
    //         // echo 'hii';
    //     }
    // }

    public function actionPendinggrn()
    {
        $model = new PendingGrn();
        $rows = $model->getPendingGrn();
        
        if (count($rows)>0) {
            // echo $rows[0]->grn_id;

            return $this->render('entry-confirm', ['model' => $rows]);
        } else {
            // either the page is initially displayed or there is some validation error
            return $this->render('entry', ['model' => $model]);
        }
    }

    public function actionGetinvoicedeductiondetailstest(){
        $this->actionGetinvoicedeductiondetails('28', 'shortage');
    }

    public function actionGetinvoicedeductiondetails($gi_id, $ded_type)
    {   
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        // $gi_id = $request->post('gi_id');
        // $ded_type = $request->post('ded_type');

        // $gi_id = 5386;
        // $ded_type = "Shortage";

        // $gi_id = 5824;
        // $ded_type = "Shortage";

        // $gi_id = 4;
        // $ded_type = "Shortage";

        // $col_qty = "invoice_qty";

        $expiry_style = 'display: none;';
        $margin_diff_style = 'display: none;';

        if($ded_type=="shortage"){
            $col_qty = "shortage_qty";
        } else if($ded_type=="expiry"){
            $col_qty = "expiry_qty";
            $expiry_style = '';
        } else if($ded_type=="damaged"){
            $col_qty = "damaged_qty";
        } else if($ded_type=="margin_diff"){
            $col_qty = "mrp_issue_qty";
            $margin_diff_style = '';
        }

        // if($col_qty==""){   
        //     $gi_id = 4;
        //     $col_qty = "shortage_qty";
        // }

        $model = new PendingGrn();
        $rows = array();
        $account_master = new AccountMaster();
        $acc_master = $account_master->getAccountDetails('', 'pending');

        $grnAccSku = $model->getGrnAccSku($gi_id);
        if(count($grnAccSku)>0){
            $rows = $model->getGrnAccSkuEntries($gi_id, $ded_type);
        } else {
            $rows = $model->getInvoiceDeductionDetails($gi_id, $col_qty);
        }
        
        $result = "";
        $table = "";
        $invoice_no = "";
        $new_invoice_no = "";
        $invoice_total = 0;
        $grand_total = 0;
        $sr_no = 1;
        // $sr_no_val = 1;

        if (count($rows)>0) {
            // $prev_invoice_no = $rows[0]["invoice_no"];

            for($i=0; $i<count($rows); $i++){
                $invoice_no = $rows[$i]["invoice_no"];

                $voucher_id = '';
                $ledger_type = 'Sub Entry';

                // for($k=0; $k<count($invoice_details); $k++) { 
                //     if($invoice_details[$k]['invoice_no']==$invoice_no) {
                //         $voucher_id = $invoice_details[$k]['total_deduction_voucher_id'];
                //     }
                // }

                $data = $model->getGrnSkues($gi_id);
                $sku_list = '<option value="">Select</option>';
                for($k=0; $k<count($data); $k++){
                    if($rows[$i]["psku"]==$data[$k]['psku']) {
                        $sku_list = $sku_list . '<option value="'.$data[$k]['psku'].'" selected>'.$data[$k]['psku'].'</option>';
                    } else {
                        $sku_list = $sku_list . '<option value="'.$data[$k]['psku'].'">'.$data[$k]['psku'].'</option>'; 
                    }
                }

                $data = $model->getGrnInvoices($gi_id);
                $invoice_list = '<option value="">Select</option>';
                for($k=0; $k<count($data); $k++){
                    if($rows[$i]["invoice_no"]==$data[$k]['invoice_no']) {
                        $invoice_list = $invoice_list . '<option value="'.$data[$k]['invoice_no'].'" selected>'.$data[$k]['invoice_no'].'</option>';
                    } else {
                        $invoice_list = $invoice_list . '<option value="'.$data[$k]['invoice_no'].'">'.$data[$k]['invoice_no'].'</option>'; 
                    }
                }

                $cost_acc_list = '<option value="">Select</option>';
                for($k=0; $k<count($acc_master); $k++){
                    if($rows[$i]["cost_acc_id"]==$acc_master[$k]['id']) {
                        $cost_acc_list = $cost_acc_list . '<option value="'.$acc_master[$k]['id'].'" selected>'.$acc_master[$k]['legal_name'].'</option>';
                    } else {
                        $cost_acc_list = $cost_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>'; 
                    }
                }

                $tax_acc_list = '<option value="">Select</option>';
                for($k=0; $k<count($acc_master); $k++){
                    if($rows[$i]["tax_acc_id"]==$acc_master[$k]['id']) {
                        $tax_acc_list = $tax_acc_list . '<option value="'.$acc_master[$k]['id'].'" selected>'.$acc_master[$k]['legal_name'].'</option>';
                    } else {
                        $tax_acc_list = $tax_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>'; 
                    }
                }

                if(isset($rows[$i][$col_qty])){
                    $qty = $rows[$i][$col_qty];
                } else {
                    $qty = 0;
                }
                
                $state = $rows[$i]["tax_zone_code"];
                $vat_cst = $rows[$i]["vat_cst"];
                $vat_percen = floatval($rows[$i]["vat_percen"]);
                $cost_excl_tax_per_unit = 0;
                if(count($grnAccSku)>0){
                    $cost_excl_tax_per_unit = floatval($rows[$i]["cost_excl_vat_per_unit"]);
                } else {
                    $cost_excl_tax_per_unit = floatval($rows[$i]["cost_excl_vat"]);
                }
                
                $tax_per_unit = ($cost_excl_tax_per_unit*$vat_percen)/100;
                $total_per_unit = $cost_excl_tax_per_unit + $tax_per_unit;
                $cost_excl_tax = $qty*$cost_excl_tax_per_unit;
                $tax = $qty*$tax_per_unit;
                $total = $cost_excl_tax + $tax;
                $invoice_total = $invoice_total + $total;
                $grand_total = $grand_total + $total;

                $row = '<tr>
                            <td>' . $sr_no . '</td>
                            <td>
                                <select class="'.$ded_type.'_psku_'.$sr_no.'" id="'.$ded_type.'_psku_'.$i.'" name="'.$ded_type.'_psku[]" onChange="get_sku_details(this)">' . $sku_list . '</select>
                            </td>
                            <td><input type="text" class="'.$ded_type.'_product_title_'.$sr_no.'" id="'.$ded_type.'_product_title_'.$i.'" name="'.$ded_type.'_product_title[]" value="'.$rows[$i]["product_title"].'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_ean_'.$sr_no.'" id="'.$ded_type.'_ean_'.$i.'" name="'.$ded_type.'_ean[]" value="'.$rows[$i]["ean"].'" readonly /></td>
                            <td>
                                <select id="'.$ded_type.'cost_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_cost_acc_id[]" onChange="get_acc_details(this)">'.$cost_acc_list.'</select>
                                <input type="hidden" id="'.$ded_type.'cost_ledger_name_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_name[]" value="'.$rows[$i]["cost_ledger_name"].'" />
                                <input type="hidden" id="'.$ded_type.'cost_voucher_id_'.$sr_no.'" name="'.$ded_type.'_cost_voucher_id[]" value="'.$voucher_id.'" />
                                <input type="hidden" id="'.$ded_type.'cost_ledger_type_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_type[]" value="'.$ledger_type.'" />
                            </td>
                            <td><input type="text" id="'.$ded_type.'cost_ledger_code_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_code[]" value="'.$rows[$i]["cost_ledger_code"].'" readonly /></td>
                            <td>
                                <select id="'.$ded_type.'tax_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_tax_acc_id[]" onChange="get_acc_details(this)">'.$tax_acc_list.'</select>
                                <input type="hidden" id="'.$ded_type.'tax_ledger_name_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_name[]" value="'.$rows[$i]["tax_ledger_name"].'" />
                                <input type="hidden" id="'.$ded_type.'tax_voucher_id_'.$sr_no.'" name="'.$ded_type.'_tax_voucher_id[]" value="'.$voucher_id.'" />
                                <input type="hidden" id="'.$ded_type.'tax_ledger_type_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_type[]" value="'.$ledger_type.'" />
                            </td>
                            <td><input type="text" id="'.$ded_type.'tax_ledger_code_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_code[]" value="'.$rows[$i]["tax_ledger_code"].'" readonly /></td>
                            <td>
                                <select class="'.$ded_type.'_invoice_no_'.$sr_no.'" id="'.$ded_type.'_invoice_no_'.$i.'" name="'.$ded_type.'_invoice_no[]" onChange="set_sku_details(this)">' . $invoice_list . '</select>
                            </td>
                            <td>'.$rows[$i]["invoice_date"].'</td>
                            <td><input type="text" class="'.$ded_type.'_state_'.$sr_no.'" id="'.$ded_type.'_state_'.$i.'" name="'.$ded_type.'_state[]" value="'.$state.'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_vat_cst_'.$sr_no.'" id="'.$ded_type.'_vat_cst_'.$i.'" name="'.$ded_type.'_vat_cst[]" value="'.$vat_cst.'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_vat_percen_'.$sr_no.'" id="'.$ded_type.'_vat_percen_'.$i.'" name="'.$ded_type.'_vat_percen[]" value="'.$mycomponent->format_money($vat_percen,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_qty_'.$sr_no.'" id="'.$ded_type.'_qty_'.$i.'" name="'.$ded_type.'_qty[]" value="' . $mycomponent->format_money($qty,2) . '" onChange="set_sku_details(this)" /></td>
                            <td><input type="text" class="'.$ded_type.'_box_price_'.$sr_no.'" id="'.$ded_type.'_box_price_'.$i.'" name="'.$ded_type.'_box_price[]" value="'.$mycomponent->format_money($rows[$i]["box_price"],2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_cost_excl_tax_per_unit_'.$sr_no.'" id="'.$ded_type.'_cost_excl_tax_per_unit_'.$i.'" name="'.$ded_type.'_cost_excl_tax_per_unit[]" value="'.$mycomponent->format_money($cost_excl_tax_per_unit,2).'" onChange="set_sku_details(this)" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_tax_per_unit_'.$sr_no.'" id="'.$ded_type.'_tax_per_unit_'.$i.'" name="'.$ded_type.'_tax_per_unit[]" value="'.$mycomponent->format_money($tax_per_unit,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_total_per_unit_'.$sr_no.'" id="'.$ded_type.'_total_per_unit_'.$i.'" name="'.$ded_type.'_total_per_unit[]" value="'.$mycomponent->format_money($total_per_unit,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_cost_excl_tax_'.$sr_no.'" id="'.$ded_type.'_cost_excl_tax_'.$i.'" name="'.$ded_type.'_cost_excl_tax[]" value="'.$mycomponent->format_money($cost_excl_tax,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_tax_'.$sr_no.'" id="'.$ded_type.'_tax_'.$i.'" name="'.$ded_type.'_tax[]" value="'.$mycomponent->format_money($tax,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_total_'.$sr_no.'" id="'.$ded_type.'_total_'.$i.'" name="'.$ded_type.'_total[]" value="'.$mycomponent->format_money($total,2).'" readonly /></td>
                            <td style="'.$expiry_style.'"><input type="text" class="'.$ded_type.'_expiry_date_'.$sr_no.'" id="'.$ded_type.'_expiry_date_'.$i.'" name="'.$ded_type.'_expiry_date[]" value="'.$rows[$i]["expiry_date"].'" readonly /></td>
                            <td style="'.$expiry_style.'"><input type="text" class="'.$ded_type.'_earliest_expected_date_'.$sr_no.'" id="'.$ded_type.'_earliest_expected_date_'.$i.'" name="'.$ded_type.'_earliest_expected_date[]" value="'.$rows[$i]["earliest_expected_date"].'" readonly /></td>
                            <td style="'.$margin_diff_style.'"></td>
                            <td style="'.$margin_diff_style.'"></td>
                            <td></td>
                        </tr>';

                // $sr_no_val = "";
                $result = $result . $row;

                // if($i==count($rows)-1){
                //     $new_invoice_no = "";
                // } else {
                //     $new_invoice_no = $rows[$i+1]["invoice_no"];
                // }

                // if($new_invoice_no!=$invoice_no){
                //     $row = '<tr>
                //                 <td><button type="button" class="btn btn-success repeat" id="repeat_'.$sr_no.'">+</button></td>
                //                 <td></td>
                //                 <td>Invoice Total</td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td id="'.$ded_type.'_invoice_total_'.$sr_no.'">' . $mycomponent->format_money($invoice_total,2) . '</td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //                 <td></td>
                //             </tr>';

                //     $result = $result . $row;
                //     $invoice_total = 0;
                //     $sr_no = $sr_no + 1;
                //     $sr_no_val = $sr_no;
                // }

                $sr_no = $sr_no + 1;
            }
        }

        $row = '<tr id="grand_total_row">
                    <td>
                        <input type="hidden" name="'.$ded_type.'_total_rows" id="'.$ded_type.'_total_rows" value="'.$sr_no.'" />
                        <button type="button" class="btn btn-success" id="'.$ded_type.'_repeat_sku" onClick="add_sku_details(this)">+</button>
                    </td>
                    <td>GRN Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td id="'.$ded_type.'_grand_total">' . $mycomponent->format_money($grand_total,2) . '</td>
                    <td style="'.$expiry_style.'"></td>
                    <td style="'.$expiry_style.'"></td>
                    <td style="'.$margin_diff_style.'"></td>
                    <td style="'.$margin_diff_style.'"></td>
                    <td></td>
                </tr>';

        $result = $result . $row;

        $table = '<table class="table table-bordered" id="'.$ded_type.'_sku_details">
                    <thead>
                        <tr>
                            <th colspan="4">SKU Details</th>
                            <th colspan="4">Account Details</th>
                            <th colspan="2">Invoice Details</th>
                            <th colspan="3">Purchase Ledger</th>
                            <th colspan="2">Quantity Deducted</th>
                            <th colspan="3">Amount Deducted (Per Unit)</th>
                            <th colspan="3">Amount Deducted (Total)</th>
                            <th colspan="2" style="'.$expiry_style.'">For Expiry Only</th>
                            <th colspan="2" style="'.$margin_diff_style.'">For Margin Difference (Per Unit)</th>
                            <th rowspan="2">Remarks</th>
                        </tr>
                        <tr>
                            <th>Sr No</th>
                            <th>SKU Code</th>
                            <th>SKU Name</th>
                            <th>EAN Code</th>
                            <th>Cost Ledger Name</th>
                            <th>Cost Ledger Code</th>
                            <th>Tax Ledger Name</th>
                            <th>Tax Ledger Code</th>
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Purchase State</th>
                            <th>Tax</th>
                            <th>Tax Rate</th>
                            <th>Quantity</th>
                            <th>MRP</th>
                            <th>Cost Excl Tax</th>
                            <th>Tax</th>
                            <th>Total</th>
                            <th>Cost Excl Tax</th>
                            <th>Tax</th>
                            <th>Total</th>
                            <th style="'.$expiry_style.'">Date Received</th>
                            <th style="'.$expiry_style.'">Earliest Expected Date</th>
                            <th style="'.$margin_diff_style.'">Difference in Cost Excl Tax</th>
                            <th style="'.$margin_diff_style.'">Difference in Tax</th>
                        </tr>
                    </thead>
                    <tbody id="deduction_data">
                        '.$result.'
                    </tbody>
                </table>';

        // echo json_encode($rows);
        // echo $result;
        return $table;
        // echo $table;
    }

    public function actionGetnewrow()
    {   
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $gi_id = $request->post('grn_id');
        $ded_type = $request->post('ded_type');
        $sr_no = $request->post('sr_no');

        // $gi_id = 28;
        // $ded_type = "shortage";
        // $sr_no = 2;

        // $gi_id = 5386;
        // $ded_type = "Shortage";

        // $gi_id = 5824;
        // $ded_type = "Shortage";

        // $gi_id = 4;
        // $ded_type = "Shortage";

        // $col_qty = "invoice_qty";

        $expiry_style = 'display: none;';
        $margin_diff_style = 'display: none;';

        if($ded_type=="shortage"){
            $col_qty = "shortage_qty";
        } else if($ded_type=="expiry"){
            $col_qty = "expiry_qty";
            $expiry_style = '';
        } else if($ded_type=="damaged"){
            $col_qty = "damaged_qty";
        } else if($ded_type=="margin_diff"){
            $col_qty = "mrp_issue_qty";
            $margin_diff_style = '';
        }

        // if($col_qty==""){   
        //     $gi_id = 4;
        //     $col_qty = "shortage_qty";
        // }

        $model = new PendingGrn();
        // $rows = array();
        // $rows = $model->getInvoiceDeductionDetails($gi_id, $col_qty);
        $account_master = new AccountMaster();
        $acc_master = $account_master->getAccountDetails('', 'pending');
        
        $result = "";
        $table = "";
        $invoice_no = "";
        $new_invoice_no = "";
        $invoice_total = 0;
        $grand_total = 0;
        // $sr_no = 1;
        // $sr_no_val = 1;

        $data = $model->getGrnSkues($gi_id);
        $sku_list = '<option value="">Select</option>';
        for($k=0; $k<count($data); $k++){
            $sku_list = $sku_list . '<option value="'.$data[$k]['psku'].'">'.$data[$k]['psku'].'</option>';
        }

        $data = $model->getGrnInvoices($gi_id);
        $invoice_list = '<option value="">Select</option>';
        for($k=0; $k<count($data); $k++){
            $invoice_list = $invoice_list . '<option value="'.$data[$k]['invoice_no'].'">'.$data[$k]['invoice_no'].'</option>';
        }

        $acc_list = '<option value="">Select</option>';
        for($k=0; $k<count($acc_master); $k++){
            $acc_list = $acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>';
        }

        $invoice_no = "";

        $qty = 0;
        $state = "";
        $vat_cst = "";
        $vat_percen = 0;
        $cost_excl_tax_per_unit = 0;
        
        $tax_per_unit = ($cost_excl_tax_per_unit*$vat_percen)/100;
        $total_per_unit = $cost_excl_tax_per_unit + $tax_per_unit;
        $cost_excl_tax = $qty*$cost_excl_tax_per_unit;
        $tax = $qty*$tax_per_unit;
        $total = $cost_excl_tax + $tax;
        $invoice_total = $invoice_total + $total;
        $grand_total = $grand_total + $total;

        $i = $sr_no - 1;

        $row = '<tr>
                    <td>' . $sr_no . '</td>
                    <td>
                        <select class="'.$ded_type.'_psku_'.$sr_no.'" id="'.$ded_type.'_psku_'.$i.'" name="'.$ded_type.'_psku[]" onChange="get_sku_details(this)">' . $sku_list . '</select>
                    </td>
                    <td><input type="text" class="'.$ded_type.'_product_title_'.$sr_no.'" id="'.$ded_type.'_product_title_'.$i.'" name="'.$ded_type.'_product_title[]" value="" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_ean_'.$sr_no.'" id="'.$ded_type.'_ean_'.$i.'" name="'.$ded_type.'_ean[]" value="" readonly /></td>
                    <td>
                        <select id="'.$ded_type.'cost_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_cost_acc_id[]" onChange="get_acc_details(this)">'.$acc_list.'</select>
                        <input type="hidden" id="'.$ded_type.'cost_ledger_name_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_name[]" value="" />
                    </td>
                    <td><input type="text" id="'.$ded_type.'cost_ledger_code_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_code[]" value="" readonly /></td>
                    <td>
                        <select id="'.$ded_type.'tax_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_tax_acc_id[]" onChange="get_acc_details(this)">'.$acc_list.'</select>
                        <input type="hidden" id="'.$ded_type.'tax_ledger_name_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_name[]" value="" />
                    </td>
                    <td><input type="text" id="'.$ded_type.'tax_ledger_code_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_code[]" value="" readonly /></td>
                    <td>
                        <select class="'.$ded_type.'_invoice_no_'.$sr_no.'" id="'.$ded_type.'_invoice_no_'.$i.'" name="'.$ded_type.'_invoice_no[]" onChange="set_sku_details(this)">' . $invoice_list . '</select>
                    </td>
                    <td></td>
                    <td><input type="text" class="'.$ded_type.'_state_'.$sr_no.'" id="'.$ded_type.'_state_'.$i.'" name="'.$ded_type.'_state[]" value="'.$state.'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_vat_cst_'.$sr_no.'" id="'.$ded_type.'_vat_cst_'.$i.'" name="'.$ded_type.'_vat_cst[]" value="'.$vat_cst.'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_vat_percen_'.$sr_no.'" id="'.$ded_type.'_vat_percen_'.$i.'" name="'.$ded_type.'_vat_percen[]" value="'.$mycomponent->format_money($vat_percen,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_qty_'.$sr_no.'" id="'.$ded_type.'_qty_'.$i.'" name="'.$ded_type.'_qty[]" value="' . $mycomponent->format_money($qty,2) . '" onChange="set_sku_details(this);" /></td>
                    <td><input type="text" class="'.$ded_type.'_box_price_'.$sr_no.'" id="'.$ded_type.'_box_price_'.$i.'" name="'.$ded_type.'_box_price[]" value="" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_cost_excl_tax_per_unit_'.$sr_no.'" id="'.$ded_type.'_cost_excl_tax_per_unit_'.$i.'" name="'.$ded_type.'_cost_excl_tax_per_unit[]" value="'.$mycomponent->format_money($cost_excl_tax_per_unit,2).'" onChange="set_sku_details(this);" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_tax_per_unit_'.$sr_no.'" id="'.$ded_type.'_tax_per_unit_'.$i.'" name="'.$ded_type.'_tax_per_unit[]" value="'.$mycomponent->format_money($tax_per_unit,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_total_per_unit_'.$sr_no.'" id="'.$ded_type.'_total_per_unit_'.$i.'" name="'.$ded_type.'_total_per_unit[]" value="'.$mycomponent->format_money($total_per_unit,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_cost_excl_tax_'.$sr_no.'" id="'.$ded_type.'_cost_excl_tax_'.$i.'" name="'.$ded_type.'_cost_excl_tax[]" value="'.$mycomponent->format_money($cost_excl_tax,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_tax_'.$sr_no.'" id="'.$ded_type.'_tax_'.$i.'" name="'.$ded_type.'_tax[]" value="'.$mycomponent->format_money($tax,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_total_'.$sr_no.'" id="'.$ded_type.'_total_'.$i.'" name="'.$ded_type.'_total[]" value="'.$mycomponent->format_money($total,2).'" readonly /></td>
                    <td style="'.$expiry_style.'"><input type="text" class="'.$ded_type.'_expiry_date_'.$sr_no.'" id="'.$ded_type.'_expiry_date_'.$i.'" name="'.$ded_type.'_expiry_date[]" value="" readonly /></td>
                    <td style="'.$expiry_style.'"><input type="text" class="'.$ded_type.'_earliest_expected_date_'.$sr_no.'" id="'.$ded_type.'_earliest_expected_date_'.$i.'" name="'.$ded_type.'_earliest_expected_date[]" value="" readonly /></td>
                    <td style="'.$margin_diff_style.'"></td>
                    <td style="'.$margin_diff_style.'"></td>
                    <td></td>
                </tr>';

        // $sr_no_val = "";
        $result = $result . $row;

        // if($i==count($rows)-1){
        //     $new_invoice_no = "";
        // } else {
        //     $new_invoice_no = $rows[$i+1]["invoice_no"];
        // }

        // if($new_invoice_no!=$invoice_no){
        //     $row = '<tr>
        //                 <td><button type="button" class="btn btn-success repeat" id="repeat_'.$sr_no.'">+</button></td>
        //                 <td></td>
        //                 <td>Invoice Total</td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td id="'.$ded_type.'_invoice_total_'.$sr_no.'">' . $mycomponent->format_money($invoice_total,2) . '</td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //                 <td></td>
        //             </tr>';

        //     $result = $result . $row;
        //     $invoice_total = 0;
        //     $sr_no = $sr_no + 1;
        //     $sr_no_val = $sr_no;
        // }

        // $row = '<tr>
        //             <td><button type="button" class="btn btn-success repeat_inv" id="repeat_sku">+</button></td>
        //             <td><input type="hidden" name="total_rows" id="total_rows" value="'.$sr_no.'" /></td>
        //             <td>GRN Total</td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td id="'.$ded_type.'_grand_total">' . $mycomponent->format_money($grand_total,2) . '</td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //         </tr>';

        // $result = $result . $row;

        // $table = '<table class="table table-bordered">
        //             <thead>
        //                 <tr>
        //                     <th colspan="6">SKU Details</th>
        //                     <th colspan="2">Invoice Details</th>
        //                     <th colspan="3">Purchase Ledger</th>
        //                     <th colspan="2">Quantity Deducted</th>
        //                     <th colspan="3">Amount Deducted (Per Unit)</th>
        //                     <th colspan="3">Amount Deducted (Total)</th>
        //                     <th colspan="2">For Expiry Only</th>
        //                     <th colspan="2">For Margin Difference (Per Unit)</th>
        //                     <th rowspan="2">Remarks</th>
        //                 </tr>
        //                 <tr>
        //                     <th>Action</th>
        //                     <th>Sr No</th>
        //                     <th>SKU Code</th>
        //                     <th>SKU Name</th>
        //                     <th>EAN Code</th>
        //                     <th>Reason</th>
        //                     <th>Invoice Number</th>
        //                     <th>Invoice Date</th>
        //                     <th>Purchase State</th>
        //                     <th>Tax Rate</th>
        //                     <th>Ledger Name</th>
        //                     <th>Quantity</th>
        //                     <th>MRP</th>
        //                     <th>Cost Excl Tax</th>
        //                     <th>Tax</th>
        //                     <th>Total</th>
        //                     <th>Cost Excl Tax</th>
        //                     <th>Tax</th>
        //                     <th>Total</th>
        //                     <th>Date Received</th>
        //                     <th>Earliest Expected Date</th>
        //                     <th>Difference in Cost Excl Tax</th>
        //                     <th>Difference in Tax</th>
        //                 </tr>
        //             </thead>
        //             <tbody id="deduction_data">
        //                 '.$result.'
        //             </tbody>
        //         </table>';

        // echo json_encode($rows);
        echo $result;
        // echo $table;
    }

    public function actionGetskudetails(){
        $grn = new PendingGrn();
        $data = $grn->getSkuDetails();
        echo json_encode($data);
    }

    public function actionGetaccdetails(){
        $acc_master = new AccountMaster();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        $data = $acc_master->getAccountDetails($acc_id);
        echo json_encode($data);
    }

    public function actionGetgrndetails(){
        $grn = new PendingGrn();
        $grn = $grn->getNewGrnDetails();
        // $result = "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
        $result = "";

        for($i=0; $i<count($grn); $i++){
            $result = $result . '<tr> 
                <td scope="row">'.($i+1).'</td> 
                <td>'.$grn[$i]["gi_id"].'</td> 
                <td>'.$grn[$i]["location"].'</td> 
                <td>'.$grn[$i]["vendor_name"].'</td> 
                <td>'.$grn[$i]["scanned_qty"].'</td> 
                <td>'.$grn[$i]["payable_val_after_tax"].'</td> 
                <td>'.$grn[$i]["gi_date"].'</td> 
                <td>'.$grn[$i]["status"].'</td> 
                <td><a href="' . Url::base() .'index.php?r=pendinggrn%2Fupdate&id='.$grn[$i]['grn_id'].'"> Post </a></td> 
            </tr>';
        }

        // return $result;
        echo json_encode($grn);
    }

    public function actionGetpendinggrndetails(){
        $grn = new PendingGrn();
        $grn = $grn->getPendingGrnDetails();
        $result = "";

        for($i=0; $i<count($grn); $i++){
            $result = $result . '<tr> 
                <td scope="row">'.($i+1).'</td> 
                <td>'.$grn[$i]["gi_id"].'</td> 
                <td>'.$grn[$i]["location"].'</td> 
                <td>'.$grn[$i]["vendor_name"].'</td> 
                <td>'.$grn[$i]["scanned_qty"].'</td> 
                <td>'.$grn[$i]["payable_val_after_tax"].'</td> 
                <td>'.$grn[$i]["gi_date"].'</td> 
                <td>'.$grn[$i]["status"].'</td> 
                <td><a href="' . Url::base() .'index.php?r=pendinggrn%2Fupdate&id='.$grn[$i]['grn_id'].'"> Post </a></td> 
            </tr>';
        }

        echo $result;
    }

    public function actionGetgrnparticulars(){
        $grn = new PendingGrn();
        $data = $grn->getGrnParticulars();

        echo json_encode($data);
    }
}
