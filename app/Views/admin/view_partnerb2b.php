<script>
function validate(form) {
        return confirm('Do you really want to delete this record ?');   
}
</script>
<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">
            <h3 class="pull-left">  B2B Partner</h3>
         <?php if(in_array("Add Partner", $GLOBALS['permission'])){ echo anchor('admin/partnerb2b/add','Add B2B Partner',['class'=>'btn btn-danger pull-right']);}?>
	  <div class="clearfix"></div>
           
   
        <div class="bs-example4" data-example-id="contextual-table" >
             
              <table class="table table-responsive" id="example">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th>Panel Name</th>
                          <th> Username</th>
                          <th> Password</th>
                          <th> API Key</th>
                            <th>Action</th>
                      </tr>
                  </thead>
                  <tbody>
          <?php if(empty($count)){ $count =0;} else {$count=$count;} ?>
          <?php if (empty($code)): ?>
                      <tr class="active">
                          <td colspan="6" class="text-center" style="font-weight:bold; color:#d00;">No Data Found</td>
                      </tr>
          <?php else: ?>
               <?php foreach ($code as $key):   $count++;  ?>
                      <tr class="active" style="    overflow-wrap: anywhere;">
                          <td width="4%"><?= $count; ?></td>
                          <td><?= $key->title; ?></td>
                          <td><?= $key->username; ?></td>
                          <td><?= $key->password; ?></td>
                          <td><?= $key->apikey; ?></td>                        
                           <td width="15%"><?php if(in_array("Add Partner", $GLOBALS['permission'])){ echo anchor("admin/partnerb2b/edit/{$key->id}",'Edit',['class'=>'btn btn-primary pull-left']); 
                             echo form_open('admin/partnerb2b/delete',['onsubmit'=>'return validate(this);']),
                                 form_hidden('id',$key->id),
                                 form_submit(['name'=>'submit','value'=>'Delete','class'=>'btn btn-danger pull-right']),
                           form_close(); }?> </td>
                      </tr>
                          <?php endforeach;?>
          <?php endif; ?>
                  </tbody>
              </table>
              <p class="pagination"><?php echo $links; ?></p>
          </div>
        </div>
  
