<script>
function validate(form) {
    return confirm('Do you really want to delete this record ?');
}
</script>

<style>
div.dt-buttons {
    margin-top: 20px !important;
}
</style>

<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <!-- TITLE + BUTTONS SECTION -->
            <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0;">All User</h3>

                <div style="display:flex; align-items:center; gap:10px;">
                    <?php if (in_array("Add Access Control", $GLOBALS['permission'])): ?>
                        <?= anchor('admin/users/add', 'Add User', ['class' => 'btn btn-danger']); ?>
                    <?php endif; ?>

                    <button id="exportBtn" type="button" class="btn btn-success">Export All</button>
                </div>
            </div>

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

            <div class="clearfix"></div>

            <!-- FILTER SECTION -->
            <div class="bs-example4" data-example-id="contextual-table">

                <?= form_open('admin/users/index'); ?>
                <div class="row" style="margin-bottom:15px;">

    <div class="col-md-3">
        <input type="text" class="form-control" name="username"
               placeholder="Name"
               value="<?= $data['selected_name'] ?? '' ?>">
    </div>

    <div class="col-md-3">
        <input type="text" class="form-control" name="email" placeholder="Email" value="<?= $data['selected_email'] ?? '' ?>">
    </div>

    <div class="col-md-2">
            <select class="form-control" name="hub">
                <option value="">All Hubs</option>
                <?php foreach($hub_list as $h): ?>
                    <option value="<?= $h->id ?>"
                        <?= ($data['selected_hub'] == $h->id) ? "selected" : "" ?>>
                        <?= $h->name ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-control" name="role">
                <option value="All">All Roles</option>
                <?php foreach($all_role as $r_name){ ?>
                    <option value="<?= $r_name->id ?>"
                        <?= ($data['selected_role'] == $r_name->id) ? "selected" : "" ?>>
                        <?= $r_name->name ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-2">
            <input type="submit" name="apply" value="Apply"
                class="btn btn-primary" style="width:100%;">
        </div>

    </div>

                <?= form_close(); ?>

                <!-- USER TABLE -->
                <table class="table table-responsive" id="example">
                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (empty($count)) { $count = 0; }
                        foreach ($data['code'] as $key): $count++; ?>
                            <tr class="active">
                                <td><?= $count; ?></td>
                                <td><?= $key->userName; ?></td>
                                <td><?= $key->userEmail; ?></td>
                                <td><?= $key->userPhone; ?></td>
                                <td><?= $roles[$key->role] ?? 'N/A'; ?></td>

                                <td>
                                    <span class="<?= $key->status == 1 ? 'text-success' : 'text-danger'; ?>">
                                        <?= $key->status == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>

                                <td width="18%">
                                    <?php if (in_array("Add Access Control", $GLOBALS['permission'])): ?>

                                        <?= anchor("admin/users/edit_user/{$key->id}", 'Edit', [
                                            'class' => 'btn btn-primary col-md-4'
                                        ]); ?>

                                        <?= form_open('admin/users/delete', [
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

                <p class="pagination"><?= $data['links']; ?></p>

            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('exportBtn').addEventListener('click', function() {
    window.location.href = "<?= base_url('admin/users/user_export'); ?>";
});
</script>
