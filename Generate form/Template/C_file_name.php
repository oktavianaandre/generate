<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_@file_name extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/Role_guide/general/urls.html
	 **/
     /** ---------------------------------------------- Role----------------------------------------------**/

     public function __construct()
     {
         parent::__construct();

         $this->load->helper('date'); // Load helper date untuk fungsi tanggal
         $this->load->helper('file'); // Load helper file untuk fungsi upload
         $this->load->model('M_@file_name'); // Load model
         $this->load->model('M_email');
         $this->load->model('UserModel'); // untuk load user model hak akses menu
         $this->load->library('encryption'); // untuk enkripsi data

         // Cari hak akses berdasarkan controller
         $Hak_akses = $this->UserModel->get_controller_access($this->session->userdata('role_id'), 'C_@file_name');
         if ($Hak_akses->found != 'found') {
             redirect('Auth'); // Kembali ke halaman Auth
         }
     }

    /// @see index()
    /// @note Fungsi tampilan utama
    /// @attention Mengirim data yang diperlukan untuk view
    public function Index()
    {
        $menu_code = $this->input->get('var');  // Mengambil menu ID untuk dekripsi menu
        $menu_name = $this->input->get('var2');  // Mengambil menu name untuk dekripsi menu name
        $data['menu_name'] = $menu_name; // Mengirim menu_name ke view
        $data_akses['menu_akses'] = $this->UserModel->get_menu_access($this->session->userdata('role_id'));  // Mengambil menu akses untuk menampilkan menu
        $data['hak_akses'] = $this->UserModel->get_hak_access($this->session->userdata('role_id'), $menu_code); // Mengambil hak akses untuk tombol (Tambah, Edit, Lihat, Hapus, Import, Export)
        //@superior
        $this->load->view('new_templates/header'); // Menampilkan header
        $this->load->view('new_templates/sidebar', $data_akses); // Menampilkan sidebar dengan data akses
        $this->load->view('@file_name/V_@file_name', $data); // Menampilkan data
        $this->load->view('new_templates/footer'); // Menampilkan footer
    }

    /// @see view_data()
    /// @note Menampilkan data yang ada di table tanpa kondisi
    function view_data()
    {    
        $tables = "@tables";									
        @searchs

        $isWhere = null; // jika memakai IS NULL pada where sql
         // $isWhere = 'artikel.deleted_at IS NULL';
        header('Content-Type: application/json');  // Memberi tahu browser bahwa data dalam bentuk format json
        echo $this->M_@file_name->get_tables($tables,$search,$isWhere);
        
    }

    /// @see view_data_where()
    /// @note Menampilkan data yang ada di table 
    /// @attention Menampilkan data dengan kondisi tertentu
    // Satu table dengan where
    function view_data_where()
    {
        $tables = "@tables";								
        @searchs    
        

        if($_POST['searchByFromdate']==''||$_POST['searchByTodate']==''){
            $where  = array('transaction_date >'=>'2020-01-01');              
        }else{
            $where  = array('transaction_date >' => $_POST['searchByFromdate'],'transaction_date <' => $_POST['searchByTodate']);
        };

        $isWhere = null; // jika memakai IS NULL pada where sql
        // $isWhere = 'artikel.deleted_at IS NULL';
        header('Content-Type: application/json');  // Memberi tahu browser bahwa data dalam bentuk format json
        echo $this->M_@file_name->get_tables_where($tables,$search,$where,$isWhere);  // Mengambisl data dari database 
        
    }

    /// @see ajax_add() 
    /// @note Menambahkan Data
    /// @attention Menambahkan data baru ke database
    public function ajax_add()
	{

       // ********************* 0. Generate nomor transaksi  *********************          
       $mdate="TR".mdate('%Y%m',time());   // Membuat HDRID otomatis dengan format RL Tahun Bulan Jam (RL202210)                       
       $hdrid2=$this->M_@file_name->Max_data($mdate,'tb_@file_name')->row();    // Mengambil baris dari database       

       if ($hdrid2->hdrid==NULL){ // Jika HDRID belum ada
          $hdrid3=$mdate."001";// Maka mulai dari 001
       }else{
          $hdrid3=$hdrid2->hdrid; // Jika sudah ada 
          $hdrid3="TR".(intval(substr($hdrid3,2,10))+1); // Jika sudah ada maka naik 1 level
       };

        $hdrid=$hdrid3;
        // ********************* 1. Set hdrid  *********************
        $post_data2=array('hdrid'=>$hdrid3); // Membuat array untuk diposting ke field hdrid
        $post_data3=array('transaction_date'=>mdate('%Y%m%d',time())); // Membuat array untuk diposting ke field Transaction_date

         // ********************* 2. Kumpulkan semua data yang diposting *********************     
        $post_data = $this->input->post(); 
        
        //@caprov
        
        //@datetime

        //@sendc

             
        $msg = "Berhasil menyimpan";

        // ********************* 3. Gabungkan data yang diposting *********************        
        $post_datamerge=array_merge($post_data,$post_data2,$post_data3);

        // ********************** 4. Simpan data     *********************

        $this->M_@file_name->Input_Data($post_datamerge,'@tables');
        // ********************** 4. Upload file jika ada  *********************   
        //@attachc

        //@sendmail

        $data['status']=$msg;

        // return value array
        $this->output->set_content_type('application/json')->set_output(json_encode($data));

    }
    
    /// @see ajax_update()
    /// @note Mengupdate Data
    /// @attention Mengupdate data berdasarkan hdrid
    public function ajax_update()
    {
        // Mengumpulkan data yang diposting
        $post_data = $this->input->post();
        $hdrid = $this->input->post('hdrid');
        $msg = "Berhasil mengupdate";

        //@datetime
        // Mengupload file jika ada
        //@attachc

        // Menggabungkan data yang diposting
        $post_datamerge = $post_data;

        // Menyimpan data yang diupdate
        $where = array('hdrid' => $hdrid);
        $this->M_@file_name->Update_Data($where, $post_datamerge, '@tables');

        $data['status'] = "berhasil update";

        // Mengembalikan nilai dalam bentuk array
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /// @see ajax_delete()
    /// @note Menghapus Data
    /// @attention Menghapus data berdasarkan hdrid
    public function ajax_delete()
	{

        // Menyiapkan kondisi untuk menghapus data
        $where = array('hdrid' => $this->input->post('hdrid'));
        // Menghapus data berdasarkan kondisi yang telah disiapkan
        $this->M_@file_name->Delete_Data($where,'@tables');
        //@delete
        // Menyimpan status berhasil dihapus
        $data['status']="berhasil dihapus";
        // Mengembalikan nilai dalam bentuk array
        $this->output->set_content_type('application/json')->set_output(json_encode($data));

    }
    

    // Fungsi untuk mengambil data berdasarkan hdrid melalui AJAX
    function ajax_getid()
    {
        // Mengambil nilai hdrid dari input GET
        $hdrid = $this->input->get('hdrid');
        // Menggunakan model untuk mengambil data berdasarkan hdrid
        $data = $this->M_@file_name->ajax_getid($hdrid, '@tables')->row();
        // Mengembalikan data dalam format JSON
        echo json_encode($data);
    }

    /**
     * Fungsi untuk mengupload file dan mengupdate nama file ke dalam basis data.
     * 
     * param string $filename Nama field dari file yang diupload.
     * param string $hdrid ID dari header yang terkait dengan file.
     * param string $table Nama tabel basis data yang terkait dengan file.
     */
    function upload_file_attach($filename,$hdrid,$table)
    {

        // Cek apakah ada file yang diupload
        if(!empty($_FILES[$filename]['name']))
        {
            // Konfigurasi untuk upload file
            $config['upload_path'] = './assets/upload';  // Folder tujuan untuk upload file
            $config['allowed_types'] = '*'; // Tipe file yang diizinkan untuk diupload
            $config['overwrite'] = True; // Izinkan file untuk diubah
            $config['max_size']  = '2000000'; // Ukuran maksimal file yang diupload dalam bytes
            $config['max_width']  = '20000'; // Lebar maksimal file yang diupload dalam pixel
            $config['max_height']  = '20000'; // Tinggi maksimal file yang diupload dalam pixel
            $config['file_name']=$hdrid.'_'.$filename; // Nama file yang diupload dengan format hdrid dan nama field
            $this->load->library('upload', $config); // Load library untuk upload file
            $this->upload->initialize($config); 

            // Proses upload file
            if ( ! $this->upload->do_upload($filename)){
                $msg = $this->upload->display_errors();  // Tampilkan pesan error jika gagal upload
            }
            else{
                $msg = "success upload"; // Tampilkan pesan sukses jika berhasil upload

                $dataupload = $this->upload->data(); // Dapatkan data dari file yang diupload
                $data_update = array($filename =>$dataupload['file_name']); // Buat array untuk update basis data
                
                $where = array('hdrid' => $hdrid);  // Buat array untuk kondisi di query model
                $this->M_@file_name->Update_Data($where,$data_update,$table); // Kirim semua parameter ke model untuk update basis data
                $data['status']=$msg; // Simpan status ke dalam array untuk dikembalikan
            };

        };

    }

    // Fungsi untuk membaca data dari file Excel dan mengembalikannya dalam format JSON
    public function ajax_read_excel() {
        // Memuat library Excel untuk mengolah file Excel
        $this->load->library('excel');
        
        // Mendapatkan nama file yang diupload
        $fileName = $_FILES['excelFile']['name'];
        
        // Konfigurasi untuk upload file Excel
        $config['upload_path'] = './uploads/'; // Path untuk menyimpan file Excel yang diupload
        $config['allowed_types'] = 'xls|xlsx'; // Tipe file Excel yang diizinkan untuk diupload
        $this->load->library('upload', $config); // Memuat library upload dengan konfigurasi
        
        // Proses upload file Excel
        if ($this->upload->do_upload('excelFile')) {
            $fileData = $this->upload->data(); // Mendapatkan data file yang diupload
            $inputFileName = './uploads/' . $fileData['file_name']; // Menyimpan path file yang diupload
            
            // Membaca file Excel yang diupload
            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
            $data = []; // Inisialisasi array untuk menyimpan data
            
            // Mengiterasi setiap baris dalam sheet Excel
            foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Mendapatkan semua sel dalam baris
                
                $rowData = []; // Inisialisasi array untuk menyimpan data per baris
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue(); // Menyimpan nilai sel ke dalam array
                }
                $data[] = $rowData; // Menyimpan data per baris ke dalam array utama
            }
            echo json_encode($data); // Mengembalikan data dalam format JSON
        } else {
            echo json_encode(['error' => $this->upload->display_errors()]); // Mengembalikan pesan error jika gagal upload
        }
    }

    // Fungsi untuk menyimpan data ke database melalui AJAX
    public function ajax_save_data() {
        // Mengambil data yang dikirim melalui POST
        $data = $this->input->post('data');
        
        // Melakukan loop untuk setiap baris data yang dikirim
        foreach ($data as $row) {
            // Contoh: Simpan data ke tabel 'your_table'
            // Sesuaikan dengan nama kolom yang ada di basis data
            $this->db->insert('your_table', [
                'column1' => $row[0], // Kolom pertama
                'column2' => $row[1], // Kolom kedua
                // Tambahkan kolom lainnya sesuai kebutuhan
            ]);
        }
        
        // Mengembalikan respons JSON untuk menandakan proses berhasil
        echo json_encode(['success' => true]);
    }


    /// @see import()
    /// @note Import data 
    /// @attention Import data excel ke database
    public function import() {

        // Tidak ada validasi form karena tidak ada form validation rules yang ditentukan
        // $this->form_validation->set_rules('excel', 'File', 'trim|required');

        // Cek apakah file excel telah diupload
        if ($_FILES['excel']['name'] == '') {

            $this->session->set_flashdata('msg', 'File harus diisi');
            // redirect('C_@file_name'); // Kembali ke halaman C_@file_name

        } else {

            // Konfigurasi untuk upload file excel
            $config['upload_path'] = './assets/excel/'; // Folder untuk menyimpan file yang diupload
            $config['allowed_types'] = 'xls|xlsx'; // Tipe file yang diperbolehkan untuk diupload
            
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            
            // Proses upload file
            if ( ! $this->upload->do_upload('excel')){
                $error = array('error' => $this->upload->display_errors());
            }
            else{

                $data = $this->upload->data();
                
                // Konfigurasi untuk PHPExcel
                error_reporting(E_ALL);
                date_default_timezone_set('Asia/Jakarta');

                include './assets/phpexcel/Classes/PHPExcel/IOFactory.php';

                $inputFileName = './assets/excel/' .$data['file_name'];
                $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

                $index = 0;
                $rowexcel = 1;

                foreach ($sheetData as $key => $value) {

                    // Proses untuk setiap baris data excel
                    if($rowexcel>1){

                        // Generate nomor transaksi untuk setiap baris data
                        if($index==0)
						{
                            $mdate="TR".mdate('%Y%m',time());        
                            $hdrid2=$this->M_@file_name->Max_data($mdate,'tb_@file_name')->row();  
							
                            if ($hdrid2->hdrid==NULL){ // Jika belum ada
								$hdrid3=$mdate."001"; // Maka mulai dari 001
							}else{
                                $hdrid3=$hdrid2->hdrid;// Jika sudah ada
                                $hdrid3="TR".(intval(substr($hdrid3,2,10))+1); //maka naik 1 level
							}

                            $hdrid=$hdrid3;

							$resultData[$index]['hdrid'] =   $hdrid3;  
							$resultData[$index]['transaction_date'] = mdate('%Y-%m-%d',time());    

							// Import data ke database
							// Tidak ada detail implementasi karena tidak ada konteks yang jelas
							@excel_imports
							$index++;
						}

                    }
                    
                    $rowexcel++;

                }

                // Menghapus file excel yang telah diupload
                unlink('./assets/excel/' .$data['file_name']);

                // Cek apakah ada data yang berhasil diimport
                if (count($resultData) != 0) {
                    $result = $this->M_@file_name->insert_batch('tb_@file_name',$resultData);
                        // $this->session->set_flashdata('msg', show_succ_msg('Data Legalitas Perusahaan Berhasil diimport ke database'));
                        redirect('C_@file_name'); // Kembali ke halaman C_@file_name
                } else {
                        // $this->session->set_flashdata('msg', show_msg('Data Legalitas Perusahaan Gagal diimport ke database (Data Sudah terupdate)', 'warning', 'fa-warning'));
                        redirect('C_@file_name'); // Kembali ke halaman C_@file_name
                }

            }
        }
    }

    /**
     * Fungsi untuk mengexport data ke Excel berdasarkan template dan tanggal yang dipilih.
     */
    public function ajax_export_template() {
        $this->load->database();
    
        $query = $this->db->limit(1)->get('tb_@file_name');
        $data = $query->row_array();
    
        if (empty($data)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['error' => 'No data found in the database.']));
            return;
        }
    
        $mapping = [
            @export_excel
        ];
    
        $fileName = $this->write_excel_template($data, $mapping);
        
        // Construct the full URL to the file
        $file_url = base_url('uploads/' . $fileName);
    
        $this->output->set_content_type('application/json')->set_output(json_encode(['file_url' => $file_url]));
    }
    
    public function write_excel_template($data, $mapping) {
        date_default_timezone_set('Asia/Jakarta');
        if (!class_exists('PHPExcel')) {
            include './assets/phpexcel/Classes/PHPExcel.php';
        }
    
        $templatePath = './excel/template.xlsx';
    
        if (!file_exists($templatePath)) {
            throw new Exception("Template file not found: " . $templatePath);
        }
    
        $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
        $sheet = $objPHPExcel->getActiveSheet();
    
        foreach ($mapping as $field => $cell) {
            if (isset($data[$field])) {
                $sheet->setCellValue($cell, $data[$field]);
            }
        }
    
        $fileName = 'export_data_' . date('YmdHis') . '.xlsx';
        $filePath = './uploads/' . $fileName;
    
        if (!is_dir('./uploads/')) {
            mkdir('./uploads/', 0777, true);
        }
    
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($filePath);
    
        return $fileName; // Return only the file name
    }
    

    /** ---------------------------------------------- /Close controller----------------------------------------------**/

}
