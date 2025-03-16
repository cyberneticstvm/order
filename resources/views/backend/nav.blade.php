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
            <!--<ul class="sidebar-submenu">
                <li><a class="" href="{{ route('dashboard') }}">Dashboard</a></li>
            </ul>-->
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->link(route('dashboard'), 'Dashboard')
                ->linkIfCan('admin-dashboard', route('ajax.admin.dashboard'), 'Admin Dashboard');
            }}
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-social') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-social') }}"></use>
                </svg>
                <span>Lab</span>
            </a>
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('lab-assign-orders', route('lab.assign.orders'), 'Assign Orders')
                ->linkIfCan('lab-assigned-orders', route('lab.view.orders'), 'Assigned Orders')
                ->linkIfCan('received-from-lab-orders', route('received.from.lab.orders'), 'Received From Lab')
                ->linkIfCan('lab-job-completed-orders', route('job.completed.lab.orders'), 'Job Completed')
                ->linkIfCan('lab-under-process-orders', route('under.process.lab.orders'), 'Under Process')
                ->linkIfCan('lab-main-branch-orders', route('main.branch.lab.orders'), 'Main Branch');
            }}
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-social') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-social') }}"></use>
                </svg>
                <span class="">Order Status </span>
            </a>
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('search-order', route('search.order'), 'Order')
                ->linkIfCan('update-delivered-order', route('edit.dispatched.order'), 'Update Delivered Order');
            }}
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-search') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-search') }}"></use>
                </svg>
                <span class="">Search </span>
            </a>
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('search-order', route('search.order'), 'Order')
                ->linkIfCan('search-customer', route('search.customer'), 'Customer')
                ->linkIfCan('search-prescription', route('search.prescription'), 'Prescription');
            }}
        </li>
        @if(auth()?->user()?->can('customer-list') || auth()?->user()?->can('customer-create') || auth()?->user()?->can('customer-edit') || auth()?->user()?->can('customer-delete') || auth()?->user()?->can('spectacle-list'))
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#customers') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#customers') }}"></use>
                </svg>
                <span>Customer / Spectacle</span>
            </a>
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('customer-list', route('customer.register'), 'Customer Register')                
                ->linkIfCan('spectacle-list', route('spectacles'), 'Spectacle Register');                
            }}
        </li>
        @endif
        <li class="sidebar-main-title">
            <div>
                <h6>Order</h6>
            </div>
        </li>
        @if(auth()?->user()?->can('store-order-list') || auth()?->user()?->can('store-order-create') || auth()?->user()?->can('store-order-edit') || auth()?->user()?->can('store-order-delete') || auth()?->user()?->can('invoice-register') || auth()?->user()?->can('invoice-register-not-generated') || auth()?->user()?->can('sales-return-list') || auth()?->user()?->can('product-damage-list'))
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
                ->linkIfCan('sales-return-list', route('sales.return'), 'Sales Return Register')
                ->linkIfCan('product-damage-list', route('product.damage.register'), 'Product Damage Register')
                ->linkIfCan('invoice-register', route('invoice.register'), 'Invoice Register')
                ->linkIfCan('invoice-register-not-generated', route('not.generated.invoice.register'), 'Pending Invoice Register')
                ->linkIfCan('po-list', route('po'), 'Purchase Order');
            }}
        </li>
        @endif
        @if(auth()?->user()?->can('pending-transfer-list') || auth()?->user()?->can('pending-transfer-edit') || auth()?->user()?->can('product-damage-transfer-list'))
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
                ->linkIfCan('pending-transfer-list', route('pending.transfer'), 'Pending Transfer Register')
                ->linkIfCan('product-damage-transfer-list', route('pending.damage.transfer'), 'Product Damage Register');
            }}
        </li>
        @endif
        @if(auth()?->user()?->can('payment-list') || auth()?->user()?->can('payment-create') || auth()?->user()?->can('payment-edit') || auth()?->user()?->can('payment-delete'))
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
                ->linkIfCan('payment-list', route('patient.payments'), 'Payment Register')
                ->linkIfCan('voucher-list', route('voucher'), 'Voucher Register');
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('head-list', route('heads'), 'Heads')
                ->linkIfCan('income-expense-list', route('iande'), 'Income & Expense Register')
                ->linkIfCan('bank-transfer-list', route('bank.transfers'), 'Bank Transfer');
            }}
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#stroke-learning') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#fill-learning') }}"></use>
                </svg>
                <span>Stock / Inventory</span>
            </a>
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('stock-comparison', route('stock.preview'), 'Stock Comparison');
            }}
        </li>




        <li class="sidebar-main-title">
            <div>
                <h6 class="">Operations</h6>
            </div>
        </li>
        @if(auth()?->user()?->can('user-list') || auth()?->user()?->can('user-create') || auth()?->user()?->can('user-edit') || auth()?->user()?->can('user-delete') || auth()?->user()?->can('role-list') || auth()?->user()?->can('role-create') || auth()?->user()?->can('role-edit') || auth()?->user()?->can('role-delete') || auth()?->user()?->can('branch-opto-list'))
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
                ->linkIfCan('role-list', route('roles'), 'Roles & Permissions')
                ->linkIfCan('branch-opto-list', route('bo'), 'Branch & Opto');
            }}
        </li>
        @endif
        @if(auth()?->user()?->can('branch-list') || auth()?->user()?->can('branch-create') || auth()?->user()?->can('branch-edit') || auth()?->user()?->can('branch-delete'))
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

        <li class="sidebar-list"><i class="fa fa-thumb-tack"> </i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#star') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#star') }}"></use>
                </svg>
                <span>Offers</span>
            </a>
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('offer-category-list', route('offer.category.list'), 'Offer Category Register');                
            }}
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
                <li><a class="submenu-title" href="javascript:void(0)">Extras<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    {{
                        Menu::new()->addClass('nav-sub-childmenu submenu-content')
                        ->linkIfCan('collection-list', route('collections'), 'Extras');
                    }}
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Frame<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    {{
                        Menu::new()->addClass('nav-sub-childmenu submenu-content')
                        ->linkIfCan('product-frame-list', route('product.frame'), 'Frame Register')
                        ->linkIfCan('purchase-frame-list', route('frame.purchase'), 'Frame Purchase')
                        ->linkIfCan('frame-transfer-list', route('frame.transfer'), 'Frame Transfer');
                    }}
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Lens<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    {{
                        Menu::new()->addClass('nav-sub-childmenu submenu-content')
                        ->linkIfCan('product-lens-list', route('product.lens'), 'Lens Register')
                        ->linkIfCan('product-lens-price-list', route('product.lens.price.list'), 'Lens Price List')
                        ->linkIfCan('purchase-lens-list', route('lens.purchase'), 'Lens Purchase')
                        ->linkIfCan('lens-transfer-list', route('lens.transfer'), 'Lens Transfer');
                    }}
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Service<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    {{
                        Menu::new()->addClass('nav-sub-childmenu submenu-content')
                        ->linkIfCan('product-service-list', route('product.service'), 'Service Register');
                    }}
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Solutions<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    {{
                        Menu::new()->addClass('nav-sub-childmenu submenu-content')
                        ->linkIfCan('product-solution-list', route('product.solution'), 'Solutions Register')
                        ->linkIfCan('purchase-solution-list', route('solution.purchase'), 'Solution Purchase')
                        ->linkIfCan('solution-transfer-list', route('solution.transfer'), 'Solution Transfer');
                    }}
                </li>
                <li><a class="submenu-title" href="javascript:void(0)">Accessories<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    {{
                        Menu::new()->addClass('nav-sub-childmenu submenu-content')
                        ->linkIfCan('product-accessory-list', route('product.accessory'), 'Accessory Register')
                        ->linkIfCan('purchase-accessory-list', route('accessory.purchase'), 'Accessory Purchase')
                        ->linkIfCan('accessory-transfer-list', route('accessory.transfer'), 'Accessory Transfer');
                    }}
                </li>

                <li><a class="submenu-title" href="javascript:void(0)">Imports<span class="sub-arrow"><i class="fa fa-angle-right"></i></span></a>
                    {{
                        Menu::new()->addClass('nav-sub-childmenu submenu-content')
                        ->linkIfCan('import-product-purchase', route('import.product.purchase'), 'Add Product Purchases')
                        ->linkIfCan('import-new-frames', route('import.frames'), 'Add New Frames')
                        ->linkIfCan('import-new-lenses', route('import.lenses'), 'Add New Lenses')
                        ->linkIfCan('import-product-transfer', route('import.transfer'), 'Add Product Transfer')
                    }}
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('supplier-list', route('suppliers'), 'Supplier Register');
            }}
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('manufacturer-list', route('manufacturers'), 'Manufacturer Register');
            }}
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('report-daybook', route('report.daybook'), 'Daybook')
                ->linkIfCan('report-payment', route('report.payment'), 'Payments')
                ->linkIfCan('report-sales', route('report.sales'), 'Sales')
                ->linkIfCan('report-stock-status', route('report.stock.status'), 'Stock Status')
                ->linkIfCan('report-login-log', route('report.login.log'), 'Login Log')
                ->linkIfCan('report-income-expense', route('report.income.expense'), 'Income & Expenses')
                ->linkIfCan('report-bank-transfer', route('report.bank.transfer'), 'Bank Transfer')
                ->linkIfCan('report-product-damage', route('report.product.damage'), 'Product Damage')
                ->linkIfCan('report-sales-return', route('report.sales.return'), 'Sales Return')
                ->linkIfCan('report-product-transfer', route('report.product.transfer'), 'Product Transfer')
                ->linkIfCan('report-purchase', route('report.purchase'), 'Purchases')
                ->linkIfCan('export-order', route('order.export'), 'Export Order / Invoice')
                ->linkIfCan('report-order-by-price', route('order.by.price'), 'Order by Price')
                ->linkIfCan('report-stock-movement', route('report.stock.movement'), 'Stock Movement')
                ->linkIfCan('report-vehicle-export', route('vehicle.export'), 'Vehicle Export');
            }}
        </li>
        <li class="sidebar-main-title">
            <div>
                <h6>Ads</h6>
            </div>
        </li>
        <li class="sidebar-list"><i class="fa fa-thumb-tack"></i>
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg class="stroke-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#eth') }}"></use>
                </svg>
                <svg class="fill-icon">
                    <use href="{{ asset('/backend/assets/svg/icon-sprite.svg#eth') }}"></use>
                </svg>
                <span>Vehicles</span>
            </a>
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('vehicle-list', route('vehicles'), 'Vehicle List')
                ->linkIfCan('fetch-vehicle-for-payment', route('vehicle.payment.fetch'), 'Payment');
            }}
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
            {{
                Menu::new()->addClass('sidebar-submenu')
                ->linkIfCan('setting-account-adjustment', route('account.setting'), 'Adjust Account')
                ->linkIfCan('setting-stock-adjustment', route('setting.stock.adjustment'), 'Stock Adjustment')
                ->linkIfCan('settings-extra', route('settings.extra'), 'Settings Extra');
            }}
        </li>
    </ul>
</div>