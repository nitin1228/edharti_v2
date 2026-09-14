<!DOCTYPE html>
<html>

<head>
    <title>Demand Letter PDF</title>
    <style>
        @page {
            margin: 25px;
            background: transparent;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            /* font-family: sans-serif !important; */
            margin: 0;
            padding: 0;
            position: relative;
        }

        .watermark{
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("<?php echo e(public_path('assets/images/water-mark.png')); ?>") repeat;
            transform: rotate(-30deg);
            opacity: 0.1;
            z-index: -99;
        }

        body::before {
            content: "";
            position: fixed;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -99;
            background: url(assets/images/water-mark-emblem.png) center center no-repeat;
            background-size: 300px;
            opacity: 0.2;
        }

        /* body::after {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -9;
            background: url(assets/images/water-mark.jpg) 0 0 repeat;
            background-size:300px;
            transform: rotate(-30deg);
            opacity: 0.2;
        } */

        .emblem-div {
            width: 100%;
            text-align: center;
        }

        .emblem {
            display: inline-block;
            margin: auto;
        }

        .title-main {
            color: navy;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .title-sub {
            color: navy;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .part-title {
            background-color: #1fa1a2;
            color: white;
            font-size: 14px;
            padding: 8px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .content-wrap {
            margin-right: 30px;
            margin-left: 30px;
        }
        img {
            image-rendering: optimizeQuality;
            -dompdf-image-resolution: 72dpi;
        }

         ol {
            padding-left: 20px;
        }
        ol li{
            font-size: 12px;
            line-height: 20px;
        }

        .hidden-table {
            margin-bottom: 0;
        }

        .hidden-table th{
            border: 0;
            font-size: 12px;
            vertical-align: top;
            margin: 0 0 10px;
            padding: 5px 5px;
        }
        .hidden-table,
        .hidden-table td {
            border: 0;
            font-size: 10px;
            vertical-align: top;
            margin: 0 0 10px;
            padding: 5px 5px;
        }

        .hidden-table th p,
        .hidden-table td p {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="watermark"></div>
    <!-- Emblem -->
    <div class="emblem-div">
        <img src="assets/images/emblem.jpg" width="40" alt="Emblem" class="emblem">
    </div>

    <!-- Title -->
    <!-- <h1 class="title-main">Land And Development Office</h1>
    <h2 class="title-sub">Ministry of Housing and Urban Affairs</h2>
    <h2 class="title-sub">Government of India</h2> -->
    <h1 class="title-main">Government of India</h1>
    <h1 class="title-main">Ministry of Housing and Urban Affairs</h1>                    
    <h1 class="title-main">Land And Development Office</h1>


    <!-- <p style="text-align: center; font-size: 14px; color:#116d6e; font-weight: bold; margin-top: 30px;">
        Payment Receipt
    </p> -->

    <!-- Applicant Details -->
    <div class="part-title">DEMAND LETTER</div>
    <table class="hidden-table">
        <tbody>
            <tr>
                <td style="min-width: 120px;">To</td>
                <th>Property Details</th>
                <th>Demand Details</th>
            </tr>
        </tbody>
        <tr>
            <td>Name: <span style="text-decoration: underline;"><?php echo e($name); ?></span></td>
            <td>Property ID: <span style="text-decoration: underline;"><?php echo e($demand->old_property_id); ?></span></td>
            <td>Demand ID: <span style="text-decoration: underline;"><?php echo e($demand->unique_id); ?></span></td>
        </tr>
        <tr>
            <td>Address: <span style="text-decoration: underline;"><?php echo e($address); ?></span></td>
            <td>Block No.: <span style="text-decoration: underline;"><?php echo e($propertyMaster->block_no); ?></span></td>
            <td>Demand Date: <span style="text-decoration: underline;"><?php echo e(date('d-m-Y',strtotime($demand->approved_at))); ?></span></td>
        </tr>
        <tr>
            <td></td>
            <td>Plot No.: <span style="text-decoration: underline;"><?php echo e($splittedProperty?->plot_flat_no ?? $propertyMaster->plot_or_property_no); ?></span></td> 
            <td>Due Date: <span style="text-decoration: underline;"><?php echo e(date('d-m-Y',strtotime('+30 days',strtotime($demand->approved_at)))); ?></span></td>
        </tr>
        <?php if($demand->flat_id): ?>
        <tr>
            <td></td>
            <td>Flat ID: <span style="text-decoration: underline;"><?php echo e($flat->unique_flat_id); ?></span></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td></td>
            <td>FlatNo./ FloorNo./ ShopNo. <span style="text-decoration: underline;"><?php echo e($flat?$flat->flat_number.'/'.$flat->floor :"N/A"); ?></span></td>
            <td>Demand Amount: <span style="text-decoration: underline;">₹<?php echo e(customNumFormat($demand->balance_amount)); ?></span></td>
        </tr>
        <tr>
            <td></td>
            <td>Known As: <span style="text-decoration: underline;"><?php echo e($address); ?></span></td>
            
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td></td>
            <td>Area (in sqm): <span style="text-decoration: underline;"><?php echo e(customNumFormat(round($flat?$flat->area_in_sqm:($splittedProperty?->area_in_sqm ?? $propertyMaster->propertyLeaseDetail->plot_area_in_sqm),2))); ?></span></td>
            <td>Demand Withdrawal Date: <span style="text-decoration: underline;">N/A</span></td>
        </tr>
        <tr>
            <td></td>
            <td>Status: <span style="text-decoration: underline;"><?php echo e(getServiceNameById($flat?$flat->property_flat_status:($splittedProperty?->property_status ?? $propertyMaster->status))); ?></span></td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <!-- Property Details -->
    <div class="part-title">EXISTING DETAILS OF CHARGES</div>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">S. No.</th>
                <th>Description</th>
                <th>Rate</th>
                <th>Period (From - To)</th>
                <th>Amount</th>
                <th style="width:300px">Calculation Details</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $demandDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
                <tr>
                    <td><strong><?php echo e($loop->iteration); ?></strong></td>
                    <td> <?php if(getServiceCodeById($dd->subhead_id) == "DEM_MANUAL"): ?>
                        <?php echo e($dd->subhead_keys['manual_title']); ?>

                        <?php else: ?><?php echo e(getServiceNameById($dd->subhead_id)); ?>

                        <?php endif; ?>
                    </td>
                    <td></td>
                    <td>  <?php echo e(isset($dd->subhead_keys['manual_date_from'])
                            ? date('d-m-Y', strtotime($dd->subhead_keys['manual_date_from']))
                            : ''); ?>

                        - <br>
                        <?php echo e(isset($dd->subhead_keys['manual_date_to'])
                            ? date('d-m-Y', strtotime($dd->subhead_keys['manual_date_to']))
                            : ''); ?></td>
                    <td>₹&nbsp;<?php echo e(customNumFormat($dd->balance_amount)); ?></td>
                    <td>
                        <?php if(getServiceCodeById($dd->subhead_id) == "DEM_MANUAL"): ?>
                        <?php echo e($dd->subhead_keys['manual_description']); ?>

                        <?php elseif(getServiceCodeById($dd->subhead_id) == "DEM_SETTLED_AMOUNT"): ?>
                        <?php echo e($dd->subhead_keys['settled_title']); ?>

                        <?php else: ?>
                        <?php echo e($dd->formula->formula ?? ''); ?>

                        <br>where<br>
                        <?php echo e($dd->formula->description ?? ''); ?>

                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <tr>
                <td colspan="5">Total Charges:</td>
                <td>₹&nbsp;<?php echo e(customNumFormat($demand->balance_amount)); ?></td>
            </tr>
        </tbody>
    </table>
    

    
    <br/>

    <h4>TERMS & CONDITIONS :</h4>
    <ol>
        <li>The above charges have been calculated against your application or otherwise.</li>
        <li>The charges are to be paid on or before the due date without penalty or with a penalty of 10% upto 60 days beyond the due date. After this period, the demand letter will be automatically withdrawn and the underlying request will be rejected by the system.</li>
        <li>Land & Development Office will initiate action as per the prevailing policy on non-receipt of due charges.</li>
        <li>The payment of the above charges shall be done using the Demand Details and Login Credentials from the edharti 2.0 portal of the Land & Development Office which is accessible from the website of the office i.e. <a href="https://ldo.mohua.gov.in/" target="_blank">www.ldo.mohua.gov.in</a></li>
        <li>Any errors or ommissions shall be brought to the notice of Land & Development Office through written communication well before the due date. However, the written communication shall not act as deterance to the Land & Development Office to levy penalty on delay in making payments of the charges.</li>
        <li>This is a computer-generated demand letter and does not require any signature.</li>
    </ol>

</body>

</html>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/demand/demand_letter_pdf.blade.php ENDPATH**/ ?>