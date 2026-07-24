<style>
.checkbox-group { margin-left: 12px; }

.checkbox-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.checkbox-row input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: #1E88E5;
    cursor: pointer;
}

#ticket_option {
    display: none;
    border: 1px solid #ccc;
    padding: 15px;
    margin-top: 10px;
    border-radius: 5px;
    background: #f9f9f9;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const checkbox = document.getElementById("create_ticket");
    const ticketDiv = document.getElementById("ticket_option");

    checkbox.addEventListener("change", function () {
        ticketDiv.style.display = this.checked ? "block" : "none";
    });
});
</script>

<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">

            <!-- HEADER -->
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3>Add Exception Master</h3>

                <?= anchor(
                    'admin/exceptionMaster/',
                    'View Exception List',
                    ['class' => 'btn btn-success']
                ); ?>
            </div>

            <div class="col-lg-12">
                <?php
                    $error = session()->getFlashdata('error');
                    $error_class = session()->getFlashdata('error_class');
                ?>

                <?php if ($error): ?>
                    <div class="alert alert-dismissible <?= $error_class; ?>">
                        <strong><?= $error; ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <div class="clearfix"></div>

            <!-- FORM -->
            <div class="well1 white form-container">
                <?= form_open_multipart('admin/exceptionMaster/insert') ?>

                <fieldset>

                    <!-- STATUS CODE -->
                    <div class="form-group">
                        <label>Status Code <span style="color:red">*</span></label>
                        <input class="form-control" type="text" id="status_code" name="status_code" required>
                    </div>
                    
                     <!-- STATUS CODE -->
                    <div class="form-group">
                        <label>Status Type <span style="color:red">*</span></label>
                        <input class="form-control" type="text" id="status_type" name="status_type" required>
                    </div>
                    
                    <!-- CODE TYPE -->
                    <div class="form-group">
                        <label>Code Type <span style="color:red">*</span></label>
                        <input class="form-control" type="text" id="code_type" name="code_type" required>
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="form-group">
                        <label>Description <span style="color:red">*</span></label>
                        <textarea class="form-control" id="desc" name="desc" rows="4" required></textarea>
                    </div>

                    <!-- ADD IN EXCEPTION REPORT -->
                    <div class="form-group checkbox-row">
                        <input type="checkbox" id="is_ndr" name="is_ndr" value="1">
                        <label for="is_ndr">ADD IN NDR</label>
                    </div>

                    <!-- CREATE TICKET -->
                    <div class="form-group checkbox-row">
                        <input type="checkbox" id="create_ticket" name="create_ticket" value="1">
                        <label for="create_ticket">CREATE A TICKET</label>
                    </div>

                    <div id="ticket_option" class="form-group">

                        <div class="form-group">
                            <label>TICKET TYPE <span style="color:red">*</span></label>
                            <select class="form-control" name="ticket_department">
                                <option value="">SELECT...</option>
                                <option value="BILLING">BILLING</option>
                                <option value="CUSTOMER SERVICE">CUSTOMER SERVICE</option>
                                <option value="OPERATIONS">OPERATIONS</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>TICKET SUB TYPE <span style="color:red">*</span></label>
                            <select class="form-control" name="ticket_priority">
                                <option value="">SELECT...</option>
                                <option value="BILLING ALERT">BILLING ALERT</option>
                                <option value="CROSEED EDD">CROSEED EDD</option>
                                <option value="EXCEPTION SCAN">EXCEPTION SCAN</option>
                            </select>
                        </div>

                    </div>
                    <!-- STATUS -->
                    <div class="form-group">
                        <label>Status</label>
                        <div>
                            <label class="radio-inline" style="margin-right:20px;">
                                <input name="status" type="radio" value="1" checked> Active
                            </label>

                            <label class="radio-inline">
                                <input name="status" type="radio" value="0"> Inactive
                            </label>
                        </div>
                    </div>

                    <!-- BUTTON -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>

                </fieldset>

                <?= form_close(); ?>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const checkbox = document.getElementById("create_ticket");
    const ticketDiv = document.getElementById("ticket_option");

    checkbox.addEventListener("change", function () {
        if (this.checked) {
            ticketDiv.style.display = "block";
        } else {
            ticketDiv.style.display = "none";
        }
    });
});
</script>
