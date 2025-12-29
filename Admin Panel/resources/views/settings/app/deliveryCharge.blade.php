@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.deliveryCharge')}}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.deliveryCharge')}}</li>
                </ol>
            </div>
        </div>
        <div class="card-body">
            <div class="row vendor_payout_create">
                <div class="vendor_payout_create-inner">
                    <fieldset>
                        <legend>{{trans('lang.deliveryCharge')}}</legend>
                        <div class="form-check width-100">
                            <input type="checkbox" class="form-check-inline" id="vendor_can_modify">
                            <label class="col-5 control-label" for="vendor_can_modify">{{ trans('lang.vendor_can_modify')}}</label>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.delivery_charges_per')}} <span class="distance-type"></span></label>
                            <div class="col-7">
                                <input type="number" class="form-control" id="delivery_charges_per_km">
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.minimum_delivery_charges')}}</label>
                            <div class="col-7">
                                <input type="number" class="form-control" id="minimum_delivery_charges">
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.minimum_delivery_charges_within')}} <span class="distance-type"></span></label>
                            <div class="col-7">
                                <input type="number" class="form-control" id="minimum_delivery_charges_within_km">
                            </div>
                        </div>
                        <div class="form-text text-muted pl-4">
                            <strong>{{ trans('lang.delivery_charges_note')}}</strong><br>
                            - <b>{{ trans('lang.vendor_can_modify')}}</b> {{ trans('lang.vendor_can_modify_help')}}<br>
                            - <b>{{ trans('lang.delivery_charges_per')}}</b> {{ trans('lang.delivery_charges_per_help')}}<br>
                            - <b>{{ trans('lang.minimum_delivery_charges')}}</b> {{ trans('lang.minimum_delivery_charges_help')}}<br>
                            - <b>{{ trans('lang.minimum_delivery_charges_within')}}</b> {{ trans('lang.minimum_delivery_charges_within_help')}}
                        </div>
                    </fieldset>

                    <fieldset style="margin-top: 30px;">
                        <legend>{{trans('lang.secteur_delivery')}}</legend>
                        <div class="form-check width-100">
                            <input type="checkbox" class="form-check-inline" id="secteur_delivery_enabled">
                            <label class="col-5 control-label" for="secteur_delivery_enabled">{{ trans('lang.enable_secteur_delivery')}}</label>
                        </div>
                        
                        <div id="secteur_delivery_form" style="display: none; margin-top: 20px;">
                            <div class="form-group row width-100">
                                <div class="col-12">
                                    <h5>{{trans('lang.add_secteur_pair')}}</h5>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.secteur_from')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control" id="secteur_from_input" placeholder="{{trans('lang.enter_secteur_name')}}">
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.secteur_to')}}</label>
                                <div class="col-7">
                                    <input type="text" class="form-control" id="secteur_to_input" placeholder="{{trans('lang.enter_secteur_name')}}">
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.delivery_price')}}</label>
                                <div class="col-7">
                                    <input type="number" class="form-control" id="secteur_price_input" placeholder="{{trans('lang.enter_delivery_price')}}" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <div class="col-12">
                                    <button type="button" class="btn btn-success" id="add_secteur_pair_btn">
                                        <i class="fa fa-plus"></i> {{trans('lang.add_pair')}}
                                    </button>
                                </div>
                            </div>

                            <div class="form-group row width-100" style="margin-top: 30px;">
                                <div class="col-12">
                                    <h5>{{trans('lang.secteur_pairs_list')}}</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="secteur_pairs_table">
                                            <thead>
                                                <tr>
                                                    <th>{{trans('lang.secteur_from')}}</th>
                                                    <th>{{trans('lang.secteur_to')}}</th>
                                                    <th>{{trans('lang.delivery_price')}}</th>
                                                    <th>{{trans('lang.actions')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="secteur_pairs_tbody">
                                                <!-- Les couples seront ajoutés ici dynamiquement -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="form-group col-12 text-center">
                <button type="button" class="btn btn-primary edit-setting-btn"><i
                            class="fa fa-save"></i> {{trans('lang.save')}}</button>
                <a href="{{url('/dashboard')}}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
            </div>
        </div>
@endsection
@section('scripts')
<script type="text/javascript">
    var database = firebase.firestore();
    var ref_deliverycharge = database.collection('settings').doc("DeliveryCharge");
    var driverNearBy = database.collection('settings').doc("DriverNearBy");
    var ref_secteur_delivery = database.collection('settings').doc("SecteurDelivery");
    var secteurPairs = []; // Stocker les couples de secteurs en mémoire

    $(document).ready(function () {
        jQuery("#data-table_processing").show();
        
        // Charger les paramètres de frais de livraison
        ref_deliverycharge.get().then(async function (snapshots_charge) {
            var deliveryChargeSettings = snapshots_charge.data();
            if (deliveryChargeSettings == undefined) {
                database.collection('settings').doc('DeliveryCharge').set({
                    'vendor_can_modify': '',
                    'delivery_charges_per_km': '',
                    'minimum_delivery_charges': '',
                    'minimum_delivery_charges_within_km': ''
                });
            }
            try {
                if (deliveryChargeSettings.vendor_can_modify) {
                    $("#vendor_can_modify").prop('checked', true);
                }
                $("#delivery_charges_per_km").val(deliveryChargeSettings.delivery_charges_per_km);
                $("#minimum_delivery_charges").val(deliveryChargeSettings.minimum_delivery_charges);
                $("#minimum_delivery_charges_within_km").val(deliveryChargeSettings.minimum_delivery_charges_within_km);
            } catch (error) {
            }
        });

        // Charger les paramètres de secteur delivery
        ref_secteur_delivery.get().then(async function (snapshot) {
            var secteurDeliverySettings = snapshot.data();
            if (secteurDeliverySettings) {
                if (secteurDeliverySettings.enabled) {
                    $("#secteur_delivery_enabled").prop('checked', true);
                    $("#secteur_delivery_form").show();
                }
                
                // Charger les couples de secteurs depuis la collection secteur_delivery
                await loadSecteurPairs();
            }
            jQuery("#data-table_processing").hide();
        }).catch(function(error) {
            jQuery("#data-table_processing").hide();
        });

        driverNearBy.get().then(async function (snapshots) {
            var driverNearByData = snapshots.data(); 
            $(".distance-type").text(driverNearByData.distanceType);      
        });

        // Gérer l'affichage/masquage du formulaire selon le checkbox
        $("#secteur_delivery_enabled").change(function() {
            if ($(this).is(":checked")) {
                $("#secteur_delivery_form").show();
            } else {
                $("#secteur_delivery_form").hide();
            }
        });

        // Ajouter un couple de secteurs
        $("#add_secteur_pair_btn").click(function() {
            var secteurFrom = $("#secteur_from_input").val().trim();
            var secteurTo = $("#secteur_to_input").val().trim();
            var price = parseFloat($("#secteur_price_input").val());

            if (secteurFrom === '' || secteurTo === '' || isNaN(price) || price < 0) {
                alert("{{trans('lang.please_enter_details')}}");
                return;
            }

            // Vérifier si le couple existe déjà
            var exists = secteurPairs.some(function(pair) {
                return (pair.secteurFrom === secteurFrom && pair.secteurTo === secteurTo) ||
                       (pair.secteurFrom === secteurTo && pair.secteurTo === secteurFrom);
            });

            if (exists) {
                alert("{{trans('lang.secteur_pair_already_exists')}}");
                return;
            }

            // Ajouter le couple à la liste
            var pair = {
                id: database.collection('tmp').doc().id,
                secteurFrom: secteurFrom,
                secteurTo: secteurTo,
                price: price
            };
            secteurPairs.push(pair);

            // Ajouter au tableau
            addPairToTable(pair);

            // Réinitialiser les champs
            $("#secteur_from_input").val('');
            $("#secteur_to_input").val('');
            $("#secteur_price_input").val('');
        });

        // Fonction pour ajouter un couple au tableau
        function addPairToTable(pair) {
            var row = '<tr data-pair-id="' + pair.id + '">' +
                    '<td>' + pair.secteurFrom + '</td>' +
                    '<td>' + pair.secteurTo + '</td>' +
                    '<td>' + pair.price + '</td>' +
                    '<td>' +
                    '<button type="button" class="btn btn-danger btn-sm delete-pair-btn" data-pair-id="' + pair.id + '">' +
                    '<i class="fa fa-trash"></i> {{trans("lang.delete")}}' +
                    '</button>' +
                    '</td>' +
                    '</tr>';
            $("#secteur_pairs_tbody").append(row);
        }

        // Supprimer un couple
        $(document).on('click', '.delete-pair-btn', function() {
            var pairId = $(this).data('pair-id');
            if (confirm("{{trans('lang.are_you_sure')}}")) {
                secteurPairs = secteurPairs.filter(function(pair) {
                    return pair.id !== pairId;
                });
                $('tr[data-pair-id="' + pairId + '"]').remove();
            }
        });

        // Charger les couples de secteurs depuis Firebase
        async function loadSecteurPairs() {
            secteurPairs = [];
            $("#secteur_pairs_tbody").empty();
            
            await database.collection('secteur_delivery').get().then(function(snapshot) {
                snapshot.forEach(function(doc) {
                    var data = doc.data();
                    var pair = {
                        id: doc.id,
                        secteurFrom: data.secteurFrom,
                        secteurTo: data.secteurTo,
                        price: data.price
                    };
                    secteurPairs.push(pair);
                    addPairToTable(pair);
                });
            });
        }

        // Sauvegarder les paramètres
        $(".edit-setting-btn").click(async function () {
            var checkboxValue = $("#vendor_can_modify").is(":checked");
            var delivery_charges_per_km = parseInt($("#delivery_charges_per_km").val());
            var minimum_delivery_charges = parseInt($("#minimum_delivery_charges").val());
            var minimum_delivery_charges_within_km = parseInt($("#minimum_delivery_charges_within_km").val());
            
            // Sauvegarder les paramètres de frais de livraison
            await database.collection('settings').doc("DeliveryCharge").update({
                'vendor_can_modify': checkboxValue,
                'delivery_charges_per_km': delivery_charges_per_km,
                'minimum_delivery_charges': minimum_delivery_charges,
                'minimum_delivery_charges_within_km': minimum_delivery_charges_within_km
            });

            // Sauvegarder les paramètres de secteur delivery
            var secteurDeliveryEnabled = $("#secteur_delivery_enabled").is(":checked");
            await database.collection('settings').doc("SecteurDelivery").set({
                'enabled': secteurDeliveryEnabled
            }, { merge: true });

            // Sauvegarder les couples de secteurs
            if (secteurDeliveryEnabled) {
                // Supprimer tous les couples existants
                await database.collection('secteur_delivery').get().then(function(snapshot) {
                    snapshot.forEach(function(doc) {
                        doc.ref.delete();
                    });
                });

                // Ajouter tous les nouveaux couples
                var batch = database.batch();
                secteurPairs.forEach(function(pair) {
                    var docRef = database.collection('secteur_delivery').doc(pair.id);
                    batch.set(docRef, {
                        secteurFrom: pair.secteurFrom,
                        secteurTo: pair.secteurTo,
                        price: pair.price,
                        createdAt: firebase.firestore.FieldValue.serverTimestamp()
                    });
                });
                await batch.commit();
            }

            window.location.href = '{{ url("settings/app/deliveryCharge")}}';
        });
    });
</script>
@endsection