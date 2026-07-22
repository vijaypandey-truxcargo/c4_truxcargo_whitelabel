  <div id="page-wrapper">
        <div class="col-md-12 graphs">
	   <div class="xs">
  	    <h3 class="pull-left">Add Hub</h3>
            <?= anchor('admin/master/hub_masters','View Hub',['class'=>'btn btn-success pull-right']);?>
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
            <?= form_open_multipart('admin/master/insert_hub',['class'=>'form-floating'])?>
          <fieldset>

              <div class="form-group">
                <label class="control-label">Code</label>
                <input type="text" name="code" class="form-control1" required="">
              </div>
              <div class="form-group">
                <label class="control-label">Name</label>
                <input type="text" name="name" class="form-control" required="">
              </div>
               <div class="form-group">
                    <label class="control-label">Hub Type</label>
                    <select class="form-control" name="type" id="hub_type">
                        <option value="">SELECT...</option>
                        <option value="HUB">HUB</option>
                        <option value="FRANCHISE">FRANCHISE</option>
                        <option value="BRANCH">BRANCH</option>
                        <option value="DISTRICT HUB">DISTRICT HUB</option>
                        <option value="MASTER DISTRICT HUB">MASTER DISTRICT HUB</option>
                        <option value="ROUTING HUB">ROUTING HUB</option>
                        <option value="STATE CAPITAL HUB">STATE CAPITAL HUB</option>
                        <option value="ZONAL HUB">ZONAL HUB</option>
                    </select>
                </div>


              <div class="form-group">
                <label class="control-label">phone</label>
                <input type="text" name="phone" class="form-control" required="">
              </div>

              <div class="form-group">
                <label class="control-label">Email</label>
                <input type="email" name="email_id" class="form-control" required="">
              </div>
              <div class="form-group">
                <label class="control-label">Company</label>
                <select class="form-control" name="company" id="company_id">
                    <option value="">SELECT...</option>
                    <?php
                      foreach($companies as $company){ ?>
                        <option value="<?php echo $company->id ?>"><?php echo $company->code ?></option>
                     <?php }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">Address</label>
                <textarea class="form-control" name="address" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label class="control-label">Status</label><br>

                <label class="radio-inline">
                    <input type="radio" name="status" value="1" checked> Active
                </label>

                <label class="radio-inline" style="margin-left:15px;">
                    <input type="radio" name="status" value="0"> Inactive
                </label>
            </div>

            <div class="form-group">
               <?= form_submit(['name'=>'submit','value'=>'Save','class'=>'btn btn-primary'])?>
             </div>
          </fieldset>
         <?= form_close();?>
      </div>
    </div>
