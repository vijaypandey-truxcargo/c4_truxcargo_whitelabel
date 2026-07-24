<style> .disabled{    background: #f1f1f1;} 
    </style>
<div id="page-wrapper">
        <div class="col-md-12 graphs">
	   <div class="xs">
  	    <h3 class="pull-left">Edit Bank</h3>
            <?= anchor('admin/customer/bank/all','View Bank Info',['class'=>'btn btn-success pull-right']);?>
		<div class="col-lg-12">
          <?php $error=session()->getFlashdata('error');
                $error_class=session()->getFlashdata('error_class');
                if($error):?>
                  <div class="alert alert-dismissible <?= $error_class;?>">
                      <strong><?= $error;?></strong>
                  </div>
                <?php endif;?>
              </div>
	 <div class="clearfix"></div>
  	    <div class="well1 white">                
            <?= form_open_multipart("admin/customer/update_bank/{$data->id}",['class'=>'form-floating'])?>              
           <fieldset>   
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Username</label>
                     <?= form_input(['name'=>'username', 'class'=>'form-control1 disabled', 'value'=>set_value('username',$data->username),'readonly'=>'readonly']);?>             
                 </div>
               </div>
               <div class="clearfix"></div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Account No</label>
                     <?= form_input(['name'=>'account','id'=>'account', 'class'=>'form-control1', 'value'=>set_value('account',$data->account),'required'=>'required']);?>             
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">IFSC</label>
                     <?= form_input(['name'=>'ifsc','id'=>'ifsc', 'class'=>'form-control1', 'value'=>set_value('ifsc',$data->ifsc),'required'=>'required']);?>             
                 </div>
               </div> 
               <div class="col-lg-4">
                 <div class="form-group">
                     <br>
                     <button class="btn btn-primary" onclick="bankverify()" type="button">Verify</button>         
                 </div>
               </div> 
                <div class="clearfix"></div>
                <div class="col-lg-12" id="info"></div>
                 <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Bank Name</label>
                     <?= form_input(['name'=>'bname','id'=>'bname', 'class'=>'form-control1', 'value'=>set_value('bname',$data->bname),'required'=>'required']);?>             
                 </div>
               </div>
               <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Account Holder Name</label>
                     <?= form_input(['name'=>'holder_name','id'=>'holder_name', 'class'=>'form-control1', 'value'=>set_value('holder_name',$data->holder_name),'required'=>'required']);?>             
                 </div>
               </div>
                <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Verified Account</label>
                     <?= form_input(['name'=>'verify','id'=>'verify', 'class'=>'form-control1', 'value'=>set_value('verify',$data->verify)]);?>             
                 </div>
               </div>
                <div class="col-lg-4">
                 <div class="form-group">
                    <label class="control-label">Cancel Cheque</label>
                     <input type="file" name="image"  class="form-control"/> 
                      <small>(Max size: 100KB)</small>
                    <?php  if(!empty($data->cheque)){
                    echo '<br><a href="'.base_url().'uploads/bank/'.$data->cheque.'" target="_blank"><img src="'.base_url().'uploads/bank/'.$data->cheque.'" style="width:auto; height:150px" /></a>';}?>
                                                       
                 </div>
               </div> 
               <div class="clearfix"></div>
               <div class="col-lg-4">
                  <div class="form-group">
                  <?php if(in_array("Add Customer", $GLOBALS['permission'])){ echo form_submit(['name'=>'submit','value'=>'Update','class'=>'btn btn-primary']);}?>
                  </div>
               </div>
          </fieldset>
         <?= form_close();?>
      </div>
    </div>
             <div class="loader" id="loader"><img src="<?= base_url('assets/images/loading.gif');?>"></div>
    <script>
        function bankverify() { 
            var account = $("#account").val();
            var ifsc = $("#ifsc").val();
            $.ajax({  
                url:"<?= base_url('admin/customer/bank_verify') ?>",
                method:"POST",  
                data:{[csrfName]: csrfHash,account:account,ifsc:ifsc}, 
                dataType: 'json',
                beforeSend: function() {
                    $('#loader').css('display', 'block');
                }, 
                success:function(data){
                    $('#loader').css('display', 'none');
                    if(data===2){ document.getElementById("info").innerHTML= "<span style='color:red'>Bank account is not exist.</span>";  }
                    else if(data===3){ document.getElementById("info").innerHTML= "<span style='color:red'>Account No/IFSC is empty.</span>";  }
                    else {
                         $('#bname').val(data.bank);	
                         $('#holder_name').val(data.holder);	
                         $('#verify').val(1);	
                        document.getElementById("info").innerHTML= "<span style='color:green'>Verified Account.</span>"; 
                    }                      
                },
                error:function() {  alert('fail');}    
            }); 
        }
     </script>
     
     
