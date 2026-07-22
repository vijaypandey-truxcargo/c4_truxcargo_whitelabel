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
            <h3 style="margin:0;">All State</h3>

            <!-- RIGHT SIDE BUTTONS -->
            <div style="display:flex; align-items:center; gap:10px;">

                <?php if (in_array("Add State", $GLOBALS['permission'])): ?>
                    <?= anchor('admin/state/add_state', 'Add New State', [
                        'class' => 'btn btn-danger'
                    ]); ?>
                <?php endif; ?>
                <?php if (in_array("Import State", $GLOBALS['permission'])): ?>
                <button id="exportSampleBtn" type="button" class="btn btn-success">
                        Download Sample
                    </button>
                <?= form_open_multipart('admin/state/import_state', [
                    'style' => 'display:flex; align-items:center; gap:10px; margin:0;'
                ]); ?>

                    <input type="file" name="file" class="form-control" style="max-width:200px;">

                    <input type="submit" class="btn btn-success" name="importSubmit" value="IMPORT">
                    <?php endif; ?>
                     <?php if (in_array("Export State", $GLOBALS['permission'])): ?>
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
                $error = $this->session->flashdata('error');
                $error_class = $this->session->flashdata('error_class');
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
                            <th>State Name</th>
                            <th>State Code</th>
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
                                <td><?= $key->states; ?></td>
                                <td><?= $key->state_code ?? ''; ?></td>
                                <td>
                                    <span class="<?= $key->status == 1 ? 'text-success' : 'text-danger'; ?>">
                                        <?= $key->status == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>

                                <td width="18%">
                                     <?php if (in_array("Edit State", $GLOBALS['permission'])): ?>

                                        <?= anchor("admin/state/edit_state/{$key->id}", 'Edit', [
                                            'class' => 'btn btn-primary col-md-4'
                                        ]); ?>
                                        <?php endif; ?>
                                        <?php if (in_array("Delete State", $GLOBALS['permission'])): ?>
                                        <?= form_open('admin/state/delete_state', [
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
document.getElementById('exportSampleBtn').addEventListener('click', function() {
    window.location.href = "<?= base_url('admin/state/export_sample_state'); ?>";
});

document.getElementById('exportBtn').addEventListener('click', function() {
    window.location.href = "<?= base_url('admin/state/export_ticket_state'); ?>";
});
</script>
