<?php

namespace app\controllers;

use Yii;
use app\models\AccReport;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
// use moonlandsoft\phpexcel\Excel;
use phpoffice\phpexcel\Excel;
use app\models\PaymentReceipt;
use app\models\GroupMaster;

class AccreportController extends Controller
{   
    public function actionExcel(){
        $objPHPExcel = new \PHPExcel();
 
         $sheet=0;
          
         $objPHPExcel->setActiveSheetIndex($sheet);
         
        // foreach ($foos as $foo) {  
                
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                
            // $objPHPExcel->getActiveSheet()->setTitle($foo->bar)
                
            $objPHPExcel->getActiveSheet()->setTitle('bar')
                
             ->setCellValue('A1', 'Firstname')
             ->setCellValue('B1', 'Lastname');
                 
                 $row=2;
                        
            // $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$foo->firstname); 
            // $objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$foo->lastname);
            //             $row++ ;
            //             }
                
                // echo 'hii';


                // $filename='Sale_Invoice_Report.xls';

                // header('Content-Type: application/vnd.ms-excel');
                // header('Content-Disposition: attachment;filename="data.xls"');
                // header('Cache-Control: max-age=0');
                // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                // $objWriter->save('php://output');


                $filename='Sale_Invoice_Report.xls';
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");;
                // header("Content-Disposition: attachment;filename=$filename");
                header("Content-Transfer-Encoding: binary ");

                $filename='Sale_Invoice_Report.xls';
                // $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                // $objWriter->save($filename);

                $objPHPExcel->saveExcel2007($objPHPExcel,$filename);



                // header("Content-Disposition: attachment; filename='data.csv' ");
                // header('Cache-Control: max-age=0');
                // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
                // $objWriter->save('php://output');

                // header('Content-Type: application/vnd.ms-excel');
                // header('Content-Disposition: attachment;filename="'.$filename.'"');
                // header('Cache-Control: max-age=0');
                // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                // $objWriter->save('php://output');

                // header('Content-Type: application/vnd.ms-excel');
                // $filename = "MyExcelReport_".date("d-m-Y-His").".xls";
                // header('Content-Disposition: attachment;filename='.$filename .' ');
                // header('Cache-Control: max-age=0');
                // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                // echo $objWriter->save('php://output');

                // echo 'hii22';

                // $report = new AccReport();
                // $acc_details = $report->getAccountDetails();

                // return $this->render('ledger_report', ['acc_details' => $acc_details]);
    }

    public function actionLedgerreport(){
        $report = new AccReport();
        $acc_details = $report->getAccountDetails();
        $report->setLog('LedgerReport', '', 'View', '', 'View Ledger Report', 'acc_ledger_entries', '');
        return $this->render('ledger_report', ['acc_details' => $acc_details]);
    }

    public function actionDetailledgerreport(){
        $report = new AccReport();
        $acc_details = $report->getVendorname();
        $state_master = $report->getstatemaster();

        $data['account']=[];
        $data['vouchertype']=[];
        $data['date_criteria']='';
        $data['from_date']='';
        $data['to_date']='';
        $data['state_detail']=$state_master;
        $data['acc_details']=$acc_details;
        $data['state']=[];
        $data['view']='';

        $report->setLog('DetailLedgerReport', '', 'View', '', 'View Ledger Report', 'acc_ledger_entries', '');
        return $this->render('detail_ledger_report',$data);
    }


    public function actionGetdetailledgerreport() {   
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $report = new AccReport();

        $account = $request->post('account');
        $vouchertype = $request->post('vouchertype');
        $date_type = $request->post('date_criteria');
        $from_date = $request->post('from_date');
        $to_date = $request->post('to_date');
        $view = $request->post('view');
        $state = $request->post('state');

        $data['account']=$account;
        $data['date_criteria']=$date_type;
        $data['from_date']=$from_date;
        $data['to_date']=$to_date;
        $data['view']=$view;
        
        $acc_details = $report->getVendorname();
        $state_master = $report->getstatemaster();    
        $data['acc_details'] = $acc_details;
        $data['state_detail'] = $state_master;

        $table = ''; 

        if(count($account)==0){
            $account_id = array();
            for($i=0; $i<count($acc_details); $i++){
                $account_id[$i] = $acc_details[$i]['id'];
            }
            $account = implode(",",$account_id);
        } else {
            $account = implode(",",$account);
        }
        
        if(count($vouchertype)==0) {
            $data['vouchertype']=[];
            $vouchertype = array('purchase','journal_voucher','payment_receipt','go_debit_details','other_debit_credit','promotion','sales_upload');
        } else {
            $data['vouchertype']=$vouchertype;
        }
        if(count($state)>0){
            $data['state']=$state;
            $state = implode(",",$state);
        } else {
            $state = '';
            $data['state']=[];
        }
        

        if($from_date==''){
            $from_date=NULL;
        } else {
            $from_date=$mycomponent->formatdate($from_date);
        }

        if($to_date==''){
            $to_date=NULL;
        } else {
            $to_date=$mycomponent->formatdate($to_date);
        }

        $column_names= [];
        $acc_details= [];
        $colname = '';
        
        if($view=='state') {
            $state_wise_columns= $report->state_wise_column($account, $vouchertype, $from_date, $to_date, $date_type, $state);
            $i = 0;

            foreach ($state_wise_columns as $res) {
                if($res['acc_type']=='ZZPurchase') {
                    $colname.='<th class="text-center">Purchase '.$res['state'].' Excl Tax '.$res['percentage'].'%</th>
                                <th class="text-center">Input-'.$res['state'].'-CGST-'.($res['percentage']/2).'%</th>
                                <th class="text-center">Input-'.$res['state'].'-SGST-'.($res['percentage']/2).'%</th>
                                <th class="text-center">Input-'.$res['state'].'-IGST-'.$res['percentage'].'%</th>
                                <th class="text-center">Purchase '.$res['state'].' Incl Tax '.$res['percentage'].'%</th>';

                    $column_names[$i]['ledger_name']='Purchase '.$res['state'].' Excl Tax '.$res['percentage'].'%';
                    $column_names[$i+1]['ledger_name']='Input-'.$res['state'].'-CGST-'.($res['percentage']/2).'%';
                    $column_names[$i+2]['ledger_name']='Input-'.$res['state'].'-SGST-'.($res['percentage']/2).'%';
                    $column_names[$i+3]['ledger_name']='Input-'.$res['state'].'-IGST-'.$res['percentage'].'%';
                    $column_names[$i+4]['ledger_name']='Purchase '.$res['state'].' Incl Tax '.$res['percentage'].'%';

                    $i = $i + 5;
                } else {
                    $colname.='<th class="text-center">'.$res['state'].'</th>';
                    $column_names[$i]['ledger_name']=$res['state'];
                    $i = $i + 1;
                }
            }
        }
        
        if($view=='tax') {
            $tax_wise_columns= $report->tax_wise_column($account, $vouchertype, $from_date, $to_date, $date_type, $state);
            $i = 0;

            foreach ($tax_wise_columns as $res) {
                if($res['acc_type']=='ZZPurchase') {
                    $colname.='<th class="text-center">Purchase Value Excl Tax '.$res['percentage'].'%</th>
                                <th class="text-center">Tax '.$res['percentage'].'% Total Amount</th>
                                <th class="text-center">Total Purchase Incl Tax '.$res['percentage'].'%</th>';

                    $column_names[$i]['ledger_name']='Purchase Value Excl Tax '.$res['percentage'].'%';
                    $column_names[$i+1]['ledger_name']='Tax '.$res['percentage'].'% Total Amount';
                    $column_names[$i+2]['ledger_name']='Total Purchase Incl Tax '.$res['percentage'].'%';

                    $i = $i + 3;
                } else {
                    $colname.='<th class="text-center">'.$res['ledger_name'].'</th>';
                    $column_names[$i]['ledger_name']=$res['ledger_name'];
                    $i = $i + 1;
                }
            }
        }
        
        if($view=='default') {
            $column_names= $report->column_names($account, $vouchertype, $from_date, $to_date, $date_type, $state);

            foreach ($column_names as $res) {
               $colname.='<th class="text-center">'.$res['ledger_name'].'</th>';
            }
        }

        $acc_details= $report->getDetailledger($account, $vouchertype, $from_date, $to_date, $date_type, $state);

        // echo json_encode($acc_details);
        // echo '<br/><br/>';
        // echo json_encode($column_names);
        // echo '<br/><br/>';

        $blFlag = false;
        $report_data = [];
        $row_cnt = -1;
        $innertab = '';
        $prv_voucher_id = 0;
        $prv_invoice_no = 0;

        for ($i=0; $i <count($acc_details); $i++) {
            $voucher_id = $acc_details[$i]['voucher_id'];
            $invoice_no = $acc_details[$i]['invoice_no'];
            
            if($voucher_id!=$prv_voucher_id || $invoice_no!=$prv_invoice_no) {
                $prv_voucher_id = $voucher_id;
                $prv_invoice_no = $invoice_no;
                $row_cnt = $row_cnt + 1;
                $report_data[$row_cnt]['invoice_date'] = ($acc_details[$i]['invoice_date']!="" && $acc_details[$i]['invoice_date']!="0000-00-00 00:00:00")?date('Y-m-d',strtotime($acc_details[$i]['invoice_date'])):'';
                $report_data[$row_cnt]['grn_approved_date'] = $acc_details[$i]['grn_approved_date_time']!=""?date('Y-m-d',strtotime($acc_details[$i]['grn_approved_date_time'])):'';
                $report_data[$row_cnt]['gi_date'] = $acc_details[$i]['gi_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['gi_date'])):'';
                $report_data[$row_cnt]['posting_date'] = $acc_details[$i]['updated_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['updated_date'])):'';
                $report_data[$row_cnt]['vendor_name'] = $acc_details[$i]['cp_ledger_name'];
                $report_data[$row_cnt]['voucher_type'] = '';
                $report_data[$row_cnt]['voucher_no'] = $acc_details[$i]['voucher_id'];
                $report_data[$row_cnt]['grn_no'] = '';
                $report_data[$row_cnt]['go_no'] = '';
                
                if($acc_details[$i]['ref_type']=='go_debit_details'){
                    $report_data[$row_cnt]['go_no'] = $acc_details[$i]['gi_go_ref_no'];
                } else {
                    $report_data[$row_cnt]['grn_no'] = $acc_details[$i]['gi_go_ref_no'];
                }
                
                $report_data[$row_cnt]['invoice_no'] = $acc_details[$i]['ref_type']=='Debit Note'?$acc_details[$i]['debit_note_ref']:$acc_details[$i]['invoice_no'];

                if($acc_details[$i]['ref_type']=='purchase'){
                    if($acc_details[$i]['entry_type']=='Taxable Amount' || $acc_details[$i]['entry_type']=='CGST' || $acc_details[$i]['entry_type']=='SGST' || $acc_details[$i]['entry_type']=='IGST' || $acc_details[$i]['entry_type']=='Other Charges'){
                        $report_data[$row_cnt]['voucher_type'] = 'Purchase';
                        $report_data[$row_cnt]['invoice_no'] = $acc_details[$i]['invoice_no'];
                    } else {
                        $report_data[$row_cnt]['voucher_type'] = 'Debit Note';
                        $report_data[$row_cnt]['invoice_no'] = $acc_details[$i]['invoice_no'];
                    }
                } else if($acc_details[$i]['ref_type']=='journal_voucher'){
                    $report_data[$row_cnt]['voucher_type'] = 'Journal Voucher';
                } else if($acc_details[$i]['ref_type']=='payment_receipt'){
                    $report_data[$row_cnt]['voucher_type'] = 'Payment Receipt';
                } else if($acc_details[$i]['ref_type']=='go_debit_details'){
                    $report_data[$row_cnt]['voucher_type'] = 'GO RTV';
                } else if($acc_details[$i]['ref_type']=='other_debit_credit'){
                    $report_data[$row_cnt]['voucher_type'] = 'Other Debit Note';
                } else if($acc_details[$i]['ref_type']=='promotion'){
                    $report_data[$row_cnt]['voucher_type'] = 'Promotion';
                }

                $report_data[$row_cnt]['sum_total'] = 0;
                $report_data[$row_cnt]['value_exc_tax'] = 0;
                $report_data[$row_cnt]['total_tax_amt'] = 0;
                $report_data[$row_cnt]['total_inc_tax'] = 0;
                for($j=0;$j<count($column_names);$j++) {
                    $report_data[$row_cnt][$column_names[$j]['ledger_name']] = 0;
                }
            }

            $report_data[$row_cnt]['sum_total'] = $report_data[$row_cnt]['sum_total']+$acc_details[$i]['amount1'];
            $report_data[$row_cnt]['total_inc_tax'] = $report_data[$row_cnt]['total_inc_tax']+$acc_details[$i]['amount1'];

            if($view=='default') {
                if($acc_details[$i]['acc_type']=='Goods Purchase'){
                    $report_data[$row_cnt]['value_exc_tax'] = $report_data[$row_cnt]['value_exc_tax']+$acc_details[$i]['amount1'];
                } else if($acc_details[$i]['acc_type']=='CGST' || $acc_details[$i]['acc_type']=='SGST' || $acc_details[$i]['acc_type']=='IGST'){
                    $report_data[$row_cnt]['total_tax_amt'] = $report_data[$row_cnt]['total_tax_amt']+$acc_details[$i]['amount1'];
                } else {
                    for($j=0;$j<count($column_names);$j++) {
                        if(strtoupper(trim($column_names[$j]['ledger_name']))==strtoupper(trim($acc_details[$i]['ledger_name']))){
                            $report_data[$row_cnt][$column_names[$j]['ledger_name']] = $report_data[$row_cnt][$column_names[$j]['ledger_name']]+$acc_details[$i]['amount1'];
                        }
                    }
                }
            } else if($view=='state') {
                $ledger_name = $acc_details[$i]['ledger_name'];

                if($acc_details[$i]['acc_type']=='Goods Purchase' || $acc_details[$i]['acc_type']=='CGST' || $acc_details[$i]['acc_type']=='SGST' || $acc_details[$i]['acc_type']=='IGST') {
                    $state_name = substr($ledger_name, strpos($ledger_name, '-')+1);
                    $state_name = substr($state_name, 0, strpos($state_name, '-'));
                    $tax_type = substr($ledger_name, strpos($ledger_name, '-')+1);
                    $tax_type = substr($tax_type, strpos($tax_type, '-')+1);
                    $tax_type = substr($tax_type, 0, strpos($tax_type, '-'));
                    $percentage = substr($ledger_name, strrpos($ledger_name, '-')+1);
                    $percentage = str_replace('%', '', $percentage);

                    if($acc_details[$i]['acc_type']=='CGST' || $acc_details[$i]['acc_type']=='SGST'){
                        $report_data[$row_cnt]['Purchase '.$state_name.' Incl Tax '.($percentage*2).'%'] = $report_data[$row_cnt]['Purchase '.$state_name.' Incl Tax '.($percentage*2).'%']+$acc_details[$i]['amount1'];
                    } else {
                        $report_data[$row_cnt]['Purchase '.$state_name.' Incl Tax '.$percentage.'%'] = $report_data[$row_cnt]['Purchase '.$state_name.' Incl Tax '.$percentage.'%']+$acc_details[$i]['amount1'];
                    }

                    if($acc_details[$i]['acc_type']=='Goods Purchase'){
                        $ledger_name = 'Purchase '.$state_name.' Excl Tax '.$percentage.'%';
                        $report_data[$row_cnt]['value_exc_tax'] = $report_data[$row_cnt]['value_exc_tax']+$acc_details[$i]['amount1'];
                    } else {
                        $ledger_name = 'Input-'.$state_name.'-'.$tax_type.'-'.$percentage.'%';
                        $report_data[$row_cnt]['total_tax_amt'] = $report_data[$row_cnt]['total_tax_amt']+$acc_details[$i]['amount1'];
                    }
                }
                for($j=0;$j<count($column_names);$j++) {
                    if(strtoupper(trim($column_names[$j]['ledger_name']))==strtoupper(trim($ledger_name))){
                        $report_data[$row_cnt][$column_names[$j]['ledger_name']] = $report_data[$row_cnt][$column_names[$j]['ledger_name']]+$acc_details[$i]['amount1'];
                    }
                }
            } else if($view=='tax') {
                $ledger_name = $acc_details[$i]['ledger_name'];

                if($acc_details[$i]['acc_type']=='Goods Purchase' || $acc_details[$i]['acc_type']=='CGST' || $acc_details[$i]['acc_type']=='SGST' || $acc_details[$i]['acc_type']=='IGST') {
                    $state_name = substr($ledger_name, strpos($ledger_name, '-')+1);
                    $state_name = substr($state_name, 0, strpos($state_name, '-'));
                    $tax_type = substr($ledger_name, strpos($ledger_name, '-')+1);
                    $tax_type = substr($tax_type, strpos($tax_type, '-')+1);
                    $tax_type = substr($tax_type, 0, strpos($tax_type, '-'));
                    $percentage = substr($ledger_name, strrpos($ledger_name, '-')+1);
                    $percentage = str_replace('%', '', $percentage);

                    if($acc_details[$i]['acc_type']=='CGST' || $acc_details[$i]['acc_type']=='SGST'){
                        $percentage = $percentage * 2;
                    }

                    $report_data[$row_cnt]['Total Purchase Incl Tax '.$percentage.'%'] = $report_data[$row_cnt]['Total Purchase Incl Tax '.$percentage.'%']+$acc_details[$i]['amount1'];

                    if($acc_details[$i]['acc_type']=='Goods Purchase'){
                        $ledger_name = 'Purchase Value Excl Tax '.$percentage.'%';
                        $report_data[$row_cnt]['value_exc_tax'] = $report_data[$row_cnt]['value_exc_tax']+$acc_details[$i]['amount1'];
                    } else {
                        $ledger_name = 'Tax '.$percentage.'% Total Amount';
                        $report_data[$row_cnt]['total_tax_amt'] = $report_data[$row_cnt]['total_tax_amt']+$acc_details[$i]['amount1'];
                    }
                }
                for($j=0;$j<count($column_names);$j++) {
                    if(strtoupper(trim($column_names[$j]['ledger_name']))==strtoupper(trim($ledger_name))){
                        $report_data[$row_cnt][$column_names[$j]['ledger_name']] = $report_data[$row_cnt][$column_names[$j]['ledger_name']]+$acc_details[$i]['amount1'];
                    }
                }
            }
        }

        for ($i=0; $i <count($report_data); $i++) {
            $innertab.='<tr>
                    <td>'.$report_data[$i]['invoice_date'].'</td>
                    <td>'.$report_data[$i]['grn_approved_date'].'</td>
                    <td>'.$report_data[$i]['gi_date'].'</td>
                    <td>'.$report_data[$i]['posting_date'].'</td>
                    <td>'.$report_data[$i]['vendor_name'].'</td>
                    <td>'.$report_data[$i]['voucher_type'].'</td>
                    <td>'.$report_data[$i]['voucher_no'].'</td>
                    <td>'.$report_data[$i]['grn_no'].'</td>
                    <td>'.$report_data[$i]['go_no'].'</td>
                    <td>'.$report_data[$i]['invoice_no'].'</td>
                    <td>'.$report_data[$i]['sum_total'].'</td>';
            if($view=='default') {
                $innertab.='<td>'.$report_data[$i]['value_exc_tax'].'</td>
                            <td>'.$report_data[$i]['total_tax_amt'].'</td>
                            <td>'.$report_data[$i]['total_inc_tax'].'</td>';
            }
            for($j=0;$j<count($column_names);$j++) {
                $innertab.='<td>'.$report_data[$i][$column_names[$j]['ledger_name']].'</td>';
            }
            $innertab.='</tr>';
        }
        
        $table ='<thead>
                        <tr><th class="text-center"> Invoice Date</th>
                            <th class="text-center"> Grn Approved Date</th>
                            <th class="text-center"> GI Date</th>
                            <th class="text-center"> Posting Date </th>
                            <th class="text-center"> Vendor Name</th>
                            <th class="text-center"> Voucher Type </th>
                            <th class="text-center"> Voucher No </th>
                            <th class="text-center"> Grn No  </th>
                            <th class="text-center"> Go No </th>
                            <th class="text-center"> Invoice No/ Debit Note No </th>
                            <th class="text-center"> Sum Total</th>';
        if($view=='default') {
            $table.='<th class="text-center"> Value Exc Tax </th>
                        <th class="text-center"> Total Tax Amount </th>
                        <th class="text-center"> Total Inc Tax </th>';
        }
        $table.=$colname.'</tr></thead><tbody>'.$innertab.'</tbody>';
    
        $data['table'] = $table;  

        for($i=0;$i<count($account);$i++) {
          $report->setLog('LedgerReport', '', 'Generate', '', 'Generate Ledger Report', 'acc_ledger_entries', $account[$i]);
        }

        // echo json_encode($column_names);
        // echo '<br/>';
        // echo '<br/>';
        // echo json_encode($acc_details);
        // echo '<br/>';

        // echo '<html><head><style type="text/css">table tr td {border: 1px solid;}</style></head><body><table class="table table-border">'.$table.'</table></body></html>';
        
        return $this->render('detail_ledger_report',$data);
    }

    public function actionTrialbalancereport(){
        $report = new AccReport();
        $acc_details = $report->getAccountDetails();
        $report->setLog('TrialBalanceReport', '', 'View', '', 'View Trial Balance Report', 'acc_ledger_entries', '');
        return $this->render('trial_balance_report', ['acc_details' => $acc_details]);
    }

    public function actionGetledger() {   
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $account = $request->post('account');
        $from_date = $request->post('from_date');
        $to_date = $request->post('to_date');
        $narration = $request->post('narration');
        
        // $from_date = '01-03-2007';
        // $to_date = '31-03-2017';
        
        // $account = '24';
        // $from_date = '01/03/2007';
        // $to_date = '31/03/2018';

        if($from_date==''){
            $from_date=NULL;
        } else {
            $from_date=$mycomponent->formatdate($from_date);
        }

        if($to_date==''){
            $to_date=NULL;
        } else {
            $to_date=$mycomponent->formatdate($to_date);
        }
        
        // echo $from_date;
        // echo $to_date;

        $report = new AccReport();

        // $data = $report->getAccountDetails($account);
        // if(count($data)>0){
        //     $acc_code = $data[0]['code'];
        // }

        // $data = $report->getOpeningBal($acc_code);
        // $opening_bal = 0;
        // $opening_bal_id = "";
        // if(count($data)>0){
        //     $opening_bal_id = $data[0]['entry_id'];
        //     if($data[0]['type']=='Debit'){
        //         $opening_bal = floatval($data[0]['amount'])*-1;
        //     } else {
        //         $opening_bal = floatval($data[0]['amount']);
        //     }
        // }

        // $data = $report->getLedgerTillDate($acc_code, $from_date, $opening_bal_id);
        // $total_ledger_till_date = 0;
        // if(count($data)>0){
        //     $total_ledger_till_date = floatval($data[0]['tot_amount']);
        // }

        // $opening_bal = $opening_bal - $total_ledger_till_date;
        // $balance = $opening_bal;

        // if($opening_bal<0){
        //     $opening_bal = $opening_bal*-1;
        //     $opening_bal_type = 'Dr';
        // } else {
        //     $opening_bal_type = 'Cr';
        // }

        // $tbody = '<tr>
        //             <td></td>
        //             <td>Start Date</td>
        //             <td>Opening Balance</td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td style="text-align:right;">'.$mycomponent->format_money($opening_bal,2).'</td>
        //             <td>'.$opening_bal_type.'</td>
        //             <td></td>
        //             <td class="show_narration"></td>
        //           </tr>
        //           <tr>
        //             <td>&nbsp;</td>
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
        //             <td class="show_narration"></td>
        //           </tr>';

        $opening_bal = 0;
        $opening_bal_type = 'Cr';
        $balance = 0;
        $data = $report->getOpeningBal($account, $from_date);
        if(count($data)>0){
            $opening_bal = floatval($data[0]['opening_bal']);
        }

        if($opening_bal<0){
            $opening_bal = $opening_bal*-1;
            $opening_bal_type = 'Dr';
        } else {
            $opening_bal_type = 'Cr';
        }
        $tbody = '<tr>
                    <td></td>
                    <td></td>
                    <td>Start Date</td>
                    <td>Opening Balance</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right;">'.$mycomponent->format_money($opening_bal,2).'</td>
                    <td>'.$opening_bal_type.'</td>
                    <td></td>
                    <td></td>
                    <td class="show_narration"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
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
                    <td class="show_narration"></td>
                  </tr>';
        $balance = $opening_bal;

        $data = $report->getLedger($account, $from_date, $to_date);
        $debit_amt = 0;
        $credit_amt = 0;
        $cur_total = 0;
        
        if(count($data)>0){
            for($i=0; $i<count($data); $i++){
                $ledger_code = '';
                $ledger_name = '';

                if($data[$i]['type']=='Debit'){
                    $entry_type = 'Dr';
                    $debit_amt = floatval($data[$i]['amount']);
                    $balance = round($balance - $debit_amt,2);
                    $credit_amt = '';
                    $cur_total = round($cur_total - $debit_amt,2);
                } else {
                    $entry_type = 'Cr';
                    $credit_amt = floatval($data[$i]['amount']);
                    $balance = round($balance + $credit_amt,2);
                    $debit_amt = '';
                    $cur_total = round($cur_total + $credit_amt,2);
                }
                if($balance<0){
                    $balance_type = 'Dr';
                    $balance_val = $balance * -1;
                } else {
                    $balance_type = 'Cr';
                    $balance_val = $balance;
                }
                if(isset($data[$i]['cp_acc_id'])){
                    if($data[$i]['cp_acc_id']!=$account){
                        $ledger_code = $data[$i]['cp_ledger_code'];
                        $ledger_name = $data[$i]['cp_ledger_name'];
                    }
                }
                if($ledger_code == ''){
                    $ledger_code = $data[$i]['ledger_code'];
                    $ledger_name = $data[$i]['ledger_name'];
                }

                $tbody = $tbody . '<tr>
                                    <td>'.($i+1).'</td>
                                    <td>'.$data[$i]['voucher_id'].'</td>
                                    <td>'.(($data[$i]['ref_date']!=null && $data[$i]['ref_date']!="")?date("d/m/Y",strtotime($data[$i]['ref_date'])):"").'</td>
                                    <td>'.$ledger_code.'</td>
                                    <td>'.$ledger_name.'</td>
                                    <td>'.$data[$i]['ref_id'].'</td>
                                    <td>'.$data[$i]['invoice_no'].'</td>
                                    <td style="text-align:right;">'.$mycomponent->format_money($debit_amt,2).'</td>
                                    <td style="text-align:right;">'.$mycomponent->format_money($credit_amt,2).'</td>
                                    <td style="text-align:right;">'.$mycomponent->format_money($balance_val,2).'</td>
                                    <td>'.$balance_type.'</td>
                                    <td>'.$data[$i]['payment_ref'].'</td>
                                    <td class="show_narration">'.$data[$i]['narration'].'</td>
                                  </tr>';
            }
        }

        if($balance<0){
            $balance_type = 'Dr';
            $balance_val = $balance * -1;
        } else {
            $balance_type = 'Cr';
            $balance_val = $balance;
        }

        if($cur_total<0){
            $cur_total_type = 'Dr';
            $cur_total_val = $cur_total * -1;
        } else {
            $cur_total_type = 'Cr';
            $cur_total_val = $cur_total;
        }

        $tbody = $tbody . '<tr>
                            <td>&nbsp;</td>
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
                            <td class="show_narration"></td>
                          </tr>
                          <tr>
                            <td></td>
                            <td></td>
                            <td>End Date</td>
                            <td>Closing Balance</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align:right;">'.$mycomponent->format_money($balance_val,2).'</td>
                            <td>'.$balance_type.'</td>
                            <td></td>
                            <td></td>
                            <td class="show_narration"></td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
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
                            <td class="show_narration"></td>
                          </tr>
                          <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Opening Balance</td>
                            <td style="text-align:right;">'.(($opening_bal_type == "Dr")?$mycomponent->format_money($opening_bal,2):"0.00").'</td>
                            <td style="text-align:right;">'.(($opening_bal_type == "Cr")?$mycomponent->format_money($opening_bal,2):"0.00").'</td>
                            <td>'.$opening_bal_type.'</td>
                            <td></td>
                            <td></td>
                            <td class="show_narration"></td>
                          </tr>
                          <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Current Total</td>
                            <td style="text-align:right;">'.(($cur_total < 0)?$mycomponent->format_money($cur_total_val,2):"0.00").'</td>
                            <td style="text-align:right;">'.(($cur_total >= 0)?$mycomponent->format_money($cur_total_val,2):"0.00").'</td>
                            <td>'.$cur_total_type.'</td>
                            <td></td>
                            <td></td>
                            <td class="show_narration"></td>
                          </tr>
                          <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Closing Balance</td>
                            <td style="text-align:right;">'.(($balance < 0)?$mycomponent->format_money($balance_val,2):"0.00").'</td>
                            <td style="text-align:right;">'.(($balance >= 0)?$mycomponent->format_money($balance_val,2):"0.00").'</td>
                            <td>'.$balance_type.'</td>
                            <td></td>
                            <td></td>
                            <td class="show_narration"></td>
                          </tr>';

        // $tbody = '<thead>
        //             <tr>
        //                 <th class="text-center"> Sr No </th>
        //                 <th class="text-center"> Ref ID (Voucher No) </th>
        //                 <th class="text-center"> Date </th>
        //                 <th class="text-center"> Ledger Code </th>
        //                 <th class="text-center"> Ledger Name </th>
        //                 <th class="text-center"> Ref 1 </th>
        //                 <th class="text-center"> Ref 2 </th>
        //                 <th class="text-center"> Debit </th>
        //                 <th class="text-center"> Credit </th>
        //                 <th class="text-center"> Balance </th>
        //                 <th class="text-center"> DB/CR </th>
        //                 <th class="text-center"> Knock Off Ref </th>
        //                 <th class="text-center show_narration"> Narration </th>
        //             </tr>
        //         </thead>
        //         <tbody>
        //             '.$tbody.'
        //         </tbody>';


        // echo $tbody;

        $data['tbody']=$tbody;
        $data['account']=$account;
        $data['from_date']=$from_date;
        $data['to_date']=$to_date;
        $acc_details = $report->getAccountDetails();

        // echo json_encode($data);

        return $this->render('ledger_report', ['acc_details' => $acc_details, 'tbody' => $tbody, 
                                                'account' => $account, 'narration' => $narration,
                                                'from_date' => $from_date, 'to_date' => $to_date]);
    }

    public function actionGetledgerreport() {   
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $account = $request->post('account');
        $from_date = $request->post('from_date');
        $to_date = $request->post('to_date');
        $narration = $request->post('narration');
        
        // $from_date = '01-03-2007';
        // $to_date = '31-03-2017';
        
        // $account = '24';
        // $from_date = '01/03/2007';
        // $to_date = '31/03/2018';

        if($from_date==''){
            $from_date=NULL;
        } else {
            $from_date=$mycomponent->formatdate($from_date);
        }

        if($to_date==''){
            $to_date=NULL;
        } else {
            $to_date=$mycomponent->formatdate($to_date);
        }
        
        // echo $from_date;
        // echo $to_date;

        $report = new AccReport();

        $opening_bal = 0;
        $opening_bal_type = 'Cr';
        $balance = 0;
        $result = $report->getOpeningBal($account, $from_date);
        if(count($result)>0){
            $opening_bal = floatval($result[0]['opening_bal']);
        }

        

        $data = $report->getLedger($account, $from_date, $to_date);
        

        

        // echo $tbody;

        // $data['tbody']=$tbody;
        // $data['data']=$result;
        // $data['account']=$account;
        // $data['from_date']=$from_date;
        // $data['to_date']=$to_date;
        $acc_details = $report->getAccountDetails();

        // echo json_encode($data);

        $report->setLog('LedgerReport', '', 'Generate', '', 'Generate Ledger Report', 'acc_ledger_entries', $account);
        return $this->render('ledger_report', ['acc_details' => $acc_details, 'opening_bal' => $opening_bal, 
                                                'data' => $data, 'account' => $account, 
                                                'from_date' => $from_date, 'to_date' => $to_date, 'narration' => $narration]);
    }

    public function actionGetsummeryreport() {   
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $account = $request->post('account');
        $from_date = $request->post('from_date');
        $to_date = $request->post('to_date');
        $narration = $request->post('narration');

        if($from_date==''){
            $from_date=NULL;
        } else {
            $from_date=$mycomponent->formatdate($from_date);
        }

        if($to_date==''){
            $to_date=NULL;
        } else {
            $to_date=$mycomponent->formatdate($to_date);
        }
        
        // echo $from_date;
        // echo $to_date;

        $report = new AccReport();

        $opening_bal = 0;
        $opening_bal_type = 'Cr';
        $balance = 0;
        $result = $report->getOpeningBal($account, $from_date);
        if(count($result)>0){
            $opening_bal = floatval($result[0]['opening_bal']);
        }

        $data = $report->getsummeryledger($account, $from_date, $to_date);
        $acc_details = $report->getAccountDetails();

        // echo json_encode($data);

        $report->setLog('LedgerSummaryReport', '', 'Generate', '', 'Generate Ledger Report', 'acc_ledger_entries', $account);
        return $this->render('summery_ledger_report', ['acc_details' => $acc_details, 'opening_bal' => $opening_bal, 
                                                'data' => $data, 'account' => $account, 
                                                'from_date' => $from_date, 'to_date' => $to_date, 'narration' => $narration]);
    }

    public function actionGetledgertotalreport() {   
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $account = $request->post('account');
        $from_date = $request->post('from_date');
        $to_date = $request->post('to_date');
        if($from_date==''){
            $from_date=NULL;
        } else {
            $from_date=$mycomponent->formatdate($from_date);
        }

        if($to_date==''){
            $to_date=NULL;
        } else {
            $to_date=$mycomponent->formatdate($to_date);
        }

        $report = new AccReport();
        $data = $report->get_ledger_totalamount($from_date, $to_date);
        $acc_details = $report->getAccountDetails();

        $report->setLog('LedgerTotalReport', '', 'Generate', '', 'Generate Ledger Report', 'acc_ledger_entries', $account);
        return $this->render('total_ledger_amount.php', ['acc_details' => $acc_details,'data' => $data, 'account' => $account, 
            'from_date' => $from_date, 'to_date' => $to_date]);
    }

    public function actionGettrialbalance() {   
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        // $from_date = '01/03/2007';
        // $to_date = '31/03/2018';

        $date_type = $request->post('date_type');
        $as_of_date = $request->post('as_of_date');
        $from_date = $request->post('from_date');
        $to_date = $request->post('to_date');

        // $date_type = 'As Of Date';
        // $as_of_date = '02/08/2018';
        // $from_date = '';
        // $to_date = '';

        if($as_of_date==''){
            $as_of_date=NULL;
        } else {
            $as_of_date=$mycomponent->formatdate($as_of_date);
        }
        if($from_date==''){
            $from_date=NULL;
        } else {
            $from_date=$mycomponent->formatdate($from_date);
        }
        if($to_date==''){
            $to_date=NULL;
        } else {
            $to_date=$mycomponent->formatdate($to_date);
        }
        
        if($date_type=='As Of Date'){
            $from_date = '2001-01-01';
            $to_date = $as_of_date;
        }

        $report = new AccReport();
        $data = $report->getTrialBalance($from_date, $to_date);
        $opening_bal = 0;
        $tot_amount = 0;
        $closing_bal = 0;
        $debit_amt = 0;
        $credit_amt = 0;
        $cur_total = 0;
        $acc_code = '';
        $prev_acc_code = '';
        $tbody = '';
        $tbody2 = '';
        $tot_deb_ope_bal = 0;
        $tot_crd_ope_bal = 0;
        $tot_deb_tran = 0;
        $tot_crd_tran = 0;
        $tot_deb_clo_bal = 0;
        $tot_crd_clo_bal = 0;
        $sr_no = 0;

        if(count($data)>0){
            for($i=0; $i<count($data); $i++){
                $acc_code = $data[$i]['code'];

                // if($acc_code!=$prev_acc_code){
                //     $prev_acc_code = $acc_code;
                //     $opening_bal = floatval($data[$i]['opening_bal']);
                //     $closing_bal = $opening_bal;
                // }

                // if($data[$i]['type']=='Debit'){
                //     $debit_amt = floatval($data[$i]['amount']);
                //     $closing_bal = $opening_bal - $debit_amt;
                //     $credit_amt = '';
                // } else {
                //     $credit_amt = floatval($data[$i]['amount']);
                //     $closing_bal = $closing_bal + $credit_amt;
                //     $debit_amt = '';
                // }

                $opening_bal = floatval($data[$i]['opening_bal']);
                $debit_amt = floatval($data[$i]['debit_amt']);
                $credit_amt = floatval($data[$i]['credit_amt']);
                $closing_bal = $opening_bal - $debit_amt + $credit_amt;

                if($debit_amt!=0 || $credit_amt!=0){
                    if($debit_amt==0){
                        $debit_amt_val = '';
                    } else {
                        $debit_amt_val = $mycomponent->format_money($debit_amt,2);
                    }
                    if($credit_amt==0){
                        $credit_amt_val = '';
                    } else {
                        $credit_amt_val = $mycomponent->format_money($credit_amt,2);
                    }

                    // echo 'debit ' . $debit_amt . '<br/>';
                    // echo 'debit value ' . $debit_amt_val . '<br/>';
                    // echo 'credit ' . $credit_amt . '<br/>';
                    // echo 'credit value ' . $credit_amt_val . '<br/>';

                    $tbody = $tbody . '<tr>
                                        <td>'.($sr_no+1).'</td>
                                        <td>'.$data[$i]['legal_name'].'</td>
                                        <td>'.$data[$i]['category_1'].'</td>
                                        <td>'.$data[$i]['code'].'</td>
                                        <td style="text-align: right;">'.(($opening_bal<0)?$mycomponent->format_money($opening_bal*-1,2):"").'</td>
                                        <td style="text-align: right;">'.(($opening_bal>=0)?$mycomponent->format_money($opening_bal,2):"").'</td>
                                        <td style="text-align: right;">'.$debit_amt_val.'</td>
                                        <td style="text-align: right;">'.$credit_amt_val.'</td>
                                        <td style="text-align: right;">'.(($closing_bal<0)?$mycomponent->format_money($closing_bal*-1,2):"").'</td>
                                        <td style="text-align: right;">'.(($closing_bal>=0)?$mycomponent->format_money($closing_bal,2):"").'</td>
                                        <td class="bus_cat">'.$data[$i]['bus_category'].'</td>
                                        <td class="acc_cat">'.$data[$i]['category_1'].'</td>
                                        <td class="acc_cat">'.$data[$i]['category_2'].'</td>
                                        <td class="acc_cat">'.$data[$i]['category_3'].'</td>
                                      </tr>';


                    $tbody2 = $tbody2 . '<tr>
                                        <td>'.($sr_no+1).'</td>
                                        <td>'.$data[$i]['legal_name'].'</td>
                                        <td>'.$data[$i]['category_1'].'</td>
                                        <td>'.$data[$i]['code'].'</td>
                                        <td style="text-align: right;">'.(($closing_bal<0)?$mycomponent->format_money($closing_bal*-1,2):"").'</td>
                                        <td style="text-align: right;">'.(($closing_bal>=0)?$mycomponent->format_money($closing_bal,2):"").'</td>
                                        <td class="bus_cat">'.$data[$i]['bus_category'].'</td>
                                        <td class="acc_cat">'.$data[$i]['category_1'].'</td>
                                        <td class="acc_cat">'.$data[$i]['category_2'].'</td>
                                        <td class="acc_cat">'.$data[$i]['category_3'].'</td>
                                      </tr>';

                  $sr_no = $sr_no + 1;
                }

                if($opening_bal<0){
                    $tot_deb_ope_bal = $tot_deb_ope_bal + ($opening_bal*-1);
                } else {
                    $tot_crd_ope_bal = $tot_crd_ope_bal + $opening_bal;
                }
                if($closing_bal<0){
                    $tot_deb_clo_bal = $tot_deb_clo_bal + ($closing_bal*-1);
                } else {
                    $tot_crd_clo_bal = $tot_crd_clo_bal + $closing_bal;
                }
                $tot_deb_tran = $tot_deb_tran + $debit_amt;
                $tot_crd_tran = $tot_crd_tran + $credit_amt;

                // echo 'opening_bal ' . $opening_bal . '<br/>';
                // echo 'tot_deb_ope_bal ' . $tot_deb_ope_bal . '<br/>';
                // echo 'tot_crd_ope_bal ' . $tot_crd_ope_bal . '<br/>';
                // echo 'closing_bal ' . $closing_bal . '<br/>';
                // echo 'tot_deb_clo_bal ' . $tot_deb_clo_bal . '<br/>';
                // echo 'tot_crd_clo_bal ' . $tot_crd_clo_bal . '<br/>';

                // $opening_bal = $closing_bal;
            }

            $tbody = $tbody . '<tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Grant Total</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_deb_ope_bal,2).'</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_crd_ope_bal,2).'</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_deb_tran,2).'</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_crd_tran,2).'</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_deb_clo_bal,2).'</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_crd_clo_bal,2).'</td>
                                    <td class="bus_cat"></td>
                                    <td class="acc_cat"></td>
                                    <td class="acc_cat"></td>
                                    <td class="acc_cat"></td>
                                </tr>';

            $tbody2 = $tbody2 . '<tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Grant Total</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_deb_clo_bal,2).'</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_crd_clo_bal,2).'</td>
                                    <td class="bus_cat"></td>
                                    <td class="acc_cat"></td>
                                    <td class="acc_cat"></td>
                                    <td class="acc_cat"></td>
                                </tr>';
        }

        $tbody = '<thead>
                    <tr class="sticky-row">
                        <th class="text-center" rowspan="2"> Sr No </th>
                        <th class="text-center" rowspan="2"> Particulars </th>
                        <th class="text-center" rowspan="2"> Accounts Level 1 Category </th>
                        <th class="text-center" rowspan="2"> Account Name </th>
                        <th class="text-center" colspan="2" style="border-bottom: 0;"> Opening Balance </th>
                        <th class="text-center" colspan="2" style="border-bottom: 0;"> Transaction </th>
                        <th class="text-center" colspan="2" style="border-bottom: 0;"> Closing Balance </th>
                        <th class="text-center bus_cat" rowspan="2"> Business Category </th>
                        <th class="text-center acc_cat" rowspan="2"> Accounts Level 1 </th>
                        <th class="text-center acc_cat" rowspan="2"> Accounts Level 2 </th>
                        <th class="text-center acc_cat" rowspan="2"> Accounts Level 3 </th>
                    </tr>
                    <tr class="sticky-row">
                        <th class="text-center"> Debit </th>
                        <th class="text-center"> Credit </th>
                        <th class="text-center"> Debit </th>
                        <th class="text-center"> Credit </th>
                        <th class="text-center"> Debit </th>
                        <th class="text-center" style="border-right: 1px solid;"> Credit </th>
                    </tr>
                </thead>
                <tbody>
                    '.$tbody.'
                </tbody>';

        $tbody2 = '<thead>
                        <tr>
                            <th class="text-center" rowspan="2"> Sr No </th>
                            <th class="text-center" rowspan="2"> Particulars </th>
                            <th class="text-center" rowspan="2"> Accounts Level 1 Category </th>
                            <th class="text-center" rowspan="2"> Account Name </th>
                            <th class="text-center" colspan="2" style="border-bottom: 0;"> Balance </th>
                            <th class="text-center bus_cat" rowspan="2"> Business Category </th>
                            <th class="text-center acc_cat" rowspan="2"> Accounts Level 1 </th>
                            <th class="text-center acc_cat" rowspan="2"> Accounts Level 2 </th>
                            <th class="text-center acc_cat" rowspan="2"> Accounts Level 3 </th>
                        </tr>
                        <tr>
                            <th class="text-center"> Debit </th>
                            <th class="text-center" style="border-right: 1px solid;"> Credit </th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$tbody2.'
                    </tbody>';

        $data['tbody'] = $tbody;
        $data['tbody2'] = $tbody2;

        // echo '<html><body><table>'.$tbody.'</table></body></html>';

        $report->setLog('TrialBalanceReport', '', 'Generate', '', 'Generate Trial Balance Report', 'acc_ledger_entries', '');
        echo json_encode($data);
    }

    public function actionReconsile() {
       $report = new AccReport();
       $payment_receipt = new PaymentReceipt();
       $acc_details = $report->getVendorname();
       $bank = $payment_receipt->getBanks();
       $data['view']='';
       $data['acc_details']=$acc_details;
       $data['bank']=$bank  ;

       $data['date_criteria']='';
       $data['view']='';
       $data['opening_bal']=0;
       $data['from_date']='';
       $data['to_date']='';
       $data['data']=[];
       return $this->render('reconsile_report',$data);
    }

    public function actionGetreconsile() {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $report = new AccReport();
        $payment_receipt = new PaymentReceipt();
        $acc_details = $report->getVendorname();
        $bank = $payment_receipt->getBanks();

        $date_criteria = $request->post('date_criteria');
        $account = $request->post('account');
        $from_date = $request->post('from_date');
        $to_date = $request->post('to_date');
        $view = $request->post('view');

        if($from_date==''){
            $from_date=NULL;
        } else {
            $from_date=$mycomponent->formatdate($from_date);
        }

        if($to_date==''){
            $to_date=NULL;
        } else {
            $to_date=$mycomponent->formatdate($to_date);
        }
    
        $ledg_balance = 0;
        $result = $report->getLedgerBal($account, $to_date);
        if(count($result)>0){
            $ledg_balance = floatval($result[0]['opening_bal']);
        }

        if($view=='show_reconsiled') {
            $result = $report->getreconsiledonly($account, $from_date, $to_date);
        } else {
            $result = $report->getdefault($account, $from_date, $to_date);
        }
        
        $opening_bal = 0;

        $data['date_criteria']=$date_criteria;
        $data['view']=$view;
        $data['acc_details']=$acc_details;
        $data['bank']=$bank ;
        $data['opening_bal']=$opening_bal;
        $data['ledg_balance']=$ledg_balance;
        $data['account']=$account;
        $data['from_date']=$from_date;
        $data['to_date']=$to_date;
        $data['data']=$result;

        return $this->render('reconsile_report',$data);
    }

    public function actionSave() {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

        $submit = $request->post('submit');

        if($submit=='submit'){
            $reconsile = $request->post('reconsile');
            $payment_date = $request->post('payment_date');

            $report = new AccReport();
            $payment_receipt = new PaymentReceipt();
            $acc_details = $report->getVendorname();
            $bank = $payment_receipt->getBanks();
            $data['view']='';
            $data['acc_details']=$acc_details;
            $data['bank']=$bank  ;

            $data['view']='';;
            $data['opening_bal']=0;
            $data['from_date']='';
            $data['to_date']='';
            $data['data']=[];    

            for ($i=0; $i <count($reconsile) ; $i++) 
            { 
                $reconsiled = $reconsile[$i];
                $paydate = $payment_date[$i];

                if($paydate!="")
                {
                   $paydate=$mycomponent->formatdate($paydate);
                }
                else
                {
                   $paydate = NULL;
                }

                $result = $report->update_ledger($paydate,$reconsiled);
            }
        }

        // return $this->render('reconsile_report',$data);

        return $this->actionGetreconsile();

        // return $this->redirect('myCustomAction');
    }
    
    public function actionGetasperbank() {
       $request = Yii::$app->request;
       $mycomponent = Yii::$app->mycomponent;
       $report = new AccReport();
       $payment_receipt = new PaymentReceipt();
       $account = $request->post('account');
       $from_date = $request->post('from_date');
       $to_date = $request->post('to_date');
       $view = $request->post('view');

       // $account = '689';
       // $from_date = '01/01/2018';
       // $to_date = '31/03/2019';
       // $view = 'default';

       if($from_date==''){
            $from_date=NULL;
        } else {
            $from_date=$mycomponent->formatdate($from_date);
        }

        if($to_date==''){
            $to_date=NULL;
        } else {
            $to_date=$mycomponent->formatdate($to_date);
        }

        $asperbank =0;

        $result = $report->getbalasperbank($account, $from_date, $to_date, $view);
        if(count($result)>0){
            $asperbank = floatval($result[0]['asperbank']);
        }

        echo $asperbank;
    }

    public function actionGetinvoice_detail()
    {   
        $report = new Paymentreceipt();
        $model = new GroupMaster();
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $view = $request->post('view');
        $from_date = $request->post('from_date');
        $to_date = $request->post('to_date');
        $ledger_name = $request->post('ledger_name');
        $group = $request->post('group');

        if($from_date==''){
            $from_date=NULL;
        } else {
            $from_date=$mycomponent->formatdate($from_date);
        }

        if($to_date==''){
            $to_date=NULL;
        } else {
            $to_date=$mycomponent->formatdate($to_date);
        }
        /*echo "<pre>";
        print_r($ledger_name);
        echo "</pre>";*/
        $leader_result = array();

        if(in_array('ALL',$group) && in_array('ALL',$ledger_name))
        {
            $leader_id = array();
            $leader_detail = $model->getLedgerDetail();

            for ($i=0; $i <count($leader_detail) ; $i++) {
                $leader_result[] = $leader_detail[$i]['id'];
            }
            $acc_id = implode($leader_result,",");
        }
        else
        {
            if(in_array('ALL',$ledger_name))
            {
                 unset( $ledger_name[array_search( 'ALL', $ledger_name )] );
            }
            $acc_id = implode($ledger_name,",");
        }

        $type = $request->post('type');
        $j=0;
        if($view=='Summary')
        {
           $acc_details=$report->getInvoicewiseLedger($acc_id,$from_date,$to_date,$type);
           $start = $request->post('start'); 
           $invoice_detail = array();

            //$params['start'].", ".$params['length']
            for($i=0; $i<count($acc_details); $i++) { 

                $j = $j+1;

                if($acc_details[$i]['openingtype']=='Credit')
                    $otype = 'Cr';
                else
                    $otype = 'Dr';

                if($acc_details[$i]['type']=='Debit')
                    $type = 'Cr';
                else
                    $type = 'Dr';

                $invoice_date = '';
                if($acc_details[$i]['ref_date']!='' || $acc_details[$i]['ref_date']!=null)
                {
                    $invoice_date = date('d-F-Y',strtotime($acc_details[$i]['ref_date']));
                }

                $due_date = '';
                if($acc_details[$i]['due_date']!='' || $acc_details[$i]['due_date']!=null)
                {
                    $due_date = date('d-F-Y',strtotime($acc_details[$i]['due_date']));
                }

               $row = array(
                            ''.$acc_details[$i]['invoice_no'].'',
                            ''.$invoice_date.'',
                            ''.$acc_details[$i]['opening_amount'].' '.$otype,
                            ''.$mycomponent->format_money($acc_details[$i]['bal_amount'], 2).' '.$type.'',
                            ''.$due_date.'',
                            ''.$acc_details[$i]['overdueby'].'',
                            ) ;
               $invoice_detail[] = $row;
               $start = $start+1;
            }
            $json_data = array(
                    "draw"            => intval($request->post('draw')),   
                    "recordsTotal"    => count($acc_details),  
                    "recordsFiltered" => count($acc_details),
                    "data"            => $invoice_detail
                    );

            echo json_encode($json_data);
        }
        else
        {
         
           $acc_details=$report->getInvoicewiseLedger($acc_id,$from_date,$to_date,$type);
           $start = $request->post('start'); 
           $invoice_detail = array(); 
           $prev_invo = '';  
            //$params['start'].", ".$params['length']
           $count = 0;
            for($i=0; $i<count($acc_details); $i++) { 
                if($acc_details[$i]['openingtype']=='Credit')
                    $otype = 'Cr';
                else
                    $otype = 'Dr';

                if($acc_details[$i]['type']=='Debit')
                    $type = 'Cr';
                else
                    $type = 'Dr';

                $j = $j+1;
                if($prev_invo!=$acc_details[$i]['invoice_no'])
                {
                    $invoice_date = '';
                    if($acc_details[$i]['ref_date']!='' || $acc_details[$i]['ref_date']!=null)
                    {
                        $invoice_date = date('d-F-Y',strtotime($acc_details[$i]['ref_date']));
                    }

                    $due_date = '';
                    if($acc_details[$i]['due_date']!='' || $acc_details[$i]['due_date']!=null)
                    {
                        $due_date = date('d-F-Y',strtotime($acc_details[$i]['due_date']));
                    }

                   $prev_invo = $acc_details[$i]['invoice_no'];
                   $count=$count+1;
                   $row = array(
                                ''.$invoice_date.'',
                                ''.$acc_details[$i]['invoice_no'].'',
                                'Opening Balance',
                                '',
                                '',
                                '',
                                '',
                                '',
                                ''.$acc_details[$i]['opening_amount'].' '.$otype,
                                '',
                                '',
                                ''
                            ) ;
                        $invoice_detail[] = $row;
                        $debit_note = '';
                        if($acc_details[$i]['ref_type']=='other_debit_credit')
                        {
                            $acc_details[$i]['ref_type'] = 'Debit Note';
                            $debit_note = $acc_details[$i]['invoice_no'];
                        }

                        $row = array(
                                ''.$invoice_date.'',
                                '',
                                ''.ucfirst($acc_details[$i]['ref_type']).'',
                                ''.$acc_details[$i]['gi_id'].'',
                                ''.$acc_details[$i]['gi_go_ref_no'].'',
                                ''.$debit_note.'',
                                ''.$acc_details[$i]['ref_id'].'',
                                ''.$acc_details[$i]['amount'].''.$type,
                                ''.$acc_details[$i]['opening_amount'].' '.$otype,
                                ''.$mycomponent->format_money($acc_details[$i]['bal_amount'], 2).' '.$type.'',
                                ''.$due_date.'',
                                ''.$acc_details[$i]['overdueby'].'',
                            ) ;
                        $invoice_detail[] = $row;
                }

            }
            $json_data = array(
                    "draw"            => intval($request->post('draw')),   
                    "recordsTotal"    => count($acc_details),  
                    "recordsFiltered" => count($acc_details),
                    "data"            => $invoice_detail
                    );

            echo json_encode($json_data);
        }
    }

     public function actionInvoice_wise()
    {
        $model = new GroupMaster();
        $access = $model->getAccess();
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $submit = $request->post('submit');
        
        if(count($access)>0) {
            if($access[0]['r_view']==1) {
            if($submit=='submit'){
                $group = $request->post('group');                
                $view = $request->post('view');
                $from_date = $request->post('from_date');
                $to_date = $request->post('to_date');
                $type = $request->post('type');
                $group_ids = implode($group, ",");
                $leder_id = $request->post('ledger_name');
                $array = '';
                for ($i=0; $i <count($group) ; $i++) { 
                    $array = $array . "'" . $group[$i]."', ".
                    $array = $array . $model->getGroupDetails_ids($group[$i]);
                }
                /*$group_ids =  rtrim($array,',');*/
                $group_ids = substr($array, 0, -1);
                /*echo "<pre>";
                print_r($group);
                echo "</pre>";*/
                $ledername = $model->getLedgerDetail();
                $ledger_name = $ledername;
                $data['ledger_name']=$ledger_name;
                $select = '';
                if(in_array('ALL',$group))
                { 
                    $select='selected';
                }
                $explode_id = implode(",",$group);
                $list1 = $model->getGroupDetails_invoice(0,$submit,$explode_id);
                $list = '<select class="form-control group" id="account" name="group[]" multiple="multiple" data-error="#accounterror" ><option value="ALL" '.$select.'>ALL</option>'.$list1.'</select>';
            }
            else
            {
                $group = '';                
                $view = '';
                $from_date = '';
                $to_date = '';
                $type = '';
                $ledger_name=[];
                $leder_id=[];
                
                $list1 = $model->getGroupDetails_invoice(0);
                $list = '<select class="form-control group" id="account" name="group[]" multiple="multiple" data-error="#accounterror"><option value="ALL">ALL</option>'.$list1.'</select>';
            }

            $model->setLog('GroupMaster', '', 'View', '', 'View Group Master Details', 'group_master', '');
                return $this->render('invoice_wise', ['select' => $list,'from_date'=>$from_date,'to_date'=>$to_date,'view'=>$view,'type'=>$type,'ledger_name'=>$ledger_name,'leder_id'=>$leder_id,'group'=>$group]);
            } else {
                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'Access Denied'),
                    'module' => $this->module,
                    'msg' => '<h4>You donot have access to this page.</h4>'
                ]);
            }
        }
    }

    public function actionGetledger_name()
    {
        $model = new GroupMaster();
        $access = $model->getAccess();
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $group = $request->post('group');
        if(in_array('ALL',$group))
        {
            $ledername = $model->getLedgerDetail();
            echo json_encode($ledername);
        }
        else
        {
            $array = '';
            for ($i=0; $i <count($group) ; $i++) { 
                $array = $array . "'" . $group[$i]."', ".
                $array = $array . $model->getGroupDetails_ids($group[$i]);
            }
            $group_ids = substr($array, 0, -1);
            $ledername = $model->getLedgerDetail($group_ids);
            echo json_encode($ledername);
        }
    }
}