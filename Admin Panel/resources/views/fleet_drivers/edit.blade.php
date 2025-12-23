@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.fleet_drivers')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('drivers') !!}">{{trans('lang.fleet_drivers')}}</a>
                    </li>
                    <li class="breadcrumb-item active">{{trans('lang.fleet_driver_edit')}}</li>
                </ol>
            </div>
        </div>
        <div>
            <div class="card-body">
                <div class="error_top"></div>
                <div class="row vendor_payout_create">
                    <div class="vendor_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.driver_details')}}</legend>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.first_name')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control user_first_name"
                                           onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode == 32)">
                                    <div class="form-text text-muted">{{trans('lang.first_name_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.last_name')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control user_last_name"
                                           onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode == 32)">
                                    <div class="form-text text-muted">{{trans('lang.last_name_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.email')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control user_email">
                                    <div class="form-text text-muted">{{trans('lang.user_email_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control user_phone"
                                        onkeypress="return chkAlphabets2(event,'error2')" readonly>
                                    <div id="error2" class="err"></div>
                                    <div class="form-text text-muted">
                                        {{ trans("lang.user_phone_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.user_latitude')}}</label>
                                <div class="col-7">
                                    <input type="number" class="form-control user_latitude"
                                           onkeypress="return chkAlphabets3(event,'error2')">
                                    <div id="error2" class="err"></div>
                                    <div class="form-text text-muted">{{trans('lang.user_latitude_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.user_longitude')}}</label>
                                <div class="col-7">
                                    <input type="number" class="form-control user_longitude"
                                           onkeypress="return chkAlphabets3(event,'error3')">
                                    <div id="error3" class="err"></div>
                                    <div class="form-text text-muted">{{trans('lang.user_longitude_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{ trans('lang.zone') }}<span class="required-field"></span></label>
                                <div class="col-7">
                                    <select id='zone' class="form-control">
                                        <option value="">{{ trans('lang.select_zone') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.profile_image')}}</label>
                                <div class="col-7">
                                    <input type="file" onChange="handleFileSelect(event)" class="">
                                    <div class="form-text text-muted">{{trans('lang.profile_image_help')}}</div>
                                </div>
                                <div class="placeholder_img_thumb user_image">
                                </div>
                                <div id="uploding_image"></div>
                            </div>
                            <div class="form-check width-100">
                                <input type="checkbox" class="col-7 form-check-inline user_active" id="user_active">
                                <label class="col-3 control-label" for="user_active">{{trans('lang.active')}}</label>
                            </div>
                            <div class="form-check width-100">
                                <input type="checkbox" class="col-7 form-check-inline" id="reset_password">
                                <label class="col-3 control-label"
                                       for="reset_password">{{trans('lang.reset_driver_password')}}</label>
                            </div>
                            <div class="form-group row width-100">
                                <div class="form-text text-muted w-100 col-12">
                                    {{ trans("lang.note_reset_driver_password_email") }}
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <div class="col-3 control-label" style="margin-top: 16px;">
                                    <button type="button" class="btn btn-primary"
                                            id="send_mail">{{trans('lang.send_mail')}}
                                    </button>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="vehicle-details" style="display: none">
                        
                            <legend>{{trans('lang.car_details')}}</legend>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label ">{{trans('lang.service_type')}}</label>
                                <div class="col-12">
                                    <select name="service_type" id="service_type" class="form-control service_type" disabled>
                                        <option value="">{{trans('lang.select')}} {{trans('lang.service_type')}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.select_section')}}</label>
                                <div class="col-12">
                                    <select name="vehicle_section_id" id="vehicle_section_id" class="form-control" disabled>
                                        <option value="">{{trans('lang.select_section')}}</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="ride-service" style="display:none;">

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.car_number')}}</label>
                                    <div class="col-7">
                                        <input type="text" class="form-control car_number">
                                        <div class="form-text text-muted">{{trans('lang.car_number_help')}}</div>
                                    </div>
                                </div>
                                
                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.vehicle_type')}}</label>
                                    <div class="col-7">
                                        <select name="vehicle_type" class="form-control vehicle_type">
                                            <option value="">{{trans('lang.select')}} {{trans('lang.vehicle_type')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group row width-50"> 
                                    <label class="col-3 control-label">{{trans('lang.car_make')}}</label>
                                    <div class="col-7">
                                        <select name="car_make" class="form-control car_make">
                                            <option value="">{{trans('lang.select')}} {{trans('lang.car_make')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.car_model')}}</label>
                                    <div class="col-7">
                                        <select name="car_model" class="form-control car_model">
                                            <option value="">{{trans('lang.select')}} {{trans('lang.car_model')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                              
                                 <div class="form-group row width-100" id="div_ride_type" style="display: none">
                                    <label class="col-3 control-label" for="user_active">{{ trans('lang.choose_ride_type') }}</label>
                                    <div class="col-7">
                                        <div id="type_ride" style="display: none">
                                            <input type="radio" class="form-check-inline" name="ride_type" id="ride" value="ride">
                                            <label for="ride">{{ trans('lang.ride') }}</label>
                                        </div>
                                        <div id="type_intercity" style="display: none">
                                            <input type="radio" class="form-check-inline" name="ride_type" id="intercity" value="intercity">
                                            <label for="intercity">{{ trans('lang.intercity') }}</label>
                                        </div>
                                        <div id="type_both" style="display: none">
                                            <input type="radio" class="form-check-inline" name="ride_type" id="both" value="both">
                                            <label for="both">{{ trans('lang.both') }}</label>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                        </fieldset>

                    </div>
                </div>
            </div>
            <div class="form-group col-12 text-center btm-btn">
                <button type="button" class="btn btn-primary edit-form-btn"><i class="fa fa-save"></i> {{
                trans('lang.save')}}
                </button>
                <a href="{!! route('fleet.drivers') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
                trans('lang.cancel')}}</a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script type="text/javascript">

        var id = "{{ $id }}";
        
        var section_id = getCookie('section_id') || '';
        var service_type = getCookie('service_type') || '';

        if (service_type == "cab-service" || service_type == "rental-service") {
            $('.vehicle-details').show();
            $('.ride-service').show();
        }else if (service_type == "parcel_delivery") {
            $('.vehicle-details').show();
        } 
        
        var database = firebase.firestore();
        var ref = database.collection('users').where("id", "==", id);
        var photo = "";
        var fileName = '';
        var oldProfileFile = '';
        
        var storage = firebase.storage();
        var storageRef = firebase.storage().ref('images');
        
        var refZone = database.collection('zone').where('publish', '==', true);
        var refCarMake = database.collection('car_make');
        var refCarModel = database.collection('car_model');

        var refVehicleType = '';
        if (service_type == "cab-service"){
            refVehicleType = database.collection('vehicle_type');
        }else if (service_type == "rental-service"){
            refVehicleType = database.collection('rental_vehicle_type');
        }
    
        var services = database.collection('services').where('flag','in',["rental-service","delivery-service","parcel_delivery","cab-service"]);
        var refSection = database.collection('sections').where('isActive', '==', true);

        var placeholderImage = '';
        var placeholder = database.collection('settings').doc('placeHolderImage');
        placeholder.get().then(async function (snapshotsimage) {
            var placeholderImageData = snapshotsimage.data();
            placeholderImage = placeholderImageData.image;
        })
        
        var currency = database.collection('settings');
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var refCurrency = database.collection('currencies').where('isActive', '==', true);
        refCurrency.get().then(async function (snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimal_degits) {
                decimal_degits = currencyData.decimal_degits;
            }
        });
        
        $("#send_mail").click(function () {
            if ($("#reset_password").is(":checked")) {
                var email = $(".user_email").val();
                firebase.auth().sendPasswordResetEmail(email)
                    .then((res) => {
                        alert('{{trans("lang.driver_mail_sent")}}');
                    })
                    .catch((error) => {
                        console.log('Error password reset: ', error);
                    });
            } else {
                alert('{{trans("lang.error_reset_driver_password")}}');
            }
        });

        $(document).ready(async function () {
            
            jQuery("#data-table_processing").show();

            let sectionRef = await database.collection('sections').doc(section_id).get();
            let sectionData = sectionRef.data();

            if(service_type == "cab-service" && sectionData.rideType != ''){
                $("#div_ride_type").show();
                if(sectionData.rideType == "ride"){
                    $("#div_ride_type #type_ride").show();
                    $("#div_ride_type #type_ride input");
                }else if(sectionData.rideType == "intercity"){
                    $("#div_ride_type #type_intercity").show();
                    $("#div_ride_type #type_intercity input");
                }else if(sectionData.rideType == "both"){
                    $("#div_ride_type #type_ride").show();
                    $("#div_ride_type #type_ride input");
                    $("#div_ride_type #type_intercity").show();
                    $("#div_ride_type #type_both").show();
                }
            }
            
            $('#zone').empty().append(
                $("<option></option>").attr("value", "").attr("disabled", true).attr("selected", 'selected').text("{{ trans('lang.select_zone') }}")
            );
            
            refZone.orderBy('name', 'asc').get().then(async function(snapshots) {
                snapshots.docs.forEach((listval) => {
                    var data = listval.data();
                    $('#zone').append($("<option></option>")
                        .attr("value", data.id)
                        .text(data.name));
                })
            });

            if (service_type == "cab-service" || service_type == "rental-service"){
            
                refCarMake.orderBy('name', 'asc').get().then(async function (snapshots) {
                    snapshots.docs.forEach((listval) => {
                        var data = listval.data();
                        $('.car_make').append($("<option></option>")
                            .attr("value", data.name)
                            .text(data.name));
                    })
                });

                refVehicleType.where('sectionId', '==', section_id).orderBy('name', 'asc').get().then(async function (snapshots) {
                    snapshots.docs.forEach((listval) => {
                        var data = listval.data();
                        $('.vehicle_type').append($("<option></option>")
                            .attr("value", data.name)
                            .attr("data-id", data.id)
                            .text(data.name));
                    })
                });
            }
            
            services.get().then(async function (snapshots) {
                snapshots.docs.forEach((listval) => {
                    var data = listval.data();
                    let option = $("<option></option>").attr("value", data.flag).text(data.name);
                    if (data.flag == service_type) {
                        option.prop("selected", true);
                    }
                    $('.service_type').append(option);
                });
            });

            refSection.where('serviceTypeFlag','==',service_type).get().then(async function (snapshots) {
                snapshots.docs.forEach((listval) => {
                    var data = listval.data();
                    let option = $("<option></option>").attr("value", data.id).text(data.name);
                    if (data.id == section_id) {
                        option.prop("selected", true);
                    }
                    $('#vehicle_section_id').append(option);
                });
            });
            
            ref.get().then(async function (snapshots) {
                var user = snapshots.docs[0].data();
                
                $(".user_first_name").val(user.firstName);
                $(".user_last_name").val(user.lastName);
                $(".user_email").val(shortEmail(user.email)).prop('disabled',true);
                
                $(".car_number").val(user.carNumber);

                if (user.hasOwnProperty('zoneId') && user.zoneId != '') {
                    $("#zone").val(user.zoneId);
                }
                let = phoneNumber = user.countryCode + user.phoneNumber;
                if(phoneNumber){
                    $(".user_phone").val('+' + EditPhoneNumber(phoneNumber.slice(1))).prop('disabled',true);
                }else{
                    $(".user_phone").val(EditPhoneNumber(phoneNumber)).prop('disabled',true);
                }
                if (user.hasOwnProperty('carMakes')) {
                    $('.car_make').val(user.carMakes).trigger('change');
                }
                if (user.hasOwnProperty('carName')) {
                    setTimeout(function(){
                        $('.car_model').val(user.carName);
                    },500);
                }
                if (user.hasOwnProperty('carColor')) {
                    $('.car_color').val(user.carColor);
                }
                if (user.hasOwnProperty('serviceType')) {
                    $('.service_type').val(user.serviceType);
                }
                if (user.hasOwnProperty('vehicleType')) {
                    $('.vehicle_type').val(user.vehicleType);
                }
                if (user.hasOwnProperty('sectionId') && user.sectionId != '') {
                    $('#vehicle_section_id').val(user.sectionId);
                }
                if (user.hasOwnProperty('rideType')) {
                    if(user.rideType == "ride"){
                        $("#div_ride_type #type_ride").show();
                        $("#div_ride_type #type_ride input").prop('checked',true);
                    }else if(user.rideType == "intercity"){
                        $("#div_ride_type #type_intercity").show();
                        $("#div_ride_type #type_intercity input").prop('checked',true);
                    }else if(user.rideType == "both"){
                        $("#div_ride_type #type_ride").show();
                        $("#div_ride_type #type_intercity").show();
                        $("#div_ride_type #type_both").show();
                        $("#div_ride_type #type_both input").prop('checked',true);
                    }
                }
                if (user.hasOwnProperty('vehicleType')) {
                    $('.vehicle_type').val(user.vehicleType).trigger('change');
                }
                if (user.hasOwnProperty('location')) {
                    $(".user_latitude").val(user.location.latitude);
                    $(".user_longitude").val(user.location.longitude);
                }
                oldProfileFile = user.profilePictureURL;
                
                if (user.active) {
                    $(".user_active").prop('checked', true);
                }
                
                if (oldProfileFile != '' && oldProfileFile != null) {
                    $(".user_image").append('<img class="rounded" style="width:50px" src="' + oldProfileFile + '" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'" alt="image">');
                } else {
                    $(".user_image").append('<img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');
                }

                var wallet = 0;
                if (user.wallet_amount) {
                    wallet = user.wallet_amount;
                }
                if (currencyAtRight) {
                    wallet = parseFloat(wallet).toFixed(decimal_degits) + "" + currentCurrency;
                } else {
                    wallet = currentCurrency + "" + parseFloat(wallet).toFixed(decimal_degits);
                }

                $("#wallet_amount").text(wallet);
                
                getTotalOrders(id, user.serviceType);
                
                jQuery("#data-table_processing").hide();
            })

            $(".edit-form-btn").click(function () {

                var userFirstName = $(".user_first_name").val();
                var userLastName = $(".user_last_name").val();
                var email = $(".user_email").val();
                var userPhone = $(".user_phone").val();
                var active = $(".user_active").is(":checked");
                var zoneId = $('#zone option:selected').val();
                
                var latitude = parseFloat($(".user_latitude").val());
                var longitude = parseFloat($(".user_longitude").val());
                var location = { 'latitude': latitude, 'longitude': longitude };
                var vehicleSectionId = $('#vehicle_section_id').val();

                var carNumber = $(".car_number").val() || null;
                var carMakeName = $('.car_make').val() || null;
                var carName = $('.car_model').val() || null;
                
                var vehicleType = $('.vehicle_type').val() || null;
                var vehicleTypeName = $('.vehicle_type option:selected').text() || null;
                var vehicleTypeId = $('.vehicle_type option:selected').data('id') || null;
                var rideType = $("input[name='ride_type']:checked").val() || null;
                
                if (userFirstName == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.user_firstname_error')}}</p>");
                    window.scrollTo(0, 0);
                } else if (userLastName == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.user_lastname_error')}}</p>");
                    window.scrollTo(0, 0);
                } else if(isNaN(latitude)) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.driver_lattitude_error')}}</p>");
                    window.scrollTo(0, 0);
                } else if (latitude < -90 || latitude > 90) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.driver_lattitude_limit_error')}}</p>");
                    window.scrollTo(0, 0);
                } else if (isNaN(longitude)) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.driver_longitude_error')}}</p>");
                    window.scrollTo(0, 0);
                } else if (longitude < -180 || longitude > 180) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.driver_longitude_limit_error')}}</p>");
                    window.scrollTo(0, 0);
                } else if (zoneId == '') {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{ trans('lang.select_zone_help') }}</p>");
                    window.scrollTo(0, 0);
                } else if ((carNumber == '' || carNumber == null) && (service_type == "rental-service" || service_type == "cab-service")) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.car_number_error')}}</p>");
                    window.scrollTo(0, 0);
                } else if ((vehicleType == '' || vehicleType == null) && (service_type === "rental-service" || service_type === "cab-service")){
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.vehicle_type_error')}}</p>");
                    window.scrollTo(0, 0);
                } else if ((carMakeName == '' || carMakeName == null) && (service_type == "rental-service" || service_type == "cab-service")) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.car_make_error')}}</p>");
                    window.scrollTo(0, 0);
                } else if ((carName == '' || carName == null) && (service_type == "rental-service" || service_type == "cab-service")) {
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.car_model_error')}}</p>");
                    window.scrollTo(0, 0);
                } else {

                    jQuery("#data-table_processing").show();

                    storeImageData().then(IMG => {
                        database.collection('users').doc(id).update({
                            'firstName': userFirstName,
                            'lastName': userLastName,
                            'active': active,
                            'profilePictureURL': IMG.profile,
                            'carNumber': carNumber,
                            'carMakes': carMakeName,
                            'carName': carName,
                            'vehicleId': vehicleTypeId,
                            'rideType': rideType,
                            'location': location,
                            'vehicleType': vehicleType,
                            'zoneId': zoneId
                        }).then(function (result) {
                            window.location.href = '{{ route("fleet.drivers")}}';
                        });
                    }).catch(function (error) {
                        jQuery("#data-table_processing").hide();
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>" + error + "</p>");
                        window.scrollTo(0, 0);
                    });
                }
            })
        })

        $('.car_make').on('change', function () {
            var cab_make_name = $(this).val();
            var options = '<option value="">{{trans("lang.select")}} {{trans("lang.car_model")}}</option>';
            refCarModel.where('car_make_name', '==', cab_make_name).orderBy('name', 'asc').get().then(async function (snapshots) {
                snapshots.docs.forEach((listval) => {
                    var data = listval.data();
                    options += '<option value="' + data.name + '" data-id="' + data.id + '">' + data.name + '</option>';
                })
                $(".car_model").html(options);
            });
        })
        
        async function getTotalOrders(id, type) {
            var count_order_complete = 0;
            var url = "Javascript:void(0)";
            var order_text = '';
            if (type == "cab-service") {
                url = "{{route('drivers.rides','driverId')}}";
                url = url.replace('driverId', id);
                await database.collection('rides').where('driverID', '==', id).get().then(async function (orderSnapshots) {
                    count_order_complete = orderSnapshots.docs.length;
                });
                order_text = "{{trans('lang.rides')}}";
            } else if (type == "rental-service") {
                url = "{{route('rental_orders.driver','id')}}";
                url = url.replace("id", id);
                await database.collection('rental_orders').where('driverID', '==', id).get().then(async function (orderSnapshots) {
                    count_order_complete = orderSnapshots.docs.length;
                });
                order_text = "{{trans('lang.rental_orders')}}";
            } else if (type == "delivery-service" || type == "ecommerce-service") {
                url = "{{route('orders','id')}}";
                url = url.replace("id", 'driverId=' + id);
                await database.collection('vendor_orders').where('driverID', '==', id).get().then(async function (orderSnapshots) {
                    count_order_complete = orderSnapshots.docs.length;
                });
                order_text = "{{trans('lang.order_plural')}}";
            } else if (type == "parcel_delivery") {
                url = "{{route('parcel_orders.driver','id')}}";
                url = url.replace("id", id);
                await database.collection('parcel_orders').where('driverID', '==', id).get().then(async function (orderSnapshots) {
                    count_order_complete = orderSnapshots.docs.length;
                });
                order_text = "{{trans('lang.parcel_orders')}}";
            }
            $("#total_orders").text(count_order_complete);
            $('.driver_order_text').html(order_text);
            $('.driver_orders_url').attr('href', url);
        }

        function handleFileSelect(evt) {
            var f = evt.target.files[0];
            var reader = new FileReader();
            reader.onload = (function (theFile) {
                return function (e) {
                    var filePayload = e.target.result;
                    var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                    var val = f.name;
                    var ext = val.split('.')[1];
                    var docName = val.split('fakepath')[1];
                    var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                    var timestamp = Number(new Date());
                    var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                    photo = filePayload;
                    fileName = filename;
                    $(".user_image").empty();
                    $(".user_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image" onerror="this.onerror=null;this.src=\'' + placeholderImage + '\'">');
                };
            })(f);
            reader.readAsDataURL(f);
        }

        async function storeImageData() {
            var newPhoto = [];
            newPhoto['profile'] = '';
            if (photo != '' && photo != oldProfileFile) {
                    if (oldProfileFile != "" && oldProfileFile != null) {
                        var oldImageUrlRef = await storage.refFromURL(oldProfileFile);
                        imageBucket = oldImageUrlRef.bucket;
                        var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                        if (imageBucket == envBucket) {
                            if (oldImageUrlRef) {
                                await oldImageUrlRef.delete().then(() => {
                                    console.log("Old file deleted!")
                                }).catch((error) => {
                                    console.log("ERR File delete ===", error);
                                });
                            }
                        } else {
                            console.log('Bucket not matched');
                        }
                    }
                    try {
                        photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")
                        var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', {contentType: 'image/jpg'});
                        var downloadURL = await uploadTask.ref.getDownloadURL();
                        newPhoto['profile'] = downloadURL;
                        photo = downloadURL;
                    } catch (error) {
                        console.log("ERR ===", error);
                    }
            } else {
                newPhoto['profile'] = oldProfileFile;
            }
            return newPhoto;
        }

        function chkAlphabets3(event, msg) {
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                document.getElementById(msg).innerHTML = "Accept only Number and Dot(.)";
                return false;
            } else {
                document.getElementById(msg).innerHTML = "";
                return true;
            }
        }

    </script>

@endsection
