<script>
function validate(form) {
    return confirm('Do you really want to delete this record ?');
}
</script>

<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

        <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center;">

            <h3 style="margin:0;">All Hub</h3>

            <div style="display:flex; align-items:center; gap:10px;">

                <?php if (in_array("Add Hub", $GLOBALS['permission'])): ?>
                    <?= anchor('admin/master/add_hub', 'New Hub', [
                        'class' => 'btn btn-danger'
                    ]); ?>
                <?php endif; ?>
                <?php if (in_array("Import Hub", $GLOBALS['permission'])): ?>
                <button id="exportSampleBtn" type="button" class="btn btn-success">
                    Download Sample
                </button>

                <?= form_open_multipart('admin/master/import_hub', [
                    'style' => 'display:flex; align-items:center; gap:10px; margin:0;'
                ]); ?>

                    <input type="file" name="file" class="form-control" style="max-width:200px;">

                    <input type="submit"
                           class="btn btn-success"
                           name="importSubmit"
                           value="IMPORT">
                    <?php endif; ?>
                     <?php if (in_array("Export Hub", $GLOBALS['permission'])): ?>
                        <button id="exportBtn" type="button" class="btn btn-success">
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
                $error = session()->getFlashdata('error');
                $error_class = session()->getFlashdata('error_class');

                if ($error): ?>
                    <div class="alert alert-dismissible <?= $error_class; ?>">
                        <strong><?= $error; ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- FILTER -->
        <div class="bs-example4">

            <?= form_open('admin/master/hub_masters'); ?>

            <div class="row" style="margin-bottom:15px;">

                <!-- Code -->
                <div class="col-md-4">
                    <label>Hub Code</label>

                    <select name="code" class="form-control">
                        <option value="">All</option>

                        <?php foreach ($hub_list as $h): ?>
                            <option value="<?= $h->code ?>"
                                <?= ($selected_code == $h->code) ? 'selected' : '' ?>>
                                <?= $h->code ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <!-- Status -->
                <div class="col-md-4">
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

                <!-- Apply -->
                <div class="col-md-4" style="margin-top:23px;">
                    <input type="submit"
                           name="apply"
                           value="Apply"
                           class="btn btn-primary"
                           style="width:100%;">
                </div>

            </div>

            <?= form_close(); ?>

            <table class="table table-responsive" id="example">

                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Contact No.</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($code)): ?>

                        <tr>
                            <td colspan="7"
                                class="text-center"
                                style="font-weight:bold; color:#d00;">
                                No Data Found
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php if (empty($count)) { $count = 0; } ?>

                        <?php foreach ($code as $key): $count++; ?>

                            <tr>

                                <td><?= $count; ?></td>
                                <td><?= $key->name; ?></td>
                                <td><?= $key->code; ?></td>
                                <td><?= $key->phone; ?></td>
                                <td><?= $key->email_id; ?></td>
                                <td><?= $key->created_at; ?></td>

                                <td>
                                    <span class="<?= $key->status == 1 ? 'text-success' : 'text-danger'; ?>">
                                        <?= $key->status == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>

                                <td width="18%">

                                    <?php if (in_array("Edit Hub", $GLOBALS['permission'])): ?>

                                        <?= anchor(
                                            "admin/master/edit_hub/".$key->id,
                                            'Edit',
                                            ['class'=>'btn btn-primary col-md-4']
                                        ); ?>
                                          <?php endif; ?>
                                         <?php if (in_array("Delete Hub", $GLOBALS['permission'])): ?>
                                        <?= form_open(
                                            'admin/master/delete_hub',
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

            <p class="pagination"><?= $links; ?></p>

        </div>

        </div>
    </div>
</div>

<script>
document.getElementById('exportBtn')?.addEventListener('click', function() {
    window.location.href =
        "<?= base_url('admin/master/export_ticket_hub'); ?>";
});

document.getElementById('exportSampleBtn')?.addEventListener('click', function() {
    window.location.href =
        "<?= base_url('admin/master/export_sample_hub'); ?>";
});
</script>
