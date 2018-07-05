<?php

namespace app\controllers;

use Yii;
use app\models\Promotion;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class PromotionController extends Controller
{
    public function actionIndex(){
        $model = new Promotion();
        $access = $model->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $approved = $model->getPromotionDetails("", "approved");

                $model->setLog('Promotion', '', 'View', '', 'View Promotion List', 'acc_promotion_details', '');
                return $this->render('promotion_list', ['access' => $access, 'approved' => $approved]);
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

    public function actionCreate(){
        $model = new Promotion();
        $access = $model->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1) {
                $action = 'insert';
                $acc_details = $model->getAccountDetails();
                $vendor = $model->getVendors();
                $promotion_type = $model->getPromoTypes();
                $warehouse_gst = $model->getWarehouseDetails();

                $model->setLog('Promotion', '', 'Insert', '', 'Insert Promotion Details', 'acc_promotion_details', '');
                return $this->render('promotion_details', ['action' => $action, 'acc_details' => $acc_details, 
                                                            'vendor' => $vendor, 'promotion_type' => $promotion_type, 
                                                            'warehouse_gst' => $warehouse_gst]);
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

    public function actionRedirect($action, $id) {
        $model = new Promotion();
        $acc_details = $model->getAccountDetails();
        $vendor = $model->getVendors();
        $promotion_type = $model->getPromoTypes();
        $warehouse_gst = $model->getWarehouseDetails();
        $data = $model->getPromotionDetails($id, "");
        $promotion_entries = $model->gerPromotionEntries($id);
        // $approver_list = $model->getApprover($action);


        return $this->render('promotion_details', ['action' => $action, 'acc_details' => $acc_details, 
                                                    'vendor' => $vendor, 'promotion_type' => $promotion_type, 
                                                    'warehouse_gst' => $warehouse_gst, 'data' => $data, 
                                                    'promotion_entries' => $promotion_entries]);
    }

    public function actionView($id) {
        $model = new Promotion();
        $access = $model->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $model->setLog('Promotion', '', 'View', '', 'View Promotion Details', 'acc_promotion_details', $id);
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
        $model = new Promotion();
        $access = $model->getAccess();
        $data = $model->getPromotionDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_edit']==1 && $access[0]['session_id']!=$data[0]['approver_id']) {
                $model->setLog('Promotion', '', 'Edit', '', 'Edit Promotion Details', 'acc_promotion_details', $id);
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

    public function actionGetpromotypes(){
        $model = new Promotion();
        $request = Yii::$app->request;
        $vendor_id = $request->post('vendor_id');
        $debit_note_ref = $request->post('debit_note_ref');
        $data = $model->getPromoTypes($vendor_id, $debit_note_ref);

        $promotion_type = '<option value="">Select</option>';
        for($i=0; $i<count($data); $i++){
            $promotion_type = $promotion_type . '<option value="'.$data[$i]['promotion_type'].'">'.$data[$i]['promotion_type'].'</option>';
        }

        $result['promotion_type'] = $promotion_type;
        echo json_encode($result);
    }

    public function actionGetpromocodes(){
        $model = new Promotion();
        $request = Yii::$app->request;
        $id = $request->post('id');
        $vendor_id = $request->post('vendor_id');
        $promotion_type = $request->post('promotion_type');
        $debit_note_ref = $request->post('debit_note_ref');

        // $vendor_id = '504';
        // $promotion_type = 'Normal Promo';
        // $debit_note_ref = '1819/Jul/MH/016';

        $promotion_codes = '';
        $data = $model->getPromotionDetails($id, "");
        if(count($data)>0){
            $promotion_codes = $data[0]['promotion_code'];
        }


        $data = $model->getPromoCodes($vendor_id, $promotion_type, $debit_note_ref);
        $promotion_code = '';
        $selected='selected';
        for($i=0; $i<count($data); $i++){
            if(strpos($promotion_codes, $data[$i]['promotion_code'])!==false){
                $selected='selected';
            } else {
                $selected='';
            }
            $promotion_code = $promotion_code . '<option value="'.$data[$i]['promotion_code'].'" '.$selected.' >'.$data[$i]['promotion_code'].'</option>';
        }

        $result['promotion_code'] = $promotion_code;
        echo json_encode($result);
    }

    public function actionGetaccdetails(){
        $model = new Promotion();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        $data = $model->getAccountDetails($acc_id);
        echo json_encode($data);
    }

    public function actionGetdetails(){
        $model = new Promotion();
        $request = Yii::$app->request;
        $vendor_id = $request->post('vendor_id');
        $promotion_type = $request->post('promotion_type');
        $promotion_code = $request->post('promotion_code');
        $debit_note_ref = $request->post('debit_note_ref');
        $trans_type = $request->post('trans_type');
        $warehouse_id = $request->post('warehouse_id');

        // $vendor_id = '495';
        // $promotion_type = 'Advertisement';
        // $promotion_code = array('AMS-Aug17-Deemark');
        // $debit_note_ref = '';
        // $trans_type = 'Invoice';
        // $warehouse_id = '63';

        // $vendor_id = '495';
        // $promotion_type = "Advertisement";;
        // $promotion_code = array("AMS-Aug17-Deemark", "AMS-Jul17-Deemark");
        // $debit_note_ref = "";

        $acc_details = $model->getAccountDetails();
        $result = '';
        $data = $model->getDetails($vendor_id, $promotion_type, $promotion_code, $debit_note_ref);
        if(count($data)>0){
            $total_amount = $data[0]['total_amount'];
            // $total_amount = 1500;
            $acc_id = '';
            $tax_code = '';
            $vendor_details = $model->getVendorDetails($vendor_id);
            if(count($vendor_details)>0){
                $tax_code = $vendor_details[0]['vendor_name'];
                $result2 = $model->getAccountDetails('','',$tax_code);
                if(count($result2)>0){
                    $acc_id = $result2[0]['id'];
                }
            }

            $result = $result . $model->getTransaction($acc_details, 'Vendor Goods', $acc_id, '0', $total_amount, 'Debit');

            // $acc_master = '<option value="">Select</option>';
            // $account_name = '';
            // $account_code = '';
            // for($j=0; $j<count($acc_details); $j++) {
            //     if($acc_details[$j]['type']=='Vendor Goods'){
            //         if($vendor_id==$acc_details[$j]['id']){
            //             $acc_master = $acc_master . '<option value="'.$acc_details[$j]['id']'" selected>'.$acc_details[$j]['legal_name'].'</option>';
            //             $account_name = $acc_details[$j]['legal_name'];
            //             $account_code = $acc_details[$j]['code'];
            //         } else {
            //             $acc_master = $acc_master . '<option value="'.$acc_details[$j]['id']'">'.$acc_details[$j]['legal_name'].'</option>';
            //         }
            //     }
            // }

            // $result = '<tr id="row_0">
            //                 <td style="text-align: center;" class="action_delete"><button type="button" class="btn btn-sm btn-success" id="delete_row_0" onClick="delete_row(this);">-</button></td>
            //                 <td  style="text-align: center; display: none;" id="sr_no_0">1</td>
            //                 <td>
            //                     <input class="form-control" type="hidden" name="entry_id[]" id="entry_id_0" value="" />
            //                     <select class="form-control" name="acc_id[]" id="acc_id_0" onchange="get_acc_details(this);">
            //                         '.$acc_master.'
            //                     </select>
            //                     <input class="form-control" type="hidden" name="legal_name[]" id="legal_name_0" value="'.$account_name.'" />
            //                 </td>
            //                 <td><input class="form-control" type="text" name="acc_code[]" id="acc_code_0" value="'.$account_code.'" readonly /></td>
            //                 <td>
            //                     <select class="form-control" name="transaction[]" id="trans_0" onchange="set_transaction(this);">
            //                         <option value="">Select</option>
            //                         <option value="Debit" selected>Debit</option>
            //                         <option value="Credit">Credit</option>
            //                     </select>
            //                 </td>
            //                 <td><input class="form-control" type="text debit_amt" name="debit_amt[]" id="debit_amt_0" value="'.$mycomponent->format_money($total_amount,2).'" onChange="get_total();" /></td>
            //                 <td><input class="form-control" type="text credit_amt" name="credit_amt[]" id="credit_amt_0" value="" onChange="get_total();" readonly /></td>
            //             </tr>';

            $tax = 5;
            
            $warehouse_details = $model->getWarehouseDetails($warehouse_id);

            $vendor_state = '';
            $warehouse_state = '';

            if(count($vendor_details)>0){
                $vendor_state = $vendor_details[0]['state_name'];
            }
            if(count($warehouse_details)>0){
                $warehouse_state = $warehouse_details[0]['state_name'];
            }

            $cgst = $tax/2;
            $sgst = $tax/2;
            $igst = $tax;
            $tax_type = '';
            $purchase_amt = 0;
            $tax_amt = 0;
            $cgst_amt = 0;
            $sgst_amt = 0;
            $igst_amt = 0;
            if($vendor_state==$warehouse_state){
                $tax_type = 'Local';
                $purchase_amt = $total_amount/(1+($tax/100));
                $tax_amt = $total_amount - $purchase_amt;
                $cgst_amt = $tax_amt/2;
                $sgst_amt = $tax_amt/2;
            } else {
                $tax_type = 'Inter State';
                $purchase_amt = $total_amount/(1+($tax/100));
                $tax_amt = $total_amount - $purchase_amt;
                $igst_amt = $tax_amt;
            }

            $acc_id = '';
            $tax_code = 'Purchase-'.$warehouse_state.'-'.$tax_type.'-'.$tax;
            $result2 = $model->getAccountDetails('','',$tax_code);
            if(count($result2)>0){
                $acc_id = $result2[0]['id'];
            }
            if($trans_type == "Invoice"){
                $result = $result . $model->getTransaction($acc_details, 'Goods Purchase', $acc_id, '1', $purchase_amt, 'Credit');
            } else {
                $result = $result . $model->getTransaction($acc_details, 'Goods Purchase', $acc_id, '1', $total_amount, 'Credit');
            }
            
            if($trans_type=="Invoice"){
                if($tax_type == "Local"){
                    $acc_id = '';
                    $tax_code = 'Input-'.$warehouse_state.'-CGST-'.$cgst;
                    $result2 = $model->getAccountDetails('','',$tax_code);
                    if(count($result2)>0){
                        $acc_id = $result2[0]['id'];
                    }
                    $result = $result . $model->getTransaction($acc_details, 'CGST', $acc_id, '2', $cgst_amt, 'Credit');

                    $acc_id = '';
                    $tax_code = 'Input-'.$warehouse_state.'-SGST-'.$sgst;
                    $result2 = $model->getAccountDetails('','',$tax_code);
                    if(count($result2)>0){
                        $acc_id = $result2[0]['id'];
                    }
                    $result = $result . $model->getTransaction($acc_details, 'SGST', $acc_id, '3', $sgst_amt, 'Credit');
                } else {
                    $acc_id = '';
                    $tax_code = 'Input-'.$warehouse_state.'-IGST-'.$igst;
                    $result2 = $model->getAccountDetails('','',$tax_code);
                    if(count($result2)>0){
                        $acc_id = $result2[0]['id'];
                    }
                    $result = $result . $model->getTransaction($acc_details, 'IGST', $acc_id, '2', $igst_amt, 'Credit');
                }
            }
        }

        $final_data['result'] = $result;
        echo json_encode($final_data);
    }

    public function actionSave(){   
        $model = new Promotion();
        $result = $model->save();
        $this->redirect(array('promotion/index'));
    }

    public function actionViewtaxinvoice($id){
        $model = new Promotion();
        $data = $model->getDebitNoteDetails($id);
        
        $this->layout = false;
        return $this->render('tax_invoice', ['debit_note' => $data['debit_note'], 'vendor_details' => $data['vendor_details'], 
                                            'invoice_details' => $data['invoice_details'], 'inv_tax_details' => $data['inv_tax_details']]);
    }

    public function actionDownloadtaxinvoice($id){
        $model = new Promotion();
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

    public function actionEmailtaxinvoice($id){
        $model = new Promotion();
        $data = $model->getDebitNoteDetails($id);
        $file = "";

        return $this->render('email', ['debit_note' => $data['debit_note'], 'vendor_details' => $data['vendor_details'], 
                                        'invoice_details' => $data['invoice_details'], 'inv_tax_details' => $data['inv_tax_details']]);
    }

    public function actionViewdebitnote($id){
        $model = new Promotion();
        $data = $model->getDebitNoteDetails($id);
        
        $this->layout = false;
        return $this->render('debit_note', ['debit_note' => $data['debit_note'], 'vendor_details' => $data['vendor_details']]);
    }

    public function actionDownloaddebitnote($id){
        $model = new Promotion();
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
        $model = new Promotion();
        $data = $model->getDebitNoteDetails($id);
        $file = "";

        return $this->render('email', ['debit_note' => $data['debit_note'], 'vendor_details' => $data['vendor_details']]);
    }

    public function actionEmail(){
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $session = Yii::$app->session;

        $id = $request->post('id');
        // $grn_id = $request->post('grn_id');
        // $invoice_id = $request->post('invoice_id');
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
        // $data['grn_id'] = $grn_id;
        // $data['invoice_id'] = $invoice_id;

        
        $attachment_type = 'PDF';
        $vendor_name = $request->post('vendor_name');
        $company_id = $request->post('company_id');
        $model = new Promotion();
        $model->setEmailLog($vendor_name, $from, $to, $id, $body, $attachment, 
                                $attachment_type, $email_sent_status, $error_message, $company_id);

        return $this->render('email_response', ['data' => $data]);
    }

}
