<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <img src="{{ asset('assets/img/icons/favicon.ico') }}" alt="Ícono" class="app-brand-icon" style="width: 20px; height: 20px; margin-right: 8px;">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">TODO LIST</span>
        </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- Dashboard -->
        @can('Modulo_Dashboard')
            <li class="menu-item" id="dashboard-link">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
            </li>
        @endcan


        <!-- Utilidades -->
        @can('Modulo_Utilidades')

            <li class="menu-header small text-uppercase"><span class="menu-header-text">Utilidades</span></li>

            <!-- Usuarios -->
            <li class="menu-item" id="dashboard-link">
                <a href="{{ route('usuarios') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bxs-user-detail"></i>
                    <div data-i18n="Analytics">Usuarios</div>
                </a>
            </li>

            <!-- Roles y Permisos -->
            <li class="menu-item" id="roles-permisos-link">
                <a href="javascript:void(0)" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-toggle-left'></i>
                    <div data-i18n="Extended UI">Roles y Permisos</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="roles-link">
                    <a href="{{ route('roles') }}" class="menu-link">
                        <div data-i18n="Perfect Scrollbar">Roles</div>
                    </a>
                    </li>
                    <li class="menu-item" id="permisos-link">
                    <a href="{{ route('permisos') }}" class="menu-link">
                        <div data-i18n="Text Divider">Permisos</div>
                    </a>
                    </li>
                </ul>
            </li>

            <!-- Logs -->
            <li class="menu-item" id="log-link">
                <a href="{{ route('logs') }}" class="menu-link" target="_blank">
                    <i class="menu-icon tf-icons bx bx-ghost"></i>
                    <div data-i18n="Analytics">Logs</div>
                </a>
            </li>

        @endcan


      </ul>
      </aside>

      <script>
        document.addEventListener("DOMContentLoaded", function() {
          const currentUrl = window.location.href;
          const menuItems = document.querySelectorAll('.menu-item');

          menuItems.forEach(item => {
            const link = item.querySelector('.menu-link');

            if (link && currentUrl.includes(link.href)) {
              item.classList.add('active');

              let ancestor = item.closest('.menu-item');
              while (ancestor) {
                ancestor.classList.add('open');
                ancestor.classList.add('active');
                ancestor = ancestor.parentElement.closest('.menu-item');
              }
            }
          });
        });
      </script>
