<script>
function validate(form) {
    return confirm('Do you really want to delete this record ?');
}
</script>

<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

        <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center;">

            <!-- LEFT: TITLE -->
            <h3 style="margin:0;">All Comapny</h3>

            <!-- RIGHT SIDE BUTTONS -->
            <div style="display:flex; align-items:center; gap:10px;">

                <?php if (in_array("Add Company", $GLOBALS['permission'])): ?>
                    <?= anchor('admin/company/add_company', 'New Company', [
                        'class' => 'btn btn-danger'
                    ]); ?>
                <?php endif; ?>
                <?php if (in_array("Import Company", $GLOBALS['permission'])): ?>
                <button id="exportSampleBtn" type="button" class="btn btn-success">
                        Download Sample
                    </button>
                <?= form_open_multipart('admin/company/import_company', [
                    'style' => 'display:flex; align-items:center; gap:10px; margin:0;'
                ]); ?>

                    <input type="file" name="file" class="form-control" style="max-width:200px;">

                    <input type="submit" class="btn btn-success" name="importSubmit" value="IMPORT">
                    <?php endif; ?>
                     <?php if (in_array("Export Company", $GLOBALS['permission'])): ?>
                        <button id="exportBtn" type="button" class="btn btn-success">
                            Export All
                        </button>
                    <?php endif; ?>
                <?= form_close(); ?>

            </div>
        </div>
        <div class="clearfix"></div>
        <!-- ERROR MESSAGE -->
        <div class="row">
            <div class="col-lg-12">
                <?php
                $error = session()->getFlashdata('error');
                $error_class = session()->getFlashdata('error_class');
                if ($error): ?>
                    <div class="alert alert-dismissible <?= $error_class; ?>">
                        <strong><?= $error; ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

            <!-- TABLE -->
            <div class="bs-example4" data-example-id="contextual-table">
                <table class="table table-responsive" id="example">
                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>Code</th>
                             <th>Name</th>
                            <th>Contact NO.</th>
                            <th>Email ID</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (empty($count)) { $count = 0; }
                        foreach ($code as $key): $count++; ?>
                            <tr class="active">
                                <td><?= $count; ?></td>
                                <td><?= $key->code; ?></td>
                                <td><?= $key->name; ?></td>
                                <td><?= $key->phone; ?></td>
                                <td><?= $key->email; ?></td>
                                <td><?= $key->created_at; ?></td>
                                <td>
                                    <span class="<?= $key->status == 1 ? 'text-success' : 'text-danger'; ?>">
                                        <?= $key->status == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>

                                <td width="18%">
                                    <?php if (in_array("Edit Company", $GLOBALS['permission'])): ?>

                                        <?= anchor("admin/company/edit_company/{$key->id}", 'Edit', [
                                            'class' => 'btn btn-primary col-md-4'
                                        ]); ?>
                                        <?php endif; ?>
                                         <?php if (in_array("Delete Company", $GLOBALS['permission'])): ?>
                                        <?= form_open('admin/company/delete_company', [
                                            'onsubmit' => 'return validate(this);'
                                        ]); ?>
                                        
                                            <?= form_hidden('id', $key->id); ?>
                                            <?= form_submit([
                                                'name' => 'submit',
                                                'value' => 'Delete',
                                                'class' => 'btn btn-danger col-md-offset-1 col-md-5'
                                            ]); ?>
                                        <?= form_close(); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>

                <p class="pagination"><?= $links; ?></p>

            </div>

        </div>
    </div>
</div>


<script>
document.getElementById('exportSampleBtn')?.addEventListener('click', function() {
    window.location.href = "<?= base_url('admin/company/export_sample_company'); ?>";
});

document.getElementById('exportBtn')?.addEventListener('click', function() {
    window.location.href = "<?= base_url('admin/company/export_ticket_company'); ?>";
});
</script>
