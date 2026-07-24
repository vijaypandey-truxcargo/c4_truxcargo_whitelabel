<?php
$isEdit = ! empty($row);
$value = static fn (string $field, $default = '') => old($field, $isEdit ? ($row->{$field} ?? $default) : $default);
$servicesSelected = array_filter(explode(',', (string) $value('service_id')));
$customersSelected = array_filter(explode(',', (string) $value('customer_id')));
$vendorsSelected = array_filter(explode(',', (string) $value('vendor_id')));
?>
<div id="page-wrapper">
<div class="graphs"><div class="xs">
    <h3 class="pull-left"><?= $isEdit ? 'Edit' : 'Add' ?> Rate Modifier</h3>
    <?= anchor('admin/RateModifier', 'View Rate Modifier', ['class' => 'btn btn-success pull-right']) ?>
    <div class="clearfix"></div>

    <?php if ($error = session()->getFlashdata('error')): ?>
        <div class="alert <?= esc(session()->getFlashdata('error_class') ?: 'alert-danger') ?>"><?= esc($error) ?></div>
    <?php endif; ?>

    <div class="well1 white">
    <?= form_open($isEdit ? 'admin/RateModifier/update/' . $row->id : 'admin/RateModifier/insert') ?>
        <div class="row">
            <div class="col-md-6">
                <label>Charge <span class="text-danger">*</span></label>
                <select name="charge_id" class="form-control select2" required>
                    <option value="">Select Charge</option>
                    <?php foreach ($charge_master as $item): ?>
                        <option value="<?= $item->id ?>" <?= (string) $value('charge_id') === (string) $item->id ? 'selected' : '' ?>><?= esc($item->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label>Billing Type <span class="text-danger">*</span></label>
                <select name="billing_type" class="form-control" required>
                    <option value="sale" <?= strtolower((string)$value('billing_type', 'sale')) === 'sale' ? 'selected' : '' ?>>SALE</option>
                    <option value="purchase" <?= strtolower((string)$value('billing_type')) === 'purchase' ? 'selected' : '' ?>>PURCHASE</option>
                </select>
            </div>
        </div>
        <div class="row" style="margin-top:20px">
            <div class="col-md-4"><label>Fixed Amount</label><input type="number" step="0.01" min="0" name="fixed_amount" class="form-control" value="<?= esc($value('fixed_amount', 0)) ?>"></div>
            <div class="col-md-4"><label>Min Amount</label><input type="number" step="0.01" min="0" name="min_amount" class="form-control" value="<?= esc($value('min_amount', 0)) ?>"></div>
            <div class="col-md-4">
                <label>Rate Per <span class="text-danger">*</span></label>
                <select name="rate_mod" class="form-control select2" required>
                    <option value="">Select</option>
                    <?php foreach ($rates as $id => $name): ?><option value="<?= $id ?>" <?= (string)$value('rate_mod') === (string)$id ? 'selected' : '' ?>><?= esc($name) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row" style="margin-top:20px">
            <div class="col-md-4">
                <label>Services</label>
                <select name="service_id[]" class="form-control select2" multiple>
                    <option value="all">Select All</option>
                    <?php foreach ($services as $item): ?><option value="<?= $item->id ?>" <?= in_array((string)$item->id, $servicesSelected, true) ? 'selected' : '' ?>><?= esc(strtoupper($item->name)) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Customers</label>
                <select name="customer_id[]" class="form-control select2" multiple>
                    <option value="all">Select All</option>
                    <?php foreach ($customers as $item): ?><option value="<?= $item->id ?>" <?= in_array((string)$item->id, $customersSelected, true) ? 'selected' : '' ?>><?= esc(strtoupper(($item->code ?? '') . ' - ' . ($item->userName ?? ''))) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Vendors</label>
                <select name="vendor_id[]" class="form-control select2" multiple>
                    <option value="all">Select All</option>
                    <?php foreach ($vendors as $item): ?><option value="<?= $item->id ?>" <?= in_array((string)$item->id, $vendorsSelected, true) ? 'selected' : '' ?>><?= esc(strtoupper($item->name)) ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row" style="margin-top:20px">
            <div class="col-md-4"><label>Effective From <span class="text-danger">*</span></label><input type="date" name="effective_from" class="form-control" value="<?= esc($value('effective_from')) ?>" required></div>
            <div class="col-md-4"><label>Effective To</label><input type="date" name="effective_to" class="form-control" value="<?= esc($value('effective_to')) ?>"></div>
            <div class="col-md-4">
                <label>Status</label><br>
                <label><input type="radio" name="status" value="1" <?= (string)$value('status', 1) === '1' ? 'checked' : '' ?>> Active</label>&nbsp;&nbsp;
                <label><input type="radio" name="status" value="0" <?= (string)$value('status', 1) === '0' ? 'checked' : '' ?>> Inactive</label>
            </div>
        </div>
        <br><button type="submit" class="btn btn-success"><?= $isEdit ? 'Update' : 'Save' ?></button>
    <?= form_close() ?>
    </div>
</div></div>
</div>
<script>
$(function () {
    $('.select2').select2({allowClear: true, closeOnSelect: false});
    $('.select2').on('select2:select', function (e) {
        if (e.params.data.id !== 'all') return;
        const values = $(this).find('option').map(function () { return this.value && this.value !== 'all' ? this.value : null; }).get();
        $(this).val(values).trigger('change');
    });
});
</script>
