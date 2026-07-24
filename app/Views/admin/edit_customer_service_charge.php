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

            <h3 class="pull-left">Edit Customer Service Charge</h3>
            <?= anchor('admin/CustomerServiceCharge/','View List',['class'=>'btn btn-success pull-right']);?>

                <div class="clearfix"></div>

                <div class="well1 white form-container">

                                        <?= form_open('admin/CustomerServiceCharge/update/'.$customer_charge->id) ?>
                                            <fieldset>

                                                <!-- CUSTOMER -->
                                                <?php 
                    $selectedCustomers = explode(',', $customer_charge->customer_id);
                    ?>

                    <div class="form-group">
                        <label>Customer <span style="color:red">*</span></label>

                        <select name="customer_id[]" 
                                id="customer_select" 
                                class="form-control" 
                                multiple="multiple" 
                                required>

                            <option></option>
                            <option value="all">Select All</option>

                            <?php foreach($customers as $c): ?>
                                <option value="<?= $c->id ?>"
                                    <?= in_array($c->id, $selectedCustomers) ? 'selected' : '' ?>>
                                    
                                    <?= $c->code ? $c->code.' - ' : '' ?>
                                    <?= $c->first ?> <?= $c->last ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>


                            <!-- SERVICE -->
                            <div class="form-group">
                                <label>Service <span style="color:red">*</span></label>
                                <select name="service" class="form-control" required>
                                    <option value="">Select Anyone</option>
                                    <?php foreach($services as $s): ?>
                                        <option value="<?= $s->id ?>" <?=( $customer_charge->service_id == $s->id) ? 'selected' : '' ?>>
                                            <?= $s->name ?>
                                        </option>
                                        <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- VENDOR -->
                            <div class="form-group">
                                <label>Vendor</label>
                                <select name="vendor" class="form-control">
                                    <option value="">Select Anyone</option>
                                    <?php foreach($vendors as $v): ?>
                                        <option value="<?= $v->id ?>" <?=( $customer_charge->vendor_id == $v->id) ? 'selected' : '' ?>>
                                            <?= $v->name ?>
                                        </option>
                                        <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- MIN CHARGE -->
                            <div class="form-group">
                                <label>Minimum Charge <span style="color:red">*</span></label>
                                <input class="form-control" type="number" step="0.01" name="min_charge" value="<?= $customer_charge->min_charge ?>" required>
                            </div>

                            <!-- DIVISOR -->
                            <div class="form-group">
                                <label>Divisor <span style="color:red">*</span></label>
                                <input class="form-control" type="text" maxlength="5" name="divisor" value="<?= $customer_charge->divisor ?>" required>
                            </div>

                            <!-- MIN WEIGHT -->
                            <div class="form-group">
                                <label>Minimum Weight <span style="color:red">*</span></label>
                                <input class="form-control" type="number" step="0.01" name="min_weight" value="<?= $customer_charge->min_weight ?>" required>
                            </div>

                             <!-- FUEL PERCENTAGE -->
                            <div class="form-group">
                                <label>Fuel Percentage <span style="color:red">*</span></label>
                                <input class="form-control" type="number" step="0.01" name="fuel_percent" value="<?= $customer_charge->fuel_percent ?>" required>
                            </div>

                            <!-- STATUS -->
                            <div class="form-group">
                                <label>Status</label>
                                <div>
                                    <label class="radio-inline" style="margin-right:20px;">
                                        <input name="status" type="radio" value="1" <?=( $customer_charge->status == 1) ? 'checked' : '' ?>> Active
                                    </label>

                                    <label class="radio-inline">
                                        <input name="status" type="radio" value="0" <?=( $customer_charge->status == 0) ? 'checked' : '' ?>> Inactive
                                    </label>
                                </div>
                            </div>

                            <!-- BUTTON -->
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update</button>
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
