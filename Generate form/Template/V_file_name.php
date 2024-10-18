<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">

                <div class="page-body">
                    <div class="card">
                        <div class="card-header">
                            <h5><?php echo $menu_name?></h5>

                            <div class="card-header-right">
                                <ul class="list-unstyled card-option">
                                    <li><i class="feather icon-maximize full-card"></i></li>
                                    <li><i class="feather icon-minus minimize-card"></i></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-block">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <?php if (!empty($hak_akses)) { ?>
                                            <!-- Jika allow add = true -->
                                            <?php if ($hak_akses->allow_add=='on') { ?>

                                            <!-- menampilkan tombol add data-->
                                            <a data-bs-toggle="modal" data-bs-target="#modal-default"  Onclick="view_modal('1','Add')" href="#">
                                              <i class="fa fa-plus"></i> Add Data
                                            </a>

                                            <?php } ?>
                                            <!-- /Jika allow add = true -->
                                            <?php } ?>
                                            <a data-bs-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                                aria-expanded="true" aria-controls="collapseOne">
                                                <i class="fa fa-binoculars"></i> Custom Filter
                                            </a>&nbsp
                                            <a id="reset">
                                                <i class="fa fa-undo-alt"></i> Reset Filter
                                            </a>
                                            <!-- Tambahan untuk import dan export -->
                                            <a data-bs-toggle="modal" data-bs-target="#import-modal" href="#">
                                                <i class="fa fa-file-import"></i> Import Data
                                            </a>
                                            <!-- <a id="export" href="#" onclick="exportToExcel()">
                                                <i class="fa fa-file-excel"></i> Export to Excel
                                            </a>
                                            <a id="export" href="#" onclick="exportToExcelTemplate()">
                                                <i class="fa fa-file-excel"></i> Export to Excel Template
                                            </a> -->
                                        </div>
                                        <div class="card-block accordion-block">
                                            <div id="accordion" role="tablist" aria-multiselectable="true">
                                                <div class="accordion-panel">

                                                    <div id="collapseOne" class="panel-collapse collapse in"
                                                        role="tabpanel" aria-labelledby="headingOne">
                                                        <div class="accordion-content accordion-desc">

                                                            <h4 class="sub-title">Select Date</h4>
                                                            <div class="row">
                                                                <div class="col-md-5">
                                                                    <input class="form-control" type="date" name="start"
                                                                        id="search_fromdate" />
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="input-group-text">
                                                                        <span class="input-group-addon">To</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input class="form-control" type="date" name="end"
                                                                        id="search_todate" />

                                                                </div>
                                                            </div>

                                                            <div class="d-grid">
                                                                <button class="btn btn-primary m-t-20"
                                                                    data-bs-toggle="collapse" data-parent="#accordion"
                                                                    href="#collapseOne" aria-expanded="true"
                                                                    aria-controls="collapseOne" id="search"> Search
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">

                                </div>
                                <div class="card-block">
                                    <div class="table-responsive">
                                        <div class="dt-responsive table-responsive">
                                            <table id="example1"
                                                class="table table-bordered table-hover display nowrap table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        @th
                                                        
                                                        <!-- @status -->
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--card-block-->
                    </div>
                    <!--card-->
                </div>
                <!--page-body-->
                <div class="modal fade" id="modal-default" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Add</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"></span>
                                </button>
                            </div>
                            
                            <div class="modal-body">
                                <!-- form -->
                                <form class="form-horizntal" id="quickForm" role="form">

                                    <div class="card-body">
                                        <div class="container-fluid">
                                           @form
                                        </div><!-- Close Container -->
                                    </div><!-- Close Card Body -->

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger waves-effect "
                                            data-bs-dismiss="modal">Close
                                        </button>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light "
                                            id="btnsubmit">Save
                                        </button>
                                    </div>

                                </form>
                                <!-- /form  -->
                            </div>

                        </div>
                        <!-- Close modal-content -->
                    </div>
                    <!-- Close modal-dialog -->
                </div>

                <!-- Modal Import Data -->
                <div class="modal fade" id="import-modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="exampleModalLabel">Import Data Excel</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"></span>
                                </button>
                            </div>
                            <div class="modal-body">
                            <form method="POST" enctype="multipart/form-data" action="<?php echo base_url('C_@file_name/import'); ?>">
                                    <div class="form-group">
                                        <label for="excelFile">Pilih File Excel</label>
                                        <input type="file" class="form-control" id="excel" name="excel" accept=".xls,.xlsx" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <button type="button" class="form-control btn btn-primary"> 
                                            <a href="./assets/excel/Template.xlsx" download="Template.xlsx">
                                            <i class="fa fa-download" style="color:white"></i><font color="white"> Template Import Data</font>
                                            </a>
                                        </button>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-primary">Impor</button>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div id="styleSelector">

                </div>
            </div>
            <!--page-wrapper-->

        </div>
        <!--main-body-->
    </div>
    <!--pcoded-inner-content-->
</div>
<!--pcoded-content-->

<script type="text/javascript">
$(document).ready(function() {

    $.validator.setDefaults({
        submitHandler: function() {

            //success_alert( "Form successful submitted!" );
            var status = $('#exampleModalLabel').text();

            if (status == "Add Data") {

                // Ajax insert data
                Simpan_data("Add");

            } else if (status == "Edit Data") {

                // Ajax update data
                Simpan_data("Update");

            } else {

                success_alert(status);

            }

        }
    });

    $('#quickForm').validate({
        rules: {
            @rules
        },
        messages: {
            @messages
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>


<script type="text/javascript">
// Untuk Add,Edit,delete.

function view_modal(hdrid1, status) {


    if (status == "Add") {
        $('#exampleModalLabel').text('Add Data'); // name view
        $('#quickForm')[0].reset(); // reset form 
        //@modal
        //@hide
        $('#btnsubmit').text('Save'); // name Save
        //@adds  
        document.getElementById("btnsubmit").style.visibility = "visible"; // Visible button              
        //Ajax kosongkan data

    } else if (status == "Edit" || status == "View") {

        // Get data

        if (status == "View") {
            document.getElementById("btnsubmit").style.visibility = "hidden";
            $('#exampleModalLabel').text('View Data'); //name view  
            //@view            
        } else {
            $('#exampleModalLabel').text('Edit Data'); //name view 
            $('#btnsubmit').text('Update'); //name Update  
            //@view
            document.getElementById("btnsubmit").style.visibility = "visible";
        }

        $('#hdrid').val(hdrid1);
        var hdrid = hdrid1;
        var form_data_edit = $('#quickForm').serializeArray();
        var field;
        var fieldvalue;

        // Ajax isi data
        $.ajax({
            url: "<?php echo base_url('C_@file_name/ajax_getid')?>",
            method: "get",
            dataType: "JSON",
            data: {
                hdrid: hdrid
            },
            success: function(data) {
 @data_vals
            },
            error: function(e) {
                alert(e);

            }
        });


    } else {

        // Get data
        $('#exampleModalLabel').text('View Data'); // Name view                        
        document.getElementById("btnsubmit").style.visibility = "hidden"; // Hidden button     
        vreadonly('#quickForm', true); // Function Read Only form = True           
        // alert(data_form("#quickForm"));

    }

}
</script>

<script>
//@attachv
</script>

<script type="text/javascript">
function Simpan_data($trigger) {

    // Form data
    var fdata = new FormData();

    // Form data collect name value
    var form_data = $('#quickForm').serializeArray();
    $.each(form_data, function(key, input) {
        // if (input.value !== ''){
        // fdata.append(input.name, input.value);
        // }else{
        //   fdata.append(input.name, null);
        // }
        fdata.append(input.name, input.value);
    });

    // if ($('#my_images').val()) {
    //     var file_data = $('#my_images').prop('files')[0];
    //     fdata.append("file", file_data);
    // } else {
    //     // alert('Please Upload File');
    // }
    // $('#quickForm input[type="file"]').each(function(i, e) {
    //     // jika ada file attach maka akan ditambahkan  
    //     if ($('#' + e.getAttribute("name")).val()) {
    //         var file_data = $('#' + e.getAttribute("name")).prop('files')[0];
    //         fdata.append(e.getAttribute("name"), file_data);
    //     }
    // });

    $('#quickForm input[type="file"]').each(function(i, e) {
        // jika ada file attach maka akan ditambahkan  
        if ($('#' + e.getAttribute("name")).val()) {
            var file_data = $('#' + e.getAttribute("name")).prop('files')[0];
            fdata.append(e.getAttribute("name"), file_data);
        }
    });
    // Print_r(file_data);

    // Simpan or Update data
    var vurl;
    if ($trigger == 'Add') {
        vurl = "<?php echo base_url('C_@file_name/ajax_add')?>";
    } else {
        vurl = "<?php echo base_url('C_@file_name/ajax_update')?>";
    }

    $.ajax({
        url: vurl,
        method: "post",
        processData: false,
        contentType: false,
        data: fdata,
        beforeSend: function() {
            loading_start();
        },
        success: function(data) {
            loading_end();
            $('#modal-default').modal('hide');
            // Pesan success_alert
            success_alert();
            // Reset Form
            $('#quickForm')[0].reset();
            // location.reload();
            tabel.draw('page');

        },
        error: function(e) {
            gagal(e);
            //location.reload();
            //error
        }
    });

}
function Delete_data(id) {

// Form data
var fdata = new FormData();

// Delete by Hdrid
fdata.append('hdrid', id);
// Url Post delete
vurl = "<?php echo base_url('C_@file_name/ajax_delete')?>";

// Post data
$.ajax({
    url: vurl,
    method: "post",
    processData: false,
    contentType: false,
    data: fdata,
    beforeSend: function() {
        loading_start();

    },
    success: function(data) {
        loading_end();
        // Hide modal delete
        // $('#modal-delete').modal('hide');
        // Delete rows datatables
        $('#example1').DataTable().row("#" + id).remove().draw('page');
        // Pesan berhasil
        Swal.fire({
            title: "Deleted!",
            text: "Your item has been deleted.",
            icon: "success"
        });
    },
    error: function(e) {
        //Pesan Gagal
        gagal(e);
        swal("error", "Your item is safe  :)", "error");
    }
});

}

function Send_mail(){

var fdata = new FormData();

fdata.append("hdrid", $('#hdrid').val());
fdata.append("hdrid", $('#hdrid').val());

// Form data collect name value
var form_data = $('#quickForm').serializeArray();
$.each(form_data, function(key, input) {
        fdata.append(input.name, input.value);
});

$.ajax({
    url: "<?php echo base_url('C_email/ajax_sendmail')?>",
    method: "post",
    processData: false,
    contentType: false,
    data: fdata,
    success: function(data) {

        berhasil("E-mail Sent");
        tabel.draw();
        $("#modal-default").modal('hide');
        $("#btnsend").text("Send");

    },
    error: function(e) {
        // gagal;

        berhasil("Email terkirim");

        tabel.draw();

        $("#modal_default").modal('hide');


    }
});
}


</script>

<!-- ***************************** Handle Button di datatable (View,edit,delete,row selected) ***************************** -->
<script type="text/javascript">
//  HDRID selected konfirmasiHapus
$(document).on("click", ".konfirmasiHapus", function() {
    // $('#iddelete').text($(this).attr("data-id"));
    validasi_delete($(this).attr("data-id"));
})

//  HDRID selected  konfirmasiEdit
$(document).on("click", ".konfirmasiEdit", function() {
    view_modal($(this).attr("data-id"), 'Edit');
})

//  HDRID selected  konfirmasiEdit
$(document).on("click", ".konfirmasiView", function() {
    view_modal($(this).attr("data-id"), 'View');
})

$(document).on("click", ".konfirmasiExport", function() {
    exportToExcelTemplate($(this).attr("data-id"), 'Export');
})

// ID Rows selected
$('#example1').on('click', 'tr', function() {
    $('#iddelete2').text($('#example1').DataTable().row(this).id());
});
</script>


<script type="text/javascript">
$('.select2').select2();

//Date range picker
// $('#reservationdate').datetimepicker({
//     format: 'L'
// });


// //Date range picker
// $('#startdate').datetimepicker({
//     format: 'L'
// });

// //Date range picker
// $('#enddate').datetimepicker({
//     format: 'L'
// });
</script>

<!-- <script type="text/javascript">
  document.getElementById('btnsubmit2').onclick = function() {
    var select = document.getElementById('multiple');
    var selected = [...select.options]
                      .filter(option => option.selected)
                      .map(option => option.value);
    alert(selected);
  } 
</script> -->

<!-- Handle datatable -->
<script type="text/javascript">
var tabel = null;
$(document).ready(function() {

    tabel = $('#example1').DataTable({
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "ordering": true, // Set true agar bisa di sorting
        "order": [
            [0, 'asc']
        ], // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
        dom: "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" + 
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", // Menambahkan dom untuk menampilkan tombol
        // buttons: [
        //     {
        //         extend: 'excelHtml5',
        //         className: 'btn btn-success',
        //         text: '<i class="fas fa-file-excel">&nbsp</i> Export Data to Excel',
        //         customize: function(xlsx) {
        //             var sheet = xlsx.xl.worksheets['sheet1.xml'];

        //             // $('row b', sheet).attr('s', '25');
        //             // Tambahkan styling atau kustomisasi lain di sini jika diperlukan
        //         }
        //     }
        // ],
        "ajax": {
            "url": "<?= base_url('C_@file_name/view_data_where');?>", // URL file untuk proses select datanya
            "type": "POST",
            "data": function(data) {
                data.searchByFromdate = $('#search_fromdate').val();
                data.searchByTodate = $('#search_todate').val();
            }

        },
        "deferRender": true,
        "aLengthMenu": [
            [5, 10, 100, 1000, 10000, 100000, 1000000, 1000000000],
            [5, 10, 100, 1000, 10000, 100000, 1000000, "All"]
        ], // Combobox Limit
        "columns": [{
                "data": 'hdrid',
                "sortable": false, //1
                render: function(data, type, row, meta) {
                    // return meta.row + meta.settings._iDisplayStart + 1;
                    // return '<div class="btn btn-success btn-sm konfirmasiView" data-id="'+ data +'" data-toggle="modal" data-target="#modal-default" > <i class="fa fa-eye"></i></div> <div class="btn btn-danger btn-sm konfirmasiHapus" data-id="'+ data +'" data-toggle="modal" data-target="#modal-delete" > <i class="fa fa-trash"></i></div> <div class="btn btn-primary btn-sm konfirmasiEdit" data-id="'+ data +'" data-toggle="modal" data-target="#modal-default"> <i class="fa fa-edit"></i></div>';

                    mnu = '';
                    mnu = mnu +
                        '<div type="button" class="btn btn-success btn-icon rounded-pill konfirmasiView" data-id="' +
                        data +
                        '" data-bs-toggle="modal" data-bs-target="#modal-default" > <span class="fa fa-eye"></span></div>|';

                    //Tombol Edit
                    <?php if(!empty($hak_akses)){ if ($hak_akses->allow_edit=='on') { ?>
                    mnu = mnu +
                        '<div type="button" class="btn btn-secondary btn-icon rounded-pill konfirmasiEdit" data-id="' +
                        data +
                        '" data-bs-toggle="modal" data-bs-target="#modal-default"> <span class="fa fa-edit"></span></div>|';
                    <?php } } ?>

                    //Tombol Delete
                    <?php if(!empty($hak_akses)){ if ($hak_akses->allow_delete=='on') { ?>
                    mnu = mnu +
                        '<div type="button" class="btn btn-danger btn-icon rounded-pill konfirmasiHapus" data-id="' +
                        data +
                        '"  > <span class="fa fa-trash"></span></div>|';
                    <?php } } ?>

                    //Tombol Export Template
                    <?php if(!empty($hak_akses)){ if ($hak_akses->allow_export=='on') { ?>
                        mnu = mnu +
                        '<div type="button" class="btn btn-primary btn-icon rounded-pill konfirmasiExport" data-id="' +
                        data +
                        '"  > <span class="fa fa-file-excel"></span></div>|';
                    <?php } } ?>

                    @btntabel

                    return mnu;
                }
            },

            @datatables
            //@transct

        ],
    });

    // Search button
    $('#search').click(function() {


        if ($('#search_fromdate').val() != '' && $('#search_todate').val() != '') {
            tabel.draw('page');
        } else {
            gagal("Both Date is Required");
        }

    });
    $('#reset').click(function() {

        $('#search_fromdate').val(''); 
        $('#search_todate').val('');
        tabel.draw('page');

});

});
</script>
<script type="text/javascript">
    $('#importForm').on('submit', function(e) {
        e.preventDefault(); // Mencegah pengiriman form default
        var formData = new FormData(this); // Mengambil data form

        $.ajax({
            url: "<?php echo base_url('C_@file_name/ajax_import')?>", // URL untuk proses import
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                loading_start(); // Menampilkan loading
            },
            success: function(data) {
                loading_end(); // Menghentikan loading
                $('#import-modal').modal('hide'); // Menutup modal
                success_alert("Data berhasil diimpor!"); // Pesan sukses
                tabel.draw('page'); // Mengupdate tabel
            },
            error: function(e) {
                loading_end(); // Menghentikan loading
                gagal(e); // Menampilkan pesan gagal
            }
        });
        
    });

</script>

<script type="text/javascript">
// function handleSelectChange_dept(event) {

// var data = $('#kode_department').select2('data')[0].text;

//   if (data=='-Select-'){
//     $('#nama_department').val('');   
//   }else{
//     var res = data.split("-");
//     $('#nama_department').val(res[1]);
//   };


// }

// function handleSelectChange_role(event) {


// var data = $('#role_id').select2('data')[0].text;

//   if (data=='-Select-'){
//     $('#role_name').val('');   
//   }else{
//     var res = data.split("-");
//     $('#role_name').val(res[1]);
//   };



// }

// function handleSelectChange_nik(event) {

//   var data = $('#nik').select2('data')[0].text;

//   if (data=='-Select-'){
//     $('#username').val('');   
//   }else{
//     var res = data.split("-");
//     $('#username').val(res[1]);
//   };



// }
</script>

<script type="text/javascript">
function vreadonly(form, vboolean) {

    // alert(form);

    // var x = $(form).serializeArray();

    // $.each(x, function(i, field){

    //   if(field.name=="hdrid"){
    //     if(vboolean==true){
    //       document.getElementsByName(field.name)[0].readOnly=true;
    //     }else{
    //       document.getElementsByName(field.name)[0].readOnly=false;
    //     }
    //   }


    // });


}
</script>
<script>
// $(document).ready(function() {
//     bsCustomFileInput.init();



// });
function validasi_delete(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "Delete item " + id,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            Delete_data(id);

        }
    });
}
</script>

<script type="text/javascript">
    // Fungsi untuk mengexport data ke Excel berdasarkan tanggal yang dipilih
    function exportToExcel() {
        // Mengambil nilai tanggal awal dan akhir dari inputan
        var fromDate = $('#search_fromdate').val();
        var toDate = $('#search_todate').val();

        // Mengirim data filter ke server untuk proses export
        $.ajax({
            url: "<?php echo base_url('C_@file_name/ajax_export')?>", // URL untuk mengakses fungsi ajax_export di controller
            method: "POST", // Metode pengiriman data menggunakan POST
            data: {
                searchByFromdate: fromDate, // Tanggal awal yang dipilih
                searchByTodate: toDate // Tanggal akhir yang dipilih
            },
            success: function(data) {
                // Mengunduh file Excel yang dihasilkan
                window.location.href = data.file_url; // Ganti dengan URL file yang dihasilkan
            },
            error: function(e) {
                gagal(e); // Menangani kesalahan yang terjadi
            }
        });
    }
</script>

<script type="text/javascript">
    function exportToExcelTemplate() {
        var fromDate = $('#search_fromdate').val();
        var toDate = $('#search_todate').val();

        Swal.fire({
        title: 'Loading...',
        text: 'Please wait while we process your request.',
        allowOutsideClick: false,
        onBeforeOpen: () => {
            Swal.showLoading();
        }
    });

        // Mengirim data filter ke server untuk proses export
        $.ajax({
            url: "<?php echo base_url('C_coba/ajax_export_template')?>",
            method: "POST",
            data: {
                searchByFromdate: fromDate,
                searchByTodate: toDate
                
            },
            beforeSend: function() {
            // Menampilkan loading saat permintaan dikirim
            Swal.showLoading();
        },
            success: function(data) {
                // Mengunduh file Excel yang dihasilkan
                Swal.fire({
                title: "Success!",
                text: "Your item has been Export.",
                icon: "success"
                });
                // Mengunduh file Excel yang dihasilkan
                window.location.href = data.file_url; // Ganti dengan URL file yang dihasilkan
            },
            error: function(e) {
                gagal(e);
            }
        });
    }
</script>