<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_report extends CI_Model
{

   /// @see ajax_getbyhdrid()
   /// @note get data by hdrid
   /// @attention get all data from tb report audit by hdrid 
   function ajax_getbyhdrid($hdrid, $table)
   {
      return $this->db->get_where($table, array('hdrid' => $hdrid));
   }
 
   /// @see ajax_getbyhdrid_detail()
   /// @note get data by trxiddetail
   /// @attention get data by trxiddetail from tb report_audit_detail

   function cari_tb_approver($hdrid)
   {

       // $query = $this->db->query("select top 1 * from tb_approval where hdrid='$hdrid' and report_no_finding='$report_no_finding' and date_approve is null order by position_code asc");
       $query = $this->db->query("select top 1 * from tb_approval where hdrid='$hdrid' and date_approve is null order by position_code asc");
       if ($query->num_rows() > 0) {
           return $query->row();
       }else{
           $query = (object) array('nik'=>'not found','name'=>'All','office_email'=>'not found','position_code'=>'not found','position_name'=>'not found');
           return $query;
       } 

   }
   function cari_tb_master($hdrid, $table)
   {

      return $this->db->get_where($table, array('hdrid' => $hdrid));
   

   }

   function cari_tb_superior($nik)
   {
       // query select berdasarkan nik
       $query = $this->db->query("select * from tb_superior where nik='$nik'");
       
       if ($query->num_rows() > 0) {
           return $query->row();
       }else{
           $query = (object) array('nik'=>'not found');
           return $query;
       }

   }

   

   
}
