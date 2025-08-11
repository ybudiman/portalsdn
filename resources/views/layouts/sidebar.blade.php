 <!-- Menu -->

 <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
     <div class="app-brand demo">
         <a href="index.html" class="app-brand-link">
             <span class="app-brand-logo demo">
                 <i class="ti ti-fingerprint" style="font-size:32px !important"></i>
                 {{-- <img src="{{ asset('assets/img/logo/hibah.png') }}" alt="" width="64"> --}}
             </span>
             <span class="app-brand-text demo menu-text fw-bold"><i><b>e</b></i>PresensiV2</span>
         </a>

         <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
             <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
             <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
         </a>
     </div>

     <div class="menu-inner-shadow"></div>

     <ul class="menu-inner py-1">
         <!-- Dashboards -->
         <li class="menu-item {{ request()->is(['dashboard', 'dashboard/*']) ? 'active' : '' }}">
             <a href="{{ route('dashboard.index') }}" class="menu-link">
                 <i class="menu-icon tf-icons ti ti-home"></i>
                 <div>Dashboard</div>
             </a>
         </li>
         @if (auth()->user()->hasAnyPermission(['karyawan.index', 'departemen.index', 'cabang.index', 'cuti.index', 'jamkerja.index', 'jabatan.index']))
             <li
                 class="menu-item {{ request()->is(['karyawan', 'karyawan/*', 'departemen', 'cabang', 'cuti', 'jamkerja', 'jabatan']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-database"></i>
                     <div>Data Master</div>

                 </a>
                 <ul class="menu-sub">
                     @can('karyawan.index')
                         <li class="menu-item {{ request()->is(['karyawan', 'karyawan/*']) ? 'active' : '' }}">
                             <a href="{{ route('karyawan.index') }}" class="menu-link">
                                 <div>Karyawan</div>
                             </a>
                         </li>
                     @endcan
                     @can('departemen.index')
                         <li class="menu-item {{ request()->is(['departemen', 'departemen/*']) ? 'active' : '' }}">
                             <a href="{{ route('departemen.index') }}" class="menu-link">
                                 <div>Departemen</div>
                             </a>
                         </li>
                     @endcan
                     @can('jabatan.index')
                         <li class="menu-item {{ request()->is(['jabatan', 'jabatan/*']) ? 'active' : '' }}">
                             <a href="{{ route('jabatan.index') }}" class="menu-link">
                                 <div>Jabatan</div>
                             </a>
                         </li>
                     @endcan
                     @can('cabang.index')
                         <li class="menu-item {{ request()->is(['cabang', 'cabang/*']) ? 'active' : '' }}">
                             <a href="{{ route('cabang.index') }}" class="menu-link">
                                 <div>Cabang</div>
                             </a>
                         </li>
                     @endcan
                     @can('cuti.index')
                         <li class="menu-item {{ request()->is(['cuti', 'cuti/*']) ? 'active' : '' }}">
                             <a href="{{ route('cuti.index') }}" class="menu-link">
                                 <div>Cuti</div>
                             </a>
                         </li>
                     @endcan
                     @can('jamkerja.index')
                         <li class="menu-item {{ request()->is(['jamkerja', 'jamkerja/*']) ? 'active' : '' }}">
                             <a href="{{ route('jamkerja.index') }}" class="menu-link">
                                 <div>Jam Kerja</div>
                             </a>
                         </li>
                     @endcan


                 </ul>
             </li>
         @endif
         @if (auth()->user()->hasAnyPermission([
                     'gajipokok.index',
                     'jenistunjangan.index',
                     'tunjangan.index',
                     'bpjskesehatan.index',
                     'bpjstenagakerja.index',
                     'penyesuaiangaji.index',
                 ]))
             <li
                 class="menu-item {{ request()->is(['gajipokok', 'jenistunjangan', 'tunjangan', 'bpjskesehatan', 'bpjstenagakerja', 'penyesuaiangaji', 'penyesuaiangaji/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-moneybag"></i>
                     <div>Payroll</div>

                 </a>
                 <ul class="menu-sub">
                     @can('jenistunjangan.index')
                         <li class="menu-item {{ request()->is(['jenistunjangan', 'jenistunjangan/*']) ? 'active' : '' }}">
                             <a href="{{ route('jenistunjangan.index') }}" class="menu-link">
                                 <div>Jenis Tunjangan</div>
                             </a>
                         </li>
                     @endcan
                     @can('gajipokok.index')
                         <li class="menu-item {{ request()->is(['gajipokok', 'gajipokok/*']) ? 'active' : '' }}">
                             <a href="{{ route('gajipokok.index') }}" class="menu-link">
                                 <div>Gaji Pokok</div>
                             </a>
                         </li>
                     @endcan
                     @can('tunjangan.index')
                         <li class="menu-item {{ request()->is(['tunjangan', 'tunjangan/*']) ? 'active' : '' }}">
                             <a href="{{ route('tunjangan.index') }}" class="menu-link">
                                 <div>Tunjangan</div>
                             </a>
                         </li>
                     @endcan
                     @can('bpjskesehatan.index')
                         <li class="menu-item {{ request()->is(['bpjskesehatan', 'bpjskesehatan/*']) ? 'active' : '' }}">
                             <a href="{{ route('bpjskesehatan.index') }}" class="menu-link">
                                 <div>BPJS Kesehatan</div>
                             </a>
                         </li>
                     @endcan
                     @can('bpjstenagakerja.index')
                         <li class="menu-item {{ request()->is(['bpjstenagakerja', 'bpjstenagakerja/*']) ? 'active' : '' }}">
                             <a href="{{ route('bpjstenagakerja.index') }}" class="menu-link">
                                 <div>BPJS Tenaga Kerja</div>
                             </a>
                         </li>
                     @endcan
                     @can('penyesuaiangaji.index')
                         <li class="menu-item {{ request()->is(['penyesuaiangaji', 'penyesuaiangaji/*']) ? 'active' : '' }}">
                             <a href="{{ route('penyesuaiangaji.index') }}" class="menu-link">
                                 <div>Penyesuaian Gaji</div>
                             </a>
                         </li>
                     @endcan
                     @can('slipgaji.index')
                         <li class="menu-item {{ request()->is(['slipgaji', 'slipgaji/*']) ? 'active' : '' }}">
                             <a href="{{ route('slipgaji.index') }}" class="menu-link">
                                 <div>Slip Gaji</div>
                             </a>
                         </li>
                     @endcan
                 </ul>
             </li>
         @endif
         @if (auth()->user()->hasAnyPermission(['presensi.index']))
             <li class="menu-item {{ request()->is(['presensi', 'presensi/*']) ? 'active' : '' }}">
                 <a href="{{ route('presensi.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-device-desktop"></i>
                     <div>Monitoring Presensi</div>
                 </a>
             </li>
         @endif
         @if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index', 'izindinas.index']))
             <li class="menu-item {{ request()->is(['izinabsen', 'izinabsen/*', 'izinsakit', 'izincuti', 'izindinas']) ? 'active' : '' }}">
                 <a href="{{ route('izinabsen.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-folder-check"></i>
                     <div>Pengajuan Absen</div>
                     @if (!empty($notifikasi_ajuan_absen))
                         <div class="badge bg-danger rounded-pill ms-auto">{{ $notifikasi_ajuan_absen }}</div>
                     @endif
                 </a>
             </li>
         @endif
         @if (auth()->user()->hasAnyPermission(['lembur.index']))
             <li class="menu-item {{ request()->is(['lembur', 'lembur/*']) ? 'active' : '' }}">
                 <a href="{{ route('lembur.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-clock"></i>
                     <div>Lembur</div>
                     @if (!empty($notifikasi_lembur))
                         <div class="badge bg-danger rounded-pill ms-auto">{{ $notifikasi_lembur }}</div>
                     @endif
                 </a>
             </li>
         @endif
         @if (auth()->user()->hasAnyPermission(['harilibur.index', 'jamkerjabydept.index', 'generalsetting.index']))
             <li
                 class="menu-item {{ request()->is(['harilibur', 'harilibur/*', 'jamkerjabydept', 'jamkerjabydept/*', 'generalsetting', 'denda']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-settings"></i>
                     <div>Konfigurasi</div>
                 </a>
                 <ul class="menu-sub">
                     <li class="menu-item {{ request()->is(['generalsetting', 'generalsetting/*']) ? 'active' : '' }}">
                         <a href="{{ route('generalsetting.index') }}" class="menu-link">
                             <div>General Setting</div>
                         </a>
                     </li>
                     @if ($general_setting->denda)
                         <li class="menu-item {{ request()->is(['denda', 'denda/*']) ? 'active' : '' }}">
                             <a href="{{ route('denda.index') }}" class="menu-link">
                                 <div>Denda</div>
                             </a>
                         </li>
                     @endif

                     <li class="menu-item {{ request()->is(['harilibur', 'harilibur/*']) ? 'active' : '' }}">
                         <a href="{{ route('harilibur.index') }}" class="menu-link">
                             <div>Hari Libur</div>
                         </a>
                     </li>
                     <li class="menu-item {{ request()->is(['jamkerjabydept', 'jamkerjabydept/*']) ? 'active' : '' }}">
                         <a href="{{ route('jamkerjabydept.index') }}" class="menu-link">
                             <div>Jam Kerja Departemen</div>
                         </a>
                     </li>
                 </ul>
             </li>
         @endif
         @if (auth()->user()->hasAnyPermission(['laporan.presensi']))
             <li class="menu-item {{ request()->is(['laporan', 'laporan/*']) ? 'open' : '' }} ">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-adjustments-alt"></i>
                     <div>Laporan</div>
                 </a>
                 <ul class="menu-sub">
                     <li class="menu-item {{ request()->is(['laporan/presensi']) ? 'active' : '' }}">
                         <a href="{{ route('laporan.presensi') }}" class="menu-link">
                             <div>Presensi & Gaji</div>
                         </a>
                     </li>
                 </ul>
             </li>
         @endif
         @if (auth()->user()->hasRole(['super admin']))
             <li
                 class="menu-item {{ request()->is(['roles', 'roles/*', 'permissiongroups', 'permissiongroups/*', 'permissions', 'permissions/*', 'users', 'users/*']) ? 'open' : '' }} ">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-adjustments-alt"></i>
                     <div>Utilities</div>
                 </a>
                 <ul class="menu-sub">
                     <li class="menu-item {{ request()->is(['users', 'users/*']) ? 'active' : '' }}">
                         <a href="{{ route('users.index') }}" class="menu-link">
                             <div>User</div>
                         </a>
                     </li>
                     <li class="menu-item {{ request()->is(['roles', 'roles/*']) ? 'active' : '' }}">
                         <a href="{{ route('roles.index') }}" class="menu-link">
                             <div>Role</div>
                         </a>
                     </li>
                     <li class="menu-item {{ request()->is(['permissions', 'permissions/*']) ? 'active' : '' }}"">
                         <a href="{{ route('permissions.index') }}" class="menu-link">
                             <div>Permission</div>
                         </a>
                     </li>
                     <li class="menu-item  {{ request()->is(['permissiongroups', 'permissiongroups/*']) ? 'active' : '' }}">
                         <a href="{{ route('permissiongroups.index') }}" class="menu-link">
                             <div>Group Permission</div>
                         </a>
                     </li>
                 </ul>
             </li>
         @endif
         @if (auth()->user()->hasRole(['super admin']))
             <li class="menu-item {{ request()->is(['wagateway', 'wagateway/*']) ? 'active' : '' }}">
                 <a href="{{ route('wagateway.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-brand-whatsapp"></i>
                     <div>WA Gateway</div>
                 </a>
             </li>
         @endif
     </ul>
 </aside>
 <!-- / Menu -->
