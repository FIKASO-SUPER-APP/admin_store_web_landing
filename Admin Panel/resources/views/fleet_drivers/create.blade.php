@extends('layouts.app')
@section('content')
<?php
$countries = file_get_contents(public_path('countriesdata.json'));
$countries = json_decode($countries);
$countries = (array)$countries;
$newcountries = array();
$newcountriesjs = array();
foreach ($countries as $keycountry => $valuecountry) {
    $newcountries[$valuecountry->phoneCode] = $valuecountry;
    $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
}
?>
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.fleet_drivers')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('fleet.drivers') !!}">{{trans('lang.fleet_drivers')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.fleet_driver_create')}}</li>
            </ol>
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
                                    <input type="email" class="form-control user_email">
                                    <div class="form-text text-muted">{{trans('lang.user_email_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.password')}}</label>
                                <div class="col-7">
                                    <input type="password" class="form-control user_password">
                                    <div class="form-text text-muted">{{trans('lang.user_password_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row"> 
                                <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                                <div class="col-md-6">
                                    <div class="phone-box position-relative" id="phone-box">
                                        <select name="country" id="country_selector">
                                            <?php foreach ($newcountries as $keycy => $valuecy) { ?>
                                                <?php $selected = ""; ?>
                                                <option <?php echo $selected; ?> code="<?php echo $valuecy->code; ?>" value="<?php echo $keycy; ?>">+<?php echo $valuecy->phoneCode; ?> {{$valuecy->countryName}}</option>
                                            <?php } ?>
                                        </select>
                                        <input type="text" class="form-control user_phone"  onkeypress="return chkAlphabets2(event,'error2')">
                                        <div id="error2" class="err"></div>
                                        <div class="form-text text-muted">
                                            {{trans('lang.user_phone_help')}}
                                        </div>
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
                                <label class="col-3 control-label">{{ trans('lang.document_owner') }}<span class="required-field"></span></label>
                                <div class="col-7">
                                    <select id='owner' class="form-control">
                                        <option value="">{{ trans('lang.select_owner') }}</option>
                                    </select>
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
                                <div class="placeholder_img_thumb user_image"></div>
                                <div id="uploding_image"></div>
                            </div>
                            <div class="form-check width-100">
                                <input type="checkbox" class="col-7 form-check-inline user_active" id="user_active">
                                <label class="col-3 control-label" for="user_active">{{trans('lang.active')}}</label>
                            </div>
                        </fieldset>

                        <fieldset class="vehicle-details" style="display: none">
                        
                            <legend>{{trans('lang.car_details')}}</legend>

                            <div class="form-group row width-50">
                                <label class="col-3 control-label ">{{trans('lang.service_type')}}</label>
                                <div class="col-12">
                                    <select name="service_type" id="service_type" class="form-control service_type" disabled>
                                        <option value="">{{trans('lang.select')}} {{trans('lang.service_type')}}</option>
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
                                            <option value="">{{trans('lang.select')}} {{trans('lang.vehicle_type')}}</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group row width-50"> 
                                    <label class="col-3 control-label">{{trans('lang.car_make')}}</label>
                                    <div class="col-7">
                                        <select name="car_make" class="form-control car_make">
                                            <option value="">{{trans('lang.select')}} {{trans('lang.car_make')}}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row width-50">
                                    <label class="col-3 control-label">{{trans('lang.car_model')}}</label>
                                    <div class="col-7">
                                        <select name="car_model" class="form-control car_model">
                                            <option value="">{{trans('lang.select')}} {{trans('lang.car_model')}}</option>
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
                <button type="button" class="btn btn-primary save-form-btn"><i class="fa fa-save"></i> {{
                    trans('lang.save')}}</button>
                <a href="{!! route('fleet.drivers') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
                    trans('lang.cancel')}}</a>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">

    var section_id = getCookie('section_id') || '';
    var service_type = getCookie('service_type') || '';

    if (service_type == "cab-service" || service_type == "rental-service") {
        $('.vehicle-details').show();
        $('.ride-service').show();
    }else if (service_type == "parcel_delivery") {
        $('.vehicle-details').show();
    } 

    var database = firebase.firestore();
    var geoFirestore = new GeoFirestore(database);
    var createdAt = firebase.firestore.FieldValue.serverTimestamp();
    var photo = "";
    var fileName = '';
    
    var refZone = database.collection('zone').where('publish', '==', true);
    var refCarMake = database.collection('car_make');
    var refCarModel = database.collection('car_model');
    var refOwner = database.collection('users').where('isOwner', '==', true).where('ownerId', '==', null).where('role', '==', 'driver');
    
    var refVehicleType = '';
    if (service_type == "cab-service"){
        refVehicleType = database.collection('vehicle_type');
    }else if (service_type == "rental-service"){
        refVehicleType = database.collection('rental_vehicle_type');
    }
    var services = database.collection('services').where('flag','in',["rental-service","delivery-service","parcel_delivery","cab-service"]);
    var refSection = database.collection('sections').where('isActive', '==', true);
    
    $(document).ready(async function () {

        jQuery("#data-table_processing").show();

        let sectionRef = await database.collection('sections').doc(section_id).get();
        let sectionData = sectionRef.data();

        if(service_type == "cab-service" && sectionData.rideType != ''){
            $("#div_ride_type").show();
            if(sectionData.rideType == "ride"){
                $("#div_ride_type #type_ride").show();
                $("#div_ride_type #type_ride input").prop('checked',true);
            }else if(sectionData.rideType == "intercity"){
                $("#div_ride_type #type_intercity").show();
                $("#div_ride_type #type_intercity input").prop('checked',true);
            }else if(sectionData.rideType == "both"){
                $("#div_ride_type #type_ride").show();
                $("#div_ride_type #type_ride input").prop('checked',true);
                $("#div_ride_type #type_intercity").show();
                $("#div_ride_type #type_both").show();
            }
        }

        jQuery("#country_selector").select2({
			templateResult: formatState,
			templateSelection: formatState2,
			placeholder: "Select Country",
			allowClear: true
		});

        // --- ADD THIS BLOCK TO SET DEFAULT COUNTRY CODE ---
        var globalSettingsRef = database.collection('settings').doc('globalSettings');
        globalSettingsRef.get().then(async function (snapshot) {
            var globalSettings = snapshot.data();
            if (globalSettings && globalSettings.defaultCountryCode) {
                var defaultPhoneCode = globalSettings.defaultCountryCode.replace('+', '').trim();
                // Find the option with matching phoneCode
                var $option = $("#country_selector option").filter(function() {
                    return $(this).val() === defaultPhoneCode;
                });
                if ($option.length > 0) {
                    $("#country_selector").val(defaultPhoneCode).trigger('change');
                } else {
                    console.warn("Default country code not found in list:", defaultPhoneCode);
                }
            }
        }).catch(function (error) {
            console.error("Error fetching global settings: ", error);
        });
        // --- END OF DEFAULT COUNTRY LOGIC ---

        refZone.orderBy('name', 'asc').get().then(async function(snapshots) {
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                $('#zone').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.name));
            })
        });

        refOwner.orderBy('firstName','asc').orderBy('lastName','asc').get().then(async function(snapshots) {
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                $('#owner').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.firstName +' '+data.lastName));
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
        
        jQuery("#data-table_processing").hide();
    });

    $(".save-form-btn").click(function () {

        var userFirstName = $(".user_first_name").val();
        var userLastName = $(".user_last_name").val();
        var email = $(".user_email").val();
        var password = $(".user_password").val();
        var country_code = '+' + $("#country_selector").val();
        var userPhone = $(".user_phone").val();
        var active = $(".user_active").is(":checked");
        var ownerId = $('#owner option:selected').val();
        var zoneId = $('#zone option:selected').val();
        
        var latitude = parseFloat($(".user_latitude").val());
        var longitude = parseFloat($(".user_longitude").val());
        var location = { 'latitude': latitude, 'longitude': longitude };
        
        var vehicleSectionId = $('#vehicle_section_id').val() || section_id;

        var carNumber = $(".car_number").val() || null;
        var carMakeName = $('.car_make').val() || null;
        var carName = $('.car_model').val() || null;
        
        var vehicleType = $('.vehicle_type').val() || null;
        var vehicleTypeName = $('.vehicle_type option:selected').text() || null;
        var vehicleTypeId = $('.vehicle_type option:selected').data('id') || null;
        var rideType = $("input[name='ride_type']:checked").val() || null;
        
        var id = database.collection("tmp").doc().id;

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
        } else if (email == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.user_email_error')}}</p>");
            window.scrollTo(0, 0);
        } else if (password == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.user_password_error')}}</p>");
            window.scrollTo(0, 0);
        }else if(!country_code) {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.select_country_code')}}</p>");
            window.scrollTo(0,0);
        } else if (userPhone == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.user_phone_error')}}</p>");
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
        } else if (ownerId == '') {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{ trans('lang.select_owner') }}</p>");
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
            
            firebase.auth().createUserWithEmailAndPassword(email, password).then(function (firebaseUser) {
                    
                id = firebaseUser.user.uid;
                coordinates = new firebase.firestore.GeoPoint(latitude, longitude);

                storeImageData().then(IMG => {
                    geoFirestore.collection('users').doc(id).set({
                        'id': id,
                        'firstName': userFirstName,
                        'lastName': userLastName,
                        'email': email,
                        'phoneNumber': country_code+userPhone,
                        'active': active,
                        'profilePictureURL': IMG.profile,
                        'carNumber': carNumber,
                        'carMakes': carMakeName,
                        'carName': carName,
                        'vehicleId': vehicleTypeId,
                        'sectionId': vehicleSectionId,
                        'rideType': rideType,
                        'carColor': null,
                        'carProofPictureURL': null,
                        'driverProofPictureURL': null,
                        'location': location,
                        'carPictureURL': null,
                        'role': 'driver',
                        'serviceType': service_type,
                        'vehicleType': vehicleType,
                        'carRate': null,
                        'carInfo': null,
                        'userBankDetails': null,
                        'coordinates': coordinates,
                        'createdAt': createdAt,
                        'isDocumentVerify':true,
                        'isOwner': false,
                        'isActive': false,
                        'ownerId': ownerId,
                        'zoneId': zoneId,
                        'provider': 'email',
                        'wallet_amount' : 0,
                    }).then(function (result) {
                        window.location.href = '{{ route("fleet.drivers")}}';
                    });

                }).catch(err => {
                    jQuery("#data-table_processing").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + err + "</p>");
                    window.scrollTo(0, 0);
                });
            }).catch(function (error) {
                jQuery("#data-table_processing").hide();
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>" + error + "</p>");
                window.scrollTo(0, 0);
            });
        }
    });

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

    var storageRef = firebase.storage().ref('images');
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
                $(".user_image").append('<img class="rounded" style="width:50px" src="' + photo + '" alt="image">');
            };
        })(f);
        reader.readAsDataURL(f);
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
    async function storeImageData() {
        var newPhoto = [];
        newPhoto['profile'] = '';
        try {
            if (photo != "") {
                photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto['profile'] = downloadURL;
                photo = downloadURL;
            }
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }
    
    var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
    var newcountriesjs = JSON.parse(newcountriesjs);
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo URL::to('/');?>/scss/icons/flag-icon-css/flags";
        var $state = $(
            '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.svg" class="img-flag" /> ' + state.text + '</span>'
        );
        return $state;
    }

    function formatState2(state) {
        if (!state.id) {
            return state.text;
        }
        var baseUrl = "<?php echo URL::to('/');?>/scss/icons/flag-icon-css/flags"
        var $state = $(
            '<span><img class="img-flag" /> <span></span></span>'
        );
        $state.find("span").text(state.text);
        $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".svg");
        return $state;
    }

    function chkAlphabets2(event,msg)
    {
        if(!(event.which>=48  && event.which<=57)){
            document.getElementById(msg).innerHTML="Accept only Number";
            return false;
        }else{
            document.getElementById(msg).innerHTML="";
            return true;
        }
    }

</script>
@endsection
