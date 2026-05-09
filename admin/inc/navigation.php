<!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-light-primary elevation-4 border-right shadow-sm" style="background: #ffffff !important;">
        <!-- Brand Logo -->
        <a href="<?php echo base_url ?>admin" class="brand-link border-bottom p-3" style="background: #ffffff !important;">
        <img src="<?php echo validate_image($_settings->info('logo'))?>" alt="Store Logo" class="brand-image img-circle" style="opacity: .8;width: 2.5rem;height: 2.5rem;max-height: unset">
        <span class="brand-text font-weight-bold text-primary"><?php echo $_settings->info('short_name') ?></span>
        </a>
        <style>
            .main-sidebar .nav-link {
                color: #4b5563 !important;
                border-radius: 0.75rem !important;
                margin-bottom: 5px;
                padding: 10px 15px !important;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            .main-sidebar .nav-sidebar .nav-link:hover,
            .main-sidebar .nav-sidebar .nav-link:hover p,
            .main-sidebar .nav-sidebar .nav-link:hover i {
                color: #4361ee !important;
                background: rgba(67, 97, 238, 0.1) !important;
            }
            .main-sidebar .nav-sidebar .nav-link.active,
            .main-sidebar .nav-sidebar .nav-link.active p,
            .main-sidebar .nav-sidebar .nav-link.active i {
                background: #4361ee !important;
                color: #ffffff !important;
                box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
            }
            .main-sidebar .nav-header {
                color: #9ca3af !important;
                font-weight: 700 !important;
                text-transform: uppercase;
                font-size: 0.7rem !important;
                letter-spacing: 1px;
                margin-top: 15px !important;
            }
            .brand-link {
                border-bottom: 1px solid #f3f4f6 !important;
            }
        </style>
        <!-- Sidebar -->
        <div class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-transition os-host-scrollbar-horizontal-hidden">
          <div class="os-resize-observer-host observed">
            <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
          </div>
          <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
            <div class="os-resize-observer"></div>
          </div>
          <div class="os-content-glue" style="margin: 0px -8px; width: 249px; height: 646px;"></div>
          <div class="os-padding">
            <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow-y: scroll;">
              <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">
                <!-- Sidebar user panel (optional) -->
                <div class="clearfix"></div>
                <!-- Sidebar Menu -->
                <nav class="mt-4">
                   <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent nav-collapse-hide-child" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item dropdown">
                      <a href="./" class="nav-link nav-home">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                          Dashboard
                        </p>
                      </a>
                    </li> 
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=analysis" class="nav-link nav-analysis">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>
                          Analysis
                        </p>
                      </a>
                    </li> 
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=budget" class="nav-link nav-budget">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>
                          Budget Management
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=expense" class="nav-link nav-expense">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>
                          Expense Management
                        </p>
                      </a>
                    </li>
                    <li class="nav-header">Reports</li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=reports/budget" class="nav-link nav-reports_budget">
                        <i class="nav-icon fas fa-file"></i>
                        <p>
                          Budget Report
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=reports/expense" class="nav-link nav-reports_expense">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                          Expense Report
                        </p>
                      </a>
                    </li>
                    <?php if($_settings->userdata('type') == 1): ?>
                    <li class="nav-header">Maintenance</li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=maintenance/department" class="nav-link nav-maintenance_department">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                          Department List
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=maintenance/category" class="nav-link nav-maintenance_category">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>
                          Category List
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=user/list" class="nav-link nav-user_list">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                          User List
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=system_info" class="nav-link nav-system_info">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                          Settings
                        </p>
                      </a>
                    </li>
                    <li class="nav-item dropdown">
                      <a href="<?php echo base_url ?>admin/?page=backup" class="nav-link nav-backup">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                          Database Backup
                        </p>
                      </a>
                    </li>
                    <?php endif; ?>
                  </ul>
                </nav>
                <!-- /.sidebar-menu -->
              </div>
            </div>
          </div>
          <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
            <div class="os-scrollbar-track">
              <div class="os-scrollbar-handle" style="width: 100%; transform: translate(0px, 0px);"></div>
            </div>
          </div>
          <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-auto-hidden">
            <div class="os-scrollbar-track">
              <div class="os-scrollbar-handle" style="height: 55.017%; transform: translate(0px, 0px);"></div>
            </div>
          </div>
          <div class="os-scrollbar-corner"></div>
        </div>
        <!-- /.sidebar -->
      </aside>
  <script>
    $(document).ready(function(){
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
      var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      
      // Clean up the page string for class matching
      var clean_page = page.replace(/\//g, '_');
      
      // Add active class to the direct link
      if($('.nav-link.nav-'+clean_page).length > 0){
          $('.nav-link.nav-'+clean_page).addClass('active')
          
          // If it's a child item, open the parent menu
          if($('.nav-link.nav-'+clean_page).closest('.nav-treeview').length > 0){
              $('.nav-link.nav-'+clean_page).closest('.nav-treeview').parent().addClass('menu-open')
              $('.nav-link.nav-'+clean_page).closest('.nav-treeview').siblings('a').addClass('active')
          }
      } else {
          // Fallback to first part of the path if full path doesn't match
          var parts = page.split('/');
          if($('.nav-link.nav-'+parts[0]).length > 0){
              $('.nav-link.nav-'+parts[0]).addClass('active')
          }
      }

      // Handle specific case for Dashboard (home)
      if(page == 'home' || page == ''){
          $('.nav-home').addClass('active')
      }
    })
  </script>