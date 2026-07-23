<?php
$session = session();
$permissions = $permission ?? ($GLOBALS['permission'] ?? []);
$canManage = in_array('Add Pincode', $permissions, true);
$count = (int) ($count ?? 0);
$rows = $code ?? [];
$selectedPincode = $selected_pincode ?? '';
$error = $session->getFlashdata('error');
$errorClass = $session->getFlashdata('error_class') ?? 'alert-info';
?>

<script>
function validateBluedartDelete() {
    return confirm('Do you really want to delete this record ?');
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
                <h3 class="pull-left"><?= esc($page_title ?? 'Bluedart Pincodes') ?></h3>
                <button type="button" class="btn btn-success" onclick="window.location.href='<?= esc(base_url('admin/pincode/export_bluedart'), 'attr') ?>'">
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
                <div class="row" style="margin:20px 0 40px">
                    <div class="col-md-6">
                        <label>Import Air(Apex) Pincode</label>
                        <?= form_open_multipart('admin/pincode/import_bluedart'); ?>
                            <div class="pincode-import-row">
                                <input type="file" name="file" accept=".csv" required>
                                <input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
                            </div>
                        <?= form_close(); ?>
                    </div>

                    <div class="col-md-6">
                        <label>Import Surface Pincode</label>
                        <?= form_open_multipart('admin/pincode/surface_bluedart'); ?>
                            <div class="pincode-import-row">
                                <input type="file" name="file" accept=".csv" required>
                                <input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
                            </div>
                        <?= form_close(); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Sample Format</label>
                    <div class="pincode-sample-scroll">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Pincode</th>
                                    <th>State</th>
                                    <th>ODA</th>
                                    <th>Code</th>
                                    <th>Code1</th>
                                    <th>Distance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>110085</td>
                                    <td>Delhi</td>
                                    <td>NO</td>
                                    <td>DEL</td>
                                    <td>DEL</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td>382715</td>
                                    <td>Gujarat</td>
                                    <td>EDL</td>
                                    <td>GUJ</td>
                                    <td>GUJ</td>
                                    <td>112</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bs-example4" data-example-id="contextual-table">
                <div class="clearfix"></div>

                <?= form_open('admin/pincode/bluedart', ['method' => 'get']); ?>
                    <div class="col-lg-4">
                        <input
                            type="text"
                            name="pincode"
                            value="<?= esc($selectedPincode, 'attr') ?>"
                            placeholder="Search By Pincode"
                            class="form-control"
                            required
                        >
                    </div>
                    <div class="col-lg-4">
                        <input type="submit" name="submit" value="Search" class="btn btn-danger">
                        <?php if ($selectedPincode !== ''): ?>
                            <a href="<?= esc(base_url('admin/pincode/bluedart'), 'attr') ?>" class="btn btn-default">Clear</a>
                        <?php endif; ?>
                    </div>
                <?= form_close(); ?>

                <div class="clearfix" style="margin-bottom:15px;"></div>

                <div class="pincode-table-scroll">
                    <table class="table table-responsive" id="example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pincode</th>
                                <th>State</th>
                                <th>SFC</th>
                                <th>AIR</th>
                                <th>ODA SFC</th>
                                <th>ODA AIR</th>
                                <?php if ($canManage): ?>
                                    <th>Action</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rows)): ?>
                                <tr class="active">
                                    <td colspan="<?= esc((string) ($canManage ? 8 : 7), 'attr') ?>" class="text-center">No records found.</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($rows as $row): $count++; ?>
                                <tr class="active">
                                    <td><?= esc((string) $count) ?></td>
                                    <td><?= esc($row->pincode ?? '') ?></td>
                                    <td><?= esc($row->state ?? '') ?></td>
                                    <td><?= esc($row->sfc ?? '') ?></td>
                                    <td><?= esc($row->air ?? '') ?></td>
                                    <td><?= esc($row->oda_sfc ?? '') ?></td>
                                    <td><?= esc($row->oda_air ?? '') ?></td>
                                    <?php if ($canManage): ?>
                                        <td width="18%">
                                            <?= form_open('admin/pincode/delete_bluedart', ['onsubmit' => 'return validateBluedartDelete();']) ?>
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

                <p class="pagination"><?= $links ?? '' ?></p>
            </div>
        </div>
    </div>
</div>
