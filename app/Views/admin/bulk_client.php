  <div id="page-wrapper">
        <div class="col-md-12 graphs">
	   <div class="xs">
  	    <h3 class="pull-left">Upload Bulk Customers</h3>
 		<div class="col-lg-12">
                 <?php
                  $action='admin/customer/upload_client';
                 $error=session()->getFlashdata('error');
                $error_class=session()->getFlashdata('error_class');
                if($error):?>
                  <div class="alert alert-dismissible <?= $error_class;?>">
                      <strong><?= $error;?></strong>
                  </div>
                <?php endif;?>
              </div>
	            <div class="clearfix"></div>
           
              <a href="<?= base_url('backend/clients.csv');?>" class="pull-right btn btn-warning" download>Download Sample</a>
              <div class="well1 white">
             
            <?= form_open_multipart($action ,['class'=>'form-floating','autocomplete'=>'off'])?>              
          <fieldset>     
              
            <div class="col-lg-6">
              <div class="form-group">
                  <label class="control-label">CSV  File</label>
                  <input type="file" name="file"  class="form-control"/>
              </div>
              </div>       
            
         <div class="clearfix"></div>
         <div class="col-lg-12">
            <div class="form-group">
               <?= form_submit(['name'=>'importSubmit','value'=>'Import','class'=>'btn btn-primary'])?>
             </div>
         </div>    
          </fieldset>
         <?= form_close();?>
      </div>
    </div>
      
             
