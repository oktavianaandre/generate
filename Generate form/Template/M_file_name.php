<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_@file_name extends CI_Model {
      
    /// @see get_tables()
   /// @note 
   /// @attention 
   function get_tables($tables,$cari,$iswhere)
        {
            // Ambil data yang di ketik user pada textbox pencarian
            $search = htmlspecialchars($_POST['search']['value']);
            // Ambil data limit per page
            $limit = (int)$_POST['length']; // Menggunakan casting untuk performa
            // Ambil data start
            $start = (int)$_POST['start']; // Menggunakan casting untuk performa
            
            // Menyiapkan nama tabel untuk query
            $query = $tables;
            // Menjalankan query untuk menghitung jumlah total data
            $sql = !empty($iswhere) ? $this->db->query("SELECT * FROM ".$query." WHERE ".$iswhere) : $this->db->query("SELECT * FROM ".$query);
            $sql_count = $sql->num_rows();

            // Menyiapkan kondisi untuk filter data berdasarkan inputan user
            $cari = implode(" LIKE '%".$search."%' OR ", $cari)." LIKE '%".$search."%'";
            // Mengambil kolom dan arah urutan dari inputan DataTables
            $order_field = $_POST['order'][0]['column']; 
            $order_ascdesc = $_POST['order'][0]['dir']; 
            // Menyiapkan klausa ORDER BY untuk mengatur urutan data
            $order = " ORDER BY ".$this->db->escape($_POST['columns'][$order_field]['data'])." ".$order_ascdesc; // Menggunakan escape untuk keamanan

            // Menggunakan query builder untuk efisiensi dalam mengatur query
            $this->db->start_cache();
            $this->db->from($query);
            // Jika ada kondisi WHERE, maka tambahkan ke query
            if (!empty($iswhere)) {
                $this->db->where($iswhere);
            }
            // Menambahkan kondisi where untuk filter data berdasarkan inputan user
            $this->db->where("($cari)");
            // Mengatur batas data yang akan ditampilkan dan mulai dari data ke berapa
            $this->db->limit($limit, $start);
            // Mengatur urutan data berdasarkan kolom yang dipilih dan arah urutan
            $this->db->order_by($_POST['columns'][$order_field]['data'], $order_ascdesc);
            // Menjalankan query untuk mengambil data
            $sql_data = $this->db->get();
            // Mengubah hasil query menjadi array untuk lebih mudah diolah
            $data = $sql_data->result_array();
            // Menghapus cache query untuk mengoptimalkan kinerja
            $this->db->flush_cache();

            // Menghitung jumlah total data setelah filter dengan menggunakan fungsi count_all_results untuk efisiensi
            $sql_filter_count = $this->db->count_all_results(); 

            // Membuat array callback untuk mengembalikan data ke DataTables dalam format JSON
            $callback = array(    
                'draw' => $_POST['draw'],    // Nomor draw yang dikirim oleh DataTables
                'recordsTotal' => $sql_count,    // Jumlah total data tanpa filter
                'recordsFiltered' => $sql_filter_count,    // Jumlah total data setelah filter
                'data' => $data    // Data yang akan ditampilkan dalam DataTables
            );
            // Mengembalikan data dalam format JSON
            return json_encode($callback); 
        }


    /// @see get_tables_where()
   /// @note 
   /// @attention 
   function get_tables_where($tables,$cari,$where,$iswhere)
   {
       // Ambil data yang di ketik user pada textbox pencarian
       $search = htmlspecialchars($_POST['search']['value']);
       // Ambil data limit per page
       $limit = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST['length']}");
       // Ambil data start
       $start = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST['start']}"); 

       $setWhere = array();
       foreach ($where as $key => $value)
       {
           $setWhere[] = $key."='".$value."'";
       }

       $fwhere = implode(' AND ', $setWhere);

       if(!empty($iswhere)){
           $sql = $this->db->query("SELECT * FROM ".$tables." WHERE $iswhere AND ".$fwhere);
       }else{
           $sql = $this->db->query("SELECT * FROM ".$tables." WHERE ".$fwhere);
       }
       $sql_count = $sql->num_rows();

       $query = $tables;
       $cari = implode(" LIKE '%".$search."%' OR ", $cari)." LIKE '%".$search."%'";
       
       // Untuk mengambil nama field yg menjadi acuan untuk sorting
       $order_field = $_POST['order'][0]['column']; 

       // Untuk menentukan order by "ASC" atau "DESC"
       $order_ascdesc = $_POST['order'][0]['dir']; 
       $order = " ORDER BY ".$_POST['columns'][$order_field]['data']." ".$order_ascdesc;

       if(!empty($iswhere)){
           $sql_data = $this->db->query("SELECT * FROM ".$query." WHERE $iswhere AND ".$fwhere." AND (".$cari.")".$order." OFFSET ".$start." ROWS FETCH NEXT ". $limit . " ROWS only ");
       }else{
           $sql_data = $this->db->query("SELECT * FROM ".$query." WHERE ".$fwhere." AND (".$cari.")".$order." OFFSET ".$start." ROWS FETCH NEXT ". $limit . " ROWS only ");
       }

       if(isset($search))
       {
           if(!empty($iswhere)){
               $sql_cari =  $this->db->query("SELECT * FROM ".$query." WHERE $iswhere AND ".$fwhere." AND (".$cari.")");
           }else{
               $sql_cari =  $this->db->query("SELECT * FROM ".$query." WHERE ".$fwhere." AND (".$cari.")");
           }
           $sql_filter_count = $sql_cari->num_rows();
       }else{
           if(!empty($iswhere)){
               $sql_filter = $this->db->query("SELECT * FROM ".$tables." WHERE $iswhere AND ".$fwhere);
           }else{
               $sql_filter = $this->db->query("SELECT * FROM ".$tables." WHERE ".$fwhere);
           }
           $sql_filter_count = $sql_filter->num_rows();
       }

       $data = $sql_data->result_array();
       
       $callback = array(    
           'draw' => $_POST['draw'], // Ini dari datatablenya    
           'recordsTotal' => $sql_count,    
           'recordsFiltered'=>$sql_filter_count,    
           'data'=>$data
       );
       return json_encode($callback); // Convert array $callback ke json
   }
      
      /// @see get_tables_query()
     /// @note 
     /// @attention 
      function get_tables_query($query,$cari,$where,$iswhere)
      {
          // Ambil data yang di ketik user pada textbox pencarian
          $search = htmlspecialchars($_POST['search']['value']);
          // Ambil data limit per page
          $limit = (int)$_POST['length']; // Menggunakan casting untuk performa
          // Ambil data start
          $start = (int)$_POST['start']; // Menggunakan casting untuk performa

          $setWhere = array();
          if ($where != null) {
              foreach ($where as $key => $value) {
                  $setWhere[] = $this->db->escape($key) . "='" . $this->db->escape($value) . "'"; // Menggunakan escape untuk keamanan
              }
          }
          $fwhere = implode(' AND ', $setWhere);
          
          // Menggunakan query builder untuk efisiensi
          $this->db->start_cache();
          $this->db->from($query);
          if (!empty($iswhere)) {
              $this->db->where($iswhere);
          }
          if (!empty($fwhere)) {
              $this->db->where($fwhere);
          }
          $sql_count = $this->db->count_all_results(); // Menghitung jumlah total dengan lebih efisien
          $this->db->flush_cache();

          $cari = implode(" LIKE '%".$search."%' OR ", $cari)." LIKE '%".$search."%'";
          $this->db->start_cache();
          $this->db->from($query);
          if (!empty($iswhere)) {
              $this->db->where($iswhere);
          }
          if (!empty($fwhere)) {
              $this->db->where($fwhere);
          }
          $this->db->where("($cari)");
          $this->db->limit($limit, $start);
          $order_field = $_POST['order'][0]['column'];
          $order_ascdesc = $_POST['order'][0]['dir'];
          $this->db->order_by($_POST['columns'][$order_field]['data'], $order_ascdesc);
          $sql_data = $this->db->get();
          $data = $sql_data->result_array();
          $this->db->flush_cache();

          $sql_filter_count = $this->db->count_all_results(); // Menghitung jumlah filter dengan lebih efisien

          $callback = array(    
              'draw' => $_POST['draw'],    
              'recordsTotal' => $sql_count,    
              'recordsFiltered' => $sql_filter_count,    
              'data' => $data
          );
          return json_encode($callback); // Convert array $callback ke json
      }     


  /// @see Max_data()
   /// @note 
   /// @attention 
   public function Max_data($mdate,$table)
   {
      // Mengambil nilai maksimum dari kolom 'hdrid'
      $this->db->select_max('hdrid');     
      // Menyaring data 'hdrid' yang mengandung nilai dari variabel 'mdate'
      $this->db->like('hdrid', $mdate);
      // Menjalankan query untuk mengambil data dari tabel 'table'
      $query = $this->db->get($table);      
      // Mengembalikan hasil query
      return  $query;

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
   function cari_approval($hdrid)
   {

       // $query = $this->db->query("select top 1 * from tb_approval where hdrid='$hdrid' and report_no_finding='$report_no_finding' and date_approve is null order by position_code asc");
       $query = $this->db->query("select top 1 * from tb_approval where hdrid='$hdrid' and date_approve is null and position_code = '1' order by position_code asc");
       if ($query->num_rows() > 0) {
           return $query->row();
       }else{
           $query = (object) array('nik'=>'not found','name'=>'All','office_email'=>'not found','position_code'=>'not found','position_name'=>'not found');
           return $query;
       } 
   }
    
   /// @see Input_Data()
   /// @note 
   /// @attention 
   function Input_Data($data,$table){
      $this->db->insert($table,$data);
      
   }

   /// @see Update_Data()
   /// @note 
   /// @attention 
   function Update_Data($where,$data,$table){
      $this->db->where($where);
      $this->db->update($table,$data);
   }
      
   /// @see Delete_Data()
   /// @note 
   /// @attention 
   function Delete_Data($where,$table){
      $this->db->where($where);
      $this->db->delete($table);
   }

   /// @see ajax_getbyrole_id()
   /// @note 
   /// @attention 
   function ajax_getid($id,$table){      
      return $this->db->get_where($table, array('hdrid' => $id));
   }
   /// @see get_tb_menu()
   /// @note 
   /// @attention 
   Public Function get_tb_menu()
   {
      $Query = $this->db->query("SELECT * FROM a_menu ORDER BY menu_id ASC");
      return  $Query->result();
              
   }
   //@maprov
   /**
     * Fungsi untuk mengambil data antara dua tanggal.
     *
     * @param string $fromDate Tanggal awal.
     * @param string $toDate Tanggal akhir.
     * @return array Data yang diambil dari database.
     */
    public function get_data_between_dates($fromDate, $toDate) {
        // Menambahkan kondisi WHERE untuk filter data berdasarkan tanggal awal dan akhir
        $this->db->where('transaction_date >=', $fromDate);
        $this->db->where('transaction_date <=', $toDate);
        // Menjalankan query untuk mengambil data dari tabel yang sesuai
        $query = $this->db->get('@tables'); // Ganti '@tables' dengan nama tabel yang sesuai
        // Mengembalikan hasil query sebagai array objek
        return $query->result();
    }

    // Fungsi untuk menyimpan data dalam batch
    public function insert_batch($table, $data) {
        // Memasukkan data ke dalam tabel
        return $this->db->insert_batch($table, $data);
    }

    // Tambahkan fungsi lain sesuai kebutuhan model
    }

?>



