<div id="sidebar-menu">
    <ul class="sidebar-links" id="simple-bar">
        <li class="back-btn"><a href="{{ route('dashboard') }}"><img class="img-fluid" src="{{ asset('/backend/assets/images/logo/devi-logo.png') }}" alt="" style="width: 25%;"></a>
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
                <li><a class="" href="{{ route('dashboard') }}">Dashboard</a></li>
            </ul>
        </li>
        <li class="sidebar-main-title">
            <div>
                <h6 class="">Operations</h6>
            </div>
        </li>
        @if(auth()->user()->can('user-list') || auth()->user()->can('user-create') || auth()->user()->can('user-edit') || auth()->user()->can('user-delete') || auth()->user()->can('role-list') || auth()->user()->can('role-create') || auth()->user()->can('role-edit') || auth()->user()->can('role-delete'))
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('user-list', route('users'), 'User Register')
                ->linkIfCan('role-list', route('roles'), 'Roles & Permissions');
            }}
        </li>
        @endif
        @if(auth()->user()->can('branch-list') || auth()->user()->can('branch-create') || auth()->user()->can('branch-edit') || auth()->user()->can('branch-delete'))
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('branch-list', route('branches'), 'Branch Register');
            }}
        </li>
        @endif
        <li class="sidebar-main-title">
            <div>
                <h6>Order</h6>
            </div>
        </li>
        @if(auth()->user()->can('store-order-list') || auth()->user()->can('store-order-create') || auth()->user()->can('store-order-edit') || auth()->user()->can('store-order-delete') || auth()->user()->can('invoice-register') || auth()->user()->can('invoice-register-not-generated'))
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('store-order-list', route('store.order'), 'Order Register')
                ->linkIfCan('invoice-register', route('invoice.register'), 'Invoice Register')
                ->linkIfCan('invoice-register-not-generated', route('not.generated.invoice.register'), 'Not Generated Invoices');
            }}
        </li>
        @endif
        <!--@if(auth()->user()->can('pharmacy-order-list') || auth()->user()->can('pharmacy-order-create') || auth()->user()->can('pharmacy-order-edit') || auth()->user()->can('pharmacy-order-delete'))
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('pharmacy-order-list', route('pharmacy.order'), 'Pharmacy Order Register');
            }}
        </li>
        @endif-->
        @if(auth()->user()->can('pending-transfer-list') || auth()->user()->can('pending-transfer-edit'))
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('pending-transfer-list', route('pending.transfer'), 'Pending Transfer Register');
            }}
        </li>
        @endif
        @if(auth()->user()->can('payment-list') || auth()->user()->can('payment-create') || auth()->user()->can('payment-edit') || auth()->user()->can('payment-delete'))
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('payment-list', route('patient.payments'), 'Payment Register');
            }}
        </li>
        @endif
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
                <!--<li><a class="submenu-title" href="javascript:void(0)">Pharmacy<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    <ul class="nav-sub-childmenu submenu-content">
                        <li><a href="{{ route('product.pharmacy') }}">Pharmacy List</a></li>
                        <li><a href="{{ route('pharmacy.purchase') }}">Pharmacy Purchase</a></li>
                        <li><a href="{{ route('pharmacy.transfer') }}">Pharmacy Transfer</a></li>
                    </ul>
                </li>-->
                <li><a class="submenu-title" href="javascript:void(0)">Col / Type / Shape<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    <ul class="nav-sub-childmenu submenu-content">
                        <li><a href="{{ route('collections') }}">Collection / Shape / Type</a></li>
                    </ul>
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Frame<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    <ul class="nav-sub-childmenu submenu-content">
                        <li><a href="{{ route('product.frame') }}">Frame Register</a></li>
                        <li><a href="{{ route('frame.purchase') }}">Frame Purchase</a></li>
                        <li><a href="{{ route('frame.transfer') }}">Frame Transfer</a></li>
                    </ul>
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Lens<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    <ul class="nav-sub-childmenu submenu-content">
                        <li><a href="{{ route('product.lens') }}">Lens Register</a></li>
                        <li><a href="{{ route('lens.purchase') }}">Lens Purchase</a></li>
                        <li><a href="{{ route('lens.transfer') }}">Lens Transfer</a></li>
                    </ul>
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Service<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    <ul class="nav-sub-childmenu submenu-content">
                        <li><a href="{{ route('product.service') }}">Service Register</a></li>
                    </ul>
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Import Purchases<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    <ul class="nav-sub-childmenu submenu-content">
                        <li><a href="{{ route('import.frame.purchase') }}">Import</a></li>
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
                <!--<li><a href="{{ route('report.consultation') }}">Consultation</a></li>
                <li><a href="{{ route('report.lab') }}">Lab</a></li>-->
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