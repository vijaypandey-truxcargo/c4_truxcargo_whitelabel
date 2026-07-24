<script>
function validate(form) {
    return confirm('Do you really want to delete this record ?');
}
</script>
<style>
.action-buttons {
    gap: 8px;
    align-items: center;
}

.action-buttons form.deleteForm {
    display: inline-block;
    margin: 0;
}
.modal-header .close {
    margin-top: -27px;
    width: 20px;
    color: red;
    font-size: 31px;
}

</style>


<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

        <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center;">

            <!-- LEFT TITLE -->
            <h3 style="margin:0;">Rate Modifier List</h3>

            <!-- RIGHT BUTTONS -->
            <div style="display:flex; align-items:center; gap:10px;">
                <?php if (in_array("Add Rate Modifier", $GLOBALS['permission'])): ?>

                    <?= anchor('admin/RateModifier/add', 'New Rate Modifier', ['class'=>'btn btn-danger']); ?>
                <?php endif; ?>

                <?php if (in_array("Import Rate Modifier", $GLOBALS['permission'])): ?>

                <!-- Download Sample -->
                <button id="exportSampleBtn" class="btn btn-success">Download Sample</button>

                <!-- Import File -->
                <?= form_open_multipart('admin/RateModifier/import', ['style'=>'display:flex; align-items:center; gap:10px; margin:0;']); ?>

                    <input type="file" name="file" class="form-control" style="max-width:200px;">
                    <input type="submit" name="importSubmit" class="btn btn-success" value="IMPORT">
                    <?php endif; ?>
                    <?php if (in_array("Export Rate Modifier", $GLOBALS['permission'])): ?>
                    <button id="exportBtn" type="button" class="btn btn-success">Export All</button>
                    <?php endif; ?>
                <?= form_close(); ?>
            </div>
        </div>

        <!-- FLASH ERROR -->
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

        <!-- FILTERS -->
        <div class="bs-example4" data-example-id="contextual-table">

            <?= form_open('admin/RateModifier/index'); ?>
            <div class="row" style="margin-bottom:15px;">

                <div class="col-md-3">
                    <label>Service</label>
                    <select name="service" class="form-control">
                        <option value="">All</option>
                        <?php foreach ($service_list as $s): ?>
                            <option value="<?= $s->id ?>"
                                <?= ($selected_service == $s->id) ? 'selected' : '' ?>>
                                <?= $s->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>    
                <!-- CHARGE NAME -->
                <div class="col-md-3">
                    <label>Charge Name</label>
                    <select name="charge_id" class="form-control">
                        <option value="">All</option>
                        <?php foreach ($charge_list as $c): ?>
                            <option value="<?= $c->id ?>"
                                <?= ($selected_charge == $c->id) ? 'selected' : '' ?>>
                                <?= $c->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- BILLING TYPE -->
                <div class="col-md-2">
                    <label>Billing Type</label>
                    <select name="billing_type" class="form-control">
                        <option value="All">All</option>
                        <option value="SALE"     <?= ($selected_billing=="SALE")?'selected':'' ?>>SALE</option>
                        <option value="PURCHASE" <?= ($selected_billing=="PURCHASE")?'selected':'' ?>>PURCHASE</option>
                    </select>
                </div>

                <!-- STATUS -->
                <div class="col-md-2">
                    <label>status</label>
                    <select name="status" class="form-control">
                        <option value="All">All</option>
                        <option value="1" <?= ($selected_status=="1")?'selected':'' ?>>Active</option>
                        <option value="0" <?= ($selected_status=="0")?'selected':'' ?>>Inactive</option>
                    </select>
                </div>


            <!-- APPLY BUTTON -->

                <div class="col-md-2" style=" margin-top: 23px; ">
                    <input type="submit" name="apply" value="Apply" class="btn btn-primary" style="width:100%;">
                </div>


            <?= form_close(); ?>
                        </div>
        <div class="clearfix"></div> <div class="clearfix"></div>
            <div class="bs-example4" data-example-id="contextual-table">
            <!-- TABLE -->
            <table class="table table-responsive table-bordered" id="example">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Charge Name</th>
                        <th>Billing Type</th>
                        <th>Fixed Amount</th>
                        <th>Min Amount</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Effective From</th>
                        <th>Effective To</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(empty($code)): ?>
                        <tr>
                            <td colspan="10" class="text-center" style="font-weight:bold; color:#d00;">
                                No Data Found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php if(empty($count)) { $count=0; } ?>
                        <?php foreach($code as $row): $count++; ?>
                            <tr>
                                <td><?= $count; ?></td>
                                <td> <?= isset($charge_map[$row->charge_id]) ? $charge_map[$row->charge_id] : 'N/A'; ?></td>
                                <td><?= $row->billing_type; ?></td>
                                <td><?= $row->fixed_amount; ?></td>
                                <td><?= $row->min_amount; ?></td>
                                <td>
                                    <?php
                                    if (!empty($row->service_id)) {

                                        $service_ids = explode(',', $row->service_id);
                                        $service_names = [];

                                        foreach ($service_ids as $sid) {
                                            if (isset($service_map[$sid])) {
                                                $service_names[] = $service_map[$sid];
                                            }
                                        }

                                        $total = count($service_names);

                                        if ($total > 3) {

                                            echo implode(', ', array_slice($service_names, 0, 2));

                                            echo '<span class="extra-services d-none">';
                                            echo ', ' . implode(', ', array_slice($service_names, 2));
                                            echo '</span>';

                                            echo ' <a href="javascript:void(0);" class="show-more-service text-primary"> + More</a>';

                                        } else {
                                            echo implode(', ', $service_names);
                                        }

                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                    </td>
                                <td>    
                                    <span class="<?= $row->status==1?'text-success':'text-danger'; ?>">
                                        <?= $row->status==1 ? "Active" : "Inactive"; ?>
                                    </span>
                                </td>

                                <td><?= $row->effective_from; ?></td>
                                <td><?= $row->effective_to; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-info btn-sm viewBtn" data-id="<?= $row->id ?>">View</button>
                                          <?php if (in_array("Edit Rate Modifier", $GLOBALS['permission'])): ?>
                                        <?= anchor("admin/RateModifier/edit/".$row->id,'Edit', ['class'=>'btn btn-primary btn-sm']); ?>
                                        <?php endif; ?>
                                            <?php if (in_array("Delete Rate Modifier", $GLOBALS['permission'])): ?>
                                        <?= form_open('admin/RateModifier/delete', ['onsubmit'=>'return validate(this);', 'class'=>'deleteForm']); ?>
                                            <input type="hidden" name="id" value="<?= $row->id ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        <?= form_close(); ?>
                                        <?php endif; ?>
                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
            <!-- PAGINATION -->  <p class="pagination"><?= $links; ?></p>

        </div>

        </div>
    </div>
 </div>

 <!-- AJAX VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" style="color: blue;">Rate Modifier Details</h5>
        <button type="button" class="close" data-dismiss="modal" style="">&times;</button>

      </div>

      <div class="modal-body" id="modalContent">
        Loading...
      </div>

      <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<script>

    document.getElementById('exportSampleBtn').onclick = function() {
        window.location.href = "<?= base_url('admin/RateModifier/export_sample_rate'); ?>";
    };
    document.getElementById('exportBtn').onclick = function() { 
        window.location.href = "<?= base_url('admin/RateModifier/export_all'); ?>";
    };

    $(document).on("click", ".viewBtn", function () {
        
        let id = $(this).data("id");
        $("#modalContent").html("Loading...");

        $.ajax({
            url: "<?= base_url('admin/RateModifier/view_ajax/') ?>" + id,
            method: "GET",
            success: function(response) {
                $("#modalContent").html(response);
                $("#viewModal").modal("show");
            },
            error: function() {
                $("#modalContent").html("<p class='text-danger'>Failed to load data.</p>");
            }
        });
    });
    $(document).on('click', '.show-more', function () {
        let btn = $(this);
        let target = btn.data('target');
        let items = btn.closest('td').find('.' + target);

        if (items.hasClass('d-none')) {
            items.removeClass('d-none');
            btn.text('Show Less');
        } else {
            items.addClass('d-none');
            btn.text('+ More');
        }
    });

    $(document).on('click', '.show-more-service', function () {

    let btn = $(this);
    let items = btn.closest('td').find('.extra-services');

    if (items.hasClass('d-none')) {
        items.removeClass('d-none');
        btn.text(' Show Less');
    } else {
        items.addClass('d-none');
        btn.text(' + More');
    }
});

    

</script>
