<div class="loader" id="loader"><img src="<?= base_url('assets/images/loading.gif');?>"></div>
<!-- slip -->
 <div class="slip1" id="slip" ></div>
 <div id="report_order" style="display: none"></div>
 <div class="modal fade none-border" id="info">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong>Billing's Alert</strong></h4>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>            
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">     
                        <div class="form-group">
                            <p class="control-label mb-1 red text-center">Please provide the billing info i.e (GSTIN, Billing Address) in your profile.</p>
                        </div>
                    </div> </div>    
            </div>
            <div class="modal-footer">
                <a href="<?= base_url('dashboard/profile');?>" class="btn btn-primary">Go</a>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>                
            </div>
         </div>
    </div>
  </div>
   <script>
    $(".modal").on("shown.bs.modal", function () {
      if ($(".modal-backdrop").length > 1) {
        $(".modal-backdrop").not(':first').remove();
      }
    }); 
    
    function dateSearch(){
      $('#apply').css('display', 'block');
      var opt = $("#date option:selected").val();
      if(opt==='Custom Range'){$('#show').css('display', 'block'); $('#show1').css('display', 'block');}
      else {$('#show').css('display', 'none'); $('#show1').css('display', 'none');}
    }
    $(function() {  $(".choose").chosen();});
    
    function minus(source) {
      checkboxes = document.getElementsByName('waybill');
      boxes = document.getElementsByName('select-all');
      for(var i=0, n=checkboxes.length;i<n;i++) {
       checkboxes[i].checked = false;
      }
      boxes[0].checked =false;
      $('#footer').css('display', 'none');
    }

    $('#select-all').click(function(event) {   
      if(this.checked) {
         $('.mani').each(function() {
            this.checked = true; 
            $('#footer').css('display', 'block');
            $('#pack').css('opacity', '1');
        });
      }
      else {
        $('.mani').each(function() {
            this.checked = false;  
            $('#footer').css('display', 'none');
            $('#pack').css('opacity', '0');
        });
      }
    });
 
    $('.mani').click(function(event) {   
      var fav = [];
      $.each($("input[name='waybill']:checked"), function(){            
      fav.push($(this).val());});
      if(fav.length>0){$('#footer').css('display', 'block'); $('#pack').css('opacity', '1');
        document.getElementById("al").innerHTML = fav.length+" Orders Selected";
      }
      else {$('#footer').css('display', 'none'); $('#pack').css('opacity', '0');}
    });
    
    function packaging(ship,waybill){
        var w = waybill;
        document.getElementById(w).innerHTML= 'Printing';
         $.ajax({  
                     url:"<?php echo base_url() ?>index.php/orderb2c/packaging_slip",   
                     method:"POST",  
                     data:{[csrfName]: csrfHash,ship:ship,waybill:waybill},  
                     beforeSend: function() {
                       $('#loader').css('display', 'block');
                      },                     
                     success:function(data){ 
                         $('#loader').css('display', 'none'); 
                         $('#slip').html(data);                      
                         document.getElementById(w).innerHTML= '<i class="fa fa-print"></i> Packaging Slip';
                         var el ='slip';
                         var restorepage = $('body').html();
                         var printcontent = $('#' + el).clone();
                         $('body').empty().html(printcontent);                         
                         setTimeout(function () { // wait until all resources loaded 
                          window.print();
                          $('body').html(restorepage); 
                         }, 750);
                     }                             
                });         
         }  
         window.onafterprint = function() {
        window.location.reload(true);
    };
    </script>  
<footer class="footer">
    <div class="footer-body">
        <ul class="left-panel list-inline mb-0 p-0">
            <li class="list-inline-item"><a href="<?= $wconfig->website ?? '';?>/privacypolicy" target="_blank">Privacy Policy</a></li>
            <li class="list-inline-item"><a href="<?= $wconfig->website ?? '';?>/termandcondition"  target="_blank">Terms of Use</a></li>
        </ul>
        <div class="right-panel">
            Copyright &copy; <?= date('Y');?> <?= $wconfig->company ?? '';?> &nbsp; &nbsp;&nbsp; &nbsp;
            <span class="fp">  Made In <img src="<?= base_url('assets/images/india.png');?>" width="20"> with
              <span style="color:red">
                  <svg class="icon-16" width="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.85 2.50065C16.481 2.50065 17.111 2.58965 17.71 2.79065C21.401 3.99065 22.731 8.04065 21.62 11.5806C20.99 13.3896 19.96 15.0406 18.611 16.3896C16.68 18.2596 14.561 19.9196 12.28 21.3496L12.03 21.5006L11.77 21.3396C9.48102 19.9196 7.35002 18.2596 5.40102 16.3796C4.06102 15.0306 3.03002 13.3896 2.39002 11.5806C1.26002 8.04065 2.59002 3.99065 6.32102 2.76965C6.61102 2.66965 6.91002 2.59965 7.21002 2.56065H7.33002C7.61102 2.51965 7.89002 2.50065 8.17002 2.50065H8.28002C8.91002 2.51965 9.52002 2.62965 10.111 2.83065H10.17C10.21 2.84965 10.24 2.87065 10.26 2.88965C10.481 2.96065 10.69 3.04065 10.89 3.15065L11.27 3.32065C11.3618 3.36962 11.4649 3.44445 11.554 3.50912C11.6104 3.55009 11.6612 3.58699 11.7 3.61065C11.7163 3.62028 11.7329 3.62996 11.7496 3.63972C11.8354 3.68977 11.9247 3.74191 12 3.79965C13.111 2.95065 14.46 2.49065 15.85 2.50065ZM18.51 9.70065C18.92 9.68965 19.27 9.36065 19.3 8.93965V8.82065C19.33 7.41965 18.481 6.15065 17.19 5.66065C16.78 5.51965 16.33 5.74065 16.18 6.16065C16.04 6.58065 16.26 7.04065 16.68 7.18965C17.321 7.42965 17.75 8.06065 17.75 8.75965V8.79065C17.731 9.01965 17.8 9.24065 17.94 9.41065C18.08 9.58065 18.29 9.67965 18.51 9.70065Z" fill="currentColor"></path>
                  </svg>
              </span> 
            </span>    
        </div>
    </div>
 </footer>
</main> 
<script src="<?= base_url('assets/js/core/libs.min.js')?>"></script>
<script src="<?= base_url('assets/vendor/flatpickr/dist/flatpickr.min.js')?>"></script>
<script src="<?= base_url('assets/js/plugins/flatpickr.js')?>" defer></script>
<script src="<?= base_url('assets/js/plugins/select2.js')?>" defer></script> 
<script src="<?= base_url('assets/vendor/lodash/lodash.min.js')?>"></script>
<script src="<?= base_url('assets/js/iqonic-script/utility.min.js')?>"></script>
<script src="<?= base_url('assets/js/iqonic-script/setting.min.js')?>"></script>
<script src="<?= base_url('assets/js/setting-init.js')?>"></script>
<script src="<?= base_url('assets/js/core/external.min.js')?>"></script>
<script src="<?= base_url('assets/js/charts/widgetchartsf700.js')?>?v=1.0.1" defer></script>
<script src="<?= base_url('assets/js/charts/dashboardf700.js')?>?v=1.0.1" defer></script>
<script src="<?= base_url('assets/js/qompac-uif700.js')?>?v=1.0.1" defer></script>
<script src="<?= base_url('assets/js/sidebarf700.js')?>?v=1.0.1" defer></script>

<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){ $('#bootstrap-data-table-export').DataTable(); } );
	$(document).ready(function(){ $('.table1').dataTable();} );
        $(document).ready(function(){
            $('#example').DataTable( {
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: ['excelHtml5']
            } );
        } );
    </script>
    <script src="<?= base_url('assets/js/lib/chosen/chosen.jquery.min.js')?>"></script>
    <script>
      jQuery(document).ready(function() {
        jQuery(".standardSelect").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%"
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
      function close1(){
         $(".modal").modal('hide');
         $(".modal").css('display', 'none');
         $('.modal-backdrop ').css('display', 'none');
      }     
    </script>
    <script src="<?= base_url('assets/js/jquery-ui.js')?>"></script>
    <script> 
      var dateToday = new Date(); 
       $(function () {
         $("#pickup_date").datepicker({  minDate: dateToday  });
      });
      $(function () {
        $("#datepicker").datepicker({ minDate: dateToday });
      });
      $(function () {
        $("#datepicker1").datepicker();
      });
        setTimeout(function(){$(".alert-dismissible").hide();},20000);
     
    </script>
    
</body>
</html>