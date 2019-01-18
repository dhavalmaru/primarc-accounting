<?php

namespace app\controllers;

use Yii;
use app\models\PendingGo;
use app\models\AccountMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Session;

class PendinggoController extends Controller
{
    public function actionIndex(){
        $grn_cnt = new PendingGo();
        // $grn = $grn_cnt->getNewGrnDetails();
        $grn = [];
        // $pending = $grn_cnt->getPurchaseDetails('pending');
        $pending = [];
        // $approved = $grn_cnt->getPurchaseDetails('approved');
        $approved = [];
        // $all = $grn_cnt->getAllGrnDetails();
        $all = [];
        return $this->render('pending_go', [
            'grn' => $grn, 'pending' => $pending, 'approved' => $approved, 'all' => $all
        ]);
    }

    public function actionGetgo(){
        $grn_cnt = new PendingGo();
        $grn = $grn_cnt->getNewGoDetails();
        $grn_count  = $grn_cnt->getCountGrnDetails();
        $request = Yii::$app->request;
        $grn_data = array();
        $mycomponent = Yii::$app->mycomponent;
        $start = $request->post('start');    
        //$params['start'].", ".$params['length']

        $access = $grn_cnt->getAccess();
        $r_edit = 0;
        if(isset($access[0])) { 
            if($access[0]['r_insert']=='1' || $access[0]['r_edit']=='1') { 
                $r_edit = 1;
            } 
        }

        for($i=0; $i<count($grn); $i++) { 
            $row = array(
                        $start+1,
                        (($r_edit == 1)?'<a href="'.Url::base() .'index.php?r=pendinggo%2Fupdate&id='.$grn[$i]['gi_go_id'].'" >Post </a>':''),
                        /*''.$grn[$i]['gi_go_id'].'',*/
                        ''.$grn[$i]['gi_go_ref_no'].'',
                        ''.$grn[$i]['po_number'].'',
                        ''.$grn[$i]['order_id'].'',
                        ''.$grn[$i]['dcno'].'',
                        ''.$grn[$i]['invoice_number'].'',
                        ''.$grn[$i]['warehouse_name'].'',
                        ''.$grn[$i]['customerState'].'',
                        ''.$grn[$i]['customerName'].'',
                        ''.$grn[$i]['no_good_units'].'',
                        // ''.$mycomponent->format_money($grn[$i]['value_at_cost'], 2).'',
                        ''.$grn[$i]['gi_go_date_time'].'',
                        ''.$grn[$i]['gi_go_status'].'',
                        ''.$grn[$i]['updated_by'].'',
                        '',
                        ) ;
           $grn_data[] = $row;
           $start = $start+1;
        }
        $json_data = array(
                "draw"            => intval($request->post('draw')),   
                "recordsTotal"    => intval($grn_count),  
                "recordsFiltered" => intval($grn_count),
                "data"            => $grn_data
                );

        echo json_encode($json_data);
    }

    public function actionGetapprovedgrn(){
        $grn_cnt = new PendingGo();
        $grn = $grn_cnt->getPurchaseDetails('approved');
        $grn_count  = $grn_cnt->getCountPurchaseDetails('approved');
        $request = Yii::$app->request;
        $grn_data = array();
        $mycomponent = Yii::$app->mycomponent;
        $start = $request->post('start');    
        //$params['start'].", ".$params['length']

        $access = $grn_cnt->getAccess();
        $r_edit = 0;
        if(isset($access[0])) { 
            if($access[0]['r_insert']=='1' || $access[0]['r_edit']=='1') { 
                $r_edit = 1;
            } 
        }
        
        for($i=0; $i<count($grn); $i++) { 
           $row = array(
                        $start+1,
                        '<a href="'.Url::base() .'index.php?r=pendinggo%2Fview&id='.$grn[$i]['gi_go_id'].'" >View </a>'.
                        (($r_edit == 1)?'<a href="'.Url::base() .'index.php?r=pendinggo%2Fupdate&id='.$grn[$i]['gi_go_id'].'" style="'.($grn[$i]['is_paid']=='1'?'display: none;':'').'" >Edit </a>':''),
                        /*''.$grn[$i]['gi_go_id'].'',*/
                        ''.$grn[$i]['gi_go_id'].'',
                        ''.$grn[$i]['po_number'].'',
                        ''.$grn[$i]['order_id'].'',
                        ''.$grn[$i]['warehouse_state'].'',
                        ''.$grn[$i]['customerState'].'',
                        ''.$grn[$i]['customerName'].'',
                        // ''.$mycomponent->format_money($grn[$i]['value_at_cost'], 2).'',
                        ''.$grn[$i]['gi_go_date_time'].'',
                        ''.$grn[$i]['gi_go_status'].'',
                        ''.$grn[$i]['updated_by'].'',
                        '',
                        '<a href="'.Url::base() .'index.php?r=pendinggo%2Fledger&id='.$grn[$i]['gi_go_id'].'" target="_new"> <span class="fa fa-file-pdf-o"></span> </a>',
                        ) ;
           $grn_data[] = $row;
           $start = $start+1;
        }
        $json_data = array(
                "draw"            => intval($request->post('draw')),   
                "recordsTotal"    => intval($grn_count),  
                "recordsFiltered" => intval($grn_count),
                "data"            => $grn_data
                );

        echo json_encode($json_data);
    }

    public function actionGetallgrn(){  
        $grn_cnt = new PendingGo();
        $grn = $grn_cnt->getAllGrnDetails();
        $grn_count  = $grn_cnt->getCountAllGrnDetails();
        $request = Yii::$app->request;
        $grn_data = array();
        $mycomponent = Yii::$app->mycomponent;
        $start = $request->post('start');    
        //$params['start'].", ".$params['length']

        $access = $grn_cnt->getAccess();
        $r_edit = 0;
        if(isset($access[0])) { 
            if($access[0]['r_insert']=='1' || $access[0]['r_edit']=='1') { 
                $r_edit = 1;
            } 
        }
        
        for($i=0; $i<count($grn); $i++) { 
           $row = array(
                        $start+1,
                        (($r_edit == 1)?'<a href="'.Url::base() .'index.php?r=pendinggo%2Fupdate&id='.$grn[$i]['gi_go_id'].'" >Post </a>':''),
                        /*''.$grn[$i]['gi_go_id'].'',*/
                        ''.$grn[$i]['gi_go_id'].'',
                        ''.$grn[$i]['po_number'].'',
                        ''.$grn[$i]['order_id'].'',
                        ''.$grn[$i]['warehouse_state'].'',
                        ''.$grn[$i]['customerState'].'',
                        ''.$grn[$i]['customerName'].'',
                        // ''.$mycomponent->format_money($grn[$i]['value_at_cost'], 2).'',
                        ''.$grn[$i]['gi_go_date_time'].'',
                        ''.$grn[$i]['gi_go_status'].'',
                        ''.$grn[$i]['updated_by'].'',
                        '',
                        ) ;
           $grn_data[] = $row;
           $start = $start+1;
        }
        $json_data = array(
                "draw"            => intval($request->post('draw')),   
                "recordsTotal"    => intval($grn_count),  
                "recordsFiltered" => intval($grn_count),
                "data"            => $grn_data
                );

        echo json_encode($json_data);
    }


    // public function actionGetdebitnote(){
    //     $invoice_id = '11266';

    //     $model = new PendingGo();
    //     $data = $model->getDebitNoteDetails($invoice_id);
        
    //     $this->layout = false;
    //     return $this->render('debit_note', [
    //         'invoice_details' => $data['invoice_details'], 'debit_note' => $data['debit_note'], 
    //         'deduction_details' => $data['deduction_details'], 'vendor_details' => $data['vendor_details']
    //     ]);
    // }

    public function actionViewdebitnote($invoice_id){
        $model = new PendingGo();
        $data = $model->getDebitNoteDetails($invoice_id);
        
        $this->layout = false;
        return $this->render('debit_note', [
            'invoice_details' => $data['invoice_details'], 'debit_note' => $data['debit_note'], 
            'deduction_details' => $data['deduction_details'], 'vendor_details' => $data['vendor_details'], 
            'grn_details' => $data['grn_details']
        ]);
    }

    public function actionDownload($invoice_id){
        $model = new PendingGo();
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
        $model = new PendingGo();
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
        $from = 'accounts@primarcpecan.com';
        $to = $request->post('to');
        // $to = 'prasad.bhisale@otbconsulting.co.in';
        $subject = $request->post('subject');
        $attachment = $request->post('attachment');
        $body = $request->post('body');

        // $grn_id = '28';
        // $from = 'accounts@primarcpecan.com';
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
        $model = new PendingGo();
        $model->setEmailLog($vendor_name, $from, $to, $id, $body, $attachment, 
                                $attachment_type, $email_sent_status, $error_message, $company_id);

        return $this->render('email_response', ['data' => $data]);
    }

    public function actionView($id) {
        $model = new PendingGo();
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
        $model = new PendingGo();
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

  
    public function actionGetgrnpostingdetails($id){
        $total_val = array();
        $total_tax = array();
        $invoice_details = array();
        $invoice_tax = array();

        $model = new PendingGo();
        $result = $model->getGrnPostingDetails($id);


        if(count($result)>0){
            $total_val[0]['total_amount'] = 0;
            $total_val[0]['total_payable_amount'] = 0;
            $other_charge = 0;
            $total_val[0]['other_charges'] = $other_charge;
            $total_val[0]['total_amount'] = $other_charge;

            $j = 0;
            $total_tax[$j]['gi_go_id'] = $result[0]['gi_go_id'];
            $total_tax[$j]['tax_zone_code'] = $result[0]['tax_zone_code'];
            $total_tax[$j]['tax_zone_name'] = $result[0]['tax_zone_name'];
            $total_tax[$j]['vat_cst'] = $result[0]['vat_cst'];
            $total_tax[$j]['vat_percent'] = $result[0]['vat_percent'];
            $total_tax[$j]['cgst_rate'] = $result[0]['cgst_rate'];
            $total_tax[$j]['sgst_rate'] = $result[0]['sgst_rate'];
            $total_tax[$j]['igst_rate'] = $result[0]['igst_rate'];
            $total_tax[$j]['total_cost'] = 0;
            $total_tax[$j]['total_tax'] = 0;
            $total_tax[$j]['total_cgst'] = 0;
            $total_tax[$j]['total_sgst'] = 0;
            $total_tax[$j]['total_igst'] = 0;
            
            $total_tax[$j]['invoice_cost_acc_id'] = null;
            $total_tax[$j]['invoice_cost_ledger_name'] = null;
            $total_tax[$j]['invoice_cost_ledger_code'] = null;
            $total_tax[$j]['invoice_tax_acc_id'] = null;
            $total_tax[$j]['invoice_tax_ledger_name'] = null;
            $total_tax[$j]['invoice_tax_ledger_code'] = null;
            $total_tax[$j]['invoice_cgst_acc_id'] = null;
            $total_tax[$j]['invoice_cgst_ledger_name'] = null;
            $total_tax[$j]['invoice_cgst_ledger_code'] = null;
            $total_tax[$j]['invoice_sgst_acc_id'] = null;
            $total_tax[$j]['invoice_sgst_ledger_name'] = null;
            $total_tax[$j]['invoice_sgst_ledger_code'] = null;
            $total_tax[$j]['invoice_igst_acc_id'] = null;
            $total_tax[$j]['invoice_igst_ledger_name'] = null;
            $total_tax[$j]['invoice_igst_ledger_code'] = null;

            $k = 0;
            $invoice_details[$k]['invoice_number'] = $result[0]['invoice_number'];
            $invoice_details[$k]['invoice_total_cost'] = 0;
            $invoice_details[$k]['invoice_total_tax'] = 0;
            $invoice_details[$k]['invoice_other_charges'] = $other_charge;
            $invoice_details[$k]['invoice_total_amount'] = $other_charge;
            
            $invoice_details[$k]['invoice_total_payable_amount'] = 0;
            $invoice_details[$k]['edited_total_cost'] = 0;
            $invoice_details[$k]['edited_total_tax'] = 0;
            $invoice_details[$k]['edited_total_amount'] = 0;
            $invoice_details[$k]['edited_other_charges'] = $other_charge;
            $invoice_details[$k]['edited_total_amount'] = $other_charge;
            $invoice_details[$k]['edited_total_payable_amount'] = 0;
            $invoice_details[$k]['diff_total_cost'] = 0;
            $invoice_details[$k]['diff_total_tax'] = 0;
            $invoice_details[$k]['diff_total_amount'] = 0;
            $invoice_details[$k]['diff_total_payable_amount'] = 0;
            $invoice_details[$k]['diff_other_charges'] = 0;
            $invoice_details[$k]['total_amount_voucher_id'] = null;
            $invoice_details[$k]['total_amount_ledger_type'] = null;
            $invoice_details[$k]['total_deduction_voucher_id'] = null;
            $invoice_details[$k]['total_deduction_ledger_type'] = null;
            $invoice_details[$k]['diff_other_charges'] = 0;

            $l = 0;
            $invoice_tax[$l]['gi_go_id'] = $result[0]['gi_go_id'];
            $invoice_tax[$l]['tax_zone_code'] = $result[0]['tax_zone_code'];
            $invoice_tax[$l]['tax_zone_name'] = $result[0]['tax_zone_name'];
            $invoice_tax[$l]['invoice_number'] = $result[0]['invoice_number'];
            $invoice_tax[$l]['vat_cst'] = $result[0]['vat_cst'];
            $invoice_tax[$l]['vat_percent'] = $result[0]['vat_percent'];
            $invoice_tax[$j]['cgst_rate'] = $result[0]['cgst_rate'];
            $invoice_tax[$j]['sgst_rate'] = $result[0]['sgst_rate'];
            $invoice_tax[$j]['igst_rate'] = $result[0]['igst_rate'];
            $invoice_tax[$l]['total_cost'] = 0;
            $invoice_tax[$l]['total_tax'] = 0;
            $invoice_tax[$l]['total_cgst'] = 0;
            $invoice_tax[$l]['total_sgst'] = 0;
            $invoice_tax[$l]['total_igst'] = 0;
            $invoice_tax[$l]['invoice_cost'] = 0;
            $invoice_tax[$l]['invoice_tax'] = 0;
            $invoice_tax[$l]['invoice_cgst'] = 0;
            $invoice_tax[$l]['invoice_sgst'] = 0;
            $invoice_tax[$l]['invoice_igst'] = 0;
            $invoice_tax[$l]['edited_cost'] = 0;
            $invoice_tax[$l]['edited_tax'] = 0;
            $invoice_tax[$l]['edited_cgst'] = 0;
            $invoice_tax[$l]['edited_sgst'] = 0;
            $invoice_tax[$l]['edited_igst'] = 0;
            $invoice_tax[$l]['diff_cost'] = 0;
            $invoice_tax[$l]['diff_tax'] = 0;
            $invoice_tax[$l]['diff_cgst'] = 0;
            $invoice_tax[$l]['diff_sgst'] = 0;
            $invoice_tax[$l]['diff_igst'] = 0;
            
            $invoice_tax[$l]['invoice_cost_acc_id'] = null;
            $invoice_tax[$l]['invoice_cost_ledger_name'] = null;
            $invoice_tax[$l]['invoice_cost_ledger_code'] = null;
            $invoice_tax[$l]['invoice_tax_acc_id'] = null;
            $invoice_tax[$l]['invoice_tax_ledger_name'] = null;
            $invoice_tax[$l]['invoice_tax_ledger_code'] = null;
            $invoice_tax[$l]['invoice_cgst_acc_id'] = null;
            $invoice_tax[$l]['invoice_cgst_ledger_name'] = null;
            $invoice_tax[$l]['invoice_cgst_ledger_code'] = null;
            $invoice_tax[$l]['invoice_sgst_acc_id'] = null;
            $invoice_tax[$l]['invoice_sgst_ledger_name'] = null;
            $invoice_tax[$l]['invoice_sgst_ledger_code'] = null;
            $invoice_tax[$l]['invoice_igst_acc_id'] = null;
            $invoice_tax[$l]['invoice_igst_ledger_name'] = null;
            $invoice_tax[$l]['invoice_igst_ledger_code'] = null;

            $blFlag = false;

            for($i=0; $i<count($result); $i++){
                
                $cost_excl_vat = floatval($result[$i]['cost_excl_vat']);
                $cost_incl_vat_cst = floatval($result[$i]['value_incl_vat']);
                $invoice_qty = floatval($result[$i]['invoice_qty']);
               
                $vat_percent = floatval($result[$i]['vat_percent']);
                $cgst_rate = floatval($result[$i]['cgst_rate']);
                $sgst_rate = floatval($result[$i]['sgst_rate']);
                $igst_rate = floatval($result[$i]['igst_rate']);

                $tot_cost = $cost_excl_vat;
                $tot_cgst = $cost_excl_vat*$cgst_rate/100;
                $tot_sgst = $cost_excl_vat*$sgst_rate/100;
                $tot_igst = $cost_excl_vat*$igst_rate/100;
                $tot_tax = $tot_cgst+$tot_sgst+$tot_igst;

                $total_val[0]['total_amount'] = floatval($total_val[0]['total_amount']) + $tot_cost + $tot_tax;

                $blFlag = false;
                for($a=0;$a<count($total_tax);$a++){
                    if($result[$i]['gi_go_id']==$total_tax[$a]['gi_go_id'] && $result[$i]['tax_zone_code']==$total_tax[$a]['tax_zone_code'] && 
                       $result[$i]['tax_zone_name']==$total_tax[$a]['tax_zone_name'] && $result[$i]['vat_cst']==$total_tax[$a]['vat_cst'] && 
                       $result[$i]['vat_percent']==$total_tax[$a]['vat_percent'] && $result[$i]['cgst_rate']==$total_tax[$a]['cgst_rate'] && 
                       $result[$i]['sgst_rate']==$total_tax[$a]['sgst_rate'] && $result[$i]['igst_rate']==$total_tax[$a]['igst_rate'] ){

                        $blFlag = true;
                        $j = $a;
                    }
                }


                if($blFlag==true){
                   /* echo 'true';
                    echo '<br/>';*/

                    $total_tax[$j]['total_cost'] = floatval($total_tax[$j]['total_cost']) + $tot_cost;
                    $total_tax[$j]['total_tax'] = floatval($total_tax[$j]['total_tax']) + $tot_tax;
                    $total_tax[$j]['total_cgst'] = floatval($total_tax[$j]['total_cgst']) + $tot_cgst;
                    $total_tax[$j]['total_sgst'] = floatval($total_tax[$j]['total_sgst']) + $tot_sgst;
                    $total_tax[$j]['total_igst'] = floatval($total_tax[$j]['total_igst']) + $tot_igst;

                   /* echo 'total_tax';
                    echo '<br/>';*/

                    $warehouse_code = $result[$i]['warehouse_code'];
                    $state_name = '';
                    $result2 = $model->getState($warehouse_code);
                    if(count($result2)>0){
                        $state_name = $result2[0]['state_name'];
                    }

                    if($result[$i]['igst_rate']==0){
                        $vat_percent = $result[$i]['vat_percent'];
                        if(is_numeric($vat_percent)){
                            $vat_percent = floatval($vat_percent);
                        }
                        $tax_code = 'Sales-'.$state_name.'-Local-B2B-'.$vat_percent;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            // echo json_encode($result2);
                            // echo '<br/>';
                            $total_tax[$j]['invoice_cost_acc_id'] = $result2[0]['id'];
                            $total_tax[$j]['invoice_cost_ledger_name'] = $result2[0]['legal_name'];
                            $total_tax[$j]['invoice_cost_ledger_code'] = $result2[0]['code'];
                        }

                        $cgst_rate = $result[$i]['cgst_rate'];
                        if(is_numeric($cgst_rate)){
                            $cgst_rate = floatval($cgst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-CGST-'.$cgst_rate;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $total_tax[$j]['invoice_cgst_acc_id'] = $result2[0]['id'];
                            $total_tax[$j]['invoice_cgst_ledger_name'] = $result2[0]['legal_name'];
                            $total_tax[$j]['invoice_cgst_ledger_code'] = $result2[0]['code'];
                        }

                        $sgst_rate = $result[$i]['sgst_rate'];
                        if(is_numeric($sgst_rate)){
                            $sgst_rate = floatval($sgst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-SGST-'.$sgst_rate;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $total_tax[$j]['invoice_sgst_acc_id'] = $result2[0]['id'];
                            $total_tax[$j]['invoice_sgst_ledger_name'] = $result2[0]['legal_name'];
                            $total_tax[$j]['invoice_sgst_ledger_code'] = $result2[0]['code'];
                        }
                    } else {
                        $vat_percent = $result[$i]['vat_percent'];
                        if(is_numeric($vat_percent)){
                            $vat_percent = floatval($vat_percent);
                        }
                        $tax_code = 'Sales-'.$state_name.'-Inter State-B2B-'.$vat_percent;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $total_tax[$j]['invoice_cost_acc_id'] = $result2[0]['id'];
                            $total_tax[$j]['invoice_cost_ledger_name'] = $result2[0]['legal_name'];
                            $total_tax[$j]['invoice_cost_ledger_code'] = $result2[0]['code'];
                        }

                        $igst_rate = $result[$i]['igst_rate'];
                        if(is_numeric($igst_rate)){
                            $igst_rate = floatval($igst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-IGST-'.$result[$i]['igst_rate'];
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $total_tax[$j]['invoice_igst_acc_id'] = $result2[0]['id'];
                            $total_tax[$j]['invoice_igst_ledger_name'] = $result2[0]['legal_name'];
                            $total_tax[$j]['invoice_igst_ledger_code'] = $result2[0]['code'];
                        }
                    }
                } else {
                    // echo 'false';
                    // echo '<br/>';

                    $j = count($total_tax);
                    $total_tax[$j]['gi_go_id'] = $result[$i]['gi_go_id'];
                    $total_tax[$j]['tax_zone_code'] = $result[$i]['tax_zone_code'];
                    $total_tax[$j]['tax_zone_name'] = $result[$i]['tax_zone_name'];
                    $total_tax[$j]['vat_cst'] = $result[$i]['vat_cst'];
                    $total_tax[$j]['vat_percent'] = $result[$i]['vat_percent'];
                    $total_tax[$j]['cgst_rate'] = $result[$i]['cgst_rate'];
                    $total_tax[$j]['sgst_rate'] = $result[$i]['sgst_rate'];
                    $total_tax[$j]['igst_rate'] = $result[$i]['igst_rate'];
                    $total_tax[$j]['total_cost'] = $tot_cost;
                    $total_tax[$j]['total_tax'] = $tot_tax;
                    $total_tax[$j]['total_cgst'] = $tot_cgst;
                    $total_tax[$j]['total_sgst'] = $tot_sgst;
                    $total_tax[$j]['total_igst'] = $tot_igst;
                    
                    $total_tax[$j]['invoice_cost_acc_id'] = null;
                    $total_tax[$j]['invoice_cost_ledger_name'] = null;
                    $total_tax[$j]['invoice_cost_ledger_code'] = null;
                    $total_tax[$j]['invoice_tax_acc_id'] = null;
                    $total_tax[$j]['invoice_tax_ledger_name'] = null;
                    $total_tax[$j]['invoice_tax_ledger_code'] = null;
                    $total_tax[$j]['invoice_cgst_acc_id'] = null;
                    $total_tax[$j]['invoice_cgst_ledger_name'] = null;
                    $total_tax[$j]['invoice_cgst_ledger_code'] = null;
                    $total_tax[$j]['invoice_sgst_acc_id'] = null;
                    $total_tax[$j]['invoice_sgst_ledger_name'] = null;
                    $total_tax[$j]['invoice_sgst_ledger_code'] = null;
                    $total_tax[$j]['invoice_igst_acc_id'] = null;
                    $total_tax[$j]['invoice_igst_ledger_name'] = null;
                    $total_tax[$j]['invoice_igst_ledger_code'] = null;

                    // echo 'total_tax';
                    // echo '<br/>';

                    $warehouse_code = $result[$i]['warehouse_code'];
                    $state_name = '';
                    $result2 = $model->getState($warehouse_code);
                    if(count($result2)>0){
                        $state_name = $result2[0]['state_name'];
                    }

                    if($result[$i]['igst_rate']==0){
                        $vat_percent = $result[$i]['vat_percent'];
                        if(is_numeric($vat_percent)){
                            $vat_percent = floatval($vat_percent);
                        }
                        $tax_code = 'Sales-'.$state_name.'-Local-B2B-'.$vat_percent;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            // echo json_encode($result2);
                            // echo '<br/>';
                            $total_tax[$j]['invoice_cost_acc_id'] = $result2[0]['id'];
                            $total_tax[$j]['invoice_cost_ledger_name'] = $result2[0]['legal_name'];
                            $total_tax[$j]['invoice_cost_ledger_code'] = $result2[0]['code'];
                        }

                        $cgst_rate = $result[$i]['cgst_rate'];
                        if(is_numeric($cgst_rate)){
                            $cgst_rate = floatval($cgst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-CGST-'.$cgst_rate;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $total_tax[$j]['invoice_cgst_acc_id'] = $result2[0]['id'];
                            $total_tax[$j]['invoice_cgst_ledger_name'] = $result2[0]['legal_name'];
                            $total_tax[$j]['invoice_cgst_ledger_code'] = $result2[0]['code'];
                        }

                        $sgst_rate = $result[$i]['sgst_rate'];
                        if(is_numeric($sgst_rate)){
                            $sgst_rate = floatval($sgst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-SGST-'.$sgst_rate;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $total_tax[$j]['invoice_sgst_acc_id'] = $result2[0]['id'];
                            $total_tax[$j]['invoice_sgst_ledger_name'] = $result2[0]['legal_name'];
                            $total_tax[$j]['invoice_sgst_ledger_code'] = $result2[0]['code'];
                        }
                    } else {
                        $vat_percent = $result[$i]['vat_percent'];
                        if(is_numeric($vat_percent)){
                            $vat_percent = floatval($vat_percent);
                        }
                        $tax_code = 'Sales-'.$state_name.'-Inter State-B2B-'.$vat_percent;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $total_tax[$j]['invoice_cost_acc_id'] = $result2[0]['id'];
                            $total_tax[$j]['invoice_cost_ledger_name'] = $result2[0]['legal_name'];
                            $total_tax[$j]['invoice_cost_ledger_code'] = $result2[0]['code'];
                        }

                        $igst_rate = $result[$i]['igst_rate'];
                        if(is_numeric($igst_rate)){
                            $igst_rate = floatval($igst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-IGST-'.$result[$i]['igst_rate'];
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $total_tax[$j]['invoice_igst_acc_id'] = $result2[0]['id'];
                            $total_tax[$j]['invoice_igst_ledger_name'] = $result2[0]['legal_name'];
                            $total_tax[$j]['invoice_igst_ledger_code'] = $result2[0]['code'];
                        }
                    }
                }
                
                $blFlag = false;
                for($a=0;$a<count($invoice_details);$a++){
                    if($result[$i]['invoice_number']==$invoice_details[$a]['invoice_number'] ){

                        $blFlag = true;
                        $k = $a;
                    }
                }
                if($blFlag==true){
                    $invoice_details[$k]['invoice_total_cost'] = floatval($invoice_details[$k]['invoice_total_cost']) + $tot_cost;
                    $invoice_details[$k]['invoice_total_tax'] = floatval($invoice_details[$k]['invoice_total_tax']) + $tot_tax;
                    $invoice_details[$k]['invoice_total_amount'] = floatval($invoice_details[$k]['invoice_total_amount']) + $tot_cost + $tot_tax;

                    $invoice_details[$k]['invoice_total_payable_amount'] = $invoice_details[$k]['invoice_total_amount'];

                    $invoice_details[$k]['edited_total_cost'] = floatval($invoice_details[$k]['edited_total_cost']) + $tot_cost;
                    $invoice_details[$k]['edited_total_tax'] = floatval($invoice_details[$k]['edited_total_tax']) + $tot_tax;
                    $invoice_details[$k]['edited_total_amount'] = floatval($invoice_details[$k]['edited_total_amount']) + $tot_cost + $tot_tax;
                    $invoice_details[$k]['edited_total_payable_amount'] =$invoice_details[$k]['edited_total_amount'];
                    $invoice_details[$k]['diff_total_cost'] = 0;
                    $invoice_details[$k]['diff_total_tax'] = 0;
                    $invoice_details[$k]['diff_total_amount'] = 0;
                    $invoice_details[$k]['diff_total_payable_amount'] = 0;
                    $invoice_details[$k]['total_amount_voucher_id'] = null;
                    $invoice_details[$k]['total_amount_ledger_type'] = null;
                    $invoice_details[$k]['total_deduction_voucher_id'] = null;
                    $invoice_details[$k]['total_deduction_ledger_type'] = null;
                } else {
                    $k = count($invoice_details);
                    $invoice_details[$k]['invoice_number'] = $result[$i]['invoice_number'];
                    $invoice_details[$k]['invoice_total_cost'] = $tot_cost;
                    $invoice_details[$k]['invoice_total_tax'] = $tot_tax;
                     $invoice_details[$k]['invoice_other_charges'] = $other_charge;
                    $invoice_details[$k]['invoice_total_amount'] = $tot_cost + $tot_tax + $other_charge;
                    $invoice_details[$k]['invoice_total_payable_amount'] = $invoice_details[$k]['invoice_total_amount'] ;
                    $invoice_details[$k]['edited_total_cost'] = $tot_cost;
                    $invoice_details[$k]['edited_total_tax'] = $tot_tax;
                    $invoice_details[$k]['edited_total_amount'] = $tot_cost + $tot_tax + $other_charge;
                    $invoice_details[$k]['edited_total_payable_amount'] = $invoice_details[$k]['edited_total_amount'];
                    $invoice_details[$k]['diff_total_cost'] = 0;
                    $invoice_details[$k]['diff_total_tax'] = 0;
                    $invoice_details[$k]['diff_total_amount'] = 0;
                    $invoice_details[$k]['diff_total_payable_amount'] = 0;
                    $invoice_details[$k]['diff_other_charges'] = $other_charge;
                    $invoice_details[$k]['total_amount_voucher_id'] = null;
                    $invoice_details[$k]['total_amount_ledger_type'] = null;
                    $invoice_details[$k]['total_deduction_voucher_id'] = null;
                    $invoice_details[$k]['total_deduction_ledger_type'] = null;
                    
                    $total_val[0]['other_charges'] = $total_val[0]['other_charges'] + $other_charge;
                    $total_val[0]['total_amount'] = $total_val[0]['total_amount'] + 0;
                }

                $blFlag = false;
                for($a=0;$a<count($invoice_tax);$a++){
                    if($result[$i]['gi_go_id']==$invoice_tax[$a]['gi_go_id'] && $result[$i]['tax_zone_code']==$invoice_tax[$a]['tax_zone_code'] && 
                       $result[$i]['tax_zone_name']==$invoice_tax[$a]['tax_zone_name'] && $result[$i]['vat_cst']==$invoice_tax[$a]['vat_cst'] && 
                       $result[$i]['vat_percent']==$invoice_tax[$a]['vat_percent'] && $result[$i]['cgst_rate']==$invoice_tax[$a]['cgst_rate'] && 
                       $result[$i]['sgst_rate']==$invoice_tax[$a]['sgst_rate'] && $result[$i]['igst_rate']==$invoice_tax[$a]['igst_rate']  && 
                       $result[$i]['invoice_number']==$invoice_tax[$a]['invoice_number'] ){

                        $blFlag = true;
                        $l = $a;
                    }
                }
                if($blFlag==true){
                    $invoice_tax[$l]['total_cost'] = floatval($invoice_tax[$l]['total_cost']) + $tot_cost;
                    $invoice_tax[$l]['total_tax'] = floatval($invoice_tax[$l]['total_tax']) + $tot_tax;
                    $invoice_tax[$l]['total_cgst'] = floatval($invoice_tax[$l]['total_cgst']) + $tot_cgst;
                    $invoice_tax[$l]['total_sgst'] = floatval($invoice_tax[$l]['total_sgst']) + $tot_sgst;
                    $invoice_tax[$l]['total_igst'] = floatval($invoice_tax[$l]['total_igst']) + $tot_igst;
                    $invoice_tax[$l]['invoice_cost'] = floatval($invoice_tax[$l]['invoice_cost']) + $tot_cost;
                    $invoice_tax[$l]['invoice_tax'] = floatval($invoice_tax[$l]['invoice_tax']) + $tot_tax;
                    $invoice_tax[$l]['invoice_cgst'] = floatval($invoice_tax[$l]['invoice_cgst']) + $tot_cgst;
                    $invoice_tax[$l]['invoice_sgst'] = floatval($invoice_tax[$l]['invoice_sgst']) + $tot_sgst;
                    $invoice_tax[$l]['invoice_igst'] = floatval($invoice_tax[$l]['invoice_igst']) + $tot_igst;
                    $invoice_tax[$l]['edited_cost'] = floatval($invoice_tax[$l]['edited_cost']) + $tot_cost;
                    $invoice_tax[$l]['edited_tax'] = floatval($invoice_tax[$l]['edited_tax']) + $tot_tax;
                    $invoice_tax[$l]['edited_cgst'] = floatval($invoice_tax[$l]['edited_cgst']) + $tot_cgst;
                    $invoice_tax[$l]['edited_sgst'] = floatval($invoice_tax[$l]['edited_sgst']) + $tot_sgst;
                    $invoice_tax[$l]['edited_igst'] = floatval($invoice_tax[$l]['edited_igst']) + $tot_igst;
                    $invoice_tax[$l]['diff_cost'] = 0;
                    $invoice_tax[$l]['diff_tax'] = 0;
                    $invoice_tax[$l]['diff_cgst'] = 0;
                    $invoice_tax[$l]['diff_sgst'] = 0;
                    $invoice_tax[$l]['diff_igst'] = 0;
                    
                    // echo 'invoice_tax';
                    // echo '<br/>';

                    $warehouse_code = $result[$i]['warehouse_code'];
                    $state_name = '';
                    $result2 = $model->getState($warehouse_code);
                    if(count($result2)>0){
                        $state_name = $result2[0]['state_name'];
                    }

                    if($result[$i]['igst_rate']==0){
                        $vat_percent = $result[$i]['vat_percent'];
                        if(is_numeric($vat_percent)){
                            $vat_percent = floatval($vat_percent);
                        }
                        $tax_code = 'Sales-'.$state_name.'-Local-B2B-'.$vat_percent;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            // echo json_encode($result2);
                            // echo '<br/>';
                            $invoice_tax[$l]['invoice_cost_acc_id'] = $result2[0]['id'];
                            $invoice_tax[$l]['invoice_cost_ledger_name'] = $result2[0]['legal_name'];
                            $invoice_tax[$l]['invoice_cost_ledger_code'] = $result2[0]['code'];
                        }

                        $cgst_rate = $result[$i]['cgst_rate'];
                        if(is_numeric($cgst_rate)){
                            $cgst_rate = floatval($cgst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-CGST-'.$cgst_rate;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            // echo json_encode($result2);
                            // echo '<br/>';
                            $invoice_tax[$l]['invoice_cgst_acc_id'] = $result2[0]['id'];
                            $invoice_tax[$l]['invoice_cgst_ledger_name'] = $result2[0]['legal_name'];
                            $invoice_tax[$l]['invoice_cgst_ledger_code'] = $result2[0]['code'];
                        }

                        $sgst_rate = $result[$i]['sgst_rate'];
                        if(is_numeric($sgst_rate)){
                            $sgst_rate = floatval($sgst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-SGST-'.$sgst_rate;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            // echo json_encode($result2);
                            // echo '<br/>';
                            $invoice_tax[$l]['invoice_sgst_acc_id'] = $result2[0]['id'];
                            $invoice_tax[$l]['invoice_sgst_ledger_name'] = $result2[0]['legal_name'];
                            $invoice_tax[$l]['invoice_sgst_ledger_code'] = $result2[0]['code'];
                        }
                    } else {
                        $vat_percent = $result[$i]['vat_percent'];
                        if(is_numeric($vat_percent)){
                            $vat_percent = floatval($vat_percent);
                        }
                        $tax_code = 'Sales-'.$state_name.'-Inter State-B2B-'.$vat_percent;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $invoice_tax[$l]['invoice_cost_acc_id'] = $result2[0]['id'];
                            $invoice_tax[$l]['invoice_cost_ledger_name'] = $result2[0]['legal_name'];
                            $invoice_tax[$l]['invoice_cost_ledger_code'] = $result2[0]['code'];
                        }

                        $igst_rate = $result[$i]['igst_rate'];
                        if(is_numeric($igst_rate)){
                            $igst_rate = floatval($igst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-IGST-'.$igst_rate;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $invoice_tax[$l]['invoice_igst_acc_id'] = $result2[0]['id'];
                            $invoice_tax[$l]['invoice_igst_ledger_name'] = $result2[0]['legal_name'];
                            $invoice_tax[$l]['invoice_igst_ledger_code'] = $result2[0]['code'];
                        }
                    }
                } else {
                    $l = count($invoice_tax);
                    $invoice_tax[$l]['gi_go_id'] = $result[$i]['gi_go_id'];
                    $invoice_tax[$l]['tax_zone_code'] = $result[$i]['tax_zone_code'];
                    $invoice_tax[$l]['tax_zone_name'] = $result[$i]['tax_zone_name'];
                    $invoice_tax[$l]['invoice_number'] = $result[$i]['invoice_number'];
                    $invoice_tax[$l]['vat_cst'] = $result[$i]['vat_cst'];
                    $invoice_tax[$l]['vat_percent'] = $result[$i]['vat_percent'];
                    $invoice_tax[$l]['cgst_rate'] = $result[$i]['cgst_rate'];
                    $invoice_tax[$l]['sgst_rate'] = $result[$i]['sgst_rate'];
                    $invoice_tax[$l]['igst_rate'] = $result[$i]['igst_rate'];
                    $invoice_tax[$l]['total_cost'] = $tot_cost;
                    $invoice_tax[$l]['total_tax'] = $tot_tax;
                    $invoice_tax[$l]['total_cgst'] = $tot_cgst;
                    $invoice_tax[$l]['total_sgst'] = $tot_sgst;
                    $invoice_tax[$l]['total_igst'] = $tot_igst;
                    $invoice_tax[$l]['invoice_cost'] = $tot_cost;
                    $invoice_tax[$l]['invoice_tax'] = $tot_tax;
                    $invoice_tax[$l]['invoice_cgst'] = $tot_cgst;
                    $invoice_tax[$l]['invoice_sgst'] = $tot_sgst;
                    $invoice_tax[$l]['invoice_igst'] = $tot_igst;
                    $invoice_tax[$l]['edited_cost'] = $tot_cost;
                    $invoice_tax[$l]['edited_tax'] = $tot_tax;
                    $invoice_tax[$l]['edited_cgst'] = $tot_cgst;
                    $invoice_tax[$l]['edited_sgst'] = $tot_sgst;
                    $invoice_tax[$l]['edited_igst'] = $tot_igst;
                    $invoice_tax[$l]['diff_cost'] = 0;
                    $invoice_tax[$l]['diff_tax'] = 0;
                    $invoice_tax[$l]['diff_cgst'] = 0;
                    $invoice_tax[$l]['diff_sgst'] = 0;
                    $invoice_tax[$l]['diff_igst'] = 0;
                    
                    $invoice_tax[$l]['invoice_cost_acc_id'] = null;
                    $invoice_tax[$l]['invoice_cost_ledger_name'] = null;
                    $invoice_tax[$l]['invoice_cost_ledger_code'] = null;
                    $invoice_tax[$l]['invoice_tax_acc_id'] = null;
                    $invoice_tax[$l]['invoice_tax_ledger_name'] = null;
                    $invoice_tax[$l]['invoice_tax_ledger_code'] = null;
                    $invoice_tax[$l]['invoice_cgst_acc_id'] = null;
                    $invoice_tax[$l]['invoice_cgst_ledger_name'] = null;
                    $invoice_tax[$l]['invoice_cgst_ledger_code'] = null;
                    $invoice_tax[$l]['invoice_sgst_acc_id'] = null;
                    $invoice_tax[$l]['invoice_sgst_ledger_name'] = null;
                    $invoice_tax[$l]['invoice_sgst_ledger_code'] = null;
                    $invoice_tax[$l]['invoice_igst_acc_id'] = null;
                    $invoice_tax[$l]['invoice_igst_ledger_name'] = null;
                    $invoice_tax[$l]['invoice_igst_ledger_code'] = null;

                    // echo 'invoice_tax';
                    // echo '<br/>';

                    $warehouse_code = $result[$i]['warehouse_code'];
                    $state_name = '';
                    $result2 = $model->getState($warehouse_code);
                    if(count($result2)>0){
                        $state_name = $result2[0]['state_name'];
                    }

                    if($result[$i]['igst_rate']==0){
                        $vat_percent = $result[$i]['vat_percent'];
                        if(is_numeric($vat_percent)){
                            $vat_percent = floatval($vat_percent);
                        }
                        $tax_code = 'Sales-'.$state_name.'-Local-B2B-'.$vat_percent;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            // echo json_encode($result2);
                            // echo '<br/>';
                            $invoice_tax[$l]['invoice_cost_acc_id'] = $result2[0]['id'];
                            $invoice_tax[$l]['invoice_cost_ledger_name'] = $result2[0]['legal_name'];
                            $invoice_tax[$l]['invoice_cost_ledger_code'] = $result2[0]['code'];
                        }

                        $cgst_rate = $result[$i]['cgst_rate'];
                        if(is_numeric($cgst_rate)){
                            $cgst_rate = floatval($cgst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-CGST-'.$cgst_rate;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            // echo json_encode($result2);
                            // echo '<br/>';
                            $invoice_tax[$l]['invoice_cgst_acc_id'] = $result2[0]['id'];
                            $invoice_tax[$l]['invoice_cgst_ledger_name'] = $result2[0]['legal_name'];
                            $invoice_tax[$l]['invoice_cgst_ledger_code'] = $result2[0]['code'];
                        }

                        $sgst_rate = $result[$i]['sgst_rate'];
                        if(is_numeric($sgst_rate)){
                            $sgst_rate = floatval($sgst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-SGST-'.$sgst_rate;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            // echo json_encode($result2);
                            // echo '<br/>';
                            $invoice_tax[$l]['invoice_sgst_acc_id'] = $result2[0]['id'];
                            $invoice_tax[$l]['invoice_sgst_ledger_name'] = $result2[0]['legal_name'];
                            $invoice_tax[$l]['invoice_sgst_ledger_code'] = $result2[0]['code'];
                        }
                    } else {
                        $vat_percent = $result[$i]['vat_percent'];
                        if(is_numeric($vat_percent)){
                            $vat_percent = floatval($vat_percent);
                        }
                        $tax_code = 'Sales-'.$state_name.'-Inter State-B2B-'.$vat_percent;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $invoice_tax[$l]['invoice_cost_acc_id'] = $result2[0]['id'];
                            $invoice_tax[$l]['invoice_cost_ledger_name'] = $result2[0]['legal_name'];
                            $invoice_tax[$l]['invoice_cost_ledger_code'] = $result2[0]['code'];
                        }

                        $igst_rate = $result[$i]['igst_rate'];
                        if(is_numeric($igst_rate)){
                            $igst_rate = floatval($igst_rate);
                        }
                        $tax_code = 'Output-'.$state_name.'-IGST-'.$igst_rate;
                        // echo $tax_code;
                        // echo '<br/>';
                        $result2 = $model->getAccountDetails('','',$tax_code);
                        if(count($result2)>0){
                            $invoice_tax[$l]['invoice_igst_acc_id'] = $result2[0]['id'];
                            $invoice_tax[$l]['invoice_igst_ledger_name'] = $result2[0]['legal_name'];
                            $invoice_tax[$l]['invoice_igst_ledger_code'] = $result2[0]['code'];
                        }
                    }
                }
            }

            $total_val[0]['total_payable_amount'] = floatval($total_val[0]['total_amount']);
        }

        // echo json_encode($result);
        // echo '<br/><br/>';
        // echo json_encode($total_val);
        // echo '<br/><br/>';
        // echo json_encode($total_tax);
        // echo '<br/><br/>';
        // echo json_encode($invoice_details);
        // echo '<br/><br/>';
        // echo json_encode($invoice_tax);

        $data['total_val'] = $total_val;
        $data['total_tax'] = $total_tax;
        $data['invoice_details'] = $invoice_details;
        $data['invoice_tax'] = $invoice_tax;

        return $data;
    }

    public function actionRedirect($action, $id){
        $model = new PendingGo();
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $grn_entries = $model->getGrnAccEntries($id);
        $grn_details = $model->getGrnDetails($id);

        // $total_val = $model->getTotalValue($id);
        // $total_tax = $model->getTotalTax($id);

        $data = $this->actionGetgrnpostingdetails($id);


        $total_val = $data['total_val'];
        $total_tax = $data['total_tax'];

        // echo json_encode($data);

        $acc_master = $model->getAccountDetails('', 'approved');
        $tax_zone_code = '';//$grn_details[0]['vat_cst'];

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
                    $invoice_details[$num]['invoice_number'] = $invoice_no;
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
                            if($invoice_tax[$k]['invoice_number']==$grn_entries[$i]['invoice_no'] && 
                                $invoice_tax[$k]['vat_cst']==$grn_entries[$i]['vat_cst'] && 
                                $invoice_tax[$k]['vat_percent']==$grn_entries[$i]['vat_percen']){
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
                        $invoice_tax[$tax_num]['invoice_number'] = $grn_entries[$i]['invoice_no'];
                        $invoice_tax[$tax_num]['sub_particular_cost'] = $grn_entries[$i]['sub_particular'];
                        $invoice_tax[$tax_num]['vat_cst'] = $grn_entries[$i]['vat_cst'];
                        $invoice_tax[$tax_num]['vat_percent'] = $grn_entries[$i]['vat_percen'];

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
            // $invoice_details = $model->getInvoiceDetails($id);
            // $invoice_tax = $model->getInvoiceTaxDetails($id);

            $invoice_details = $data['invoice_details'];
            $invoice_tax = $data['invoice_tax'];

            // echo json_encode($invoice_tax);
            // echo '<br/>';

            for($i=0; $i<count($invoice_details); $i++) {
                $series = 2;
                $sql = "select * from acc_series_master where type = 'Voucher' and company_id = '$company_id'";
                $command = Yii::$app->db->createCommand($sql);
                $reader = $command->query();
                $data = $reader->readAll();
                if (count($data)>0){
                    $series = intval($data[0]['series']) + 2;

                    $sql = "update acc_series_master set series = '$series' where type = 'Voucher' and company_id = '$company_id'";
                    $command = Yii::$app->db->createCommand($sql);
                    $count = $command->execute();
                } else {
                    $series = 2;

                    $sql = "insert into acc_series_master (type, series, company_id) values ('Voucher', '".$series."', '".$company_id."')";
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

            $tax_code = 'Round Off';
            $result2 = $model->getAccountDetails('','',$tax_code);
            if(count($result2)>0){
                $acc['other_charges_acc_id'] = $result2[0]['id'];
                $acc['other_charges_ledger_name'] = $result2[0]['legal_name'];
                $acc['other_charges_ledger_code'] = $result2[0]['code'];
            }

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
        $deductions = [];
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
        $model = new PendingGo();

        $acc_ledger_entries = $model->getGrnAccLedgerEntries($id);
        $grn_details = $model->getGrnDetails($id);

        return $this->render('ledger', ['grn_details' => $grn_details, 'acc_ledger_entries' => $acc_ledger_entries]);
    }

    public function actionGetledger(){
        $model = new PendingGo();
        $mycomponent = Yii::$app->mycomponent;

        $data = $model->getGoParticulars();
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
        $model = new PendingGo();
        $mycomponent = Yii::$app->mycomponent;

        $gi_id = $request->post('gi_go_id');
        $invoice_no = $request->post('invoice_no');

        $data = $model->getGoParticulars();

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
        /*echo "<pre>";
        print_r($grnAccEntries);
        echo "</pre>";

        echo "<pre>";
        print_r($ledgerArray);
        echo "</pre>";*/


        if(count($bulkInsertArray)>0){
            $sql = "delete from acc_go_entries where gi_go_id = '$gi_id'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['gi_go_id','customer_id','particular','sub_particular','acc_id','ledger_name','ledger_code',
                                'voucher_id','ledger_type','vat_cst','vat_percen','invoice_no','total_val',
                                'invoice_val','edited_val','difference_val','narration','status','is_active',
                                'updated_by','updated_date', 'gi_date', 'company_id'];
            // below line insert all your record and return number of rows inserted
            $tableName = "acc_go_entries";
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
            $sql = "delete from acc_ledger_entries where ref_id = '$gi_id' and ref_type='B2B Sales'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['ref_id','ref_type','entry_type','invoice_no','customer_id','acc_id','ledger_name','ledger_code',
                                'voucher_id','ledger_type','type','amount','narration','status','is_active',
                                'updated_by','updated_date', 'ref_date', 'company_id'];
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

       /* if(count($grnAccEntries)>0){
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
                                'margin_diff_cgst','margin_diff_sgst','margin_diff_igst','margin_diff_tax','margin_diff_total','company_id'];
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
        }*/

        $this->redirect(array('pendinggo/ledger', 'id'=>$gi_id));
    }

    public function actionPendingGo(){
        $model = new PendingGo();
        $rows = $model->getPendingGo();
        
        if (count($rows)>0) {
            // echo $rows[0]->grn_id;

            return $this->render('entry-confirm', ['model' => $rows]);
        } else {
            // either the page is initially displayed or there is some validation error
            return $this->render('entry', ['model' => $model]);
        }
    }

    public function actionGetaccdetails(){
        $acc_master = new AccountMaster();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        $data = $acc_master->getAccountDetails($acc_id);
        echo json_encode($data);
    }

    public function actionGetgrndetails(){
        $grn = new PendingGo();
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
                <td>'.$grn[$i]["invoice_created_date"].'</td> 
                <td>'.$grn[$i]["status"].'</td> 
                <td><a href="' . Url::base() .'index.php?r=pendinggrn%2Fupdate&id='.$grn[$i]['grn_id'].'"> Post </a></td> 
            </tr>';
        }

        // return $result;
        echo json_encode($grn);
    }

    public function actionGetpendinggrndetails(){
        $grn = new PendingGo();
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

    public function actiongetGoParticulars(){
        $grn = new PendingGo();
        $data = $grn->getGoParticulars();

        echo json_encode($data);
    }
}