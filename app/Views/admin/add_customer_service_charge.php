<style>
.checkbox-group {
    margin-left: 12px;
}
.checkbox-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}
.checkbox-row input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: #1E88E5;
    cursor: pointer;
}

/* Select2 dropdown scroll */
.select2-results__options {
    max-height: 220px;
    overflow-y: auto;
}

/* Selected items area scroll (top box) */
.select2-selection--multiple {
    max-height: 120px;
    overflow-y: auto;
}

</style>

<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">
            <h3 class="pull-left">Add Customer Service Charge</h3>
            <?= anchor('admin/CustomerServiceCharge/','View List',['class'=>'btn btn-success pull-right']);?>
            <div class="col-lg-12">
                <?php 
                $error = session()->getFlashdata('error');
                $error_class = session()->getFlashdata('error_class');
                if($error): ?>
                    <div class="alert alert-dismissible <?= $error_class;?>">
                        <strong><?= $error;?></strong>
                    </div>
                <?php endif;?>
            </div>

            <div class="clearfix"></div>

            <div class="well1 white form-container">

                <?= form_open('admin/CustomerServiceCharge/insert') ?>
                <fieldset>

           
                    <div class="form-group">
                        <label>Customer</label>

                        <select name="customer_id[]" id="customer_select" class="form-control" multiple>
                            <option></option>
                            <option value="all">Select All</option>
                            <?php foreach($customers as $c): ?>
                                <option value="<?= $c->id ?>">
                                    <?= $c->code ? $c->code.' - ' : '' ?><?= $c->first ?> <?= $c->last ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- SERVICE -->
                    <div class="form-group">
                        <label>Service <span style="color:red">*</span></label>
                        <select name="service_id" class="form-control" required>
                            <option value="">Select Anyone</option>
                            <?php foreach($services as $c): ?>
                                <option value="<?= $c->id ?>"><?= $c->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- VENDOR -->
                    <div class="form-group">
                        <label>Vendor</label>
                        <select name="vendor_id" class="form-control">
                            <option value="">Select Anyone</option>
                            <?php foreach($vendors as $c): ?>
                                <option value="<?= $c->id ?>"><?= $c->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- MIN CHARGE -->
                    <div class="form-group">
                        <label>Minimum Charge <span style="color:red">*</span></label>
                        <input class="form-control" type="number" step="0.01" name="min_charge" required>
                    </div>

                    <!-- DIVISOR -->
                    <div class="form-group">
                        <label>Divisor <span style="color:red">*</span></label>
                        <input class="form-control" type="text" maxlength="5" name="divisor" required>
                    </div>

                    <!-- MIN WEIGHT -->
                    <div class="form-group">
                        <label>Minimum Weight <span style="color:red">*</span></label>  
                        <input class="form-control" type="number" step="0.01" name="min_weight" required>
                    </div>

                    <!-- FUEL PERCENTAGE -->
                    <div class="form-group">
                        <label>Fuel Percentage <span style="color:red">*</span></label>  
                        <input class="form-control" type="number" step="0.01" name="fuel_percent" required>
                    </div>

                    <!-- STATUS -->
                    <div class="form-group">
                        <label>Status</label>
                        <div>
                            <label class="radio-inline" style="margin-right:20px;">
                                <input name="status" type="radio" value="1" checked> Active
                            </label>

                            <label class="radio-inline">
                                <input name="status" type="radio" value="0"> Inactive
                            </label>
                        </div>
                    </div>

                    <!-- BUTTON -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-save">Save</button>
                    </div>

                </fieldset>
                <?= form_close(); ?>

            </div>
        </div>
    </div>
</div>
  
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function(){


    $('#customer_select').on('select2:select', function (e) { 

        if (e.params.data.id === 'all') {

            let allValues = [];

            $('#customer_select option').each(function() {
                if ($(this).val() !== 'all') {
                    allValues.push($(this).val());
                }
            });

            $('#customer_select').val(allValues).trigger('change');
        }

    });
});
$(window).on('load', function () {
    
    $('#customer_select').select2({
        placeholder: "Select Customers",
        allowClear: true,
        closeOnSelect: false,
        width: '100%'
});

});
</script>

