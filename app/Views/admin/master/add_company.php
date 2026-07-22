<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <h3 class="pull-left">Add Company</h3>
            <?= anchor('admin/company','View Company',['class'=>'btn btn-success pull-right']);?>

            <div class="clearfix"></div>
            <div class="col-lg-12">
                <?php
                    $error = session()->getFlashdata('error');
                    $error_class = session()->getFlashdata('error_class');
                    if ($error):
                ?>
                <div class="alert alert-dismissible <?= $error_class; ?>">
                    <strong><?= $error; ?></strong>
                </div>
                <?php endif; ?>
            </div>

            <div class="clearfix"></div>

            <div class="well1 white">
                <?= form_open_multipart('admin/company/insert_company', ['class' => 'form-floating']); ?>
                <fieldset>

                    <div class="row">

                        <!-- LEFT COLUMN -->
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">Code<span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control1" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Website<span class="text-danger">*</span></label>
                                <input type="text" name="website" class="form-control" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Phone<span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required="">
                                 <span id="phone_error" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Email<span class="text-danger">*</span></label>
                               <input type="email" name="email" id="email" class="form-control" required>
                               <span id="email_error" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label class="control-label">State<span class="text-danger">*</span></label>
                                <input type="text" name="state" class="form-control" required="">
                            </div>

                        </div>

                        <!-- RIGHT COLUMN -->
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">Pincode<span class="text-danger">*</span></label>
                                 <input type="text" name="pincode" id="pincode" class="form-control" maxlength="6" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required>
                                <span id="pincode_error" class="text-danger"></span>
                                </div>

                            <div class="form-group">
                                <label class="control-label">Pan Number<span class="text-danger">*</span></label>
                                <input type="text" name="pan_number" class="form-control" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Cin Number<span class="text-danger">*</span></label>
                                <input type="text" name="cin_number" class="form-control" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Gst Number<span class="text-danger">*</span></label>
                                <input type="text" name="gst_number" class="form-control" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">SAC Code<span class="text-danger">*</span></label>
                                <input type="text" name="sac_code" class="form-control" required="">
                            </div>
                             <div class="form-group">
                                <label class="control-label">City<span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" required="">
                            </div>
                        </div> 
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
                        <?= form_submit(['name' => 'submit', 'value' => 'Save', 'class' => 'btn btn-primary']); ?>
                    </div>

                </fieldset>
                <?= form_close(); ?>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function(){

    $('#phone').on('input', function(){
        this.value = this.value.replace(/[^0-9]/g, '');

        if(this.value.length > 0 && this.value.length != 10){
            $('#phone_error').text('Phone number must be 10 digits.');
        }else{
            $('#phone_error').text('');
        }
    });

    $('#pincode').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');

        if(this.value.length > 0 && this.value.length != 6){
            $('#pincode_error').text('Pincode must be exactly 6 digits.');
        }else{
            $('#pincode_error').text('');
        }
    });

    $('form').on('submit', function(e){

        var pincode = $('#pincode').val();

        if(!/^[0-9]{6}$/.test(pincode)){
            e.preventDefault();
            $('#pincode_error').text('Pincode must be exactly 6 digits.');
            $('#pincode').focus();
            return false;
        }

    });

});
</script>
