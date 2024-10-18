<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class C_Email extends CI_Controller {

    public function __construct() {

        parent:: __construct();
        $this->load->model('M_email');
        $this->load->model('M_report');
        $this->load->helper('date'); // Load helper date fungsi tanggal
    }
    
    function Send_mail_reject() {

        $hdrid=$this->input->post('hdrid');
        $this->M_email->Send_mail_reject($hdrid); // Ambil dari database central
    }

    public function ajax_approve()
	{
        date_default_timezone_set('Asia/Jakarta'); // Set timezone

        // ********************* 1. Collect data post **************************************
        $hdrid=$this->input->post('hdrid');                                 // get data by hdrid
        $nik=$this->input->post('nik');                                     // get data nik
        $nik_admin=$this->input->post('nik_admin');                                     // get data nik
        $position_code=$this->input->post('position_code');                 // get data position code
        $date_approve = mdate('%Y-%m-%d %H:%i:%s',time());
        
        $post_data = array('date_approve' => $date_approve);  // date approve
        $where = array('hdrid' => $hdrid,                           // berdasarkan hdrid
        'nik' => $nik,                                  // berdasarkan nik approver
        'position_code' => $position_code);             // bersaarkan position code approver
        
        $this->M_email->Update_Data($where,$post_data,'tb_approval');     // update data tb approver
        
        $last_update_transaction =mdate('%Y-%m-%d %H:%i:%s',time()) ;
        $next_approver = $this->M_report->cari_tb_approver($hdrid);
        $superior = $this->M_report->cari_tb_superior($nik_admin);


        if ($next_approver->nik=='not found'){

        $status_transaction ="Done";
        $post_data1 = array('status_transaction' => $status_transaction,
                            'last_update_transaction' => $last_update_transaction,
                            'status' => 'Closed');
        $where_doc = array('hdrid'=>$hdrid);
        $this->M_email->Update_Data($where_doc,$post_data1,'tb_@file_name');     // update data tb approver

        $email_data = array(
            'hdrid'=>$hdrid,
            'transaction_date'=>mdate('%Y-%m-%d %H:%i:%s',time()),
            'nik'=>$superior->nik,
            'name'=>$superior->name,
            'department_code'=>$superior->kode_section,
            'department_name'=>$superior->section,
            // 'office_email'=>$superior->email_pic,
            'position_code'=>'Closed',
            'position_name'=>'Closed',
            'subject_email'=>"Finish Approval",
            'body_content'=>'Closed',
            'comment'=> '',
            'cc_email'=>'oktavianaandree@gmail.com');
            // 'cc_email'=>$superior->email_pic.';'.$superior->email_admin.';'.$superior->email_superior.';'.$superior->email_superior2.';'.$superior->email_mr);

        $this->M_email->send_mail($email_data);

        }else{

            $status_transaction ="Waiting Approve by " .$next_approver->name ." (" . $next_approver->position_name .')' ;
            $post_data1 = array('status_transaction' => $status_transaction,
                                'last_update_transaction' => $last_update_transaction,
                                'status' => 'On Progress');
            $where_doc = array('hdrid'=>$hdrid);
            $this->M_email->Update_Data($where_doc,$post_data1,'tb_@file_name');

            $email_data = array(
                'hdrid'=>$hdrid,
                'transaction_date'=>mdate('%Y-%m-%d %H:%i:%s',time()),
                'nik'=>$next_approver->nik,
                'name'=>$next_approver->name,
                'department_code'=>$next_approver->department_code,
                'department_name'=>$next_approver->department_name,
                // 'office_email'=>$next_approver->office_email,
                'office_email'=>'oktavianaandree@gmail.com',
                'position_code'=>$next_approver->position_code,
                'position_name'=>$next_approver->position_name,
                'subject_email'=>"Need Approve " .$next_approver->name ." (" . $next_approver->position_name .')',
                'body_content'=>'Waiting Approve :',
                'comment'=> '',
                'cc_email'=>'oktavianaandree@gmail.com');
                // 'cc_email'=>$superior->email_pic.';'.$superior->email_admin.';'.$superior->email_superior.';'.$superior->email_superior2.';'.$superior->email_mr);
    
            $this->M_email->send_mail($email_data);
        }

        $data['status']="berhasil approve";

        // return value array
        $this->output->set_content_type('application/json')->set_output(json_encode($data));

    }

    public function ajax_reject()
	{

        date_default_timezone_set('Asia/Jakarta'); // Set timezone

        $hdrid = $this->input->post('hdrid');
        $reason = $this->input->post('reason');
        $rejected_by = $this->input->post('rejected_by');
        $position_name = $this->input->post('position_name');
        $nik_admin = $this->input->post('nik_admin');
        
        $post_data = array('date_approve' => NULL); 
        $superior = $this->M_report->cari_tb_superior($nik_admin);

        $where = array('hdrid' => $hdrid);    // condition where hdrid and report no finding
        $this->M_email->Update_Data($where,$post_data,'tb_approval');  // update data tb approval

        $status_transaction = "Rejected By : ".$rejected_by ." - " .$position_name ." With Reason : ".$reason ; 
        $last_update_transaction =mdate('%Y-%m-%d %H:%i:%s',time()) ; 
        $post_data2 = array('reason' => $reason,
                            'status_transaction' =>  $status_transaction,
                            'last_update_transaction' => $last_update_transaction,
                            'status' => 'rejected'
        );   

        $where = array('hdrid' => $hdrid);      // condition where hdrid and report no finding
        $this->M_email->Update_Data($where,$post_data2,'tb_@file_name');  // update data tb report audit detail

        $email_data = array(
            'hdrid'=>$hdrid,
            'transaction_date'=>mdate('%Y-%m-%d %H:%i:%s',time()),
            'nik'=>$superior->nik,
            'name'=>$superior->name,
            'department_code'=>$superior->kode_section,
            'department_name'=>$superior->section,
            'office_email'=>'oktavianaandree@gmail.com',
            // 'office_email'=>$superior->email_pic,
            'position_code'=>'',
            'position_name'=>'PIC Admin',
            'subject_email'=>"Rejected By : ".$rejected_by ." - " .$position_name ." With Reason : ".$reason,
            'body_content'=>'Rejected :',
            'comment'=> $reason,
            'cc_email'=>'oktavianaandree@gmail.com');
            // 'cc_email'=>$superior->email_pic.';'.$superior->email_admin.';'.$superior->email_superior.';'.$superior->email_superior2.';'.$superior->email_mr);

        $this->M_email->send_mail($email_data);
        $data['status']="berhasil reject";

        // return value array
        $this->output->set_content_type('application/json')->set_output(json_encode($data));

    }
  
}