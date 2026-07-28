<script>
function refreshSupportCsrf() {
    var match = document.cookie.match(new RegExp('(?:^|; )<?= config('Security')->cookieName; ?>=([^;]*)'));
    if (match) {
        csrfHash = decodeURIComponent(match[1]);
    }
}

$(document).ajaxComplete(refreshSupportCsrf);

function reply(id) {
    refreshSupportCsrf();
    $.ajax({
        type: "POST",
        url: "<?= base_url('admin/support/reply'); ?>",
        data: {[csrfName]: csrfHash, id: id},
        success: function(data) {
            $('#reply1').html(data);
        },
        error: function() {
            alert('fail');
        }
    });
}

function submit(id) {
    refreshSupportCsrf();
    var status = $('#status').val();
    $.ajax({
        type: "POST",
        url: "<?= base_url('admin/support/submit'); ?>",
        data: {[csrfName]: csrfHash, id: id, status: status},
        beforeSend: function() {
            $('#loader').css('display', 'block');
        },
        success: function(data) {
            $('#loader').css('display', 'none');
            $('#success').modal('toggle');
            $('#reply').modal('toggle');
            document.getElementById("success-body").innerHTML = data;
        },
        error: function() {
            alert('fail');
        }
    });
}

$('#select-all').click(function() {
    if (this.checked) {
        $('.mani').each(function() { this.checked = true; $('#footer').css('display', 'block'); });
    } else {
        $('.mani').each(function() { this.checked = false; $('#footer').css('display', 'none'); });
    }
});

$('.mani').click(function() {
    var selected = [];
    $.each($("input[name='ticket']:checked"), function() {
        selected.push($(this).val());
    });
    if (selected.length > 0) {
        $('#footer').css('display', 'block');
        document.getElementById("al").innerHTML = selected.length + " Ticket Selected";
    } else {
        $('#footer').css('display', 'none');
    }
});

function minus(source) {
    var checkboxes = document.getElementsByName('ticket');
    var boxes = document.getElementsByName('select-all');
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        checkboxes[i].checked = false;
    }
    boxes[0].checked = false;
    $('#footer').css('display', 'none');
}

function mark(status) {
    refreshSupportCsrf();
    var selected = [];
    $.each($("input[name='ticket']:checked"), function() {
        selected.push($(this).val());
    });
    $.ajax({
        url: "<?= base_url('admin/support/markall'); ?>",
        method: "POST",
        data: {[csrfName]: csrfHash, id: selected, status: status},
        beforeSend: function() {
            $('#loader').css('display', 'block');
        },
        success: function(data) {
            $('#loader').css('display', 'none');
            $('#success').modal('toggle');
            $('#success-body').html(data);
        }
    });
}
</script>
