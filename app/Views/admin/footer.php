<div class="copy"> <p>Copyright &copy; <?= date('Y'); ?>   All Rights Reserved </p>    </div>
	</div>
     </div>
  </div>
  <link href="<?= base_url('backend/css/select2.min.css');?>" rel="stylesheet" />
  <script src="<?= base_url('backend/js/select2.min.js');?>"></script>
  <script>
     setTimeout(function(){$(".alert-dismissible").hide();},20000);
      function goto(){
          count =  document.getElementById("count").value;
          url =  document.getElementById("url").value;
          page = count*10-10;
          window.location.href = url+page;
      }

     $(document).ready(function () {
        $("select").select2();
     });
     $(document).ready(function() {
      $('#example').DataTable( {
        "pageLength": 10,
         dom: 'Bfrtip',
         buttons: [
             { extend: 'excelHtml5', footer: true },
            { extend: 'csvHtml5', footer: true },
            { extend: 'pdfHtml5', footer: true }
         ]
      });
     });
     $(document).ready(function() {
       $('#exam').DataTable( {
        "pageLength": 10,
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
      });
     });

     $(document).ready(function() {
      $('#example2').DataTable( {
        "pageLength": 20,
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
      });
     });
     $(document).ready(function() {
      $('#example1').DataTable( {
        "pageLength": 1000,
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
      });
     });
     $('[data-dismiss=modal]').on('click', function (e) {
        var $t = $(this),
        target = $t[0].href || $t.data("target") || $t.parents('.modal') || [];
        $(target)
           .find("input[type=text],input[type=email],textarea,select")
           .val('')
           .end()
           .find("input[type=checkbox], input[type=radio]")
           .prop("checked", "")
           .end();
        });
  </script>
  <script src="<?= base_url('assets/js/jquery-ui.js')?>"></script>
  <script>
    var dateToday = new Date();
    $(function () {
      $("#datepicker").datepicker({ minDate: dateToday });
    });
     $(function () {
      $("#datepicker1").datepicker({ minDate: dateToday });
    });
    $(function () {
     $("#pickup_date").datepicker({minDate: dateToday });
    });
     $(function () {
     $("#pdate").datepicker();
    });
  </script>
   <!------------- Notification------------------->
  <?php if(in_array("Fleet", $GLOBALS['permission'])){ ?>
  <script>
      $(document).ready(function() {
          showNotification();
          setInterval(function(){ showNotification(); }, 50000);
      });
      function showNotification() {
          if (!Notification) {
              $('body').append('<h4 style="color:red">*Browser does not support Web Notification</h4>');
              return;
          }
          if (Notification.permission !== "granted")
              Notification.requestPermission();
          else {
              $.ajax({
                  url : "<?php echo base_url() ?>index.php/admin/notification",
                  type: "POST",
                  data:{[csrfName]: csrfHash},
                  success: function(data, textStatus, jqXHR) {
                      var data = jQuery.parseJSON(data);
                      if(data.result == true) {
                          var data_notif = data.notif;
                          for (var i = data_notif.length - 1; i >= 0; i--) {
                              var theurl = data_notif[i]['url'];
                              var notifikasi = new Notification(data_notif[i]['title'], { icon: data_notif[i]['icon'],body: data_notif[i]['msg']});
                              notifikasi.onclick = function(){
                                  window.open(theurl);
                                  notifikasi.close();
                              };
                              setTimeout(function(){notifikasi.close();}, 5000);
                          };
                      } else { alert('failed');}
                  },
                  error: function(jqXHR, textStatus, errorThrown) { alert('error');}
               });
           }};
      </script>
    <?php }?>
</body>
</html>


<script>
var csrfName = '<?= csrf_token(); ?>';
var csrfHash = '<?= csrf_hash(); ?>';


    console.log('Screen Time JS Loaded');

var screenTimeId = 0;
var isUserActive = true;
var lastUserActivity = Date.now();

var currentModule = '<?= ucfirst(service('uri')->getSegment(2) ?: 'Dashboard'); ?>';
var currentPageTitle = document.title;
var currentPageUrl = window.location.href;

$(document).on('mousemove keydown scroll click touchstart', function () {
    isUserActive = true;
    lastUserActivity = Date.now();
});


setInterval(function () {
    if ((Date.now() - lastUserActivity) > 180000) {
        isUserActive = false;
    }
}, 10000);

function updateScreenTime() {
 
    if (!isUserActive) {
        return;
    }
    var postData = {
        screen_id: screenTimeId,
        module_name: currentModule,
        page_title: currentPageTitle,
        page_url: currentPageUrl
    };

    postData[csrfName] = csrfHash;

    $.ajax({
        url: '<?= base_url("admin/screenTime/heartbeat"); ?>',
        type: 'POST',
        dataType: 'json',
        data: postData,
        success: function (response) {
            if (response.status) {
                screenTimeId = response.screen_id;
            }
        }
    });
}


updateScreenTime();

setInterval(updateScreenTime, 10000);

window.addEventListener('beforeunload', function () {

    if (screenTimeId > 0) {
        navigator.sendBeacon(
            '<?= base_url("admin/screenTime/close_screen"); ?>',
            new URLSearchParams({
                screen_id: screenTimeId
            })
        );
    }
});

</script>
