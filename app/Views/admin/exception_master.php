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
            <h3 style="margin:0;">Exception Master</h3>

            <!-- RIGHT SIDE BUTTONS -->
            <div style="display:flex; align-items:center; gap:10px;">

                <?php if (in_array("Add Exception Master", $GLOBALS['permission'])): ?>
                    <?= anchor('admin/exceptionMaster/add', 'Add Exception', [
                        'class' => 'btn btn-danger'
                    ]); ?>
                <?php endif; ?>
                  <?php if (in_array("Import Exception Master", $GLOBALS['permission'])): ?>
                <button id="exportSampleBtn" type="button" class="btn btn-success">
                        Download Sample
                    </button>
                <?= form_open_multipart('admin/exceptionMaster/import', [
                    'style' => 'display:flex; align-items:center; gap:10px; margin:0;'
                ]); ?>

                    <input type="file" name="file" class="form-control" style="max-width:200px;">

                    <input type="submit" class="btn btn-success" name="importSubmit" value="IMPORT">
                     <?php endif; ?>
                       <?php if (in_array("Export Exception Master", $GLOBALS['permission'])): ?>
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

        <div class="bs-example4" data-example-id="contextual-table">

            <?= form_open('admin/exceptionMaster/index'); ?>

            <div class="row" style="margin-bottom:15px;">

                <div class="col-md-3">
                    <input type="text"
                           name="status_code"
                           placeholder="Status Code"
                           class="form-control"
                           value="<?= isset($selected_status_code) ? $selected_status_code : '' ?>">
                </div>

                <div class="col-md-3">
                    <input type="text"
                           name="desc"
                           placeholder="Description"
                           class="form-control"
                           value="<?= isset($selected_desc) ? $selected_desc : '' ?>">
                </div>

                <div class="col-md-3">
                    <select class="form-control" name="is_ndr">

                        <option value="">ALL NDR REPORT</option>

                        <option value="1"
                            <?= ($selected_is_ndr == '1') ? 'selected' : '' ?>>
                            Yes
                        </option>

                        <option value="0"
                            <?= ($selected_is_ndr == '0') ? 'selected' : '' ?>>
                            No
                        </option>

                    </select>
                </div>

                <div class="col-md-2">
                    <input type="submit"
                           name="apply"
                           value="Apply"
                           class="btn btn-primary"
                           style="width:100%;">
                </div>

            </div>

            <?= form_close(); ?>

            <!-- TABLE -->
            <div class="bs-example4" data-example-id="contextual-table">

                <table class="table table-responsive" id="example">

                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>Tracking Event CODE</th>
                            <th>Tracking Event TYPE</th>
                            <th>Tracking Code TYPE</th>
                            <th>Tracking EVENT</th>
                            <th>ADD IN NDR   REPORT</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        if (empty($count)) {
                            $count = 0;
                        }

                        foreach ($code as $key):
                            $count++;
                        ?>

                        <tr class="active">

                            <td><?= $count; ?></td>

                            <td><?= $key->status_code; ?></td>
                            
                            <td><?= $key->status_type; ?></td>
                            
                             <td><?= $key->code_type ?? ''; ?></td>

                            <td><?= $key->desc; ?></td>

                            <td>
                                <?= ($key->is_ndr == 1) ? 'Yes' : 'No'; ?>
                            </td>

                            <td><?= $key->created_at; ?></td>

                            <td width="18%">

                                <?php if (in_array("Edit Exception Master", $GLOBALS['permission'])): ?>

                                    <?= anchor(
                                        "admin/exceptionMaster/edit/{$key->id}",
                                        'Edit',
                                        ['class' => 'btn btn-primary col-md-4']
                                    ); ?>

                                    <?php endif; ?>
                                    
                                     <?php if (in_array("Delete Exception Master", $GLOBALS['permission'])): ?>
                                        
                                        <?= form_open(
                                            'admin/exceptionMaster/delete',
                                            ['onsubmit' => 'return validate(this);']
                                        ); ?>

                                        <?= form_hidden('id', $key->id); ?>

                                        <?= form_submit([
                                            'name'  => 'submit',
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
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) exportBtn.addEventListener('click', function() {
        window.location.href = "<?= base_url('admin/exceptionMaster/export_all'); ?>";
    });

    const exportSampleBtn = document.getElementById('exportSampleBtn');
    if (exportSampleBtn) exportSampleBtn.addEventListener('click', function() {
        window.location.href ="<?= base_url('admin/exceptionMaster/export_sample_exception_master'); ?>"; 
    });
</script>    
