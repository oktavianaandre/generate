<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_report extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/welcome
     *  - or -
     *      http://example.com/index.php/welcome/index
     *  - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     **/
     /** ---------------------------------------------- report_audit----------------------------------------------**/

    
     /// @see __construct()
    /// @note load model
    /// @attention fungsi load model
    public function __construct()
    {
        parent::__construct();   

        if ($this->session->userdata('user_name')=="") {
            redirect('Auth');
        }

        $this->load->helper('date');  // load date
        $this->load->helper('file');        // load file
        $this->load->model('M_report');   // load model
        $this->load->model('M_email');   // load model
        // $this->load->library('encrypt');    

        $this->load->model('UserModel');    // load model

        // Cari hak akses by controller
	    $Hak_akses = $this->UserModel->get_controller_access($this->session->userdata('role_id'),'C_report_audit_eksternal'); 
	    if($Hak_akses->found!='found') {
		    redirect('Auth'); // Kembali ke halaman Auth
	    }
                
    } 

    /// @see index()
    /// @note fungsi tampil data
    /// @attention tarik dan tampil data untuk select filter
    public function Index()
    {

        $hdrid = $this->input->get('var1');      // get hdrid
        // var_dump($hdrid);
        // $v_hdrid = $this->input->get('var1');      // get hdrid for link email

        $menu_code = $this->input->get('var');                  // Decrypt menu ID   untuk dekrip menu   
        // $menu_name = $this->input->get('var2');                 // Decrypt menu ID   untuk dekrip menu name  
        // $data['menu_name'] =  $menu_name; 
        // $menu_akses['menu_akses']=$this->UserModel->get_menu_access($this->session->userdata('role_id'));           //Menu akses untuk munculkan menu   
        // $data['hak_akses']=$this->UserModel->get_hak_access($this->session->userdata('role_id'), $menu_code);       //button akses(Add,Adit,View,Delete,Import,Export)
        
        $data['hak_akses']=$this->UserModel->get_hak_access($this->session->userdata('role_id'), $menu_code);       //button akses(Add,Adit,View,Delete,Import,Export)

  

        $data['hdrid'] = $hdrid ;       // hdrid

               
    }

    public function doc()
    {
    $hdrid = $this->input->get('var1');
    $data['trx']=$hdrid;
    $data['nik']=$this->M_report->cari_tb_approver($hdrid);
    $data['tb_master']=$this->M_report->cari_tb_master($hdrid, 'tb_@file_name')->row();

    $this->load->view('report/V_report',$data); // Tampil data


    }

    /// @see view_data_where()
    /// @note fungsi tampil data report audit dengan where
    /// @attention tampil data dengan where
   

}
   