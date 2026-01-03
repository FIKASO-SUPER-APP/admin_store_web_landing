@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <?php if ($id != '') { ?>
            <h3 class="text-themecolor vendor_name_heading"></h3>
            <?php } else { ?>
            <h3 class="text-themecolor">{{ trans('lang.item_plural') }} - Bulk Import</h3>
            <?php } ?>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{!! route('dashboard') !!}">{{ trans('lang.dashboard') }}</a></li>
                <?php if ($id != '') { ?>
                <li class="breadcrumb-item"><a href="{{ route('vendors.items', $id) }}">{{ trans('lang.item_plural') }}</a></li>
                <?php } else { ?>
                <li class="breadcrumb-item"><a href="{!! route('items') !!}">{{ trans('lang.item_plural') }}</a></li>
                <?php } ?>
                <li class="breadcrumb-item active">Bulk Import</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <?php if ($id != '') { ?>
                <div class="menu-tab">
                    <ul>
                        <li>
                            <a href="{{route('stores.view',$id)}}"><i class="ri-list-indefinite"></i>{{trans('lang.tab_basic')}}</a>
                        </li>
                        <li class="active">
                            <a href="{{route('vendors.items',$id)}}"><i class="ri-shopping-basket-fill"></i>{{trans('lang.tab_items')}}</a>
                        </li>
                        <li>
                            <a href="{{route('vendors.orders',$id)}}"><i class="ri-shopping-bag-line"></i>{{trans('lang.tab_orders')}}</a>
                        </li>
                        <li>
                            <a href="{{route('vendors.reviews',$id)}}"><i class="ri-shield-star-fill"></i>{{trans('lang.tab_reviews')}}</a>
                        </li>
                        <li>
                            <a href="{{route('vendors.coupons',$id)}}"><i class="ri-discount-percent-fill"></i>{{trans('lang.tab_promos')}}</a>
                        </li>
                        <li>
                            <a href="{{route('vendors.payout',$id)}}"><i class="ri-bank-card-line"></i>{{trans('lang.tab_payouts')}}</a>
                        </li>
                        <li>
                            <a href="{{route('payoutRequests.vendor.view',$id)}}"><i class="ri-refund-line"></i>{{trans('lang.tab_payout_request')}}</a>
                        </li>
                        <li>
                            <a class="wallet_transaction"><i class="ri-wallet-line"></i>{{trans('lang.wallet_transaction')}}</a>
                        </li>
                        <li class="dine_in_future" style="display:none;">
                            <a href="{{route('vendors.booktable',$id)}}"><i class="ri-restaurant-line"></i>{{trans('lang.dine_in_booking_history')}}</a>
                        </li>
                        <?php
                        $subscription = route("subscription.subscriptionPlanHistory", ":id");
                        $subscription = str_replace(":id", "storeID=" . $id, $subscription);
                        ?>
                        <li>
                            <a href="{{ $subscription }}"><i class="ri-chat-history-fill"></i>{{trans('lang.subscription_history')}}</a>
                        </li>
                        <li>
                            <a href="{{ route('restaurants.advertisements', $id) }}"><i class="mdi mdi-newspaper"></i>{{ trans('lang.advertisement_plural') }}</a>
                        </li>
                        @php
                            $sectionType = $_COOKIE['service_type'] ?? ''; 
                        @endphp
                        <?php if($sectionType == 'ecommerce-service'){ ?>
                        <?php }else{ ?>
                        <li class="">
                            <a href="{{ route('restaurants.deliveryman', $id) }}"><i class="ri-riding-fill"></i>{{ trans('lang.deliveryman') }}</a>
                        </li>
                        <?php }?>
                    </ul>
                </div>
                <?php } ?>

                <div class="card border">
                    <div class="card-header d-flex justify-content-between align-items-center border-0">
                        <div class="card-header-title">
                            <h3 class="text-dark-2 mb-2 h4">Bulk Import Items</h3>
                            <p class="mb-0 text-dark-2">Import multiple items from a CSV file</p>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="error_top alert alert-danger" style="display:none"></div>
                        <div class="success_top alert alert-success" style="display:none"></div>

                        <!--{}<div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="control-label"><strong>Step 1:</strong> Download CSV Template</label>
                                    <div>
                                        <a href="{{ route('items.download.template') }}" class="btn btn-info">
                                            <i class="mdi mdi-download mr-2"></i>Download Template
                                        </a>
                                        <p class="form-text text-muted mt-2">
                                            Download the CSV template, fill it with your items data, and upload it below.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="control-label"><strong>Step 2:</strong> Select Restaurant <?php if ($id == '') { ?>(Required)<?php } ?></label>
                                    <div>
                                        <?php if ($id == '') { ?>
                                        <select id="vendor_id" class="form-control" required>
                                            <option value="">{{ trans('lang.select_vendor') }}</option>
                                        </select>
                                        <?php } else { ?>
                                        <input type="text" class="form-control" id="vendor_name_display" readonly>
                                        <input type="hidden" id="vendor_id" value="{{ $id }}">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="control-label"><strong>Step 3:</strong> Upload CSV File</label>
                                    <div>
                                        <input type="file" id="csv_file" accept=".csv" class="form-control">
                                        <p class="form-text text-muted mt-2">
                                            Select the CSV file containing your items data. Make sure it follows the template format.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary btn-lg" id="import_btn" onclick="importItems()">
                                    <i class="mdi mdi-upload mr-2"></i>Import Items
                                </button>
                                <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="window.history.back()">
                                    <i class="mdi mdi-close mr-2"></i>Cancel
                                </button>
                            </div>
                        </div>

                        <div class="row mt-4" id="import_progress" style="display:none;">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5>Import Progress</h5>
                                    <div class="progress mt-3" style="height: 30px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                             id="progress_bar" style="width: 0%">0%</div>
                                    </div>
                                    <p class="mt-2 mb-0" id="progress_text">Preparing import...</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4" id="import_results" style="display:none;">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">Import Results</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Total Items Processed:</strong> <span id="total_processed">0</span></p>
                                        <p><strong>Successfully Imported:</strong> <span id="total_success">0</span></p>
                                        <p><strong>Failed:</strong> <span id="total_failed">0</span></p>
                                        <div id="errors_list"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var database = firebase.firestore();
    var section_id = getCookie('section_id') || '';
    var vendorID = "{{ $id }}";
    var storageRef = firebase.storage().ref('images');
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;

    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function (snapshots) {
        if (snapshots.docs.length > 0) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        }
    });

    $(document).ready(function() {
        <?php if ($id != '') { ?>
        getStoreNameFunction(vendorID);
        <?php } else { ?>
        loadVendors();
        <?php } ?>
    });

    async function getStoreNameFunction(vendorId) {
        await database.collection('vendors').where('id', '==', vendorId).get().then(async function (snapshots) {
            if (snapshots.docs.length > 0) {
                var vendorData = snapshots.docs[0].data();
                $('.vendor_name_heading').html("{{trans('lang.item_plural')}} - " + vendorData.title + " - Bulk Import");
                $('#vendor_name_display').val(vendorData.title);
                var wallet_route = "{{route('users.walletstransaction','id')}}";
                $(".wallet_transaction").attr("href", wallet_route.replace('id', 'storeID=' + vendorData.author));
            }
        });
    }

    async function loadVendors() {
        var vendorRef = database.collection('vendors').where('section_id', '==', section_id);
        await vendorRef.get().then(function(snapshots) {
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                $('#vendor_id').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.title));
            });
        });
    }

    function parseCSV(text) {
        const lines = text.split('\n');
        const headers = lines[0].split(',').map(h => h.trim());
        const rows = [];
        
        for (let i = 1; i < lines.length; i++) {
            if (lines[i].trim() === '') continue;
            
            const values = [];
            let currentValue = '';
            let insideQuotes = false;
            
            for (let j = 0; j < lines[i].length; j++) {
                const char = lines[i][j];
                
                if (char === '"') {
                    insideQuotes = !insideQuotes;
                } else if (char === ',' && !insideQuotes) {
                    values.push(currentValue.trim());
                    currentValue = '';
                } else {
                    currentValue += char;
                }
            }
            values.push(currentValue.trim());
            
            if (values.length === headers.length) {
                const row = {};
                headers.forEach((header, index) => {
                    row[header] = values[index];
                });
                rows.push(row);
            }
        }
        
        return rows;
    }

    async function importItems() {
        const selectedVendorId = $('#vendor_id').val();
        const fileInput = document.getElementById('csv_file');
        
        if (!selectedVendorId) {
            $('.error_top').text('Please select a restaurant').show();
            setTimeout(() => $('.error_top').hide(), 3000);
            return;
        }
        
        if (!fileInput.files || fileInput.files.length === 0) {
            $('.error_top').text('Please select a CSV file').show();
            setTimeout(() => $('.error_top').hide(), 3000);
            return;
        }
        
        const file = fileInput.files[0];
        const reader = new FileReader();
        
        reader.onload = async function(e) {
            try {
                const csvData = parseCSV(e.target.result);
                
                if (csvData.length === 0) {
                    $('.error_top').text('The CSV file is empty or invalid').show();
                    setTimeout(() => $('.error_top').hide(), 3000);
                    return;
                }
                
                $('#import_btn').prop('disabled', true);
                $('#import_progress').show();
                $('#import_results').hide();
                
                let successCount = 0;
                let failCount = 0;
                const errors = [];
                
                for (let i = 0; i < csvData.length; i++) {
                    const row = csvData[i];
                    const progress = Math.round(((i + 1) / csvData.length) * 100);
                    
                    $('#progress_bar').css('width', progress + '%').text(progress + '%');
                    $('#progress_text').text(`Processing item ${i + 1} of ${csvData.length}: ${row.name}`);
                    
                    try {
                        await createItemFromCSV(row, selectedVendorId);
                        successCount++;
                    } catch (error) {
                        failCount++;
                        errors.push(`Row ${i + 2}: ${row.name} - ${error.message}`);
                    }
                }
                
                $('#import_progress').hide();
                $('#import_results').show();
                $('#total_processed').text(csvData.length);
                $('#total_success').text(successCount);
                $('#total_failed').text(failCount);
                
                if (errors.length > 0) {
                    let errorHtml = '<div class="alert alert-warning mt-3"><strong>Errors:</strong><ul class="mb-0">';
                    errors.forEach(error => {
                        errorHtml += `<li>${error}</li>`;
                    });
                    errorHtml += '</ul></div>';
                    $('#errors_list').html(errorHtml);
                } else {
                    $('#errors_list').html('');
                }
                
                $('.success_top').text(`Import completed! ${successCount} items imported successfully.`).show();
                setTimeout(() => $('.success_top').hide(), 5000);
                
                $('#import_btn').prop('disabled', false);
                $('#csv_file').val('');
                
            } catch (error) {
                console.error('Import error:', error);
                $('.error_top').text('Error processing CSV file: ' + error.message).show();
                setTimeout(() => $('.error_top').hide(), 5000);
                $('#import_btn').prop('disabled', false);
                $('#import_progress').hide();
            }
        };
        
        reader.readAsText(file);
    }

    async function createItemFromCSV(row, vendorId) {
        const id = database.collection('tmp').doc().id;
        
        const itemData = {
            id: id,
            name: row.name || '',
            price: parseFloat(row.price) || 0,
            disPrice: parseFloat(row.discount) || 0,
            categoryID: row.category_id || '',
            description: row.description || '',
            publish: row.publish === 'true' || row.publish === '1',
            quantity: parseInt(row.quantity) || -1,
            vendorID: vendorId,
            section_id: section_id,
            photo: '',
            photos: [],
            addOnsTitle: [],
            addOnsPrice: [],
            calories: row.calories || '',
            grams: row.grams || '',
            fats: row.fats || '',
            proteins: row.proteins || '',
            nonveg: row.nonveg === 'true' || row.nonveg === '1',
            takeawayOption: row.take_away === 'true' || row.take_away === '1',
            createdAt: firebase.firestore.FieldValue.serverTimestamp(),
            brandID: '',
            item_attribute: null,
            itemAttributes: null
        };
        
        await database.collection('vendor_products').doc(id).set(itemData);
        return id;
    }
</script>
@endsection
