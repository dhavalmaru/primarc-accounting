<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\RolePermission */
/* @var $form yii\widgets\ActiveForm */
$session = Yii::$app->session;
$userPermission=  json_decode($session->get('userPermission'));
$json="";
$json1="";
$json2="";
$json3="";
$json4 ="";
?>
<style type="text/css">
    fieldset {
        padding: 0.35em 0.625em 0.75em;
        margin: 0px 2px;
        border: 1px solid #C0C0C0;
    }
    legend {
        display: block;
        width: auto;
        padding: 0px;
        margin-bottom: 0;
        font-size: 15px;
        line-height: inherit;
        color: #333;
        border: 0px solid #E5E5E5;
    }
</style>
<div class="role-permission-form">

<?php $form = ActiveForm::begin(); ?>
    <div class="form-group">
        <?php
        if($userPermission->isSystemAdmin)
        {
        ?>
        <div class="row">

            <div class="col-lg-6">  
<?= $form->field($model, 'company_id')->dropDownList($companyArray) ?>
            </div>
        </div>
        <?php }?>
        <div class="row">

            <div class="col-lg-6">  
<?= $form->field($model, 'role_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">

<?= $form->field($model, 'organization_id[]')->dropDownList($orgnzationArray, ['multiple' => true]) ?>
            </div>
           
        </div>
        <?php
        if(!$userPermission->isSystemAdmin)
        {
        ?>
        <div class="row">
            <div class="col-lg-12">
                <fieldset>
                    <legend>Product</legend>

                    <div class="row"><div class="col-lg-2"></div><div class="col-lg-9"><input type="checkbox" id="product-category-all" name="product-category-all[]" class="product"/>Select All</div></div> 
<?php
//  var_dump($productCategoryArray);die;
$productArray=array_keys($productCategoryArray );                   
$json = json_encode($productArray);

foreach ($productCategoryArray as $catkey => $cat) {
    ?>
                        <div class="row">

                            <div class="col-lg-2" style="text-align:left;padding-left:20px;font-size:15px;"><?php echo $cat ?></div>

                            <div class="col-lg-10">
                                <div class="row">  
                                    <div class="col-lg-2"><input type="checkbox" class="product product-category-all-<?php echo $catkey ?>" id="product-category-all-<?php echo $catkey ?>" name="product-main-category-id[]" value="<?php echo $catkey ?>" onclick="getcheckAllCategory('<?php echo $catkey ?>')"> Select All</div>
    <?php
    foreach ($permissionArray as $keyperm => $perm) {

        $value = "product-category-" . $keyperm . "-" . $catkey;
        ?>

                                        <div class="col-lg-2" style="max-width: 110px;">
                                            <input type="checkbox"  id="product-category-<?php echo $catkey ?>-<?php echo $keyperm; ?>" class="product-category-all  category-wise-all-<?php echo $catkey ?>" name="product-category[]" value="<?php echo $value ?>" <?php if (in_array($value, $permissions)) echo "checked"; ?>  onclick="checkcheckboxproductcategory('<?php echo $catkey; ?>','<?php echo $keyperm; ?>')"/> <?php echo $perm
        ?>               </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                                    <?php
                                }
                                ?>         

                </fieldset>
            </div>

        </div>

        <div class="row">
            <div class="col-lg-12">
                <fieldset>
                    <legend>Market Place Product Master</legend>

                    <div class="row">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-9">
                            <input type="checkbox" id="market-place-product-master-all" name="market-place-product-master-all[]" class="marketplace"/>Select All
                        </div>
                    </div> 
<?php
//  var_dump($productCategoryArray);die;

$marketArray=array();
foreach ($marketplaceArray as $key => $mrk) {
    $marketArray[]=$key;
    $json1 = json_encode($marketArray);
     //var_dump($json1);die;
    ?>
                        <div class="row">
                            <div class="col-lg-2" style="text-align:left;padding-left:20px;font-size:15px;"><?php echo $mrk ?></div>
                            <div class="col-lg-10">
                                <div class="row">  
                                    <div class="col-lg-2"><input type="checkbox"  class="marketplace market-place-product-master-all-<?php echo $key ?>" id="market-place-product-master-all-<?php echo $key ?>" name="marketplace_id[]" value="<?php echo $key ?>" onclick="getcheckAllMarketPlace('<?php echo $key ?>')"> Select All</div>
                        <?php
                        foreach ($permissionArray as $keyperm => $perm) {
                            $value = "market-place-product-master-" . $keyperm . "-" . $key;
                            ?>

                                        <div class="col-lg-2" style="max-width: 110px;">
                                            <input type="checkbox" id="marketplace-<?php echo $key ?>-<?php echo $keyperm; ?>" class="market-place-product-master-all  marketplace-wise-all-<?php echo $key ?>" name="market-place-product-master[]" value="<?php echo $value; ?>" <?php if (in_array($value, $permissions)) echo "checked"; ?>  onclick="checkcheckboxmarketplace('<?php echo $key; ?>','<?php echo $keyperm; ?>')"/> <?php echo $perm ?>
                                        </div>

                                    <?php } ?>
                                </div>
                            </div>
                        </div>

    <?php
}
?>         

                </fieldset>
            </div>
        </div>
         <div class="row">
        <?php
        
          $authArray=  explode(",", $model->auth_permission_id);
          $moduleArray=array();
          $i=0;
          foreach($authpermissionObj as  $authperm)
          {
              $code=$authperm['authorize_code'];
              $fieldname="RolePermission[".$code."]";
             
             
              ?>
                <?php  if(!in_array($authperm['module_name'], $moduleArray)){
                              array_push($moduleArray,$authperm['module_name']);
                              
                              if($i!=0)
                              {?>
                                    </div>
           
                    </fieldset>
                </div>
                                  <?php
                                  
                              }else{
                                  $i++;
                              }
                             ?>    
                <div class="col-lg-12">
                    <fieldset>
                      
                        <legend>
                            <?php echo $authperm['module_name'] ?>
                        </legend> 
                           <div class="row">
                          
                        <div class="col-lg-4" style="text-align:left;padding-left:20px;font-size:15px;">
               
                            <input id="rolepermission-<?php echo $authperm['authorize_code']?>" name="<?php echo $fieldname?>" value="1" type="checkbox" <?php if(in_array($authperm['id'],$authArray)) echo "checked"?>> <?php echo $authperm['name']?>
                        </div>
                        <?php }else{
                            
                           
                            ?>
                            
                                <div class="col-lg-4" style="text-align:left;padding-left:20px;font-size:15px;">
               
                            <input id="rolepermission-<?php echo $authperm['authorize_code']?>" name="<?php echo $fieldname?>" value="1" type="checkbox" <?php if(in_array($authperm['id'],$authArray)) echo "checked"?>> <?php echo $authperm['name']?>
                        </div>
                        <?php }?>
                         
         <?php }
        
        ?>
          </div>
           
                    </fieldset>
                </div>
         </div>
          
        
         <?php
                    $iArray=array_keys($resourceArray);
                    
                    $json2 = json_encode($iArray);
                   
                    foreach ($resourceArray as $key => $resource) {
                        ?>
            <div class="row">
                <div class="col-lg-12">
                    <fieldset>
                        <legend><?php echo $resource ?></legend>               


                        <div class="row">

                            <div class="col-lg-2" style="text-align:left;padding-left:20px;font-size:15px;">  
                                <input type="checkbox" name="<?php $key ?>[]" id="resource-all-<?php echo $key ?>" class="resource-<?php echo $key ?>" onclick="getCheckAllResource('<?php echo $key ?>')"/>Select All
                            </div>

                            <div class="col-lg-10">  
                                <div class="row">  
    <?php
    foreach ($permissionArray as $keyperm => $perm) {
        if(in_array($key,array("purchase-order","goods-inward","goods-outward","goods-receive-notification","prepare-go","sku-movements","warehouse-inventory")) && $keyperm=="deactive")
        {
            $perm="Enable";
            $keyperm="enable";
        }       
       $value = $key . "-" . $keyperm;
       
        ?>
                                    <?php 
                                      
                                      if($key=="company-master")
                                      {
                                          
                                           if($keyperm=="index" || $keyperm=="update")
                                           { ?>
                                             <div class="col-lg-2" style="max-width: 110px;">
                                            <input type="checkbox" id="resource-<?php echo $key ?>-<?php echo $keyperm ?>"name="<?php echo $key ?>[]" class="resource resource-all-<?php echo $key ?>"  value="<?php echo $value; ?>" <?php if (in_array($value, $permissions)) echo "checked"; ?> onclick="checkboxresource('<?php echo $key; ?>','<?php echo $keyperm; ?>');"/> <?php echo $perm ?>
                                        </div>   
                                         <?php   }
                                        
                                    }else if($key=="migration-utility")
                                      {
                                          
                                           if($keyperm=="index")
                                           { ?>
                                             <div class="col-lg-2" style="max-width: 110px;">
                                            <input type="checkbox" id="resource-<?php echo $key ?>-<?php echo $keyperm ?>"name="<?php echo $key ?>[]" class="resource resource-all-<?php echo $key ?>"  value="<?php echo $value; ?>" <?php if (in_array($value, $permissions)) echo "checked"; ?> onclick="checkboxresource('<?php echo $key; ?>','<?php echo $keyperm; ?>');"/> <?php echo "Authorize" ?>
                                        </div>   
                                         <?php   }
                                        
                                    } else if($key=="opening-cost")
                                      {
                                          
                                           if($keyperm=="index" || $keyperm=="create")
                                           { ?>
                                             <div class="col-lg-2" style="max-width: 110px;">
                                            <input type="checkbox" id="resource-<?php echo $key ?>-<?php echo $keyperm ?>"name="<?php echo $key ?>[]" class="resource resource-all-<?php echo $key ?>"  value="<?php echo $value; ?>" <?php if (in_array($value, $permissions)) echo "checked"; ?> onclick="checkboxresource('<?php echo $key; ?>','<?php echo $keyperm; ?>');"/> <?php echo $perm ?>
                                        </div>   
                                         <?php   }
                                        
                                    }else{?>
      
                                        <div class="col-lg-2" style="max-width: 110px;">
                                            <input type="checkbox" id="resource-<?php echo $key ?>-<?php echo $keyperm ?>"name="<?php echo $key ?>[]" class="resource resource-all-<?php echo $key ?>"  value="<?php echo $value; ?>" <?php if (in_array($value, $permissions)) echo "checked"; ?> onclick="checkboxresource('<?php echo $key; ?>','<?php echo $keyperm; ?>');"/> <?php echo $perm ?>
                                        </div>

                                    <?php }
                                    
                                    
                                    } ?>
                                </div>
                            </div>
                        </div>

                    </fieldset>
                </div>
            </div>
<?php } ?>
        
        
        <div class="row">
            <div class="col-lg-12">
                <fieldset>
                    <legend>Reports</legend>

                    <div class="row">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-8">
                            <input type="checkbox" id="reportpermission-all" name="reportpermission-all[]" class="reportpermission"/>Select All
                        </div>
                    </div> 
<?php

$reportpermission=array();

foreach ($reportpermissionMasterArray as $key => $code) {
$reportpermission[]=$key;
$json4 = json_encode($reportpermission);
//echo "<pre>";var_dump($json3);die;
    ?>
                        <div class="row">
                            <div class="col-lg-4" style="text-align:left;padding-left:20px;font-size:15px;"><?php echo $code ?></div>
                            <div class="col-lg-8">
                                <div class="row">  
                                    <div class="col-lg-2"><input type="checkbox" class="reportpermission reportpermission-all-<?php echo $key ?>" id="reportpermission-all-<?php echo $key ?>" name="reportpermission_id[]" value="<?php echo $key ?>" onclick="getcheckAllReportPermission('<?php echo $key ?>')"> Select All</div>
                        <?php
                        foreach ($permissionArray as $keyperm => $perm) {
                            
                            if(!in_array($keyperm,array("deactive","A1","A2","update")))
                            {
                            $value =  $key. "-" . $keyperm;
                            ?>

                                        <div class="col-lg-2" style="max-width: 110px;">
                                            <input type="checkbox" id="reportpermission-<?php echo $key ?>-<?php echo $keyperm ?>" class="reportpermission-all reportpermission-wise-all-<?php echo $key ?>"  name="reportpermission-master[]" value="<?php echo $value; ?>" <?php if (in_array($value, $permissions)) echo "checked"; ?> onclick="checkboxreportpermission('<?php echo $key; ?>','<?php echo $keyperm; ?>')"/> <?php echo $perm ?>
                                        </div>

                                    <?php }
                                    
                        }?>
                                </div>
                            </div>
                        </div>

    <?php
}
?>   
                </fieldset>
            </div>
        </div>
      
      
        <div class="row">
            <div class="col-lg-12">
                <fieldset>
                    <legend>Warehouse</legend>

                    <div class="row">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-9">
                            <input type="checkbox" id="warehouse-all" name="warehouse-all[]" class="warehouse"/>Select All
                        </div>
                    </div> 
<?php

$warehouse=array();

foreach ($warehouseArray as $key => $ware) {
$warehouse[]=$key;
$json3 = json_encode($warehouse);
//echo "<pre>";var_dump($json3);die;
    ?>
                        <div class="row">
                            <div class="col-lg-2" style="text-align:left;padding-left:20px;font-size:15px;"><?php echo $ware ?></div>
                            <div class="col-lg-8">
                                <div class="row">  
                                    <div class="col-lg-2"><input type="checkbox" class="warehouse warehouse-all-<?php echo $key ?>" id="warehouse-all-<?php echo $key ?>" name="warehouse_id[]" value="<?php echo $key ?>" onclick="getcheckAllWarehouse('<?php echo $key ?>')"> Select All</div>
                        <?php
                        foreach ($permissionArray as $keyperm => $perm) {
                            
                            if(!in_array($keyperm,array("deactive","A1","A2")))
                            {
                            $value = "warehouse-" . $keyperm . "-" . $key;
                            ?>

                                        <div class="col-lg-2" style="max-width: 110px;">
                                            <input type="checkbox" id="warehouse-<?php echo $key ?>-<?php echo $keyperm ?>" class="warehouse-all warehouse-wise-all-<?php echo $key ?>"  name="warehouse-master[]" value="<?php echo $value; ?>" <?php if (in_array($value, $permissions)) echo "checked"; ?> onclick="checkboxwarehouse('<?php echo $key; ?>','<?php echo $keyperm; ?>')"/> <?php echo $perm ?>
                                        </div>

                                    <?php }
                                    
                        }?>
                                </div>
                            </div>
                        </div>

    <?php
}
?>   
                </fieldset>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <fieldset>
                    <legend>Purchase Order Category</legend>

                    <div class="row"><div class="col-lg-2"></div><div class="col-lg-9"><input type="checkbox" id="purchase-order-category-all" name="purchase-order-category-all[]" class="purchase"/>Select All</div></div> 
<?php

foreach ($productCategoryArray as $catkey => $cat) {
    ?>
                        <div class="row">

                            <div class="col-lg-2" style="text-align:left;padding-left:20px;font-size:15px;"><?php echo $cat ?></div>

                            <div class="col-lg-10">
                                <div class="row">  
                                    <div class="col-lg-2"><input type="checkbox" class="purchase purchase-order-category-all-<?php echo $catkey ?>" id="purchase-order-category-all-<?php echo $catkey ?>" name="purchase-order-main-category-id[]" value="<?php echo $catkey ?>" onclick="getcheckAllCategoryPurchaseOrder('<?php echo $catkey ?>')"> Select All</div>
    <?php
    foreach ($permissionArray as $keyperm => $perm) {

        $value = "purchase-order-category-" . $keyperm . "-" . $catkey;
        ?>

                                        <div class="col-lg-2" style="max-width: 110px;">
                                            <input type="checkbox" id="purchase-order-<?php echo $catkey ?>-<?php echo $keyperm ?>" class="purchase-order-category-all  purchase-order-category-wise-all-<?php echo $catkey ?>" name="purchase-order-category[]" value="<?php echo $value ?>" <?php if (in_array($value, $permissions)) echo "checked"; ?>  onclick="checkcheckboxproductcategorypurchaseorder('<?php echo $catkey?>','<?php echo $keyperm?>')"/> <?php echo $perm
        ?>               </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                                    <?php
                                }
                                ?>         

                </fieldset>
            </div>

        </div>
        <?php } ?>
    </div>
    

        <div class="form-group">
<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    </div>
<?php ActiveForm::end(); ?>


</div>

<?php
        if(!$userPermission->isSystemAdmin)
        {
        ?>
 <?php
$this->registerJs('$("document").ready(function(){ 
        checkcheckboxproductcategory(); checkcheckboxmarketplace();
        checkboxresource();checkboxwarehouse();checkboxreportpermission();checkcheckboxproductcategorypurchaseorder();
       '
        . '$("#product-category-all").change(function(){
              $(".product").prop("checked", $(this).prop("checked"));
              $(".product-category-all").prop("checked", $(this).prop("checked"));
             
        });'
        
         . '$("#purchase-order-category-all").change(function(){
              $(".purchase").prop("checked", $(this).prop("checked"));
              $(".purchase-order-category-all").prop("checked", $(this).prop("checked"));
             
        });'
        
       
        .'$("#market-place-product-master-all").change(function(){
             $(".marketplace").prop("checked", $(this).prop("checked"));
            $(".market-place-product-master-all").prop("checked", $(this).prop("checked"));
            
         });'
         . '$("#warehouse-all").change(function(){
            $(".warehouse").prop("checked", $(this).prop("checked"));
            $(".warehouse-all").prop("checked", $(this).prop("checked"));
            });'
        
         . '$("#reportpermission-all").change(function(){
            $(".reportpermission").prop("checked", $(this).prop("checked"));
            $(".reportpermission-all").prop("checked", $(this).prop("checked"));
            });'
        
        . "$('#rolepermission-organization_id').val([" . $model->organization_id . "]);"
        . ' $("#rolepermission-organization_id").multipleSelect({ filter: true });'
        . '});'
        
        
        
        );
        }else{
            
            
$this->registerJs('$("document").ready(function(){ 
        '
        . "$('#rolepermission-organization_id').val([" . $model->organization_id . "]);"
        . ' $("#rolepermission-organization_id").multipleSelect({ filter: true });'
        . '});'
        
        
        
        );
        }
?>

<script>

    function getcheckAllCategory(category_id)
    {

        $("#product-category-all-" + category_id).change(function () {
            $(".category-wise-all-" + category_id).prop('checked', $(this).prop("checked"));
            checkcheckboxproductcategory();
        });
      
      
    }

function checkcheckboxproductcategory(catid,operation)
  { 
      if(operation==="create"  ||  operation==="update")
      {
        $("#product-category-"+ catid+"-index").prop("checked",true);  
      }
       var json = "<?php echo $json; ?>";
       if(json.length!=0) 
      {
       var obj = JSON.parse(json);
      
       obj.forEach(function(entry) 
     {
         
     
        var checkCount =$("input[class='product-category-all  category-wise-all-"+ entry+"']").length;
        
        var checkCount1 = $("input[class='product-category-all  category-wise-all-"+ entry+"']:checked").length;
       
        var checkboxesall2 = document.getElementsByClassName("product-category-all-"+ entry);
       if(checkCount===checkCount1)
        {
           
            checkboxesall2[0].checked=true;
            
        }
        else
        {
         
            checkboxesall2[0].checked=false;
           
        } 
     });
      }
    uncheckproduct(); 
  }
  function uncheckproduct()
  {
      var checkCount =$("input[name='product-main-category-id[]']").length;
      var checkCount1 = $("input[name='product-main-category-id[]']:checked").length;
       var checkboxesall=document.getElementsByClassName("product");
      if(checkCount===checkCount1)
        {
            checkboxesall[0].checked=true;
        }
        else
        {
            checkboxesall[0].checked=false;
        } 
  }
      
    function getcheckAllMarketPlace(market_id)
    {

        $("#market-place-product-master-all-" + market_id).change(function () {
            $(".marketplace-wise-all-" + market_id).prop('checked', $(this).prop("checked"));
        checkcheckboxmarketplace(); 
        });
        
    }
    
    function checkcheckboxmarketplace(marketid,operation)
   {
       if(operation==="create"  ||  operation==="update")
      {
        $("#marketplace-"+ marketid+"-index").prop("checked",true);  
      }
       
       var json = '<?php echo $json1; ?>';
      
      if(json.length!=0) 
      {
      var obj = JSON.parse(json);
       obj.forEach(function(entry) 
     {
          
       var checkCount =$("input[class='market-place-product-master-all  marketplace-wise-all-"+ entry+"']").length;
       var checkCount1 =$("input[class='market-place-product-master-all  marketplace-wise-all-"+ entry+"']:checked").length;
       
       var checkboxesall2 = document.getElementsByClassName("market-place-product-master-all-"+ entry);
       if(checkCount===checkCount1)
        {
          
            checkboxesall2[0].checked=true;
            
        }
        else
        {
           
            checkboxesall2[0].checked=false;
           
        } 
     }); 
 }
     uncheckmarketplace();
  
  } 
  function uncheckmarketplace()
  {
      var checkCount =$("input[name='marketplace_id[]']").length;
      var checkCount1 = $("input[name='marketplace_id[]']:checked").length;
      var checkboxesall=document.getElementsByClassName("marketplace");
      if(checkCount===checkCount1)
        {
            checkboxesall[0].checked=true;
        }
        else
        {
            checkboxesall[0].checked=false;
        } 
  }
    function getCheckAllResource(resource_id)
    {

         $("#resource-all-" + resource_id).change(function () {
            $(".resource-all-" + resource_id).prop('checked', $(this).prop("checked"));
          checkboxresource();
          });
       
    }
    function checkboxresource(resourceid,operation)
  { 
      if(operation==="create"  ||  operation==="update")
      {
        $("#resource-"+ resourceid+"-index").prop("checked",true);  
      }
       var json = '<?php echo $json2; ?>';
        if(json.length!=0) 
      {
       var obj = JSON.parse(json);
       
       obj.forEach(function(entry) 
     {
          
        var checkCount =$("input[class='resource resource-all-"+ entry+"']").length;
        
        var checkCount1 = $("input[class='resource resource-all-"+ entry+"']:checked").length;
        
        var checkboxesall2 = document.getElementsByClassName("resource-"+ entry);
       if(checkCount===checkCount1)
        {
            
            checkboxesall2[0].checked=true;
            
        }
        else
        {
           
            checkboxesall2[0].checked=false;
           
        } 
     }); 
      }
  }
function getcheckAllWarehouse(warehouse_id)
    {

        $("#warehouse-all-" + warehouse_id).change(function () {
            $(".warehouse-wise-all-" + warehouse_id).prop('checked', $(this).prop("checked"));
          checkboxwarehouse();
          });

        
    }
    
  function getcheckAllReportPermission(warehouse_id)
    {

        $("#reportpermission-all-" + warehouse_id).change(function () {
            $(".reportpermission-wise-all-" + warehouse_id).prop('checked', $(this).prop("checked"));
          checkboxreportpermission();
          });

        
    }
     function checkboxreportpermission(warehouseid,operation)
  {
      if(operation==="create")
      {
        $("#"+ warehouseid+"-index").prop("checked",true);  
      }
       var json = '<?php echo $json4; ?>';
        if(json.length!=0) 
      {
       var obj = JSON.parse(json);
       
       obj.forEach(function(entry) 
     {
         
     
        var checkCount =$("input[class='reportpermission-all reportpermission-wise-all-"+ entry+"']").length;
        
        var checkCount1 = $("input[class='reportpermission-all reportpermission-wise-all-"+ entry+"']:checked").length;
       
        var checkboxesall2 = document.getElementsByClassName("reportpermission-all-"+ entry);
       if(checkCount===checkCount1)
        {
           
            checkboxesall2[0].checked=true;
            
        }
        else
        {
         
            checkboxesall2[0].checked=false;
           
        } 
     });
      }
    uncheckreportpermission(); 
  }
  
  function uncheckreportpermission()
  {
      var checkCount =$("input[name='reportpermission-master[]']").length;
      var checkCount1 = $("input[name='reportpermission-master[]']:checked").length;
       var checkboxesall=document.getElementsByClassName("reportpermission");
      if(checkCount===checkCount1)
        {
            checkboxesall[0].checked=true;
        }
        else
        {
            checkboxesall[0].checked=false;
        } 
  }
    
  function checkboxwarehouse(warehouseid,operation)
  {
      if(operation==="create"  ||  operation==="update")
      {
        $("#warehouse-"+ warehouseid+"-index").prop("checked",true);  
      }
       var json = '<?php echo $json3; ?>';
        if(json.length!=0) 
      {
       var obj = JSON.parse(json);
       
       obj.forEach(function(entry) 
     {
         
     
        var checkCount =$("input[class='warehouse-all warehouse-wise-all-"+ entry+"']").length;
        
        var checkCount1 = $("input[class='warehouse-all warehouse-wise-all-"+ entry+"']:checked").length;
       
        var checkboxesall2 = document.getElementsByClassName("warehouse-all-"+ entry);
       if(checkCount===checkCount1)
        {
           
            checkboxesall2[0].checked=true;
            
        }
        else
        {
         
            checkboxesall2[0].checked=false;
           
        } 
     });
      }
    uncheckwarehouse(); 
  }
  function uncheckwarehouse()
  {
      var checkCount =$("input[name='warehouse-master[]']").length;
      var checkCount1 = $("input[name='warehouse-master[]']:checked").length;
       var checkboxesall=document.getElementsByClassName("warehouse");
      if(checkCount===checkCount1)
        {
            checkboxesall[0].checked=true;
        }
        else
        {
            checkboxesall[0].checked=false;
        } 
  }
 function getcheckAllCategoryPurchaseOrder(category_id)
    {

        $("#purchase-order-category-all-" + category_id).change(function () {
            $(".purchase-order-category-wise-all-" + category_id).prop('checked', $(this).prop("checked"));
            checkcheckboxproductcategorypurchaseorder();
        });
      
      
    }

function checkcheckboxproductcategorypurchaseorder(purchaseid,operation)
  {
      
       if(operation==="create"  ||  operation==="update")
      {
        $("#purchase-order-"+ purchaseid+"-index").prop("checked",true);  
      }
      
       var json = "<?php echo $json; ?>";
        if(json.length!=0) 
      {
       var obj = JSON.parse(json);
       
       obj.forEach(function(entry) 
     {
         
     
        var checkCount =$("input[class='purchase-order-category-all  purchase-order-category-wise-all-"+ entry+"']").length;
        
        var checkCount1 = $("input[class='purchase-order-category-all  purchase-order-category-wise-all-"+ entry+"']:checked").length;
       
        var checkboxesall2 = document.getElementsByClassName("purchase-order-category-all-"+ entry);
       if(checkCount===checkCount1)
        {
           
            checkboxesall2[0].checked=true;
            
        }
        else
        {
         
            checkboxesall2[0].checked=false;
           
        } 
     });
      }
    uncheckpurchaseorder(); 
  }
  function uncheckpurchaseorder()
  {
      var checkCount =$("input[name='purchase-order-main-category-id[]']").length;
      var checkCount1 = $("input[name='purchase-order-main-category-id[]']:checked").length;
       var checkboxesall=document.getElementsByClassName("purchase");
      if(checkCount===checkCount1)
        {
            checkboxesall[0].checked=true;
        }
        else
        {
            checkboxesall[0].checked=false;
        } 
  }
  
</script>