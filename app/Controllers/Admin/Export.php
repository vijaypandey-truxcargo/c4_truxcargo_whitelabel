<?php include 'Secure.php';

class Export extends Secure{

    public function export_critical_log()
    {
        $data = $this->supportmodel->show('criticalog_pins',$order='DESC');
        $filename = 'Criticallog_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['#','Pincode','State Code','Service Type','Controlling City Name','Sale Zone','Purchase Zone']);
        $i = 1;
        foreach ($data as $row) {
                fputcsv($output, [
                $i++,
                    $row->pincode ?? '',
                    $row->state_code ?? '',
                    $row->service_type ?? '',
                    $row->controlling_city_name  ?? '',
                    $row->sale_zone ?? '',
                    $row->purchase_zone ?? ''
                ]);
        }

        fclose($output);
        exit;
    }

    public function export_bluedart_surface()
    {
        $data = $this->supportmodel->show('bluedart_surface',$order='DESC');
        $filename = 'BluedartSurface_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['#','Pincode','Area','Area Description','Branch','State','State Code','Purchase Zone','SFC Service','SFC_SERV_LOC_INB','SFC_SERV_LOC_OUTB','SFC TAT']);
        $i = 1;
        foreach ($data as $row) {
                fputcsv($output, [
                $i++,
                    $row->pincode ?? '',
                    $row->area ?? '',
                    $row->area_desc ?? '',
                    $row->branch  ?? '',
                    $row->state ?? '',
                    $row->state_code ?? '',
                    $row->purchase_zone ?? '',
                    $row->sfc_service ?? '',
                    $row->sfc_serv_loc_inb ?? '',
                    $row->sfc_serv_loc_outb ?? '',
                    $row->sfc_tat ?? ''
                ]);
        }

        fclose($output);
        exit;
    }

    public function export_bluedart_air()
    {
        $data = $this->supportmodel->show('bluedart_air',$order='DESC');
        $filename = 'BluedarAir_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['#','Pincode','Area','Area Description','Branch','State','Purchase Zone','CODE','CODE1','APEX TAT','EDL_APEX']);
        $i = 1;
        foreach ($data as $row) {
                fputcsv($output, [
                $i++,
                $row->pincode ?? '',
                $row->area ?? '',
                $row->area_desc ?? '',
                $row->branch  ?? '',
                $row->state ?? '',
                $row->purchase_zone ?? '',
                $row->code ?? '',
                $row->code1 ?? '',
                $row->apex_tat ?? '',
                $row->edl_apex ?? '',
            ]);
        }

        fclose($output);
        exit;
    }

    public function export_bluedart_dp()
    {
        $data = $this->supportmodel->show('bluedart_dp',$order='DESC');
        $filename = 'BluedarDP_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['#','Pincode','Area','Area Description','Location', 'Location Description', 'Branch', 'State', 'Purchase Zone', 'EDL_DP', 'DP SERVICE','CODE','CODE1','TAT']);
        $i = 1;
        foreach ($data as $row) {
                fputcsv($output, [
                $i++,
                    $row->pincode ?? '',
                    $row->area ?? '',
                    $row->area_desc ?? '',
                    $row->location  ?? '',
                    $row->loc_desc ?? '',
                    $row->branch ?? '',
                    $row->state ?? '',
                    $row->purchase_zone ?? '',
                    $row->edl_dp ?? '',
                    $row->dp_service ?? '',
                    $row->code ?? '',
                    $row->code1 ?? '',
                    $row->tat ?? ''
                ]);
        }
        fclose($output);
        exit;
    }


    public function export_delhivery_pincode()
    {
        $data = $this->supportmodel->show('delhivery_pincode',$order='DESC');
        $filename = 'BluedarDelhivery_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['#','Pincode','Origin','Delivery','CITY', 'Code', 'Branch', 'State', 'Green', 'Zone']);
        $i = 1;
        foreach ($data as $row) {
                fputcsv($output, [
                $i++,
                    $row->pincode ?? '',
                    $row->origin ?? '',
                    $row->delivery ?? '',
                    $row->city  ?? '',
                    $row->code ?? '',
                    $row->branch ?? '',
                    $row->state ?? '',
                    $row->green ?? '',
                    $row->zone ?? ''
                ]);
        }
        fclose($output);
        exit;
    }

    public function export_sales_pincode()
    {
        $data = $this->supportmodel->show('sales_pincode',$order='DESC');
        $filename = 'SalesPincode_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['#','CPINCODE','CAREA','CSCRCD','CSCRCDDESC', 'CREGION', 'CSTATECD', 'CSTATE', 'CPRODUCT', 'SFCZONE', 'EDL', 'CEDLDISTANCE', 'TAT', 'SURFACE SERVICE', 'SALE ZONE']);
        $i = 1;
        foreach ($data as $row) {
                fputcsv($output, [
                $i++,
                    $row->pincode ?? '',
                    $row->area ?? '',
                    $row->scrcd ?? '',
                    $row->scrcd_desc  ?? '',
                    $row->region ?? '',
                    $row->state_code ?? '',
                    $row->state ?? '',
                    $row->product ?? '',
                    $row->sfc_zone ?? '',
                    $row->edl ?? '',
                    $row->edl_distance ?? '',
                    $row->tat ?? '',
                    $row->surface_service ?? '',
                    $row->sale_zone ?? '',
                ]);
        }
        fclose($output);
        exit;
    }

    public function export_charge()
    {
        $data = $this->supportmodel->show('charge', $order = 'DESC');

        $filename = 'Charge_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Header Row
        fputcsv($output, [
            '#',
            'name',
            'is_gst_applicable',
            'is_fsc_applicable_sales',
            'is_fsc_applicable_purchase',
            'apply_on_order_creation',
            'rate',
            'status'
        ]);

        $i = 1;
        foreach ($data as $row) {
            if($row->rate_type == 1){
                $rate = 'Fixed';
            }else if($row->rate_type == 2){
                $rate = 'Rate per Kg';
            }else if($row->rate_type == 3){
                $rate = 'Rate per half Kg';
            }else{
                $rate = '';
            }

            fputcsv($output, [
                $i++,
                $row->name ?? '',
                ($row->is_gst_applicable == 1 ? 'Yes' : 'No'),
                ($row->is_fsc_applicable_sales == 1 ? 'Yes' : 'No'),
                ($row->is_fsc_applicable_purchase == 1 ? 'Yes' : 'No'),
                 ($row->apply_on_order_creation == 1 ? 'Yes' : 'No'),
                $rate ?? '',
                ($row->status == 1 ? 'Active' : 'Inactive')
            ]);
        }

        fclose($output);
        exit;
    }

    public function export_rate_modifier()
    {
        $this->db->select('rm.*,c.name AS charge_name');
        $this->db->from('rate_modifier rm');
        $this->db->join('charge c', 'c.id = rm.charge_id', 'left');
        $this->db->order_by('rm.id', 'DESC');

        $data = $this->db->get()->result();

        $filename = 'RateModifier_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        /* ================= CSV HEADER ================= */

        fputcsv($output, [
            '#',
            'charge_name',
            'billing_type',
            'fixed_amount',
            'min_amount',
            'percentage_on_freight',
            'percentage_on_shipment_value',
            'min_chargeable_weight',
            'max_chargeable_weight',
            'min_actual_weight',
            'max_actual_weight',
            'min_volume_weight',
            'max_volume_weight',
            'min_no_of_boxes',
            'max_no_of_boxes',
            'min_per_box_actual_weight',
            'max_per_box_actual_weight',
            'min_dimension',
            'max_dimension',
            'is_origin_oda',
            'is_destination_oda',
            'min_dimension_per_pcs',
            'max_dimension_per_pcs',
            'customer_code',
            'service_code',
            'vendor_code',
            'effective_from',
            'effective_to',
            'rate_mode',
            'status'
        ]);

        /* ================= RATE MODE MAP ================= */

        $rateModeMap = [
            1 => 'RATE PER PCS',
            2 => 'RATE PER KG',
            3 => 'RATE PER HALF KG',
            4 => 'RATE MIN PER BOX WEIGHT',
            5 => 'RATE PER UNIT TYPE',
            6 => 'RATE PER TOTAL AWB',
            7 => 'RATE PER TOTAL BAGS',
            8 => 'RATE MONTHLY TOTAL BAGS'
        ];

        /* ================= DATA ROWS ================= */

        $i = 1;
        foreach ($data as $row) {
            if($row->effective_to == '0000-00-00' || $row->effective_to = ''){
                $row->effective_to = '';
            }
            $customer_codes = '';
            if (!empty($row->customer_id)) {
                $customers = $this->db ->select('code') ->where_in('id', explode(',', $row->customer_id))->get('registration')->result_array();
                $customer_codes = implode(',', array_column($customers, 'code'));
            }
            $service_codes = '';
            if (!empty($row->service_id)) {
                $services = $this->db ->select('code') ->where_in('id', explode(',', $row->service_id))->get('service')->result_array();
                $service_codes = implode(',', array_column($services, 'code'));
            }
            $vendor_codes = '';
            if (!empty($row->vendor_id)) {
                $vendors = $this->db ->select('code') ->where_in('id', explode(',', $row->vendor_id))->get('vendor')->result_array();
                $vendor_codes = implode(',', array_column($vendors, 'code'));
            }
            fputcsv($output, [
                $i++,
                $row->charge_name ?? '',
                $row->billing_type ?? '',
                $row->fixed_amount ?? '',
                $row->min_amount ?? '',
                $row->percentage_on_freight ?? '',
                $row->percentage_on_shipment_value ?? '',
                $row->min_chargeable_weight ?? '',
                $row->max_chargeable_weight ?? '',
                $row->min_actual_weight ?? '',
                $row->max_actual_weight ?? '',
                $row->min_volume_weight ?? '',
                $row->max_volume_weight ?? '',
                $row->min_no_of_boxes ?? '',
                $row->max_no_of_boxes ?? '',
                $row->min_per_box_actual_weight ?? '',
                $row->max_per_box_actual_weight ?? '',
                $row->min_dimension ?? '',
                $row->max_dimension ?? '',
                ($row->is_origin_oda == 1) ? "TRUE" : "FALSE",
                ($row->is_destination_oda == 1) ? "TRUE" : "FALSE",
                ($row->min_dimension_per_pcs  == 1)  ? "TRUE" : "FALSE",
                ($row->max_dimension_per_pcs  == 1)  ? "TRUE" : "FALSE",
                $customer_codes,
                $service_codes,
                $vendor_codes,
                !empty($row->effective_from)
                    ? date('d-m-Y', strtotime($row->effective_from))
                    : '',
                !empty($row->effective_to)
                    ? date('d-m-Y', strtotime($row->effective_to))
                    : '',
                $rateModeMap[$row->rate_mod] ?? '',
                ($row->status == 1 ? 'Active' : 'Inactive')
            ]);
        }

        fclose($output);
        exit;
    }
    
    public function export_xpressbees_pincode()
    {
        $data = $this->supportmodel->show('xpressbees_pincode', $order = 'DESC');

        $filename = 'Xpressbees_Pincode_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, [ '#', 'PINCODE', 'STATE', 'ODA', 'SALE_ZONE', 'ODA_KM' ]);

        $i = 1;
        foreach ($data as $row) {

            fputcsv($output, [
                $i++,
                $row->pincode ?? '',
                $row->state ?? '',
                ($row->oda == 1 ? 'ODA' : 'NON ODA'), // readable format
                $row->sale_zone ?? '',
                $row->oda_km ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    public function export_xpressbees_price()
    {
        $data = $this->supportmodel->show('xpressbees_pincode', $order = 'DESC');

        $filename = 'Xpressbees_Pincode_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, [ '#', 'PINCODE', 'STATE', 'ODA', 'SALE_ZONE', 'ODA_KM' ]);

        $i = 1;
        foreach ($data as $row) {

            fputcsv($output, [
                $i++,
                $row->pincode ?? '',
                $row->state ?? '',
                ($row->oda == 1 ? 'ODA' : 'NON ODA'), // readable format
                $row->sale_zone ?? '',
                $row->oda_km ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    public function export_exception()
    {
        $data = $this->supportmodel->show('exception_master', $order = 'DESC');

        $filename = 'ExceptionMaster_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');

        header(
            'Content-Disposition: attachment; filename="' .
            $filename .
            '"'
        );

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            '#',
            'Status Code',
            'Description',
            'Add In NDR',
            'Create Ticket',
            'Ticket Department',
            'Ticket Priority',
            'Status',
            'Created At'
        ]);

        $i = 1;

        foreach ($data as $row) {

            fputcsv($output, [

                $i++,

                $row->status_code ?? '',

                $row->desc ?? '',

                ($row->is_ndr == 1) ? 'Yes' : 'No',

                ($row->create_ticket == 1) ? 'Yes' : 'No',

                $row->ticket_department ?? '',

                $row->ticket_priority ?? '',

                ($row->status == 1) ? 'Active' : 'Inactive',

                $row->created_at ?? ''

            ]);
        }

        fclose($output);

        exit;
    }
    
    public function pod_export()
    {

        $data  =   $this->supportmodel->show_condition('order_waybills','id',array('awb_status'=>'Delivered'));

        $filename = 'POD_Report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            '#',
            'Customer Name',
            'Shipper',
            'Consignee',
            'AWB',
            'Service',
            'Delivery Date',
            'POD Link'
        ]);

        // Maps
       // $registration = $this->db->get('registration')->result();
        $registration = $this->supportmodel->show('registration');
        $registration_map = [];
        foreach ($registration as $row) {
            $registration_map[$row->id] = $row;
        }

        $pickup = $this->supportmodel->show('pickup');
        $pickup_map = [];
        foreach ($pickup as $row) {
            $pickup_map[$row->id] = $row;
        }

        $address =  $this->supportmodel->show('address');
        $address_map = [];
        foreach ($address as $row) {
            $address_map[$row->id] = $row;
        }

        $service = $this->supportmodel->show('service');
        $service_map = [];
        foreach ($service as $row) {
            $service_map[$row->id] = $row;
        }

        $i = 1;

        foreach ($data as $row) {
         
            $pod_link = '=HYPERLINK("'.site_url('jobstatus/pod_details_get/'.$row->lrnum).'","View")';
            fputcsv($output, [

                $i++,

                isset($registration_map[$row->login_id]) ? $registration_map[$row->login_id]->company : 'N/A',

                isset($pickup_map[$row->pickup]) ? $pickup_map[$row->pickup]->nickname : 'N/A',

                isset($address_map[$row->customer_id]) ? $address_map[$row->customer_id]->name : 'N/A',

                $row->lrnum,

                isset($service_map[$row->service_id]) ? $service_map[$row->service_id]->name : 'N/A',

                $row->delivered,
                 
                $pod_link

            ]);
        }

        fclose($output);
        exit;
    }

    public function export_software_screen_time_report(){

        $data = $this->supportmodel->show('user_screen_time', $order = 'DESC');

        $users = $this->supportmodel->show('users');

        $user_map = [];
        foreach ($users as $user) {
            $user_map[$user->id] = $user->userName;
        }

        $filename = 'Screen_report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');

        header(
            'Content-Disposition: attachment; filename="' .
            $filename .
            '"'
        );

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            '#',
            'User',
            'Module Name',
            'Page Title',
            'Page URL',
            'Start Time',
            'Last Active Time'
            
        ]);

        $i = 1;

        foreach ($data as $row) {
            $user_name = isset($user_map[$row->user_id]) ? $user_map[$row->user_id] : '';
            fputcsv($output, [
                $i++,
                $user_name,
                $row->module_name ?? '',
                $row->page_title ?? '',
                $row->page_url ?? '',
                $row->start_time ?? '',
                $row->last_active_time ?? ''
            ]);
        }

        fclose($output);

        exit;
    }

    public function export_user_login_activity(){

        $data = $this->supportmodel->show('user_login_activity', $order = 'DESC');

        $filename = 'Screen_report_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');

        header(
            'Content-Disposition: attachment; filename="' .
            $filename .
            '"'
        );

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            '#',
            'User Name',
            'Login As',
            'login Time',
            'Logout Time',
            'Status'
            
        ]);

        $i = 1;

        foreach ($data as $row) {
           
            fputcsv($output, [
                $i++,
                $row->name,
                $row->login_as ?? '',
                $row->login_time ?? '',
                $row->logout_time ?? '',
                $row->status ?? ''
            ]);
        }

        fclose($output);

        exit;
    }

}