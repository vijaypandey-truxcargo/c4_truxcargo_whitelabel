<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <h3 class="pull-left">Edit Vendor</h3>
            <?= anchor('admin/vendor', 'View Vendor', ['class' => 'btn btn-success pull-right']); ?>

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
                <?= form_open_multipart('admin/vendor/update_vendor/'.$vendor->id, ['class' => 'form-floating']); ?>
                <fieldset>
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">

                            <!-- Vendor Name -->
                            <div class="form-group">
                                <label class="control-label">Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="<?= $vendor->name; ?>" class="form-control" required="">
                            </div>

                            <!-- Vendor Code -->
                            <div class="form-group">
                                <label class="control-label">Vendor Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" value="<?= $vendor->code; ?>" class="form-control" required="">
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label class="control-label">Email ID <span class="text-danger">*</span></label>
                                <input type="text" name="email" value="<?= $vendor->email; ?>" class="form-control" required="">
                            </div>

                            <!-- AIR Divisor -->
                            <div class="form-group">
                                <label class="control-label">AIR Divisor</label>
                                <input type="number" name="air_divisor" value="<?= $vendor->air_divisor; ?>" class="form-control"  placeholder="Enter 4-digit divisor">
                            </div>

                            <!-- SFC Divisor -->
                            <div class="form-group">
                                <label class="control-label">SFC Divisor</label>
                                <input type="number" name="sfc_divisor" value="<?= $vendor->sfc_divisor; ?>" class="form-control"  placeholder="Enter 4-digit divisor">
                            </div>
                            
                                  <!-- Freight -->
                            <div class="form-group">
                                <label class="control-label">Min Freight</label>
                                <input type="number" name="min_freight" value="<?= $vendor->min_freight; ?>" class="form-control" step="any" placeholder="Enter Min Freight">
                            </div>

                            <!-- Mode -->
                            <!-- <div class="form-group">
                                <label class="control-label">Mode <span class="text-danger">*</span></label>
                                <select name="mode" class="form-control" required="">
                                    <option value="">SELECT Mode</option>
                                    <?php
                                        foreach($mode as $mode_name){ ?>
                                           <option value="<?= $mode_name->id ?>"
                                            <?= ($vendor->mode == $mode_name->id) ? 'selected' : '' ?>>
                                            <?= $mode_name->name ?>
                                        </option>
                                    <?php }
                                    ?>
                                </select>
                            </div> -->


                            <!-- Address -->
                            <div class="form-group">
                                <label class="control-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="address" rows="3"><?= $vendor->address; ?></textarea>
                            </div>

                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="control-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="<?= $vendor->phone; ?>" required="">
                            </div>

                            <!-- Vendor Type -->
                            <div class="form-group">
                                <label class="control-label">Vendor Type <span class="text-danger">*</span></label>
                                <select name="vendor_type" class="form-control" required="">
                                    <option value="">SELECT TYPE</option>
                                    <option value="CO-COURIER"        <?= ($vendor->vendor_type == 'CO-COURIER') ? 'selected' : '' ?>>CO-COURIER</option>
                                    <option value="INVENTORY VENDOR" <?= ($vendor->vendor_type == 'INVENTORY VENDOR') ? 'selected' : '' ?>>INVENTORY VENDOR</option>
                                    <option value="PURCHASE VENDOR"  <?= ($vendor->vendor_type == 'PURCHASE VENDOR') ? 'selected' : '' ?>>PURCHASE VENDOR</option>
                                    <option value="RENT"             <?= ($vendor->vendor_type == 'RENT') ? 'selected' : '' ?>>RENT</option>
                                </select>
                            </div>

                            <!-- Destination Hub -->
                            <div class="form-group">
                                <label class="control-label">DESTINATION HUB <span class="text-danger">*</span></label>
                                <select class="form-control" name="destination_hub" required="">
                                    <option value="">SELECT HUB</option>
                                    <?php foreach($hub as $hubname): ?>
                                        <option value="<?= $hubname->id ?>"
                                            <?= ($vendor->destination_hub == $hubname->id) ? 'selected' : '' ?>>
                                            <?= $hubname->code ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Company -->
                            <div class="form-group">
                                <label class="control-label">Company <span class="text-danger">*</span></label>
                                <select class="form-control" name="company" required="">
                                    <option value="">SELECT COMPANY</option>
                                    <?php foreach($companies as $company): ?>
                                        <option value="<?= $company->id ?>"
                                            <?= ($vendor->company == $company->id) ? 'selected' : '' ?>>
                                            <?= $company->code ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Min Actual -->
                           <div class="form-group">
                                <label>Min Actual Weight <span class="text-danger">*</span></label>
                                <input type="text" name="min_weight" value="<?= $vendor->min_weight; ?>" placeholder="Enter Min Weight" class="form-control" required>
                            </div>

                            <!-- Max Actual -->
                           <div class="form-group">
                                <label>Max Actual Weight <span class="text-danger">*</span></label>
                                <input type="text" name="max_weight" value="<?= $vendor->max_weight; ?>" placeholder="Enter Max Weight" class="form-control" required>
                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label class="control-label">Status</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="1" <?= ($vendor->status == 1 ? 'checked' : '') ?>> Active
                                </label>
                                <label class="radio-inline" style="margin-left:15px;">
                                    <input type="radio" name="status" value="0" <?= ($vendor->status == 0 ? 'checked' : '') ?>> Inactive
                                </label>
                            </div>

                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        <?= form_submit(['name' => 'submit', 'value' => 'Update', 'class' => 'btn btn-primary']); ?>
                    </div>

                </fieldset>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
