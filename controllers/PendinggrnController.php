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

class PendinggrnController extends Controller
{
    public function actionIndex(){
        $grn_cnt = new PendingGrn();
        $grn = $grn_cnt->getNewGrnDetails();
        $pending = $grn_cnt->getPurchaseDetails('pending');
        $approved = $grn_cnt->getPurchaseDetails('approved');
        $all = $grn_cnt->getAllGrnDetails();
        return $this->render('pending_grn', [
            'grn' => $grn, 'pending' => $pending, 'approved' => $approved, 'all' => $all
        ]);
    }

    // public function actionGetdebitnote(){
    //     $invoice_id = '11266';

    //     $model = new PendingGrn();
    //     $data = $model->getDebitNoteDetails($invoice_id);
        
    //     $this->layout = false;
    //     return $this->render('debit_note', [
    //         'invoice_details' => $data['invoice_details'], 'debit_note' => $data['debit_note'], 
    //         'deduction_details' => $data['deduction_details'], 'vendor_details' => $data['vendor_details']
    //     ]);
    // }

    public function actionViewdebitnote($invoice_id){
        $model = new PendingGrn();
        $data = $model->getDebitNoteDetails($invoice_id);
        
        $this->layout = false;
        return $this->render('debit_note', [
            'invoice_details' => $data['invoice_details'], 'debit_note' => $data['debit_note'], 
            'deduction_details' => $data['deduction_details'], 'vendor_details' => $data['vendor_details'], 
            'grn_details' => $data['grn_details']
        ]);
    }

    public function actionDownload($invoice_id){
        $model = new PendingGrn();
        $data = $model->getDebitNoteDetails($invoice_id);
        $file = "";

        if(isset($data['debit_note'])){
            if(count($data['debit_note'])>0){
                $debit_note = $data['debit_note'];
                $file = $debit_note[0]['debit_note_path'];
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
            'deduction_details' => $data['deduction_details'], 'vendor_details' => $data['vendor_details'], 
            'grn_details' => $data['grn_details']
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
            $email_sent_status = '1';
            $error_message = '';
        } else {
            $data['response'] = 'Mail Sending Failed.';
            $email_sent_status = '0';
            $error_message = $response;
        }
        $data['id'] = $id;
        $data['grn_id'] = $grn_id;
        $data['invoice_id'] = $invoice_id;

        
        $attachment_type = 'PDF';
        $vendor_name = $request->post('vendor_name');
        $company_id = $request->post('company_id');
        $model = new PendingGrn();
        $model->setEmailLog($vendor_name, $from, $to, $id, $body, $attachment, 
                                $attachment_type, $email_sent_status, $error_message, $company_id);

        return $this->render('email_response', ['data' => $data]);
    }

    public function actionView($id) {
        $model = new PendingGrn();
        $access = $model->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $model->setLog('PendingGrn', '', 'View', '', 'View GRN Details', 'acc_grn_entries', $id);
                return $this->actionRedirect('view', $id);
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        } else {
            $this->layout = 'other';
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Session Expired'),
                'module' => $this->module,
                'msg' => 'Session Expired. Please <a href="'.Url::base().'index.php">Login</a> again.'
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = new PendingGrn();
        $access = $model->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $model->setLog('PendingGrn', '', 'Edit', '', 'Edit GRN Details', 'acc_grn_entries', $id);
                return $this->actionRedirect('edit', $id);
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        } else {
            $this->layout = 'other';
            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Session Expired'),
                'module' => $this->module,
                'msg' => 'Session Expired. Please <a href="'.Url::base().'index.php">Login</a> again.'
            ]);
        }
    }

    public function actionRedirect($action, $id){
        $model = new PendingGrn();

        $grn_entries = $model->getGrnAccEntries($id);
        $grn_details = $model->getGrnDetails($id);
        $total_val = $model->getTotalValue($id);
        $total_tax = $model->getTotalTax($id);

        $acc_master = $model->getAccountDetails('', 'approved');

        $tax_zone_code = $grn_details[0]['vat_cst'];

        if (count($grn_entries) > 0){
            // echo json_encode($grn_entries);

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

            for($i=0; $i<count($grn_entries); $i++){
                $invoice_no = $grn_entries[$i]['invoice_no'];

                if($prev_invoice_no != $invoice_no){
                    $num = $num + 1;
                    $invoice_details[$num] = array();
                    // array_push($invoice_details[$num], array('invoice_no'=>$invoice_no));
                    // $invoice_details[] = array('invoice_no'=>$invoice_no);
                    $invoice_details[$num]['invoice_no'] = $invoice_no;
                    $prev_invoice_no = $invoice_no;
                    // $tax_num = 0;
                }
                
                // if($grn_entries[$i]['particular']=="Taxable Amount"){
                //     $invoice_details[$num]['invoice_total_cost'] = $grn_entries[$i]['invoice_val'];
                //     $invoice_details[$num]['edited_total_cost'] = $grn_entries[$i]['edited_val'];
                //     $invoice_details[$num]['diff_total_cost'] = $grn_entries[$i]['difference_val'];
                //     $narration['narration_taxable_amount'] = $grn_entries[$i]['narration'];
                // }

                // if($grn_entries[$i]['particular']=="Tax"){
                //     $invoice_details[$num]['invoice_total_tax'] = $grn_entries[$i]['invoice_val'];
                //     $invoice_details[$num]['edited_total_tax'] = $grn_entries[$i]['edited_val'];
                //     $invoice_details[$num]['diff_total_tax'] = $grn_entries[$i]['difference_val'];
                //     $narration['narration_total_tax'] = $grn_entries[$i]['narration'];
                // }

                if($grn_entries[$i]['particular']=="Taxable Amount" || $grn_entries[$i]['particular']=="Tax" || 
                   $grn_entries[$i]['particular']=="CGST" || $grn_entries[$i]['particular']=="SGST" || 
                   $grn_entries[$i]['particular']=="IGST"){
                    $blFlag = false;

                    // if($grn_entries[$i]['particular']=="Tax"){
                    //     $blFlag = true;
                    //     $invoice_tax[$tax_num]['invoice_tax_acc_id'] = $grn_entries[$i]['acc_id'];
                    //     $invoice_tax[$tax_num]['invoice_tax_ledger_name'] = $grn_entries[$i]['ledger_name'];
                    //     $invoice_tax[$tax_num]['invoice_tax_ledger_code'] = $grn_entries[$i]['ledger_code'];
                    //     $invoice_tax[$tax_num]['invoice_tax'] = $grn_entries[$i]['invoice_val'];
                    //     $invoice_tax[$tax_num]['edited_tax'] = $grn_entries[$i]['edited_val'];
                    //     $invoice_tax[$tax_num]['diff_tax'] = $grn_entries[$i]['difference_val'];
                    //     $narration[$tax_num]['tax'] = $grn_entries[$i]['narration'];
                    // }

                    if($grn_entries[$i]['particular']=="Taxable Amount" || $grn_entries[$i]['particular']=="Tax" || 
                       $grn_entries[$i]['particular']=="CGST" || $grn_entries[$i]['particular']=="SGST" || 
                       $grn_entries[$i]['particular']=="IGST"){
                        for($k=0; $k<count($invoice_tax); $k++){
                            if($invoice_tax[$k]['invoice_no']==$grn_entries[$i]['invoice_no'] && 
                                $invoice_tax[$k]['vat_cst']==$grn_entries[$i]['vat_cst'] && 
                                $invoice_tax[$k]['vat_percen']==$grn_entries[$i]['vat_percen']){
                                $blFlag = true;
                                if($grn_entries[$i]['particular']=="Taxable Amount"){
                                    $invoice_tax[$k]['invoice_cost_acc_id'] = $grn_entries[$i]['acc_id'];
                                    $invoice_tax[$k]['invoice_cost_ledger_name'] = $grn_entries[$i]['ledger_name'];
                                    $invoice_tax[$k]['invoice_cost_ledger_code'] = $grn_entries[$i]['ledger_code'];
                                    // $invoice_tax[$k]['invoice_cost_voucher_id'] = $grn_entries[$i]['voucher_id'];
                                    // $invoice_tax[$k]['invoice_cost_ledger_type'] = $grn_entries[$i]['ledger_type'];
                                    $invoice_tax[$k]['invoice_cost'] = $grn_entries[$i]['invoice_val'];
                                    $invoice_tax[$k]['edited_cost'] = $grn_entries[$i]['edited_val'];
                                    $invoice_tax[$k]['diff_cost'] = $grn_entries[$i]['difference_val'];
                                    $narration[$k]['cost'] = $grn_entries[$i]['narration'];
                                } else if($grn_entries[$i]['particular']=="Tax"){
                                    $invoice_tax[$k]['invoice_tax_acc_id'] = $grn_entries[$i]['acc_id'];
                                    $invoice_tax[$k]['invoice_tax_ledger_name'] = $grn_entries[$i]['ledger_name'];
                                    $invoice_tax[$k]['invoice_tax_ledger_code'] = $grn_entries[$i]['ledger_code'];
                                    // $invoice_tax[$k]['invoice_tax_voucher_id'] = $grn_entries[$i]['voucher_id'];
                                    // $invoice_tax[$k]['invoice_tax_ledger_type'] = $grn_entries[$i]['ledger_type'];
                                    $invoice_tax[$k]['invoice_tax'] = $grn_entries[$i]['invoice_val'];
                                    $invoice_tax[$k]['edited_tax'] = $grn_entries[$i]['edited_val'];
                                    $invoice_tax[$k]['diff_tax'] = $grn_entries[$i]['difference_val'];
                                    $narration[$k]['tax'] = $grn_entries[$i]['narration'];
                                } else if($grn_entries[$i]['particular']=="CGST"){
                                    $invoice_tax[$k]['invoice_cgst_acc_id'] = $grn_entries[$i]['acc_id'];
                                    $invoice_tax[$k]['invoice_cgst_ledger_name'] = $grn_entries[$i]['ledger_name'];
                                    $invoice_tax[$k]['invoice_cgst_ledger_code'] = $grn_entries[$i]['ledger_code'];
                                    // $invoice_tax[$k]['invoice_cgst_voucher_id'] = $grn_entries[$i]['voucher_id'];
                                    // $invoice_tax[$k]['invoice_cgst_ledger_type'] = $grn_entries[$i]['ledger_type'];
                                    $invoice_tax[$k]['invoice_cgst'] = $grn_entries[$i]['invoice_val'];
                                    $invoice_tax[$k]['edited_cgst'] = $grn_entries[$i]['edited_val'];
                                    $invoice_tax[$k]['diff_cgst'] = $grn_entries[$i]['difference_val'];
                                    $narration[$k]['cgst'] = $grn_entries[$i]['narration'];
                                } else if($grn_entries[$i]['particular']=="SGST"){
                                    $invoice_tax[$k]['invoice_sgst_acc_id'] = $grn_entries[$i]['acc_id'];
                                    $invoice_tax[$k]['invoice_sgst_ledger_name'] = $grn_entries[$i]['ledger_name'];
                                    $invoice_tax[$k]['invoice_sgst_ledger_code'] = $grn_entries[$i]['ledger_code'];
                                    // $invoice_tax[$k]['invoice_sgst_voucher_id'] = $grn_entries[$i]['voucher_id'];
                                    // $invoice_tax[$k]['invoice_sgst_ledger_type'] = $grn_entries[$i]['ledger_type'];
                                    $invoice_tax[$k]['invoice_sgst'] = $grn_entries[$i]['invoice_val'];
                                    $invoice_tax[$k]['edited_sgst'] = $grn_entries[$i]['edited_val'];
                                    $invoice_tax[$k]['diff_sgst'] = $grn_entries[$i]['difference_val'];
                                    $narration[$k]['sgst'] = $grn_entries[$i]['narration'];
                                } else if($grn_entries[$i]['particular']=="IGST"){
                                    $invoice_tax[$k]['invoice_igst_acc_id'] = $grn_entries[$i]['acc_id'];
                                    $invoice_tax[$k]['invoice_igst_ledger_name'] = $grn_entries[$i]['ledger_name'];
                                    $invoice_tax[$k]['invoice_igst_ledger_code'] = $grn_entries[$i]['ledger_code'];
                                    // $invoice_tax[$k]['invoice_igst_voucher_id'] = $grn_entries[$i]['voucher_id'];
                                    // $invoice_tax[$k]['invoice_igst_ledger_type'] = $grn_entries[$i]['ledger_type'];
                                    $invoice_tax[$k]['invoice_igst'] = $grn_entries[$i]['invoice_val'];
                                    $invoice_tax[$k]['edited_igst'] = $grn_entries[$i]['edited_val'];
                                    $invoice_tax[$k]['diff_igst'] = $grn_entries[$i]['difference_val'];
                                    $narration[$k]['igst'] = $grn_entries[$i]['narration'];
                                }

                                // echo json_encode($invoice_tax);
                                // echo '<br/>';
                            }
                        }
                    }
                    
                    if($blFlag==false){
                        $invoice_tax[$tax_num]['particular'] = $grn_entries[$i]['particular'];
                        $invoice_tax[$tax_num]['tax_zone_code'] = $grn_entries[$i]['tax_zone_code'];
                        $invoice_tax[$tax_num]['invoice_no'] = $grn_entries[$i]['invoice_no'];
                        $invoice_tax[$tax_num]['sub_particular_cost'] = $grn_entries[$i]['sub_particular'];
                        $invoice_tax[$tax_num]['vat_cst'] = $grn_entries[$i]['vat_cst'];
                        $invoice_tax[$tax_num]['vat_percen'] = $grn_entries[$i]['vat_percen'];

                        if($grn_entries[$i]['particular']=="Taxable Amount"){
                            $invoice_tax[$tax_num]['invoice_cost_acc_id'] = $grn_entries[$i]['acc_id'];
                            $invoice_tax[$tax_num]['invoice_cost_ledger_name'] = $grn_entries[$i]['ledger_name'];
                            $invoice_tax[$tax_num]['invoice_cost_ledger_code'] = $grn_entries[$i]['ledger_code'];
                            // $invoice_tax[$tax_num]['invoice_cost_voucher_id'] = $grn_entries[$i]['voucher_id'];
                            // $invoice_tax[$tax_num]['invoice_cost_ledger_type'] = $grn_entries[$i]['ledger_type'];
                            $invoice_tax[$tax_num]['invoice_cost'] = $grn_entries[$i]['invoice_val'];
                            $invoice_tax[$tax_num]['edited_cost'] = $grn_entries[$i]['edited_val'];
                            $invoice_tax[$tax_num]['diff_cost'] = $grn_entries[$i]['difference_val'];
                            $narration[$tax_num]['cost'] = $grn_entries[$i]['narration'];
                        } else if($grn_entries[$i]['particular']=="Tax"){
                            $invoice_tax[$tax_num]['invoice_tax_acc_id'] = $grn_entries[$i]['acc_id'];
                            $invoice_tax[$tax_num]['invoice_tax_ledger_name'] = $grn_entries[$i]['ledger_name'];
                            $invoice_tax[$tax_num]['invoice_tax_ledger_code'] = $grn_entries[$i]['ledger_code'];
                            // $invoice_tax[$tax_num]['invoice_tax_voucher_id'] = $grn_entries[$i]['voucher_id'];
                            // $invoice_tax[$tax_num]['invoice_tax_ledger_type'] = $grn_entries[$i]['ledger_type'];
                            $invoice_tax[$tax_num]['invoice_tax'] = $grn_entries[$i]['invoice_val'];
                            $invoice_tax[$tax_num]['edited_tax'] = $grn_entries[$i]['edited_val'];
                            $invoice_tax[$tax_num]['diff_tax'] = $grn_entries[$i]['difference_val'];
                            $narration[$tax_num]['tax'] = $grn_entries[$i]['narration'];
                        } else if($grn_entries[$i]['particular']=="CGST"){
                            $invoice_tax[$tax_num]['invoice_cgst_acc_id'] = $grn_entries[$i]['acc_id'];
                            $invoice_tax[$tax_num]['invoice_cgst_ledger_name'] = $grn_entries[$i]['ledger_name'];
                            $invoice_tax[$tax_num]['invoice_cgst_ledger_code'] = $grn_entries[$i]['ledger_code'];
                            // $invoice_tax[$tax_num]['invoice_cgst_voucher_id'] = $grn_entries[$i]['voucher_id'];
                            // $invoice_tax[$tax_num]['invoice_cgst_ledger_type'] = $grn_entries[$i]['ledger_type'];
                            $invoice_tax[$tax_num]['invoice_cgst'] = $grn_entries[$i]['invoice_val'];
                            $invoice_tax[$tax_num]['edited_cgst'] = $grn_entries[$i]['edited_val'];
                            $invoice_tax[$tax_num]['diff_cgst'] = $grn_entries[$i]['difference_val'];
                            $narration[$tax_num]['cgst'] = $grn_entries[$i]['narration'];
                        } else if($grn_entries[$i]['particular']=="SGST"){
                            $invoice_tax[$tax_num]['invoice_sgst_acc_id'] = $grn_entries[$i]['acc_id'];
                            $invoice_tax[$tax_num]['invoice_sgst_ledger_name'] = $grn_entries[$i]['ledger_name'];
                            $invoice_tax[$tax_num]['invoice_sgst_ledger_code'] = $grn_entries[$i]['ledger_code'];
                            // $invoice_tax[$tax_num]['invoice_sgst_voucher_id'] = $grn_entries[$i]['voucher_id'];
                            // $invoice_tax[$tax_num]['invoice_sgst_ledger_type'] = $grn_entries[$i]['ledger_type'];
                            $invoice_tax[$tax_num]['invoice_sgst'] = $grn_entries[$i]['invoice_val'];
                            $invoice_tax[$tax_num]['edited_sgst'] = $grn_entries[$i]['edited_val'];
                            $invoice_tax[$tax_num]['diff_sgst'] = $grn_entries[$i]['difference_val'];
                            $narration[$tax_num]['sgst'] = $grn_entries[$i]['narration'];
                        } else if($grn_entries[$i]['particular']=="IGST"){
                            $invoice_tax[$tax_num]['invoice_igst_acc_id'] = $grn_entries[$i]['acc_id'];
                            $invoice_tax[$tax_num]['invoice_igst_ledger_name'] = $grn_entries[$i]['ledger_name'];
                            $invoice_tax[$tax_num]['invoice_igst_ledger_code'] = $grn_entries[$i]['ledger_code'];
                            // $invoice_tax[$tax_num]['invoice_igst_voucher_id'] = $grn_entries[$i]['voucher_id'];
                            // $invoice_tax[$tax_num]['invoice_igst_ledger_type'] = $grn_entries[$i]['ledger_type'];
                            $invoice_tax[$tax_num]['invoice_igst'] = $grn_entries[$i]['invoice_val'];
                            $invoice_tax[$tax_num]['edited_igst'] = $grn_entries[$i]['edited_val'];
                            $invoice_tax[$tax_num]['diff_igst'] = $grn_entries[$i]['difference_val'];
                            $narration[$tax_num]['igst'] = $grn_entries[$i]['narration'];
                        }
                        
                        $tax_num = $tax_num + 1;
                        // echo json_encode($invoice_tax);
                        // echo '<br/>';
                    }
                }

                // if($grn_entries[$i]['particular']=="Tax"){
                //     $invoice_tax[$tax_num]['tax_zone_code'] = $grn_entries[$i]['tax_zone_code'];
                //     $invoice_tax[$tax_num]['invoice_no'] = $grn_entries[$i]['invoice_no'];
                //     $invoice_tax[$tax_num]['sub_particular'] = $grn_entries[$i]['sub_particular'];
                //     $invoice_tax[$tax_num]['vat_cst'] = $grn_entries[$i]['vat_cst'];
                //     $invoice_tax[$tax_num]['vat_percen'] = $grn_entries[$i]['vat_percen'];
                //     $invoice_tax[$tax_num]['invoice_tax'] = $grn_entries[$i]['invoice_val'];
                //     $invoice_tax[$tax_num]['edited_tax'] = $grn_entries[$i]['edited_val'];
                //     $invoice_tax[$tax_num]['diff_tax'] = $grn_entries[$i]['difference_val'];
                //     $narration[$tax_num] = $grn_entries[$i]['narration'];
                //     $tax_num = $tax_num + 1;
                // }

                if($grn_entries[$i]['particular']=="Other Charges"){
                    $acc['other_charges_acc_id'] = $grn_entries[$i]['acc_id'];
                    $acc['other_charges_ledger_name'] = $grn_entries[$i]['ledger_name'];
                    $acc['other_charges_ledger_code'] = $grn_entries[$i]['ledger_code'];
                    // $acc['other_charges_voucher_id'] = $grn_entries[$i]['voucher_id'];
                    // $acc['other_charges_ledger_type'] = $grn_entries[$i]['ledger_type'];
                    $invoice_details[$num]['invoice_other_charges'] = $grn_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_other_charges'] = $grn_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_other_charges'] = $grn_entries[$i]['difference_val'];
                    $narration['narration_other_charges'] = $grn_entries[$i]['narration'];
                }

                if($grn_entries[$i]['particular']=="Total Amount"){
                    $acc['total_amount_acc_id'] = $grn_entries[$i]['acc_id'];
                    $acc['total_amount_ledger_name'] = $grn_entries[$i]['ledger_name'];
                    $acc['total_amount_ledger_code'] = $grn_entries[$i]['ledger_code'];
                    $invoice_details[$num]['invoice_total_amount'] = $grn_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_total_amount'] = $grn_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_total_amount'] = $grn_entries[$i]['difference_val'];
                    $invoice_details[$num]['total_amount_voucher_id'] = $grn_entries[$i]['voucher_id'];
                    $invoice_details[$num]['total_amount_ledger_type'] = $grn_entries[$i]['ledger_type'];
                    $narration['narration_total_amount'] = $grn_entries[$i]['narration'];
                }

                if($grn_entries[$i]['particular']=="Shortage Amount"){
                    $invoice_details[$num]['invoice_shortage_amount'] = $grn_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_shortage_amount'] = $grn_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_shortage_amount'] = $grn_entries[$i]['difference_val'];
                    $narration['narration_shortage_amount'] = $grn_entries[$i]['narration'];
                }

                if($grn_entries[$i]['particular']=="Expiry Amount"){
                    $invoice_details[$num]['invoice_expiry_amount'] = $grn_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_expiry_amount'] = $grn_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_expiry_amount'] = $grn_entries[$i]['difference_val'];
                    $narration['narration_expiry_amount'] = $grn_entries[$i]['narration'];
                }

                if($grn_entries[$i]['particular']=="Damaged Amount"){
                    $invoice_details[$num]['invoice_damaged_amount'] = $grn_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_damaged_amount'] = $grn_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_damaged_amount'] = $grn_entries[$i]['difference_val'];
                    $narration['narration_damaged_amount'] = $grn_entries[$i]['narration'];
                }

                if($grn_entries[$i]['particular']=="Margin Diff Amount"){
                    $invoice_details[$num]['invoice_margindiff_amount'] = $grn_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_margindiff_amount'] = $grn_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_margindiff_amount'] = $grn_entries[$i]['difference_val'];
                    $narration['narration_margindiff_amount'] = $grn_entries[$i]['narration'];
                }

                if($grn_entries[$i]['particular']=="Total Deduction"){
                    $acc['total_deduction_acc_id'] = $grn_entries[$i]['acc_id'];
                    $acc['total_deduction_ledger_name'] = $grn_entries[$i]['ledger_name'];
                    $acc['total_deduction_ledger_code'] = $grn_entries[$i]['ledger_code'];
                    $invoice_details[$num]['invoice_total_deduction'] = $grn_entries[$i]['invoice_val'];
                    $invoice_details[$num]['edited_total_deduction'] = $grn_entries[$i]['edited_val'];
                    $invoice_details[$num]['diff_total_deduction'] = $grn_entries[$i]['difference_val'];
                    $invoice_details[$num]['total_deduction_voucher_id'] = $grn_entries[$i]['voucher_id'];
                    $invoice_details[$num]['total_deduction_ledger_type'] = $grn_entries[$i]['ledger_type'];
                    $narration['narration_total_deduction'] = $grn_entries[$i]['narration'];
                }
            }

            $grn_details['isNewRecord']=0;

            $sql = "select A.gi_go_invoice_id, A.invoice_no, A.invoice_date, B.grn_id, B.vendor_id, 
                    C.edited_val as total_deduction from goods_inward_outward_invoices A 
                    left join grn B on (A.gi_go_ref_no = B.gi_id) left join acc_grn_entries C 
                    on (B.grn_id = C.grn_id and A.invoice_no = C.invoice_no) 
                    where B.grn_id = '$id' and B.status = 'approved' and B.is_active = '1' and 
                    C.status = 'approved' and C.is_active = '1' and C.particular = 'Total Deduction' and 
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
                $sql = "select * from acc_series_master where type = 'Voucher'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $data = $reader->readAll();
                if (count($data)>0){
                    $series = intval($data[0]['series']) + 2;

                    $sql = "update acc_series_master set series = '$series' where type = 'Voucher'";
                    $command = Yii::$app->db->createCommand($sql);
                    $count = $command->execute();
                } else {
                    $series = 2;

                    $sql = "insert into acc_series_master (type, series) values ('Voucher', '".$series."')";
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
            $narration['narration_margindiff_amount'] = "";
            $narration['narration_total_deduction'] = "";

            for($i=0; $i<count($total_tax); $i++){
                $narration[$i]['cost'] = "";
                $narration[$i]['tax'] = "";
                $narration[$i]['cgst'] = "";
                $narration[$i]['sgst'] = "";
                $narration[$i]['igst'] = "";
            }

            $grn_details['isNewRecord']=1;
            $debit_note = array();
        }

        $deductions['shortage'] = $this->actionGetinvoicedeductiondetails($id, "shortage", $tax_zone_code);
        $deductions['expiry'] = $this->actionGetinvoicedeductiondetails($id, "expiry", $tax_zone_code);
        $deductions['damaged'] = $this->actionGetinvoicedeductiondetails($id, "damaged", $tax_zone_code);
        $deductions['margindiff'] = $this->actionGetinvoicedeductiondetails($id, "margindiff", $tax_zone_code);

        if (count($grn_details)>0) {
            return $this->render('update', ['grn_details' => $grn_details, 'total_val' => $total_val, 'total_tax' => $total_tax, 
                                'invoice_details' => $invoice_details, 'invoice_tax' => $invoice_tax, 'narration' => $narration, 
                                'deductions' => $deductions, 'acc_master' => $acc_master, 'acc' => $acc, 
                                'debit_note' => $debit_note, 'action' => $action]);
        }

        // echo json_encode($invoice_details);
        // echo json_encode($total_tax);
        // echo json_encode($invoice_tax);
        // echo json_encode($deductions['margindiff']);
        // echo $deductions['shortage'];
    }

    public function actionLedger($id){
        $model = new PendingGrn();

        $acc_ledger_entries = $model->getGrnAccLedgerEntries($id);
        $grn_details = $model->getGrnDetails($id);

        return $this->render('ledger', ['grn_details' => $grn_details, 'acc_ledger_entries' => $acc_ledger_entries]);
    }

    public function actionGetledger(){
        $model = new PendingGrn();
        $mycomponent = Yii::$app->mycomponent;

        $data = $model->getGrnParticulars();
        $acc_ledger_entries = $data['ledgerArray'];

        $rows = ""; $new_invoice_no = ""; $invoice_no = ""; $debit_amt=0; $credit_amt=0; $sr_no=1;
        $total_debit_amt=0; $total_credit_amt=0; 
        $table_arr = array(); $table_cnt = 0;
        $bl_deduction = false;
        $row_deduction = '';

        for($i=0; $i<count($acc_ledger_entries); $i++) {
            if ($bl_deduction==true){
                $rows = $rows . $row_deduction;
                $bl_deduction = false;
            }
            $rows = $rows . '<tr>
                                <td>' . ($sr_no++) . '</td>
                                <td>' . $acc_ledger_entries[$i]["voucher_id"] . '</td>
                                <td>' . $acc_ledger_entries[$i]["ledger_name"] . '</td>
                                <td>' . $acc_ledger_entries[$i]["ledger_code"] . '</td>';

            if($acc_ledger_entries[$i]["type"]=="Debit") {
                $debit_amt = $debit_amt + $acc_ledger_entries[$i]["amount"];
                $total_debit_amt = $total_debit_amt + $acc_ledger_entries[$i]["amount"];
                $rows = $rows . '<td class="text-right">'.$mycomponent->format_money($acc_ledger_entries[$i]["amount"],2).'</td>';
            } else {
                $rows = $rows . '<td></td>';
            }

            if($acc_ledger_entries[$i]["type"]=="Credit") {
                $credit_amt = $credit_amt + $acc_ledger_entries[$i]["amount"];
                $total_credit_amt = $total_credit_amt + $acc_ledger_entries[$i]["amount"];
                $rows = $rows . '<td class="text-right">'.$mycomponent->format_money($acc_ledger_entries[$i]["amount"],2).'</td>';
            } else {
                $rows = $rows . '<td></td>';
            }

            $rows = $rows . '</tr>';

            if($acc_ledger_entries[$i]["entry_type"]=="Total Amount" || $acc_ledger_entries[$i]["entry_type"]=="Total Deduction"){
                if($acc_ledger_entries[$i]["entry_type"]=="Total Amount"){
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

                if($acc_ledger_entries[$i]["entry_type"]=="Total Amount"){
                    // $rows = $rows . '<tr class="bold-text text-right">
                    //                     <td colspan="6" style="text-align:left;">Deduction Entry</td>
                    //                 </tr>';
                    $row_deduction = '<tr class="bold-text text-right">
                                        <td colspan="6" style="text-align:left;">Deduction Entry</td>
                                    </tr>';

                    $bl_deduction = true;
                }
            }

            $blFlag = false;
            if(($i+1)==count($acc_ledger_entries)){
                $blFlag = true;
            } else if($acc_ledger_entries[$i]["invoice_no"]!=$acc_ledger_entries[$i+1]["invoice_no"]){
                $blFlag = true;
            }

            if($blFlag == true){
                $rows = '<tr class="bold-text text-right">
                            <td colspan="6" style="text-align:left;">Purchase Entry</td>
                        </tr>' . $rows;

                $debit_amt = round($debit_amt,2);
                $credit_amt = round($credit_amt,2);

                $table = '<div class="diversion"><h4 class=" ">Invoice No: ' . $acc_ledger_entries[$i]["invoice_no"] . '</h4>
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

    public function actionSave(){
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

        // echo json_encode($bulkInsertArray);
        // echo '<br/>';
        // echo json_encode($grnAccEntries);
        // echo '<br/>';
        // echo json_encode($ledgerArray);

        // echo count($bulkInsertArray);
        // echo '<br/>';

        if(count($bulkInsertArray)>0){
            $sql = "delete from acc_grn_entries where grn_id = '$gi_id'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['grn_id','vendor_id','particular','sub_particular','acc_id','ledger_name','ledger_code',
                                'voucher_id','ledger_type','vat_cst','vat_percen','invoice_no','total_val',
                                'invoice_val','edited_val','difference_val','narration','status','is_active',
                                'updated_by','updated_date', 'gi_date'];
            // below line insert all your record and return number of rows inserted
            $tableName = "acc_grn_entries";
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
            $sql = "delete from acc_ledger_entries where ref_id = '$gi_id' and ref_type='purchase'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['ref_id','ref_type','entry_type','invoice_no','vendor_id','acc_id','ledger_name','ledger_code',
                                'voucher_id','ledger_type','type','amount','narration','status','is_active',
                                'updated_by','updated_date', 'ref_date'];
            // below line insert all your record and return number of rows inserted
            $tableName = "acc_ledger_entries";
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
        // $this->actionSaveskudetails($gi_id, $request, "margindiff");

        if(count($grnAccEntries)>0){
            $sql = "delete from acc_grn_sku_entries where grn_id = '$gi_id'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['grn_id','vendor_id','ded_type','cost_acc_id','cost_ledger_name','cost_ledger_code',
                                'tax_acc_id','tax_ledger_name','tax_ledger_code','cgst_acc_id','cgst_ledger_name','cgst_ledger_code',
                                'sgst_acc_id','sgst_ledger_name','sgst_ledger_code','igst_acc_id','igst_ledger_name','igst_ledger_code',
                                'invoice_no','state','vat_cst','vat_percen','cgst_rate','sgst_rate','igst_rate','ean','hsn_code','psku',
                                'product_title','qty','box_price','cost_excl_vat_per_unit','tax_per_unit','cgst_per_unit',
                                'sgst_per_unit','igst_per_unit','total_per_unit','cost_excl_vat','tax','cgst','sgst','igst',
                                'total','expiry_date','earliest_expected_date','status','is_active', 'remarks','po_mrp',
                                'po_cost_excl_vat','po_tax','po_cgst','po_sgst','po_igst','po_total','margin_diff_excl_tax',
                                'margin_diff_cgst','margin_diff_sgst','margin_diff_igst','margin_diff_tax','margin_diff_total'];
            // below line insert all your record and return number of rows inserted
            $tableName = "acc_grn_sku_entries";
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

    public function actionPendinggrn(){
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

        $this->actionGetinvoicedeductiondetails('5909', 'shortage', 'INTRA');
    }

    public function actionGetinvoicedeductiondetails($gi_id, $ded_type, $tax_zone_code){   
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

        // $col_qty = "proper_qty";

        $expiry_style = 'display: none;';
        $margindiff_style = 'display: none;';
        $all_style = 'display: none;';

        if($ded_type=="shortage"){
            $col_qty = "shortage_qty";
        } else if($ded_type=="expiry"){
            $col_qty = "expiry_qty";
            $expiry_style = '';
        } else if($ded_type=="damaged"){
            $col_qty = "damaged_qty";
        } else if($ded_type=="margindiff"){
            $col_qty = "margindiff_qty";
            $margindiff_style = '';
        }

        // if($col_qty==""){   
        //     $gi_id = 4;
        //     $col_qty = "shortage_qty";
        // }

        $model = new PendingGrn();
        $rows = array();
        $acc_master = $model->getAccountDetails('', 'approved');

        $grnAccSku = $model->getGrnAccSku($gi_id);
        if(count($grnAccSku)>0){
            $rows = $model->getGrnAccSkuEntries($gi_id, $ded_type);
            // if($ded_type=="margindiff"){
            //     $col_qty = "margindiff_qty";
            // }
        } else {
            // if($col_qty != "mrp_issue_qty"){
            //     $rows = $model->getInvoiceDeductionDetails($gi_id, $col_qty);
            // }

            $rows = $model->getInvoiceDeductionDetails($gi_id, $col_qty);
        }
        
        $result = "";
        $table = "";
        $invoice_no = "";
        $new_invoice_no = "";
        $invoice_total = 0;
        $grand_total = 0;
        $po_grand_total = 0;
        $diff_grand_total = 0;
        $sr_no = 1;
        // $sr_no_val = 1;

        $intra_state_style = "";
        $inter_state_style = "";
        $colspan_no = 10;
        if(strtoupper($tax_zone_code)=="INTRA"){
            $inter_state_style = "display:none;";
            $colspan_no = 6;
        } else {
            $intra_state_style = "display:none;";
            $colspan_no = 4;
        }

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
                $tax_acc_list = '<option value="">Select</option>';
                $cgst_acc_list = '<option value="">Select</option>';
                $sgst_acc_list = '<option value="">Select</option>';
                $igst_acc_list = '<option value="">Select</option>';
                for($k=0; $k<count($acc_master); $k++){
                    if($acc_master[$k]['type']=="Goods Purchase") { 
                        if($rows[$i]["cost_acc_id"]==$acc_master[$k]['id']) {
                            $cost_acc_list = $cost_acc_list . '<option value="'.$acc_master[$k]['id'].'" selected>'.$acc_master[$k]['legal_name'].'</option>';
                        } else {
                            $cost_acc_list = $cost_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>'; 
                        }
                    }
                    if($acc_master[$k]['type']=="Tax") { 
                        if($rows[$i]["tax_acc_id"]==$acc_master[$k]['id']) {
                            $tax_acc_list = $tax_acc_list . '<option value="'.$acc_master[$k]['id'].'" selected>'.$acc_master[$k]['legal_name'].'</option>';
                        } else {
                            $tax_acc_list = $tax_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>'; 
                        }
                    }
                    if($acc_master[$k]['type']=="CGST") { 
                        if($rows[$i]["cgst_acc_id"]==$acc_master[$k]['id']) {
                            $cgst_acc_list = $cgst_acc_list . '<option value="'.$acc_master[$k]['id'].'" selected>'.$acc_master[$k]['legal_name'].'</option>';
                        } else {
                            $cgst_acc_list = $cgst_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>'; 
                        }
                    }
                    if($acc_master[$k]['type']=="SGST") { 
                        if($rows[$i]["sgst_acc_id"]==$acc_master[$k]['id']) {
                            $sgst_acc_list = $sgst_acc_list . '<option value="'.$acc_master[$k]['id'].'" selected>'.$acc_master[$k]['legal_name'].'</option>';
                        } else {
                            $sgst_acc_list = $sgst_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>'; 
                        }
                    }
                    if($acc_master[$k]['type']=="IGST") { 
                        if($rows[$i]["igst_acc_id"]==$acc_master[$k]['id']) {
                            $igst_acc_list = $igst_acc_list . '<option value="'.$acc_master[$k]['id'].'" selected>'.$acc_master[$k]['legal_name'].'</option>';
                        } else {
                            $igst_acc_list = $igst_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>'; 
                        }
                    }
                }

                // echo $col_qty;
                // echo '<br/>';
                // echo $rows[$i][$col_qty];
                // echo '<br/>';

                if(isset($rows[$i][$col_qty])){
                    $qty = $rows[$i][$col_qty];
                } else {
                    $qty = 0;
                }
                
                // echo $qty;
                // echo '<br/>';

                $state = $rows[$i]["tax_zone_code"];
                $vat_cst = $rows[$i]["vat_cst"];
                $vat_percen = floatval($rows[$i]["vat_percen"]);
                $cgst_rate = floatval($rows[$i]["cgst_rate"]);
                $sgst_rate = floatval($rows[$i]["sgst_rate"]);
                $igst_rate = floatval($rows[$i]["igst_rate"]);
                $cost_excl_tax_per_unit = 0;
                if(count($grnAccSku)>0){
                    $cost_excl_tax_per_unit = floatval($rows[$i]["cost_excl_vat_per_unit"]);

                    // $po_cost_excl_tax = floatval($rows[$i]["po_cost_excl_vat"]);
                    // $po_tax = floatval($rows[$i]["po_tax"]);
                    // $po_total = floatval($rows[$i]["po_total"]);

                    $po_total = floatval($rows[$i]["po_total"]);
                } else {
                    $cost_excl_tax_per_unit = floatval($rows[$i]["cost_excl_vat"]);

                    // $po_cost_excl_tax = $qty*floatval($rows[$i]["po_unit_rate_excl_tax"]);
                    // $po_cost_excl_tax = floatval($rows[$i]["po_unit_rate_excl_tax"]);

                    // $po_tax = $qty*floatval($rows[$i]["po_unit_tax"]);
                    // $po_total = $po_cost_excl_tax + $po_tax;

                    $po_total = floatval($rows[$i]["po_unit_rate_incl_tax"]);
                }
                
                $box_price = floatval($rows[$i]["box_price"]);
                $po_mrp = floatval($rows[$i]["po_mrp"]);

                $cgst_per_unit = ($cost_excl_tax_per_unit*$cgst_rate)/100;
                $sgst_per_unit = ($cost_excl_tax_per_unit*$sgst_rate)/100;
                $igst_per_unit = ($cost_excl_tax_per_unit*$igst_rate)/100;
                $tax_per_unit = ($cost_excl_tax_per_unit*$vat_percen)/100;
                $total_per_unit = $cost_excl_tax_per_unit + $tax_per_unit;

                $cost_excl_tax = $qty*$cost_excl_tax_per_unit;
                $cgst = $qty*$cgst_per_unit;
                $sgst = $qty*$sgst_per_unit;
                $igst = $qty*$igst_per_unit;
                $tax = $qty*$tax_per_unit;
                $total = $cost_excl_tax + $tax;
                $invoice_total = $invoice_total + $total;

                // $po_cgst = ($po_cost_excl_tax*$cgst_rate)/100;
                // $po_sgst = ($po_cost_excl_tax*$sgst_rate)/100;
                // $po_igst = ($po_cost_excl_tax*$igst_rate)/100;
                // $po_tax = ($po_cost_excl_tax*$vat_percen)/100;
                // $po_total = $po_cost_excl_tax + $po_tax;

                // $diff_cost_excl_tax = round($cost_excl_tax - $po_cost_excl_tax,2);
                // $diff_cgst = round($cgst - $po_cgst,2);
                // $diff_sgst = round($sgst - $po_sgst,2);
                // $diff_igst = round($igst - $po_igst,2);
                // $diff_tax = round($tax - $po_tax,2);
                // $diff_total = round($total - $po_total,2);

                $po_cost_excl_tax = $po_total/(1+($vat_percen/100));
                $po_tax = round(($po_cost_excl_tax*$vat_percen)/100,2);
                $po_cgst = round(($po_cost_excl_tax*$cgst_rate)/100,2);
                $po_sgst = round(($po_cost_excl_tax*$sgst_rate)/100,2);
                $po_igst = round(($po_cost_excl_tax*$igst_rate)/100,2);
                // $po_tax = $po_cgst+$po_sgst+$po_igst;

                $margin_from_po = floor((($po_mrp-$po_total)/$po_mrp*100)*100)/100;
                $margin_from_scan = floor((($box_price-$total_per_unit)/$box_price*100)*100)/100;

                $diff_cost_excl_tax = round((($margin_from_po-$margin_from_scan)/100*$box_price*$qty)/(1+($vat_percen/100)),2);
                $diff_cgst = ($diff_cost_excl_tax*$cgst_rate)/100;
                $diff_sgst = ($diff_cost_excl_tax*$sgst_rate)/100;
                $diff_igst = ($diff_cost_excl_tax*$igst_rate)/100;
                $diff_tax = ($diff_cost_excl_tax*$vat_percen)/100;
                $diff_total = $diff_cost_excl_tax + $diff_tax;

                $grand_total = $grand_total + $total;
                $po_grand_total = $po_grand_total + $po_total;
                $diff_grand_total = $diff_grand_total + $diff_total;

                $remarks = $rows[$i]["remarks"];

                $row = '<tr id="'.$ded_type.'_row_'.$i.'">
                            <td style="text-align: center;"><button type="button" class="btn btn-sm btn-success" id="'.$ded_type.'_delete_row_'.$i.'" onClick="delete_row(this);">-</button></td>
                            <td style="display: none;">' . $sr_no . '</td>
                            <td>
                                <select class="'.$ded_type.'_psku_'.$sr_no.'" id="'.$ded_type.'_psku_'.$i.'" name="'.$ded_type.'_psku[]" onChange="get_sku_details(this)" data-error="#'.$ded_type.'_psku_'.$i.'_error">' . $sku_list . '</select>
                                <div id="'.$ded_type.'_psku_'.$i.'_error"></div>
                            </td>
                            <td><input type="text" class="'.$ded_type.'_product_title_'.$sr_no.'" id="'.$ded_type.'_product_title_'.$i.'" name="'.$ded_type.'_product_title[]" value="'.$rows[$i]["product_title"].'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_ean_'.$sr_no.'" id="'.$ded_type.'_ean_'.$i.'" name="'.$ded_type.'_ean[]" value="'.$rows[$i]["ean"].'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_hsn_code_'.$sr_no.'" id="'.$ded_type.'_hsn_code_'.$i.'" name="'.$ded_type.'_hsn_code[]" value="'.$rows[$i]["hsn_code"].'" readonly /></td>
                            <td>
                                <select id="'.$ded_type.'cost_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_cost_acc_id[]" onChange="get_acc_details(this)" data-error="#'.$ded_type.'cost_acc_id_'.$sr_no.'_error">'.$cost_acc_list.'</select>
                                <input type="hidden" id="'.$ded_type.'cost_ledger_name_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_name[]" value="'.$rows[$i]["cost_ledger_name"].'" />
                                <input type="hidden" id="'.$ded_type.'cost_voucher_id_'.$sr_no.'" name="'.$ded_type.'_cost_voucher_id[]" value="'.$voucher_id.'" />
                                <input type="hidden" id="'.$ded_type.'cost_ledger_type_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_type[]" value="'.$ledger_type.'" />
                                <div id="'.$ded_type.'cost_acc_id_'.$sr_no.'_error"></div>
                            </td>
                            <td><input type="text" id="'.$ded_type.'cost_ledger_code_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_code[]" value="'.$rows[$i]["cost_ledger_code"].'" readonly /></td>
                            <td style="'.$intra_state_style.'">
                                <select id="'.$ded_type.'cgst_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_cgst_acc_id[]" onChange="get_acc_details(this)" data-error="#'.$ded_type.'cgst_acc_id_'.$sr_no.'_error">'.$cgst_acc_list.'</select>
                                <input type="hidden" id="'.$ded_type.'cgst_ledger_name_'.$sr_no.'" name="'.$ded_type.'_cgst_ledger_name[]" value="'.$rows[$i]["cgst_ledger_name"].'" />
                                <input type="hidden" id="'.$ded_type.'cgst_voucher_id_'.$sr_no.'" name="'.$ded_type.'_cgst_voucher_id[]" value="'.$voucher_id.'" />
                                <input type="hidden" id="'.$ded_type.'cgst_ledger_type_'.$sr_no.'" name="'.$ded_type.'_cgst_ledger_type[]" value="'.$ledger_type.'" />
                                <div id="'.$ded_type.'cgst_acc_id_'.$sr_no.'_error"></div>
                            </td>
                            <td style="'.$intra_state_style.'"><input type="text" id="'.$ded_type.'cgst_ledger_code_'.$sr_no.'" name="'.$ded_type.'_cgst_ledger_code[]" value="'.$rows[$i]["cgst_ledger_code"].'" readonly /></td>
                            <td style="'.$intra_state_style.'">
                                <select id="'.$ded_type.'sgst_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_sgst_acc_id[]" onChange="get_acc_details(this)" data-error="#'.$ded_type.'sgst_acc_id_'.$sr_no.'_error">'.$sgst_acc_list.'</select>
                                <input type="hidden" id="'.$ded_type.'sgst_ledger_name_'.$sr_no.'" name="'.$ded_type.'_sgst_ledger_name[]" value="'.$rows[$i]["sgst_ledger_name"].'" />
                                <input type="hidden" id="'.$ded_type.'sgst_voucher_id_'.$sr_no.'" name="'.$ded_type.'_sgst_voucher_id[]" value="'.$voucher_id.'" />
                                <input type="hidden" id="'.$ded_type.'sgst_ledger_type_'.$sr_no.'" name="'.$ded_type.'_sgst_ledger_type[]" value="'.$ledger_type.'" />
                                <div id="'.$ded_type.'sgst_acc_id_'.$sr_no.'_error"></div>
                            </td>
                            <td style="'.$intra_state_style.'"><input type="text" id="'.$ded_type.'sgst_ledger_code_'.$sr_no.'" name="'.$ded_type.'_sgst_ledger_code[]" value="'.$rows[$i]["sgst_ledger_code"].'" readonly /></td>
                            <td style="'.$inter_state_style.'">
                                <select id="'.$ded_type.'igst_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_igst_acc_id[]" onChange="get_acc_details(this)" data-error="#'.$ded_type.'igst_acc_id_'.$sr_no.'_error">'.$igst_acc_list.'</select>
                                <input type="hidden" id="'.$ded_type.'igst_ledger_name_'.$sr_no.'" name="'.$ded_type.'_igst_ledger_name[]" value="'.$rows[$i]["igst_ledger_name"].'" />
                                <input type="hidden" id="'.$ded_type.'igst_voucher_id_'.$sr_no.'" name="'.$ded_type.'_igst_voucher_id[]" value="'.$voucher_id.'" />
                                <input type="hidden" id="'.$ded_type.'igst_ledger_type_'.$sr_no.'" name="'.$ded_type.'_igst_ledger_type[]" value="'.$ledger_type.'" />
                                <div id="'.$ded_type.'igst_acc_id_'.$sr_no.'_error"></div>
                            </td>
                            <td style="'.$inter_state_style.'"><input type="text" id="'.$ded_type.'igst_ledger_code_'.$sr_no.'" name="'.$ded_type.'_igst_ledger_code[]" value="'.$rows[$i]["igst_ledger_code"].'" readonly /></td>
                            <td style="'.$all_style.'">
                                <select id="'.$ded_type.'tax_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_tax_acc_id[]" onChange="get_acc_details(this)" data-error="#'.$ded_type.'tax_acc_id_'.$sr_no.'_error">'.$tax_acc_list.'</select>
                                <input type="hidden" id="'.$ded_type.'tax_ledger_name_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_name[]" value="'.$rows[$i]["tax_ledger_name"].'" />
                                <input type="hidden" id="'.$ded_type.'tax_voucher_id_'.$sr_no.'" name="'.$ded_type.'_tax_voucher_id[]" value="'.$voucher_id.'" />
                                <input type="hidden" id="'.$ded_type.'tax_ledger_type_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_type[]" value="'.$ledger_type.'" />
                                <div id="'.$ded_type.'tax_acc_id_'.$sr_no.'_error"></div>
                            </td>
                            <td style="'.$all_style.'"><input type="text" id="'.$ded_type.'tax_ledger_code_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_code[]" value="'.$rows[$i]["tax_ledger_code"].'" readonly /></td>
                            <td>
                                <select class="'.$ded_type.'_invoice_no_'.$sr_no.'" id="'.$ded_type.'_invoice_no_'.$i.'" name="'.$ded_type.'_invoice_no[]" onChange="set_sku_details(this)" data-error="'.$ded_type.'_invoice_no_'.$sr_no.'_error">' . $invoice_list . '</select>
                                <div id="'.$ded_type.'_invoice_no_'.$sr_no.'_error"></div>
                            </td>
                            <td id="'.$ded_type.'_invoice_date_'.$i.'">'.$rows[$i]["invoice_date"].'</td>
                            <td><input type="text" class="'.$ded_type.'_state_'.$sr_no.'" id="'.$ded_type.'_state_'.$i.'" name="'.$ded_type.'_state[]" value="'.$state.'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_vat_cst_'.$sr_no.'" id="'.$ded_type.'_vat_cst_'.$i.'" name="'.$ded_type.'_vat_cst[]" value="'.$vat_cst.'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_cgst_rate_'.$sr_no.'" id="'.$ded_type.'_cgst_rate_'.$i.'" name="'.$ded_type.'_cgst_rate[]" value="'.$cgst_rate.'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_sgst_rate_'.$sr_no.'" id="'.$ded_type.'_sgst_rate_'.$i.'" name="'.$ded_type.'_sgst_rate[]" value="'.$sgst_rate.'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_igst_rate_'.$sr_no.'" id="'.$ded_type.'_igst_rate_'.$i.'" name="'.$ded_type.'_igst_rate[]" value="'.$igst_rate.'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_vat_percen_'.$sr_no.'" id="'.$ded_type.'_vat_percen_'.$i.'" name="'.$ded_type.'_vat_percen[]" value="'.$vat_percen.'" readonly /></td>
                            <td>
                                <input type="text" class="'.$ded_type.'_qty_'.$sr_no.' edit-sku" id="'.$ded_type.'_qty_'.$i.'" name="'.$ded_type.'_qty[]" value="' . $mycomponent->format_money($qty,2) . '" onChange="set_sku_details(this)" data-error="#'.$ded_type.'qty_'.$i.'_error" '.(($ded_type=="margindiff")?"readonly ":" ").'/>
                                <div id="'.$ded_type.'qty_'.$i.'_error"></div>
                            </td>
                            <td><input type="text" class="'.$ded_type.'_box_price_'.$sr_no.'" id="'.$ded_type.'_box_price_'.$i.'" name="'.$ded_type.'_box_price[]" value="'.$mycomponent->format_money($rows[$i]["box_price"],2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_cost_excl_tax_per_unit_'.$sr_no.'" id="'.$ded_type.'_cost_excl_tax_per_unit_'.$i.'" name="'.$ded_type.'_cost_excl_tax_per_unit[]" value="'.$mycomponent->format_money($cost_excl_tax_per_unit,2).'" onChange="set_sku_details(this)" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_cgst_per_unit_'.$sr_no.'" id="'.$ded_type.'_cgst_per_unit_'.$i.'" name="'.$ded_type.'_cgst_per_unit[]" value="'.$mycomponent->format_money($cgst_per_unit,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_sgst_per_unit_'.$sr_no.'" id="'.$ded_type.'_sgst_per_unit_'.$i.'" name="'.$ded_type.'_sgst_per_unit[]" value="'.$mycomponent->format_money($sgst_per_unit,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_igst_per_unit_'.$sr_no.'" id="'.$ded_type.'_igst_per_unit_'.$i.'" name="'.$ded_type.'_igst_per_unit[]" value="'.$mycomponent->format_money($igst_per_unit,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_tax_per_unit_'.$sr_no.'" id="'.$ded_type.'_tax_per_unit_'.$i.'" name="'.$ded_type.'_tax_per_unit[]" value="'.$mycomponent->format_money($tax_per_unit,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_total_per_unit_'.$sr_no.'" id="'.$ded_type.'_total_per_unit_'.$i.'" name="'.$ded_type.'_total_per_unit[]" value="'.$mycomponent->format_money($total_per_unit,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_cost_excl_tax_'.$sr_no.'" id="'.$ded_type.'_cost_excl_tax_'.$i.'" name="'.$ded_type.'_cost_excl_tax[]" value="'.$mycomponent->format_money($cost_excl_tax,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_cgst_'.$sr_no.'" id="'.$ded_type.'_cgst_'.$i.'" name="'.$ded_type.'_cgst[]" value="'.$mycomponent->format_money($cgst,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_sgst_'.$sr_no.'" id="'.$ded_type.'_sgst_'.$i.'" name="'.$ded_type.'_sgst[]" value="'.$mycomponent->format_money($sgst,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_igst_'.$sr_no.'" id="'.$ded_type.'_igst_'.$i.'" name="'.$ded_type.'_igst[]" value="'.$mycomponent->format_money($igst,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_tax_'.$sr_no.'" id="'.$ded_type.'_tax_'.$i.'" name="'.$ded_type.'_tax[]" value="'.$mycomponent->format_money($tax,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_total_'.$sr_no.'" id="'.$ded_type.'_total_'.$i.'" name="'.$ded_type.'_total[]" value="'.$mycomponent->format_money($total,2).'" readonly /></td>
                            <td style="'.$expiry_style.'"><input type="text" class="'.$ded_type.'_expiry_date_'.$sr_no.'" id="'.$ded_type.'_expiry_date_'.$i.'" name="'.$ded_type.'_expiry_date[]" value="'.$rows[$i]["expiry_date"].'" readonly /></td>
                            <td style="'.$expiry_style.'"><input type="text" class="'.$ded_type.'_earliest_expected_date_'.$sr_no.'" id="'.$ded_type.'_earliest_expected_date_'.$i.'" name="'.$ded_type.'_earliest_expected_date[]" value="'.$rows[$i]["earliest_expected_date"].'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_mrp_'.$sr_no.'" id="'.$ded_type.'_po_mrp_'.$i.'" name="'.$ded_type.'_po_mrp[]" value="'.$mycomponent->format_money($rows[$i]["po_mrp"],2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_cost_excl_tax_'.$sr_no.'" id="'.$ded_type.'_po_cost_excl_tax_'.$i.'" name="'.$ded_type.'_po_cost_excl_tax[]" value="'.$mycomponent->format_money($po_cost_excl_tax,2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_cgst_'.$sr_no.'" id="'.$ded_type.'_po_cgst_'.$i.'" name="'.$ded_type.'_po_cgst[]" value="'.$mycomponent->format_money($po_cgst,2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_sgst_'.$sr_no.'" id="'.$ded_type.'_po_sgst_'.$i.'" name="'.$ded_type.'_po_sgst[]" value="'.$mycomponent->format_money($po_sgst,2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_igst_'.$sr_no.'" id="'.$ded_type.'_po_igst_'.$i.'" name="'.$ded_type.'_po_igst[]" value="'.$mycomponent->format_money($po_igst,2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_tax_'.$sr_no.'" id="'.$ded_type.'_po_tax_'.$i.'" name="'.$ded_type.'_po_tax[]" value="'.$mycomponent->format_money($po_tax,2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_total_'.$sr_no.'" id="'.$ded_type.'_po_total_'.$i.'" name="'.$ded_type.'_po_total[]" value="'.$mycomponent->format_money($po_total,2).'" onChange="set_sku_details(this)" /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_cost_excl_tax_'.$sr_no.'" id="'.$ded_type.'_diff_cost_excl_tax_'.$i.'" name="'.$ded_type.'_diff_cost_excl_tax[]" value="'.$mycomponent->format_money($diff_cost_excl_tax,2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_cgst_'.$sr_no.'" id="'.$ded_type.'_diff_cgst_'.$i.'" name="'.$ded_type.'_diff_cgst[]" value="'.$mycomponent->format_money($diff_cgst,2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_sgst_'.$sr_no.'" id="'.$ded_type.'_diff_sgst_'.$i.'" name="'.$ded_type.'_diff_sgst[]" value="'.$mycomponent->format_money($diff_sgst,2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_igst_'.$sr_no.'" id="'.$ded_type.'_diff_igst_'.$i.'" name="'.$ded_type.'_diff_igst[]" value="'.$mycomponent->format_money($diff_igst,2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_tax_'.$sr_no.'" id="'.$ded_type.'_diff_tax_'.$i.'" name="'.$ded_type.'_diff_tax[]" value="'.$mycomponent->format_money($diff_tax,2).'" readonly /></td>
                            <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_total_'.$sr_no.'" id="'.$ded_type.'_diff_total_'.$i.'" name="'.$ded_type.'_diff_total[]" value="'.$mycomponent->format_money($diff_total,2).'" readonly /></td>
                            <td><input type="text" class="'.$ded_type.'_remarks_'.$sr_no.'" id="'.$ded_type.'_remarks_'.$i.'" name="'.$ded_type.'_remarks[]" value="' . $remarks . '" maxlength="500" /></td>
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
                        <button type="button" class="btn btn-success" id="'.$ded_type.'_repeat_sku" onClick="add_sku_details(this)" style="'.(($ded_type=="margindiff")?"display: none;":"").'">+</button>
                    </td>
                    <td style="display: none;"></td>
                    <td>GRN Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="'.$intra_state_style.'"></td>
                    <td style="'.$intra_state_style.'"></td>
                    <td style="'.$intra_state_style.'"></td>
                    <td style="'.$intra_state_style.'"></td>
                    <td style="'.$inter_state_style.'"></td>
                    <td style="'.$inter_state_style.'"></td>
                    <td style="'.$all_style.'"></td>
                    <td style="'.$all_style.'"></td>
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td id="'.$ded_type.'_grand_total">' . $mycomponent->format_money($grand_total,2) . '</td>
                    <td style="'.$expiry_style.'"></td>
                    <td style="'.$expiry_style.'"></td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'" id="'.$ded_type.'_po_grand_total">' . $mycomponent->format_money($po_grand_total,2) . '</td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'"></td>
                    <td style="'.$margindiff_style.'" id="'.$ded_type.'_diff_grand_total">' . $mycomponent->format_money($diff_grand_total,2) . '</td>
                    <td></td>
                </tr>';

        $result = $result . $row;

        $table = '<table class="table table-bordered" id="'.$ded_type.'_sku_details">
                    <thead>
                        <tr>
                            <th colspan="5">SKU Details</th>
                            <th colspan="'.$colspan_no.'">Account Details</th>
                            <th colspan="2">Invoice Details</th>
                            <th colspan="6">Purchase Ledger</th>
                            <th colspan="2">Quantity Deducted</th>
                            <th colspan="6">Amount Deducted (Per Unit)</th>
                            <th colspan="6">Amount Deducted (Total)</th>
                            <th colspan="2" style="'.$expiry_style.'">Expiry Dates</th>
                            <th colspan="7" style="'.$margindiff_style.'">Purchase Order Details (Per Unit)</th>
                            <th colspan="6" style="'.$margindiff_style.'">Margin Difference Details</th>
                            <th rowspan="2">Remarks</th>
                        </tr>
                        <tr>
                            <th>Action</th>
                            <th style="display: none;">Sr No</th>
                            <th>SKU Code</th>
                            <th>SKU Name</th>
                            <th>EAN Code</th>
                            <th>HSN Code</th>
                            <th>Cost Ledger Name</th>
                            <th>Cost Ledger Code</th>
                            <th style="'.$intra_state_style.'">CGST Ledger Name</th>
                            <th style="'.$intra_state_style.'">CGST Ledger Code</th>
                            <th style="'.$intra_state_style.'">SGST Ledger Name</th>
                            <th style="'.$intra_state_style.'">SGST Ledger Code</th>
                            <th style="'.$inter_state_style.'">IGST Ledger Name</th>
                            <th style="'.$inter_state_style.'">IGST Ledger Code</th>
                            <th style="'.$all_style.'">Tax Ledger Name</th>
                            <th style="'.$all_style.'">Tax Ledger Code</th>
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Purchase State</th>
                            <th>Tax</th>
                            <th>CGST Rate</th>
                            <th>SGST Rate</th>
                            <th>IGST Rate</th>
                            <th>Total Tax Rate</th>
                            <th>Quantity</th>
                            <th>MRP</th>
                            <th>Cost Excl Tax</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>Total Tax</th>
                            <th>Total Amount</th>
                            <th>Cost Excl Tax</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>Total Tax</th>
                            <th>Total Amount</th>
                            <th style="'.$expiry_style.'">Date Received</th>
                            <th style="'.$expiry_style.'">Earliest Expected Date</th>
                            <th style="'.$margindiff_style.'">MRP</th>
                            <th style="'.$margindiff_style.'">Cost Excl Tax</th>
                            <th style="'.$margindiff_style.'">CGST</th>
                            <th style="'.$margindiff_style.'">SGST</th>
                            <th style="'.$margindiff_style.'">IGST</th>
                            <th style="'.$margindiff_style.'">Total Tax</th>
                            <th style="'.$margindiff_style.'">Total Amount</th>
                            <th style="'.$margindiff_style.'">Difference in Cost Excl Tax</th>
                            <th style="'.$margindiff_style.'">CGST</th>
                            <th style="'.$margindiff_style.'">SGST</th>
                            <th style="'.$margindiff_style.'">IGST</th>
                            <th style="'.$margindiff_style.'">Difference in Tax</th>
                            <th style="'.$margindiff_style.'">Difference in Total</th>
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

    public function actionGetnewrow(){   
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $gi_id = $request->post('grn_id');
        $ded_type = $request->post('ded_type');
        $sr_no = $request->post('sr_no');
        $tax_zone_code = $request->post('tax_zone_code');

        // $gi_id = 6293;
        // $ded_type = "shortage";
        // $sr_no = 1;

        // $gi_id = 5386;
        // $ded_type = "Shortage";

        // $gi_id = 5824;
        // $ded_type = "Shortage";

        // $gi_id = 4;
        // $ded_type = "Shortage";

        // $col_qty = "proper_qty";

        $expiry_style = 'display: none;';
        $margindiff_style = 'display: none;';
        $all_style = 'display: none;';

        if($ded_type=="shortage"){
            $col_qty = "shortage_qty";
        } else if($ded_type=="expiry"){
            $col_qty = "expiry_qty";
            $expiry_style = '';
        } else if($ded_type=="damaged"){
            $col_qty = "damaged_qty";
        } else if($ded_type=="margindiff"){
            $col_qty = "margindiff_qty";
            $margindiff_style = '';
        }

        // if($col_qty==""){   
        //     $gi_id = 4;
        //     $col_qty = "shortage_qty";
        // }

        $model = new PendingGrn();
        // $rows = array();
        // $rows = $model->getInvoiceDeductionDetails($gi_id, $col_qty);
        $acc_master = $model->getAccountDetails('', 'approved');
        
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

        $cost_acc_list = '<option value="">Select</option>';
        $tax_acc_list = '<option value="">Select</option>';
        $cgst_acc_list = '<option value="">Select</option>';
        $sgst_acc_list = '<option value="">Select</option>';
        $igst_acc_list = '<option value="">Select</option>';
        for($k=0; $k<count($acc_master); $k++){ 
            if($acc_master[$k]['type']=="Goods Purchase") { 
                $cost_acc_list = $cost_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>';
            }
            if($acc_master[$k]['type']=="Tax") { 
                $tax_acc_list = $tax_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>';
            }
            if($acc_master[$k]['type']=="CGST") { 
                $cgst_acc_list = $cgst_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>'; 
            }
            if($acc_master[$k]['type']=="SGST") { 
                $sgst_acc_list = $sgst_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>'; 
            }
            if($acc_master[$k]['type']=="IGST") { 
                $igst_acc_list = $igst_acc_list . '<option value="'.$acc_master[$k]['id'].'">'.$acc_master[$k]['legal_name'].'</option>'; 
            }
        }

        $invoice_no = "";

        $qty = 0;
        $state = "";
        $vat_cst = "";
        $vat_percen = 0;
        $cgst_rate = 0;
        $sgst_rate = 0;
        $igst_rate = 0;
        $cost_excl_tax_per_unit = 0;
        
        $cgst_per_unit = ($cost_excl_tax_per_unit*$cgst_rate)/100;
        $sgst_per_unit = ($cost_excl_tax_per_unit*$sgst_rate)/100;
        $igst_per_unit = ($cost_excl_tax_per_unit*$igst_rate)/100;
        $tax_per_unit = ($cost_excl_tax_per_unit*$vat_percen)/100;
        $total_per_unit = $cost_excl_tax_per_unit + $tax_per_unit;

        $cost_excl_tax = $qty*$cost_excl_tax_per_unit;
        $cgst = $qty*$cgst_per_unit;
        $sgst = $qty*$sgst_per_unit;
        $igst = $qty*$igst_per_unit;
        $tax = $qty*$tax_per_unit;
        $total = $cost_excl_tax + $tax;
        $invoice_total = $invoice_total + $total;
        $grand_total = $grand_total + $total;

        $i = $sr_no - 1;


        
        


        // $po_cgst = ($po_cost_excl_tax*$cgst_rate)/100;
        // $po_sgst = ($po_cost_excl_tax*$sgst_rate)/100;
        // $po_igst = ($po_cost_excl_tax*$igst_rate)/100;
        // $po_tax = ($po_cost_excl_tax*$vat_percen)/100;
        // $po_total = $po_cost_excl_tax + $po_tax;

        // $diff_cost_excl_tax = round($cost_excl_tax - $po_cost_excl_tax,2);
        // $diff_cgst = round($cgst - $po_cgst,2);
        // $diff_sgst = round($sgst - $po_sgst,2);
        // $diff_igst = round($igst - $po_igst,2);
        // $diff_tax = round($tax - $po_tax,2);
        // $diff_total = round($total - $po_total,2);

        // $grand_total = $grand_total + $total;
        // $po_grand_total = $po_grand_total + $po_total;
        // $diff_grand_total = $diff_grand_total + $diff_total;

        // $remarks = $rows[$i]["remarks"];


        $intra_state_style = "";
        $inter_state_style = "";
        if(strtoupper($tax_zone_code)=="INTRA"){
            $inter_state_style = "display:none;";
        } else {
            $intra_state_style = "display:none;";
        }

        $row = '<tr id="'.$ded_type.'_row_'.$i.'">
                    <td style="text-align: center;"><button type="button" class="btn btn-sm btn-success" id="'.$ded_type.'_delete_row_'.$i.'" onClick="delete_row(this);">-</button></td>
                    <td style="display: none;">' . $sr_no . '</td>
                    <td>
                        <select class="'.$ded_type.'_psku_'.$sr_no.'" id="'.$ded_type.'_psku_'.$i.'" name="'.$ded_type.'_psku[]" onChange="get_sku_details(this)" data-error="#'.$ded_type.'_psku_'.$i.'_error">' . $sku_list . '</select>
                        <div id="'.$ded_type.'_psku_'.$i.'_error"></div>
                    </td>
                    <td><input type="text" class="'.$ded_type.'_product_title_'.$sr_no.'" id="'.$ded_type.'_product_title_'.$i.'" name="'.$ded_type.'_product_title[]" value="" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_ean_'.$sr_no.'" id="'.$ded_type.'_ean_'.$i.'" name="'.$ded_type.'_ean[]" value="" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_hsn_code_'.$sr_no.'" id="'.$ded_type.'_hsn_code_'.$i.'" name="'.$ded_type.'_hsn_code[]" value="" readonly /></td>
                    <td>
                        <select id="'.$ded_type.'cost_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_cost_acc_id[]" onChange="get_acc_details(this)" data-error="#'.$ded_type.'cost_acc_id_'.$sr_no.'_error">'.$cost_acc_list.'</select>
                        <input type="hidden" id="'.$ded_type.'cost_ledger_name_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_name[]" value="" />
                        <input type="hidden" id="'.$ded_type.'cost_voucher_id_'.$sr_no.'" name="'.$ded_type.'_cost_voucher_id[]" value="" />
                        <input type="hidden" id="'.$ded_type.'cost_ledger_type_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_type[]" value="" />
                        <div id="'.$ded_type.'cost_acc_id_'.$sr_no.'_error"></div>
                    </td>
                    <td><input type="text" id="'.$ded_type.'cost_ledger_code_'.$sr_no.'" name="'.$ded_type.'_cost_ledger_code[]" value="" readonly /></td>
                    <td style="'.$intra_state_style.'">
                        <select id="'.$ded_type.'cgst_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_cgst_acc_id[]" onChange="get_acc_details(this)" data-error="#'.$ded_type.'cgst_acc_id_'.$sr_no.'_error">'.$cgst_acc_list.'</select>
                        <input type="hidden" id="'.$ded_type.'cgst_ledger_name_'.$sr_no.'" name="'.$ded_type.'_cgst_ledger_name[]" value="" />
                        <input type="hidden" id="'.$ded_type.'cgst_voucher_id_'.$sr_no.'" name="'.$ded_type.'_cgst_voucher_id[]" value="" />
                        <input type="hidden" id="'.$ded_type.'cgst_ledger_type_'.$sr_no.'" name="'.$ded_type.'_cgst_ledger_type[]" value="" />
                        <div id="'.$ded_type.'cgst_acc_id_'.$sr_no.'_error"></div>
                    </td>
                    <td style="'.$intra_state_style.'"><input type="text" id="'.$ded_type.'cgst_ledger_code_'.$sr_no.'" name="'.$ded_type.'_cgst_ledger_code[]" value="" readonly /></td>
                    <td style="'.$intra_state_style.'">
                        <select id="'.$ded_type.'sgst_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_sgst_acc_id[]" onChange="get_acc_details(this)" data-error="#'.$ded_type.'sgst_acc_id_'.$sr_no.'_error">'.$sgst_acc_list.'</select>
                        <input type="hidden" id="'.$ded_type.'sgst_ledger_name_'.$sr_no.'" name="'.$ded_type.'_sgst_ledger_name[]" value="" />
                        <input type="hidden" id="'.$ded_type.'sgst_voucher_id_'.$sr_no.'" name="'.$ded_type.'_sgst_voucher_id[]" value="" />
                        <input type="hidden" id="'.$ded_type.'sgst_ledger_type_'.$sr_no.'" name="'.$ded_type.'_sgst_ledger_type[]" value="" />
                        <div id="'.$ded_type.'sgst_acc_id_'.$sr_no.'_error"></div>
                    </td>
                    <td style="'.$intra_state_style.'"><input type="text" id="'.$ded_type.'sgst_ledger_code_'.$sr_no.'" name="'.$ded_type.'_sgst_ledger_code[]" value="" readonly /></td>
                    <td style="'.$inter_state_style.'">
                        <select id="'.$ded_type.'igst_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_igst_acc_id[]" onChange="get_acc_details(this)" data-error="#'.$ded_type.'igst_acc_id_'.$sr_no.'_error">'.$igst_acc_list.'</select>
                        <input type="hidden" id="'.$ded_type.'igst_ledger_name_'.$sr_no.'" name="'.$ded_type.'_igst_ledger_name[]" value="" />
                        <input type="hidden" id="'.$ded_type.'igst_voucher_id_'.$sr_no.'" name="'.$ded_type.'_igst_voucher_id[]" value="" />
                        <input type="hidden" id="'.$ded_type.'igst_ledger_type_'.$sr_no.'" name="'.$ded_type.'_igst_ledger_type[]" value="" />
                        <div id="'.$ded_type.'igst_acc_id_'.$sr_no.'_error"></div>
                    </td>
                    <td style="'.$inter_state_style.'"><input type="text" id="'.$ded_type.'igst_ledger_code_'.$sr_no.'" name="'.$ded_type.'_igst_ledger_code[]" value="" readonly /></td>
                    <td style="'.$all_style.'">
                        <select id="'.$ded_type.'tax_acc_id_'.$sr_no.'" class="acc_id" name="'.$ded_type.'_tax_acc_id[]" onChange="get_acc_details(this)" data-error="#'.$ded_type.'tax_acc_id_'.$sr_no.'_error">'.$tax_acc_list.'</select>
                        <input type="hidden" id="'.$ded_type.'tax_ledger_name_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_name[]" value="" />
                        <input type="hidden" id="'.$ded_type.'tax_voucher_id_'.$sr_no.'" name="'.$ded_type.'_tax_voucher_id[]" value="" />
                        <input type="hidden" id="'.$ded_type.'tax_ledger_type_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_type[]" value="" />
                        <div id="'.$ded_type.'tax_acc_id_'.$sr_no.'_error"></div>
                    </td>
                    <td style="'.$all_style.'"><input type="text" id="'.$ded_type.'tax_ledger_code_'.$sr_no.'" name="'.$ded_type.'_tax_ledger_code[]" value="" readonly /></td>
                    <td>
                        <select class="'.$ded_type.'_invoice_no_'.$sr_no.'" id="'.$ded_type.'_invoice_no_'.$i.'" name="'.$ded_type.'_invoice_no[]" onChange="set_sku_details(this)" data-error="'.$ded_type.'_invoice_no_'.$sr_no.'_error">' . $invoice_list . '</select>
                        <div id="'.$ded_type.'_invoice_no_'.$sr_no.'_error"></div>
                    </td>
                    <td id="'.$ded_type.'_invoice_date_'.$i.'"></td>
                    <td><input type="text" class="'.$ded_type.'_state_'.$sr_no.'" id="'.$ded_type.'_state_'.$i.'" name="'.$ded_type.'_state[]" value="'.$state.'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_vat_cst_'.$sr_no.'" id="'.$ded_type.'_vat_cst_'.$i.'" name="'.$ded_type.'_vat_cst[]" value="'.$vat_cst.'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_cgst_rate_'.$sr_no.'" id="'.$ded_type.'_cgst_rate_'.$i.'" name="'.$ded_type.'_cgst_rate[]" value="'.$mycomponent->format_money($cgst_rate,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_sgst_rate_'.$sr_no.'" id="'.$ded_type.'_sgst_rate_'.$i.'" name="'.$ded_type.'_sgst_rate[]" value="'.$mycomponent->format_money($sgst_rate,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_igst_rate_'.$sr_no.'" id="'.$ded_type.'_igst_rate_'.$i.'" name="'.$ded_type.'_igst_rate[]" value="'.$mycomponent->format_money($igst_rate,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_vat_percen_'.$sr_no.'" id="'.$ded_type.'_vat_percen_'.$i.'" name="'.$ded_type.'_vat_percen[]" value="'.$mycomponent->format_money($vat_percen,2).'" readonly /></td>
                    <td>
                        <input type="text" class="'.$ded_type.'_qty_'.$sr_no.' edit-sku" id="'.$ded_type.'_qty_'.$i.'" name="'.$ded_type.'_qty[]" value="' . $mycomponent->format_money($qty,2) . '" onChange="set_sku_details(this)" data-error="#'.$ded_type.'qty_'.$i.'_error" '.(($ded_type=="margindiff")?"readonly ":" ").'/>
                        <div id="'.$ded_type.'qty_'.$i.'_error"></div>
                    </td>
                    <td><input type="text" class="'.$ded_type.'_box_price_'.$sr_no.'" id="'.$ded_type.'_box_price_'.$i.'" name="'.$ded_type.'_box_price[]" value="" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_cost_excl_tax_per_unit_'.$sr_no.'" id="'.$ded_type.'_cost_excl_tax_per_unit_'.$i.'" name="'.$ded_type.'_cost_excl_tax_per_unit[]" value="'.$mycomponent->format_money($cost_excl_tax_per_unit,2).'" onChange="set_sku_details(this)" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_cgst_per_unit_'.$sr_no.'" id="'.$ded_type.'_cgst_per_unit_'.$i.'" name="'.$ded_type.'_cgst_per_unit[]" value="'.$mycomponent->format_money($cgst_per_unit,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_sgst_per_unit_'.$sr_no.'" id="'.$ded_type.'_sgst_per_unit_'.$i.'" name="'.$ded_type.'_sgst_per_unit[]" value="'.$mycomponent->format_money($sgst_per_unit,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_igst_per_unit_'.$sr_no.'" id="'.$ded_type.'_igst_per_unit_'.$i.'" name="'.$ded_type.'_igst_per_unit[]" value="'.$mycomponent->format_money($igst_per_unit,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_tax_per_unit_'.$sr_no.'" id="'.$ded_type.'_tax_per_unit_'.$i.'" name="'.$ded_type.'_tax_per_unit[]" value="'.$mycomponent->format_money($tax_per_unit,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_total_per_unit_'.$sr_no.'" id="'.$ded_type.'_total_per_unit_'.$i.'" name="'.$ded_type.'_total_per_unit[]" value="'.$mycomponent->format_money($total_per_unit,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_cost_excl_tax_'.$sr_no.'" id="'.$ded_type.'_cost_excl_tax_'.$i.'" name="'.$ded_type.'_cost_excl_tax[]" value="'.$mycomponent->format_money($cost_excl_tax,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_cgst_'.$sr_no.'" id="'.$ded_type.'_cgst_'.$i.'" name="'.$ded_type.'_cgst[]" value="'.$mycomponent->format_money($cgst,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_sgst_'.$sr_no.'" id="'.$ded_type.'_sgst_'.$i.'" name="'.$ded_type.'_sgst[]" value="'.$mycomponent->format_money($sgst,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_igst_'.$sr_no.'" id="'.$ded_type.'_igst_'.$i.'" name="'.$ded_type.'_igst[]" value="'.$mycomponent->format_money($igst,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_tax_'.$sr_no.'" id="'.$ded_type.'_tax_'.$i.'" name="'.$ded_type.'_tax[]" value="'.$mycomponent->format_money($tax,2).'" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_total_'.$sr_no.'" id="'.$ded_type.'_total_'.$i.'" name="'.$ded_type.'_total[]" value="'.$mycomponent->format_money($total,2).'" readonly /></td>
                    <td style="'.$expiry_style.'"><input type="text" class="'.$ded_type.'_expiry_date_'.$sr_no.'" id="'.$ded_type.'_expiry_date_'.$i.'" name="'.$ded_type.'_expiry_date[]" value="" readonly /></td>
                    <td style="'.$expiry_style.'"><input type="text" class="'.$ded_type.'_earliest_expected_date_'.$sr_no.'" id="'.$ded_type.'_earliest_expected_date_'.$i.'" name="'.$ded_type.'_earliest_expected_date[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_mrp_'.$sr_no.'" id="'.$ded_type.'_po_mrp_'.$i.'" name="'.$ded_type.'_po_mrp[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_cost_excl_tax_'.$sr_no.'" id="'.$ded_type.'_po_cost_excl_tax_'.$i.'" name="'.$ded_type.'_po_cost_excl_tax[]" value="" onChange="set_sku_details(this)" /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_cgst_'.$sr_no.'" id="'.$ded_type.'_po_cgst_'.$i.'" name="'.$ded_type.'_po_cgst[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_sgst_'.$sr_no.'" id="'.$ded_type.'_po_sgst_'.$i.'" name="'.$ded_type.'_po_sgst[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_igst_'.$sr_no.'" id="'.$ded_type.'_po_igst_'.$i.'" name="'.$ded_type.'_po_igst[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_tax_'.$sr_no.'" id="'.$ded_type.'_po_tax_'.$i.'" name="'.$ded_type.'_po_tax[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_po_total_'.$sr_no.'" id="'.$ded_type.'_po_total_'.$i.'" name="'.$ded_type.'_po_total[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_cost_excl_tax_'.$sr_no.'" id="'.$ded_type.'_diff_cost_excl_tax_'.$i.'" name="'.$ded_type.'_diff_cost_excl_tax[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_cgst_'.$sr_no.'" id="'.$ded_type.'_diff_cgst_'.$i.'" name="'.$ded_type.'_diff_cgst[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_sgst_'.$sr_no.'" id="'.$ded_type.'_diff_sgst_'.$i.'" name="'.$ded_type.'_diff_sgst[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_igst_'.$sr_no.'" id="'.$ded_type.'_diff_igst_'.$i.'" name="'.$ded_type.'_diff_igst[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_tax_'.$sr_no.'" id="'.$ded_type.'_diff_tax_'.$i.'" name="'.$ded_type.'_diff_tax[]" value="" readonly /></td>
                    <td style="'.$margindiff_style.'"><input type="text" class="'.$ded_type.'_diff_total_'.$sr_no.'" id="'.$ded_type.'_diff_total_'.$i.'" name="'.$ded_type.'_diff_total[]" value="" readonly /></td>
                    <td><input type="text" class="'.$ded_type.'_remarks_'.$sr_no.'" id="'.$ded_type.'_remarks_'.$i.'" name="'.$ded_type.'_remarks[]" value="" maxlength="500" /></td>
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