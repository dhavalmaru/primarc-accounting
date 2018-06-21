<?php

namespace app\controllers;

use Yii;
use app\models\PendingGrn;
use app\models\GoodsOutward;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Session;

class GoodsoutwardController extends Controller
{
    public function actionIndex(){
        return $this->render('goods_outward_list');
    }

    public function actionGetgo(){
        $model = new GoodsOutward();
        $grn = $model->getNewGoDetails();
        $grn_count  = $model->getCountGoDetails();
        $request = Yii::$app->request;
        $grn_data = array();
        $mycomponent = Yii::$app->mycomponent;
        $start = $request->post('start');

        for($i=0; $i<count($grn); $i++) { 
            $row = array(
                        $start+1,
                        '<a href="'.Url::base() .'index.php?r=goodsoutward%2Fedit&id='.$grn[$i]['gi_go_id'].'" >Post </a>',
                        ''.$grn[$i]['gi_go_id'].'',
                        ''.$grn[$i]['gi_go_ref_no'].'',
                        ''.$grn[$i]['warehouse_name'].'',
                        ''.$grn[$i]['vendor_name'].'',
                        ''.$grn[$i]['idt_warehouse_name'].'',
                        ''.$mycomponent->format_money($grn[$i]['value_at_cost'], 2).'',
                        ''.$grn[$i]['gi_go_date_time'].'',
                        ''.$grn[$i]['updated_by'].''
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
        $model = new GoodsOutward();
        $grn = $model->getPostedGoDebitDetails('approved');
        $grn_count  = $model->getCountPostedGoDebitDetails('approved');
        $request = Yii::$app->request;
        $grn_data = array();
        $mycomponent = Yii::$app->mycomponent;
        $start = $request->post('start');    
        //$params['start'].", ".$params['length']
        for($i=0; $i<count($grn); $i++) { 
           $row = array(
                        $start+1,
                        '<a href="'.Url::base() .'index.php?r=goodsoutward%2Fview&id='.$grn[$i]['gi_go_id'].'" >View </a>
                        <a href="'.Url::base() .'index.php?r=goodsoutward%2Fedit&id='.$grn[$i]['gi_go_id'].'" style="'.($grn[$i]['is_paid']=='1'?'display: none;':'').'" >Edit </a>',
                        ''.$grn[$i]['gi_go_id'].'',
                        ''.$grn[$i]['gi_go_ref_no'].'',
                        ''.$grn[$i]['warehouse_name'].'',
                        ''.$grn[$i]['vendor_name'].'',
                        ''.$grn[$i]['idt_warehouse_name'].'',
                        ''.$mycomponent->format_money($grn[$i]['debit_amt'], 2).'',
                        ''.$grn[$i]['updated_date'].'',
                        ''.$grn[$i]['username'].'',
                        '<a href="'.Url::base() .'index.php?r=goodsoutward%2Fviewdebitnote&id='.$grn[$i]['gi_go_id'].'" target="_new">View </a>
                        <a href="'.Url::base() .'index.php?r=goodsoutward%2Fdownloaddebitnote&id='.$grn[$i]['gi_go_id'].'" target="_new">Download </a>
                        <a href="'.Url::base() .'index.php?r=goodsoutward%2Femaildebitnote&id='.$grn[$i]['gi_go_id'].'" >Email </a>'
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

    public function actionGetallgo(){
        $model = new GoodsOutward();
        $grn = $model->getAllGoDetails();
        $grn_count  = $model->getCountAllGoDetails();
        $request = Yii::$app->request;
        $grn_data = array();
        $mycomponent = Yii::$app->mycomponent;
        $start = $request->post('start');

        for($i=0; $i<count($grn); $i++) { 
            $row = array(
                        $start+1,
                        '<a href="'.Url::base() .'index.php?r=goodsoutward%2Fview&id='.$grn[$i]['gi_go_id'].'" >View </a>
                        <a href="'.Url::base() .'index.php?r=goodsoutward%2Fedit&id='.$grn[$i]['gi_go_id'].'" style="'.($grn[$i]['is_paid']=='1'?'display: none;':'').'" >Edit </a>',
                        ''.$grn[$i]['gi_go_id'].'',
                        ''.$grn[$i]['gi_go_ref_no'].'',
                        ''.$grn[$i]['warehouse_name'].'',
                        ''.$grn[$i]['vendor_name'].'',
                        ''.$grn[$i]['idt_warehouse_name'].'',
                        ''.$mycomponent->format_money($grn[$i]['value_at_cost'], 2).'',
                        ''.$grn[$i]['gi_go_date_time'].'',
                        ''.$grn[$i]['updated_by'].''
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

    public function actionView($id) {
        $model = new GoodsOutward();
        $access = $model->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $model->setLog('GoodsOutward', '', 'View', '', 'View Goods Outward Details', 'acc_grn_entries', $id);
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

    public function actionEdit($id) {
        $model = new GoodsOutward();
        $access = $model->getAccess();

        if(count($access)>0) {
            if($access[0]['r_edit']==1) {
                $model->setLog('GoodsOutward', '', 'Edit', '', 'Edit Goods Outward Details', 'acc_grn_entries', $id);
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

    public function actionTestref($value=''){
        $model = new GoodsOutward();
        $model->getDebitNoteRef();
    }

    public function actionRedirect($action, $id){
        $model = new GoodsOutward();

        $data = $model->getPostedGoDetails($id);
        $data_entries = array();

        if(count($data)>0){
            $data_entries = $model->getPostedGoEntries($id);

            $type_outward = trim($data[0]['type_outward']);
            $to_party = '';

            if($type_outward=='VENDOR'){
                $to_party = trim($data[0]['vendor_name']);
            }
            if($type_outward=='CUSTOMER'){
                $to_party = trim($data[0]['customerName']);
            }
            if($type_outward=='INTER-DEPOT'){
                $to_party = trim($data[0]['idt_warehouse_name']);
            }

            $data[0]['to_party'] = $to_party;

        } else {
            $data = $model->getGoDetails($id);

            if(count($data)>0){
                $action = "insert";
                $warehouse_state = trim($data[0]['warehouse_state']);
                $type_outward = trim($data[0]['type_outward']);
                $vendor_state = trim($data[0]['vendor_state']);
                $customerState = trim($data[0]['customerState']);
                $idt_warehouse_state = trim($data[0]['idt_warehouse_state']);
                $total_price_before_tax  = floatval($data[0]['total_price_before_tax']);
                $total_tax = floatval($data[0]['total_tax']);
                $total_other_charge = floatval($data[0]['total_other_charge']);
                $value_at_cost = floatval($data[0]['value_at_cost']);

                if($total_price_before_tax == null || $total_price_before_tax == ''){
                    $total_price_before_tax = 0;
                }
                if($total_tax == null || $total_tax == ''){
                    $total_tax = 0;
                }
                if($total_other_charge == null || $total_other_charge == ''){
                    $total_other_charge = 0;
                }
                if($value_at_cost == null || $value_at_cost == ''){
                    $value_at_cost = 0;
                }

                $data[0]['debit_amt'] = $value_at_cost;
                $data[0]['credit_amt'] = $value_at_cost;
                $data[0]['diff_amt'] = 0;

                $tax_percent = 0;
                $cgst = 0;
                $sgst = 0;
                $igst = 0;
                if($total_tax != 0 && $total_price_before_tax != 0){
                    $tax_percent = ($total_tax * 100)/$total_price_before_tax;
                }
                if($tax_percent<3){
                    $tax_percent = round($tax_percent,2);
                } else {
                    $tax_percent = round($tax_percent,0);
                }

                $from_ledger = '';
                $to_ledger = '';
                $cgst_ledger = '';
                $sgst_ledger = '';
                $igst_ledger = '';
                $other_ledger = '';
                $tax_type = '';
                $to_state = '';
                $to_party = '';

                if($type_outward=='VENDOR'){
                    $to_state = $vendor_state;
                    $to_party = trim($data[0]['vendor_name']);
                }
                if($type_outward=='CUSTOMER'){
                    $to_state = $customerState;
                    $to_party = trim($data[0]['customerName']);
                }
                if($type_outward=='INTER-DEPOT'){
                    $to_state = $idt_warehouse_state;
                    $to_party = trim($data[0]['idt_warehouse_name']);
                }

                $data[0]['to_party'] = $to_party;

                if(strtoupper($warehouse_state)==strtoupper($to_state)){
                    $tax_type = 'Local';
                } else {
                    $tax_type = 'Inter State';
                }

                if($tax_type == 'Local'){
                    $cgst = ($tax_percent/2);
                    $sgst = ($tax_percent/2);

                    if($cgst<3){
                        $cgst = round($cgst,2);
                    } else {
                        $cgst = round($cgst,0);
                    }
                    if($sgst<3){
                        $sgst = round($sgst,2);
                    } else {
                        $sgst = round($sgst,0);
                    }
                } else {
                    $igst = $tax_percent;
                }

                // echo $tax_type;
                // echo '<br/>';
                // echo $value_at_cost;
                // echo '<br/>';
                // echo $total_tax;
                // echo '<br/>';
                // echo $total_price_before_tax;
                // echo '<br/>';
                // echo $cgst;
                // echo '<br/>';
                // echo $sgst;
                // echo '<br/>';
                // echo $igst;
                // echo '<br/>';
                // echo $total_other_charge;
                // echo '<br/>';

                $i = 0;
                if($value_at_cost!=0){
                    $data_entries[$i]['acc_id'] = '';
                    $data_entries[$i]['ledger_name'] = '';
                    $data_entries[$i]['ledger_code'] = '';
                    $data_entries[$i]['debit_amt'] = $value_at_cost;
                    $data_entries[$i]['credit_amt'] = 0;
                    $data_entries[$i]['transaction'] = 'Debit';
                    $data_entries[$i]['ledger_type'] = 'Main Entry';

                    if($type_outward=='VENDOR'){
                        $to_ledger = $data[0]['vendor_name'];
                        $data_entries[$i]['acc_type'] = 'Vendor Goods';
                    } else {
                        $to_ledger = 'Purchase-'.$to_state.'-'.$tax_type.'-'.$tax_percent.'%';
                        $data_entries[$i]['acc_type'] = 'Goods Purchase';
                    }

                    $result2 = $model->getAccountDetails('','',$to_ledger);
                    if(count($result2)>0){
                        $data_entries[$i]['acc_id'] = $result2[0]['id'];
                        $data_entries[$i]['ledger_name'] = $result2[0]['legal_name'];
                        $data_entries[$i]['ledger_code'] = $result2[0]['code'];
                    }

                    $i = $i + 1;
                }

                if($total_tax == 0){
                    $data_entries[$i]['acc_id'] = '';
                    $data_entries[$i]['ledger_name'] = '';
                    $data_entries[$i]['ledger_code'] = '';
                    $data_entries[$i]['debit_amt'] = 0;
                    $data_entries[$i]['credit_amt'] = $value_at_cost;
                    $data_entries[$i]['transaction'] = 'Credit';
                    $data_entries[$i]['acc_type'] = 'Goods Purchase';
                    $data_entries[$i]['ledger_type'] = 'Sub Entry';

                    $from_ledger = 'Purchase-'.$warehouse_state.'-'.$tax_type.'-'.$tax_percent.'%';
                    $result2 = $model->getAccountDetails('','',$from_ledger);
                    if(count($result2)>0){
                        $data_entries[$i]['acc_id'] = $result2[0]['id'];
                        $data_entries[$i]['ledger_name'] = $result2[0]['legal_name'];
                        $data_entries[$i]['ledger_code'] = $result2[0]['code'];
                    }
                }
                if($total_price_before_tax!=0){
                    $data_entries[$i]['acc_id'] = '';
                    $data_entries[$i]['ledger_name'] = '';
                    $data_entries[$i]['ledger_code'] = '';
                    $data_entries[$i]['debit_amt'] = 0;
                    $data_entries[$i]['credit_amt'] = $total_price_before_tax;
                    $data_entries[$i]['transaction'] = 'Credit';
                    $data_entries[$i]['acc_type'] = 'Goods Purchase';
                    $data_entries[$i]['ledger_type'] = 'Sub Entry';

                    $from_ledger = 'Purchase-'.$warehouse_state.'-'.$tax_type.'-'.$tax_percent.'%';
                    $result2 = $model->getAccountDetails('','',$from_ledger);
                    if(count($result2)>0){
                        $data_entries[$i]['acc_id'] = $result2[0]['id'];
                        $data_entries[$i]['ledger_name'] = $result2[0]['legal_name'];
                        $data_entries[$i]['ledger_code'] = $result2[0]['code'];
                    }

                    $i = $i + 1;
                }

                if($cgst!=0){
                    $data_entries[$i]['acc_id'] = '';
                    $data_entries[$i]['ledger_name'] = '';
                    $data_entries[$i]['ledger_code'] = '';
                    $data_entries[$i]['debit_amt'] = 0;
                    $data_entries[$i]['credit_amt'] = $cgst;
                    $data_entries[$i]['transaction'] = 'Credit';
                    $data_entries[$i]['acc_type'] = 'CGST';
                    $data_entries[$i]['ledger_type'] = 'Sub Entry';
                    
                    $cgst_ledger = 'Input-'.$warehouse_state.'-CGST-'.$cgst.'%';
                    $result2 = $model->getAccountDetails('','',$cgst_ledger);
                    if(count($result2)>0){
                        $data_entries[$i]['acc_id'] = $result2[0]['id'];
                        $data_entries[$i]['ledger_name'] = $result2[0]['legal_name'];
                        $data_entries[$i]['ledger_code'] = $result2[0]['code'];
                    }
                        
                    $i = $i + 1;
                }
                if($sgst!=0){
                    $data_entries[$i]['acc_id'] = '';
                    $data_entries[$i]['ledger_name'] = '';
                    $data_entries[$i]['ledger_code'] = '';
                    $data_entries[$i]['debit_amt'] = 0;
                    $data_entries[$i]['credit_amt'] = $sgst;
                    $data_entries[$i]['transaction'] = 'Credit';
                    $data_entries[$i]['acc_type'] = 'SGST';
                    $data_entries[$i]['ledger_type'] = 'Sub Entry';
                    
                    $sgst_ledger = 'Input-'.$warehouse_state.'-SGST-'.$sgst.'%';
                    $result2 = $model->getAccountDetails('','',$sgst_ledger);
                    if(count($result2)>0){
                        $data_entries[$i]['acc_id'] = $result2[0]['id'];
                        $data_entries[$i]['ledger_name'] = $result2[0]['legal_name'];
                        $data_entries[$i]['ledger_code'] = $result2[0]['code'];
                    }
                        
                    $i = $i + 1;
                }
                if($igst!=0){
                    $data_entries[$i]['acc_id'] = '';
                    $data_entries[$i]['ledger_name'] = '';
                    $data_entries[$i]['ledger_code'] = '';
                    $data_entries[$i]['debit_amt'] = 0;
                    $data_entries[$i]['credit_amt'] = $igst;
                    $data_entries[$i]['transaction'] = 'Credit';
                    $data_entries[$i]['acc_type'] = 'IGST';
                    $data_entries[$i]['ledger_type'] = 'Sub Entry';
                    
                    $igst_ledger = 'Input-'.$warehouse_state.'-IGST-'.$igst.'%';
                    $result2 = $model->getAccountDetails('','',$igst_ledger);
                    if(count($result2)>0){
                        $data_entries[$i]['acc_id'] = $result2[0]['id'];
                        $data_entries[$i]['ledger_name'] = $result2[0]['legal_name'];
                        $data_entries[$i]['ledger_code'] = $result2[0]['code'];
                    }
                    
                    $i = $i + 1;
                }
                if($total_other_charge!=0){
                    $data_entries[$i]['acc_id'] = '';
                    $data_entries[$i]['ledger_name'] = '';
                    $data_entries[$i]['ledger_code'] = '';
                    $data_entries[$i]['debit_amt'] = 0;
                    $data_entries[$i]['credit_amt'] = $total_other_charge;
                    $data_entries[$i]['transaction'] = 'Credit';
                    $data_entries[$i]['acc_type'] = 'Others';
                    $data_entries[$i]['ledger_type'] = 'Sub Entry';
                    
                    $other_ledger = 'Profit And Loss A/c';
                    $result2 = $model->getAccountDetails('','',$other_ledger);
                    if(count($result2)>0){
                        $data_entries[$i]['acc_id'] = $result2[0]['id'];
                        $data_entries[$i]['ledger_name'] = $result2[0]['legal_name'];
                        $data_entries[$i]['ledger_code'] = $result2[0]['code'];
                    }
                        
                    $i = $i + 1;
                }
            }
        }

        // echo json_encode($data_entries);
        // echo '<br/>';

        $acc_master = $model->getAccountDetails('', 'approved');

        return $this->render('goods_outward_details', ['data'=>$data, 'acc_master'=>$acc_master, 
                                                        'data_entries'=>$data_entries, 'action'=>$action]);
    }

    public function actionGetaccdetails(){
        $model = new GoodsOutward();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        $data = $model->getAccountDetails($acc_id);
        echo json_encode($data);
    }

    public function actionSave(){   
        $model = new GoodsOutward();
        $transaction_id = $model->save();
        // $this->redirect(array('goodsoutward/ledger', 'transaction_id'=>$transaction_id));
        $this->redirect(array('goodsoutward/index'));
    }

    public function actionLedger($transaction_id){
        $model = new GoodsOutward();
        $ledger = $model->getLedger($transaction_id);
        return $this->render('goodsoutward_ledger', ['ledger' => $ledger]);
    }

    public function actionViewdebitnote($id){
        $model = new GoodsOutward();
        $data = $model->getDebitNoteDetails($id);

        $this->layout = false;
        return $this->render('debit_note', ['debit_note' => $data['debit_note'], 'go_details' => $data['go_details'], 
                                            'total_amt' => $data['total_amt'], 'amt_without_tax' => $data['amt_without_tax'], 
                                            'cgst_amt' => $data['cgst_amt'], 'sgst_amt' => $data['sgst_amt'], 
                                            'igst_amt' => $data['igst_amt']]);
    }

    public function actionDownloaddebitnote($id){
        $model = new GoodsOutward();
        $data = $model->getDebitNoteDetails($id);
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

    public function actionEmaildebitnote($id){
        $model = new GoodsOutward();
        $data = $model->getDebitNoteDetails($id);
        $file = "";

        return $this->render('email', ['debit_note' => $data['debit_note'], 'go_details' => $data['go_details'], 
                                        'total_amt' => $data['total_amt'], 'amt_without_tax' => $data['amt_without_tax'], 
                                        'cgst_amt' => $data['cgst_amt'], 'sgst_amt' => $data['sgst_amt'], 
                                        'igst_amt' => $data['igst_amt']]);
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
        $model = new PendingGrn();
        $model->setEmailLog($vendor_name, $from, $to, $id, $body, $attachment, 
                                $attachment_type, $email_sent_status, $error_message, $company_id);

        return $this->render('email_response', ['data' => $data]);
    }

}