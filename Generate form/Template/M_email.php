<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_email extends CI_Model {
   
   
    /// @see Send_mail()
   /// @note
   /// @attention
   function send_mail($email_data)
    {
        sqlsrv_configure('WarningsReturnAsErrors', 0);                  // hapus pesan error pada exec procedure
        
        // jalankan store procedure
        $query = $this->db->query("
            sp_send_mail 
                @hdrid='$email_data[hdrid]',
                @nik='$email_data[nik]',
                @name='$email_data[name]',
                @department_code='$email_data[department_code]',
                @department_name='$email_data[department_name]',
                @email='$email_data[office_email]',
                @position_code='$email_data[position_code]',
                @position_name='$email_data[position_name]',
                @EmailSubject='$email_data[subject_email]',
                @Email_body_content='$email_data[body_content]',
                @comment='$email_data[comment]',
                @copy_to='$email_data[cc_email]'
        ");
    }

   function Update_Data($where,$data,$table){
    $this->db->where($where);
    $this->db->update($table,$data);
 }


}






