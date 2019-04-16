<?php

namespace app\controllers;

use Yii;
use app\models\GrnEntries;
use app\models\GrnEntriesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use mPDF;
use yii\web\UploadedFile;
use phpoffice\phpexcel\Classes\PHPExcel as PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\PHPExcel_IOFactory as PHPExcel_IOFactory;
use phpoffice\phpexcel\Classes\PHPExcel\Cell\PHPExcel_Cell_DataValidation as PHPExcel_Cell_DataValidation;
use phpoffice\phpexcel\Classes\PHPExcel\Style\PHPExcel_Style_Protection as PHPExcel_Worksheet_Protection;

/**
 * GrnEntriesController implements the CRUD actions for GrnEntries model.
 */
class UploadscrapingController extends Controller
{
	public function actionIndex() {
		return $this->render('scraping_upload');
	}

	public function actionSaveupload()
	{
		
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $company_id = $session['company_id'];
        $curusr = $session['session_id'];
        $now = date('Y-m-d H:i:s');
        $mycomponent = Yii::$app->mycomponent;
        $status = '';

        echo $payment_file = $request->post('scraping_file');
        $upload_path = './uploads';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $upload_path = './uploads/scraping_file/';
        if(!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $fetched_file='';
        $uploadedFile = UploadedFile::getInstanceByName('scraping_file');
        if(!empty($uploadedFile)){
            $src_filename = $_FILES['scraping_file'];
            $fetched_file=$filename = $src_filename['name'];
            $filePath = $upload_path.'/'.$filename;
            $uploadedFile->saveAs($filePath);
            $original_file = 'uploads/scraping_file/'.$filename;
        }

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel = \PHPExcel_IOFactory::load($original_file);
        $objPHPExcel->setActiveSheetIndex(0);
       
        $array = array();
        $prev_type = '';
        
        $r_row = 2;
        $boolerror=0;
        $bank_name_tem = '';    
        $highestrow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();


        $batch_array = [];
        
        if($highestrow>0)
        {  
        	for($k=2;$k<=10;$k++){
                  $error = '';
                  $temp_flag = 0;

                  $product_code = $objPHPExcel->getActiveSheet()->getCell('A'.$k)->getValue();
                  $unit_price = $objPHPExcel->getActiveSheet()->getCell('B'.$k)->getValue();
                  $vendor_code = $objPHPExcel->getActiveSheet()->getCell('C'.$k)->getValue();
                  $vendor_skucode = $objPHPExcel->getActiveSheet()->getCell('D'.$k)->getValue();
                  $vendor_inventory = $objPHPExcel->getActiveSheet()->getCell('E'.$k)->getValue();
                  $mrp = $objPHPExcel->getActiveSheet()->getCell('F'.$k)->getValue();
                  $lead_time = $objPHPExcel->getActiveSheet()->getCell('G'.$k)->getValue();
                  $priority = $objPHPExcel->getActiveSheet()->getCell('H'.$k)->getValue();
                  $enabled = $objPHPExcel->getActiveSheet()->getCell('I'.$k)->getValue();
                  $color = $objPHPExcel->getActiveSheet()->getCell('J'.$k)->getValue();
                  $brand = $objPHPExcel->getActiveSheet()->getCell('K'.$k)->getValue();
                  $size = $objPHPExcel->getActiveSheet()->getCell('L'.$k)->getValue();
                  $updated = $objPHPExcel->getActiveSheet()->getCell('M'.$k)->getValue();
                  $hsn_code = $objPHPExcel->getActiveSheet()->getCell('N'.$k)->getValue();
                  $vendor_style_code = $objPHPExcel->getActiveSheet()->getCell('O'.$k)->getValue();
                  $myntra_team_lead = $objPHPExcel->getActiveSheet()->getCell('P'.$k)->getValue();
                  $myntra_unit_price = $objPHPExcel->getActiveSheet()->getCell('Q'.$k)->getValue();

                  $sql = "select * from acc_scraping_upload Where company_id='$company_id' and product_code='$product_code' ";

        		  $command = Yii::$app->db->createCommand($sql);
        		  $reader = $command->query();
        		  $result = $reader->readAll();

        		  if(count($result)>0)
        		  {
	        		  	$array=[
	                  			'product_code'=>$product_code,
	                            'unit_price' => $unit_price, 
	                            'vendor_code'=>$vendor_code,
	                            'vendor_skucode'=>$vendor_skucode,
	                            'vendor_inventory'=>$vendor_inventory,
	                            'mrp'=>$mrp,
	                            'lead_time'=>$lead_time,
	                            'priority'=>$priority,
	                            'enabled'=>$enabled,
	                            'color'=>$color,
	                            'brand'=>$brand,
	                            'size'=>$size,
	                            'updated_on'=>$updated,
	                            'hsn_code'=>$hsn_code,
	                            'vendor_style_code'=>$vendor_style_code,
	                            'myntra_team_lead'=>$myntra_team_lead,
	                            'myntra_unit_price'=>$myntra_unit_price,
	                            'updated_by'=>$curusr,
	                            'updated_date'=>$now,
	                            'company_id'=>$company_id
	                            ];

	        		  	Yii::$app->db->createCommand()->update("acc_scraping_upload", $array, "product_code = '".$product_code."'")->execute();;
        		  }
        		  else
        		  {
        		  		$array=[
	                  			'product_code'=>$product_code,
	                            'unit_price' => $unit_price, 
	                            'vendor_code'=>$vendor_code,
	                            'vendor_skucode'=>$vendor_skucode,
	                            'vendor_inventory'=>$vendor_inventory,
	                            'mrp'=>$mrp,
	                            'lead_time'=>$lead_time,
	                            'priority'=>$priority,
	                            'enabled'=>$enabled,
	                            'color'=>$color,
	                            'brand'=>$brand,
	                            'size'=>$size,
	                            'updated_on'=>$updated,
	                            'hsn_code'=>$hsn_code,
	                            'vendor_style_code'=>$vendor_style_code,
	                            'myntra_team_lead'=>$myntra_team_lead,
	                            'myntra_unit_price'=>$myntra_unit_price,
	                            'added_on'=>$now,
	                            'added_by'=>$curusr,
	                            'updated_by'=>$curusr,
	                            'updated_date'=>$now,
	                            'company_id'=>$company_id
	                            ];

	                  	Yii::$app->db->createCommand()->insert("acc_scraping_upload", $array)->execute();
        		  }
            }

			   /*Yii::$app->db->createCommand()->Insert("acc_scraping_upload", $batch_array)->execute();*/

         echo 'success';
        }
	}

  public function actionSales_api()
  {
      $soapUrl = "https://primarc.unicommerce.com/services/soap/uniware19.wsdl"; // asmx URL of WSDL
      $soapUser = "primarcpecan"; // username
      $soapPassword = "f4259e81-6d4d-44f9-9e76-65f23b52cebe"; // password
      $utc_current_time = gmdate("Y-m-d\TH:i:s\Z");
      // xml post structure
      $xml_post_string = '<soapenv:Envelope xmlns:ser="http://uniware.unicommerce.com/services/" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
      <soapenv:Header>
      <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
      <wsse:UsernameToken wsu:Id="UsernameToken-ECAAE6FCC192B2B2BF15325988355921">
      <wsse:Username>primarcpecan</wsse:Username>
      <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">f4259e81-6d4d-44f9-9e76-65f23b52cebe</wsse:Password>
      <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">j2g/cXEXbNIYVhkc7vjaEw==</wsse:Nonce>
      <wsu:Created>2019-01-15T09:53:55.582Z</wsu:Created>
      </wsse:UsernameToken>
      </wsse:Security>
      </soapenv:Header>
      <soapenv:Body>
      <ser:SearchSaleOrderRequest>

      <ser:SearchOptions>
      <ser:DisplayStart>1</ser:DisplayStart>
      </ser:SearchOptions>
      <!--Optional:-->
      <ser:UpdatedSinceInMinutes>1440</ser:UpdatedSinceInMinutes>
      </ser:SearchSaleOrderRequest>
      </soapenv:Body>
      </soapenv:Envelope>'; // data from the form, e.g. some ID number
      /*
      Accept-Encoding: gzip,deflate
      Content-Type: text/xml;charset=UTF-8
      SOAPAction: ""
      Content-Length: 1373
      Host: primarcpecan.unicommerce.com:443
      Connection: Keep-Alive
      User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
      */
      $headers = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "SOAPAction: ", 
      "Content-length: ".strlen($xml_post_string),
      ); //SOAPAction: your op URL
      $url = $soapUrl;
      // PHP cURL for https connection with auth
      /*$this->insert_log('Curl Is Initialize For SearchSaleOrderRequest');*/

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      // converting
      try {
          $response = curl_exec($ch); // <-- errors here 

      }
      catch (Exception $e) {
          $message = $e->getMessage();
          /*throw new Exception("Error with cURL request"); */
          $this->insert_log($message);
          /*die();*/
      }
      curl_close($ch);
      // converting
      $response1 = str_replace("<soap:Body>","",$response);
      $response2 = str_replace("</soap:Body>","",$response1);
      /*print_r($response2);*/
      /*$sales_order = $xml->Body->SearchSaleOrderResponse->SaleOrders->SaleOrder;*/
   
      $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $response2);
      $xml = simplexml_load_string($clean_xml);
      $xml = json_decode( json_encode($xml) , 1);
      /*$sales_order = $xml->Body->SearchSaleOrderResponse->SaleOrders->SaleOrder;
      */ 
      /*echo "<pre>";
      print_r($xml);
      echo "</pre>";*/
      
     /* echo "<pre>";
      print_r($xml['Body']['SearchSaleOrderResponse']['SaleOrders']['SaleOrder']);
      echo "</pre>";*/

      /*echo 'SearchSaleOrderResponse'.count($xml['Body']['SearchSaleOrderResponse']['SaleOrders']['SaleOrder']);*/
     
      $DisplayOrderCode = array();
      $flag = 0;
      $fail_api = '';
      $fail_insert = '';
      $fail_address_insert = '';
      $fail_order_insert = '';

      try {

          $total_record = $xml['Body']['SearchSaleOrderResponse']['TotalRecords'];
          $this->insert_log('Successful - Count is '.$total_record);

          if(count($xml['Body']['SearchSaleOrderResponse']['SaleOrders'])>0)
          {
              /*$salesod = $xml['Body']['SearchSaleOrderResponse']['SaleOrders'];
              foreach ($salesod as $records) {
                  if($records['Status']=='COMPLETE')
                  {  
                      if(!in_array($records['Code'],$DisplayOrderCode))
                      {
                         $DisplayOrderCode[] = $records['Code']; 
                      }
                  }
              }*/

              if($total_record==1)
              {
                  $salesod = $xml['Body']['SearchSaleOrderResponse']['SaleOrders'];
                  foreach ($salesod as $records) {
                      if($records['Status']=='COMPLETE')
                      {  
                          if(!in_array($records['Code'],$DisplayOrderCode))
                          {
                             $DisplayOrderCode[] = $records['Code']; 
                          }
                      }
                  }
              }
              else
              {
                   $salesod = $xml['Body']['SearchSaleOrderResponse']['SaleOrders']['SaleOrder'];

                  for ($i=0; $i <count($salesod) ; $i++) {

                      /*echo 'Status'.$salesod[$i]['Status'];*/

                      if($salesod[$i]['Status']=='COMPLETE')
                      {  
                          if(!in_array($salesod[$i]['Code'],$DisplayOrderCode))
                          {
                             /*echo "<br>".$salesod[$i]['Code']; */   
                             $DisplayOrderCode[] = $salesod[$i]['Code']; 
                          }
                      }
                  } 
              }
            

              /*echo "<pre>";
              print_r($DisplayOrderCode);
              echo "</pre>";*/
              /*for ($i=0; $i <$total_record ; $i++) {

                  echo 'Status'.$salesod[$i]['Status'];

                  if($salesod[$i]['Status']=='COMPLETE')
                  {  
                      if(in_array($salesod[$i]['Code'],$DisplayOrderCode))
                      {
                         echo "<br>".$salesod[$i]['Code'];
                         $DisplayOrderCode[] = $salesod[$i]['Code']; 
                      }
                  }
              }*/


              for ($i=0; $i <count($DisplayOrderCode) ; $i++) { 
                      $code =$DisplayOrderCode[$i];
                      $this->get_order_item($code,$fail_api,$fail_insert,$fail_address_insert,$fail_order_insert);
              }
              /*$code = $records['Code'];
              $this->get_order_item($code);*/
              if($fail_api!='' || $fail_insert!='' || $fail_address_insert!='' || $fail_order_insert!='')
              { 
                 $message= $fail_api.' '.$fail_insert.' '.$fail_address_insert.' '.$fail_order_insert;
                 $this->insert_log($message);
              }
          }
      }
      catch (Exception $e) {
          $this->insert_log('Fail - Error In Response');
      }

      
  }


  public function get_order_item($code,$fail_api,$fail_insert,$fail_address_insert,$fail_order_insert)
  {
      $soapUrl = "https://primarc.unicommerce.com:443/services/soap/?version=1.9"; 
      $soapUser = "primarcpecan"; // username
      $soapPassword = "f4259e81-6d4d-44f9-9e76-65f23b52cebe"; // password
      // asmx URL of WSDL
          /*$soapUser = "shradha"; // username
          $soapPassword = "d50854fd-0d94-41ca-be90-e00121da6430"; */// password
          // xml post structure
          $xml_post_string = '
              <soapenv:Envelope xmlns:ser="http://uniware.unicommerce.com/services/" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
               <soapenv:Header>
                  <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                     <wsse:UsernameToken wsu:Id="UsernameToken-D3269B9B83A8EF585D15494537018771">
                        <wsse:Username>primarcpecan</wsse:Username>
                        <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">f4259e81-6d4d-44f9-9e76-65f23b52cebe</wsse:Password>
                        <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">8zTHxTQIbYar9N//hN0vhg==</wsse:Nonce>
                        <wsu:Created>2019-02-06T11:48:21.760Z</wsu:Created>
                     </wsse:UsernameToken>
                  </wsse:Security>
               </soapenv:Header>
               <soapenv:Body>
                  <ser:GetSaleOrderRequest>
                     <ser:SaleOrder>
                        <ser:Code>'.$code.'</ser:Code>
                     </ser:SaleOrder>
                  </ser:GetSaleOrderRequest>
               </soapenv:Body>
            </soapenv:Envelope>'; // data from the form, e.g. some ID number
              /*
              Accept-Encoding: gzip,deflate
              Content-Type: text/xml;charset=UTF-8
              SOAPAction: ""
              Content-Length: 1373
              Host: primarcpecan.unicommerce.com:443
              Connection: Keep-Alive
              User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
              */
              $headers = array(
              "Content-type: text/xml;charset=\"utf-8\"",
              "Accept: text/xml",
              "Cache-Control: no-cache",
              "Pragma: no-cache",
              "SOAPAction: ", 
              "Content-length: ".strlen($xml_post_string),
              ); //SOAPAction: your op URL
              $url = $soapUrl;
              // PHP cURL for https connection with auth
              /*$this->insert_log('Curl Is Initialize For SaleOrderRequest');*/
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
              curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
              curl_setopt($ch, CURLOPT_TIMEOUT, 10);
              curl_setopt($ch, CURLOPT_POST, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
              curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
              // converting
              try {

                  $response = curl_exec($ch); // <-- errors here
              }
              catch (Exception $e) {
                  if($fail_api!='')
                      $fail_api .= ' , '.$code;
                  else
                      $fail_api .= 'Fail to get response of api for - '.$code;
              }

              curl_close($ch);
              // converting
              $response1 = str_replace("<soap:Body>","",$response);
              $response2 = str_replace("</soap:Body>","",$response1);
              
              $clean_xml1 = str_ireplace(['SOAP-ENV:', 'SOAP:'], '', $response2);
              $xml23 = simplexml_load_string($clean_xml1);
              $xml_data = json_decode( json_encode($xml23) , 1);

              if(count($xml_data['Body']['GetSaleOrderResponse']['SaleOrder'])>0)
              {
                  $successful = $xml_data['Body']['GetSaleOrderResponse']['Successful'];
                  $SaleOrder = $xml_data['Body']['GetSaleOrderResponse']['SaleOrder'];
                  $code = $SaleOrder['Code'];
                  $displayordercode = $SaleOrder['DisplayOrderCode'];
                  $channel = $SaleOrder['Channel'];
                  $displayorderdateTime = $SaleOrder['DisplayOrderDateTime'];
                  $notificationemail = json_encode($SaleOrder['NotificationEmail']);
                  $notificationmobile = $SaleOrder['NotificationMobile'];
                  $createdon = date("Y-m-d H:i:s",strtotime($SaleOrder['CreatedOn']));
                  $updatedon = date("Y-m-d H:i:s",strtotime($SaleOrder['UpdatedOn']));
                  /*$shippin_package = $SaleOrder['ShippingPackages'];*/
                  /*echo "<pre>";
                  print_r($shippin_package);
                  echo "</pre>";

                  echo 'ShipmentCode'.$shippin_package['ShipmentCode'];
                  die();*/

                  /*echo "<br>";*/
                  $order_items_table = array();
                  date_default_timezone_set('Asia/Kolkata');

                  $uniware_array = array('successful'=>$successful,
                                         'code'=>$code,
                                         'displayordercode'=>$displayordercode,
                                         'channel'=>$channel,
                                         'displayorderdateTime'=>$displayorderdateTime,
                                         'notificationemail'=>$notificationemail,
                                         'createdon'=>$createdon,
                                         'updatedon'=>$updatedon,
                                         'notificationmobile'=>$notificationmobile,
                                         'added_on'=>date('Y-m-d h:i:s')
                                        );
                  /*print_r($uniware_array);*/
                  try {
                      Yii::$app->db->createCommand()
                                ->insert("acc_uniware_master", $uniware_array)
                                ->execute();
                    $uniware_insert_id = Yii::$app->db->getLastInsertID();
                  } catch (Exception $e) {
                      if($fail_insert!='')
                         $fail_insert.= ' , '.$code;
                      else
                         $fail_insert.= 'Fail to Insert response of api for  - '.$code;
                  }


                  if(strpos(json_encode($this->array_keys_multi($SaleOrder['ShippingPackages']['ShippingPackage'])), '0')=='')
                    {
                     

                      $shippin_package = $SaleOrder['ShippingPackages']['ShippingPackage'];

                      $shipmentcode = $SaleOrder['ShippingPackages']['ShippingPackage']['ShipmentCode'];
                      $statuscode = $SaleOrder['ShippingPackages']['ShippingPackage']['StatusCode'];
                      $shippingpackagetype = $SaleOrder['ShippingPackages']['ShippingPackage']['ShippingPackageType'];
                      $invoicecode = $SaleOrder['ShippingPackages']['ShippingPackage']['InvoiceCode'];
                      $shippingprovider = $SaleOrder['ShippingPackages']['ShippingPackage']['ShippingProvider'];
                      $trackingnumber = $SaleOrder['ShippingPackages']['ShippingPackage']['TrackingNumber'];
                      $dispatchedon = $SaleOrder['ShippingPackages']['ShippingPackage']['DispatchedOn'];
                      $shipping_created_on = date("Y-m-d H:i:s",strtotime($shippin_package['CreatedOn']));
                      $shipp_array = array('acc_uniware_id'=>$uniware_insert_id,
                                             'shipment_code'=>$shipmentcode,
                                             'status_code'=>$statuscode,
                                             'shipping_packagetype'=>$shippingpackagetype,
                                             'invoice_code'=>$invoicecode,
                                             'shipping_provider'=>$shippingpackagetype,
                                             'tracking_number'=>$trackingnumber,
                                             'dispatched_on'=>$dispatchedon,
                                             'created_on'=>$shipping_created_on
                                            );
                      /*print_r($uniware_array);*/
                      try {
                          Yii::$app->db->createCommand()
                                    ->insert("acc_uniwareshipping_package", $shipp_array)
                                    ->execute();
                      } catch (Exception $e) {
                          if($fail_insert!='')
                             $fail_insert.= ' , '.$code;
                          else
                             $fail_insert.= 'Fail to Insert Shipping Package of api for  - '.$code;
                      }
                  }
                  else
                  {
                      $shippin_package1 = $SaleOrder['ShippingPackages']['ShippingPackage'];
                      for ($i=0; $i <count($shippin_package1) ; $i++) { 
                          $shipmentcode = $shippin_package1[$i]['ShipmentCode'];
                          $statuscode = $shippin_package1[$i]['StatusCode'];
                          $shippingpackagetype = $shippin_package1[$i]['ShippingPackageType'];
                          $shippingprovider = $shippin_package1[$i]['ShippingProvider'];
                          $invoicecode = $shippin_package1[$i]['InvoiceCode'];
                          $trackingnumber = $shippin_package1[$i]['TrackingNumber'];
                          $dispatchedon = $shippin_package1[$i]['DispatchedOn'];
                          $shipping_created_on = date("Y-m-d H:i:s",strtotime($shippin_package1[$i]['CreatedOn']));


                          $shipp_array = array('acc_uniware_id'=>$uniware_insert_id,
                                               'shipment_code'=>$shipmentcode,
                                               'status_code'=>$statuscode,
                                               'shipping_packagetype'=>$shippingpackagetype,
                                               'invoice_code'=>$invoicecode,
                                               'shipping_provider'=>$shippingpackagetype,
                                               'tracking_number'=>$trackingnumber,
                                               'dispatched_on'=>$dispatchedon,
                                               'created_on'=>$shipping_created_on
                                              );
                          /*print_r($uniware_array);*/
                          try {
                              Yii::$app->db->createCommand()
                                        ->insert("acc_uniwareshipping_package", $shipp_array)
                                        ->execute();
                          } catch (Exception $e) {
                              if($fail_insert!='')
                                 $fail_insert.= ' , '.$code;
                              else
                                 $fail_insert.= 'Fail to Insert Shipping Package of api for  - '.$code;
                          }
                      }

                  }

                  if(strpos(json_encode($this->array_keys_multi($SaleOrder['Addresses']['Address'])), '0')=='')
                  {
                      $Addresses = $SaleOrder['Addresses']['Address'];
                      $attributes_id = (isset($Addresses['@attributes']['id'])?isset($Addresses['@attributes']['id']):'');
                      $add_name = $Addresses['Name'];
                      $addressLine1 = $Addresses['AddressLine1'];
                      $addressLine2 = (isset($Addresses['AddressLine2'])?$Addresses['AddressLine2']:'');
                      $add_city = $Addresses['City'];
                      $add_state = $Addresses['State'];
                      $add_country = $Addresses['Country'];
                      $add_pincode = $Addresses['Pincode'];
                      $add_phone = $Addresses['Phone'];

                      $address = array(
                                  'attributes'=>$attributes_id,
                                  'name'=>$add_name,
                                  'addressLine1'=>$addressLine1,
                                  'addressLine2'=>$addressLine2,
                                  'city'=>$add_city,
                                  'state'=>$add_state,
                                  'country'=>$add_country,
                                  'pincode'=>$add_pincode,
                                  'phone'=>$add_phone,
                                  );
                      $address['type']='Address';
                      $address['acc_uniware_id']=$uniware_insert_id;

                      try {
                         Yii::$app->db->createCommand()
                                  ->insert("acc_uniware_address", $address)
                                  ->execute();
                      } catch (Exception $e) {
                          if($fail_address_insert!='')
                              $fail_address_insert.= ' , '.$code;
                          else
                              $fail_address_insert.= 'Fail to Insert Address of api for  - '.$code;
                      }

                  }
                  else
                  {
                    $Addresses1 = $SaleOrder['Addresses']['Address'];
                    $attributes_id = (isset($Addresses1['@attributes']['id'])?isset($Addresses1['@attributes']['id']):'');

                    for ($i=0; $i <count($Addresses1) ; $i++) {
                        $add_name = $Addresses1[$i]['Name'];
                        $addressLine1 = $Addresses1[$i]['AddressLine1'];
                        $addressLine2 =  (isset($Addresses1['AddressLine2'])?$Addresses1['AddressLine2']:'');
                        $add_city = $Addresses1[$i]['City'];
                        $add_state = $Addresses1[$i]['State'];
                        $add_country = $Addresses1[$i]['Country'];
                        $add_pincode = $Addresses1[$i]['Pincode'];
                        $add_phone = $Addresses1[$i]['Phone'];

                        $address = array(
                                        'attributes'=>$attributes_id,
                                        'name'=>$add_name,
                                        'addressLine1'=>$addressLine1,
                                        'addressLine2'=>$addressLine2,
                                        'city'=>$add_city,
                                        'state'=>$add_state,
                                        'country'=>$add_country,
                                        'pincode'=>$add_pincode,
                                        'phone'=>$add_phone,
                                        );
                        $address['type']='Address';
                        $address['acc_uniware_id']=$uniware_insert_id;

                        try {
                           Yii::$app->db->createCommand()
                                    ->insert("acc_uniware_address", $address)
                                    ->execute();
                        } catch (Exception $e) {
                            if($fail_address_insert!='')
                                $fail_address_insert.= ' , '.$code;
                            else
                                $fail_address_insert.= 'Fail to Insert Address of api for  - '.$code;
                        }
                      }
                  }

                  $Billing_addresses = $SaleOrder['BillingAddress'];
                  $billing_att_id = $Billing_addresses['@attributes']['ref'];

                  if($billing_att_id==$attributes_id)
                  {
                      $address['type']='Billing Addresses';
                      $address['acc_uniware_id']=$uniware_insert_id;
                      Yii::$app->db->createCommand()
                                  ->insert("acc_uniware_address", $address)
                                  ->execute();
                  }
                  else
                  {
                      $address = array(
                                  'attributes'=>$attributes_id,
                                  'type'=>'Billing Addresses'
                                  );
                      $address['acc_uniware_id']=$uniware_insert_id;
                      Yii::$app->db->createCommand()
                                  ->insert("acc_uniware_address", $address)
                                  ->execute();
                  }

                  if(strpos(json_encode($this->array_keys_multi($SaleOrder['SaleOrderItems']['SaleOrderItem'])), '0')=='')
                  {
                      $SaleOrderItem = $SaleOrder['SaleOrderItems']['SaleOrderItem'];
                      $code = $SaleOrderItem['Code'];
                      $statuscode = $SaleOrderItem['StatusCode'];
                      $itemsku = $SaleOrderItem['ItemSKU'];
                      $shippingmethodcode = $SaleOrderItem['ShippingMethodCode'];
                      $total_price = $SaleOrderItem['TotalPrice'];
                      $selling_price = $SaleOrderItem['SellingPrice'];
                      $discount = $SaleOrderItem['Discount'];
                      $shippingpackagecode = $SaleOrderItem['ShippingPackageCode'];
                      $facilitycode = $SaleOrderItem['FacilityCode'];
                      $shippingcharges = $SaleOrderItem['ShippingCharges'];
                      $shippingmethodcharges = $SaleOrderItem['ShippingMethodCharges'];;
                      $cashondeliverycharges = $SaleOrderItem['CashOnDeliveryCharges'];
                      $giftwrapcharges = $SaleOrderItem['GiftWrapCharges'];
                      /*$packetnumber = $SaleOrderItem['PacketNumber'];*/
                      $shippingaddress = $SaleOrderItem['ShippingAddress']['@attributes']['ref'];
                      $cancellable = $SaleOrderItem['Cancellable'];
                      $createdon = date("Y-m-d H:i:s",strtotime($SaleOrderItem['CreatedOn']));
                      $updatedon = date("Y-m-d H:i:s",strtotime($SaleOrderItem['UpdatedOn']));
                      $itemname1= implode(",", (array)$SaleOrderItem['ItemName']);


                      $onhold = $SaleOrderItem['OnHold'];
                      $order_items_table['code']=$code;
                      $order_items_table['statuscode']=$statuscode;
                      $order_items_table['itemsku']=$itemsku;
                      $order_items_table['itemname']=$itemname1;
                      $order_items_table['shippingmethodcode']=$shippingmethodcode;
                      $order_items_table['total_price']=$total_price;
                      $order_items_table['selling_price']=$selling_price;
                      $order_items_table['discount']=$discount;
                      $order_items_table['shippingpackagecode']=$shippingpackagecode;
                      $order_items_table['facilitycode']=$facilitycode;
                      $order_items_table['shippingcharges']=$shippingcharges;
                      $order_items_table['shippingmethodcharges']=$shippingmethodcharges;
                      $order_items_table['cashondeliverycharges']=$cashondeliverycharges;
                      $order_items_table['giftwrapcharges']=$giftwrapcharges;
                      /*$order_items_table['packetnumber']=$packetnumber;*/
                      $order_items_table['shippingaddress']=$shippingaddress;
                      $order_items_table['cancellable']=$cancellable;
                      $order_items_table['onhold']=$onhold;
                      $order_items_table['createdon']=$createdon;
                      $order_items_table['updatedon']=$updatedon;
                      $order_items_table['acc_uniware_id']=$uniware_insert_id;

                      

                    try {
                       Yii::$app->db->createCommand()
                              ->insert("acc_uniware_order_items", $order_items_table)
                              ->execute();
                    } catch (Exception $e) {
                        if($fail_order_insert!='')
                            $fail_order_insert.= ' , '.$code;
                        else
                            $fail_order_insert.= 'Fail to Insert Item -'.($i+1).'  of api for  - '.$code;
                    }
                  }
                  else
                  {
                      $SaleOrderItem = $SaleOrder['SaleOrderItems']['SaleOrderItem'];
                      $order_items_table = array();
                      for ($i=0; $i <count($SaleOrderItem) ; $i++) { 
                              $code = $SaleOrderItem[$i]['Code'];
                              $statuscode = $SaleOrderItem[$i]['StatusCode'];
                              $itemsku = $SaleOrderItem[$i]['ItemSKU'];
                              $shippingmethodcode = $SaleOrderItem[$i]['ShippingMethodCode'];
                              $total_price = $SaleOrderItem[$i]['TotalPrice'];
                              $selling_price = $SaleOrderItem[$i]['SellingPrice'];
                              $discount = $SaleOrderItem[$i]['Discount'];
                              $shippingpackagecode = $SaleOrderItem[$i]['ShippingPackageCode'];
                              $facilitycode = $SaleOrderItem[$i]['FacilityCode'];
                              $shippingcharges = $SaleOrderItem[$i]['ShippingCharges'];
                              $shippingmethodcharges = $SaleOrderItem[$i]['ShippingMethodCharges'];;
                              $cashondeliverycharges = $SaleOrderItem[$i]['CashOnDeliveryCharges'];
                              $giftwrapcharges = $SaleOrderItem[$i]['GiftWrapCharges'];
                              /*$packetnumber = $SaleOrderItem[$i]['PacketNumber'];*/
                              $shippingaddress = $SaleOrderItem[$i]['ShippingAddress']['@attributes']['ref'];
                              $cancellable = $SaleOrderItem[$i]['Cancellable'];
                              $createdon = date("Y-m-d H:i:s",strtotime($SaleOrderItem[$i]['CreatedOn']));
                              $updatedon = date("Y-m-d H:i:s",strtotime($SaleOrderItem[$i]['UpdatedOn']));
                              //$itemname1='';
                              //$itemname1 = $SaleOrderItem[$i]['ItemName'];
                              //echo $SaleOrderItem[$i]['ItemName'] . "<br/>".$i."";
                              $itemname1= implode(",", (array)$SaleOrderItem[$i]['ItemName']);
                              //echo $itemname1;
                              //echo $shippingaddress;
                             
                              $order_items_table['code']=trim($code);
                              $order_items_table['statuscode']=trim($statuscode);
                              $order_items_table['itemsku']=trim($itemsku);
                              $order_items_table['itemname']=$itemname1;
                              $order_items_table['shippingmethodcode']=trim($shippingmethodcode);
                              $order_items_table['total_price']=trim($total_price);
                              $order_items_table['selling_price']=trim($selling_price);
                              $order_items_table['discount']=trim($discount);
                              $order_items_table['shippingpackagecode']=trim($shippingpackagecode);
                              $order_items_table['facilitycode']=trim($facilitycode);
                              $order_items_table['shippingcharges']=trim($shippingcharges);
                              $order_items_table['shippingmethodcharges']=trim($shippingmethodcharges);
                              $order_items_table['cashondeliverycharges']=trim($cashondeliverycharges);
                              $order_items_table['giftwrapcharges']=trim($giftwrapcharges);
                              /*$order_items_table['packetnumber']=$packetnumber;*/
                              $order_items_table['shippingaddress']=trim($shippingaddress);
                              $order_items_table['cancellable']=trim($cancellable);
                              $order_items_table['createdon']=$createdon;
                              $order_items_table['updatedon']=$updatedon;
                              $order_items_table['acc_uniware_id']=trim($uniware_insert_id);


                              try {
                                 Yii::$app->db->createCommand()
                                        ->insert("acc_uniware_order_items", $order_items_table)
                                        ->execute();
                              } catch (Exception $e) {
                                  if($fail_order_insert!='')
                                      $fail_order_insert.= ' , '.$code;
                                  else
                                      $fail_order_insert.= 'Fail to Insert Item -'.($i+1).'  of api for  - '.$code;
                              }

                      }
                  }

                  
              }
  }

  public function insert_log($message)
  {
    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
    $array = array('action'=>$message,
                   'log_activity_date'=>$date
                  );
    Yii::$app->db->createCommand()
                      ->insert("acc_uniware_log", $array)
                      ->execute();
  }

  public function actionGetscrapinglog()
  {
      $sql = "select * from acc_uniware_log Order By id desc";
      $command = Yii::$app->db->createCommand($sql);
      $reader = $command->query();
      $result =  $reader->readAll();
      $request = Yii::$app->request;
      $log_data = array();
      $mycomponent = Yii::$app->mycomponent;
      $start = $request->post('start');    

      for($i=0; $i<count($result); $i++) { 
            $row = array(
                        $start+1,
                        ''.date("d-m-Y H:i:s A",strtotime($result[$i]['log_activity_date'])).'',
                        ''.$result[$i]['action'].''
                        ) ;
           $log_data[] = $row;
           $start = $start+1;
        }
        $json_data = array(
                "draw"            => intval($request->post('draw')),   
                "recordsTotal"    => count($result),  
                "recordsFiltered" => count($result),
                "data"            => $log_data
                );

        echo json_encode($json_data);
  }

  public function actionRedirectscraping()
  {
    return $this->render('scraping_log');
  }

  function array_keys_multi(array $array)
    {
        $keys = array();
        foreach($array as $key => $value) {
            $keys[] = $key;
            if (is_array($array[$key]))
                $keys = array_merge($keys, $this->array_keys_multi($array[$key]));
        }
        return $keys;
    }
}

?>
