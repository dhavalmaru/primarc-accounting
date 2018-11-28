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
                        ''.$mycomponent->format_money($grn[$i]['total_amount'], 2).'',
                        ''.$grn[$i]['gi_go_final_commit_date'].'',
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
                        ''.$mycomponent->format_money($grn[$i]['total_amount'], 2).'',
                       ''.$grn[$i]['gi_go_final_commit_date'].'',
                        ''.$grn[$i]['username'].'',
                        '<a href="'.Url::base() .'index.php?r=goodsoutward%2Fledger&id='.$grn[$i]['gi_go_id'].'" target="_new"> <span class="fa fa-file-pdf-o"></span> </a>'
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

    public function actionGetallgo  (){
        $model = new GoodsOutward();
        $grn = $model->getAllGoDetails();
        $grn_count  = $model->getCountAllGoDetails();
        $request = Yii::$app->request;
        $grn_data = array();
        $mycomponent = Yii::$app->mycomponent;
        $start = $request->post('start');

        for($i=0; $i<count($grn); $i++) {
            $link = '';
            if($grn[$i]['go_status']=='GRN Not Posted') {
                $link = '';
            } else if($grn[$i]['go_status']=='GRN Posted & GO Balance') {
                $link = '<a href="'.Url::base() .'index.php?r=goodsoutward%2Fedit&id='.$grn[$i]['gi_go_id'].'" >Post </a>';
            } else if($grn[$i]['go_status']=='GO Posted') {
                $link = '<a href="'.Url::base() .'index.php?r=goodsoutward%2Fview&id='.$grn[$i]['gi_go_id'].'" >View </a>
                        <a href="'.Url::base() .'index.php?r=goodsoutward%2Fedit&id='.$grn[$i]['gi_go_id'].'" style="'.($grn[$i]['is_paid']=='1'?'display: none;':'').'" >Edit </a>';
            }

            $row = array(
                        $start+1,
                        $link,
                        ''.$grn[$i]['gi_go_id'].'',
                        ''.$grn[$i]['gi_go_ref_no'].'',
                        ''.$grn[$i]['grn_no'].'',
                        ''.$grn[$i]['warehouse_name'].'',
                        ''.$grn[$i]['vendor_name'].'',
                        ''.$mycomponent->format_money($grn[$i]['total_amount'], 2).'',
                        ''.$grn[$i]['gi_go_final_commit_date'].'',
                        ''.$grn[$i]['updated_by'].'',
                        ''.$grn[$i]['go_status'].''
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

    /*public function actionRedirect($action, $id){
        $model = new GoodsOutward();

        $data = $model->getPostedGoDetails($id);
        echo "<pre>";
        print_r($data);
        echo "</pre>";
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
    }*/


    public function actionRedirect($action, $id){
        $model = new GoodsOutward();
        $session = Yii::$app->session;
        $company_id = $session['company_id'];

        $tax_per = $model->getTaxPercent();
        $grn_entries = $model->getGrnAccEntries($id);
        $grn_details = $model->getGrnDetails($id);

        // $total_val = $model->getTotalValue($id);
        // $total_tax = $model->getTotalTax($id);/*

        $gi_go_id = $grn_details[0]['pre_go_ref'];
        $resulsku = $model->getskugoItems($gi_go_id);

        if($resulsku[0]['skucount']>0) {
            $skuentries=true;
        } else {
            $skuentries=false;  
        }

        $data = $this->actionGetgrnpostingdetails($id, $skuentries);

        $total_val =  $data['total_val'];
        $total_tax =  $data['total_tax'];
        $tax_percent = $data['tax_percent'];
        $skuwise = $data['skuwise'];        
        // echo json_encode($data);
        $ware_array = array();        
        $acc_master = $model->getAccountDetails('', 'approved');
        $tax_zone_code = '';//$grn_details[0]['vat_cst'];

        if(count($grn_details)>0) {
            if($grn_details[0]['tax_zone_code']=='INTRA') {
                $tax_type = 'Local';
            } else {
                $tax_type = 'Inter State';
            }

            $grn_details[0]['warehouse_id'];   
            if($grn_details[0]['warehouse_id']!="") {
                if(is_numeric($tax_percent)){
                    $tax_percent = floatval($tax_percent);
                }

                $tax_code = 'Purchase-'.$grn_details[0]['to_state'].'-'.$tax_type.'-'.$tax_percent.'%';
                $result2 = $model->getAccountDetails('','',$tax_code);
               
                /*echo "<pre>";
                print_r($result2);
                echo "</pre>";*/ 

                if(count($result2)>0){
                   $ware_array['total_amount_acc_id'] = $result2[0]['id'];
                   $ware_array['total_amount_ledger_name'] = $result2[0]['legal_name'];
                   $ware_array['total_amount_ledger_code'] = $result2[0]['code'];
                } else {
                    $ware_array['total_amount_acc_id'] = '';
                    $ware_array['total_amount_ledger_name'] = '';
                    $ware_array['total_amount_ledger_code'] = '';
                }
            }
        }

        if (count($grn_entries) > 0){
            // echo json_encode($grn_entries);

          

            $num = -1;
            $prev_invoice_no = "---";
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
                else
                {
                    /*$invoice_details[$num] = array();
                    $invoice_details[$num]['invoice_number'] = '';*/
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
            return $this->render('update', ['ware_array'=>$ware_array,'grn_details' => $grn_details, 'total_val' => $total_val, 'total_tax' => $total_tax, 
                                'invoice_details' => $invoice_details, 'invoice_tax' => $invoice_tax, 'narration' => $narration, 
                                'deductions' => $deductions, 'acc_master' => $acc_master, 'acc' => $acc, 
                                'debit_note' => $debit_note, 'action' => $action, 'skuwise' => $skuwise,'tax_per'=>$tax_per]);
        }

        // echo json_encode($invoice_details);
        // echo json_encode($total_tax);
        // echo json_encode($invoice_tax);
        // echo json_encode($deductions['margindiff']);
        // echo $deductions['shortage'];
    }

    public function actionGetgrnpostingdetails($id,$skuentries=''){
        $total_val = array();
        $total_tax = array();
        $invoice_details = array();
        $invoice_tax = array();

        $model = new GoodsOutward();
        $result = $model->getGrnPostingDetails($id,$skuentries);
        // $result_skuentries = $model->getGrnskuDetails($id,$skuentries);
        $tax_percent = ""   ;

        if(count($result)>0){
            // $data['skuwise'] = $result_skuentries;
            $data['skuwise'] = $result;
            $tax_percent = $result[0]['vat_percent'];
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
                $cost_incl_vat_cst = floatval($result[$i]['value_at_mrp']);
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
                        $tax_code = 'Purchase-'.$state_name.'-Local-'.$vat_percent;
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
                        $tax_code = 'Input-'.$state_name.'-CGST-'.$cgst_rate;
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
                        $tax_code = 'Input-'.$state_name.'-SGST-'.$sgst_rate;
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
                        $tax_code = 'Purchase-'.$state_name.'-Inter State-'.$vat_percent;
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
                        $tax_code = 'Input-'.$state_name.'-IGST-'.$result[$i]['igst_rate'];
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
                        $tax_code = 'Purchase-'.$state_name.'-Local-'.$vat_percent;
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
                        $tax_code = 'Input-'.$state_name.'-CGST-'.$cgst_rate;
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
                        $tax_code = 'Input-'.$state_name.'-SGST-'.$sgst_rate;
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
                        $tax_code = 'Purchase-'.$state_name.'-Inter State-'.$vat_percent;
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
                        $tax_code = 'Input-'.$state_name.'-IGST-'.$result[$i]['igst_rate'];
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
                        $tax_code = 'Purchase-'.$state_name.'-Local-'.$vat_percent;
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
                        $tax_code = 'Input-'.$state_name.'-CGST-'.$cgst_rate;
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
                        $tax_code = 'Input-'.$state_name.'-SGST-'.$sgst_rate;
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
                        $tax_code = 'Purchase-'.$state_name.'-Inter State-'.$vat_percent;
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
                        $tax_code = 'Input-'.$state_name.'-IGST-'.$igst_rate;
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
                        $tax_code = 'Purchase-'.$state_name.'-Local-'.$vat_percent;
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
                        $tax_code = 'Input-'.$state_name.'-CGST-'.$cgst_rate;
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
                        $tax_code = 'Input-'.$state_name.'-SGST-'.$sgst_rate;
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
                        $tax_code = 'Purchase-'.$state_name.'-Inter State-'.$vat_percent;
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
                        $tax_code = 'Input-'.$state_name.'-IGST-'.$igst_rate;
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
        $data['tax_percent'] = $tax_percent;
        $data['invoice_details'] = $invoice_details;
        $data['invoice_tax'] = $invoice_tax;
        // $data['result_skuentries'] = $result_skuentries;
        return $data;
    }

    public function get_skuEntires() {
        $model = new GoodsOutward();
        $request = Yii::$app->request;
    }
    
    public function actionGetaccdetails(){
        $model = new GoodsOutward();
        $request = Yii::$app->request;
        $acc_id = $request->post('acc_id');
        $data = $model->getAccountDetails($acc_id);
        echo json_encode($data);
    }

   /* public function actionSave(){   
        $model = new GoodsOutward();
        $transaction_id = $model->save();
        // $this->redirect(array('goodsoutward/ledger', 'transaction_id'=>$transaction_id));
        //$this->redirect(array('goodsoutward/index'));
    }*/



    public function actionSave(){
        $request = Yii::$app->request;
        $model = new GoodsOutward();
        $mycomponent = Yii::$app->mycomponent;

        $gi_id = $request->post('gi_go_id');
        $invoice_no = $request->post('invoice_no');
        $goskuentries = $model->set_goskuentries();
        $data = $model->getGoParticulars();

        $bulkInsertArray = $data['bulkInsertArray'];
        $grnAccEntries = $data['grnAccEntries'];
        $ledgerArray = $data['ledgerArray'];




        if(count($bulkInsertArray)>0){
            $sql = "delete from acc_go_debit_entries where gi_go_id = '$gi_id'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['gi_go_id','vendor_id','particular','sub_particular','acc_id','ledger_name','ledger_code',
                                'voucher_id','ledger_type','vat_cst','vat_percen','invoice_no','total_val',
                                'invoice_val','edited_val','difference_val','narration','status','is_active',
                                'updated_by','updated_date', 'gi_date', 'company_id','warehouse_no'];
            // below line insert all your record and return number of rows inserted
            $tableName = "acc_go_debit_entries";
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
            $sql = "delete from acc_ledger_entries where ref_id = '$gi_id' and ref_type='go_debit_details'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['ref_id','ref_type','entry_type','invoice_no','vendor_id','acc_id','ledger_name','ledger_code',
                                'voucher_id','ledger_type','type','amount','narration','status','is_active',
                                'updated_by','updated_date', 'ref_date', 'company_id','warehouse_no'];
            // below line insert all your record and return number of rows inserted
            $tableName = "acc_ledger_entries";
            $insertCount = Yii::$app->db->createCommand()
                           ->batchInsert(
                                 $tableName, $columnNameArray, $ledgerArray
                             )
                           ->execute();

        }


        $this->redirect(array('goodsoutward/ledger', 'id'=>$gi_id));
    }

    public function actionLedger($id){
        $model = new GoodsOutward();

        $acc_ledger_entries = $model->getGrnAccLedgerEntries($id);
        $grn_details = $model->getGrnDetails($id);

        return $this->render('ledger', ['grn_details' => $grn_details, 'acc_ledger_entries' => $acc_ledger_entries]);
    }

    public function actionGetledger(){
        $model = new GoodsOutward();
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

    /*public function actionLedger($transaction_id){
        $model = new GoodsOutward();
        $ledger = $model->getLedger($transaction_id);
        return $this->render('goodsoutward_ledger', ['ledger' => $ledger]);
    }*/

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


    public function actionGetgoodoutwards()
    {                   
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $model = new GoodsOutward();
        $state_name = $request->post('location');
        $cgst_rate = $request->post('cgst_rate');
        $sgst_rate = $request->post('sgst_rate');
        $igst_rate = $request->post('igs_rate');
        $sku_sgst = $request->post('sku_sgst');
        $sku_cgst = $request->post('sku_cgst');
        $sku_igst = $request->post('sku_igst');
        $tax_zone = $request->post('taz_zone');
        $sku_cost = $request->post('sku_cost');
        $total = $request->post('total');
        $j = $request->post('j_count');
        $vat_percent = $request->post('vat_percen');
        $tax_total_ = $sku_sgst+$sku_cgst+$sku_igst ;
        $acc_master = $model->getAccountDetails('', 'approved');

        $inv_num = 0;
        $tableamount = '';
        if($tax_zone=='INTRA')
        {
            $total_tax_code = 'Purchase-'.$state_name.'-Local-'.$vat_percent.'%';
            $result_total_tax = $model->getAccountDetails('','',$total_tax_code);
            if(count($result_total_tax)>0){
                $total_vat_id = $result_total_tax[0]['id'];
                $total_vat_legal_name  = $result_total_tax[0]['legal_name'];
                $total_vat_ledger_code = $result_total_tax[0]['code'];
            }
            else
            {
                $total_vat_id = '';
                $total_vat_legal_name = '';
                $total_vat_ledger_code = '';
            }

            $cgst_tax_code = 'Input-'.$state_name.'-CGST-'.$cgst_rate.'%';
            $result_cgst_tax = $model->getAccountDetails('','',$cgst_tax_code);
            if(count($result_cgst_tax)>0){
                $cgst_tax = $result_cgst_tax[0]['id'];
                $cgst_legal_name  = $result_cgst_tax[0]['legal_name'];
                $cgst_vat_ledger_code = $result_cgst_tax[0]['code'];
            }
            else
            {
                $cgst_tax ='';
                $cgst_legal_name='';
                $cgst_vat_ledger_code = '';
            }

            $sgst_tax_code = 'Input-'.$state_name.'-SGST-'.$sgst_rate.'%';
            $result_sgst_tax = $model->getAccountDetails('','',$sgst_tax_code);
            if(count($result_cgst_tax)>0){
                $sgst_tax = $result_sgst_tax[0]['id'];
                $sgst_legal_name  = $result_sgst_tax[0]['legal_name'];
                $sgst_vat_ledger_code = $result_sgst_tax[0]['code'];
            }
            else
            {
                $sgst_tax ='';
                $sgst_legal_name='';
                $sgst_vat_ledger_code = '';
            }

             $igst_tax='';
            $igst_legal_name='';
            $igst_vat_ledger_code = '';   
        }
        else
        {
            $total_tax_code = 'Purchase-'.$state_name.'-Inter State-'.$vat_percent.'%';
            $result_total_tax = $model->getAccountDetails('','',$total_tax_code);
            if(count($result_total_tax)>0){
                $total_vat_id = $result_total_tax[0]['id'];
                $total_vat_legal_name  = $result_total_tax[0]['legal_name'];
                $total_vat_ledger_code = $result_total_tax[0]['code'];
            }
            else
            {
                $total_vat_id = '';
                $total_vat_legal_name = '';
                $total_vat_ledger_code = '';
            }
            
            $igst_tax_code = 'Input-'.$state_name.'-IGST-'.$igst_rate.'%';
             $result_igst_tax = $model->getAccountDetails('','',$igst_tax_code);
            if(count($result_igst_tax)>0){
                $igst_tax = $result_igst_tax[0]['id'];
                $igst_legal_name  = $result_igst_tax[0]['legal_name'];
                $igst_vat_ledger_code = $result_igst_tax[0]['code'];
            }
            else
            {
                $igst_tax='';
                $igst_legal_name='';
                $igst_vat_ledger_code = '';
            } 

            $cgst_tax ='';
            $cgst_legal_name='';
            $cgst_vat_ledger_code = '';
            $sgst_tax ='';
            $sgst_legal_name='';
            $sgst_vat_ledger_code = '';   
        }

            $j = $j;
            $inv_num = 0; 
            $invoice_cost_td = ''; 
            $invoice_tax_td = ''; 
            $invoice_cgst_td = ''; 
            $invoice_sgst_td = ''; 
            $invoice_igst_td = '';
            $intra_state_style = "";
            $inter_state_style = "";

            if($tax_zone=="INTRA"){
                $inter_state_style = "display:none;";
            } else {
                $intra_state_style = "display:none;";
            }
            $k=0;
                           
           /* floatval($total_tax[$j]['vat_percent']) == floatval($invoice_tax[$i]['vat_percent'])) {*/
            $total_tax = [];
            $total_tax[$j]['invoice_cost_acc_id']=$total_vat_id;
            $total_tax[$j]['invoice_cost_ledger_name']=$total_vat_legal_name;
            $total_tax[$j]['invoice_cost_ledger_code']=$total_vat_ledger_code;
            $total_tax[$j]['invoice_tax_acc_id']='';
            $total_tax[$j]['invoice_tax_ledger_name']='';
            $total_tax[$j]['invoice_tax_ledger_code']='';
            $total_tax[$j]['invoice_cgst_acc_id']=$cgst_tax;
            $total_tax[$j]['invoice_cgst_ledger_name']=$cgst_legal_name;
            $total_tax[$j]['invoice_cgst_ledger_code']=$cgst_vat_ledger_code;
            $total_tax[$j]['invoice_sgst_acc_id']=$sgst_tax;
            $total_tax[$j]['invoice_sgst_ledger_name']=$sgst_legal_name;
            $total_tax[$j]['invoice_sgst_ledger_code']=$sgst_vat_ledger_code;
            $total_tax[$j]['invoice_igst_acc_id']=$igst_tax;
            $total_tax[$j]['invoice_igst_ledger_name']=$igst_legal_name;
            $total_tax[$j]['invoice_igst_ledger_code']=$igst_vat_ledger_code;
            $total_tax[$j]['vat_cst']=$tax_zone;
            $total_tax[$j]['vat_percent']=$vat_percent;
            $total_tax[$j]['cgst_rate']=$cgst_rate;
            $total_tax[$j]['sgst_rate']=$sgst_rate;
            $total_tax[$j]['igst_rate']=$igst_rate;
            $total_tax[$j]['tax_zone_code'] =$tax_zone;
            $total_tax[$j]['total_cost'] = $total;
            $total_tax[$j]['total_tax'] = $tax_total_;
            $total_tax[$j]['total_cgst'] = $sku_cgst;
            $total_tax[$j]['total_sgst'] = $sku_sgst;
            $total_tax[$j]['total_igst'] = $sku_igst;
            /*echo "<pre>";
            print_r($total_tax);
            echo "</pre>";*/
            
            $td = '<td>
                        <input type="text" class="text-right" id="invoice_'.$k.'_cost_'.$j.'" name="invoice_cost_'.$j.'[]" value="0" readonly />
                        <input type="hidden" id="invoice_'.$k.'_cost_voucher_id_'.$j.'" name="invoice_cost_voucher_id_'.$j.'[]" value="" />
                        <input type="hidden" id="invoice_'.$k.'_cost_ledger_type_'.$j.'" name="invoice_cost_ledger_type_'.$j.'[]" value="Sub Entry" />
                    </td>
                    <td>
                        <input type="text" class="text-right edited-cost edit-text" id="edited_'.$k.'_cost_'.$j.'" name="edited_cost_'.$j.'[]" value="'.$mycomponent->format_money($sku_cost, 2).'" onChange="getDifference(this);" />
                    </td>
                    <td>
                        <input type="text" class="text-right diff" id="diff_'.$k.'_cost_'.$j.'" name="diff_cost_'.$j.'[]" value="0.00" readonly />
                    </td>';
            $invoice_cost_td = $invoice_cost_td . $td;

            $td = '<td>
                        <input type="text" class="text-right" id="invoice_'.$k.'_tax_'.$j.'" name="invoice_tax_'.$j.'[]" value="0.00" readonly />
                        <input type="hidden" id="invoice_'.$k.'_tax_voucher_id_'.$j.'" name="invoice_tax_voucher_id_'.$j.'[]" value="" />
                        <input type="hidden" id="invoice_'.$k.'_tax_ledger_type_'.$j.'" name="invoice_tax_ledger_type_'.$j.'[]" value="Sub Entry" />
                    </td>
                    <td style="display: none;">
                        <input type="text" class="text-right" id="edited_'.$k.'_tax_'.$j.'" name="edited_tax_'.$j.'[]" value="0.00" onChange="getDifference(this);" readonly />
                    </td>
                    <td style="display: none;">
                        <input type="text" class="text-right " id="diff_'.$k.'_tax_'.$j.'" name="diff_tax_'.$j.'[]" value="0.00" readonly />
                    </td>';
            $invoice_tax_td = $invoice_tax_td . $td;

            $td = '<td>
                        <input type="text" class="text-right" id="invoice_'.$k.'_cgst_'.$j.'" name="invoice_cgst_'.$j.'[]" value="0" readonly />
                        <input type="hidden" id="invoice_'.$k.'_cgst_voucher_id_'.$j.'" name="invoice_cgst_voucher_id_'.$j.'[]" value="" />
                        <input type="hidden" id="invoice_'.$k.'_cgst_ledger_type_'.$j.'" name="invoice_cgst_ledger_type_'.$j.'[]" value="Sub Entry" />
                    </td>
                    <td>
                        <input type="text" class="text-right" id="edited_'.$k.'_cgst_'.$j.'" name="edited_cgst_'.$j.'[]" value="'.$mycomponent->format_money($sku_cgst, 2).'" onChange="getDifference(this);" readonly />
                    </td>
                    <td>
                        <input type="text" class="text-right diff" id="diff_'.$k.'_cgst_'.$j.'" name="diff_cgst_'.$j.'[]" value="'.$mycomponent->format_money($sku_cgst, 2).'" readonly />
                    </td>';
            $invoice_cgst_td = $invoice_cgst_td . $td;

            $td = '<td>
                        <input type="text" class="text-right" id="invoice_'.$k.'_sgst_'.$j.'" name="invoice_sgst_'.$j.'[]" value="0" readonly />
                        <input type="hidden" id="invoice_'.$k.'_sgst_voucher_id_'.$j.'" name="invoice_sgst_voucher_id_'.$j.'[]" value="" />
                        <input type="hidden" id="invoice_'.$k.'_sgst_ledger_type_'.$j.'" name="invoice_sgst_ledger_type_'.$j.'[]" value="Sub Entry" />
                    </td>
                    <td>
                        <input type="text" class="text-right" id="edited_'.$k.'_sgst_'.$j.'" name="edited_sgst_'.$j.'[]" value="'.$mycomponent->format_money($sku_sgst, 2).'" onChange="getDifference(this);" readonly />
                    </td>
                    <td>
                        <input type="text" class="text-right diff" id="diff_'.$k.'_sgst_'.$j.'" name="diff_sgst_'.$j.'[]" value="'.$sku_sgst.'" readonly />
                    </td>';
            $invoice_sgst_td = $invoice_sgst_td . $td;

            $td = '<td>
                        <input type="text" class="text-right" id="invoice_'.$k.'_igst_'.$j.'" name="invoice_igst_'.$j.'[]" value="0" readonly />
                        <input type="hidden" id="invoice_'.$k.'_igst_voucher_id_'.$j.'" name="invoice_igst_voucher_id_'.$j.'[]" value="" />
                        <input type="hidden" id="invoice_'.$k.'_igst_ledger_type_'.$j.'" name="invoice_igst_ledger_type_'.$j.'[]" value="Sub Entry" />
                    </td>
                    <td>
                        <input type="text" class="text-right" id="edited_'.$k.'_igst_'.$j.'" name="edited_igst_'.$j.'[]" value="'.$mycomponent->format_money($sku_igst, 2).'" onChange="getDifference(this);" readonly />
                    </td>
                    <td>
                        <input type="text" class="text-right diff" id="diff_'.$k.'_igst_'.$j.'" name="diff_igst_'.$j.'[]" value="'.$mycomponent->format_money($sku_igst, 2).'" readonly />
                    </td>';
            $invoice_igst_td = $invoice_igst_td . $td;
            $total_tax[$j]['invoice_cost_td']=$invoice_cost_td;
            $total_tax[$j]['invoice_tax_td']=$invoice_tax_td;
            $total_tax[$j]['invoice_cgst_td']=$invoice_cgst_td;
            $total_tax[$j]['invoice_sgst_td']=$invoice_sgst_td;
            $total_tax[$j]['invoice_igst_td']=$invoice_igst_td; 

            $tableamount='<tr>
                    <td class="sticky-cell" style="border: none!important;">1.'.($j+1).'</td>
                    <td class="sticky-cell" style="border: none!important;">Taxable Amount</td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="invoicecost_acc_id_'.$j.'" class="form-control acc_id select2" name="invoice_cost_acc_id[]" onChange="get_acc_details(this)">
                            <option value="">Select</option>';
                            for($i=0; $i<count($acc_master); $i++) { 
                            if($acc_master[$i]['type']=="Goods Purchase") { 
                                $tableamount .='<option value="'.$acc_master[$i]['id'].'"';
                                if($total_tax[$j]['invoice_cost_acc_id']==$acc_master[$i]['id']) 
                                   $tableamount .='selected';
                                   $tableamount .='>';
                                   $tableamount .=$acc_master[$i]['legal_name']; 
                                   $tableamount .='</option>';
                                }
                            }
                    $tableamount .='</select>';
                    $tableamount.='<input type="hidden" id="invoicecost_ledger_name_'.$j.'" name="invoice_cost_ledger_name[]" value="'.$total_tax[$j]['invoice_cost_ledger_name'].'" />
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="invoicecost_ledger_code_'.$j.'" name="invoice_cost_ledger_code[]" value="'.$total_tax[$j]['invoice_cost_ledger_code'].'" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="hidden" id="vat_cst_'.$j.'" name="vat_cst[]" value="'.$total_tax[$j]['vat_cst'].'" />
                        <input type="hidden" id="vat_percen_'.$j.'" name="vat_percen[]" value="'.$total_tax[$j]['vat_percent'].'" />
                        <input type="hidden" id="cgst_rate_'.$j.'" name="cgst_rate[]" value="'.$total_tax[$j]['cgst_rate'].'" />
                        <input type="hidden" id="sgst_rate_'.$j.'" name="sgst_rate[]" value="'.$total_tax[$j]['sgst_rate'].'" />
                        <input type="hidden" id="igst_rate_'.$j.'" name="igst_rate[]" value="'.$total_tax[$j]['igst_rate'].'" />
                        <input type="hidden" id="sub_particular_cost_'.$j.'" name="sub_particular_cost[]" value="Purchase_'.$total_tax[$j]['tax_zone_code'].'_'.$total_tax[$j]['vat_cst'].'_'.$total_tax[$j]['vat_percent'].'" />
                        '.$mycomponent->format_money($total_tax[$j]['vat_percent'],2).'
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right" id="edited_total_cost_'.$j.'" name="total_cost_'.$j.'" value="0" readonly />
                    </td>
                    '.$total_tax[$j]['invoice_cost_td'].'
                    <td>
                        <input type="text" id="narration_cost_'.$j.'" name="narration_cost_'.$j.'" value="" class="narration"/>
                    </td>
                </tr>
                <tr style="display: none;">
                    <td class="sticky-cell" style="border: none!important;">2.'.($j+1).'</td>
                    <td class="sticky-cell" style="border: none!important;">Tax</td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="invoicetax_acc_id_'.$j.'" class="form-control acc_id select2" name="invoice_tax_acc_id[]" onChange="get_acc_details(this)">
                            <option value="">Select</option>
                        </select>
                        <input type="hidden" id="invoicetax_ledger_name_'.$j.'" name="invoice_tax_ledger_name[]" value="'.$total_tax[$j]['invoice_tax_ledger_name'].'" />
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="invoicetax_ledger_code_'.$j.'" name="invoice_tax_ledger_code[]" value="'.$total_tax[$j]['invoice_tax_ledger_code'].'" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="hidden" id="sub_particular_tax_'.$j.'" name="sub_particular_tax[]" value="Tax_'.$total_tax[$j]['tax_zone_code'].'_'.$total_tax[$j]['vat_percent'].'" />
                     '.$mycomponent->format_money($total_tax[$j]['vat_percent'],2).'
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right " id="total_tax_'.$j.'" name="total_tax_'.$j.'" value="'.$mycomponent->format_money($total_tax[$j]['total_tax'], 2).'" readonly />
                    </td>
                    '.$total_tax[$j]['invoice_tax_td'].'
                    <td>
                        <input type="text" id="narration_tax_'.$j.'" name="narration_tax_'.$j.'" value="" class="narration"/>
                    </td>
                </tr>
                <tr style="'.$intra_state_style.'">
                    <td class="sticky-cell" style="border: none!important;">2.'.($j+1).'</td>
                    <td class="sticky-cell" style="border: none!important;">CGST</td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="invoicecgst_acc_id_'.$j.'" class="form-control acc_id select2" name="invoice_cgst_acc_id[]" onChange="get_acc_details(this)">
                            <option value="">Select</option>';
                            for($i=0; $i<count($acc_master); $i++) { 
                            if($acc_master[$i]['type']=="CGST") { 
                                $tableamount .='<option value="'.$acc_master[$i]['id'].'"';
                                if($cgst_tax==$acc_master[$i]['id']) 
                                   $tableamount .='selected';
                                   $tableamount .='>';
                                   $tableamount .=$acc_master[$i]['legal_name']; 
                                   $tableamount .='</option>';
                                }
                            }
                        $tableamount.='<input type="hidden" id="invoicecgst_ledger_name_'.$j.'" name="invoice_cgst_ledger_name[]" value="'.$total_tax[$j]['invoice_cgst_ledger_name'].'" />
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="invoicecgst_ledger_code_'.$j.'" name="invoice_cgst_ledger_code[]" value="'.$total_tax[$j]['invoice_cgst_ledger_code'].'" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;">
                        <input type="hidden" id="sub_particular_cgst_'.$j.'" name="sub_particular_cgst[]" value="Tax_cgst_'.$total_tax[$j]['cgst_rate'].'" />
                      '.$mycomponent->format_money($total_tax[$j]['cgst_rate'],2).'
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right " id="total_cgst_'.$j.'" name="total_cgst_'.$j.'" value="0" readonly />
                    </td>
                    '.$total_tax[$j]['invoice_cgst_td'].'
                    <td>
                        <input type="text" id="narration_cgst_'.$j.'" name="narration_cgst_'.$j.'" value="" class="narration"/>
                    </td>
                </tr>
                <tr style="'.$intra_state_style.'">
                    <td class="sticky-cell" style="border: none!important;">2.'.($j+1).'</td>
                    <td class="sticky-cell" style="border: none!important;">SGST</td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="invoicesgst_acc_id_'.$j.'" class="form-control acc_id select2" name="invoice_sgst_acc_id[]" onChange="get_acc_details(this)">
                            <option value="">Select</option>';
                           for($i=0; $i<count($acc_master); $i++) { 
                        if($acc_master[$i]['type']=="SGST") { 
                            $tableamount .='<option value="'.$acc_master[$i]['id'].'"';
                            if($sgst_tax==$acc_master[$i]['id']) 
                               $tableamount .='selected';
                               $tableamount .='>';
                               $tableamount .=$acc_master[$i]['legal_name']; 
                               $tableamount .='</option>';
                            }
                        }
                         $tableamount.='<input type="hidden" id="invoicesgst_ledger_name_'.$j.'" name="invoice_sgst_ledger_name[]" value="'.$total_tax[$j]['invoice_sgst_ledger_name'].'" />
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="invoicesgst_ledger_code_'.$j.'" name="invoice_sgst_ledger_code[]" value="'.$total_tax[$j]['invoice_sgst_ledger_code'].'" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;"> 
                        <input type="hidden" id="sub_particular_sgst_'.$j.'" name="sub_particular_sgst[]" value="Tax_sgst_'.$total_tax[$j]['sgst_rate'].'" />
                        '.$mycomponent->format_money($total_tax[$j]['sgst_rate'],2).'
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right " id="total_sgst_'.$j.'" name="total_sgst_'.$j.'" value="0" readonly />
                    </td>
                    '.$total_tax[$j]['invoice_sgst_td'].'
                    <td>
                        <input type="text" id="narration_sgst_'.$j.'" name="narration_sgst_'.$j.'" value="" class="narration"/>
                    </td>
                </tr>
                <tr style="'.$inter_state_style.'">
                    <td class="sticky-cell" style="border: none!important;">2.'.($j+1).'</td>
                    <td class="sticky-cell" style="border: none!important;">IGST</td>
                    <td class="sticky-cell" style="border: none!important;">
                        <select id="invoiceigst_acc_id_'.$j.'" class="form-control acc_id select2" name="invoice_igst_acc_id[]" onChange="get_acc_details(this)">
                            <option value="">Select</option>';
                            for($i=0; $i<count($acc_master); $i++) { 
                            if($acc_master[$i]['type']=="IGST") { 
                                $tableamount .='<option value="'.$acc_master[$i]['id'].'"';
                                if($igst_tax==$acc_master[$i]['id']) 
                                   $tableamount .='selected';
                                   $tableamount .='>';
                                   $tableamount .=$acc_master[$i]['legal_name']; 
                                   $tableamount .='</option>';
                                }
                            }
                        
                        $tableamount.='<input type="hidden" id="invoiceigst_ledger_name_'.$j.'" name="invoice_igst_ledger_name[]" value="'.$total_tax[$j]['invoice_igst_ledger_name'].'" />
                    </td>
                    <td class="sticky-cell" style="border: none!important;"><input type="text" id="invoiceigst_ledger_code_'.$j.'" name="invoice_igst_ledger_code[]" value="'.$total_tax[$j]['invoice_igst_ledger_code'].'" style="border: none;" readonly /></td>
                    <td class="sticky-cell" style="border: none!important;">
                     
                        <input type="hidden" id="sub_particular_igst_'.$j.'" name="sub_particular_igst[]" value="Tax_igst_'.$total_tax[$j]['igst_rate'].'" />
                     
                        '.$mycomponent->format_money($total_tax[$j]['igst_rate'],2).'
                    </td>
                    <td class="sticky-cell text-right" style="border: none!important;">
                        <input type="text" class="text-right " id="total_igst_'.$j.'" name="total_igst_'.$j.'" value="0" readonly />
                    </td>
                    '.$total_tax[$j]['invoice_igst_td'].'
                    <td>
                        <input type="text" class="narration" id="narration_igst_'.$j.'" name="narration_igst_'.$j.'" value="" />
                    </td>
                </tr>';

        echo $tableamount;
        
    }          

   
    }


