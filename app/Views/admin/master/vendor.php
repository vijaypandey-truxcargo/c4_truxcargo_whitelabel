<script>
function validate(form) {
    return confirm('Do you really want to delete this record ?');
}
</script>

<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

        <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center;">

            <!-- LEFT -->
            <h3 style="margin:0;">All Vendor</h3>

            <!-- RIGHT -->
            <div style="display:flex; align-items:center; gap:10px;">

                <?php if (in_array("Add Vendor", $GLOBALS['permission'])): ?>
                    <?= anchor('admin/vendor/add_vendor', 'Add New Vendor', [
                        'class' => 'btn btn-danger'
                    ]); ?>
                <?php endif; ?>
                <?php if (in_array("Add Vendor", $GLOBALS['permission'])): ?>
                <button id="exportSampleBtn" type="button" class="btn btn-success">
                    Download Sample
                </button>

                <?= form_open_multipart('admin/vendor/import_vendor', [
                    'style' => 'display:flex; align-items:center; gap:10px; margin:0;'
                ]); ?>

                    <input type="file" name="file" class="form-control" style="max-width:200px;">

                    <input type="submit"
                           class="btn btn-success"
                           name="importSubmit"
                           value="IMPORT">
                    <?php endif; ?>
                    <?php if (in_array("Export Vendor", $GLOBALS['permission'])): ?>
                    <button id="exportBtn"
                            type="button"
                            class="btn btn-success">
                        Export All
                    </button>
                     <?php endif; ?>

                <?= form_close(); ?>

            </div>
        </div>

        <div class="clearfix"></div>

        <!-- FLASH -->
        <div class="row">
            <div class="col-lg-12">
                <?php
                $error = $this->session->flashdata('error');
                $error_class = $this->session->flashdata('error_class');

                if ($error): ?>
                    <div class="alert alert-dismissible <?= $error_class; ?>">
                        <strong><?= $error; ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- FILTER -->
        <div class="bs-example4" data-example-id="contextual-table">

            <?= form_open('admin/vendor/index'); ?>

            <div class="row" style="margin-bottom:15px;">

                <!-- Vendor Name -->
                <div class="col-md-3">
                    <label>Vendor Name</label>

                    <select name="name" class="form-control">
                        <option value="">All</option>

                        <?php foreach ($vendor_list as $v): ?>
                            <option value="<?= $v->name ?>"
                                <?= ($selected_name == $v->name) ? 'selected' : '' ?>>
                                <?= $v->name ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <!-- Vendor Code -->
                <div class="col-md-3">
                    <label>Vendor Code</label>

                    <select name="code" class="form-control">
                        <option value="">All</option>

                        <?php foreach ($vendor_list as $v): ?>
                            <option value="<?= $v->code ?>"
                                <?= ($selected_code == $v->code) ? 'selected' : '' ?>>
                                <?= $v->code ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <!-- Status -->
                <div class="col-md-3">
                    <label>Status</label>

                    <select name="status" class="form-control">
                        <option value="All">All</option>

                        <option value="1"
                            <?= ($selected_status=="1")?'selected':'' ?>>
                            Active
                        </option>

                        <option value="0"
                            <?= ($selected_status=="0")?'selected':'' ?>>
                            Inactive
                        </option>
                    </select>
                </div>

                <!-- APPLY -->
                <div class="col-md-3" style="margin-top:23px;">
                    <input type="submit"
                           name="apply"
                           value="Apply"
                           class="btn btn-primary"
                           style="width:100%;">
                </div>

            </div>

            <?= form_close(); ?>

            <div class="clearfix"></div>

            <!-- TABLE -->
            <table class="table table-responsive" id="example">

                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Vendor Name</th>
                        <th>Vendor Code</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($code)): ?>

                        <tr>
                            <td colspan="6"
                                class="text-center"
                                style="font-weight:bold; color:#d00;">
                                No Data Found
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php if (empty($count)) { $count = 0; } ?>

                        <?php foreach ($code as $key): $count++; ?>

                            <tr class="active">

                                <td><?= $count; ?></td>

                                <td><?= $key->name; ?></td>

                                <td><?= $key->code; ?></td>

                                <td>
                                    <span class="<?= $key->status == 1 ? 'text-success' : 'text-danger'; ?>">
                                        <?= $key->status == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>

                                <td><?= $key->created_at; ?></td>

                                <td width="18%">

                                    <?php if (in_array("Edit Vendor", $GLOBALS['permission'])): ?>

                                        <?= anchor(
                                            "admin/vendor/edit_vendor/".$key->id,
                                            'Edit',
                                            ['class'=>'btn btn-primary col-md-4']
                                        ); ?>
                                         <?php endif; ?>
                                         <?php if (in_array("Delete Vendor", $GLOBALS['permission'])): ?>
                                        <?= form_open(
                                            'admin/vendor/delete_vendor',
                                            ['onsubmit'=>'return validate(this);']
                                        ); ?>

                                            <?= form_hidden('id', $key->id); ?>

                                            <?= form_submit([
                                                'name'=>'submit',
                                                'value'=>'Delete',
                                                'class'=>'btn btn-danger col-md-offset-1 col-md-5'
                                            ]); ?>

                                        <?= form_close(); ?>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

            <!-- PAGINATION -->
            <p class="pagination"><?= $links; ?></p>

        </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('exportSampleBtn').addEventListener('click', function () {
        window.location.href =
            "<?= base_url('admin/vendor/export_sample_vendor'); ?>";
    });

    document.getElementById('exportBtn').addEventListener('click', function () {
        window.location.href =
            "<?= base_url('admin/vendor/export_all'); ?>";
    });
</script>