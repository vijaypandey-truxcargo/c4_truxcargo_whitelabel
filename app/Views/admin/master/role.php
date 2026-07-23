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
            <h3 style="margin:0;">All Role</h3>

            <!-- RIGHT SIDE BUTTONS -->
            <div style="display:flex; align-items:center; gap:10px;">

                <?php if (in_array("Add Role", $GLOBALS['permission'])): ?>
                    <?= anchor('admin/role/add_role', 'New Role', [
                        'class' => 'btn btn-danger'
                    ]); ?>
                <?php endif; ?>
                <?php if (in_array("Import Role", $GLOBALS['permission'])): ?>
                <button id="exportSampleBtn" type="button" class="btn btn-success">
                        Download Sample
                    </button>
                    
                <?= form_open_multipart('admin/role/import_role', [
                    'style' => 'display:flex; align-items:center; gap:10px; margin:0;'
                ]); ?>

                    <input type="file" name="file" class="form-control" style="max-width:200px;">

                    <input type="submit" class="btn btn-success" name="importSubmit" value="IMPORT">
                    <?php endif; ?>
                    <?php if (in_array("Export Role", $GLOBALS['permission'])): ?>
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
                             <th>Roll Name</th>
                             <th>Parent Role</th>
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
                                <td><?= $key->name; ?></td>
                                <td>
                                    <?php

                                        if(!empty($key->parent_id)){

                                           echo $role_names[$key->parent_id] ?? '-';

                                        }else{

                                            echo 'TOP LEVEL';
                                        }

                                        ?>
                                    </td>
                                <td><?= $key->created_at; ?></td>
                                <td>
                                    <span class="<?= $key->status == 1 ? 'text-success' : 'text-danger'; ?>">
                                        <?= $key->status == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>

                                <td width="18%">
                                    <?php if (in_array("Edit Role", $GLOBALS['permission'])): ?>

                                        <?= anchor("admin/role/edit_role/{$key->id}", 'Edit', [
                                            'class' => 'btn btn-primary col-md-4'
                                        ]); ?>
                                         <?php endif; ?>
                                        <?php if (in_array("Delete Role", $GLOBALS['permission'])): ?>
                                        <?= form_open('admin/role/delete_role', [
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
    window.location.href = "<?= base_url('admin/role/export_sample_role'); ?>";
});

document.getElementById('exportBtn')?.addEventListener('click', function() {
    window.location.href = "<?= base_url('admin/role/export_ticket_role'); ?>";
});
</script>
