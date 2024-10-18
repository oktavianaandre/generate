</div>
            </div>
        </div>
    </div>

    <!-- Warning Section Starts -->
    <!-- Older IE warning message -->
    <!--[if lt IE 10]>
<div class="ie-warning">
    <h1>Warning!!</h1>
    <p>You are using an outdated version of Internet Explorer, please upgrade <br/>to any of the following web browsers to access this website.</p>
    <div class="iew-container">
        <ul class="iew-download">
            <li>
                <a href="http://www.google.com/chrome/">
                    <img src="<?php echo base_url() ?>files/assets/images/browser/chrome.png" alt="Chrome">
                    <div>Chrome</div>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/en-US/firefox/new/">
                    <img src="<?php echo base_url() ?>files/assets/images/browser/firefox.png" alt="Firefox">
                    <div>Firefox</div>
                </a>
            </li>
            <li>
                <a href="http://www.opera.com">
                    <img src="<?php echo base_url() ?>files/assets/images/browser/opera.png" alt="Opera">
                    <div>Opera</div>
                </a>
            </li>
            <li>
                <a href="https://www.apple.com/safari/">
                    <img src="<?php echo base_url() ?>files/assets/images/browser/safari.png" alt="Safari">
                    <div>Safari</div>
                </a>
            </li>
            <li>
                <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    <img src="<?php echo base_url() ?>files/assets/images/browser/ie.png" alt="">
                    <div>IE (9 & above)</div>
                </a>
            </li>
        </ul>
    </div>
    <p>Sorry for the inconvenience!</p>
</div>
<![endif]-->
    <!-- Warning Section Ends -->
    <!-- Required Jquery -->
    


        <!-- jQuery -->
    <!-- <script src="../../plugins/jquery/jquery.min.js"></script> -->
    <!-- Bootstrap 4 -->
    <scripttype="text/javascript" src="<?php echo base_url() ?>files/assets/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <!-- <script type="text/javascript" src="<?php echo base_url() ?>files/assets/js/adminlte.min.js"></script> -->
    <!-- AdminLTE for demo purposes -->
    <!-- <script type="text/javascript" src="<?php echo base_url() ?>files/assets/js/demo.js"></script> -->
       
    
    <!-- custom js -->
    <script type="text/javascript" src="<?php echo base_url() ?>files/assets/pages/advance-elements/select2-custom.js"></script>

    <!-- <script type="text/javascript" src="<?php echo base_url() ?>files/assets/pages/dashboard/custom-dashboard.js"></script> -->
    <script type="text/javascript" src="<?php echo base_url() ?>files/assets/js/script.min.js"></script>
</body>

</html>
<script>

      // Membuat konfigurasi toast untuk loading
      const Toasts = Swal.mixin({
          title: "Loading..",
          showCancelButton: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });

      // Fungsi untuk memulai loading
      function loading_start() {
          Toasts.fire();
      }

      // Fungsi untuk mengakhiri loading
      function loading_end() {
          Toasts.close();
      }

      // Fungsi untuk menampilkan pesan error
      function error_message() {
          Swal.fire({
              icon: "error",
              title: "Oops...",
              text: "Something went wrong!",
          });
      }

      // Fungsi untuk menampilkan pesan sukses
      function success_alert() {
          Swal.fire({
              icon: "success",
              title: "Your item has been saved",
              showConfirmButton: false,
              timer: 1500
          });
          // setTimeout(function() {
          //     swal.close();
          // }, 2000);
      }

</script>