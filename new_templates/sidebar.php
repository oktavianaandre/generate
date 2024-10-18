<nav class="pcoded-navbar">
    <div class="pcoded-inner-navbar main-menu">
        <div class="pcoded-navigatio-lavel">eLine Process</div>

        <ul class="pcoded-item pcoded-left-item" >
        <?php if ($this->session->userdata('rolename') == 'Admin'){ ?>
            <li class=" ">
                <a href="<?php echo base_url() ?>C_Dashboard">
                    <span class="pcoded-micon"><i class="fas fa-home"></i></span>
                    <span class="pcoded-mtext">Dashboard</span>
                </a>
            </li>
           
            <!-- <li class=" ">
                <a href="<?php echo base_url() ?>C_document">
                    <span class="pcoded-micon"><i class="fab fa-dochub"></i></span>
                    <span class="pcoded-mtext">Document</span>
                </a>
            </li> -->

            <?php }?>
            
            <?php  foreach ($menu_akses as $value) {  ?>

            <?php  if($value->parentid=='') {  ?>
            <li class="pcoded-hasmenu">
                <a href="#">
                    <span class="pcoded-micon"><i class="fas <?php echo $value->icon ?>"></i></span>
                    <span class="pcoded-mtext"><?php echo $value->menu_name ?></span>
                </a>
                <ul class="pcoded-submenu">
                    <?php  foreach ($menu_akses as $value2) {  ?>

                    <?php  if($value2->parentid==$value->menu_id) {  ?>

                    <?php $menu_name = $value2->menu_name; ?>

                    <li class="">
                        <a
                            href="<?php echo base_url() ?><?php echo $value2->controller ?>?var=<?php echo $value2->menu_id ?>&var2=<?php echo $menu_name ?>">
                            <span class="pcoded-mtext"><?php echo $value2->menu_name ?></span>
                        </a>
                    </li>
                    <?php } ?>

                    <?php } ?>
                </ul>
            </li>
            <?php } ?>

            <?php } ?>
        </ul>


    </div>
</nav>