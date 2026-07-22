<style>

    .log-table td,
    .log-table th {
        font-size: 12px;
        vertical-align: top !important;
    }

    .badge-action {
        padding: 5px 10px;
        border-radius: 4px;
        color: #fff;
        font-size: 11px;
        display: inline-block;
    }

    .badge-insert { background: #28a745; }
    .badge-update { background: #007bff; }
    .badge-delete { background: #dc3545; }
    .badge-other  { background: #f0ad4e; }

    .view-changes-btn {
        font-size: 12px;
        padding: 5px 10px;
    }

    #activityLogModal .modal-dialog {
        width: 900px;
    }

    #activityLogModal .modal-body {
        max-height: 500px;
        overflow-y: auto;
    }

    .modal-change-table th {
        background: #f5f5f5;
        font-weight: 600;
    }

    .modal-change-table td,
    .modal-change-table th {
        font-size: 12px;
        vertical-align: middle !important;
        padding: 8px !important;
    }

    .modal-change-table td:first-child {
        width: 25%;
        font-weight: 600;
    }

    .modal-change-table .old-val {
        color: #d9534f;
        background: #fff4f4;
        font-weight: 600;
        word-break: break-word;
        white-space: pre-wrap;
    }

    .modal-change-table .new-val {
        color: #198754;
        background: #f2fff4;
        font-weight: 600;
        word-break: break-word;
        white-space: pre-wrap;
    }

    .record-info-box {
        margin-bottom: 15px;
        padding: 10px 12px;
        border: 1px solid #bce8f1;
        background: #d9edf7;
        border-radius: 4px;
        font-size: 13px;
        line-height: 22px;
    }

</style>

<div id="page-wrapper">

    <div class="col-md-12 graphs">

        <div class="xs">

            <div style="margin-bottom:15px; width:100%; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0;">Activity Logs</h3>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-lg-12">

                    <?php
                    $error = $this->session->flashdata('error');
                    $error_class = $this->session->flashdata('error_class');

                    if ($error):
                    ?>
                        <div class="alert alert-dismissible <?= $error_class; ?>">
                            <strong><?= $error; ?></strong>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <div class="bs-example4" data-example-id="contextual-table">

                <table class="table table-bordered table-hover log-table" id="example">

                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>IP Address</th>
                            <th>Date</th>
                            <th>Changes</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php

                        if (empty($count)) {
                            $count = 0;
                        }

                        foreach ($logs as $log):

                            $count++;

                            $action_type = strtoupper(trim($log->action_type));

                            $new_data = json_decode($log->new_data, true);
                            $old_data = json_decode($log->old_data, true);

                            if (!is_array($new_data)) {
                                $new_data = [];
                            }

                            if (!is_array($old_data)) {
                                $old_data = [];
                            }

                            $badge_class = 'badge-other';

                            if ($action_type == 'INSERT') {
                                $badge_class = 'badge-insert';
                            } elseif ($action_type == 'UPDATE') {
                                $badge_class = 'badge-update';
                            } elseif ($action_type == 'DELETE') {
                                $badge_class = 'badge-delete';
                            }

                        ?>

                            <tr class="active">

                                <td><?= $count; ?></td>

                                <td><?= htmlspecialchars($log->user_name ?? ''); ?></td>

                                <td><?= htmlspecialchars($log->module_name ?? ''); ?></td>

                                <td>
                                    <span class="badge-action <?= $badge_class; ?>">
                                        <?= htmlspecialchars($action_type); ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($log->ip_address ?? ''); ?></td>

                                <td>
                                    <?= !empty($log->created_at) ? date('d M Y h:i:s A', strtotime($log->created_at)) : ''; ?>
                                </td>

                                <td class="text-center">

                                    <button
                                        type="button"
                                        class="btn btn-info btn-sm view-changes-btn"
                                        data-toggle="modal"
                                        data-target="#activityLogModal"
                                        data-module="<?= htmlspecialchars($log->module_name ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        data-action="<?= htmlspecialchars($action_type, ENT_QUOTES, 'UTF-8'); ?>"
                                        data-old='<?= htmlspecialchars(json_encode($old_data), ENT_QUOTES, 'UTF-8'); ?>'
                                        data-new='<?= htmlspecialchars(json_encode($new_data), ENT_QUOTES, 'UTF-8'); ?>'
                                    >
                                        <i class="fa fa-eye"></i> View Changes
                                    </button>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

                <p class="pagination">
                    <?= $links; ?>
                </p>

            </div>

        </div>

    </div>

</div>

<!-- MODAL -->
<div class="modal fade" id="activityLogModal" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-lg" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>

                <h4 class="modal-title">Activity Log Changes</h4>

            </div>

            <div class="modal-body">
                <div id="activityLogModalContent">Loading...</div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Close
                </button>
            </div>

        </div>

    </div>

</div>

<script>

function escapeHtml(value)
{
    return $('<div>').text(
        value === null || value === undefined ? '' : value
    ).html();
}

function formatFieldName(key)
{
    return String(key)
        .replace(/_/g, ' ')
        .replace(/([a-z])([A-Z])/g, '$1 $2')
        .replace(/\b\w/g, function(letter) {
            return letter.toUpperCase();
        });
}

function formatActivityFieldValue(key, value)
{
    if (value === null || value === undefined || value === '') {
        return '';
    }

    // Only status conversion
    if (key === 'status') {
        if (String(value) === '1') {
            return 'Active';
        }

        if (String(value) === '0') {
            return 'Inactive';
        }
    }

    if (typeof value === 'object') {
        return JSON.stringify(value, null, 2);
    }

    return value;
}

/*
|--------------------------------------------------------------------------
| Record name automatic find
|--------------------------------------------------------------------------
| Aapke logs me mostly name / userName aa raha hai.
*/
function getRecordTitle(oldData, newData)
{
    var data = {};

    // New first, then old overwrite: old data full DB record hota hai
    $.extend(data, newData);
    $.extend(data, oldData);

    if (
        data.name !== undefined &&
        data.name !== null &&
        String(data.name).trim() !== ''
    ) {
        return String(data.name).trim();
    }

    if (
        data.userName !== undefined &&
        data.userName !== null &&
        String(data.userName).trim() !== ''
    ) {
        return String(data.userName).trim();
    }

    var skipFields = [
        'id',
        'status',
        'mode',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'is_deleted',
        'is_active',
        'active',
        'submit',
        'password',
        'confirm_passowrd',
        'confirm_password',
        'login_token',
        'is_login',
        'last_activity',
        'date'
    ];

    var title = '';

    $.each(data, function(key, value) {

        if ($.inArray(key, skipFields) !== -1) {
            return true;
        }

        if (
            value === null ||
            value === undefined ||
            String(value).trim() === '' ||
            typeof value === 'object'
        ) {
            return true;
        }

        if (/^\d+(\.\d+)?$/.test(String(value).trim())) {
            return true;
        }

        title = String(value).trim();
        return false;
    });

    return title;
}

function getUpdatedByName(updatedById, callback)
{
    if (
        updatedById === null ||
        updatedById === undefined ||
        updatedById === ''
    ) {
        callback('');
        return;
    }

    $.ajax({
        url: "<?= base_url('admin/activityLog/get_user_name'); ?>",
        type: "POST",
        dataType: "json",
        data: {
            user_id: updatedById
        },
        success: function(response) {
            callback(response.status ? response.user_name : updatedById);
        },
        error: function() {
            callback(updatedById);
        }
    });
}

$(document).on('click', '.view-changes-btn', function() {

    var button = $(this);

    var moduleName = button.attr('data-module') || '';
    var actionType = String(button.attr('data-action') || '').toUpperCase();

    var oldData = {};
    var newData = {};

    try {
        oldData = JSON.parse(button.attr('data-old') || '{}');
        newData = JSON.parse(button.attr('data-new') || '{}');
    } catch (error) {
        $('#activityLogModalContent').html(
            '<div class="alert alert-danger">Invalid activity log data.</div>'
        );
        return;
    }

    var recordTitle = getRecordTitle(oldData, newData);

    $('#activityLogModal .modal-title').text(
        moduleName +
        (recordTitle ? ' - ' + recordTitle : '') +
        ' - ' + actionType + ' Details'
    );

    var html = '';

    html += '<div class="record-info-box">';
    html += '<b>Module:</b> ' + escapeHtml(moduleName) + '<br>';
    html += '<b>Action:</b> ' + escapeHtml(actionType);

    if (recordTitle) {
        html += '<br><b>Record:</b> ' + escapeHtml(recordTitle);
    }

    html += '</div>';

    var hiddenFields = [
        'id',
        'created_at',
        'updated_at',
        'password',
        'confirm_passowrd',
        'confirm_password',
        'login_token',
        'is_login',
        'last_activity'
    ];

    /* INSERT */
    if (actionType === 'INSERT') {

        html += '<table class="table table-bordered modal-change-table">';
        html += '<thead><tr><th>Field</th><th>New Value</th></tr></thead><tbody>';

        var insertFound = false;

        $.each(newData, function(key, value) {

            if ($.inArray(key, hiddenFields) !== -1 || key === 'updated_by') {
                return true;
            }

            insertFound = true;

            html += '<tr>';
            html += '<td>' + escapeHtml(formatFieldName(key)) + '</td>';
            html += '<td class="new-val">' + escapeHtml(formatActivityFieldValue(key, value)) + '</td>';
            html += '</tr>';
        });

        if (!insertFound) {
            html += '<tr><td colspan="2" class="text-center">No Data Found</td></tr>';
        }

        html += '</tbody></table>';
        $('#activityLogModalContent').html(html);
        return;
    }

    /* DELETE */
    if (actionType === 'DELETE') {

        html += '<table class="table table-bordered modal-change-table">';
        html += '<thead><tr><th>Field</th><th>Old Value</th></tr></thead><tbody>';

        var deleteFound = false;
        var deleteUpdatedBy = [];

        $.each(oldData, function(key, value) {

            if ($.inArray(key, hiddenFields) !== -1) {
                return true;
            }

            deleteFound = true;

            var displayValue = formatActivityFieldValue(key, value);

            if (key === 'updated_by' && displayValue !== '') {

                var elementId = 'delete_updated_by_' + deleteUpdatedBy.length;

                deleteUpdatedBy.push({
                    elementId: elementId,
                    userId: displayValue
                });

                displayValue = '<span id="' + elementId + '">Loading...</span>';

            } else {
                displayValue = escapeHtml(displayValue);
            }

            html += '<tr>';
            html += '<td>' + escapeHtml(formatFieldName(key)) + '</td>';
            html += '<td class="old-val">' + displayValue + '</td>';
            html += '</tr>';
        });

        if (!deleteFound) {
            html += '<tr><td colspan="2" class="text-center">No Data Found</td></tr>';
        }

        html += '</tbody></table>';
        $('#activityLogModalContent').html(html);

        $.each(deleteUpdatedBy, function(index, row) {
            getUpdatedByName(row.userId, function(userName) {
                $('#' + row.elementId).html(escapeHtml(userName));
            });
        });

        return;
    }

    /* UPDATE */
    html += '<table class="table table-bordered modal-change-table">';
    html += '<thead><tr><th>Field</th><th>Old Value</th><th>New Value</th></tr></thead><tbody>';

    var allKeys = {};

    $.each(oldData, function(key) {
        allKeys[key] = true;
    });

    $.each(newData, function(key) {
        allKeys[key] = true;
    });

    var updateFound = false;
    var updateUpdatedBy = [];

    $.each(allKeys, function(key) {

        if ($.inArray(key, hiddenFields) !== -1) {
            return true;
        }

        var oldValue = oldData[key] !== undefined ? oldData[key] : '';
        var newValue = newData[key] !== undefined ? newData[key] : '';

        var oldText = formatActivityFieldValue(key, oldValue);
        var newText = formatActivityFieldValue(key, newValue);

        /*
        | updated_by same ho tab bhi show hoga.
        | Name / Code same ho to show nahi hoga, only actual change dikhega.
        */
        if (
            key !== 'updated_by' &&
            String(oldText).trim() === String(newText).trim()
        ) {
            return true;
        }

        updateFound = true;

        var oldDisplay = escapeHtml(oldText);
        var newDisplay = escapeHtml(newText);

        if (key === 'updated_by') {

            var oldElementId = 'update_old_updated_by_' + updateUpdatedBy.length;
            var newElementId = 'update_new_updated_by_' + updateUpdatedBy.length;

            updateUpdatedBy.push({ 
                oldElementId: oldElementId,
                newElementId: newElementId,
                oldUserId: oldText,
                newUserId: newText
            });

            oldDisplay = '<span id="' + oldElementId + '">Loading...</span>';
            newDisplay = '<span id="' + newElementId + '">Loading...</span>';
        }

        html += '<tr>';
        html += '<td>' + escapeHtml(formatFieldName(key)) + '</td>';
        html += '<td class="old-val">' + oldDisplay + '</td>';
        html += '<td class="new-val">' + newDisplay + '</td>';
        html += '</tr>';
    });

    if (!updateFound) {
        html += '<tr><td colspan="3" class="text-center">No Changes Found</td></tr>';
    }

    html += '</tbody></table>';
    $('#activityLogModalContent').html(html);

    $.each(updateUpdatedBy, function(index, row) {

        getUpdatedByName(row.oldUserId, function(userName) {
            $('#' + row.oldElementId).html(escapeHtml(userName));
        });

        getUpdatedByName(row.newUserId, function(userName) {
            $('#' + row.newElementId).html(escapeHtml(userName));
        });

    });

});

</script>