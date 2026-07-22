<?php include 'Secure.php';
class ActivityLog extends Secure{
    function __construct() {
        parent::__construct();
    }

     public function index($count = 0)
    {
        if(!in_array("Add Access Control", $GLOBALS['permission'])){
            return redirect("admin/login/dashboard");
        }

        $condition = '1=1';

        $config = array();

        $config["base_url"] = base_url() . "admin/activityLog/index";

        $config["total_rows"] = $this->supportmodel->getRows('activity_logs', $condition);

        $config["per_page"] = 10;

        $config["uri_segment"] = 4;

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

        $data["count"] = $count;

        $data["links"] = $this->pagination->create_links();

        $data['logs'] = $this->supportmodel ->show_limit( 'activity_logs', $config["per_page"], $page, 'DESC', $condition );

        $this->load->view( 'admin/header', ['page_title' => 'Activity Logs'] );

        $this->load->view('admin/master/activity_logs',$data);

        $this->load->view('admin/footer');
    }

    public function user_login_activity($count = 0)
    {
        // if (!in_array("User Login Activity Report", $GLOBALS['permission'])) {
        //     return redirect("admin/login/dashboard");
        // }

        $condition = '1=1';

        $config = array();
        $config["base_url"]    = base_url() . "admin/activityLog/user_login_activity";
        $config["total_rows"]  = $this->supportmodel->getRows('user_login_activity', $condition);
        $config["per_page"]    = 10;
        $config["uri_segment"] = 4;

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

        $data["count"] = $count;
        $data["links"] = $this->pagination->create_links();

        // user_login_activity table se records
        $data['code'] = $this->supportmodel->show_limit(
            'user_login_activity',
            $config["per_page"],
            $page,
            'DESC',
            $condition
        );

        $this->load->view('admin/header', ['page_title' => 'User Login Activity Report']);
        $this->load->view('admin/user_login_activity', $data);
        $this->load->view('admin/footer');
    }    

    public function software_screen_time_report($count = 0)
    {
        // if (!in_array("Software Screen Time Report", $GLOBALS['permission'])) {
        //     return redirect("admin/login/dashboard");
        // }

        $condition = '1=1';

        $config = array();
        $config["base_url"]    = base_url() . "admin/activityLog/software_screen_time_report";
        $config["total_rows"]  = $this->supportmodel->getRows('user_screen_time', $condition);
        $config["per_page"]    = 10;
        $config["uri_segment"] = 4;

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

        $data["count"] = $count;
        $data["links"] = $this->pagination->create_links();

        $data['code'] = $this->supportmodel->show_limit(
            'user_screen_time',
            $config["per_page"],
            $page,
            'DESC',
            $condition
        );
        
        
        $data['user_list'] = $this->supportmodel->show('users');
        $user_map = [];
        foreach ($data['user_list'] as $c) {
            $user_map[$c->id] = $c->userName;
        }
        $data['user_map'] = $user_map;  

        $this->load->view('admin/header', [
            'page_title' => 'Software Screen Time Report'
        ]);

        $this->load->view('admin/software_screen_time_report', $data);

        $this->load->view('admin/footer');
    }
}      