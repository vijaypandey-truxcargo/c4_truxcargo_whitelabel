<style>
.checkbox-group {
    margin-left: 12px;
}

.checkbox-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.checkbox_new_apply {
    width: 29px;
    height: 19px;
    align-items: center;
}

.checkbox-row input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: #1E88E5;
    cursor: pointer;
}
</style>

<div id="page-wrapper">

    <div class="col-md-12 graphs">

        <div class="xs">

            <h3 class="pull-left">
                Edit Exception Master
            </h3>

            <?= anchor(
                'admin/exceptionMaster/',
                'Back to Exception List',
                ['class' => 'btn btn-primary pull-right']
            ); ?>

            <div class="clearfix"></div>

            <div class="well1 white form-container">

                <?= form_open_multipart(
                    'admin/exceptionMaster/update/'.$exception->id
                ) ?>

                <fieldset>

                    <!-- STATUS CODE -->
                    <div class="form-group">

                        <label>Status Code</label>

                        <input
                            class="form-control"
                            type="text"
                            name="status_code"
                            value="<?= $exception->status_code ?>"
                            readonly
                        >

                    </div>
                    
                    <!-- STATUS CODE -->
                    <div class="form-group">

                        <label>Status Type</label>

                        <input
                            class="form-control"
                            type="text"
                            name="status_type"
                            value="<?= $exception->status_type ?>"
                        >

                    </div>
                    
                    <div class="form-group">
                        <label>Code Type</label>
                        <input class="form-control" type="text" id="code_type" name="code_type" value="<?= $exception->code_type ?>">
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="form-group">

                        <label>Description</label>

                        <textarea
                            class="form-control"
                            name="desc"
                            rows="4"
                        ><?= $exception->desc ?></textarea>

                    </div>

                    <!-- CHECKBOX GROUP -->
                    <div class="checkbox-group">

                        <!-- IS NDR -->
                        <div class="form-group checkbox-row">

                            <input
                                type="checkbox"
                                id="is_ndr"
                                name="is_ndr"
                                value="1"
                                <?= $exception->is_ndr == 1 ? 'checked' : '' ?>
                            >

                            <label for="is_ndr">
                               Add IN NDR
                            </label>

                        </div>

                        <!-- ADD IN EXCEPTION REPORT -->
                        <!-- <div class="form-group checkbox-row">

                            <input
                                type="checkbox"
                                id="add_in_exception_report"
                                name="is_ndr"
                                value="1"
                                <?= $exception->is_ndr == 1 ? 'checked' : '' ?>
                            >

                            <label for="add_in_exception_report">
                                ADD IN NDR
                            </label>

                        </div> -->

                        <!-- CREATE TICKET -->
                        <div class="form-group checkbox-row">

                            <input
                                type="checkbox"
                                id="create_ticket"
                                name="create_ticket"
                                value="1"
                                <?= $exception->create_ticket == 1 ? 'checked' : '' ?>
                            >

                            <label for="create_ticket">
                                CREATE A TICKET
                            </label>

                        </div>

                    </div>

                    <!-- TICKET SECTION -->
                    <div
                        id="ticket_section"
                        style="<?= ($exception->create_ticket == 1) ? '' : 'display:none;' ?>"
                    >

                        <!-- TICKET DEPARTMENT -->
                        <div class="form-group">

                            <label>Ticket Department</label>

                            <select
                                class="form-control"
                                name="ticket_department"
                            >

                                <option value="">
                                    Select Department
                                </option>

                                <option value="OPERATIONS"
                                    <?= $exception->ticket_department == 'OPERATIONS' ? 'selected' : '' ?>>
                                    OPERATIONS
                                </option>

                                <option value="SUPPORT"
                                    <?= $exception->ticket_department == 'CUSTOMER SERVICE' ? 'selected' : '' ?>>
                                    SUPPORT
                                </option>

                                <option value="BILLING"
                                    <?= $exception->ticket_department == 'BILLING' ? 'selected' : '' ?>>
                                    BILLING
                                </option>

                            </select>

                        </div>

                        <!-- TICKET PRIORITY -->
                        <div class="form-group">

                            <label>Ticket Priority</label>

                            <select
                                class="form-control"
                                name="ticket_priority"
                            >

                                <option value="">
                                    Select Priority
                                </option>

                                <option value="BILLING ALERT"
                                    <?= $exception->ticket_priority == 'BILLING ALERT' ? 'selected' : '' ?>>
                                    BILLING ALERT
                                </option>

                                <option value="CROSEED EDD"
                                    <?= $exception->ticket_priority == 'CROSEED EDD' ? 'selected' : '' ?>>
                                    CROSEED EDD
                                </option>

                                <option value="EXCEPTION SCAN"
                                    <?= $exception->ticket_priority == 'EXCEPTION SCAN' ? 'selected' : '' ?>>
                                    EXCEPTION SCAN
                                </option>

                            </select>

                        </div>

                    </div>

                    <!-- STATUS -->
                    <div class="form-group">

                        <label>Status</label>

                        <div>

                            <label
                                class="radio-inline"
                                style="margin-right:20px;"
                            >

                                <input
                                    type="radio"
                                    name="status"
                                    value="1"
                                    <?= $exception->status == 1 ? 'checked' : '' ?>
                                >

                                Active

                            </label>

                            <label class="radio-inline">

                                <input
                                    type="radio"
                                    name="status"
                                    value="0"
                                    <?= $exception->status == 0 ? 'checked' : '' ?>
                                >

                                Inactive

                            </label>

                        </div>

                    </div>

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $exception->id ?>"
                    >

                    <!-- BUTTON -->
                    <div class="form-group">

                        <button
                            type="submit"
                            name="submit"
                            class="btn btn-success"
                        >
                            Update Exception
                        </button>

                    </div>

                </fieldset>

                <?= form_close(); ?>

            </div>

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const createTicketCheckbox =
        document.getElementById('create_ticket');

    const ticketSection =
        document.getElementById('ticket_section');

    function toggleTicketSection() {

        if (createTicketCheckbox.checked) {

            ticketSection.style.display = 'block';

        } else {

            ticketSection.style.display = 'none';

        }
    }

    toggleTicketSection();

    createTicketCheckbox.addEventListener(
        'change',
        toggleTicketSection
    );

});

</script>