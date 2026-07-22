<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <h3 class="pull-left">Edit Service</h3>
            <?= anchor('admin/service', 'View Service', ['class' => 'btn btn-success pull-right']); ?>

            <div class="clearfix"></div>
            <div class="col-lg-12">
                <?php
                    $error = $this->session->flashdata('error');
                    $error_class = $this->session->flashdata('error_class');
                    if ($error):
                ?>
                <div class="alert alert-dismissible <?= $error_class; ?>">
                    <strong><?= $error; ?></strong>
                </div>
                <?php endif; ?>
            </div>

            <div class="clearfix"></div>

            <div class="well1 white">
                <?= form_open_multipart('admin/service/update_service/' . $service->id, ['class' => 'form-floating']); ?>
                <fieldset>
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Service Name -->
                            <div class="form-group">
                                <label class="control-label">Service Name</label>
                                <input type="text" name="name" class="form-control" value="<?= set_value('name', $service->name); ?>" required="">
                            </div>

                            <!-- Service Code -->
                            <div class="form-group">
                                <label class="control-label">Service Code</label>
                                <input type="text" name="code" class="form-control" value="<?= set_value('code', $service->code); ?>" required="">
                            </div>

                            <!-- Company -->
                            <div class="form-group">
                                <label class="control-label">Company</label>
                                  <select class="form-control" name="company" id="company_id" required>
                                    <option value="">SELECT...</option>
                                    <?php foreach ($companies as $c): ?>
                                        <option value="<?= $c->id ?>" <?= ($service->company == $c->id) ? 'selected' : '' ?>>
                                            <?= $c->name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Type -->
                            <div class="form-group">
                                <label class="control-label">Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="INTERNATIONAL" <?= ($service->type == 'INTERNATIONAL') ? 'selected' : ''; ?>>INTERNATIONAL</option>
                                    <option value="DOMESTIC" <?= ($service->type == 'DOMESTIC') ? 'selected' : ''; ?>>DOMESTIC</option>
                                </select>
                            </div>

                            
                            <!-- Status -->
                            <div class="form-group">
                                <label class="control-label">Status</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="1" <?= ($service->status == 1) ? 'checked' : ''; ?>> Active
                                </label>
                                <label class="radio-inline" style="margin-left:15px;">
                                    <input type="radio" name="status" value="0" <?= ($service->status == 0) ? 'checked' : ''; ?>> Inactive
                                </label>
                            </div>

                        </div>

                        

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Address -->
                            <div class="form-group">
                                <label class="control-label">Address</label>
                                <textarea class="form-control" name="address" rows="3"><?= set_value('address', $service->address); ?></textarea>
                            </div>

                            <!-- Service Type -->
                            <div class="form-group">
                                <label class="control-label">Service Type</label>
                                <select name="service_type" class="form-control" required>
                                    <option value="">Select Service Type</option>
                                    <option value="DOMESTIC" <?= ($service->service_type == 'DOMESTIC') ? 'selected' : ''; ?>>DOMESTIC</option>
                                    <option value="INTERNATIONAL" <?= ($service->service_type == 'INTERNATIONAL') ? 'selected' : ''; ?>>INTERNATIONAL</option>
                                    <option value="INTERNATIONAL IMPORT" <?= ($service->service_type == 'INTERNATIONAL IMPORT') ? 'selected' : ''; ?>>INTERNATIONAL IMPORT</option>
                                </select>
                            </div>

                            <!-- Mode -->
                            <div class="form-group">
                                <label class="control-label">Mode</label>
                                <!--<select name="mode" class="form-control" required>-->
                                <!--    <option value="">Select Mode</option>-->
                                <!--    <option value="AIR" <?= ($service->mode == 'AIR') ? 'selected' : ''; ?>>AIR</option>-->
                                <!--    <option value="FULL TRACK LORD" <?= ($service->mode == 'FULL TRACK LORD') ? 'selected' : ''; ?>>FULL TRACK LORD</option>-->
                                <!--    <option value="ROAD" <?= ($service->mode == 'ROAD') ? 'selected' : ''; ?>>ROAD</option>-->
                                <!--    <option value="TRAIN" <?= ($service->mode == 'TRAIN') ? 'selected' : ''; ?>>TRAIN</option>-->
                                <!--</select>-->
                                  <select name="mode" class="form-control" required>
                                <option value="">SELECT...</option>
                                    <?php foreach ($mode as $c): ?>
                                        <option value="<?= $c->name ?>" <?= ($service->mode == $c->name) ? 'selected' : '' ?>>
                                            <?= $c->name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </option>    
                            </select>
                            </div>

                            <div class="form-group">
                            <label>Vendor <span class="text-danger">*</span></label>

                            <select name="vendor[]" class="form-control select2" multiple data-placeholder="Select Vendor">
                                <option></option>
                                <option value="all">Select All</option>

                                <?php
                                $selected_vendors = explode(',', $service->vendor);
                                if (!empty($vendors)) { ?>
                                    <?php foreach ($vendors as $v) { ?>
                                        <option value="<?= $v->id; ?>"
                                            <?= in_array($v->id, $selected_vendors) ? 'selected' : '' ?>>
                                            <?= $v->code ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
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


<script>
$(document).ready(function() {
    $(".select2").select2({
        placeholder: "Select Vendor",
        allowClear: true,
        closeOnSelect: false
    });

    $('.select2').on('select2:select', function (e) {
        if (e.params.data.id === "all") {
            let allValues = [];
            $(this).find('option').each(function () {
                if ($(this).val() !== "all" && $(this).val() !== "") {
                    allValues.push($(this).val());
                }
            });
            $(this).val(allValues).trigger("change.select2");
        }
    });
});
</script>
