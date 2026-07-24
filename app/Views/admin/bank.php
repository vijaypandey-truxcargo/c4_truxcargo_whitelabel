<script>
function validate(form) {
        return confirm('Do you really want to delete this record ?');   
}
</script>
<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">
            <h3 class="pull-left">All Customer's Bank Info</h3>
	  <div class="clearfix"></div>
          <div class="bs-example4" data-example-id="contextual-table">
             <?= form_open('admin/customer/bank/');?>
              <div class="col-lg-3">
                   <p class="input-group">
                      <select class="form-control" name="login_id">
                          <option value="All" <?php if(set_value('login_id')=='All'){echo 'selected="selected"';}?>>All Members</option>
                         <?php foreach ($customer as $key){ ?>
                          <option value="<?php echo $key->id;?>" <?php if(set_value('login_id')==$key->id){echo 'selected="selected"';}?>><?php echo $key->first.' '.$key->last.' ('.$key->username.')';?> </option>
                         <?php }?>
                      </select>  
                  </p>
                </div> 
                
                  <div class="col-lg-3" id="apply">
                  <p class="input-group">
                      <input type="submit" name="apply" value="Apply" class="btn btn-primary">        
                  </p>                  
                  </div> 
               <?= form_close();?>
              <a href="<?= base_url('admin/customer/bank_report');?>" class="btn btn-default pull-right" target="_blank"><i class="fa fa-download"></i> All Data</a>
              <table class="table table-responsive" style="margin-top: 40px" id="example">
                  <thead>
                      <tr>
                          <th>#</th>
                           <th> Username</th> 
                          <th> Bank Holder Name</th>
                          <th> Bank</th>
                          <th> Account Name</th>
                           <th> Ifsc</th>
                           <th>Cancelled Cheque </th>
                           <th>Status</th>
                           <th>Action</th>
                        </tr>
                  </thead>
                  <tbody>
          <?php 
               if(empty($count)){ $count =0;} else {$count=$count;}   foreach ($code as $key):   $count++;?>
                      <tr>
                          <td><?= $count; ?></td>
                           <td><?= $user($key->login_id);?></td>
                          <td><?= $key->holder_name; ?></td>
                          <td><?= $key->bname; ?> </td>
                          <td><?= $key->account; ?></td>                          
                          <td><?= $key->ifsc; ?>   </td> 
                            <td><?php if(!empty($key->cheque)){echo '<a href="'.base_url().'uploads/bank'.$key->cheque.'" target="_blank"><img src="'.base_url().'uploads/bank/'.$key->cheque.'" width="100"></a>';}?></td>
                           <td><?php if($key->verify==1){ echo '<span style="color:green">Verified</span>';} ?>   </td>  
                            <td>
                              <?= anchor("admin/customer/edit_bank/{$key->id}",'<i class="fa fa-pencil"></i>',['class'=>'btn btn-primary pull-left','title'=>'Edit ']);  ?>
                            
                             </td>
                      </tr>
                          <?php endforeach;?>
                  </tbody>
              </table>
              <p class="pagination"><?php echo $links; ?></p>
          </div>
        </div>
  