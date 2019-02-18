<?php

namespace app\controllers;

use Yii;
use app\models\SalesUpload;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

class SalesuploadController extends Controller 
{
    public function actionIndex() {
        $salesupload = new SalesUpload();
        $access = $salesupload->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $approved = $salesupload->getDetails("", "approved");

                $salesupload->setLog('Salesupload', '', 'View', '', 'View Sales Upload List', 'acc_sales_files', '');
                return $this->render('sales_upload', ['access' => $access, 'approved' => $approved]);
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

    public function actionUpload(){
        $salesupload = new SalesUpload();
        $result = $salesupload->upload();
        
        $this->redirect(array('salesupload/index'));
    }

    public function actionUploadsales() {
        $salesupload = new SalesUpload();
        $salesupload->upload_sales();
    }

    public function actionTest() {
        $salesupload = new SalesUpload();
        // echo $salesupload->check_gst_no_format('06AACCT5910H1ZI', 'Haryana');
        // if($salesupload->check_no('0.00')==false){
        //     echo 'rejected';
        // }
        // $salesupload->upload_sales();
        // $salesupload->test();

        $test_val = $salesupload->check_no(-597);

        if($test_val==false){
            echo 'False';
        } else {
            echo 'True';
        }
    }

    public function actionCreate(){
        $salesupload = new SalesUpload();
        $access = $salesupload->getAccess();
        if(count($access)>0) {
            if($access[0]['r_insert']==1) {
                $action = 'insert';
                //$acc_details = $salesupload->getAccountDetails();
              
                $approver_list = $salesupload->getApprover($action);

                $salesupload->setLog('Salesupload', '', 'Insert', '', 'Insert Sales Upload Details', 'acc_sales_upload', '');
                return $this->render('sales_upload_details', ['action' => $action, 
                                                                 'approver_list' => $approver_list]);
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
		$salesupload = new SalesUpload();
        $data = $salesupload->get_details($id);
        $acc_master = $salesupload->getAccountDetails('', 'approved');
        $upload_details = $salesupload->getFileDetails($id);
        $invoices = $salesupload->getFileInvoices($id);
        $marketplaces = $salesupload->getFileMarketplaces($id);

        // echo json_encode($data);
        // echo '<br/>';

        return $this->render('update', ['data' => $data, 'acc_master' => $acc_master, 'upload_details' => $upload_details, 
                                'invoices' => $invoices, 'marketplaces' => $marketplaces, 'action' => $action]);
    }

    public function actionView($id) {
        $salesupload = new SalesUpload();
        $access = $salesupload->getAccess();
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
                $salesupload->setLog(' Salesupload', '', 'View', '', 'View Sales Upload Details', 'acc_sales_upload', $id);
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
        $salesupload = new SalesUpload();
        $access = $salesupload->getAccess();
        $data = $salesupload->getFileDetails($id);
        if(count($access)>0) {
            if($access[0]['r_edit']==1 && ($access[0]['session_id']==$data[0]['created_by'] || $access[0]['session_id']==$data[0]['updated_by'])) {
                $salesupload->setLog('Salesupload', '', 'Edit', '', 'Edit Sales Upload Details', 'acc_sales_upload', $id);
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

    public function actionPost($id) {
        $salesupload = new SalesUpload();
        $access = $salesupload->getAccess();
        $data = $salesupload->getFileDetails($id);
        if(count($access)>0) {
            if($access[0]['r_edit']==1 && ($access[0]['session_id']==$data[0]['created_by'] || $access[0]['session_id']==$data[0]['updated_by'])) {
                $salesupload->setLog('Salesupload', '', 'Post', '', 'Post Sales Upload Details', 'acc_sales_upload', $id);
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

    public function actionGetledger(){
        $model = new SalesUpload();
        $mycomponent = Yii::$app->mycomponent;

        $data = $model->getSalesParticulars();
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
                    $particular = "Total Amount";
                } else {
                    $particular = "Total Deduction Amount";

                    $debit_amt = $debit_amt - ($total_debit_amt*2);
                    $credit_amt = $credit_amt - ($total_credit_amt*2);
                }

                $rows = $rows . '<tr class="bold-text text-right">
                                    <td colspan="4" style="text-align:right;">'.$particular.'</td>
                                    <td class="bold-text text-right">'.$mycomponent->format_money($total_debit_amt,2).'</td>
                                    <td class="bold-text text-right">'.$mycomponent->format_money($total_credit_amt,2).'</td>';
                // $rows = $rows . '<tr><td colspan="6"></td></tr>';

                $total_debit_amt = 0;
                $total_credit_amt = 0;
                $sr_no=1;

                // if($acc_ledger_entries[$i]["entry_type"]=="Total Amount"){
                //     // $rows = $rows . '<tr class="bold-text text-right">
                //     //                     <td colspan="6" style="text-align:left;">Deduction Entry</td>
                //     //                 </tr>';
                //     $row_deduction = '<tr class="bold-text text-right">
                //                         <td colspan="6" style="text-align:left;">Deduction Entry</td>
                //                     </tr>';

                //     $bl_deduction = true;
                // }
            }

            $blFlag = false;
            if(($i+1)==count($acc_ledger_entries)){
                $blFlag = true;
            } else if($acc_ledger_entries[$i]["invoice_no"]!=$acc_ledger_entries[$i+1]["invoice_no"]){
                $blFlag = true;
            } else if($acc_ledger_entries[$i]["voucher_id"]!=$acc_ledger_entries[$i+1]["voucher_id"]){
                $blFlag = true;
            }

            if($blFlag == true){
                $rows = '<tr class="bold-text text-right">
                            <td colspan="6" style="text-align:left;">'.$acc_ledger_entries[$i]["ledger_name"].'</td>
                        </tr>' . $rows;

                $debit_amt = round($debit_amt,2);
                $credit_amt = round($credit_amt,2);

                $table = '<div class="diversion">
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
                            <!-- <tr class="bold-text text-right">
                                <td colspan="4" style="text-align:right;">Total Amount</td>
                                <td>' . $mycomponent->format_money($debit_amt,2) . '</td>
                                <td>' . $mycomponent->format_money($credit_amt,2) . '</td>
                            </tr> -->
                        </table></div><br/><br/>';

                // echo $table;
                $table_arr[$table_cnt] = $table;
                $table_cnt = $table_cnt + 1;

                $rows=""; $debit_amt=0; $credit_amt=0; $sr_no=1;
            }
        }

        echo json_encode($table_arr);
    }

    public function actionAuthorise($id) {
        $salesupload = new SalesUpload();
        $access = $salesupload->getAccess();
        $data = $salesupload->getDetails($id, "");
        if(count($access)>0) {
            if($access[0]['r_approval']==1 && $access[0]['session_id']!=$data[0]['updated_by']) {
                $salesupload->setLog('Salesupload', '', 'Authorise', '', 'Authorise Sales Upload Details', 'acc_sales_upload', $id);
                return $this->actionRedirect('authorise', $id);
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

    public function actionSave(){
        $request = Yii::$app->request;
        $model = new SalesUpload();
        $mycomponent = Yii::$app->mycomponent;

        $file_id = $request->post('file_id');

        $data = $model->getSalesParticulars();

        $bulkInsertArray = $data['bulkInsertArray'];
        $ledgerArray = $data['ledgerArray'];

        // echo json_encode($bulkInsertArray);
        // echo '<br/>';
        // echo json_encode($ledgerArray);

        /*echo "<pre>";
        print_r($bulkInsertArray);
        echo "</pre>";

        echo "<pre>";
        print_r($ledgerArray);
        echo "</pre>";*/


        if(count($bulkInsertArray)>0){
            $sql = "delete from acc_sales_entries where file_id = '$file_id'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['file_id','particular','acc_id','ledger_name','ledger_code','voucher_id','ledger_type',
                                'tax_percent','invoice_no','amount','status','is_active',
                                'updated_by','updated_date','date_of_upload','company_id', 'marketplace_id'];
            // below line insert all your record and return number of rows inserted
            $tableName = "acc_sales_entries";
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
            $sql = "delete from acc_ledger_entries where ref_id = '$file_id' and ref_type='sales_upload'";
            Yii::$app->db->createCommand($sql)->execute();

            $columnNameArray=['ref_id','ref_type','entry_type','invoice_no','acc_id','ledger_name','ledger_code',
                                'voucher_id','ledger_type','type','amount','status','is_active',
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

        $this->redirect(array('salesupload/ledger', 'id'=>$file_id));
    }

    public function actionLedger($id){
        $model = new SalesUpload();

        $acc_ledger_entries = $model->getSalesAccLedgerEntries($id);
        $data = $model->get_details($id);
        $file_details = $model->getFileDetails($id);

        return $this->render('ledger', ['data' => $data, 'acc_ledger_entries' => $acc_ledger_entries, 'file_details' => $file_details]);
    }

    public function actionChecktaxtypeavailablity() {
        $salesupload = new SalesUpload();
        $result = $salesupload->checkTaxTypeAvailablity();
        echo $result;
    }

    public function actionFreezefile(){
        $model = new SalesUpload();
        $result = $model->freeze_file();
        echo $result;
    }

    public function actionCheckhsn(){
        $model = new SalesUpload();
        $result = $model->check_hsn();
        echo $result;
    }
}