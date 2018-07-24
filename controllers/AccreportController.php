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
        
        $account = implode(",",$account);
       
        if(count($vouchertype)==0)
        {
            $data['vouchertype']=[];
            $vouchertype = array('purchase','journal_voucher','payment_receipt','go_debit_details','other_debit_credit','promotion');
        }
        else
        {
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
        
        $report = new AccReport();

        if($view=='state')
        {
            $acc_details= $report->getstatewisebifercation($account, $vouchertype,$from_date, $to_date,$date_type,$state);

            $innertab = '';
            $voucher = 0;
            $temp = 0;

            $column_names= $report->state_wise_column($account, $vouchertype,$from_date, $to_date,$date_type,$state);
            $colname = '';

            foreach ($column_names as $res)
            { 
               $stat_val = explode('-', $res['ledger_name']);
               if (is_numeric($res['percentage'])) 
               {
                   $per_val = $res['percentage']/2;
                   $colname.='<th class="text-center">'.' Purchase '.$stat_val[1].'Exc Tax GST - '.$res['percentage'].'%'.'</th>';
                   $colname.='<th class="text-center">'.$stat_val[1].' IGST Tax'.$res['percentage'].'%';
                   $colname.='<th class="text-center">'.$stat_val[1].' CGST Tax'.$per_val.'%';
                   $colname.='<th class="text-center">'.$stat_val[1].' SGST Tax'.$per_val.'%';
                   $colname.='<th class="text-center">'.'Purchase '.$stat_val[1].' Total Tax '.'</th>';
                   $colname.='<th class="text-center">'.'Purchase '.$stat_val[1].' Total Purchase Amount Inc Tax</th>';
               }
            }

            for ($i=0; $i <count($acc_details) ; $i++) { 
                
                $voucher_id = $acc_details[$i]['voucher_id'];
                $voucher;
                $stat_val1 = explode('-', $acc_details[$i]['ledger_name']); 
                if($voucher_id!=$voucher)
                    {   
                        
                        if($voucher!=0)
                        {   
                            for($j=$temp+1;$j<count($column_names);$j++)
                            {
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                            }
                            $innertab.="</tr>";
                        }

                        $temp=0;
                        $innertab.='<tr>
                        <td>'.($acc_details[$i]['invoice_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['invoice_date'])):'').'</td>
                        <td>'.($acc_details[$i]['grn_approved_date_time']!=""?date('Y-m-d',strtotime($acc_details[$i]['grn_approved_date_time'])):'').'</td>
                        <td>'.($acc_details[$i]['gi_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['gi_date'])):'').'</td>
                        <td>'.($acc_details[$i]['updated_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['updated_date'])):'').'</td>
                        <td>'.$acc_details[$i]['cp_ledger_name'].'</td>
                        <td>'.$acc_details[$i]['ref_type'].'</td>
                        <td>'.$acc_details[$i]['voucher_id'].'</td>
                        <td>'.($acc_details[$i]['ref_type']=='journal_voucher'?'':$acc_details[$i]['ref_id']).'</td>
                        <td>'.$acc_details[$i]['gi_go_ref_no'].'</td>
                        <td>'.($acc_details[$i]['ref_type']=='Debit Note'?$acc_details[$i]['debit_note_ref']:$acc_details[$i]['invoice_no']).'</td>
                        <td>'.$acc_details[$i]['total_deduction'].'</td>';

                       $tax = '';
                         
                        for($j=0;$j<count($column_names);$j++)
                        {
                           $stat_val2 = explode('-', $column_names[$j]['ledger_name']);

                          if(((int)$acc_details[$i]['percentage']==(int)$column_names[$j]['percentage'] ) && trim($stat_val1[1])==trim($stat_val2[1]))
                            {

                               if($acc_details[$i]['entry_type']=='IGST')
                               {
                                    $innertab.='<td>'.$acc_details[$i]['purchase'].'</td>';
                                    $innertab.='<td>'.$acc_details[$i]['total_tax_amount'].'</td>';
                                    $innertab.='<td> </td>';
                                    $innertab.='<td> </td>';
                                    $innertab.='<td>'.$acc_details[$i]['total_tax_amount'].'</td>';
                                    $innertab.='<td>'.$acc_details[$i]['cost_inc_tax'].'</td>';
                                    $temp = $j;
                                    break; 
                               }
                               else
                               {

                                    $tax = $acc_details[$i]['total_tax_amount']/2;
                                    $innertab.='<td>'.$acc_details[$i]['purchase'].'</td>';
                                    $innertab.='<td> </td>';
                                    $innertab.='<td>'.$tax.'</td>';
                                    $innertab.='<td>'.$tax.'</td>';
                                    $innertab.='<td>'.$acc_details[$i]['total_tax_amount'].'</td>';
                                    $innertab.='<td>'.$acc_details[$i]['cost_inc_tax'].'</td>';
                                    $temp = $j;
                                    break;  
                               }
                               
                            }
                            else{
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                            }
                        
                        }
                    }
                    else
                    { 
                      for($j=$temp+1;$j<count($column_names);$j++)
                        {
                          $stat_val2 = explode('-', $column_names[$j]['ledger_name']);
                          if(((int)$acc_details[$i]['percentage']==(int)$column_names[$j]['percentage']) && trim($stat_val1[1])==trim($stat_val2[1]))
                            {
                               if($acc_details[$i]['entry_type']=='IGST')
                                       {
                                            $innertab.='<td>'.$acc_details[$i]['purchase'].'</td>';
                                            $innertab.='<td>'.$acc_details[$i]['total_tax_amount'].'</td>';
                                            $innertab.='<td> </td>';
                                            $innertab.='<td> </td>';
                                            $innertab.='<td>'.$acc_details[$i]['total_tax_amount'].'</td>';
                                            $innertab.='<td>'.$acc_details[$i]['cost_inc_tax'].'</td>';
                                            $temp = $j;
                                            break;
                                       }
                                       else
                                       {
                                            $tax = $acc_details[$i]['total_tax_amount']/2;
                                            $innertab.='<td>'.$acc_details[$i]['purchase'].'</td>';
                                            $innertab.='<td> </td>';
                                            $innertab.='<td>'.$tax.'</td>';
                                            $innertab.='<td>'.$tax.'</td>';
                                            $innertab.='<td>'.$acc_details[$i]['total_tax_amount'].'</td>';
                                            $innertab.='<td>'.$acc_details[$i]['cost_inc_tax'].'</td>';
                                            $temp = $j;
                                            break;  
                                       }
                            }
                            else{
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                            }
                        }
                    }
                  $voucher  = $acc_details[$i]['voucher_id'];
            }

            if($temp<count($column_names)-1){
                for($j=$temp+1;$j<count($column_names);$j++)
                {
                    $innertab.='<td> </td>';
                    $innertab.='<td> </td>';
                    $innertab.='<td> </td>';
                    $innertab.='<td> </td>';
                    $innertab.='<td> </td>';
                    $innertab.='<td> </td>';
                }
                $innertab.="</tr>";
            }
            
            $table ='<thead>
                            <tr><th class="text-center">Invoice Date</th>
                                <th class="text-center">Grn Approved Date</th>
                                <th class="text-center"> GI Date</th>
                                <th class="text-center"> Posting Date </th>
                                <th class="text-center"> Vendor Name</th>
                                <th class="text-center"> Voucher Type </th>
                                <th class="text-center"> Voucher No </th>
                                <th class="text-center"> Grn No  </th>
                                <th class="text-center"> Go No </th>
                                <th class="text-center"> Invoice No/ Debit Note No </th>
                                <th class="text-center"> Sum Total</th>'.$colname.'</tr>
                    </thead>'.$innertab;
        }
        
        if($view=='tax')
        {
            $acc_details= $report->gettaxwisebifercation($account, $vouchertype,$from_date, $to_date,$date_type,$state);

            $innertab = '';
            $voucher = 0;
            $temp = 0;
            $sum_total = 0;

            $column_names= $report->tax_wise_column($account, $vouchertype,$from_date, $to_date,$date_type,$state);
            $colname = '';
            $tabrow = '';

            // echo json_encode($column_names);
            // echo '<br/>';

            foreach ($column_names as $res)
            {
               $colname.='<th class="text-center">'.'Purchase Value Exc Tax - '.$res['percentage'].'%'.'</th>';
               $colname.='<th class="text-center">'.'Tax -'.$res['percentage'].'% Total Amount'.'</th>';
               $colname.='<th class="text-center">'.'Total Purchase inc Tax - '.$res['percentage'].'</th>';
            }

            // echo json_encode($acc_details);
            for ($i=0; $i <count($acc_details) ; $i++) { 

                $voucher_id = $acc_details[$i]['voucher_id'];

                    if($voucher_id!=$voucher)
                    {   
                        if($voucher!=0)
                        {   
                            for($j=$temp+1;$j<count($column_names);$j++)
                            {
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                            }

                            $tabrow.='<tr>
                                        <td>'.($acc_details[$i-1]['invoice_date']!=""?date('Y-m-d',strtotime($acc_details[$i-1]['invoice_date'])):'').'</td>
                                        <td>'.($acc_details[$i-1]['grn_approved_date_time']!=""?date('Y-m-d',strtotime($acc_details[$i-1]['grn_approved_date_time'])):'').'</td>
                                        <td>'.($acc_details[$i-1]['gi_date']!=""?date('Y-m-d',strtotime($acc_details[$i-1]['gi_date'])):'').'</td>
                                        <td>'.($acc_details[$i-1]['updated_date']!=""?date('Y-m-d',strtotime($acc_details[$i-1]['updated_date'])):'').'</td>
                                        <td>'.$acc_details[$i-1]['cp_ledger_name'].'</td>
                                        <td>'.$acc_details[$i-1]['ref_type'].'</td>
                                        <td>'.$acc_details[$i-1]['voucher_id'].'</td>
                                        <td>'.($acc_details[$i-1]['ref_type']=='journal_voucher'?'':$acc_details[$i-1]['ref_id']).'</td>
                                        <td>'.$acc_details[$i-1]['gi_go_ref_no'].'</td>
                                        <td>'.($acc_details[$i-1]['ref_type']=='Debit Note'?$acc_details[$i-1]['debit_note_ref']:$acc_details[$i-1]['invoice_no']).'</td>
                                        <td>'.$sum_total.'</td>'.$innertab.'
                                    </tr>';
                        }

                        $temp=0;
                        $sum_total=0;
                        // $innertab.='<tr>
                        // <td>'.($acc_details[$i]['invoice_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['invoice_date'])):'').'</td>
                        // <td>'.($acc_details[$i]['grn_approved_date_time']!=""?date('Y-m-d',strtotime($acc_details[$i]['grn_approved_date_time'])):'').'</td>
                        // <td>'.($acc_details[$i]['gi_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['gi_date'])):'').'</td>
                        // <td>'.($acc_details[$i]['updated_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['updated_date'])):'').'</td>
                        // <td>'.$acc_details[$i]['cp_ledger_name'].'</td>
                        // <td>'.$acc_details[$i]['ref_type'].'</td>
                        // <td>'.$acc_details[$i]['voucher_id'].'</td>
                        // <td>'.($acc_details[$i]['ref_type']=='journal_voucher'?'':$acc_details[$i]['ref_id']).'</td>
                        // <td>'.$acc_details[$i]['gi_go_ref_no'].'</td>
                        // <td>'.($acc_details[$i]['ref_type']=='Debit Note'?$acc_details[$i]['debit_note_ref']:$acc_details[$i]['invoice_no']).'</td>
                        // <td>'.$acc_details[$i]['total_deduction'].'</td>';

                        $innertab = '';

                        for($j=0;$j<count($column_names);$j++)
                        {
                            if($acc_details[$i]['percentage']==$column_names[$j]['percentage'])
                            {
                                $sum_total = $sum_total + $acc_details[$i]['cost_inc_tax'];

                                $innertab.='<td>'.$acc_details[$i]['purchase'].'</td>';
                                $innertab.='<td>'.$acc_details[$i]['total_tax_amount'].'</td>';
                                $innertab.='<td>'.$acc_details[$i]['cost_inc_tax'].'</td>';
                                $temp = $j;
                                break;   
                            }
                            else {
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                            }
                        }
                    }
                    else
                    {   
                        for($j=$temp+1;$j<count($column_names);$j++)
                        {
                            if($acc_details[$i]['percentage']==$column_names[$j]['percentage'])
                            {
                                $sum_total = $sum_total + $acc_details[$i]['cost_inc_tax'];

                                $innertab.='<td>'.$acc_details[$i]['purchase'].'</td>';
                                $innertab.='<td>'.$acc_details[$i]['total_tax_amount'].'</td>';
                                $innertab.='<td>'.$acc_details[$i]['cost_inc_tax'].'</td>';
                                $temp = $j;
                                break;
                            }
                            else{
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                                $innertab.='<td> </td>';
                            }
                        }
                    }
                    
                    $voucher  = $acc_details[$i]['voucher_id'];
            }

            if($temp<count($column_names)-1){
                for($j=$temp+1;$j<count($column_names);$j++)
                {
                    $innertab.='<td> </td>';
                    $innertab.='<td> </td>';
                    $innertab.='<td> </td>';
                }
            }

            $tabrow.='<tr>
                        <td>'.($acc_details[$i-1]['invoice_date']!=""?date('Y-m-d',strtotime($acc_details[$i-1]['invoice_date'])):'').'</td>
                        <td>'.($acc_details[$i-1]['grn_approved_date_time']!=""?date('Y-m-d',strtotime($acc_details[$i-1]['grn_approved_date_time'])):'').'</td>
                        <td>'.($acc_details[$i-1]['gi_date']!=""?date('Y-m-d',strtotime($acc_details[$i-1]['gi_date'])):'').'</td>
                        <td>'.($acc_details[$i-1]['updated_date']!=""?date('Y-m-d',strtotime($acc_details[$i-1]['updated_date'])):'').'</td>
                        <td>'.$acc_details[$i-1]['cp_ledger_name'].'</td>
                        <td>'.$acc_details[$i-1]['ref_type'].'</td>
                        <td>'.$acc_details[$i-1]['voucher_id'].'</td>
                        <td>'.($acc_details[$i-1]['ref_type']=='journal_voucher'?'':$acc_details[$i-1]['ref_id']).'</td>
                        <td>'.$acc_details[$i-1]['gi_go_ref_no'].'</td>
                        <td>'.($acc_details[$i-1]['ref_type']=='Debit Note'?$acc_details[$i-1]['debit_note_ref']:$acc_details[$i-1]['invoice_no']).'</td>
                        <td>'.$sum_total.'</td>'.$innertab.'
                    </tr>';
            
            $table = '<thead>
                            <tr><th class="text-center">Invoice Date</th>
                                <th class="text-center">Grn Approved Date</th>
                                <th class="text-center"> GI Date</th>
                                <th class="text-center"> Posting Date </th>
                                <th class="text-center"> Vendor Name</th>
                                <th class="text-center"> Voucher Type </th>
                                <th class="text-center"> Voucher No </th>
                                <th class="text-center"> Grn No  </th>
                                <th class="text-center"> Go No </th>
                                <th class="text-center"> Invoice No/ Debit Note No </th>
                                <th class="text-center"> Sum Total</th>'.$colname.'</tr>
                    </thead><tbody>'.$tabrow.'</tbody>';

            // echo '<html><body><table>'.$table.'</table></body></table>';
        }
        
        if($view=='default')
        {   
            $acc_details= $report->getDetailledger($account, $vouchertype,$from_date, $to_date,$date_type,$state);
            $innertab = '';
            $voucher = 0;
            $temp = 0;

            /*foreach ($result as $acc_details)
            {
                $acc_details['invoice_date'];
                $innertab.='<tr>
                <td>'.($acc_details['invoice_date']!=""?date('Y-m-d',strtotime($acc_details['invoice_date'])):'').'</td>
                <td>'.($acc_details['grn_approved_date_time']!=""?date('Y-m-d',strtotime($acc_details['grn_approved_date_time'])):'').'</td>
                <td>'.($acc_details['gi_date']!=""?date('Y-m-d',strtotime($acc_details['gi_date'])):'').'</td>
                <td>'.($acc_details['updated_date']!=""?date('Y-m-d',strtotime($acc_details['updated_date'])):'').'</td>
                <td>'.$acc_details['cp_ledger_name'].'</td>
                <td>'.$acc_details['ref_type'].'</td>
                <td>'.$acc_details['voucher_id'].'</td>
                <td>'.($acc_details['ref_type']=='journal_voucher'?'':$acc_details['ref_id']).'</td>
                <td>'.$acc_details['gi_go_ref_no'].'</td>
                <td>'.($acc_details['ref_type']=='Debit Note'?$acc_details['debit_note_ref']:$acc_details['invoice_no']).'</td>
                <td>'.$acc_details['total_deduction'].'</td>
                <td>'.$acc_details['total_without_tax'].'</td>
                <td>'.$acc_details['tax_amount'].'</td>
                <td>'.$acc_details['total_deduction'].'</td>';
            }*/
            $colname = '';
            if(in_array('journal_voucher',$vouchertype))
            {
               $column_names= $report->column_names($account, $vouchertype,$from_date, $to_date,$date_type,$state);
                foreach ($column_names as $res)
                {
                   $colname.='<th class="text-center">'.$res['ledger_name'].'</th>';
                } 
            }
            else
            {
                $column_names= [];
            }
            
            for ($i=0; $i <count($acc_details) ; $i++) { 
                
                if($acc_details[$i]['ref_type']=='journal_voucher')
                {
                    
                    $voucher_id = $acc_details[$i]['voucher_id'];
               
                    if($voucher_id!=$voucher)
                    {   
                        if($voucher!=0)
                        {   
                            for($j=$temp+1;$j<count($column_names);$j++)
                            {
                                $innertab.='<td> </td>';
                            }
                            $innertab.="</tr>";
                        }

                        $temp=0;
                        $innertab.='<tr>
                        <td>'.($acc_details[$i]['invoice_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['invoice_date'])):'').'</td>
                        <td>'.($acc_details[$i]['grn_approved_date_time']!=""?date('Y-m-d',strtotime($acc_details[$i]['grn_approved_date_time'])):'').'</td>
                        <td>'.($acc_details[$i]['gi_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['gi_date'])):'').'</td>
                        <td>'.($acc_details[$i]['updated_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['updated_date'])):'').'</td>
                        <td>'.$acc_details[$i]['cp_ledger_name'].'</td>
                        <td>Journal Voucher</td>
                        <td>'.$acc_details[$i]['voucher_id'].'</td>
                        <td>'.($acc_details[$i]['ref_type']=='purchase'?$acc_details[$i]['ref_id']:'').'</td>
                        <td>'.$acc_details[$i]['gi_go_ref_no'].'</td>
                        <td>'.($acc_details[$i]['ref_type']=='Debit Note'?$acc_details[$i]['debit_note_ref']:$acc_details[$i]['invoice_no']).'</td>
                        <td>'.$acc_details[$i]['total_deduction'].'</td>
                        <td>'.$acc_details[$i]['total_without_tax'].'</td>
                        <td>'.$acc_details[$i]['tax_amount'].'</td>
                        <td>'.$acc_details[$i]['total_deduction'].'</td>';

                        for($j=0;$j<count($column_names);$j++)
                        {
                          if($acc_details[$i]['ledger_name']==$column_names[$j]['ledger_name'])
                            {
                               $innertab.='<td>'.$acc_details[$i]['amount1'].'</td>';
                               $temp = $j;
                               break;
                            }
                            else{
                                $innertab.='<td> </td>';
                            }
                        }
                    }
                    else
                    {   

                        for($j=$temp+1;$j<count($column_names);$j++)
                        {
                          if($acc_details[$i]['ledger_name']==$column_names[$j]['ledger_name'])
                            {
                                $innertab.='<td>'.$acc_details[$i]['amount1'].'</td>';
                                $temp = $j;
                            }
                            else{
                                $innertab.='<td> </td>';
                            }
                        }
                    }
                  $voucher  = $acc_details[$i]['voucher_id'];
                }
                else
                {
                    $innertab.='<tr>
                        <td>'.($acc_details[$i]['invoice_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['invoice_date'])):'').'</td>
                        <td>'.($acc_details[$i]['grn_approved_date_time']!=""?date('Y-m-d',strtotime($acc_details[$i]['grn_approved_date_time'])):'').'</td>
                        <td>'.($acc_details[$i]['gi_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['gi_date'])):'').'</td>
                        <td>'.($acc_details[$i]['updated_date']!=""?date('Y-m-d',strtotime($acc_details[$i]['updated_date'])):'').'</td>
                        <td>'.$acc_details[$i]['cp_ledger_name'].'</td>
                        <td>'.$acc_details[$i]['ref_type'].'</td>
                        <td>'.$acc_details[$i]['voucher_id'].'</td>
                        <td>'.($acc_details[$i]['ref_type']=='purchase' || $acc_details[$i]['ref_type']=='Debit Note'?$acc_details[$i]['ref_id']:'').'</td>
                        <td>'.$acc_details[$i]['gi_go_ref_no'].'</td>
                        <td>'.($acc_details[$i]['ref_type']=='Debit Note'?$acc_details[$i]['debit_note_ref']:$acc_details[$i]['invoice_no']).'</td>
                        <td>'.$acc_details[$i]['total_deduction'].'</td>
                        <td>'.$acc_details[$i]['total_without_tax'].'</td>
                        <td>'.$acc_details[$i]['tax_amount'].'</td>
                        <td>'.$acc_details[$i]['total_deduction'].'</td>';
                        for($j=0;$j<count($column_names);$j++)
                        {
                            $innertab.='<td> </td>';
                        }
                        $innertab.='</tr>';
                }
            }       
            $table ='<thead>
                            <tr><th class="text-center">Invoice Date</th>
                                <th class="text-center">Grn Approved Date</th>
                                <th class="text-center"> GI Date</th>
                                <th class="text-center"> Posting Date </th>
                                <th class="text-center"> Vendor Name</th>
                                <th class="text-center"> Voucher Type </th>
                                <th class="text-center"> Voucher No </th>
                                <th class="text-center"> Grn No  </th>
                                <th class="text-center"> Go No </th>
                                <th class="text-center"> Invoice No/ Debit Note No </th>
                                <th class="text-center"> Sum Total</th>
                                <th class="text-center"> Value Exc Tax </th>
                                <th class="text-center"> Total Tax Amount </th>
                                <th class="text-center"> Total Inc Tax </th>'.$colname.'</tr>
                    </thead>'.$innertab;  
        }

        $acc_details = $report->getAccountDetails();
        $state_master = $report->getstatemaster();    
        $data['acc_details'] = $acc_details;
        $data['state_detail'] = $state_master;     
        $data['table'] = $table;  

        for($i=0;$i<count($account);$i++)
        {
          $report->setLog('LedgerReport', '', 'Generate', '', 'Generate Ledger Report', 'acc_ledger_entries', $account[$i]);
        }   
        
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
        return $this->render('summery_ledger_report.php', ['acc_details' => $acc_details, 'opening_bal' => $opening_bal, 
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
                    $tot_deb_ope_bal = $tot_deb_ope_bal + $opening_bal;
                } else {
                    $tot_crd_ope_bal = $tot_crd_ope_bal + $opening_bal;
                }
                if($closing_bal<0){
                    $tot_deb_clo_bal = $tot_deb_clo_bal + $closing_bal;
                } else {
                    $tot_crd_clo_bal = $tot_crd_clo_bal + $closing_bal;
                }
                $tot_deb_tran = $tot_deb_tran + $debit_amt;
                $tot_crd_tran = $tot_crd_tran + $credit_amt;

                // $opening_bal = $closing_bal;
            }

            $tbody = $tbody . '<tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Grant Total</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_deb_ope_bal*-1,2).'</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_crd_ope_bal,2).'</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_deb_tran,2).'</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_crd_tran,2).'</td>
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_deb_clo_bal*-1,2).'</td>
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
                                    <td style="text-align: right;">'.$mycomponent->format_money($tot_deb_clo_bal*-1,2).'</td>
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

        $report->setLog('TrialBalanceReport', '', 'Generate', '', 'Generate Trial Balance Report', 'acc_ledger_entries', '');
        echo json_encode($data);
    }

    public function actionReconsile()
    {
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
       return $this->render('reconsile_report',$data);
    }

    public function actionGetreconsile()
    {
       $request = Yii::$app->request;
       $mycomponent = Yii::$app->mycomponent;
       $report = new AccReport();
       $payment_receipt = new PaymentReceipt();
       $acc_details = $report->getVendorname();
       $bank = $payment_receipt->getBanks();
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
    
        if($view=='show_reconsiled')
        {
            $result = $report->getreconsiledonly($account, $from_date, $to_date);
        }
        else
        {
            $result = $report->getdefault($account, $from_date, $to_date);
        }
       
       $opening_bal = 0;
       $data['view']=$view;
       $data['acc_details']=$acc_details;
       $data['bank']=$bank ;
       $data['opening_bal']=$opening_bal;
       $data['account']=$account;
       $data['from_date']=$from_date;
       $data['to_date']=$to_date;
       $data['data']=$result;
       return $this->render('reconsile_report',$data);
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;

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
            
        return $this->render('reconsile_report',$data);  
    }
      
       
   public function actionGetasperbank()
   {
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
}