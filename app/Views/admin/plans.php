<style> .disabled{    background: #f1f1f1;}
    </style>
<div id="page-wrapper">
        <div class="col-md-12 graphs">
	   <div class="xs">
  	    <h3 class="pull-left">Shipping Plans</h3>
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
               <?php if(isset($upload_error)){  echo "<span class='alert-danger'>".$upload_error."</span>";   }?>  
            <?= form_open("admin/dashboard/save_plans",['class'=>'form-floating'])?>              
           <fieldset>  
               <h3>Startup</h3>
               <div class="col-lg-3">
                   <div class="form-group">
                    <label class="control-label">monthly</label>
                     <input type="text" name="m1" value="<?= set_value('m1',$data1->monthly ?? '')?>" onkeyup="checkInput(this)"  class="form-control1" required="">
                    </div>
               </div>
               <div class="col-lg-3">
                   <div class="form-group">
                    <label class="control-label">Quarterly</label>
                     <input type="text" name="q1" value="<?= set_value('q1',$data1->quarterly ?? '')?>" onkeyup="checkInput(this)"  class="form-control1" required="">
                    </div>
               </div>
               <div class="col-lg-3">
                   <div class="form-group">
                    <label class="control-label">Semi-Yearly</label>
                     <input type="text" name="s1" value="<?= set_value('s1',$data1->half ?? '')?>" onkeyup="checkInput(this)"  class="form-control1" required="">
                    </div>
               </div>                
              <div class="col-lg-3">
                  <div class="form-group">
                    <label class="control-label">Yearly</label>
                    <input type="text" name="y1" value="<?= set_value('y1',$data1->yearly ?? '')?>" onkeyup="checkInput(this)" class="form-control1" required="">
                  </div>
              </div>
               <hr>
               <h3>SME</h3>
               <div class="col-lg-3">
                   <div class="form-group">
                    <label class="control-label">monthly</label>
                     <input type="text" name="m2" value="<?= set_value('m2',$data2->monthly ?? '')?>" onkeyup="checkInput(this)"  class="form-control1" required="">
                    </div>
               </div>
               <div class="col-lg-3">
                   <div class="form-group">
                    <label class="control-label">Quarterly</label>
                     <input type="text" name="q2" value="<?= set_value('q2',$data2->quarterly ?? '')?>" onkeyup="checkInput(this)"  class="form-control1" required="">
                    </div>
               </div>
               <div class="col-lg-3">
                   <div class="form-group">
                    <label class="control-label">Semi-Yearly</label>
                     <input type="text" name="s2" value="<?= set_value('s2',$data2->half ?? '')?>" onkeyup="checkInput(this)"  class="form-control1" required="">
                    </div>
               </div>                
              <div class="col-lg-3">
                  <div class="form-group">
                    <label class="control-label">Yearly</label>
                    <input type="text" name="y2" value="<?= set_value('y2',$data2->yearly ?? '')?>" onkeyup="checkInput(this)" class="form-control1" required="">
                  </div>
              </div>
               <hr>
               <h3>Enterprise</h3>
               <div class="col-lg-3">
                   <div class="form-group">
                    <label class="control-label">Monthly</label>
                     <input type="text" name="m3" value="<?= set_value('m3',$data3->monthly ?? '')?>" onkeyup="checkInput(this)"  class="form-control1" required="">
                    </div>
               </div>
               <div class="col-lg-3">
                   <div class="form-group">
                    <label class="control-label">Quarterly</label>
                     <input type="text" name="q3" value="<?= set_value('q3',$data3->quarterly ?? '')?>" onkeyup="checkInput(this)"  class="form-control1" required="">
                    </div>
               </div>
               <div class="col-lg-3">
                   <div class="form-group">
                    <label class="control-label">Semi-Yearly</label>
                     <input type="text" name="s3" value="<?= set_value('s3',$data3->half ?? '')?>" onkeyup="checkInput(this)"  class="form-control1" required="">
                    </div>
               </div>                
              <div class="col-lg-3">
                  <div class="form-group">
                    <label class="control-label">Yearly</label>
                    <input type="text" name="y3" value="<?= set_value('y3',$data3->yearly ?? '')?>" onkeyup="checkInput(this)" class="form-control1" required="">
                  </div>
              </div>
               <hr>
            <div class="col-lg-12">
            <div class="form-group">
               <?= form_submit(['name'=>'submit','value'=>'Save','class'=>'btn btn-primary'])?>
             </div>
            </div>
          </fieldset>
         <?= form_close();?>
      </div>
    </div>
     
