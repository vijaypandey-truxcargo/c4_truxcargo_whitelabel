<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <h3 class="pull-left">Edit Company</h3>
            <?= anchor('admin/company', 'View Company', ['class' => 'btn btn-success pull-right']); ?>

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
                <?= form_open_multipart("admin/company/update_company/{$data->id}", ['class' => 'form-floating']); ?>
                <fieldset>

                    <div class="row">

                        <!-- LEFT COLUMN -->
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">Code</label>
                                <input type="text" name="code" class="form-control1" value="<?= set_value('code', $data->code) ?>" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Name</label>
                                <input type="text" name="name" class="form-control" value="<?= set_value('name', $data->name) ?>" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Website</label>
                                <input type="text" name="website" class="form-control" value="<?= set_value('website', $data->website) ?>" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= set_value('phone', $data->phone) ?>" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= set_value('email', $data->email) ?>" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">State</label>
                                <input type="text" name="state" class="form-control" value="<?= set_value('state', $data->state) ?>" required="">
                            </div>

                        </div>

                        <!-- RIGHT COLUMN -->
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">Pincode</label>
                                <input type="text" name="pincode" class="form-control" value="<?= set_value('pincode', $data->pincode) ?>" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Pan Number</label>
                                <input type="text" name="pan_number" class="form-control" value="<?= set_value('pan_number', $data->pan_number) ?>" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Cin Number</label>
                                <input type="text" name="cin_number" class="form-control" value="<?= set_value('cin_number', $data->cin_number) ?>" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Gst Number</label>
                                <input type="text" name="gst_number" class="form-control" value="<?= set_value('gst_number', $data->gst_number) ?>" required="">
                            </div>

                            <div class="form-group">
                                <label class="control-label">SAC Code</label>
                                <input type="text" name="sac_code" class="form-control" value="<?= set_value('sac_code', $data->sac_code) ?>" required="">
                            </div>

                             <div class="form-group">
                                <label class="control-label">City</label>
                                <input type="text" name="city" class="form-control" value="<?= set_value('city', $data->city) ?>" required="">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Address</label>
                        <textarea class="form-control" name="address" rows="3"><?= set_value('address', $data->address) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Status</label><br>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="1" <?= ($data->status == 1) ? 'checked' : '' ?>> Active
                        </label>
                        <label class="radio-inline" style="margin-left:15px;">
                            <input type="radio" name="status" value="0" <?= ($data->status == 0) ? 'checked' : '' ?>> Inactive
                        </label>
                    </div>

                    <div class="form-group">
                        <?= form_submit(['name' => 'submit', 'value' => 'Update', 'class' => 'btn btn-primary']); ?>
                    </div>

                </fieldset>
                <?= form_close(); ?>
            </div>

        </div>
    </div>
</div>
