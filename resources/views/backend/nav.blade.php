<div id="sidebar-menu">
    <ul class="sidebar-links" id="simple-bar">
        <li class="back-btn"><a href="{{ route('dashboard') }}"><img class="img-fluid" src="{{ asset('/backend/assets/images/logo/devi-logo.png') }}" alt=""></a>
            <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
        </li>
        <li class="pin-title sidebar-main-title">
            <div>
                <h6>Pinned</h6>
            </div>
        </li>
        <li class="sidebar-main-title">
            <div>
                <h6 class="lan-1">General</h6>
            </div>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-home') }}"></use>
                </svg>
                <span class="lan-3">Dashboard </span>
            </a>
            <ul class="sidebar-submenu">
                <li><a class="" href="{{ route('dashboard') }}">Patient Register</a></li>
                <li><a href="{{ route('consultations') }}">Consultation Register</a></li>
                <li><a href="{{ route('appointment.list') }}">Today's Appointment</a></li>
                <li><a class="" href="{{ route('store.order') }}">Order Register</a></li>
                <li><a class="" href="{{ route('pharmacy.order') }}">Pharmacy Register</a></li>
                <li><a class="" href="{{ route('search') }}">Search</a></li>
            </ul>
        </li>
        <li class="sidebar-main-title">
            <div>
                <h6 class="">Operations</h6>
            </div>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-contact') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-contact') }}"></use>
                </svg>
                <span>Appointment</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('appointments') }}">Appointment Register</a></li>
                <li><a href="{{ route('appointment.list') }}">Today's Appointment</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-to-do') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-to-do') }}"></use>
                </svg>
                <span>Consultation</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('patients') }}">Patient Register</a></li>
                <li><a href="{{ route('consultations') }}">Consultation Register</a></li>
                <li><a href="{{ route('mrecords') }}">Medical Record Register</a></li>
                <li><a href="{{ route('patient.procedures') }}">Patient Procedure Register</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#customers') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#customers') }}"></use>
                </svg>
                <span>User & Roles</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('users') }}">User Register</a></li>
                <li><a href="{{ route('roles') }}">Roles & Permissions</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-learning') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-learning') }}"></use>
                </svg>
                <span>Branch Management</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('branches') }}">Branch Register</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-user') }}"></use>
                </svg>
                <span>Doctor Management</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('doctors') }}">Doctor Register</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-file') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-file') }}"></use>
                </svg>
                <span>Procedure Management</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('procedures') }}">Procedure Register</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-widget') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-widget') }}"></use>
                </svg>
                <span>Camp</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('camps') }}">Camp Register</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#doller-return') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#doller-return') }}"></use>
                </svg>
                <span>Income & Expenses</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('heads') }}">Heads</a></li>
                <li><a href="{{ route('iande') }}">Income & Expense Register</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-knowledgebase') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-knowledgebase') }}"></use>
                </svg>
                <span>Documents</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('documents') }}">Document Register</a></li>
            </ul>
        </li>
        <li class="sidebar-main-title">
            <div>
                <h6>Order</h6>
            </div>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-ecommerce') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-ecommerce') }}"></use>
                </svg>
                <span>Store</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('store.order') }}">Order Register</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-form') }}"></use>
                </svg>
                <span>Pharmacy</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('pharmacy.order') }}">Pharmacy Order Register</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-social') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-social') }}"></use>
                </svg>
                <span>Transfer</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('pending.transfer') }}">Pending Transfer Register</a></li>
            </ul>
        </li>
        <li class="sidebar-main-title">
            <div>
                <h6>Payments</h6>
            </div>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#doller-return') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#doller-return') }}"></use>
                </svg>
                <span>Payments</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('patient.payments') }}">Payment Register</a></li>
            </ul>
        </li>
        <li class="sidebar-main-title">
            <div>
                <h6>Inventory</h6>
            </div>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-others') }}"></use>
                </svg>
                <span>Product</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a class="submenu-title" href="javascript:void(0)">Pharmacy<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    <ul class="nav-sub-childmenu submenu-content">
                        <li><a href="{{ route('product.pharmacy') }}">Pharmacy List</a></li>
                        <li><a href="{{ route('pharmacy.purchase') }}">Pharmacy Purchase</a></li>
                        <li><a href="{{ route('pharmacy.transfer') }}">Pharmacy Transfer</a></li>
                    </ul>
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Frame<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    <ul class="nav-sub-childmenu submenu-content">
                        <li><a href="{{ route('product.frame') }}">Frame List</a></li>
                        <li><a href="{{ route('frame.purchase') }}">Frame Purchase</a></li>
                        <li><a href="{{ route('frame.transfer') }}">Frame Transfer</a></li>
                    </ul>
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Lens<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    <ul class="nav-sub-childmenu submenu-content">
                        <li><a href="{{ route('product.lens') }}">Lens List</a></li>
                        <li><a href="{{ route('lens.purchase') }}">Lens Purchase</a></li>
                        <li><a href="{{ route('lens.transfer') }}">Lens Transfer</a></li>
                    </ul>
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Service<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    <ul class="nav-sub-childmenu submenu-content">
                        <li><a href="{{ route('product.service') }}">Service List</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-builders') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-builders') }}"></use>
                </svg>
                <span>Supplier</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('suppliers') }}">Supplier Register</a></li>
            </ul>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-user') }}"></use>
                </svg>
                <span>Manufacturer</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('manufacturers') }}">Manufacturer Register</a></li>
            </ul>
        </li>
        <li class="sidebar-main-title">
            <div>
                <h6>Reports</h6>
            </div>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-knowledgebase') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-knowledgebase') }}"></use>
                </svg>
                <span>Reports</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('report.daybook') }}">Daybook</a></li>
                <li><a href="{{ route('report.consultation') }}">Consultation</a></li>
                <li><a href="{{ route('report.lab') }}">Lab</a></li>
                <li><a href="{{ route('report.sales') }}">Sales</a></li>
            </ul>
        </li>
        <li class="sidebar-main-title">
            <div>
                <h6>Settings</h6>
            </div>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-icons') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-icons') }}"></use>
                </svg>
                <span>Settings</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="{{ route('setting.global') }}">Settings</a></li>
            </ul>
        </li>
    </ul>
</div>