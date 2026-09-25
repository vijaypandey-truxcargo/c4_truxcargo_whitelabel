  <div id="page-wrapper">
        <div class="col-md-12 graphs">
	   <div class="xs">
  	    <h3 class="pull-left">Add B2B Partner</h3>
            <?= anchor('admin/partnerb2b/','View B2B Partner',['class'=>'btn btn-success pull-right']);?>
		<div class="col-lg-12">
          <?php $error = session()->getFlashdata('error');
                $error_class = session()->getFlashdata('error_class');
                if($error):?>
                  <div class="alert alert-dismissible <?= $error_class;?>">
                      <strong><?= $error;?></strong>
                  </div>
                <?php endif;?>
              </div>
	 <div class="clearfix"></div>
  	    <div class="well1 white">
            <?= form_open_multipart('admin/partnerb2b/insert',['class'=>'form-floating','autocomplete'=>'off'])?>              
          <fieldset>                
            <div class="form-group">
              <label class="control-label">Panel Name</label>
              <?= form_input(['name'=>'title', 'class'=>'form-control1', 'value'=>set_value('title'),'required'=>'required']);?>             
            </div>
              
            <div class="form-group">
              <label class="control-label">Username</label>
              <?= form_input(['name'=>'username', 'class'=>'form-control1', 'value'=>set_value('username'),'required'=>'required']);?>             
            </div>
              
            <div class="form-group">
              <label class="control-label">Password</label>
              <?= form_input(['name'=>'password', 'class'=>'form-control1', 'value'=>set_value('password'),'required'=>'required']);?>             
            </div>
              
           <div class="form-group">
              <label class="control-label">Api Key</label>
              <?= form_input(['name'=>'apikey', 'class'=>'form-control1', 'value'=>set_value('apikey'),'required'=>'required']);?>             
            </div>
             <div class="form-group">
              <label class="control-label">Rating</label>
              <?= form_input(['name'=>'overall', 'class'=>'form-control1', 'value'=>set_value('overall'),'required'=>'required']);?>             
            </div>
              <div class="form-group">
              <label class="control-label">JWT TOken</label>
              <?= form_input(['name'=>'jwt', 'class'=>'form-control1', 'value'=>set_value('jwt')]);?>             
            </div>
            
               
              <div class="clearfix"></div>
            <div class="form-group">
               <?= form_submit(['name'=>'submit','value'=>'Save','class'=>'btn btn-primary'])?>
             </div>
          </fieldset>
         <?= form_close();?>
      </div>
    </div>
     
      
     
