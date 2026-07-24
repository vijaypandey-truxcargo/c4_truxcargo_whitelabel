<style>
.customer-table-shell {
    clear: both;
    max-width: 100%;
    overflow: hidden;
    position: relative;
    z-index: 1;
}
.customer-table-scroll {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 5px;
}
.customer-table-scroll table {
    margin-bottom: 0;
    min-width: max-content;
    width: 100%;
}
.customer-table-scroll th,
.customer-table-scroll td {
    padding: 9px 12px !important;
    vertical-align: top !important;
    white-space: nowrap;
}
.customer-table-scroll table.dataTable thead > tr > th.sorting,
.customer-table-scroll table.dataTable thead > tr > th.sorting_asc,
.customer-table-scroll table.dataTable thead > tr > th.sorting_desc {
    padding-right: 34px !important;
}
.customer-table-scroll table.dataTable thead > tr > th.sorting::before,
.customer-table-scroll table.dataTable thead > tr > th.sorting_asc::before,
.customer-table-scroll table.dataTable thead > tr > th.sorting_desc::before,
.customer-table-scroll table.dataTable thead > tr > th.sorting::after,
.customer-table-scroll table.dataTable thead > tr > th.sorting_asc::after,
.customer-table-scroll table.dataTable thead > tr > th.sorting_desc::after {
    right: 12px !important;
}
.customer-dt-toolbar {
    align-items: center;
    clear: both;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: space-between;
    margin: 20px 0 10px;
}
.customer-dt-search {
    align-items: center;
    display: flex;
    gap: 8px;
}
.customer-dt-search label {
    margin: 0;
}
.customer-dt-search input {
    max-width: 260px;
}
</style>
<div id="page-wrapper">
    <div class="col-md-12 graphs">
      <div class="xs">
          <h3 class="pull-left" style="text-transform: capitalize"><?= $type;?> Users</h3>
	<div class="clearfix"></div>
         <div class="bs-example4" data-example-id="contextual-table">
            <div class="customer-dt-toolbar">
                <div class="customer-dt-search">
                    <label for="active_user_search">Search:</label>
                    <input type="search" id="active_user_search" class="form-control input-sm">
                </div>
                <div id="active_user_export_buttons"></div>
            </div>

            <div class="customer-table-shell">
                <div class="customer-table-scroll">
               <table class="table" id="active_user_table">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th width="12%"> Joining Date</th>  
                          <th> UserID</th> 
                          <th> Name</th>
                          <th> Company Name</th>
                          <th> Email Id</th>
                          <th> Contact No</th>
                          <th> B2B Orders</th>
                          <th> B2C Orders</th>
                          <th> Current B2B </th>
                          <th> Current B2C </th>
                       </tr>
                  </thead>
                  <tbody>
                  <?php
                       $count = $count ?? 0;
                       foreach ($user as $row): $count++;
                       $key = $info($row['login_id']);?>
                      <tr class="active">
                          <td><?= $count; ?></td>
                          <td><?= date('d M Y', strtotime($key->date)); ?></td> 
                          <td><b><?= $key->username; ?></b></td>
                          <td><?= $key->first; ?> <?= $key->last; ?></td>                         
                          <td><?= $key->company; ?>   </td> 
                          <td> <?= $key->email?> </td>
                          <td> <?= $key->phone?> </td>
                          <td><?= $row['b2b'];?></td>
                          <td><?= $row['b2c'];?></td>
                          <td><?= $cb2b($row['login_id']);?></td>
                          <td><?= $cb2c($row['login_id']);?></td>
                      </tr>
                          <?php endforeach;?>
                  </tbody>
              </table>
                </div>
            </div>
              <p class="pagination"><?= $links ?? ''; ?></p>
           </div>
        </div>
          <div id="report_order" style="display: none"></div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable && ! $.fn.DataTable.isDataTable('#active_user_table')) {
        var activeUserTable = $('#active_user_table').DataTable({
            paging: false,
            info: false,
            ordering: true,
            order: [],
            pageLength: 10,
            dom: 't',
            language: {
                emptyTable: 'No records found.'
            },
            buttons: [
                { extend: 'excelHtml5', footer: true },
                { extend: 'csvHtml5', footer: true },
                { extend: 'pdfHtml5', footer: true }
            ]
        });

        activeUserTable.buttons().container().appendTo('#active_user_export_buttons');
        $('#active_user_search').on('keyup change search', function() {
            activeUserTable.search(this.value).draw();
        });
    }
});
</script>
   
