<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <h3 class="pull-left">Add Service</h3>
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
                <?= form_open_multipart('admin/service/insert_service', ['class' => 'form-floating']); ?>
                <fieldset>
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Service Name -->
                            <div class="form-group">
                                <label class="control-label">Service Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required="">
                            </div>

                            <!-- Service Code -->
                            <div class="form-group">
                                <label class="control-label">Service Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" required="">
                            </div>

                            <!-- Company -->
                            <div class="form-group">
                                <label class="control-label">Company <span class="text-danger">*</span></label>
                                <select class="form-control" name="company" id="company_id" required="">
                                    <option value="">SELECT...</option>
                                    <?php
                                        foreach($companies as $company){ ?>
                                            <option value="<?php echo $company->id ?>"><?php echo $company->code ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>

                            <!-- Type -->
                            <div class="form-group">
                                <label class="control-label">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" required="">
                                    <option value="">Select Type</option>
                                    <option value="INTERNATIONAL">INTERNATIONAL</option>
                                    <option value="DOMESTIC">DOMESTIC</option>
                                </select>
                            </div>

                             <!-- Status -->
                            <div class="form-group">
                                <label class="control-label">Status</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="1" checked> Active
                                </label>
                                <label class="radio-inline" style="margin-left:15px;">
                                    <input type="radio" name="status" value="0"> Inactive
                                </label>
                            </div>

                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">

                            <!-- Service Type -->
                            <div class="form-group">
                                <label class="control-label">Service Type <span class="text-danger">*</span></label>
                                <select name="service_type" class="form-control" required="">
                                    <option value="">Select Service Type</option>
                                    <option value="DOMESTIC">DOMESTIC</option>
                                    <option value="INTERNATIONAL">INTERNATIONAL</option>
                                    <option value="INTERNATIONAL IMPORT">INTERNATIONAL IMPORT</option>
                                </select>
                            </div>

                            <!-- Mode -->
                            <div class="form-group">
                                <label class="control-label">Mode <span class="text-danger">*</span></label>
                                <!--<select name="mode" class="form-control" required="">-->
                                <!--    <option value="">Select Mode</option>-->
                                <!--    <option value="AIR">AIR</option>-->
                                <!--    <option value="FULL TRACK LORD">FULL TRACK LORD</option>-->
                                <!--    <option value="ROAD">ROAD</option>-->
                                <!--    <option value="TRAIN">TRAIN</option>-->
                                <!--</select>-->
                                <select class="form-control" name="mode" required="">
                                    <option value="">Select Mode</option>
                                    <?php
                                        foreach($mode as $mode_name){ ?>
                                            <option value="<?php echo $mode_name->name ?>"><?php echo $mode_name->name ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>

                            <!-- Weight_salb -->
                            <!-- <div class="form-group">
                                <label class="control-label">Weight Slab <span class="text-danger">*</span></label>
                                <input type="text" name="min_weight" class="form-control"  placeholder="Enter Min Weight" required>
                            </div> -->

                            <!-- vendor -->
                            <div class="form-group">
                                <label>Vendor <span class="text-danger">*</span></label>
                                <select name="vendor[]" class="form-control select2" multiple data-placeholder="Select Vendor" required="">
                                    <option></option>
                                    <option value="all">Select All</option>
                                    <?php if(!empty($vendors)) { ?>
                                        <?php foreach($vendors as $v) { ?>
                                            <option value="<?= $v->id; ?>">
                                                <?=  $v->name  ?>
                                            </option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>

                            <!-- FOV -->
                            <!-- <div class="form-group">
                                <label class="control-label">FOV</label> 
                                <input type="number" name="fov" class="form-control" step="any" placeholder="Enter FOV">
                            </div> -->

                            <!-- ROV -->
                            <!-- <div class="form-group">
                                <label class="control-label">ROV</label>
                                <input type="number" name="rov" class="form-control" step="any" placeholder="Enter ROV">
                            </div> -->

                              <!-- Address -->
                            <div class="form-group">
                                <label class="control-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="address" rows="3" required=""></textarea>
                            </div>

                        </div>
                    </div>

                    <!-- Submit Button -->
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

