<?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'setting')): ?>
<li class="nav-item dropdown dropdown-large">
    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button"
        data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-cog"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-end megamenu">
        <div class="row g-3 p-3">
            
            <div class="col-4">
                <div class="tab">
                    <a href="javascript:void(0);" class="tablinks active"
                        onmouseenter="openMegaMenu(event, 'RBAC')">
                        <div class="col mb-1 megamenu-item">
                            <div class="app-box mx-auto text-white">
                                <i class='bx bx-group'></i>
                            </div>
                            <div class="app-title">Application Configuration</div>
                        </div>
                    </a>
                    <!-- <a href="javascript:void(0);" class="tablinks"
                                                    onmouseenter="openMegaMenu(event, 'Store')">
                                                    <div class="col mb-1 megamenu-item">
                                                        <div class="app-box mx-auto text-white">
                                                            <i class='bx bx-store'></i>
                                                        </div>
                                                        <div class="app-title">Store Management</div>
                                                    </div>
                                                </a> -->
                    <a class="tablinks" onmouseenter="openMegaMenu(event, 'Purchase')">
                        <div class="col mb-1 megamenu-item">
                            <div class="app-box mx-auto text-white">
                                <i class='bx bx-cart'></i>
                            </div>
                            <div class="app-title">Logistic Management</div>
                        </div>
                    </a>
                    

                    <!-- <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('upload.excel')): ?> -->
                    <a href="<?php echo e(route('import.form')); ?>" class="tablinks" onmouseenter="openMegaMenu(event, 'UploadExcel')">
                        <div class="col mb-1 megamenu-item">
                            <div class="app-box mx-auto text-white">
                                <i class='bx bx-upload'></i>
                            </div>
                            <div class="app-title">Upload Excel</div>

                        </div>
                    </a>
                    <!-- <?php endif; ?> -->

                    <a href="javascript:void(0);" class="tablinks" onmouseenter="openMegaMenu(event, 'Miscellaneous')">
                        <div class="col mb-1 megamenu-item">
                            <div class="app-box mx-auto text-white">
                            <i class='bx bxs-grid-alt'></i>
                            </div>
                            <div class="app-title">Miscellaneous</div>

                        </div>
                    </a>
                    <a href="<?php echo e(route('actionLogListings')); ?>" class="tablinks"
                    onmouseenter="openMegaMenu(event, 'UserActionLogs')">
                    <div class="col mb-1 megamenu-item">
                        <div class="app-box mx-auto text-white">
                            <i class="bx bx-group"></i>
                        </div>
                        <div class="app-title">Action Log History</div>
                    </div>
                </a>
                
                </div>
            </div>
            <div class="col-8">
                <div class="right-container">
                    <div class="tab-content-container">
                        <div id="RBAC" class="tabcontent" style="display: block;">
                            <div class="row col-partition">
                                <div class="col-lg-4">
                                    <h5>RBAC</h5>
                                    <ul class="nav-links">
                                        <li><a href="<?php echo e(route('roles.index')); ?>"><i class='bx bx-chevron-right'></i> Role</a></li>
                                        <li><a href="<?php echo e(route('permissions.index')); ?>"><i class='bx bx-chevron-right'></i> Permissions</a></li>
                                        <li><a href="<?php echo e(route('users.index')); ?>"><i class='bx bx-chevron-right'></i> Manage Users</a></li>
                                        
                                    </ul>
                                </div>
                                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'app.settings')): ?>
                                <div class="col-lg-4">
                                    <h5>Settings</h5>
                                    <ul class="nav-links">
                                        <li><a href="<?php echo e(route('settings.mail.index')); ?>"><i class='bx bx-chevron-right'></i> Email</a></li>
                                        <li><a href="<?php echo e(route('settings.sms.index')); ?>"><i class='bx bx-chevron-right'></i> SMS</a></li>
                                        <li><a href="<?php echo e(route('settings.whatsapp.index')); ?>"><i class='bx bx-chevron-right'></i> WhatsApp</a></li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                                <div class="col-lg-4">
                                    <h5>Templates</h5>
                                    <ul class="nav-links">
                                        <li><a href="<?php echo e(route('msgtempletes')); ?>"><i class='bx bx-chevron-right'></i>Templates</a></li>
                                        <!-- <li><a href="#"><i class='bx bx-chevron-right'></i> SMS Template</a></li>
                                        <li><a href="#"><i class='bx bx-chevron-right'></i> WhatsApp Template</a></li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- <div id="Store" class="tabcontent">

                                                        <ul class="nav-links">

                                                        </ul>
                                                    </div> -->
                        <div id="Purchase" class="tabcontent">
                            <div class="row col-partition">
                                <div class="col-lg-4">
                                    <h5>Product</h5>
                                    <ul class="nav-links">
                                        <li><a href="<?php echo e(route('category.index')); ?>"><i class='bx bx-chevron-right'></i> Add Category</a></li>
                                        <li><a href="<?php echo e(route('logistic.index')); ?>"><i class='bx bx-chevron-right'></i> Add Items</a></li>
                                        <li><a href="<?php echo e(route('supplier.index')); ?>"><i class='bx bx-chevron-right'></i> Supplier/Vendor List</a></li>
                                        <li><a href="<?php echo e(route('purchase.index')); ?>"><i class='bx bx-chevron-right'></i> Purchase</a></li>
                                    </ul>
                                </div>
                                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'app.settings')): ?>
                                <div class="col-lg-4">
                                    <h5>Issues</h5>
                                    <ul class="nav-links">
                                        <li><a href="<?php echo e(route('issued_item.create')); ?>"><i class='bx bx-chevron-right'></i> Issue an Item</a></li>
                                        <li><a href="<?php echo e(route('requested_item.index')); ?>"><i class='bx bx-chevron-right'></i> Issue Requests</a></li>
                                    </ul>
                                </div>
                                <?php endif; ?>
                                <div class="col-lg-4">
                                    <h5>Stock</h5>
                                    <ul class="nav-links">
                                        <li><a href="<?php echo e(route('purchaseStock.index')); ?>"><i class='bx bx-chevron-right'></i> Available Stock</a></li>
                                        <li><a href="<?php echo e(route('purchaseHistory.index')); ?>"><i class='bx bx-chevron-right'></i> Stock History</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div id="Miscellaneous" class="tabcontent">
                            <ul class="nav-links">
                                <li><a href="<?php echo e(route('propertyAssignment')); ?>"><i class='bx bx-chevron-right'></i> Property Assignment</a></li>
                                <li><a href="<?php echo e(route('colony.merger.create')); ?>"><i class='bx bx-chevron-right'></i> Property Merging</a></li>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.scanning.list')): ?>
                                    <li><a href="<?php echo e(route('scanning.index')); ?>"><i class='bx bx-chevron-right'></i> Property Scanning</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/layouts/settings.blade.php ENDPATH**/ ?>