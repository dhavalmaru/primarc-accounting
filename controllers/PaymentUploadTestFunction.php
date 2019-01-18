<?php

 public function actionDownloadleadger_old()
    {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $payment_receipt = new PaymentReceipt();
        $id = $request->post('id');
        $acc_id = $request->post('acc_id');//10;
        $to_date = $request->post('to_date');
        if($to_date==''){
        } else {
           $to_date=$mycomponent->formatdate($to_date);
        }


        if(in_array('ALL',$acc_id))
        {   
            $array_id = array();
            $acc_details = $payment_receipt->getAccountDetails();
            for ($i=0; $i <count($acc_details) ; $i++) { 
                 $array_id[]=$acc_details[$i]['id'];
            }

            $acc_id  = implode($array_id,",");
        }
        else
        {
            $acc_id = implode($acc_id,",");
        }

        $payment_receipt = new PaymentReceipt();
        $data = $payment_receipt->getLedger($id, $acc_id,$to_date);
        $data1 = $payment_receipt->getDetails($acc_id, "");
        $mycomponent = Yii::$app->mycomponent;
        $tbody = "";


        $original_file = 'uploads/payment_file/sample_payment.xlsx';
        $objPHPExcel = IOFactory::load($original_file);
        $objPHPExcel->setActiveSheetIndex(0);
        /*$highestrow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); */
        if(count($data)>0){
            $total_debit_amt = 0;
            $total_credit_amt = 0;
            $paying_debit_amt = 0;
            $paying_credit_amt = 0;
            $net_debit_amt = 0;
            $net_credit_amt = 0;
            $total_transaction = '';
            $total_amount_total = 0;
            $total_paid_transaction = '';
            $total_paid_amount_total = 0;
            $paying_transaction = '';
            $paying_amount_total = 0;
            $bal_transaction = '';
            $bal_amount_total = 0;
            $col=0;
            $row = 1;

            $r_row = 2;
            for($i=0; $i<count($data); $i++){
                $ledger_code = '';
                $ledger_name = '';
                $transaction = '';

                $transaction = $data[$i]['type'];
                $amount = $data[$i]['amount'];
                $total_paid_amount = $data[$i]['total_paid_amount'];
                $amount_to_pay = $data[$i]['amount_to_pay'];
                $bal_amount = $data[$i]['bal_amount'];

                $bal_amount = ($amount-$total_paid_amount-$amount_to_pay);
                
                if(strtoupper(trim($transaction))=="DEBIT"){
                    $amount = $amount*-1;
                    $total_paid_amount = $total_paid_amount*-1;
                    $amount_to_pay = $amount_to_pay*-1;
                    $bal_amount = $bal_amount*-1;
                }

                $total_amount_total = $total_amount_total+$amount;
                $total_paid_amount_total = $total_paid_amount_total+$total_paid_amount;
                $paying_amount_total = $paying_amount_total+$amount_to_pay;
                $bal_amount_total = $bal_amount_total+$bal_amount;

                if(strtoupper(trim($transaction))=="DEBIT"){
                    $debit_amt = $amount;
                    $credit_amt = 0;
                } else {
                    $debit_amt = 0;
                    $credit_amt = $amount;
                }

                if(isset($data[$i]['cp_acc_id'])){
                    if($data[$i]['cp_acc_id']!=$acc_id){
                        $ledger_code = $data[$i]['cp_ledger_code'];
                        $ledger_name = $data[$i]['cp_ledger_name'];
                    }
                }
                if($ledger_code == ''){
                    $ledger_code = $data[$i]['ledger_code'];
                    // $ledger_name = $data[$i]['new_ledger_name'];
                    $ledger_name = $data[$i]['ref_type'];
                }

                $ids = $data[$i]['id'].' ,'.$data[$i]['ledger_type'].' ,'.$data[$i]['vendor_id'].' ,';

                $objPHPExcel->getActiveSheet()->setCellValue('A'.$r_row,($i+1));
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$r_row,$data[$i]['ledger_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$r_row,'AC001');
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$r_row,$ids);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$r_row,$data[$i]['voucher_id']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$r_row,$data[$i]['ref_type']);
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$r_row,$data[$i]['entry_type']);
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$r_row,$data[$i]['invoice_no']);
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$r_row,date("d-m-Y",strtotime($data[$i]['gi_date'])));
                $objPHPExcel->getActiveSheet()->setCellValue('J'.$r_row,$data[$i]['invoice_date']);
                $objPHPExcel->getActiveSheet()->setCellValue('K'.$r_row,$data[$i]['due_date']);
                $objPHPExcel->getActiveSheet()->setCellValue('L'.$r_row,$data[$i]['type']);
                $objPHPExcel->getActiveSheet()->setCellValue('M'.$r_row,$amount);
                $objPHPExcel->getActiveSheet()->setCellValue('N'.$r_row,$total_paid_amount);
                $objPHPExcel->getActiveSheet()->setCellValue('O'.$r_row,$bal_amount);
               
                $objPHPExcel->getActiveSheet()->getStyle('P'.$r_row.':U'.$r_row)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                $r_row = $r_row+1;
            }
        }  
        $objPHPExcel->getActiveSheet()->getProtection()->setPassword('dhaval1234');
        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
        
        $bank = $payment_receipt->getBanks();
        $row_t = 1;  
        
        $objPHPExcel->setActiveSheetIndex(1);
        for($i=0; $i <count($bank); $i++) { 
             $objPHPExcel->getActiveSheet()->setCellValue('A'.$row_t,$bank[$i]['legal_name']);

             $row_t = $row_t+1;
        }

        $objPHPExcel->setActiveSheetIndex(0);

         

        for($j=2;$j<=100;$j++)
        {
            $objValidation = $objPHPExcel->getActiveSheet(0)->getCell('Q'.$j)->getDataValidation();
            $this->common_excel($objValidation);
            $objValidation->setFormula1('\'Sheet2\'!$A$1:$A$'.($row_t-1));
        }

        for($k=2;$k<=100;$k++)
        {
            $objValidation = $objPHPExcel->getActiveSheet(0)->getCell('T'.$k)->getDataValidation();
            $this->common_excel($objValidation);
            $objValidation->setFormula1('\'Sheet2\'!$B$1:$B$2');
        }


      
       /* $objValidation = $objPHPExcel->getActiveSheet(0)->getCell('Q2')->getDataValidation();
        $objValidation->setType( DataValidation::TYPE_LIST );
        $objValidation->setErrorStyle( DataValidation::STYLE_INFORMATION );
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowErrorMessage(true);
        $objValidation->setShowDropDown(true);
         $objValidation->getShowDropDown(true);
        $objValidation->setErrorTitle('Input error');
        $objValidation->setError('Value is not in list.');
        $objValidation->setPromptTitle('Pick from list');
        $objValidation->setPrompt('Please pick a value from the drop-down list.');
        $objValidation->setFormula1('\'Sheet2\'!$A$1:$A$2');*/

         header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="download_payment.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        $writer = new Xlsx($objPHPExcel);
        $writer->save('php://output');        

        /*header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="download_payment.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
       /* $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        
        $writer = new Xlsx($objPHPExcel);*/
        /*$writer->save('php://output'); */  
    }




    /* public function actionDownloadleadger_test()
    {
        $existingFilePath = 'uploads/payment_file/sample_payment.xlsx';
        $newFilePath = 'uploads/payment_file/new-orders.xlsx';
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($existingFilePath);
        $reader->setShouldFormatDates(true); 
        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile($newFilePath);


        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile($newFilePath);

         foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                  if ($sheetIndex !== 1) {
                $writer->addNewSheetAndMakeItCurrent();
            }

            foreach ($sheet->getRowIterator() as $row) {
                // ... and copy each row into the new spreadsheet
                $writer->addRow($row);
            }
        }
        $writer->addRow(['2015-12-25', 'Christmas gift', 29, 'USD']);

        $reader->close();
        $writer->close();
    }*/



    public function actionUploadpayment1()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $mycomponent = Yii::$app->mycomponent;
        $status = '';


        $payment_file = $request->post('payment_file');
        $upload_path = './uploads';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $upload_path = './uploads/payment_file/';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $fetched_file='';

        $uploadedFile = UploadedFile::getInstanceByName('payment_file');
        if(!empty($uploadedFile)){
            $src_filename = $_FILES['payment_file'];
            $fetched_file=$filename = $src_filename['name'];
            $filePath = $upload_path.'/'.$filename;
            $uploadedFile->saveAs($filePath);
            $original_file = 'uploads/payment_file/'.$filename;
        }

        $boolerror = 0;
        $payment_receipt = new PaymentReceipt();
        $objPHPExcel1 = new Spreadsheet(); 
        $objPHPExcel1->createSheet(0);
        $objPHPExcel1->setActiveSheetIndex(0)->setTitle("Sheet");
        $bank = $payment_receipt->getBanks();
        $row = 1;
        $objPHPExcel1->getActiveSheet()->setCellValue('A'.$row, 'Sr no');
        $objPHPExcel1->getActiveSheet()->setCellValue('B'.$row, 'Account name');
        $objPHPExcel1->getActiveSheet()->setCellValue('C'.$row, 'Account code');
        $objPHPExcel1->getActiveSheet()->setCellValue('D'.$row, 'Id');
        $objPHPExcel1->getActiveSheet()->setCellValue('E'.$row, 'Voucher id');
        $objPHPExcel1->getActiveSheet()->setCellValue('F'.$row, 'Particular');
        $objPHPExcel1->getActiveSheet()->setCellValue('G'.$row, 'Type');
        $objPHPExcel1->getActiveSheet()->setCellValue('H'.$row, 'Ref No');
        $objPHPExcel1->getActiveSheet()->setCellValue('I'.$row, 'GI Date');
        $objPHPExcel1->getActiveSheet()->setCellValue('J'.$row, 'Invoice Date');
        $objPHPExcel1->getActiveSheet()->setCellValue('K'.$row, 'Due Date');
        $objPHPExcel1->getActiveSheet()->setCellValue('L'.$row, 'Transaction');
        $objPHPExcel1->getActiveSheet()->setCellValue('M'.$row, 'Amount');
        $objPHPExcel1->getActiveSheet()->setCellValue('N'.$row, 'Paid Amount');
        $objPHPExcel1->getActiveSheet()->setCellValue('O'.$row, 'Balance Amount');
        $objPHPExcel1->getActiveSheet()->setCellValue('P'.$row, 'Amount To Pay');
        $objPHPExcel1->getActiveSheet()->setCellValue('Q'.$row, 'Bank name');
        $objPHPExcel1->getActiveSheet()->setCellValue('R'.$row, 'Ref no/cheque no');
        $objPHPExcel1->getActiveSheet()->setCellValue('S'.$row, 'Narration');
        $objPHPExcel1->getActiveSheet()->setCellValue('T'.$row, 'Type (Payment / Receipt)');
        $objPHPExcel1->getActiveSheet()->setCellValue('U'.$row, 'Error');


        $objPHPExcel = \moonland\phpexcel\Excel::import($original_file);
        $array = array();
        $prev_type = '';
        
        $r_row = 2;

        $ledger_id = array();
        $ledger_type = array();
        $invoice_no = array();
        $vendor_id = array();
        $amount_to_pay = array();
        $total_amount = array();
        $total_paid_amount = array();
        $p_type = '';
        if(count($objPHPExcel[0])>0)
        {   
            $objPHPExcel[0][0]['Bank name'];
            $bank = $payment_receipt->getBanks('' , $objPHPExcel[0][0]['Bank name']);
            $acc_name = $payment_receipt->getAccountDetails('' , $objPHPExcel[0][0]['Account name']);
            $id = '';
            $voucher_id = $objPHPExcel[0][0]['Voucher id'];
            $trans_type = $objPHPExcel[0][0]['Type (Payment / Receipt)'];
            $acc_id = $acc_name[0]['id'];
            $legal_name = $objPHPExcel[0][0]['Account name'];
            $acc_code = $objPHPExcel[0][0]['Account code'];
            $acc_code1 = $bank[0]['code'];
            $bank_id = $bank[0]['id'];
            $bank_name = $objPHPExcel[0][0]['Bank name'];
            $payment_type = 'Knock off';
            $narration = $objPHPExcel[0][0]['Narration'];
            $payment_date = date("Y-m-d");
            $remarks = '';
            $approver_id = $curusr;
            $payment_date=$payment_date;
            $sum_amount = 0;
            $paying_amount_total = 0;
            $paying_transaction = '';
            $ref_no = '';
            for($j=0; $j<count($objPHPExcel[0]); $j++) {
                  $error = '';
                  $acc_name = $objPHPExcel[0][$j]['Account name'];
                  $acc_no = $objPHPExcel[0][$j]['Account code'];
                  $ids = $objPHPExcel[0][$j]['Id'];
                  $explode = explode("," ,$ids);
                  $ledger_id[] = $explode[0];
                  $ledger_type[] = $explode[1];
                  $vendor_id[] = $explode[2];
                  $voucher_id = $objPHPExcel[0][$j]['Voucher id'];
                  $particular = $objPHPExcel[0][$j]['Particular'];
                  $ref_type = $objPHPExcel[0][$j]['Type'];
                  $invoice_no[]=$invoice_no1 = $objPHPExcel[0][$j]['Ref No'];
                  $gi_date = $objPHPExcel[0][$j]['GI Date'];
                  $invoice_date = $objPHPExcel[0][$j]['Invoice Date'];
                  $due_date = $objPHPExcel[0][$j]['Due Date'];
                  $transaction[]=$transaction1 = $objPHPExcel[0][$j]['Transaction'];
                  $total_amount[]=$amount = $objPHPExcel[0][$j]['Amount'];
                  $sum_amount = ($sum_amount+$objPHPExcel[0][$j]['Amount']);
                  $total_paid_amount[]=$paid_amount = $objPHPExcel[0][$j]['Paid Amount'];
                  $balance_amount = $objPHPExcel[0][$j]['Balance Amount'];
                  $amount_to_pay[]=$amount_to_pay1 = $objPHPExcel[0][$j]['Amount To Pay'];
                  $paying_amount_total = ($paying_amount_total+$objPHPExcel[0][$j]['Amount To Pay']);
                  $bank_name = $objPHPExcel[0][$j]['Bank name'];
                  $ref_no_check = $objPHPExcel[0][$j]['Ref no/cheque no'];
                  $narration = $objPHPExcel[0][$j]['Narration'];
                  $payment_receipt = $objPHPExcel[0][$j]['Type (Payment / Receipt)'];
                
                  /*if($payment_receipt=='' || $acc_name=='' || $bank_name=='')
                  {
                        $boolerror=1;
                        if($error!='')
                            $error.=' , ';
                        $error .= 'Please Enter Required Detail';
                  }*/

                  if($transaction1=='Debit' && $amount_to_pay1>0)
                  {
                        $boolerror=1;
                        if($error!='')
                            $error.=' , ';
                        $error .= ' Debit Amount Should Be Negative';
                  }

                  if($payment_receipt!="")
                  {
                       /*if($prev_type!='' && $prev_type!=$payment_receipt)
                          {
                                $boolerror=1;
                                if($error!='')
                                    $error.=' , ';
                                $error .= 'Type (Payment / Receipt) Should Be Same In All column';
                          }
                          else
                          {
                            $p_type = $payment_receipt;
                          }*/
                  }

               

                    $prev_type = $payment_receipt;
                    $objPHPExcel1->getActiveSheet()->setCellValue('A'.$r_row,($j+1));
                    $objPHPExcel1->getActiveSheet()->setCellValue('B'.$r_row,$acc_name);
                    $objPHPExcel1->getActiveSheet()->setCellValue('C'.$r_row,$acc_no);
                    $objPHPExcel1->getActiveSheet()->setCellValue('D'.$r_row,$ids);
                    $objPHPExcel1->getActiveSheet()->setCellValue('E'.$r_row,$voucher_id);
                    $objPHPExcel1->getActiveSheet()->setCellValue('F'.$r_row,$particular);
                    $objPHPExcel1->getActiveSheet()->setCellValue('G'.$r_row,$ref_type);
                    $objPHPExcel1->getActiveSheet()->setCellValue('H'.$r_row,$invoice_no1);
                    $objPHPExcel1->getActiveSheet()->setCellValue('I'.$r_row,$gi_date);
                    $objPHPExcel1->getActiveSheet()->setCellValue('J'.$r_row,$invoice_date);
                    $objPHPExcel1->getActiveSheet()->setCellValue('K'.$r_row,$due_date);
                    $objPHPExcel1->getActiveSheet()->setCellValue('L'.$r_row,$transaction1);
                    $objPHPExcel1->getActiveSheet()->setCellValue('M'.$r_row,$amount);
                    $objPHPExcel1->getActiveSheet()->setCellValue('N'.$r_row,$paid_amount);
                    $objPHPExcel1->getActiveSheet()->setCellValue('O'.$r_row,$balance_amount);
                    $objPHPExcel1->getActiveSheet()->setCellValue('P'.$r_row,$amount_to_pay1);
                    $objPHPExcel1->getActiveSheet()->setCellValue('Q'.$r_row,$bank_name);
                    $objPHPExcel1->getActiveSheet()->setCellValue('R'.$r_row,$ref_no_check);
                    $objPHPExcel1->getActiveSheet()->setCellValue('S'.$r_row,$narration);
                    $objPHPExcel1->getActiveSheet()->setCellValue('T'.$r_row,$payment_receipt);
                    if($error!='')
                        $objPHPExcel1->getActiveSheet()->setCellValue('U'.$r_row,$error);
                    
                    $r_row = $r_row+1;
            }
            $bal_amount = ($sum_amount-$paying_amount_total);
            if($bal_amount<0)
            {
               $paying_transaction = 'Debit';
            } 
            else
            {
                $paying_transaction = 'Credit';
            }


            if(strtoupper(trim($paying_transaction))=='DEBIT'){
                // $amount = $paying_amount_total*-1;
                $amount = $paying_amount_total;
            } else {
                $amount = $paying_amount_total;
            }

            if($trans_type=='Payment')
            {
                if($amount<0)
                {
                    $error = 'Payable amount should be credit';
                    $objPHPExcel1->getActiveSheet()->setCellValue('U'.($r_row+1),$error);
                    $boolerror=1;
                }
            }

            if($trans_type=='Receipt')
            {
                if($amount>0)
                {
                    $error = 'Payable amount should be debit '.$amount;
                    $objPHPExcel1->getActiveSheet()->setCellValue('U'.($r_row+1),$error);
                    $boolerror=1;
                }
            }

            if($boolerror==0)
            {
                $status = 'Inserted';
                $transaction_id = "";

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
                }

                $array=[
                    'trans_type'=>$trans_type,
                    'voucher_id' => $voucher_id, 
                    'ledger_type' => 'Sub Entry', 
                    'account_id'=>$acc_id,
                    'account_name'=>$legal_name,
                    'account_code'=>$acc_code,
                    'account_code1'=>$acc_code1,
                    'bank_id'=>$bank_id,
                    'bank_name'=>$bank_name,
                    'payment_type'=>$payment_type,
                    'amount'=>((strtoupper(trim($paying_transaction))=='DEBIT')?$amount*-1:$amount),
                    'ref_no'=>$ref_no,
                    'narration'=>$narration,
                    'status'=>'approved',
                    'is_active'=>'1',
                    'updated_by'=>$curusr,
                    'updated_date'=>$now,
                    'payment_date'=>$payment_date,
                    'approver_comments'=>$remarks,
                    'approver_id'=>$approver_id,
                    'approved_by'=>$approver_id,
                    'approved_date'=>$now,
                    'company_id'=>$company_id
                ];

                  echo 'voucher_id'.$id;

                if (isset($id) && $id!=""){

                   
                    $count = Yii::$app->db->createCommand()
                            ->update("acc_payment_receipt", $array, "id = '".$id."'")
                            ->execute();

                    /*$this->setLog('PaymentReceipt', '', 'Save', '', 'Update Payment Receipt Details', 'acc_payment_receipt', $id);*/
                } else {
                    $array['created_by']=$curusr;
                    $array['created_date']=$now;
                    $count = Yii::$app->db->createCommand()
                                ->insert("acc_payment_receipt", $array)
                                ->execute();
                    $id = Yii::$app->db->getLastInsertID();

                    /*$this->setLog('PaymentReceipt', '', 'Save', '', 'Insert Payment Receipt Details', 'acc_payment_receipt', $id);*/
                }

                if (isset($ledger_id)){
                    for($i=0; $i<count($ledger_id); $i++){
                        if($amount_to_pay[$i]!="" && $amount_to_pay[$i]!="0" && $amount_to_pay[$i]!=null){
                            $type = $transaction[$i];
                            $amt = $mycomponent->format_number($amount_to_pay[$i],2);
                            $tot_amt = $mycomponent->format_number($total_amount[$i],2);
                            $tot_paid_amt = $mycomponent->format_number($total_paid_amount[$i],2);
                            if(strtoupper(trim($type))=='DEBIT'){
                                $amt = $amt * -1;
                                $tot_amt = $tot_amt * -1;
                                $tot_paid_amt = $tot_paid_amt * -1;
                            }
                            $tot_bal_amt = $tot_amt - $tot_paid_amt;

                            $led_id = explode(',', $ledger_id[$i]);

                            for($j=0; $j<count($led_id); $j++){
                                $led_id[$j] = trim($led_id[$j]);
                                $led_amt = $amt;

                                if($led_id[$j]!="" && $led_id[$j]!=null){
                                    $sql = "select A.*, B.paid_amount, B.pending_paid_amount, B.amount_to_pay from 
                                        (select * from acc_ledger_entries where id = '".$led_id[$j]."') A 
                                        left join 
                                        (select sub_ref_id, sum(case when (ref_id != '$id' and status = 'approved') then amount else 0 end) as paid_amount, 
                                            sum(case when (ref_id != '$id' and status = 'pending') then amount else 0 end) as pending_paid_amount, 
                                            sum(case when ref_id = '$id' then amount else 0 end) as amount_to_pay from acc_ledger_entries 
                                        where is_active = '1' and company_id = '$company_id' and sub_ref_id is not null and date(ref_date)>date('2018-04-01') and 
                                            ref_type = 'payment_receipt' and ledger_type = 'Sub Entry' and sub_ref_id = '".$led_id[$j]."' 
                                        group by sub_ref_id) B 
                                        on (A.id = B.sub_ref_id)";
                                    $command = Yii::$app->db->createCommand($sql);
                                    $reader = $command->query();
                                    $result = $reader->readAll();

                                    if(count($result)>0){
                                        $led_acc_name = $result[0]['ledger_name'];
                                        $led_amount = $result[0]['amount'];
                                        $led_per = 0;

                                        $paid_amount = 0;
                                        if(isset($result[0]['paid_amount'])){
                                            if($result[0]['paid_amount']!=''){
                                                $paid_amount = $result[0]['paid_amount'];
                                            }
                                        }
                                        $pending_paid_amount = 0;
                                        if(isset($result[0]['pending_paid_amount'])){
                                            if($result[0]['pending_paid_amount']!=''){
                                                $pending_paid_amount = $result[0]['pending_paid_amount'];
                                            }
                                        }
                                        $tot_paid_amount = $paid_amount + $pending_paid_amount;


                                        $led_amt = $led_amount - $tot_paid_amount;
                                        $led_amt = round($led_amt*$amt/$tot_bal_amt,4); 
                                        /*if($tot_bal_amt==0)
                                        {
                                           $led_amt = round($led_amt*$amt,4); 
                                        }
                                        else
                                        {
                                            $led_amt = round($led_amt*$amt/$tot_bal_amt,4); 
                                        }*/
                                    }

                                    $ledgerArray=[
                                                    'ref_id'=>$id,
                                                    'sub_ref_id'=>$led_id[$j],
                                                    'ref_type'=>'payment_receipt',
                                                    'entry_type'=>$ledger_type[$i],
                                                    'invoice_no'=>$invoice_no[$i],
                                                    'vendor_id'=>$vendor_id[$i],
                                                    'voucher_id' => $voucher_id, 
                                                    'ledger_type' => 'Sub Entry', 
                                                    'acc_id'=>$acc_id,
                                                    'ledger_name'=>$legal_name,
                                                    'ledger_code'=>$acc_code,
                                                    'type'=>$type,
                                                    'amount'=>$led_amt,
                                                    'narration'=>$narration,
                                                    'status'=>'approved',
                                                    'is_active'=>'1',
                                                    'updated_by'=>$curusr,
                                                    'updated_date'=>$now,
                                                    'approved_by'=>$approver_id,
                                                    'approved_date'=>$now,
                                                    'ref_date'=>$payment_date,
                                                    'approver_comments'=>$remarks,
                                                    'company_id'=>$company_id
                                                ];

                                    $count = Yii::$app->db->createCommand()
                                                ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and sub_ref_id = '".$led_id[$j]."' and ref_type = 'payment_receipt'")
                                                ->execute();

                                    if ($count==0){
                                        $ledgerArray['created_by']=$curusr;
                                        $ledgerArray['created_date']=$now;

                                        $count = Yii::$app->db->createCommand()
                                                    ->insert("acc_ledger_entries", $ledgerArray)
                                                    ->execute();
                                    }
                                }
                            }
                        } else {
                            if($ledger_id[$i]!="" && $ledger_id[$i]!=null){
                                $count = Yii::$app->db->createCommand()
                                        ->delete("acc_ledger_entries", "ref_id = '".$id."' and 
                                                    sub_ref_id in (".$ledger_id[$i].") and 
                                                    ref_type = 'payment_receipt'")
                                        ->execute();
                            }
                        }
                    }
                }

                $data = $this->getBanks($bank_id);

                if(count($data)>0){
                    $bank_legal_name = $data[0]['legal_name'];
                    $bank_acc_code = $data[0]['code'];
                } else {
                    $bank_legal_name = '';
                    $bank_acc_code = '';
                }

                if($amount>0){
                    $type = 'Credit';
                    $amount = $amount;
                } else {
                    $type = 'Debit';
                    $amount = $amount*-1;
                }

                $ledgerArray=[
                                'ref_id'=>$id,
                                'sub_ref_id'=>null,
                                'ref_type'=>'payment_receipt',
                                'entry_type'=>'Bank Entry',
                                'invoice_no'=>$ref_no,
                                'vendor_id'=>null,
                                'voucher_id' => $voucher_id, 
                                'ledger_type' => 'Main Entry', 
                                'acc_id'=>$bank_id,
                                'ledger_name'=>$bank_legal_name,
                                'ledger_code'=>$bank_acc_code,
                                'type'=>$type,
                                'amount'=>$amount,
                                'narration'=>$narration,
                                'status'=>'approved',
                                'is_active'=>'1',
                                'updated_by'=>$curusr,
                                'updated_date'=>$now,
                                'ref_date'=>$payment_date,
                                'payment_ref'=>$id,
                                'approved_by'=>$approver_id,
                                'approved_date'=>$now,
                                'approver_comments'=>$remarks,
                                'company_id'=>$company_id
                            ];

                $count = Yii::$app->db->createCommand()
                        ->update("acc_ledger_entries", $ledgerArray, "ref_id = '".$id."' and ref_type = 'payment_receipt' and entry_type = 'Bank Entry'")
                        ->execute();
                if ($count==0){
                    $ledgerArray['created_by']=$curusr;
                    $ledgerArray['created_date']=$now;

                    $count = Yii::$app->db->createCommand()
                                ->insert("acc_ledger_entries", $ledgerArray)
                                ->execute();
                } 
            }
            
        }

        $efilename = '';


        if($boolerror==1)
        {
            $status = 'Failed';
            $upload_path = './uploads';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }

            $upload_path = './uploads/payment_file/';
            if(!is_dir($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
            }

            $efilename='payment_receipt_'.time().'.xlsx';
            $file_name = $upload_path . '/' . $efilename;
            $writer = new Xlsx($objPHPExcel1);
            $writer->save($file_name);
            ob_clean();
            ob_flush();



            $filename='payment_receipt_'.time().'.xlsx';
            $file_name =  $filename;
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$file_name.'"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $writer = new Xlsx($objPHPExcel1);
            $writer->save('php://output');
        }

        $insert_array = array("uploaded_file"=>$fetched_file,
                              "date_of_upload"=>date('Y-m-d H:i:s'),
                              "error_file"=>$efilename,
                              "status"=>$status,
                              "uploaded_by"=>$curusr,
                              "bank_cash_ledger"=>$bank_name,
                              "final_amount"=>$paying_amount_total,
                              "payment_receipt"=>$payment_receipt,
                              "company_id"=>$company_id);

        Yii::$app->db->createCommand()->insert("acc_payment_upload", $insert_array)->execute();   
    }


    public function actionDownloadleadger1()
    {
        $request = Yii::$app->request;
        $mycomponent = Yii::$app->mycomponent;
        $id = $request->post('id');
        $acc_id = $request->post('acc_id');//10;
        $to_date = $request->post('to_date');
        if($to_date==''){
        } else {
           $to_date=$mycomponent->formatdate($to_date);
        }


        $payment_receipt = new PaymentReceipt();
        $data = $payment_receipt->getLedger($id, $acc_id,$to_date);
        $data1 = $payment_receipt->getDetails($acc_id, "");
        $mycomponent = Yii::$app->mycomponent;
        $tbody = "";

        $objPHPExcel = new Spreadsheet();  
        if(count($data)>0){
            $total_debit_amt = 0;
            $total_credit_amt = 0;
            $paying_debit_amt = 0;
            $paying_credit_amt = 0;
            $net_debit_amt = 0;
            $net_credit_amt = 0;
            $total_transaction = '';
            $total_amount_total = 0;
            $total_paid_transaction = '';
            $total_paid_amount_total = 0;
            $paying_transaction = '';
            $paying_amount_total = 0;
            $bal_transaction = '';
            $bal_amount_total = 0;

            
            /*$col_name[]=array();
            for($i=0; $i<=21; $i++) {
                $col_name[$i]=PHPExcel_Cell::stringFromColumnIndex($i);
            }*/
            $col=0;
            $row = 1;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, 'Sr no');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, 'Account name');
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, 'Account code');
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, 'Id');
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, 'Voucher id');
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, 'Particular');
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, 'Type');
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, 'Ref No');
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$row, 'GI Date');
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, 'Invoice Date');
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$row, 'Due Date');
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, 'Transaction');
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$row, 'Amount');
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$row, 'Paid Amount');
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$row, 'Balance Amount');
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$row, 'Amount To Pay');
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$row, 'Bank name');
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$row, 'Ref no/cheque no');
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$row, 'Narration');
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$row, 'Type (Payment / Receipt)');
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$row, 'Error)');

            $r_row = 2;
            for($i=0; $i<count($data); $i++){
                $ledger_code = '';
                $ledger_name = '';
                $transaction = '';

                $transaction = $data[$i]['type'];
                $amount = $data[$i]['amount'];
                $total_paid_amount = $data[$i]['total_paid_amount'];
                $amount_to_pay = $data[$i]['amount_to_pay'];
                $bal_amount = $data[$i]['bal_amount'];

                $bal_amount = ($amount-$total_paid_amount-$amount_to_pay);
                
                if(strtoupper(trim($transaction))=="DEBIT"){
                    $amount = $amount*-1;
                    $total_paid_amount = $total_paid_amount*-1;
                    $amount_to_pay = $amount_to_pay*-1;
                    $bal_amount = $bal_amount*-1;
                }

                $total_amount_total = $total_amount_total+$amount;
                $total_paid_amount_total = $total_paid_amount_total+$total_paid_amount;
                $paying_amount_total = $paying_amount_total+$amount_to_pay;
                $bal_amount_total = $bal_amount_total+$bal_amount;

                if(strtoupper(trim($transaction))=="DEBIT"){
                    $debit_amt = $amount;
                    $credit_amt = 0;
                } else {
                    $debit_amt = 0;
                    $credit_amt = $amount;
                }

                if(isset($data[$i]['cp_acc_id'])){
                    if($data[$i]['cp_acc_id']!=$acc_id){
                        $ledger_code = $data[$i]['cp_ledger_code'];
                        $ledger_name = $data[$i]['cp_ledger_name'];
                    }
                }
                if($ledger_code == ''){
                    $ledger_code = $data[$i]['ledger_code'];
                    // $ledger_name = $data[$i]['new_ledger_name'];
                    $ledger_name = $data[$i]['ref_type'];
                }

                $ids = $data[$i]['id'].' ,'.$data[$i]['ledger_type'].' ,'.$data[$i]['vendor_id'].' ,';

                $objPHPExcel->getActiveSheet()->setCellValue('A'.$r_row,($i+1));
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$r_row,$data[$i]['ledger_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$r_row,'AC001');
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$r_row,$ids);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$r_row,$data[$i]['voucher_id']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$r_row,$data[$i]['ref_type']);
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$r_row,$data[$i]['entry_type']);
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$r_row,$data[$i]['invoice_no']);
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$r_row,date("d-m-Y",strtotime($data[$i]['gi_date'])));
                $objPHPExcel->getActiveSheet()->setCellValue('J'.$r_row,$data[$i]['invoice_date']);
                $objPHPExcel->getActiveSheet()->setCellValue('K'.$r_row,$data[$i]['due_date']);
                $objPHPExcel->getActiveSheet()->setCellValue('L'.$r_row,$data[$i]['type']);
                $objPHPExcel->getActiveSheet()->setCellValue('M'.$r_row,$amount);
                $objPHPExcel->getActiveSheet()->setCellValue('N'.$r_row,$total_paid_amount);
                $objPHPExcel->getActiveSheet()->setCellValue('O'.$r_row,$bal_amount);

                $r_row = $r_row+1;
            }

            $objPHPExcel->createSheet(1);
            $objPHPExcel->setActiveSheetIndex(1)->setTitle("Sheet2");
            $bank = $payment_receipt->getBanks();
            $row_t =1;  
           
            for($i=0; $i <count($bank); $i++) { 
                 $objPHPExcel->getActiveSheet()->setCellValue('A'.$row_t,$bank[$i]['legal_name']);

                 $row_t = $row_t+1;
            }

            for($j=2;$j<=100;$j++)
            {
                $objValidation = $objPHPExcel->getActiveSheet()->getCell('Q'.$j)->getDataValidation();
                $this->common_excel($objValidation);
                $objValidation->setFormula1('\'Sheet2\'!$A$1:$A$'.($row_t-1));
            }

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="download_payment.xlsx"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header ('Cache-Control: cache, must-revalidate');
            header ('Pragma: public');

            $writer = new Xlsx($objPHPExcel);
            $writer->save('php://output');   
        }
        else
        {
            echo "<script>alert('Result Not Found');</script>";
        }
    }
 ?>