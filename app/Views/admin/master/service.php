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
            <h3 style="margin:0;">All Service</h3>

            <!-- RIGHT -->
            <div style="display:flex; align-items:center; gap:10px;">

                <?php if (in_array("Add Service", $GLOBALS['permission'])): ?>

                    <?= anchor('admin/service/add_service', 'New Service', [
                        'class' => 'btn btn-danger'
                    ]); ?>

                <?php endif; ?>
                <?php if (in_array("Add Service", $GLOBALS['permission'])): ?>
                <button id="exportSampleBtn"
                        type="button"
                        class="btn btn-success">
                    Download Sample
                </button>

                <?= form_open_multipart('admin/service/import_service', [
                    'style' => 'display:flex; align-items:center; gap:10px; margin:0;'
                ]); ?>

                    <input type="file" name="file" class="form-control" style="max-width:200px;">

                    <input type="submit" class="btn btn-success" name="importSubmit" value="IMPORT">

                    <?php endif; ?>
                     <?php if (in_array("Export Service", $GLOBALS['permission'])): ?>
                        <button id="exportBtn" type="button" class="btn btn-success"> Export All </button>
                    <?php endif; ?>
                <?= form_close(); ?>

            </div>
        </div>

        <div class="clearfix"></div>

        <!-- FLASH MESSAGE -->
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
        <div class="bs-example4">

            <?= form_open('admin/service'); ?>

            <div class="row" style="margin-bottom:15px;">

                <div class="col-md-4">

                    <label>Service Name</label>

                    <select name="name" class="form-control">

                        <option value="">All</option>

                        <?php foreach ($key as $k): ?>

                            <option value="<?= $k->name ?>"
                                <?= ($selected_name == $k->name) ? 'selected' : '' ?>>

                                <?= $k->name ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- SERVICE CODE -->
                <div class="col-md-3">

                    <label>Service Code</label>

                    <select name="code" class="form-control">

                        <option value="">All</option>

                        <?php foreach ($key as $k): ?>

                            <option value="<?= $k->code ?>"
                                <?= ($selected_code == $k->code) ? 'selected' : '' ?>>

                                <?= $k->code ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- STATUS -->
                <div class="col-md-3">

                    <label>Status</label>

                    <select name="status" class="form-control">

                        <option value="All">All</option>

                        <option value="1"
                            <?= ($selected_status == "1") ? 'selected' : '' ?>>

                            Active

                        </option>

                        <option value="0"
                            <?= ($selected_status == "0") ? 'selected' : '' ?>>

                            Inactive

                        </option>

                    </select>

                </div>

                <!-- APPLY -->
                <div class="col-md-2" style="margin-top:23px;">

                    <input type="submit"
                           name="apply"
                           value="Apply"
                           class="btn btn-primary"
                           style="width:100%;">

                </div>

            </div>

            <?= form_close(); ?>

            <!-- TABLE -->
            <table class="table table-responsive" id="example">

                <thead>

                    <tr>

                        <th>Sr.No.</th>
                        <th>Service Name</th>
                        <th>Service Code</th>
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

                        <?php foreach ($code as $row): $count++; ?>

                            <tr class="active">

                                <td><?= $count; ?></td>

                                <td><?= $row->name; ?></td>

                                <td><?= $row->code; ?></td>

                                <td>

                                    <span class="<?= $row->status == 1 ? 'text-success' : 'text-danger'; ?>">

                                        <?= $row->status == 1 ? 'Active' : 'Inactive'; ?>

                                    </span>

                                </td>

                                <td><?= $row->created_at; ?></td>

                                <td width="18%">

                                     <?php if (in_array("Edit Service", $GLOBALS['permission'])): ?>

                                        <?= anchor(
                                            "admin/service/edit_service/".$row->id,
                                            'Edit',
                                            ['class'=>'btn btn-primary col-md-4']
                                        ); ?>
                                        <?php endif; ?>
                                        <?php if (in_array("Delete Service", $GLOBALS['permission'])): ?>
                                        <?= form_open(
                                            'admin/service/delete_service',
                                            ['onsubmit'=>'return validate(this);']
                                        ); ?>

                                            <?= form_hidden('id', $row->id); ?>

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

            <p class="pagination"><?= $links; ?></p>

        </div>

        </div>
    </div>
</div>

<script>

document.getElementById('exportSampleBtn').addEventListener('click', function() {

    window.location.href =
        "<?= base_url('admin/service/export_sample_service'); ?>";

});

document.getElementById('exportBtn').addEventListener('click', function() {

    window.location.href =
        "<?= base_url('admin/service/export_ticket_sample'); ?>";

});

</script>