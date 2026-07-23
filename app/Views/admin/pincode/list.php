<?php
$session = session();
$permissions = $permission ?? ($GLOBALS['permission'] ?? []);
$canManage = in_array('Add Pincode', $permissions, true);
$count = (int) ($count ?? 0);
$rows = $code ?? [];
$selectedPincode = $selected_pincode ?? '';
$error = $session->getFlashdata('error');
$errorClass = $session->getFlashdata('error_class') ?? 'alert-info';
$deleteConfirm = $delete_confirm ?? 'Do you really want to delete this record ?';
?>

<script>
function validatePincodeDelete() {
    return confirm(<?= json_encode($deleteConfirm) ?>);
}
</script>

<style>
.pincode-import-row {
    align-items: center;
    display: flex;
    gap: 12px;
    margin: 8px 0 18px;
}
.pincode-import-row input[type="file"] {
    max-width: 100%;
}
.pincode-sample-shell {
    clear: both;
    max-width: 100%;
    overflow: hidden;
    position: relative;
    z-index: 1;
}
.pincode-sample-scroll,
.pincode-table-scroll {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 5px;
}
.pincode-sample-scroll table,
.pincode-table-scroll table {
    margin-bottom: 0;
    min-width: max-content;
    width: 100%;
}
.pincode-sample-scroll th,
.pincode-sample-scroll td,
.pincode-table-scroll th,
.pincode-table-scroll td {
    padding: 9px 12px !important;
    vertical-align: top !important;
    white-space: nowrap;
}
@media (max-width: 767px) {
    .pincode-import-row {
        align-items: stretch;
        flex-direction: column;
    }
}
</style>

<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">
            <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center; gap:10px;">
                <h3 class="pull-left"><?= esc($page_title ?? 'Pincodes') ?></h3>
                <button type="button" class="btn btn-success" onclick="window.location.href='<?= esc(base_url($export_route), 'attr') ?>'">
                    Export All
                </button>
            </div>

            <div class="clearfix"></div>

            <?php if ($error): ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-dismissible <?= esc($errorClass, 'attr') ?>">
                            <strong><?= nl2br(esc($error)) ?></strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($canManage): ?>
                <div class="col-md-12" id="importFrm1" style="margin:20px 0 40px">
                    <label><?= esc($import_title ?? 'Import Pincode') ?></label>

                    <div class="col-md-12">
                        <?= form_open_multipart($import_route); ?>
                            <div class="pincode-import-row">
                                <input type="file" name="file" accept=".csv" required>
                                <input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
                            </div>
                        <?= form_close(); ?>
                    </div>

                    <div class="clearfix"></div>

                    <div class="form-group" style="margin-top:20px;">
                        <label class="control-label"><?= esc($sample_title ?? 'Sample Format') ?></label>
                        <div class="pincode-sample-shell">
                            <div class="pincode-sample-scroll">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <?php foreach (($sample_headers ?? []) as $header): ?>
                                                <th><?= esc($header) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($sample_rows ?? []) as $sampleRow): ?>
                                            <tr>
                                                <?php foreach ($sampleRow as $sampleCell): ?>
                                                    <td><?= esc((string) $sampleCell) ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bs-example4" data-example-id="contextual-table">
                <div class="clearfix"></div>

                <?= form_open($search_route, ['method' => 'get']); ?>
                    <div class="col-lg-4">
                        <input
                            type="text"
                            name="pincode"
                            value="<?= esc($selectedPincode, 'attr') ?>"
                            placeholder="<?= esc($search_placeholder ?? 'Search By Pincode', 'attr') ?>"
                            class="form-control"
                            required
                        >
                    </div>
                    <div class="col-lg-4">
                        <input type="submit" name="submit" value="Search" class="btn btn-danger">
                        <?php if ($selectedPincode !== ''): ?>
                            <a href="<?= esc(base_url($search_route), 'attr') ?>" class="btn btn-default">Clear</a>
                        <?php endif; ?>
                    </div>
                <?= form_close(); ?>

                <div class="clearfix" style="margin-bottom:15px;"></div>

                <div class="pincode-sample-shell">
                    <div class="pincode-table-scroll">
                    <table class="table" id="example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <?php foreach (($columns ?? []) as $column): ?>
                                    <th><?= esc($column['label']) ?></th>
                                <?php endforeach; ?>
                                <?php if ($canManage): ?>
                                    <th style="width:80px;">Action</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rows)): ?>
                                <tr class="active">
                                    <td colspan="<?= esc((string) (count($columns ?? []) + ($canManage ? 2 : 1)), 'attr') ?>" class="text-center">
                                        No records found.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($rows as $row): $count++; ?>
                                <tr class="active">
                                    <td><?= esc((string) $count) ?></td>
                                    <?php foreach (($columns ?? []) as $column): ?>
                                        <?php
                                            $value = $column['value'] ?? '';
                                            $cell = is_callable($value) ? $value($row) : ($row->{$value} ?? '');
                                        ?>
                                        <td><?= esc((string) $cell) ?></td>
                                    <?php endforeach; ?>
                                    <?php if ($canManage): ?>
                                        <td style="width:80px; text-align:center;">
                                            <?= form_open($delete_route, ['onsubmit' => 'return validatePincodeDelete();']) ?>
                                                <?= form_hidden('id', $row->id ?? '') ?>
                                                <?= form_submit(['name' => 'submit', 'value' => 'X', 'class' => 'btn btn-danger']) ?>
                                            <?= form_close() ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>

                <p class="pagination"><?= $links ?? '' ?></p>
            </div>
        </div>
    </div>
</div>
