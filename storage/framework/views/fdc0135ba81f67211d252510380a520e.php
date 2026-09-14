<?php $__env->startSection('title', 'Public Grievances List'); ?>

<?php $__env->startSection('content'); ?>

<style>
    div.dt-buttons {
        float: none !important;
        /* width: 19%; */
        width: 33%;
        /* chagned by anil on 28-08-2025 to fix in resposive */
    }

    div.dt-buttons.btn-group {
        margin-bottom: 20px;
    }

    div.dt-buttons.btn-group .btn {
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        div.dt-buttons {
            width:100%;
        }

        div.dt-buttons.btn-group {
            flex-direction: column;
            align-items: flex-start;
        }

        div.dt-buttons.btn-group .btn {
            width: 100%;
            text-align: left;
        }
    }
</style>



<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Public Grievances</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Grievance List</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content py-3">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add.grievance')): ?>
            <a href="<?php echo e(route('grievance.create')); ?>">
                <button type="button" class="btn btn-primary py-2">+ Add Grievance</button>
            </a>            
            <?php endif; ?>
        </div>
        <table id="grievancesTable" class="display nowrap" style="width:100%">
            <thead>
            <tr>
                <th>#</th>
                <th>Ticket No.</th>
                <th>Name</th>
                <th>Contact Detail</th>
                <th>Locality</th>
                <th>Description</th>
                <th>
                    <!-- Keep the dropdown styling as is and do not set a specific width here to allow dropdown to adjust based on its content -->
                    <select style="font-weight: bold; font-size: 14px;" id="statusFilter" class="form-control">
                        <option class="text-capitalize" value="">Status</option>
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status->item_code); ?>"><?php echo e($status->item_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </th>
                <th>Official Remarks</th>
                <th>Communication Address</th>
                <th>Action</th>
            </tr>

            </thead>
        </table>
    </div>
</div>

<?php echo $__env->make('admin_public_grievances.viewMoreRemarks', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('admin_public_grievances.remarks', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php $__env->stopSection(); ?>
<?php $__env->startSection('footerScript'); ?>
<script>
    $(document).ready(function() {
        var table = $('#grievancesTable').DataTable({
            processing: true,
            serverSide: true,
         //   responsive: true,
            ajax: {
                url: "<?php echo e(route('grievance.getGrievances')); ?>",
                type: 'GET',
                data: function (d) {
                    // Append status filter value to the request
                    d.status = $('#statusFilter').val();
                }
            },
            columns: [
                {
                    data: null,
                    name: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    searchable: false,
                    orderable: false
                },
                { data: 'unique_id', name: 'unique_id' },
                { data: 'name', name: 'name' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return '<span>' + row.mobile + '</span><br><span style="font-size:smaller; color:blue;">' + row.email + '</span>';
                    },
                    name: 'mobile'
                },
                
                { 
                    data: null,
                    render: function (data, type, row) {
                        return row.locality + '<br>(' + row.section + ')';
                    },
                    name: 'locality'
                },
                {
                    data: 'description', 
                    name: 'description',
                    render: function(data, type, row) {
                        if (data.length > 25) {
                            return `<span data-toggle="tooltip" data-placement="top" title="${data}">${data.substring(0, 20)}...</span>`;
                        } else {
                            return data;
                        }
                    }
                },
                { 
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row) {
                        return row.status;
                    }
                },
                { 
                    data: 'remark', 
                    name: 'remark', 
                    render: function(data, type, row) {
                        return data; // Since data is already formatted in the backend, just display it
                    }
                },
                { data: 'communication address', name: 'communication address' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            dom: '<"top"Blf>rt<"bottom"ip><"clear">',
            buttons: ['csv', 'excel'],
            scrollX: true
        });

        $('[data-toggle="tooltip"]').tooltip();

        // Redraw the table when the status dropdown filter is changed
        $('#statusFilter').on('change', function () {
            table.ajax.reload();
        });
    });

    function openRemarksModal(grievanceId) {
    var baseUrl = '<?php echo e(route("grievance.details", ["id" => "__ID__"])); ?>';
    baseUrl = baseUrl.replace('__ID__', grievanceId);
    $.ajax({
        url: baseUrl ,
        method: 'GET',
        success: function(response) {
            var statusSelect = $('#status');
            statusSelect.empty(); // Clear previous options
            var currentStatus = response.grievance.status_item.item_code;
            var userRole = response.userRole;

            response.statuses.forEach(function(status) {
                var isSelected = (status.item_code === currentStatus);

                // Show only allowed statuses based on the role
                if (userRole === 'CDN') {
                    // Only allow 'Reopen' status for cdn-admin
                    if (status.item_code === 'PG_REO') {
                        statusSelect.append($('<option>', {
                            value: status.item_code,
                            text: status.item_name,
                            selected: isSelected
                        }));
                    }
                } else if (userRole === 'section-officer' || userRole === 'super-admin') {
                    // For section-officer and super-admin, exclude 'PG_NEW', 'PG_REO', 'PG_PEN'
                    if (!['PG_NEW', 'PG_REO', 'PG_PEN'].includes(status.item_code)) {
                        statusSelect.append($('<option>', {
                            value: status.item_code,
                            text: status.item_name,
                            selected: isSelected
                        }));
                    }
                }
            });

            var actionUrl = '<?php echo e(route("admin_public_grievances.update_remarks", ["id" => "replace_id"])); ?>';
            actionUrl = actionUrl.replace('replace_id', grievanceId);
            $('#remarksForm').attr('action', actionUrl);
            $('#remarksModal').modal('show');
        },
        error: function(xhr) {
            console.error('Error fetching grievance details:', xhr.responseText);
        }
    });
}




    function submitRemarkForm() {
        
        $('#remarksForm').submit();
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/admin_public_grievances/indexDatatable.blade.php ENDPATH**/ ?>