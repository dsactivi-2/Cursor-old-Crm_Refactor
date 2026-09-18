<?php use \App\Http\Controllers\HomeController;?>

@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <style>
                        .card {
                            overflow: auto;
                        }
                    </style>
                    <script>
						$(document).ready(function() {
							var token = $('meta[name="csrf-token"]').attr('content');
							var kand_id_ajax = $(document).find('input[name="kand_id_ajax"]').val();
							$.ajax({
								url: '/messenger/updateLastOnline',
								type: 'POST',
								data: {"_token": token},
								dataType: 'html',
								success: function(data) {

								}
							});
							var prviput = false;
							var kandidat_status_messenger =  {{ $kandidat->kandidat_status_messenger }};
							var kandidat_status_obrade =  {{ $kandidat->kandidat_status }};
							//console.log(kandidat_status_obrade);
							if(kandidat_status_messenger == 1 || kandidat_status_messenger == 3 || kandidat_status_messenger == 0){
								prviput = true;
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/updateStatusMessenger',
									type: 'POST',
									data: {"_token": token,'novi_status_mess': "2"},
									dataType: 'html',
									success: function(data) {

									}
								});
							}

							function scrollToBottom(){
                                $('html, body').animate({
                                    scrollTop: $('div.message_field').offset().top + $("div.message_field")[0].scrollHeight
                                }, 'slow');
                            }

                            $('.message').hide();
							if($('.message:eq(3)').hasClass("dipl_poc")){
								$('.message:eq(3)').delay(1000).show(0);
								$('.message:eq(4)').delay(2000).show(0);
								$('.message:eq(5)').delay(3000).show(0);
								$('.message:eq(6)').delay(4000).show(0);
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserDipl',
									type: 'POST',
									data: {"_token": token, "answer": 5},
									dataType: 'html',
									success: function(data) {

									}
								});

							}else if($('.message:eq(3)').hasClass("oglas_poc")){
								$('.message:eq(3)').delay(1000).show(0);
								$('.message:eq(4)').delay(2000).show(0);
								$('.message:eq(5)').delay(3000).show(0);
								var nalogOglas_id = $(document).find('input[name="nalogOglas_id"]').val();
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editBotOglasi',
									type: 'POST',
									data: {"_token": token, "answer": 1, "nalog_id": nalogOglas_id},
									dataType: 'html',
									success: function(data) {

									}
								});
							}else{
								$('.message:eq(0)').delay(1000).show(0);
							}
							//console.log($('.message:eq(1)'));
                            if(prviput){
								$('.message:eq(1)').delay(2000).show(0);
							}else{
								if( kandidat_status_obrade == 1 || kandidat_status_obrade == 5 || kandidat_status_obrade == 6 || kandidat_status_obrade == 9){
									$('.message:eq(2)').delay(2000).show(0);
								}
							}


							if($('.message:eq(3)').hasClass("zadnja_poruka")){
								$('.message:eq(3)').delay(3000).show(0);
								$('.message:eq(4)').delay(4000).show(0);
								console.log("zadnja");
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/updateStatusObrade',
									type: 'POST',
									data: {"_token": token, "status": 4},
									dataType: 'html',
									success: function(data) {
										if(data == 1){
											$.ajax({
												url: 'https://crm.job-step.com/public_kandidati?page=checkCandidateInputs',
												type: 'POST',
												data: {"kand_id_ajax": kand_id_ajax},
												success: function(data) {

												}
											});
										}
									}
								});
							}else if($('.message:eq(3)').hasClass("diploma_doc")){
								var broj_srednjih_iz_baze = {{ count($kandidatSrednje) }};
								if(broj_srednjih_iz_baze < 1){
									//console.log(broj_srednjih_iz_baze);
									//console.log("dipl check");
									broj_srednjih = $(document).find('input[name="broj_srednjih"]').val();
									//console.log(broj_srednjih);

									if(broj_srednjih == 0){
										$('.message:eq(7)').delay(3000).show(0);
										$('.message:eq(8)').delay(4000).show(0);
									}
									if(broj_srednjih == undefined && broj_srednjih_iz_baze == 0){
										$('.message:eq(7)').delay(3000).show(0);
										$('.message:eq(8)').delay(4000).show(0);
									}
								}else{
									$('.message:eq(3)').delay(3000).show(0);
									$('.message:eq(4)').delay(4000).show(0);
								}

								//console.log(showNext.next());
							}else{
								$('.message:eq(3)').delay(3000).show(0);
								$('.message:eq(4)').delay(4000).show(0);
							}
							if($('.message:eq(3)').hasClass("notf_new_year")){
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/updateStatusNotf',
									type: 'POST',
									data: {"_token": token, "status": 2},
									dataType: 'html',
									success: function(data) {

									}
								});
							}


							scrollToBottommmm();

							$(document).on('click', '.viza_prvo_ne', function () {

								document.getElementById("viza_prvo_da").disabled = true;
								document.getElementById("viza_prvo_ne").disabled = true;

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editUserAnswers',
										type: 'POST',
										data: {"_token": token, "answer": "ne_viza"},
										dataType: 'html',
										success: function(data) {

										}
									});

								showNextMessageNr($(this), 2);

                            });

							$(document).on('click', '.add_datum_vize', function () {

								var datum_vize = $(this).parent().find('input[name="datum_vize"]').val();
								if(datum_vize == "")
									alert("Molimo Vas da odaberete validan datum!");
								else{
									document.getElementById("viza_prvo_da").disabled = true;
									document.getElementById("viza_prvo_ne").disabled = true;

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/addViza',
										type: 'POST',
										data: {"_token": token, 'datum_vize': datum_vize},
										dataType: 'html',
										success: function(data) {

										}
									});

									// showNextMessageNr($(this), 2);
									document.getElementById("add_datum_vize").disabled = true;
								}

                            });

							//Izbačene pitanja o apliciranju za vizu i terminu - START

								// $('.viza_da').click(function(){
								//     $('.odg_viza_da').css('display', 'block');
								//     $('.odg_viza_ne').css('display', 'none');
								//     $(".unos_datuma").delay(500).show(0);
								// 	scrollToBottom();
								// 	document.getElementById("viza_da").disabled = true;
								// 	document.getElementById("viza_ne").disabled = true;
								// });

								// $('.viza_ne').click(function(){
								//     $('.odg_viza_ne').css('display', 'inline-block');
								//     $('.odg_viza_da').css('display', 'none');
								// 	scrollToBottom();
								//     showNextMessageBlock($(this));
								// 	//showNextMessageNr($(this), 3);
								// 	document.getElementById("viza_da").disabled = true;
								// 	document.getElementById("viza_ne").disabled = true;

								// 	var token = $('meta[name="csrf-token"]').attr('content');
								// 	$.ajax({
								// 		url: '/messenger/editUserAnswers',
								// 		type: 'POST',
								// 		data: {"_token": token, "answer": "ne_viza"},
								// 		dataType: 'html',
								// 		success: function(data) {

								// 		}
								// 	});


								// });

								// $('.termin_da').click(function(){
								//     $('.odg_termin_da').css('display', 'block');
								//     $(".unos_datuma_termin").delay(500).show(0);
								// 	scrollToBottom();
								// 	document.getElementById("termin_da").disabled = true;
								// 	document.getElementById("termin_ne").disabled = true;

								// });

								// $('.termin_ne').click(function(){
								//     $('.odg_termin_da').css('display', 'none');
								//     $('.unos_datuma_termin').css('display', 'none');
								// 	// showNextMessageBlockAfterTermin($(this));
								//     showNextMessageNr($(this), 2);

								// 	document.getElementById("termin_da").disabled = true;
								// 	document.getElementById("termin_ne").disabled = true;

								// });

								// $(document).on('click', '.add_datum_termina', function () {

								// 	var datum_termina = $(this).parent().find('input[name="datum_termina"]').val();
								// 	if(datum_termina == "")
								// 		alert("Molimo Vas da odaberete validan datum!");
								// 	else{
								// 		//console.log(datum_termina)

								// 		var token = $('meta[name="csrf-token"]').attr('content');
								// 		$.ajax({
								// 			url: '/messenger/addDatumTermina',
								// 			type: 'POST',
								// 			data: {"_token": token,'datum_termina': datum_termina},
								// 			dataType: 'html',
								// 			success: function(data) {

								// 			}
								// 		});
								// 		showNextMessageNr($(this), 5);

								// 		document.getElementById("add_datum_termina").disabled = true;
								// 	}
								// });

								// $(document).on('click', '.add_datum_apl', function () {

								// 	//var datum_apliciranja = $(this).parent().find('input[name="datum_apl_m"]').val();
								// 	var datum_apliciranja_mjesec = $(this).parent().find('select[name="datum_mjesec"]').val();
								// 	var datum_apliciranja_godina = $(this).parent().find('select[name="datum_god"]').val();
								// 	//console.log(datum_apliciranja_mjesec);
								// 	//console.log(datum_apliciranja_godina);
								// 	if(datum_apliciranja_mjesec == null  || datum_apliciranja_godina == null){
								// 		alert("Odaberite mjesec i godinu apliciranja termina")
								// 	}else{
								// 		//console.log("uredu je");

								// 		if(datum_apliciranja_mjesec < 10){
								// 			var datum_apliciranja_mjesec_f = "0" + datum_apliciranja_mjesec;
								// 		}else{
								// 			var datum_apliciranja_mjesec_f = datum_apliciranja_mjesec;
								// 		}

								// 		datum_apliciranja = "01." + datum_apliciranja_mjesec_f + "." + datum_apliciranja_godina;
								// 		//console.log(datum_apliciranja);

								// 		var token = $('meta[name="csrf-token"]').attr('content');
								// 		$.ajax({
								// 			url: '/messenger/addDatumApl',
								// 			type: 'POST',
								// 			data: {"_token": token,'datum_apliciranja': datum_apliciranja},
								// 			dataType: 'html',
								// 			success: function(data) {

								// 			}
								// 		});
								// 		showNextMessage($(this));
								// 		scrollToBottom();

								// 		document.getElementById("add_datum_apl").disabled = true;
								// 	}
								// });
							//Izbačene pitanja o apliciranju za vizu i terminu - END

							$(document).on('click', '.njem_jezik_edit', function () {

								//var jezik = $(this).parent().find('input[name="njem_jezik"]:checked').val();
								var jezik = $(this).parent().find('input[name="option_jezik"]:checked').val();
								//console.log(jezik);
								if(jezik == undefined)
									alert("Molimo Vas da odaberete nivo poznavanja Njemačkog jezika");
								else{
									console.log("uredu je");
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateNjemJezik',
										type: 'POST',
										data: {"_token": token,'jezik': jezik},
										dataType: 'html',
										success: function(data) {

										}
									});
									showNextMessage($(this));
									scrollToBottom();

									document.getElementById("njem_jezik_edit").disabled = true;
									document.getElementById("option1").disabled = true;
									document.getElementById("option2").disabled = true;
									document.getElementById("option3").disabled = true;
									document.getElementById("option4").disabled = true;
									document.getElementById("option5").disabled = true;
									document.getElementById("option6").disabled = true;
									document.getElementById("option7").disabled = true;
								}
							});

							$(document).on('click', '.vozacka_kategorija_check', function () {

								var polozene_kategorije = [];
								$.each($("input[name='option_vozacka_kat']:checked"), function(){
									polozene_kategorije.push($(this).val());
								});
								console.log(polozene_kategorije);
								var kategorije = $(this).parent().find('input[name="option_vozacka_kat1"]:checked').val();

								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/addKategorijeVozacke',
									type: 'POST',
									data: {"_token": token,'polozene_kategorije': polozene_kategorije},
									dataType: 'html',
									success: function(data) {

									}
								});
								showNextMessage($(this));
								scrollToBottom();

								document.getElementById("vozacka_kategorija_check").disabled = true;
								document.getElementById("optionv1").disabled = true;
								document.getElementById("optionv2").disabled = true;
								document.getElementById("optionv3").disabled = true;
								document.getElementById("optionv4").disabled = true;
								document.getElementById("optionv5").disabled = true;
								document.getElementById("optionv6").disabled = true;
							});

							$(document).on('click', '.vozacka_da', function () {
								var odg = "Da";
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/updateVozacka',
									type: 'POST',
									data: {"_token": token, "odg": odg},
									dataType: 'html',
									success: function(data) {

									}
								});
								showNextMessage($(this));
								scrollToBottom();
								document.getElementById("vozacka_da").disabled = true;
								document.getElementById("vozacka_ne").disabled = true;

							});
							$(document).on('click', '.vozacka_ne', function () {
								var odg = "Ne";
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/updateVozacka',
									type: 'POST',
									data: {"_token": token, "odg": odg},
									dataType: 'html',
									success: function(data) {

									}
								});

								provjera_vozacka = $(this).parent().find('input[name="provjera_vozacka"]').val();
								console.log(provjera_vozacka);
								if(provjera_vozacka == "NN" || provjera_vozacka == "")
									showNextMessage($(this));
								else
									showNextMessageNr($(this), 2);

								scrollToBottom();
								document.getElementById("vozacka_da").disabled = true;
								document.getElementById("vozacka_ne").disabled = true;

							});

                            $(document).on('click', '.shownext', function () {
                                showNextMessage($(this));

                                scrollToBottom();
                            });

                            function showNextMessage(thiss){
                                var nextdiv = thiss.parent().parent().parent().next("div");

                                if(nextdiv.attr('class') == "cloning"){
                                    var showNextInLoop = nextdiv.children(".message").first();
                                    showNextInLoop.show();
                                    showNextInLoop.next(".message").delay(500).show(0);
                                    $('.cloning').attr('id', 'cloned_id1');
                                }else if(nextdiv.attr('class') == "cloning_fax"){
                                    var showNextInLoop = nextdiv.children(".message").first();
                                    showNextInLoop.show();
                                    showNextInLoop.next(".message").delay(500).show(0);
                                    $('.cloning_fax').attr('id', 'fax_cloned_id1');
                                }else if(nextdiv.attr('class') == "cloning_iskustvo"){
                                    var showNextInLoop = nextdiv.children(".message").first();
                                    showNextInLoop.show();
                                    showNextInLoop.next(".message").delay(500).show(0);
                                    $('.cloning_iskustvo').attr('id', 'iskustvo_cloned_id1');
                                }else if(nextdiv.attr('class') == "first_msg_cloning_iskustvo"){
                                    var showNextInLoop = nextdiv.children(".message").first();
                                    showNextInLoop.show();
                                    showNextInLoop.next(".message").delay(500).show(0);
                                    $('.first_msg_cloning_iskustvo').attr('id', 'first_msg_iskustvo_cloned_id1');
                                }else if(nextdiv.attr('class') == "cloning_srednja"){
                                    var showNextInLoop = nextdiv.children(".message").first();
                                    showNextInLoop.show();
                                    showNextInLoop.next(".message").delay(500).show(0);
                                    $('.cloning_srednja').attr('id', 'srednja_cloned_id1');
                                }else if(nextdiv.hasClass('diploma_doc')){
									console.log("dipl check");
									var broj_srednjih_iz_baze = {{ count($kandidatSrednje) }};
									broj_srednjih = $(document).find('input[name="broj_srednjih"]').val();
									console.log(broj_srednjih);
									if(broj_srednjih == 0){
										var showNextInLoop = nextdiv.next(".message").next(".message").next(".message").next(".message");
										showNextInLoop.show();
										showNextInLoop.next(".message").delay(500).show(0);
									}else{
										if(broj_srednjih == undefined && broj_srednjih_iz_baze == 0){
											var showNextInLoop = nextdiv.next(".message").next(".message").next(".message").next(".message");
											showNextInLoop.show();
											showNextInLoop.next(".message").delay(500).show(0);
										}else{
											nextdiv.show();
											nextdiv.next(".message").delay(500).show(0);
										}
									}
									//console.log(showNext.next());
								}else{
                                    var showNext = thiss.parent().parent().parent().next(".message");
                                    showNext.show();
                                    showNext.next(".message").delay(500).show(0);
                                }

								if(nextdiv.hasClass("zadnja_poruka")){
									var showNext = thiss.parent().parent().parent().next(".message");
                                    showNext.show();
									console.log("zadnja");
									console.log(showNext);
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateStatusObrade',
										type: 'POST',
										data: {"_token": token, "status": 4},
										dataType: 'html',
										success: function(data) {
											if(data == 1){
												$.ajax({
													url: 'https://crm.job-step.com/public_kandidati?page=checkCandidateInputs',
													type: 'POST',
													data: {"kand_id_ajax": kand_id_ajax},
													success: function(data) {

													}
												});
											}
										}
									});
								}
                            }
							function showNextMessageNr(thiss, nr){
								var parentMessage = thiss.parent().parent().parent();
								//console.log(nr);
								var showNext = parentMessage.nextAll(".message").eq(nr);
								//console.log(showNext);

								if(showNext.hasClass("zadnja_poruka")){

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateStatusObrade',
										type: 'POST',
										data: {"_token": token, "status": 4},
										dataType: 'html',
										success: function(data) {
											if(data == 1){
												$.ajax({
													url: 'https://crm.job-step.com/public_kandidati?page=checkCandidateInputs',
													type: 'POST',
													data: {"kand_id_ajax": kand_id_ajax},
													success: function(data) {

													}
												});
											}
										}
									});
									scrollToBottom();
								}else if(showNext.hasClass("diploma_doc")){
									console.log("dipl check");
									var broj_srednjih_iz_baze = {{ count($kandidatSrednje) }};
									broj_srednjih = $(document).find('input[name="broj_srednjih"]').val();
									console.log(broj_srednjih);
									if(broj_srednjih == 0)
										showNext = showNext.next(".message").next(".message").next(".message").next(".message");
									if(broj_srednjih == undefined && broj_srednjih_iz_baze == 0)
										showNext = showNext.next(".message").next(".message").next(".message").next(".message");
									//console.log(showNext.next());
								}
								if(showNext.hasClass("notf_new_year")){

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateStatusNotf',
										type: 'POST',
										data: {"_token": token, "status": 2},
										dataType: 'html',
										success: function(data) {
										}
									});
									scrollToBottom();
								}
								showNext.show();
								showNext.next(".message").delay(500).show(0);
								scrollToBottom();
							}

                            function showNextMessageBlock(thiss){
                                var showNext = thiss.parent().parent().parent().next(".message").next(".message").next(".message").next(".message");
                                // var showNext2nd = showNext.next(".message");
                                // var showNext3rd = showNext2nd.next(".message");
                                // var showNext4th = showNext3rd.next(".message");

								if(showNext.hasClass("diploma_doc")){
									console.log("dipl check");
									var broj_srednjih_iz_baze = {{ count($kandidatSrednje) }};
									broj_srednjih = $(document).find('input[name="broj_srednjih"]').val();
									console.log(broj_srednjih);
									if(broj_srednjih == 0)
										showNext = showNext.next(".message").next(".message").next(".message").next(".message");
									if(broj_srednjih == undefined && broj_srednjih_iz_baze == 0)
										showNext = showNext.next(".message").next(".message").next(".message").next(".message");
									//console.log(showNext.next());
								}
                                showNext.delay(1100).show(0);
                                scrollToBottom();
                                showNext.next(".message").delay(1600).show(0);
                                scrollToBottom();
                            }
							/*
							function showNextMessageBlockAfterTermin(thiss){
                                var showNext = thiss.parent().parent().parent().next(".message").next(".message").next(".message");

                                showNext.delay(500).show(0);
                                scrollToBottom();
                                showNext.next(".message").delay(1000).show(0);
                                scrollToBottom();
                            }*/

							function scrollToBottommmm(){
                                $('html, body').animate({
                                    scrollTop: $('div.message_field').offset().top + $("div.message_field")[0].scrollHeight
                                }, 5000);
                            }
                            $('#datum_apliciranja').click(function(){
                                var datum_apliciranja = $('#datum_apl').val();
                                //alert(datum_apliciranja);
                            });

                            function showJumpMessage(thiss){
                                var showNext = thiss.parent().parent().parent().next().next();
								if(showNext.hasClass("pocetak_faxa")){
									showNext = showNext.next().next().next();
								}else if(showNext.hasClass("diploma_doc")){
									console.log("dipl check");
									var broj_srednjih_iz_baze = {{ count($kandidatSrednje) }};
									broj_srednjih = $(document).find('input[name="broj_srednjih"]').val();
									console.log(broj_srednjih);
									if(broj_srednjih == 0)
										showNext = showNext.next(".message").next(".message").next(".message").next(".message");
									if(broj_srednjih == undefined && broj_srednjih_iz_baze == 0)
										showNext = showNext.next(".message").next(".message").next(".message").next(".message");
									//console.log(showNext.next());
								}
                                showNext.show();
                                showNext.next(".message").delay(500).show(0);
                                // console.log(thiss.parent().parent().parent().next().next());
                                scrollToBottom();
								if(showNext.hasClass("zadnja_poruka")){

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateStatusObrade',
										type: 'POST',
										data: {"_token": token, "status": 4},
										dataType: 'html',
										success: function(data) {
											if(data == 1){
												$.ajax({
													url: 'https://crm.job-step.com/public_kandidati?page=checkCandidateInputs',
													type: 'POST',
													data: {"kand_id_ajax": kand_id_ajax},
													success: function(data) {

													}
												});
											}
										}
									});
									scrollToBottom();
								}
								if(showNext.hasClass("notf_new_year")){

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateStatusNotf',
										type: 'POST',
										data: {"_token": token, "status": 2},
										dataType: 'html',
										success: function(data) {

										}
									});
									scrollToBottom();
								}
                            }

							//LOOP ZA DODATNU EDUKACIJU
							$(document).on('click', '.dod_edu_ne', function () {
                                showJumpMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								document.getElementById("dod_edu_da").disabled = true;
								document.getElementById("dod_edu_ne").disabled = true;
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserAnswers',
									type: 'POST',
									data: {"_token": token, "answer": "ne_dodatna"},
									dataType: 'html',
									success: function(data) {

									}
								});
                            });

							$(document).on('click', '.dod_edu_da', function () {
                                showNextMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								document.getElementById("dod_edu_da").disabled = true;
								document.getElementById("dod_edu_ne").disabled = true;
                            });

							$(document).on('click', '.button_vrsta_dod_edu', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								vrsta_dod_edu =  $(this).parent().find('select option:selected').val();
								//console.log(vrsta_dod_edu);
								if(vrsta_dod_edu == "")
									alert("Molimo odaberite vrstu!")
								else{
									//console.log("OK godina");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#button_vrsta_dod_edu").prop('disabled', true);
									$(cloningDiv).find("#select_vrsta_dod_edu").prop('disabled', true);
								}
                            });

							$(document).on('click', '.button_naziv_dod_edu', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								naziv_dod_edu =  $(this).parent().find('input[name="naziv_dod"]').val();
								//console.log(naziv_dod_edu);
								if(naziv_dod_edu == "")
									alert("Molimo unesite naziv!")
								else{
									//console.log("OK grad");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#button_naziv_dod_edu").prop('disabled', true);
									$(cloningDiv).find("#naziv_dod").prop('disabled', true);
								}
                            });

							$(document).on('click', '.button_dod_edu_grad', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								dod_edu_grad =  $(this).parent().find('input[name="grad_dod"]').val();
								//console.log(dod_edu_grad);
								if(dod_edu_grad == "")
									alert("Molimo unesite grad!")
								else{
									//console.log("OK grad");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#button_naziv_dod_edu").prop('disabled', true);
									$(cloningDiv).find("#grad_dod").prop('disabled', true);
								}
                            });

							$(document).on('click', '.button_god_dod_edu', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								dod_edu_godina =  $(this).parent().find('select option:selected').val();
								//console.log(dod_edu_godina);
								if(dod_edu_godina == "")
									alert("Molimo odaberite godinu!")
								else{
									//console.log("OK grad");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#button_god_dod_edu").prop('disabled', true);
									$(cloningDiv).find("#godina_dod_edu").prop('disabled', true);
								}
                            });

							$(document).on('click', '.kraj_dod_edu', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								var trenutniDiv_dod = $(this).parent().parent().parent();
								var opisDiv_dod = trenutniDiv_dod.find('input[name="opis_dod"]').val();
								if(opisDiv_dod == "")
									opisDiv_dod = "nema";
								var godinDoDiv_dod = trenutniDiv_dod.prev(".message").prev(".message");
								var dod_obr_godinaDo = godinDoDiv_dod.find('select option:selected').val();

								var gradDiv_dod = godinDoDiv_dod.prev(".message").prev(".message");
								var dod_obr_grad = gradDiv_dod.find('input[name="grad_dod"]').val();

								var nazivDiv_dod = gradDiv_dod.prev(".message").prev(".message");
								var dod_obr_naziv = nazivDiv_dod.find('input[name="naziv_dod"]').val();

								var vrstaDiv_dod = nazivDiv_dod.prev(".message").prev(".message");
								var dod_obr_vrsta = vrstaDiv_dod.find('select option:selected').val();


								// var skolaDiv = zvanjeDiv.prev(".message").prev(".message");
								// var srednje_obr_skola = skolaDiv.find('select option:selected').val();

								console.log(dod_obr_vrsta);
								console.log(dod_obr_naziv);
								console.log(dod_obr_grad);
								console.log(dod_obr_godinaDo);
								console.log(opisDiv_dod);


								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/addDodatnaEdukacija',
									type: 'POST',
									data: {	"_token": token,
											"dod_obr_vrsta": dod_obr_vrsta,
											"dod_obr_naziv": dod_obr_naziv,
											"opisDiv_dod": opisDiv_dod,
											"dod_obr_godinaDo": dod_obr_godinaDo,
											"dod_obr_grad": dod_obr_grad
										},
									dataType: 'html',
									success: function(data) {

									},
									error: function(data){
										// alert("error!!!!");
									}
								});

								showNextMessage($(this));
								scrollToBottom();
								$(cloningDiv).find("#opis_dod_edu").prop('disabled', true);
								$(cloningDiv).find("#kraj_dod_edu").prop('disabled', true);
							});

                            $(document).on('click', '.dod_edu_ne_kraj', function () {
                                var cloningDiv = $(this).parent().parent().parent().parent();
								showNextMessage($(this).parent());
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(cloningDiv).find("#dod_edu_da_kraj").prop('disabled', true);
								$(cloningDiv).find("#dod_edu_ne_kraj").prop('disabled', true);
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserAnswers',
									type: 'POST',
									data: {"_token": token, "answer": "ne_dodatna"},
									dataType: 'html',
									success: function(data) {

									}
								});
                            });

                            $(document).on('click', '.dod_edu_da_kraj', function () {
                                var cloningDiv = $(this).parent().parent().parent().parent();
								showLoopMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(cloningDiv).find("#dod_edu_da_kraj").prop('disabled', true);
								$(cloningDiv).find("#dod_edu_ne_kraj").prop('disabled', true);
                            });
                            function showLoopMessage(thiss){
                                var newClone = $('.cloning').first().clone().find("input:text").val("").end();
                                newClone.insertAfter($('.ending_loop_question').parent().last());
                                newClone.children().hide();
                                var numItems = $('.cloning').length
                                //console.log("asdasd"+ numItems );
                                newID = "cloned_id"+numItems;
                                newClone.attr('id', newID);

                                newClone.find(':input').prop('disabled', false);
								var showNextInLoop =  newClone.children(".message").first();
                                showNextInLoop.show();
                                showNextInLoop.next(".message").delay(500).show(0);
                                //$('#dod_edu_loop').next(".message").clone().delay(500).show();
                               //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
                            }

							//LOOP ZA SREDNJE
							$(document).on('click', '.srednja_ne', function () {
                                showJumpMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								document.getElementById("srednja_da").disabled = true;
								document.getElementById("srednja_ne").disabled = true;

								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserAnswers',
									type: 'POST',
									data: {"_token": token, "answer": "ne_srednja"},
									dataType: 'html',
									success: function(data) {

									}
								});
                            });
							$(document).on('click', '.srednja_da', function () {
                                showNextMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								document.getElementById("srednja_da").disabled = true;
								document.getElementById("srednja_ne").disabled = true;
                            });
							$(document).on('click', '.srednja_ne_kraj', function () {
                                 var cloningDiv = $(this).parent().parent().parent().parent();
								showNextMessage($(this).parent());
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(cloningDiv).find("#srednja_da_kraj").prop('disabled', true);
								$(cloningDiv).find("#srednja_ne_kraj").prop('disabled', true);

								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserAnswers',
									type: 'POST',
									data: {"_token": token, "answer": "ne_srednja"},
									dataType: 'html',
									success: function(data) {

									}
								});
                            });
							$(document).on('click', '.srednja_da_kraj', function () {
                                var cloningDiv = $(this).parent().parent().parent().parent();
								showLoopMessageSrednja($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(cloningDiv).find("#srednja_da_kraj").prop('disabled', true);
								$(cloningDiv).find("#srednja_ne_kraj").prop('disabled', true);
                            });
							function showLoopMessageSrednja(thiss){
                                var newClone = $('.cloning_srednja').first().clone().find("input:text").val("").end();
                                newClone.insertAfter($('.ending_loop_question_srednja').parent().last());
                                newClone.children().hide();
                                var numItems = $('.cloning_srednja').length
                                //console.log("asdasd"+ numItems );
                                newID = "srednja_cloned_id"+numItems;
                                newClone.attr('id', newID);

                                //var zadnji_klon = numItems - 1;
								//console.log(zadnji_klon);
								//$("#cloning_srednja"+zadnji_klon+"").find(':input').prop('disabled', false);
								newClone.find(':input').prop('disabled', false);
								var showNextInLoop =  newClone.children(".message").first();
                                showNextInLoop.show();
                                showNextInLoop.next(".message").delay(500).show(0);
                                //$('#dod_edu_loop').next(".message").clone().delay(500).show();
                               //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
                            }

                        	//LOOP ZA FAKULTETE
							$(document).on('click', '.fax_ne', function () {
                                showJumpMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								document.getElementById("fax_da").disabled = true;
								document.getElementById("fax_ne").disabled = true;
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserAnswers',
									type: 'POST',
									data: {"_token": token, "answer": "ne_visoka"},
									dataType: 'html',
									success: function(data) {

									}
								});
                            });
                            $(document).on('click', '.fax_da', function () {
                                showNextMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								document.getElementById("fax_da").disabled = true;
								document.getElementById("fax_ne").disabled = true;
                            });
                            $(document).on('click', '.fax_ne_kraj', function () {
                                var cloningDiv = $(this).parent().parent().parent().parent();
								showNextMessage($(this).parent());
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(cloningDiv).find("#fax_da_kraj").prop('disabled', true);
								$(cloningDiv).find("#fax_ne_kraj").prop('disabled', true);
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserAnswers',
									type: 'POST',
									data: {"_token": token, "answer": "ne_visoka"},
									dataType: 'html',
									success: function(data) {

									}
								});
                            });
                            $(document).on('click', '.fax_da_kraj', function () {
                                var cloningDiv = $(this).parent().parent().parent().parent();
								showLoopMessageFax($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(cloningDiv).find("#fax_da_kraj").prop('disabled', true);
								$(cloningDiv).find("#fax_ne_kraj").prop('disabled', true);
                            });
                            function showLoopMessageFax(thiss){
                                var newClone = $('.cloning_fax').first().clone().find("input:text").val("").end();
                                newClone.insertAfter($('.ending_loop_question_fax').parent().last());
                                newClone.children().hide();
                                var numItems = $('.cloning_fax').length
                                //console.log("asdasd"+ numItems );
                                newID = "fax_cloned_id"+numItems;
                                newClone.attr('id', newID);

								newClone.find(':input').prop('disabled', false);
                                var showNextInLoop =  newClone.children(".message").first();
                                showNextInLoop.show();
                                showNextInLoop.next(".message").delay(500).show(0);
                                //$('#dod_edu_loop').next(".message").clone().delay(500).show();
                               //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
                            }

							//ISKUSTVO STARE STVARI - START
								$(document).on('click', '.iskustvo_ne', function () {
									showJumpMessage($(this));
									//console.log($(this).parent().parent().parent().next(".message"));
									scrollToBottom();
									document.getElementById("iskustvo_da").disabled = true;
									document.getElementById("iskustvo_ne").disabled = true;
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editUserAnswers',
										type: 'POST',
										data: {"_token": token, "answer": "ne_iskustvo"},
										dataType: 'html',
										success: function(data) {

										}
									});
								});
								$(document).on('click', '.iskustvo_da', function () {
									showNextMessage($(this));
									//console.log($(this).parent().parent().parent().next(".message"));
									scrollToBottom();
									document.getElementById("iskustvo_da").disabled = true;
									document.getElementById("iskustvo_ne").disabled = true;
								});
								$(document).on('click', '.button_isk_pozicija', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();

									isk_pozicija =  $(this).parent().find('input[name="pozicija_iskustvo"]').val();
									//console.log(isk_pozicija);
									if(isk_pozicija == "")
										alert("Molimo unesite poziciju!")
									else{
										//console.log("OK grad");
										showNextMessage($(this));
										scrollToBottom();
										$(cloningDiv).find("#button_isk_pozicija").prop('disabled', true);
										$(cloningDiv).find("#pozicija_iskustvo").prop('disabled', true);
									}
								});
								$(document).on('click', '.button_isk_poslodavac', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();

									isk_poslodavac =  $(this).parent().find('input[name="poslodavac_iskustvo"]').val();
									//console.log(isk_poslodavac);
									if(isk_poslodavac == "")
										alert("Molimo unesite poslodavnca!")
									else{
										//console.log("OK grad");
										showNextMessage($(this));
										scrollToBottom();
										$(cloningDiv).find("#button_isk_poslodavac").prop('disabled', true);
										$(cloningDiv).find("#poslodavac_iskustvo").prop('disabled', true);
									}
								});
								$(document).on('click', '.button_isk_datum_od', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();
									isk_datum_od_mjesec =  cloningDiv.find('select[name="datum_mjesec_isk"]').val();
									isk_datum_od_god =  cloningDiv.find('select[name="datum_god_isk"]').val();
									// console.log(isk_datum_od_mjesec);
									// console.log(isk_datum_od_god);
									if(isk_datum_od_mjesec == null  || isk_datum_od_god == null){
										alert("Odaberite mjesec i godinu")
									}else{
										showNextMessage($(this));
										scrollToBottom();
										$(cloningDiv).find("#button_isk_datum_od").prop('disabled', true);
										$(cloningDiv).find("#datum_mjesec_isk").prop('disabled', true);
										$(cloningDiv).find("#datum_god_isk").prop('disabled', true);
									}

								});
								$(document).on('click', '.button_isk_datum_do', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();
									var mjesec_select = cloningDiv.find('select[name="datum_mjesec_isk_do"]').val();
									var god_select = cloningDiv.find('select[name="datum_god_isk_do"]').val();
									var isk_datum_do_mjesec =  parseInt(mjesec_select, 10);
									var isk_datum_do_god =  parseInt(god_select, 10);

									var prethodniDiv = $(this).parent().parent().parent().prev(".message").prev(".message");
									isk_datum_od_mjesec =  parseInt(prethodniDiv.find('select[name="datum_mjesec_isk"]').val(), 10);
									isk_datum_od_god =  parseInt(prethodniDiv.find('select[name="datum_god_isk"]').val(), 10);


									var aktuelno_check = $(this).parent().find('input[name="kri_datum_do_aktuelno"]');

									if (aktuelno_check.is(':checked')){
										var today = new Date();
										var isk_datum_do_mjesec = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
										var isk_datum_do_god = today.getFullYear();
										mjesec_select = 1;
										god_select = 1
									}

									if(mjesec_select == null  || god_select == null ){
										alert("Odaberite mjesec i godinu")
									}else{
										if(isk_datum_od_god > isk_datum_do_god){
											alert("Molimo da unesete datum koji nije stariji od datuma pocetka rada!");
										}else{
											if(isk_datum_od_god == isk_datum_do_god && isk_datum_od_mjesec > isk_datum_do_mjesec)
												alert("Molimo da unesete datum koji nije stariji od datuma pocetka rada!");
											else{
												showNextMessage($(this));
												scrollToBottom();
												$(cloningDiv).find("#button_isk_datum_do").prop('disabled', true);
												$(cloningDiv).find("#datum_mjesec_isk_do").prop('disabled', true);
												$(cloningDiv).find("#datum_god_isk_do").prop('disabled', true);
												$(cloningDiv).find("#kri_datum_do_aktuelno").prop('disabled', true);

											}
										}
									}

								});
								$(document).on('click', '.button_isk_grad', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();

									isk_grad =  $(this).parent().find('input[name="grad_iskustvo"]').val();

									if(isk_grad == "")
										alert("Molimo unesite grad!")
									else{

										var trenutniDiv_isk = $(this).parent().parent().parent();
										var datumDoDiv = trenutniDiv_isk.prev(".message").prev(".message");

										var aktuelno_check = cloningDiv.find('input[name="kri_datum_do_aktuelno"]');
										if (aktuelno_check.is(':checked')){
											datum_iskustvo_do = "";
											var aktuelno = 1;
										}else{
											var datum_iskustvo_mjesec_do = datumDoDiv.find('select[name="datum_mjesec_isk_do"]').val();
											if(datum_iskustvo_mjesec_do < 10){
												var datum_iskustvo_mjesec_do_f = "0" + datum_iskustvo_mjesec_do;
											}else{
												var datum_iskustvo_mjesec_do_f = datum_iskustvo_mjesec_do;
											}
											var datum_iskustvo_godina_do = datumDoDiv.find('select[name="datum_god_isk_do"]').val();
											datum_iskustvo_do = "01." + datum_iskustvo_mjesec_do_f + "." + datum_iskustvo_godina_do;
											var aktuelno = 2;
										}
										var datumOdDiv = datumDoDiv.prev(".message").prev(".message");
										var datum_iskustvo_mjesec_od = datumOdDiv.find('select[name="datum_mjesec_isk"]').val();
										if(datum_iskustvo_mjesec_od < 10){
											var datum_iskustvo_mjesec_od_f = "0" + datum_iskustvo_mjesec_od;
										}else{
											var datum_iskustvo_mjesec_od_f = datum_iskustvo_mjesec_od;
										}
										var datum_iskustvo_godina_od = datumOdDiv.find('select[name="datum_god_isk"]').val();
										datum_iskustvo_od = "01." + datum_iskustvo_mjesec_od_f + "." + datum_iskustvo_godina_od;

										var poslodavacDiv_isk = datumOdDiv.prev(".message").prev(".message");
										var iskustvo_poslodavac = poslodavacDiv_isk.find('input[name="poslodavac_iskustvo"]').val();

										var pozicijaDiv_isk = poslodavacDiv_isk.prev(".message").prev(".message");
										var iskustvo_pozicija = pozicijaDiv_isk.find('input[name="pozicija_iskustvo"]').val();

										var token = $('meta[name="csrf-token"]').attr('content');
										$.ajax({
											url: '/messenger/addIskustvo',
											type: 'POST',
											data: {	"_token": token,
													"iskustvo_pozicija": iskustvo_pozicija,
													"iskustvo_poslodavac": iskustvo_poslodavac,
													"datum_iskustvo_od": datum_iskustvo_od,
													"datum_iskustvo_do": datum_iskustvo_do,
													"iskustvo_grad": isk_grad,
													"aktuelno": aktuelno
												},
											dataType: 'html',
											success: function(data) {

											},
											error: function(data){
												// alert("error!!!!");
											}
										});

										showNextMessage($(this));
										scrollToBottom();
										$(cloningDiv).find("#button_isk_grad").prop('disabled', true);
										$(cloningDiv).find("#grad_iskustvo").prop('disabled', true);
									}
								});
								$(document).on('click', '.iskustvo_ne_kraj', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();
									showNextMessage($(this).parent());
									//console.log($(this).parent().parent().parent().next(".message"));
									scrollToBottom();
									$(cloningDiv).find("#iskustvo_da_kraj").prop('disabled', true);
									$(cloningDiv).find("#iskustvo_ne_kraj").prop('disabled', true);
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editUserAnswers',
										type: 'POST',
										data: {"_token": token, "answer": "ne_iskustvo"},
										dataType: 'html',
										success: function(data) {

										}
									});
								});
								$(document).on('click', '.iskustvo_da_kraj', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();
									showLoopMessageIskustvo($(this));
									//console.log($(this).parent().parent().parent().next(".message"));
									scrollToBottom();
									$(cloningDiv).find("#iskustvo_da_kraj").prop('disabled', true);
									$(cloningDiv).find("#iskustvo_ne_kraj").prop('disabled', true);
								});
								function showLoopMessageIskustvo(thiss){
									var newClone = $('.cloning_iskustvo').first().clone().find("input:text").val("").end();
									newClone.insertAfter($('.ending_loop_question_iskustvo').parent().last());
									newClone.children().hide();
									var numItems = $('.cloning_iskustvo').length
									//console.log("asdasd"+ numItems );
									newID = "iskustvo_cloned_id"+numItems;
									newClone.attr('id', newID);

									newClone.find(':input').prop('disabled', false);
									newClone.find('#kri_datum_do_aktuelno').prop('checked', false);
									var showNextInLoop =  newClone.children(".message").first();
									showNextInLoop.show();
									showNextInLoop.next(".message").delay(500).show(0);
									//$('#dod_edu_loop').next(".message").clone().delay(500).show();
									//console.log($(this).parent().parent().parent().next(".message"));
									scrollToBottom();
								}
								$(document).on('click', '#kri_datum_do_aktuelno', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();
									var checker = cloningDiv.find('input[name="kri_datum_do_aktuelno"]');
									//If checkbox is checked then disable or enable input
									if (checker.is(':checked'))
									{
										cloningDiv.find("#datum_mjesec_isk_do").attr("disabled","disabled");
										cloningDiv.find('#datum_mjesec_isk_do').val('');

										cloningDiv.find("#datum_god_isk_do").attr("disabled","disabled");
										cloningDiv.find('#datum_god_isk_do').val('');

									}
									//If checkbox is unchecked then disable or enable input
									else
									{
										cloningDiv.find("#datum_mjesec_isk_do").removeAttr("disabled");
										cloningDiv.find("#datum_god_isk_do").removeAttr("disabled");
									}
								});
							//ISKUSTVO STARE STVARI - END

							//NOVO ISKUSTVO - START
								$(document).on('click', '.first_msg_iskustvo_ne', function () {
									showJumpMessage($(this));
									//console.log($(this).parent().parent().parent().next(".message"));
									scrollToBottom();
									document.getElementById("first_msg_iskustvo_da").disabled = true;
									document.getElementById("first_msg_iskustvo_ne").disabled = true;
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editUserAnswers',
										type: 'POST',
										data: {"_token": token, "answer": "ne_iskustvo"},
										dataType: 'html',
										success: function(data) {

										}
									});
								});
								$(document).on('click', '.first_msg_iskustvo_da', function () {
									showNextMessage($(this));
									//console.log($(this).parent().parent().parent().next(".message"));
									scrollToBottom();
									document.getElementById("first_msg_iskustvo_da").disabled = true;
									document.getElementById("first_msg_iskustvo_ne").disabled = true;
								});
								$(document).on('click', '.first_msg_button_isk_pozicija', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();

									isk_pozicija =  $(this).parent().find('input[name="first_msg_pozicija_iskustvo"]').val();
									//console.log(isk_pozicija);
									if(isk_pozicija == "")
										alert("Molimo unesite poziciju!")
									else{
										//console.log("OK grad");
										showNextMessage($(this));
										scrollToBottom();
										$(cloningDiv).find("#first_msg_button_isk_pozicija").prop('disabled', true);
										$(cloningDiv).find("#first_msg_pozicija_iskustvo").prop('disabled', true);
									}
								});
								$(document).on('click', '.first_msg_button_isk_poslodavac', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();

									isk_poslodavac =  $(this).parent().find('input[name="first_msg_poslodavac_iskustvo"]').val();
									//console.log(isk_poslodavac);
									if(isk_poslodavac == "")
										alert("Molimo unesite poslodavnca!")
									else{
										//console.log("OK grad");
										showNextMessage($(this));
										scrollToBottom();
										$(cloningDiv).find("#first_msg_button_isk_poslodavac").prop('disabled', true);
										$(cloningDiv).find("#first_msg_poslodavac_iskustvo").prop('disabled', true);
									}
								});
								$(document).on('click', '.first_msg_button_isk_datum_od', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();
									isk_datum_od_mjesec =  cloningDiv.find('select[name="first_msg_datum_mjesec_isk"]').val();
									isk_datum_od_god =  cloningDiv.find('select[name="first_msg_datum_god_isk"]').val();
									// console.log(isk_datum_od_mjesec);
									// console.log(isk_datum_od_god);
									if(isk_datum_od_mjesec == null  || isk_datum_od_god == null){
										alert("Odaberite mjesec i godinu")
									}else{
										showNextMessage($(this));
										scrollToBottom();
										$(cloningDiv).find("#first_msg_button_isk_datum_od").prop('disabled', true);
										$(cloningDiv).find("#first_msg_datum_mjesec_isk").prop('disabled', true);
										$(cloningDiv).find("#first_msg_datum_god_isk").prop('disabled', true);
									}

								});
								$(document).on('click', '.first_msg_button_isk_datum_do', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();
									var mjesec_select = cloningDiv.find('select[name="first_msg_datum_mjesec_isk_do"]').val();
									var god_select = cloningDiv.find('select[name="first_msg_datum_god_isk_do"]').val();
									var isk_datum_do_mjesec =  parseInt(mjesec_select, 10);
									var isk_datum_do_god =  parseInt(god_select, 10);

									var prethodniDiv = $(this).parent().parent().parent().prev(".message").prev(".message");
									isk_datum_od_mjesec =  parseInt(prethodniDiv.find('select[name="first_msg_datum_mjesec_isk"]').val(), 10);
									isk_datum_od_god =  parseInt(prethodniDiv.find('select[name="first_msg_datum_god_isk"]').val(), 10);


									var aktuelno_check = $(this).parent().find('input[name="first_msg_kri_datum_do_aktuelno"]');

									if (aktuelno_check.is(':checked')){
										var today = new Date();
										var isk_datum_do_mjesec = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
										var isk_datum_do_god = today.getFullYear();
										mjesec_select = 1;
										god_select = 1
									}

									if(mjesec_select == null  || god_select == null ){
										alert("Odaberite mjesec i godinu")
									}else{
										if(isk_datum_od_god > isk_datum_do_god){
											alert("Molimo da unesete datum koji nije stariji od datuma pocetka rada!");
										}else{
											if(isk_datum_od_god == isk_datum_do_god && isk_datum_od_mjesec > isk_datum_do_mjesec)
												alert("Molimo da unesete datum koji nije stariji od datuma pocetka rada!");
											else{
												showNextMessage($(this));
												scrollToBottom();
												$(cloningDiv).find("#first_msg_button_isk_datum_do").prop('disabled', true);
												$(cloningDiv).find("#first_msg_datum_mjesec_isk_do").prop('disabled', true);
												$(cloningDiv).find("#first_msg_datum_god_isk_do").prop('disabled', true);
												$(cloningDiv).find("#first_msg_kri_datum_do_aktuelno").prop('disabled', true);

											}
										}
									}

								});
								$(document).on('click', '.first_msg_button_isk_grad', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();

									isk_grad =  $(this).parent().find('input[name="first_msg_grad_iskustvo"]').val();

									if(isk_grad == "")
										alert("Molimo unesite grad!")
									else{

										var trenutniDiv_isk = $(this).parent().parent().parent();
										var datumDoDiv = trenutniDiv_isk.prev(".message").prev(".message");

										var aktuelno_check = cloningDiv.find('input[name="first_msg_kri_datum_do_aktuelno"]');
										if (aktuelno_check.is(':checked')){
											datum_iskustvo_do = "";
											var aktuelno = 1;
										}else{
											var datum_iskustvo_mjesec_do = datumDoDiv.find('select[name="first_msg_datum_mjesec_isk_do"]').val();
											if(datum_iskustvo_mjesec_do < 10){
												var datum_iskustvo_mjesec_do_f = "0" + datum_iskustvo_mjesec_do;
											}else{
												var datum_iskustvo_mjesec_do_f = datum_iskustvo_mjesec_do;
											}
											var datum_iskustvo_godina_do = datumDoDiv.find('select[name="first_msg_datum_god_isk_do"]').val();
											datum_iskustvo_do = "01." + datum_iskustvo_mjesec_do_f + "." + datum_iskustvo_godina_do;
											var aktuelno = 2;
										}
										var datumOdDiv = datumDoDiv.prev(".message").prev(".message");
										var datum_iskustvo_mjesec_od = datumOdDiv.find('select[name="first_msg_datum_mjesec_isk"]').val();
										if(datum_iskustvo_mjesec_od < 10){
											var datum_iskustvo_mjesec_od_f = "0" + datum_iskustvo_mjesec_od;
										}else{
											var datum_iskustvo_mjesec_od_f = datum_iskustvo_mjesec_od;
										}
										var datum_iskustvo_godina_od = datumOdDiv.find('select[name="first_msg_datum_god_isk"]').val();
										datum_iskustvo_od = "01." + datum_iskustvo_mjesec_od_f + "." + datum_iskustvo_godina_od;

										var poslodavacDiv_isk = datumOdDiv.prev(".message").prev(".message");
										var iskustvo_poslodavac = poslodavacDiv_isk.find('input[name="first_msg_poslodavac_iskustvo"]').val();

										var pozicijaDiv_isk = poslodavacDiv_isk.prev(".message").prev(".message");
										var iskustvo_pozicija = pozicijaDiv_isk.find('input[name="first_msg_pozicija_iskustvo"]').val();

										var token = $('meta[name="csrf-token"]').attr('content');
										$.ajax({
											url: '/messenger/addIskustvo',
											type: 'POST',
											data: {	"_token": token,
													"iskustvo_pozicija": iskustvo_pozicija,
													"iskustvo_poslodavac": iskustvo_poslodavac,
													"datum_iskustvo_od": datum_iskustvo_od,
													"datum_iskustvo_do": datum_iskustvo_do,
													"iskustvo_grad": isk_grad,
													"aktuelno": aktuelno
												},
											dataType: 'html',
											success: function(data) {

											},
											error: function(data){
												// alert("error!!!!");
											}
										});

										showNextMessage($(this));
										scrollToBottom();
										$(cloningDiv).find("#first_msg_button_isk_grad").prop('disabled', true);
										$(cloningDiv).find("#first_msg_grad_iskustvo").prop('disabled', true);
									}
								});
								$(document).on('click', '.first_msg_iskustvo_ne_kraj', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();
									showNextMessage($(this).parent());
									//console.log($(this).parent().parent().parent().next(".message"));
									scrollToBottom();
									$(cloningDiv).find("#first_msg_iskustvo_da_kraj").prop('disabled', true);
									$(cloningDiv).find("#first_msg_iskustvo_ne_kraj").prop('disabled', true);
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editUserAnswers',
										type: 'POST',
										data: {"_token": token, "answer": "ne_iskustvo"},
										dataType: 'html',
										success: function(data) {

										}
									});
								});
								$(document).on('click', '.first_msg_iskustvo_da_kraj', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();
									showLoopMessageIskustvoFirstMsg($(this));
									//console.log($(this).parent().parent().parent().next(".message"));
									scrollToBottom();
									$(cloningDiv).find("#first_msg_iskustvo_da_kraj").prop('disabled', true);
									$(cloningDiv).find("#first_msg_iskustvo_ne_kraj").prop('disabled', true);
								});
								function showLoopMessageIskustvoFirstMsg(thiss){
									var newClone = $('.first_msg_cloning_iskustvo').first().clone().find("input:text").val("").end();
									newClone.insertAfter($('.first_msg_ending_loop_question_iskustvo').parent().last());
									newClone.children().hide();
									var numItems = $('.first_msg_cloning_iskustvo').length
									//console.log("asdasd"+ numItems );
									newID = "first_msg_iskustvo_cloned_id"+numItems;
									newClone.attr('id', newID);

									newClone.find(':input').prop('disabled', false);
									newClone.find('#first_msg_kri_datum_do_aktuelno').prop('checked', false);
									var showNextInLoop =  newClone.children(".message").first();
									showNextInLoop.show();
									showNextInLoop.next(".message").delay(500).show(0);
									//$('#dod_edu_loop').next(".message").clone().delay(500).show();
									//console.log($(this).parent().parent().parent().next(".message"));
									scrollToBottom();
								}
								$(document).on('click', '#first_msg_kri_datum_do_aktuelno', function () {
									var cloningDiv = $(this).parent().parent().parent().parent();
									var checker = cloningDiv.find('input[name="first_msg_kri_datum_do_aktuelno"]');
									//If checkbox is checked then disable or enable input
									if (checker.is(':checked'))
									{
										cloningDiv.find("#first_msg_datum_mjesec_isk_do").attr("disabled","disabled");
										cloningDiv.find('#first_msg_datum_mjesec_isk_do').val('');

										cloningDiv.find("#first_msg_datum_god_isk_do").attr("disabled","disabled");
										cloningDiv.find('#first_msg_datum_god_isk_do').val('');

									}
									//If checkbox is unchecked then disable or enable input
									else
									{
										cloningDiv.find("#first_msg_datum_mjesec_isk_do").removeAttr("disabled");
										cloningDiv.find("#first_msg_datum_god_isk_do").removeAttr("disabled");
									}
								});
							//NOVO ISKUSTVO - END

							//DA LI JE IZABRANA SREDNJA ILI JE OSTALO
							$(document).on('click', '.odabir_srednje', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();
								//console.log(cloningDiv);

								var optionSrednja =  $(this).parent().find('select option:selected').val();
								//console.log(optionSrednja);
								if(optionSrednja == ""){
									alert("Molimo Vas da odaberete srednju školu")
								}else{
									//console.log("OK");
									if(optionSrednja == "ostalo"){
										showNextMessage($(this));
										scrollToBottom();
									}else{
										$('.svi_smjerovi').hide();
										$('.svi_smjerovi_faks').hide();

										//var cloningDiv = $(this).parent().parent().parent().parent();
										console.log(cloningDiv);
										//$('.smjerovi_all').addClass('hidden');
										var skola_idd = $(cloningDiv).find('#skole_picker option:selected').data('skolaa_id');
										$(cloningDiv).find('.opt_'+skola_idd+'').show();

										showNextMessageNr($(this), 4);
									}
									scrollToBottom();
									$(cloningDiv).find("#odabir_srednje").prop('disabled', true);
									$(cloningDiv).find("#skole_picker").prop('disabled', true);
								}
                            });

							$(document).on('click', '.ostalo_srednje_kraj', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								srednje_obr_unesen_smjer =  $(this).parent().find('input[name="ostalo_smjer"]').val();
								//console.log(srednje_obr_unesen_smjer);
								if(srednje_obr_unesen_smjer == "")
									alert("Molimo unesite naziv smjera!")
								else{
									//console.log("OK smjer");
									showNextMessageNr($(this), 4);
									scrollToBottom();
									$(cloningDiv).find("#ostalo_srednje_kraj").prop('disabled', true);
									$(cloningDiv).find("#ostalo_smjer").prop('disabled', true);
								}
                            });

							$(document).on('click', '.unos_naziva_srednje', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								srednje_obr_unesena_skola =  $(this).parent().find('input[name="ostalo_skola"]').val();
								//console.log(srednje_obr_unesena_skola);
								if(srednje_obr_unesena_skola == "")
									alert("Molimo unesite naziv škole!")
								else{
									//console.log("OK naziv");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#unos_naziva_srednje").prop('disabled', true);
									$(cloningDiv).find("#ostalo_skola").prop('disabled', true);
								}
                            });

							$(document).on('click', '.godina_zavrsetka_button', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								godina_do =  $(this).parent().find('select option:selected').val();
								//console.log(godina_do);
								if(godina_do == "")
									alert("Molimo odaberite godinu!")
								else{
									//console.log("OK godina");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#godina_zavrsetka_button").prop('disabled', true);
									$(cloningDiv).find("#godina_do").prop('disabled', true);
								}
                            });

							$(document).on('click', '.srednja_grad_button', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								srednja_grad =  $(this).parent().find('input[name="srednje_obr_grad"]').val();
								//console.log(srednja_grad);
								if(srednja_grad == "")
									alert("Molimo unesite grad!")
								else{
									//console.log("OK grad");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#srednja_grad_button").prop('disabled', true);
									$(cloningDiv).find("#srednje_obr_grad").prop('disabled', true);
								}
                            });

							$(document).on('click', '.kraj_srednje', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								srednja_drzava =  $(this).parent().find('input[name="srednje_obr_drzava"]').val();
								console.log(srednja_drzava);
								if(srednja_drzava == "")
									alert("Molimo unesite drzavu!")
								else{

									var trenutniDiv = $(this).parent().parent().parent();
									var gradDiv = trenutniDiv.prev(".message").prev(".message");
									var srednje_obr_grad = gradDiv.find('input[name="srednje_obr_grad"]').val();
									var srednje_obr_drzava = $(this).parent().find('input[name="srednje_obr_drzava"]').val();

									var godinDoDiv = gradDiv.prev(".message").prev(".message");
									var srednje_obr_godinaDo = godinDoDiv.find('select option:selected').val();

									var skolaDiv = godinDoDiv.prev(".message").prev(".message").prev(".message").prev(".message").prev(".message").prev(".message").prev(".message").prev(".message").prev(".message").prev(".message");
									var srednje_obr_skola = skolaDiv.find('select option:selected').val();

									var smjerDiv = skolaDiv.next(".message").next(".message").next(".message").next(".message").next(".message").next(".message");
									var srednje_obr_smjer = smjerDiv.find('select option:selected').val();

									if(srednje_obr_skola == "ostalo"){
										inputDiv = skolaDiv.next(".message").next(".message");
										srednje_obr_skola_f = inputDiv.find('input[name="ostalo_skola"]').val();
										inputDiv_smjer = inputDiv.next(".message").next(".message");
										srednje_obr_zvanje_id = inputDiv_smjer.find('input[name="ostalo_smjer"]').val();
										srednje_obr_smjer = "ostalo";
										var skola_idd = null;
									}else{
										var skola_idd = $(cloningDiv).find('#skole_picker option:selected').data('skolaa_id');
										//console.log(skola_idd);
										srednje_obr_skola_f = srednje_obr_skola;
										if(srednje_obr_smjer == "ostalo"){
											inputDiv_samo_smjer = smjerDiv.next(".message").next(".message");
											srednje_obr_zvanje_id = inputDiv_samo_smjer.find('input[name="ostalo_samo_smjer"]').val();
										}else{
											srednje_obr_zvanje_id = srednje_obr_smjer;
										}
									}
									// var skolaDiv = zvanjeDiv.prev(".message").prev(".message");
									// var srednje_obr_skola = skolaDiv.find('select option:selected').val();

									console.log(srednje_obr_smjer);
									console.log(srednje_obr_skola_f);
									console.log(srednje_obr_grad);
									console.log(srednje_obr_drzava);
									console.log(srednje_obr_godinaDo);
									//console.log(srednje_obr_godinaOd);
									console.log(srednje_obr_zvanje_id);

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/addSrednjaSkola',
										type: 'POST',
										data: {	"_token": token,
												"skola_idd": skola_idd,
												"srednje_obr_smjer": srednje_obr_smjer,
												"srednje_obr_skola_f": srednje_obr_skola_f,
												"srednje_obr_zvanje_id": srednje_obr_zvanje_id,
												"srednje_obr_godinaDo": srednje_obr_godinaDo,
												"srednje_obr_drzava": srednje_obr_drzava,
												"srednje_obr_grad": srednje_obr_grad
											},
										dataType: 'html',
										success: function(data) {
											document.getElementById("broj_srednjih").value = 1;
										},
										error: function(data){
											// alert("error!!!!");
										}
									});

									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#srednje_obr_drzava").prop('disabled', true);
									$(cloningDiv).find("#kraj_srednje").prop('disabled', true);
								}
							});

							//DA LI JE ODABRAN SMJER ZA SREDNJU ILI OSTALO
							$(document).on('click', '.odabir_smjera', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								var optionSrednjaSmjer =  $(this).parent().find('select option:selected').val();
								console.log(optionSrednjaSmjer);
								if(optionSrednjaSmjer == "")
									alert("Molimo odaberite smjer!")
								else{
									//console.log("OK smjer");
									if(optionSrednjaSmjer == "ostalo"){
										showNextMessage($(this));
										scrollToBottom();
									}else{
										showNextMessageNr($(this), 2);
									}
									scrollToBottom();
									$(cloningDiv).find("#odabir_smjera").prop('disabled', true);
									$(cloningDiv).find("#skole").prop('disabled', true);
								}
                            });

							$(document).on('click', '.ostalo_srednja_smjer_kraj', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								srednje_obr_unesen_smjer =  $(this).parent().find('input[name="ostalo_samo_smjer"]').val();
								console.log(srednje_obr_unesen_smjer);
								if(srednje_obr_unesen_smjer == "")
									alert("Molimo unesite naziv smjera!")
								else{
									//console.log("OK smjer");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#ostalo_srednja_smjer_kraj").prop('disabled', true);
									$(cloningDiv).find("#ostalo_samo_smjer").prop('disabled', true);
								}
                            });

							//DA LI JE IZABRAN FAX ILI JE OSTALO
							$(document).on('click', '.odabir_fakulteta', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								var optionFax =  $(this).parent().find('select option:selected').val();
								if(optionFax == ""){
									alert("Molimo Vas da odaberete visoku školu")
								}else{
									if(optionFax == "ostalo"){
										showNextMessage($(this));
										scrollToBottom();
									}else{
										$('.svi_smjerovi').hide();
										$('.svi_smjerovi_faks').hide();
										var faks_idd = $(cloningDiv).find('#fax_picker option:selected').data('fakss_id');
										$(cloningDiv).find('.opt_'+faks_idd+'').show();

										showNextMessageNr($(this), 4);
									}
									scrollToBottom();
									$(cloningDiv).find("#odabir_fakulteta").prop('disabled', true);
									$(cloningDiv).find("#fax_picker").prop('disabled', true);
								}
                            });

							$(document).on('click', '.unos_naziva_faksa', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								visoko_obr_unesena_skola =  $(this).parent().find('input[name="ostalo_fax"]').val();
								//console.log(visoko_obr_unesena_skola);
								if(visoko_obr_unesena_skola == "")
									alert("Molimo unesite naziv škole!")
								else{
									//console.log("OK naziv");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#unos_naziva_faksa").prop('disabled', true);
									$(cloningDiv).find("#ostalo_fax").prop('disabled', true);
								}
                            });

							$(document).on('click', '.ostalo_fax_kraj', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								visoko_obr_unesen_smjer =  $(this).parent().find('input[name="ostalo_smjer_fax"]').val();
								//console.log(visoko_obr_unesen_smjer);
								if(visoko_obr_unesen_smjer == "")
									alert("Molimo unesite naziv smjera!")
								else{
									//console.log("OK smjer");
									showNextMessageNr($(this), 4);
									scrollToBottom();
									$(cloningDiv).find("#ostalo_fax_kraj").prop('disabled', true);
									$(cloningDiv).find("#ostalo_smjer_fax").prop('disabled', true);
								}
                            });

							$(document).on('click', '.godina_zavrsetka_faxa_button', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								godina_diplome =  $(this).parent().find('select option:selected').val();
								console.log(godina_diplome);
								if(godina_diplome == "")
									alert("Molimo odaberite godinu!")
								else{
									//console.log("OK godina");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#godina_zavrsetka_faxa_button").prop('disabled', true);
									$(cloningDiv).find("#godina_dipl").prop('disabled', true);
								}
                            });

							$(document).on('click', '.fax_grad_button', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								fax_grad =  $(this).parent().find('input[name="grad_fax"]').val();
								//console.log(srednja_grad);
								if(fax_grad == "")
									alert("Molimo unesite grad!")
								else{
									//console.log("OK grad");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#fax_grad_button").prop('disabled', true);
									$(cloningDiv).find("#grad_fax").prop('disabled', true);
								}
                            });

							//DA LI JE ODABRAN SMJER ZA FAX ILI OSTALO
							$(document).on('click', '.odabir_smjera_fax', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								var optionFaxSmjer =  $(this).parent().find('select option:selected').val();
								if(optionFaxSmjer == "")
									alert("Molimo odaberite smjer!")
								else{
									if(optionFaxSmjer == "ostalo"){
										showNextMessage($(this));
										scrollToBottom();
									}else{
										showNextMessageNr($(this), 2);
									}
									scrollToBottom();
									$(cloningDiv).find("#odabir_smjera_fax").prop('disabled', true);
									$(cloningDiv).find("#skole2").prop('disabled', true);
								}
                            });

							$(document).on('click', '.kraj_faxa', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								faks_drzava =  $(this).parent().find('input[name="drzava_fax"]').val();
								console.log(faks_drzava);
								if(faks_drzava == "")
									alert("Molimo unesite drzavu!")
								else{
									var trenutniDiv_faks = $(this).parent().parent().parent();
									var gradDiv_faks = trenutniDiv_faks.prev(".message").prev(".message");
									var visoko_obr_grad = gradDiv_faks.find('input[name="grad_fax"]').val();
									var visoko_obr_drzava = $(this).parent().find('input[name="drzava_fax"]').val();

									var godinDoDiv_faks = gradDiv_faks.prev(".message").prev(".message");
									var visoko_obr_godinaDo = godinDoDiv_faks.find('select option:selected').val();

									var skolaDiv_faks = godinDoDiv_faks.prev(".message").prev(".message").prev(".message").prev(".message").prev(".message").prev(".message").prev(".message").prev(".message").prev(".message").prev(".message");
									var visoko_obr_skola = skolaDiv_faks.find('select option:selected').val();

									var smjerDiv_faks = skolaDiv_faks.next(".message").next(".message").next(".message").next(".message").next(".message").next(".message");
									var visoko_obr_smjer = smjerDiv_faks.find('select option:selected').val();

									if(visoko_obr_skola == "ostalo"){
										inputDiv_faks = skolaDiv_faks.next(".message").next(".message");
										visoko_obr_skola_f = inputDiv_faks.find('input[name="ostalo_fax"]').val();
										inputDiv_faks_smjer = inputDiv_faks.next(".message").next(".message");
										visoko_obr_zvanje_id = inputDiv_faks_smjer.find('input[name="ostalo_smjer_fax"]').val();
										visoko_obr_smjer = "ostalo";
										var fakss_id = null;
									}else{
										var fakss_id = $(cloningDiv).find('#fax_picker option:selected').data('fakss_id');
										visoko_obr_skola_f = visoko_obr_skola;
										if(visoko_obr_smjer == "ostalo"){
											inputDiv_fax_samo_smjer = smjerDiv_faks.next(".message").next(".message");
											visoko_obr_zvanje_id = inputDiv_fax_samo_smjer.find('input[name="ostalo_samo_smjer_fax"]').val();
										}else{
											visoko_obr_zvanje_id = visoko_obr_smjer;
										}
									}
									// var skolaDiv = zvanjeDiv.prev(".message").prev(".message");
									// var srednje_obr_skola = skolaDiv.find('select option:selected').val();

									console.log(visoko_obr_smjer);
									console.log(visoko_obr_skola_f);
									console.log(visoko_obr_grad);
									console.log(visoko_obr_drzava);
									console.log(visoko_obr_godinaDo);
									//console.log(srednje_obr_godinaOd);
									console.log(visoko_obr_zvanje_id);

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/addVisokaSkola',
										type: 'POST',
										data: {	"_token": token,
												"fakss_id": fakss_id,
												"visoko_obr_smjer": visoko_obr_smjer,
												"visoko_obr_skola_f": visoko_obr_skola_f,
												"visoko_obr_zvanje_id": visoko_obr_zvanje_id,
												"visoko_obr_godinaDo": visoko_obr_godinaDo,
												"visoko_obr_drzava": visoko_obr_drzava,
												"visoko_obr_grad": visoko_obr_grad
											},
										dataType: 'html',
										success: function(data) {

										},
										error: function(data){
											// alert("error!!!!");
										}
									});

									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#drzava_fax").prop('disabled', true);
									$(cloningDiv).find("#kraj_faxa").prop('disabled', true);
								}

							});
							$(document).on('click', '.ostalo_fax_smjer_kraj', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();

								visoko_obr_unesen_smjer =  $(this).parent().find('input[name="ostalo_samo_smjer_fax"]').val();
								console.log(visoko_obr_unesen_smjer);
								if(visoko_obr_unesen_smjer == "")
									alert("Molimo unesite naziv smjera!")
								else{
									//console.log("OK smjer");
									showNextMessage($(this));
									scrollToBottom();
									$(cloningDiv).find("#ostalo_fax_smjer_kraj").prop('disabled', true);
									$(cloningDiv).find("#ostalo_samo_smjer_fax").prop('disabled', true);
								}
                            });

							//DOKUMENTI
							$(document).on('click', '.diploma_srednje_ne', function () {
                                showNextMessageNr($(this), 2);
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(document).find("#diploma_srednje_ne").prop('disabled', true);
								$(document).find("#diploma_srednje_da").prop('disabled', true);

								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserAnswers',
									type: 'POST',
									data: {"_token": token, "answer": "ne_diploma"},
									dataType: 'html',
									success: function(data) {

									}
								});
                            });

							$(document).on('click', '.diploma_srednje_da', function () {
                                showNextMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(document).find("#diploma_srednje_ne").prop('disabled', true);
								$(document).find("#diploma_srednje_da").prop('disabled', true);
                            });

							$(document).on('click', '.pripravnicki_ne', function () {
                                showNextMessageNr($(this), 2);
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(document).find("#pripravnicki_da").prop('disabled', true);
								$(document).find("#pripravnicki_ne").prop('disabled', true);

								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserAnswers',
									type: 'POST',
									data: {"_token": token, "answer": "ne_pripravnicki"},
									dataType: 'html',
									success: function(data) {

									}
								});
                            });

							$(document).on('click', '.pripravnicki_da', function () {
                                showNextMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(document).find("#pripravnicki_da").prop('disabled', true);
								$(document).find("#pripravnicki_ne").prop('disabled', true);
                            });

							$(document).on('click', '.strucni_ne', function () {
                                showNextMessageNr($(this), 2);
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(document).find("#strucni_da").prop('disabled', true);
								$(document).find("#strucni_ne").prop('disabled', true);

								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserAnswers',
									type: 'POST',
									data: {"_token": token, "answer": "ne_strucni"},
									dataType: 'html',
									success: function(data) {

									}
								});
                            });

							$(document).on('click', '.strucni_da', function () {
                                showNextMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(document).find("#strucni_da").prop('disabled', true);
								$(document).find("#strucni_ne").prop('disabled', true);
                            });

							$(document).on('click', '.cert_jezik_ne', function () {
                                showNextMessageNr($(this), 2);
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(document).find("#cert_jezik_ne").prop('disabled', true);
								$(document).find("#cert_jezik_da").prop('disabled', true);

								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserAnswers',
									type: 'POST',
									data: {"_token": token, "answer": "ne_cert_jezik"},
									dataType: 'html',
									success: function(data) {

									}
								});
                            });
							$(document).on('click', '.cert_jezik_da', function () {
                                showNextMessage($(this));
                                //console.log($(this).parent().parent().parent().next(".message"));
                                scrollToBottom();
								$(document).find("#cert_jezik_ne").prop('disabled', true);
								$(document).find("#cert_jezik_da").prop('disabled', true);
                            });

							//ADRESA
							$(document).on('click', '.unos_adrese', function () {
								adresa =  $(this).parent().find('input[name="adresa"]').val();
								if(adresa == "")
									alert("Molimo Vas da unesete adresu!");
								else{
									console.log(adresa);
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateAdresa',
										type: 'POST',
										data: {"_token": token,'podatak': adresa, 'kolona': 'kandidat_adresa'},
										dataType: 'html',
										success: function(data) {

										}
									});
									showNextMessage($(this));
									scrollToBottom();
									$(document).find("#unos_adrese").prop('disabled', true);
									$(document).find("#adresa").prop('disabled', true);
								}
							});

							$(document).on('click', '.unos_pbroj', function () {
								adresa_pbroj =  $(this).parent().find('input[name="adresa_pbroj"]').val();
								if(adresa_pbroj == "")
									alert("Molimo Vas da unesete poštanski broj!");
								else{
									console.log(adresa_pbroj);
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateAdresa',
										type: 'POST',
										data: {"_token": token,'podatak': adresa_pbroj, 'kolona': 'kandidat_pbroj'},
										dataType: 'html',
										success: function(data) {

										}
									});
									showNextMessage($(this));
									scrollToBottom();
									$(document).find("#unos_pbroj").prop('disabled', true);
									$(document).find("#adresa_pbroj").prop('disabled', true);
								}
							});

							$(document).on('click', '.unos_grada', function () {
								adresa_grad =  $(this).parent().find('input[name="adresa_grad"]').val();
								if(adresa_grad == "")
									alert("Molimo Vas da unesete grad!");
								else{
									console.log(adresa_grad);
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateAdresa',
										type: 'POST',
										data: {"_token": token,'podatak': adresa_grad, 'kolona': 'kandidat_grad'},
										dataType: 'html',
										success: function(data) {

										}
									});
									showNextMessage($(this));
									scrollToBottom();
									$(document).find("#unos_grada").prop('disabled', true);
									$(document).find("#adresa_grad").prop('disabled', true);
								}
							});

							$(document).on('click', '.unos_drzave', function () {
								adresa_drzava =  $(this).parent().find('input[name="adresa_drzava"]').val();
								if(adresa_drzava == "")
									alert("Molimo Vas da unesete državu!");
								else{
									console.log(adresa_drzava);
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateAdresa',
										type: 'POST',
										data: {"_token": token,'podatak': adresa_drzava, 'kolona': 'kandidat_drzava'},
										dataType: 'html',
										success: function(data) {

										}
									});
									showNextMessage($(this));
									scrollToBottom();
									$(document).find("#unos_drzave").prop('disabled', true);
									$(document).find("#adresa_drzava").prop('disabled', true);
								}
							});

							//VALIDACIJA
							$(document).on('click', '.add_datum_termina_val', function () {

								var datum_termina = $(this).parent().find('input[name="datum_termina_val"]').val();
								if(datum_termina == "")
									alert("Molimo Vas da odaberete validan datum!");
								else{
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/addDatumTermina',
										type: 'POST',
										data: {"_token": token,'datum_termina': datum_termina},
										dataType: 'html',
										success: function(data) {

										}
									});
									showNextMessage($(this));
									scrollToBottom();
									document.getElementById("add_datum_termina_val").disabled = true;
								}
							});
							$(document).on('click', '.add_datum_vize_val', function () {

								var datum_vize = $(this).parent().find('input[name="datum_vize_val"]').val();
								if(datum_vize == "")
									alert("Molimo Vas da odaberete validan datum!");
								else{
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/addViza',
										type: 'POST',
										data: {"_token": token, 'datum_vize': datum_vize},
										dataType: 'html',
										success: function(data) {

										}
									});
									showNextMessage($(this));
									scrollToBottom();
									document.getElementById("add_datum_vize_val").disabled = true;
								}

                            });
							$(document).on('click', '.add_datum_apl_val', function () {

								var datum_apliciranja_mjesec = $(this).parent().find('select[name="datum_mjesec_val"]').val();
								var datum_apliciranja_godina = $(this).parent().find('select[name="datum_god_val"]').val();
								if(datum_apliciranja_mjesec == null  || datum_apliciranja_godina == null){
									alert("Odaberite mjesec i godinu apliciranja termina")
								}else{
									if(datum_apliciranja_mjesec < 10){
										var datum_apliciranja_mjesec_f = "0" + datum_apliciranja_mjesec;
									}else{
										var datum_apliciranja_mjesec_f = datum_apliciranja_mjesec;
									}

									datum_apliciranja = "01." + datum_apliciranja_mjesec_f + "." + datum_apliciranja_godina;

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/addDatumApl',
										type: 'POST',
										data: {"_token": token,'datum_apliciranja': datum_apliciranja},
										dataType: 'html',
										success: function(data) {

										}
									});
									showNextMessage($(this));
									scrollToBottom();
									document.getElementById("add_datum_apl_val").disabled = true;
								}
							});
							$(document).on('click', '.edit_skola_naziv_val', function () {

								var id_skole = $(this).parent().find('input[name="kontrola_skola_id"]').val();
								var promijenjen_podatak = $(this).parent().find('input[name="kontrola_skola"]').val();
								if(promijenjen_podatak == undefined)
									promijenjen_podatak = $(this).parent().find('select option:selected').val();
								var kontrola_id_val = $(this).parent().find('input[name="kontrola_id_val"]').val();
								var kontrola_naziv_podatka = $(this).parent().find('input[name="kontrola_naziv_podatka"]').val();
								console.log(id_skole);
								console.log(promijenjen_podatak);
								if(promijenjen_podatak == "")
									alert("Molimo Vas da ispunite polje!");
								else{
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editSkole',
										type: 'POST',
										data: {"_token": token,'id_skole': id_skole, 'promijenjen_podatak': promijenjen_podatak, 'naziv_podatka': kontrola_naziv_podatka, 'kontrola_id_val':kontrola_id_val},
										dataType: 'html',
										success: function(data) {

										}
									});
									$(this).prop('disabled', true);
									showNextMessage($(this));
									scrollToBottom();
								}
							});

							$(document).on('click', '.edit_iskustvo_val', function () {

								var id_iskustva = $(this).parent().find('input[name="kontrola_iskustvo_id"]').val();
								var promijenjen_podatak = $(this).parent().find('input[name="kontrola_iskustvo"]').val();
								if(promijenjen_podatak == undefined){
									mjesec = $(this).parent().find('select[name="kontrola_iskustvo_mjesec"]').val();
									godina = $(this).parent().find('select[name="kontrola_iskustvo_godina"]').val();
									if(mjesec < 10){
										var mjesec_f = "0" + mjesec;
									}else{
										var mjesec_f = mjesec;
									}
									promijenjen_podatak = "01." + mjesec_f + "." + godina;
								}
								var kontrola_id_val = $(this).parent().find('input[name="kontrola_isk_id_val"]').val();
								var kontrola_naziv_podatka = $(this).parent().find('input[name="kontrola_isk_naziv_podatka"]').val();
								console.log(id_iskustva);
								console.log(promijenjen_podatak);
								console.log(kontrola_id_val);
								console.log(kontrola_naziv_podatka);

								if(promijenjen_podatak == "")
									alert("Molimo Vas da ispunite polje!");
								else{
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editIskustva',
										type: 'POST',
										data: {"_token": token,'id_iskustva': id_iskustva, 'promijenjen_podatak': promijenjen_podatak, 'naziv_podatka': kontrola_naziv_podatka, 'kontrola_id_val':kontrola_id_val},
										dataType: 'html',
										success: function(data) {

										}
									});
									$(this).prop('disabled', true);
									showNextMessage($(this));
									scrollToBottom();
								}
							});

							$(document).on('click', '.edit_info_val', function () {

								var kolona = $(this).parent().find('input[name="kontrola_kolona"]').val();
								if(kolona == "kandidat_datumrodjenja"){
									var dan = $(this).parent().find('select[name="val_datum_dan"]').val();
									var mjesec = $(this).parent().find('select[name="val_datum_mjesec"]').val();
									var godina = $(this).parent().find('select[name="val_datum_godina"]').val();
									console.log(dan);
									console.log(mjesec);
									console.log(godina);
									if(dan == null || mjesec == null || godina == null){
										podatak = "";
									}else{
										if(dan < 10){
											var dan = "0" + dan;
										}
										if(mjesec < 10){
											var mjesec = "0" + mjesec;
										}
										podatak = dan + "." + mjesec + "." + godina;
									}
								}else{
									var podatak = $(this).parent().find('input[name="kontrola_podatak"]').val();
								}
								var vi_id = $(this).parent().find('input[name="kontrola_vi_id"]').val();
								console.log(podatak);
								console.log(kolona);
								console.log(vi_id);

								if(podatak == "")
									alert("Molimo Vas da ispunite polje!");
								else{
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editInformacije',
										type: 'POST',
										data: {"_token": token,'podatak': podatak, 'kolona': kolona, 'vi_id': vi_id},
										dataType: 'html',
										success: function(data) {

										}
									});
									$(this).prop('disabled', true);
									$(this).parent().find('input[name="kontrola_podatak"]').prop('disabled', true);
									if(kolona == "kandidat_datumrodjenja"){
										$(this).parent().find('select[name="val_datum_dan"]').prop('disabled', true);
										$(this).parent().find('select[name="val_datum_mjesec"]').prop('disabled', true);
										$(this).parent().find('select[name="val_datum_godina"]').prop('disabled', true);
									}
									showNextMessage($(this));
									scrollToBottom();
								}
							});

							$(document).on('click', '.edit_dtR_unos', function () {

								var dan = $(this).parent().find('select[name="unos_datum_dan"]').val();
								var mjesec = $(this).parent().find('select[name="unos_datum_mjesec"]').val();
								var godina = $(this).parent().find('select[name="unos_datum_godina"]').val();
								console.log(dan);
								console.log(mjesec);
								console.log(godina);
								if(dan == null || mjesec == null || godina == null){
									podatak = "";
								}else{
									if(dan < 10){
										var dan = "0" + dan;
									}
									if(mjesec < 10){
										var mjesec = "0" + mjesec;
									}
									podatak = dan + "." + mjesec + "." + godina;
								}
								console.log(podatak);

								if(podatak == "")
									alert("Molimo Vas da ispunite polje!");
								else{
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/insertDatumRod',
										type: 'POST',
										data: {"_token": token,'podatak': podatak},
										dataType: 'html',
										success: function(data) {

										}
									});
									$(this).prop('disabled', true);

									$(this).parent().find('select[name="unos_datum_dan"]').prop('disabled', true);
									$(this).parent().find('select[name="unos_datum_mjesec"]').prop('disabled', true);
									$(this).parent().find('select[name="unos_datum_godina"]').prop('disabled', true);

									showNextMessage($(this));
									scrollToBottom();
								}
							});

							$(document).on('click', '.dipl_prvo_da', function () {
                                var showNext = $(this).parent().parent().parent().next(".message");
								showNext.show();
								showNext.next(".message").next(".message").delay(500).show(0);
								scrollToBottom();
								$(document).find("#dipl_prvo_da").prop('disabled', true);
								$(document).find("#dipl_prvo_ne").prop('disabled', true);
                            });

							$(document).on('click', '.dipl_prvo_ne', function () {
                                var showNext = $(this).parent().parent().parent().next(".message").next(".message");
								showNext.show();
								showNext.next(".message").delay(500).show(0);
								scrollToBottom();
								$(document).find("#dipl_prvo_da").prop('disabled', true);
								$(document).find("#dipl_prvo_ne").prop('disabled', true);
                            });

							$(document).on('click', '.dipl_ne', function () {

								var showNext = $(this).parent().parent().parent().next(".message").next(".message");
								var dipl_kandidat_id = $(this).parent().find('input[name="dipl_kandidat_id"]').val();
								showNext.show();
								//showNext.next(".message").delay(500).show(0);
								scrollToBottom();
								$(document).find("#dipl_da").prop('disabled', true);
								$(document).find("#dipl_ne").prop('disabled', true);

								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserDipl',
									type: 'POST',
									data: {"_token": token, "answer": 1},
									dataType: 'html',
									success: function(data) {

									}
								});
								$.ajax({
									type: "POST",
									enctype: 'multipart/form-data',
									url: 'https://crm.job-step.com/public_kandidati?page=slanje_sbota',
									data: {"kandidat_id": dipl_kandidat_id, "povijest": 1, "sms_bot_crm": 2},
									//dataType: 'html',
									success: function(data) {
										 //alert (data);
									},

									error : function(data)
									{
										//alert(data);
									}
								});
                            });
							$(document).on('click', '.dipl_da', function () {
                                var showNext = $(this).parent().parent().parent().next(".message");
								var dipl_kandidat_id = $(this).parent().find('input[name="dipl_kandidat_id"]').val();
								showNext.show();
								scrollToBottom();
								$(document).find("#dipl_da").prop('disabled', true);
								$(document).find("#dipl_ne").prop('disabled', true);

								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editUserDipl',
									type: 'POST',
									data: {"_token": token, "answer": 2},
									dataType: 'html',
									success: function(data) {

									}
								});
								console.log(dipl_kandidat_id);

								$.ajax({
									type: "POST",
									enctype: 'multipart/form-data',
									url: 'https://crm.job-step.com/public_kandidati?page=mail_dipl_da',
									data: {"kandidat_id": dipl_kandidat_id},
									//dataType: 'html',
									success: function(data) {
										 //alert (data);
									}
								});
                            });

							$(document).on('click', '.nOglasDa', function () {
                                var showNext = $(this).parent().parent().parent().next(".message");
								//var dipl_kandidat_id = $(this).parent().find('input[name="dipl_kandidat_id"]').val();
								showNext.show();
								scrollToBottom();
								$(document).find("#nOglasNe").prop('disabled', true);
								$(document).find("#nOglasDa").prop('disabled', true);
								var nalogOglas_id = $(document).find('input[name="nalogOglas_id"]').val();
								//mijenjanje i naloga, tj update
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editBotOglasi',
									type: 'POST',
									data: {"_token": token, "answer": 2, "nalog_id": nalogOglas_id},
									dataType: 'html',
									success: function(data) {

									}
								});

								if(showNext.hasClass("kraj_novog_oglasa_poz")){

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editBotOglasi',
										type: 'POST',
										data: {"_token": token, "answer": 4, "nalog_id": nalogOglas_id},
										dataType: 'html',
										success: function(data) {

										}
									});
								}else{
									showNext.next(".message").delay(500).show(0);
								}

                            });
							$(document).on('click', '.nOglasNe', function () {
                                var showNext = $(document).find('.kraj_novog_oglasa_neg');
								//var dipl_kandidat_id = $(this).parent().find('input[name="dipl_kandidat_id"]').val();
								showNext.show();
								scrollToBottom();
								$(document).find("#nOglasNe").prop('disabled', true);
								$(document).find("#nOglasDa").prop('disabled', true);
								var nalogOglas_id = $(document).find('input[name="nalogOglas_id"]').val();
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/editBotOglasi',
									type: 'POST',
									data: {"_token": token, "answer": 3, "nalog_id": nalogOglas_id},
									dataType: 'html',
									success: function(data) {

									}
								});

                            });
							$(document).on('click', '.upgJezikDa', function () {
                                var showNext = $(this).parent().parent().parent().next(".message");
								showNext.show();
								showNext.next(".message").delay(500).show(0);
								scrollToBottom();
								$(document).find("#upgJezikDa").prop('disabled', true);
								$(document).find("#upgJezikNe").prop('disabled', true);


                            });
							$(document).on('click', '.upgJezikNe', function () {
                                var showNext = $(this).parent().parent().parent().next(".message").next(".message").next(".message");
								showNext.show();
								scrollToBottom();
								$(document).find("#upgJezikDa").prop('disabled', true);
								$(document).find("#upgJezikNe").prop('disabled', true);
								if(showNext.hasClass("kraj_novog_oglasa_poz")){

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editBotOglasi',
										type: 'POST',
										data: {"_token": token, "answer": 4, "nalog_id": nalogOglas_id},
										dataType: 'html',
										success: function(data) {

										}
									});
								}else{
									showNext.next(".message").delay(500).show(0);
								}

                            });
							$(document).on('click', '.njem_jezik_edit_novi', function () {

								var showNext = $(this).parent().parent().parent().next(".message");
								showNext.show();
								var novi_jezik = $(this).parent().find('input[name="option_novi_jezik"]:checked').val();
								console.log(novi_jezik);
								if(novi_jezik == undefined)
									alert("Molimo Vas da odaberete nivo poznavanja Njemačkog jezika");
								else{
									//console.log("uredu je");
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/updateNjemJezikNovi',
										type: 'POST',
										data: {"_token": token,'jezik': novi_jezik},
										dataType: 'html',
										success: function(data) {

										}
									});

									document.getElementById("njem_jezik_edit_novi").disabled = true;
									document.getElementById("optionNovi1").disabled = true;
									document.getElementById("optionNovi2").disabled = true;
									document.getElementById("optionNovi3").disabled = true;
									document.getElementById("optionNovi4").disabled = true;
									document.getElementById("optionNovi5").disabled = true;
									document.getElementById("optionNovi6").disabled = true;
									document.getElementById("optionNovi7").disabled = true;

									if(showNext.hasClass("kraj_novog_oglasa_poz")){

										var token = $('meta[name="csrf-token"]').attr('content');
										$.ajax({
											url: '/messenger/editBotOglasi',
											type: 'POST',
											data: {"_token": token, "answer": 4, "nalog_id": nalogOglas_id},
											dataType: 'html',
											success: function(data) {

											}
										});
									}else{
										showNext.next(".message").delay(500).show(0);
									}
									scrollToBottom();
								}
							});
							$(document).on('click', '.upgVozackaDa', function () {
                                var showNext = $(this).parent().parent().parent().next(".message");
								showNext.show();
								scrollToBottom();
								$(document).find("#upgVozackaDa").prop('disabled', true);
								$(document).find("#upgVozackaNe").prop('disabled', true);
								var odg = "Da";
								var token = $('meta[name="csrf-token"]').attr('content');
								$.ajax({
									url: '/messenger/updateVozacka',
									type: 'POST',
									data: {"_token": token, "odg": odg},
									dataType: 'html',
									success: function(data) {

									}
								});
								if(showNext.hasClass("kraj_novog_oglasa_poz")){

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editBotOglasi',
										type: 'POST',
										data: {"_token": token, "answer": 4, "nalog_id": nalogOglas_id},
										dataType: 'html',
										success: function(data) {

										}
									});
								}else{
									showNext.next(".message").delay(500).show(0);
								}
								scrollToBottom();

                            });
							$(document).on('click', '.upgVozackaNe', function () {
                                var showNext = $(this).parent().parent().parent().next(".message");
								showNext.show();
								scrollToBottom();
								$(document).find("#upgVozackaDa").prop('disabled', true);
								$(document).find("#upgVozackaNe").prop('disabled', true);

								if(showNext.hasClass("kraj_novog_oglasa_poz")){

									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/editBotOglasi',
										type: 'POST',
										data: {"_token": token, "answer": 4, "nalog_id": nalogOglas_id},
										dataType: 'html',
										success: function(data) {

										}
									});
								}else{
									showNext.next(".message").delay(500).show(0);
								}
								scrollToBottom();

                            });

							$(document).on('change', '#appointment_picker', function(){

								$.each($('#appointment_hours_picker').find('option'), function(){
									// console.log(this.value);
									if(this.value !== ""){
										this.remove();
									}else{
										// $(this).attr('selected','selected');
									}
								});

								var selected_appointment = $('#appointment_picker').find(":selected").val();
								var token = $('meta[name="csrf-token"]').attr('content');
								// console.log(selected_appointment);
								$.ajax({
									url: '/messenger/getHoursForAppointment',
									type: 'POST',
									data: {"_token": token, "selected_appointment": selected_appointment},
									dataType: 'html',
									success: function(returned_appointment_hours) {

										// console.log(returned_appointment_hours);
										$.each(JSON.parse(returned_appointment_hours), function(index, value){
											// console.log(value.pah_id);
											// console.log(value.pah_time);
											// console.log(value.pap_id);

											$('#appointment_hours_picker').append('<option value="' + value.pah_id + '">' + value.formatted_pah_time + '</option>');
										})
										$('#appointment_hours_picker').show();
										// $('#button_select_appointment').show();
									}
								});

							});

							$(document).on('change', '#appointment_hours_picker', function(){
								var selected_hours = $(this).parent().find('select[name="appointment_hours_picker"]').val();
								// console.log(selected_hours);
								if(selected_hours != null){
									$('#button_select_appointment').show();
								}
							});

							$(document).on('click', '.select_appointment', function () {
								//GETANJE APPOINTMENTA I SATNICE I INSERT U BAZU
								//POZIVANJE SLIJEDEĆE PORUKE
								var appointment_id = $(this).parent().find('select[name="appointment_picker"]').val();
								var appointment_hours_id = $(this).parent().find('select[name="appointment_hours_picker"]').val();

								if(appointment_hours_id == null)
									alert("Please select hours!");
								else{
									// document.getElementById("appointment_picker").disabled = true;
									// document.getElementById("appointment_hours_picker").disabled = true;
									$(this).parent().find('select[name="appointment_picker"]').prop('disabled', true);
									$(this).parent().find('select[name="appointment_hours_picker"]').prop('disabled', true);

									// var token = $('meta[name="csrf-token"]').attr('content');
									// $.ajax({
									// 	url: '/messenger/addViza',
									// 	type: 'POST',
									// 	data: {"_token": token, 'datum_vize': datum_vize},
									// 	dataType: 'html',
									// 	success: function(data) {

									// 	}
									// });

									// showNextMessageNr($(this), 2);
									$(this).prop('disabled', true);
								}

							});
                        });
                    </script>
                    <input type="hidden" name="kand_id_ajax" value="{{ $kandidat->kandidat_id }}" />
					<div class="message_field" id="message_field_id">
                        <div class="message zoomIn pozdrav">
                            <div class="message_bot">
                                <div class="content_message">
                                    {{ __('hometext.pozdrav') }} {{ $kandidat->kandidat_ime}} {{$kandidat->kandidat_prezime}} <?php /*echo $_COOKIE["mobile_id"]; */ ?> <br/>
                                </div>
                            </div>
                        </div>
                        <div class="message zoomIn">
                            <div class="message_bot">
                                <div class="content_message">
                                    {{ __('hometext.zahvala1') }}
                                </div>
                            </div>
                        </div>

						<div class="message zoomIn">
                            <div class="message_bot">
                                <div class="content_message">
									{{ __('hometext.ostaloJos') }}
                                </div>
                            </div>
                        </div>

					@if($kandidat->kandidat_id == 140327 OR $kandidat->kandidat_id == 163228)

						{{-- BLOK ZA RADNO ISKUSTVO --}}
						@if( Auth::user()->ne_iskustvo != 1 )

							<div class="message zoomIn">
								<div class="message_bot">
									<div class="content_message">
										@if(count($kandidatIskustva) == 0)
											{{ __('hometext.iskustvoQ') }}
										@else
											{{ __('hometext.iskustvoJos') }}
										@endif
									</div>
								</div>
							</div>
							<div class="message zoomIn">
                                <div class="message_user">
                                    <div class="content_message">
                                        <button class="tipka_da first_msg_iskustvo_da" id="first_msg_iskustvo_da" >{{ __('hometext.da') }}</button>
                                        <button class="tipka_ne first_msg_iskustvo_ne" id="first_msg_iskustvo_ne" >{{ __('hometext.ne') }}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="first_msg_cloning_iskustvo">

                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                        {{ __('hometext.unesiPoziciju') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn">
                                    <div class="message_user">
                                        <div class="content_message">
                                            <input class = "input_izgled" type="text" name="first_msg_pozicija_iskustvo" id="first_msg_pozicija_iskustvo" placeholder = "{{ __('hometext.Pozicija') }}" >
											<br>
											<button class="tipka_potvrdi first_msg_button_isk_pozicija" id="first_msg_button_isk_pozicija" >OK</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                            {{ __('hometext.nazivPoslodavca') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn">
                                    <div class="message_user">
                                        <div class="content_message">
                                            <input class = "input_izgled" type="text" name="first_msg_poslodavac_iskustvo" id="first_msg_poslodavac_iskustvo" placeholder = "{{ __('hometext.Poslodavac') }}" >
											<br>
											<button class="tipka_potvrdi first_msg_button_isk_poslodavac" id="first_msg_button_isk_poslodavac" >OK</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                            {{ __('hometext.pocetakRada') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn" style="display: none;">
									<div class="message_user">
										<div class="content_message">

											<select class="custom-select select_izgled" name="first_msg_datum_mjesec_isk" id="first_msg_datum_mjesec_isk" data-live-search="true" >
													<option value="" selected disabled>{{ __('hometext.mjesec') }}</option>

													@for ($month=1; $month <= 12; $month++)
														<option value="{{ $month }}">{{ $month }}</option>
													@endfor
											</select>
											<select class="custom-select select_izgled" name="first_msg_datum_god_isk" id="first_msg_datum_god_isk" data-live-search="true" >
													<option value="" selected disabled>{{ __('hometext.godina') }}</option>

													@for ($year=date("Y"); $year >= $godinaRodjenja+15; $year--)
														<option value="{{ $year }}">{{ $year }}</option>
													@endfor
											</select>
											<br>
											<button class="tipka_potvrdi first_msg_button_isk_datum_od" id="first_msg_button_isk_datum_od" >OK</button>
										</div>
									</div>
								</div>
                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                            {{ __('hometext.krajRada') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn" style="display: none;">
									<div class="message_user">
										<div class="content_message">

											<select class="custom-select select_izgled" name="first_msg_datum_mjesec_isk_do" id="first_msg_datum_mjesec_isk_do" data-live-search="true" >
													<option value="" selected disabled>{{ __('hometext.mjesec') }}</option>

													@for ($month=1; $month <= 12; $month++)
														<option value="{{ $month }}">{{ $month }}</option>
													@endfor
											</select>
											<select class="custom-select select_izgled" name="first_msg_datum_god_isk_do" id="first_msg_datum_god_isk_do" data-live-search="true" >
													<option value="" selected disabled>{{ __('hometext.godina') }}</option>

													@for ($year=date("Y"); $year >= 1970; $year--)
														<option value="{{ $year }}">{{ $year }}</option>
													@endfor
											</select>
											<br>
											<div class="main-container__column material-checkbox-group material-checkbox-group_primary">
												<input type="checkbox" id="first_msg_kri_datum_do_aktuelno" name="first_msg_kri_datum_do_aktuelno" class="material-checkbox">
												<label class="material-checkbox-group__label" for="first_msg_kri_datum_do_aktuelno">{{ __('hometext.Aktuelno') }}</label>
											</div>
											<button class="tipka_potvrdi first_msg_button_isk_datum_do" id="first_msg_button_isk_datum_do" >OK</button>

										</div>
									</div>
								</div>
                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                            {{ __('hometext.uKojemGradu') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn">
                                    <div class="message_user">
                                        <div class="content_message">
                                            <input class = "input_izgled" type="text" name="first_msg_grad_iskustvo" id="first_msg_grad_iskustvo" placeholder = "{{ __('hometext.Grad') }}">
											<br>
											<button class="tipka_potvrdi first_msg_button_isk_grad" id="first_msg_button_isk_grad" >OK</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                            {{ __('hometext.iskustvoJos') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn first_msg_ending_loop_question_iskustvo">
                                    <div class="message_user">
                                        <div class="content_message">
                                            <button class="tipka_da first_msg_iskustvo_da_kraj" id="first_msg_iskustvo_da_kraj" >{{ __('hometext.da') }}</button>
                                            <button class="tipka_ne first_msg_iskustvo_ne_kraj" id="first_msg_iskustvo_ne_kraj" >{{ __('hometext.ne') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<script>

                            </script>
                        @endif
					@endif

					@if($kandidat->kandidat_id == 140327 OR $kandidat->kandidat_id == 163228)
						{{-- OVDJE ĆE DA IDU USLOVI ZA NALOG I TERMINISANJE - START --}}
						{{-- PRVI USLOV: NALOG IMA OMOGUĆENO SELF TERMINIRANJE FALI!!!!!!! --}}
						{{-- DRUGI USLOV: NALOG IMA TERMINE U BUDUĆNOSTI --}}

						<div class="message zoomIn" style="display: none;">
							<div class="message_bot">
								<div class="content_message">
									{{ __('hometext.rezerviTermin') }}
								</div>
							</div>
						</div>
						<div class="message zoomIn">
							<div class="message_user">
								<div class="content_message">

									<select class="custom-select select_izgled" name="appointment_picker" id="appointment_picker" style="margin-bottom: 10px;">
										<option value="" selected disabled>{{ __('hometext.Odaberi') }}</option>
										@foreach($appointments as $appointment)
											<option value="{{ $appointment->pap_id }}">{{ $appointment->pap_city }} - {{ date("d.m.Y", strtotime($appointment->pap_date)) }}</option>
										@endforeach
									</select>

									<select class="custom-select select_izgled" name="appointment_hours_picker" id="appointment_hours_picker" style="margin-bottom: 10px; display:none;">
										<option value="" selected disabled>{{ __('hometext.Odaberi') }}</option>
									</select>


									<br/>
									<button class="select_appointment tipka_potvrdi"  id="button_select_appointment" style="margin-bottom: 10px; display:none;"> OK</button>
								</div>
							</div>
						</div>
					@endif
					{{-- OVDJE ĆE DA IDU USLOVI ZA NALOG I TERMINISANJE - END --}}

					{{-- BLOK ZA KANDIDATE KOJI NISU OBRADJENI ILI NISU NA STATUSU NEZAVRSENI --}}
                    @if( $kandidat->kandidat_status == 0 OR $kandidat->kandidat_status == 1 OR $kandidat->kandidat_status == 4 OR $kandidat->kandidat_status == 6 OR $kandidat->kandidat_status == 5)

						{{-- BLOK ZA VIZU --}}
                        @if( $kandidat->kandidat_drzavljanstvo_vrsta == "NON-EU državljanin" OR $kandidat->kandidat_drzavljanstvo_vrsta == "NON-EU drzavljanin")
							@if( $kandidat->kandidat_viza == null || $kandidat->kandidat_viza == 0)
								@if( $kandidat->datum_termina == null )
									@if( Auth::user()->ne_viza != 1 )
										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.vizaQ') }}
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
														<button class="tipka_da shownext viza_prvo_da" id="viza_prvo_da">{{ __('hometext.da') }}</button>
														<button class="tipka_ne viza_prvo_ne" id="viza_prvo_ne">{{ __('hometext.ne') }}</button>
												</div>
											</div>
										</div>

										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.vizaDokad') }}
												</div>
											</div>
										</div>

										<div class="message zoomIn unos_datuma_vize" style="display: none;">
											<div class="message_user">
												<div class="content_message">
													<input class="datumi termin_za_vizu_izgled" type="text" name="datum_vize" id="datum_vize" placeholder = "{{ __('hometext.odaberiDat') }}" >
													<br>
													<button class="tipka_potvrdi shownext add_datum_vize" id="add_datum_vize">OK</button>
												</div>
											</div>
										</div>

										<script>
										$(document).ready(function() {
												jQuery.noConflict();
												$( '.datumi' ).flatpickr({
													dateFormat: "d.m.Y",
													minDate: "today",
													disableMobile: "true"
												});
											});


										</script>

										<!-- Izbačena pitanja o apliciranju za vizu i terminima - START 19.04.2023 -->

											<!-- <div class="message zoomIn" style="display: none;">
												<div class="message_bot">
													<div class="content_message">
														{{ __('hometext.terminQ') }}
													</div>
												</div>
											</div>

											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
															<button class="tipka_da termin_da" id="termin_da">{{ __('hometext.da') }}</button>
															<button class="tipka_ne termin_ne" id="termin_ne">{{ __('hometext.ne') }}</button>
													</div>
												</div>
											</div> -->

											<!-- <div class="message zoomIn odg_termin_da" style="display: none;">
												<div class="message_bot">
													<div class="content_message">
														{{ __('hometext.terminKad') }}
													</div>
												</div>
											</div>

											<div class="message zoomIn" style="display: none;">
												<div class="message_user">
													<div class="content_message">
														<input class="datumi termin_za_vizu_izgled" type="text" name="datum_termina" id="datum_termina" placeholder = "{{ __('hometext.odaberiDat') }}" >
														<br>
														<button class="tipka_potvrdi add_datum_termina" id="add_datum_termina">OK</button>
													</div>
												</div>
											</div> -->

											<!-- <div class="message zoomIn">
												<div class="message_bot">
													<div class="content_message">
														{{ __('hometext.terminApl') }}
													</div>
												</div>
											</div>

											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
															<button class="tipka_da viza_da" id="viza_da">{{ __('hometext.da') }}</button>
															<button class="tipka_ne viza_ne" id="viza_ne">{{ __('hometext.ne') }}</button>
													</div>
												</div>
											</div> -->

											<!-- <div class="message zoomIn odg_viza_da" style="display: none;">
												<div class="message_bot">
													<div class="content_message">
														{{ __('hometext.terminAplKad') }}
													</div>
												</div>
											</div> -->

											<!-- <div class="message zoomIn odg_viza_ne" style="display: none;">
												<div class="message_bot">
													<div class="content_message">
														{{ __('hometext.upute1') }}
														{{ __('hometext.upute2') }}
														{{ __('hometext.upute3') }}
														<br/>{{ __('hometext.linkoviApl') }}
														<ul style="list-style: circle;">
															<li><a href="https://service2.diplo.de/rktermin/extern/choose_realmList.do?request_locale=de&locationCode=sarj" target="_blank">{{ __('hometext.sarajevo') }}</a></li>
															<li><a href="https://belgrad.diplo.de/rs-sr/service/visa-einreise/-/2077462#content_6" target="_blank">{{ __('hometext.beograd') }}</a></li>
															<li><a href="https://service2.diplo.de/rktermin/extern/choose_categoryList.do?locationCode=laib&realmId=266" target="_blank">{{ __('hometext.ljubljana') }}</a></li>
														</ul>
													</div>
												</div>
											</div> -->

											<!-- <div class="message zoomIn unos_datuma" style="display: none;">
												<div class="message_user">
													<div class="content_message">

														<select class="custom-select select_izgled" name="datum_mjesec" id="datum_mjesec" data-live-search="true" >
																<option value="" selected disabled>{{ __('hometext.mjesec') }}</option>

																@for ($month=1; $month <= 12; $month++)
																	<option value="{{ $month }}">{{ $month }}</option>
																@endfor
														</select>
														<select class="custom-select select_izgled" name="datum_god" id="datum_god" data-live-search="true" >
																<option value="" selected disabled>{{ __('hometext.godina') }}</option>

																@for ($year=2023; $year >= 2015; $year--)
																	<option value="{{ $year }}">{{ $year }}</option>
																@endfor
														</select>
														<br>
														<button class="tipka_potvrdi add_datum_apl" id="add_datum_apl">OK</button>
													</div>
												</div>
											</div> -->
										<!-- Izbačena pitanja o apliciranju za vizu i terminima - END -->
									@endif
								@endif
							@endif
                        @endif

                        {{-- BLOK ZA JEZIK --}}
                        @if( $kandidatJezik == null )
                            <div class="message zoomIn input_jezici">
                                <div class="message_bot">
                                    <div class="content_message">
                                        {{ __('hometext.poznNjem') }}
                                    </div>
                                </div>
                            </div>

                            <div class="message zoomIn">
                                <div class="message_user">
                                    <div class="content_message">
										<div class="inputGroup">
											<input id="option1" name="option_jezik" type="radio" value="A1"/>
											<label for="option1">A1</label>
										</div>

										<div class="inputGroup">
											<input id="option2" name="option_jezik" type="radio" value="A2"/>
											<label for="option2">A2</label>
										</div>
										<div class="inputGroup">
											<input id="option3" name="option_jezik" type="radio" value="B1"/>
											<label for="option3">B1</label>
										</div>

										<div class="inputGroup">
											<input id="option4" name="option_jezik" type="radio" value="B2"/>
											<label for="option4">B2</label>
										</div>
										<div class="inputGroup">
											<input id="option5" name="option_jezik" type="radio" value="C1"/>
											<label for="option5">C1</label>
										</div>

										<div class="inputGroup">
											<input id="option6" name="option_jezik" type="radio" value="C2"/>
											<label for="option6">C2</label>
										</div>

										<div class="inputGroup">
											<input id="option7" name="option_jezik" type="radio" value="Bez znanja"/>
											<label for="option7">{{ __('hometext.bezZnanja') }}</label>
										</div>

                                        <button class="njem_jezik_edit tipka_potvrdi" id="njem_jezik_edit"> OK</button>
                                    </div>
                                </div>
                            </div>
                        @endif
						<script>
						</script>

                        {{-- BLOK ZA SLIKU --}}

                        {{-- BLOK ZA VOZACKU --}}
                        <!-- @if( $kandidat->kandidat_vozacka_kategorija == null)
							@if ($nalogBlokovi)
								@if( $kandidat->kandidat_vozacka_dozvola == null)
									<div class="message zoomIn">
										<div class="message_bot">
											<div class="content_message">
												{{ __('hometext.vozackaQ') }}
											</div>
										</div>
									</div>
									<div class="message zoomIn">
										<div class="message_user">
											<div class="content_message">
												<button class="tipka_da vozacka_da" id="vozacka_da" >{{ __('hometext.da') }}</button>
												<button class="tipka_ne vozacka_ne" id="vozacka_ne" >{{ __('hometext.ne') }}</button>
												<input type="hidden" name="provjera_vozacka" id="provjera_vozacka" value="{{ $nalogBlokovi->nbp_vozacka_kategorija }}">
											</div>
										</div>
									</div>
								@endif
								@if($nalogBlokovi->nbp_vozacka_kategorija != null && $nalogBlokovi->nbp_vozacka_kategorija != "NN" && $kandidat->kandidat_vozacka_dozvola != "Ne")
									<div class="message zoomIn">
										<div class="message_bot">
											<div class="content_message">
												{{ __('hometext.kategorijeQ') }}
											</div>
										</div>
									</div>
									<div class="message zoomIn">
										<div class="message_user">
											<div class="content_message checkers">
												<div class="inputGroup">
													<input id="optionv1" name="option_vozacka_kat" type="checkbox" value="B"/>
													<label for="optionv1">B</label>
												</div>

												<div class="inputGroup">
													<input id="optionv2" name="option_vozacka_kat" type="checkbox" value="C1"/>
													<label for="optionv2">C1</label>
												</div>
												<div class="inputGroup">
													<input id="optionv3" name="option_vozacka_kat" type="checkbox" value="C"/>
													<label for="optionv3">C</label>
												</div>

												<div class="inputGroup">
													<input id="optionv4" name="option_vozacka_kat" type="checkbox" value="BE"/>
													<label for="optionv4">BE</label>
												</div>
												<div class="inputGroup">
													<input id="optionv5" name="option_vozacka_kat" type="checkbox" value="C1E"/>
													<label for="optionv5">C1E</label>
												</div>

												<div class="inputGroup">
													<input id="optionv6" name="option_vozacka_kat" type="checkbox" value="CE"/>
													<label for="optionv6">CE</label>
												</div>


												<button class="vozacka_kategorija_check tipka_potvrdi" id="vozacka_kategorija_check"> OK</button>
											</div>
										</div>
									</div>
								@endif
							@endif
                        @endif -->

                        {{-- BLOK ZA OBRAZOVANJE --}}

						{{-- SREDNJE OBRAZOVANJE --}}

						@if( Auth::user()->ne_srednja != 1 )

							<div class="message zoomIn">
								<div class="message_bot">
									<div class="content_message">
										@if( $kandidatSrednje == null )
											{{ __('hometext.srednjaQ') }}
										@else
											{{ __('hometext.srednjaJos') }}
										@endif
									</div>
								</div>
							</div>

							<div class="message zoomIn">
								<div class="message_user">
									<div class="content_message">
										<button class="tipka_da srednja_da" id="srednja_da" >{{ __('hometext.da') }}</button>
										<button class="tipka_ne srednja_ne" id="srednja_ne" >{{ __('hometext.ne') }}</button>
										<input type="hidden" value="{{ count($kandidatSrednje) }}" name="broj_srednjih" id="broj_srednjih">
									</div>
								</div>
							</div>


							<div class="cloning_srednja">
								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.odabirSrednje') }}
										</div>
									</div>
								</div>

								<div class="message zoomIn">
									<div class="message_user">
										<div class="content_message">
											<select class="custom-select select_izgled" id="skole_picker">
												<option value="" selected disabled>{{ __('hometext.Odaberi') }}</option>
												@foreach($srednje_skole as $srednja_skola)
													@if ( Config::get('app.locale') == 'de')
														<option value="{{ $srednja_skola->skola_naziv_de }}" data-skolaa_id="{{$srednja_skola->skola_id}}">{{ $srednja_skola->skola_naziv_de }}</option>
													@else
														<option value="{{ $srednja_skola->skola_naziv }}" data-skolaa_id="{{$srednja_skola->skola_id}}">{{ $srednja_skola->skola_naziv }}</option>
													@endif
												@endforeach
													<option value="ostalo" >{{ __('hometext.Ostalo') }}</option>
											</select>
											<br>

											<button class="tipka_potvrdi odabir_srednje" id="odabir_srednje" >OK</button>
										</div>
									</div>
								</div>

								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.nazivSrednje') }}
										</div>
									</div>
								</div>
								<div class="message zoomIn" style="display: none;">
									<div class="message_user">
										<div class="content_message">
											<input class="input_izgled" type="text" name="ostalo_skola" id="ostalo_skola" placeholder = "{{ __('hometext.NazivSkole') }}">
											<br>
											<button class="tipka_potvrdi unos_naziva_srednje" id="unos_naziva_srednje" >OK</button>
										</div>
									</div>
								</div>
								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.UnesiteSmjer') }}
										</div>
									</div>
								</div>
								<div class="message zoomIn" style="display: none;">
									<div class="message_user">
										<div class="content_message">
											<input class="input_izgled" type="text" name="ostalo_smjer" id="ostalo_smjer" placeholder = "{{ __('hometext.Smjer') }}">
											<br>
											<button class="tipka_potvrdi ostalo_srednje_kraj" id="ostalo_srednje_kraj" >OK</button>
										</div>
									</div>
								</div>

								<script>
									 $(document).ready(function() {
										jQuery.noConflict();
										$('.svi_smjerovi').hide();
										$('.svi_smjerovi_faks').hide();
										// $('#skole_picker').on('change', function() {
											// var cloningDiv = $(this).parent().parent().parent().parent();
											// console.log(cloningDiv);
											// //$('.smjerovi_all').addClass('hidden');
											// var skola_idd = $(cloningDiv).find('select option:selected').data('skolaa_id');
											// $(cloningDiv).find('.opt_'+skola_idd+'').show();
										// });
									});
								</script>
								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.OdaberiteSmjer') }}
										</div>
									</div>
								</div>

								<div class="message zoomIn">
									<div class="message_user">
										<div class="content_message">
											<select class="custom-select select_izgled" id="skole">
												<option value="" selected disabled>{{ __('hometext.Odaberi') }}</option>
												@foreach($smjerovi_srednje as $smjer)
													@if ( Config::get('app.locale') == 'de')
														<option value="{{ $smjer->ss_id }}" class="hidden svi_smjerovi opt_{{ $smjer->ss_skola_id }}">{{ $smjer->ss_naziv_de }}</option>
													@else
														<option value="{{ $smjer->ss_id }}" class="hidden svi_smjerovi opt_{{ $smjer->ss_skola_id }}">{{ $smjer->ss_naziv }}</option>
													@endif
												@endforeach
													<option value="ostalo" >{{ __('hometext.Ostalo') }}</option>
											</select>
											<br>
											<button class="tipka_potvrdi odabir_smjera" id="odabir_smjera" >OK</button>
										</div>
									</div>
								</div>

								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.UnesiteSmjer') }}
										</div>
									</div>
								</div>
								<div class="message zoomIn" style="display: none;">
									<div class="message_user">
										<div class="content_message">
											<input class="input_izgled" type="text" name="ostalo_samo_smjer" id="ostalo_samo_smjer" placeholder = "{{ __('hometext.Smjer') }}">
											<br>
											<button class="tipka_potvrdi ostalo_srednja_smjer_kraj" id="ostalo_srednja_smjer_kraj" >OK</button>
										</div>
									</div>
								</div>

								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.godinaZavr') }}
										</div>
									</div>
								</div>
								<div class="message zoomIn">
									<div class="message_user">
										<div class="content_message">
											<select class="custom-select select_izgled datum_skole" name="godina_do" id="godina_do">
												<option value="" selected disabled>{{ __('hometext.Odaberi') }}</option>
												@if($godinaRodjenja<2005)
													@for($i=2023; $i>$godinaRodjenja+10; $i--)
														<option value="{{ $i }}">{{ $i }}</option>
													@endfor
												@else
													@for($i=2023; $i>1970; $i--)
														<option value="{{ $i }}">{{ $i }}</option>
													@endfor
												@endif
											</select>
											<br>
											<button class="tipka_potvrdi godina_zavrsetka_button" id="godina_zavrsetka_button" >OK</button>
										</div>
									</div>
								</div>

								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.gradSkole') }}
										</div>
									</div>
								</div>
								<div class="message zoomIn">
									<div class="message_user">
										<div class="content_message">
											<input class = "input_izgled" name="srednje_obr_grad" id="srednje_obr_grad" type="text" placeholder = "{{ __('hometext.Grad') }}">
											<br>
											<button class="tipka_potvrdi srednja_grad_button" id="srednja_grad_button" >OK</button>
										</div>
									</div>
								</div>

								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.drzavaSkole') }}
										</div>
									</div>
								</div>
								<div class="message zoomIn">
									<div class="message_user">
										<div class="content_message">
											<input class = "input_izgled" name="srednje_obr_drzava" id="srednje_obr_drzava" type="text" placeholder = "{{ __('hometext.drzava') }}">
											<br>
											<button class="tipka_potvrdi kraj_srednje" id="kraj_srednje" >OK</button>
										</div>
									</div>
								</div>
								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.josSkole') }}
										</div>
									</div>
								</div>
								<div class="message zoomIn ending_loop_question_srednja">
									<div class="message_user">
										<div class="content_message">
											<button class="tipka_da srednja_da_kraj" id="srednja_da_kraj" >{{ __('hometext.da') }}</button>
											<button class="tipka_ne srednja_ne_kraj" id="srednja_ne_kraj" >{{ __('hometext.ne') }}</button>
										</div>
									</div>
								</div>
							</div>
						@endif

						@if ($nalogBlokovi)
							{{-- VISOKO OBRAZOVANJE --}}
                            @if($nalogBlokovi->nbp_visoko_obr)
								@if( Auth::user()->ne_visoka != 1 )
									@if( $kandidatVisoko == null )
										<div class="message zoomIn pocetak_faxa">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.strucnaSprema') }}
												</div>
											</div>
										</div>
									@else
										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.faksJos') }}
												</div>
											</div>
										</div>
									@endif
									<div class="message zoomIn">
										<div class="message_user">
											<div class="content_message">
												<button class="tipka_da fax_da" id="fax_da" >{{ __('hometext.da') }}</button>
												<button class="tipka_ne fax_ne" id="fax_ne" >{{ __('hometext.ne') }}</button>
											</div>
										</div>
									</div>

									<div class="cloning_fax">
										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.unesiFaks') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<select class="custom-select select_izgled" id="fax_picker">
														<option value="" selected disabled>Odaberi</option>
														@foreach($visoke_skole as $visoke_skole)
															@if ( Config::get('app.locale') == 'de')
																<option value="{{ $visoke_skole->skola_naziv_de }}" data-fakss_id="{{$visoke_skole->skola_id}}">{{ $visoke_skole->skola_naziv_de }}</option>
															@else
																<option value="{{ $visoke_skole->skola_naziv }}" data-fakss_id="{{$visoke_skole->skola_id}}">{{ $visoke_skole->skola_naziv }}</option>
															@endif
														@endforeach
															<option value="ostalo" >{{ __('hometext.Ostalo') }}</option>
													</select>
													<br>
													<button class="tipka_potvrdi odabir_fakulteta" id="odabir_fakulteta" > OK</button>
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.nazivVisoke') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn" style="display: none;">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="ostalo_fax" id="ostalo_fax" placeholder = "{{ __('hometext.NazivSkole') }}">
													<br>
													<button class="tipka_potvrdi unos_naziva_faksa" id="unos_naziva_faksa" >OK</button>
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.UnesiteSmjer') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn" style="display: none;">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="ostalo_smjer_fax" id="ostalo_smjer_fax" placeholder = "{{ __('hometext.Smjer') }}">
													<br>
													<button class="tipka_potvrdi ostalo_fax_kraj" id="ostalo_fax_kraj" >OK</button>
												</div>
											</div>
										</div>
										<script>
											$(document).ready(function() {
												jQuery.noConflict();
												$('.svi_smjerovi').hide();
												$('.svi_smjerovi_faks').hide();
												/*$(document).on('change', '#fax_picker', function () {
													$('.svi_smjerovi_faks').hide();
													var faks_idd = $(this).find(':selected').data('fakss_id');
													$('.opt_'+faks_idd+'').show();
												});*/
											});
										</script>
										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.Smjer') }}
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<select class="custom-select select_izgled" id="skole2">
														<option value="" selected disabled>{{ __('hometext.Odaberi') }}</option>
														@foreach($smjerovi_faks as $smjer)
															@if ( Config::get('app.locale') == 'de')
																<option value="{{ $smjer->ss_id }}" class="hidden svi_smjerovi_faks opt_{{ $smjer->ss_skola_id }}">{{ $smjer->ss_naziv_de }}</option>
															@else
																<option value="{{ $smjer->ss_id }}" class="hidden svi_smjerovi_faks opt_{{ $smjer->ss_skola_id }}">{{ $smjer->ss_naziv }}</option>
															@endif
														@endforeach
															<option value="ostalo" >{{ __('hometext.Ostalo') }}</option>
													</select>
													<br>
													<button class="tipka_potvrdi odabir_smjera_fax" id="odabir_smjera_fax" > OK</button>
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.UnesiteSmjer') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn" style="display: none;">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="ostalo_samo_smjer_fax" id="ostalo_samo_smjer_fax" placeholder = "{{ __('hometext.Smjer') }}">
													<br>
													<button class="tipka_potvrdi ostalo_fax_smjer_kraj" id="ostalo_fax_smjer_kraj" >OK</button>
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.godDiplome') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<select class="custom-select select_izgled datum_skole godina_dipl" name="godina_dipl" id="godina_dipl" >
														<option value="" selected disabled>{{ __('hometext.Odaberi') }}</option>
														@for($i=2023; $i>$godinaRodjenja+15; $i--)
															<option value="{{ $i }}">{{ $i }}</option>
														@endfor
													</select>
													<br>
													<button class="tipka_potvrdi godina_zavrsetka_faxa_button" id="godina_zavrsetka_faxa_button"> OK</button>
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.gradSkole') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class = "input_izgled" name="grad_fax" id="grad_fax" type="text" placeholder = "{{ __('hometext.Grad') }}"><br>
													<button class="tipka_potvrdi fax_grad_button" id="fax_grad_button" >OK</button>
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.drzavaSkole') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class = "input_izgled" name="drzava_fax" id="drzava_fax" type="text" placeholder = "{{ __('hometext.drzava') }}"><br>
													<button class="tipka_potvrdi kraj_faxa" id="kraj_faxa" >OK</button>
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.faksJos') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn ending_loop_question_fax">
											<div class="message_user">
												<div class="content_message">
													<button class="tipka_da fax_da_kraj" id="fax_da_kraj" >{{ __('hometext.da') }}</button>
													<button class="tipka_ne fax_ne_kraj" id="fax_ne_kraj" >{{ __('hometext.ne') }}</button>
												</div>
											</div>
										</div>
									</div>
								@endif
                            @endif

                            {{-- DODATNA EDUKACIJA --}}
                            @if($nalogBlokovi->nbp_dodatno_obr)
								@if( Auth::user()->ne_dodatna != 1 )

									<div class="message zoomIn">
										<div class="message_bot">
											<div class="content_message">
											@if( $kandidatDodatno == null )
												{{ __('hometext.dodatnaQ') }}
											@else
												{{ __('hometext.dodatnaJos') }}
											@endif
											</div>
										</div>
									</div>

									<div class="message zoomIn">
										<div class="message_user">
											<div class="content_message">
												<button class="tipka_da dod_edu_da" id="dod_edu_da" >{{ __('hometext.da') }}</button>
												<button class="tipka_ne dod_edu_ne" id="dod_edu_ne" >{{ __('hometext.ne') }}</button>
											</div>
										</div>
									</div>

									<div class="cloning">

										<div class="message zoomIn" id="dod_edu_loop">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.dodatnaVrsta') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<select class="custom-select select_izgled select_vrsta_dod_edu" id="select_vrsta_dod_edu">
														<option value="" selected disabled>{{ __('hometext.Odaberi') }}</option>
														<option value="seminar">{{ __('hometext.Seminari') }}</option>
														<option value="kurs">{{ __('hometext.Kursevi') }}</option>
														<option value="certifikat">{{ __('hometext.Certifikati') }}</option>
														@if( $kandidat->kandidat_group == "2" or $kandidat->kandidat_group == "6" or $kandidat->kandidat_group == "23" )
														<option value="pripravnicki">{{ __('hometext.pripravnicki') }}</option>
														@endif
														<option value="ostalo">Ostalo</option>
													</select>
													<br>
													<button class="tipka_potvrdi button_vrsta_dod_edu" id="button_vrsta_dod_edu"> OK</button>
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.dodatnaNaziv') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn" style="display: none;">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="naziv_dod" id="naziv_dod" placeholder = "{{ __('hometext.Naziv') }}">
													<br>
													<button class="tipka_potvrdi button_naziv_dod_edu" id="button_naziv_dod_edu" >OK</button>
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.dodatnaGrad') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class = "input_izgled" type="text" name="grad_dod" id="grad_dod" placeholder = "{{ __('hometext.Grad') }}">
													<br>
													<button class="tipka_potvrdi button_dod_edu_grad" id="button_dod_edu_grad" >OK</button>
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.kojeGodine') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<select class="custom-select select_izgled datum_dod_edu" name="godina_dod_edu" id="godina_dod_edu" >
														<option value="" selected disabled>{{ __('hometext.Odaberi') }}</option>
														@for($i=2023; $i>$godinaRodjenja+10; $i--)
															<option value="{{ $i }}">{{ $i }}</option>
														@endfor
													</select>
													<br>
													<button class="tipka_potvrdi button_god_dod_edu" id="button_god_dod_edu"> OK</button>
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.dodatniOpis') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class = "input_izgled" type="text" name="opis_dod" id="opis_dod_edu" placeholder = "{ __('hometext.dodajOpis') }}" >
													<br>
													<button class="tipka_potvrdi kraj_dod_edu" id="kraj_dod_edu" >OK</button>
												</div>
											</div>
										</div>

										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.dodatnaQ') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn ending_loop_question">
											<div class="message_user">
												<div class="content_message">
													<button class="tipka_da dod_edu_da_kraj" id="dod_edu_da_kraj" >{{ __('hometext.da') }}</button>
													<button class="tipka_ne dod_edu_ne_kraj" id="dod_edu_ne_kraj" >{{ __('hometext.ne') }}</button>
												</div>
											</div>
										</div>
									</div>
								@endif
                            @endif

                        @endif

                        {{-- BLOK ZA RADNO ISKUSTVO --}}
						@if( Auth::user()->ne_iskustvo != 1 AND $kandidat->kandidat_id != 140327)

							<div class="message zoomIn">
								<div class="message_bot">
									<div class="content_message">
										@if(count($kandidatIskustva) == 0)
											{{ __('hometext.iskustvoQ') }}
										@else
											{{ __('hometext.iskustvoJos') }}
										@endif
									</div>
								</div>
							</div>
							<div class="message zoomIn">
                                <div class="message_user">
                                    <div class="content_message">
                                        <button class="tipka_da iskustvo_da" id="iskustvo_da" >{{ __('hometext.da') }}</button>
                                        <button class="tipka_ne iskustvo_ne" id="iskustvo_ne" >{{ __('hometext.ne') }}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="cloning_iskustvo">

                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                        {{ __('hometext.unesiPoziciju') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn">
                                    <div class="message_user">
                                        <div class="content_message">
                                            <input class = "input_izgled" type="text" name="pozicija_iskustvo" id="pozicija_iskustvo" placeholder = "{{ __('hometext.Pozicija') }}" >
											<br>
											<button class="tipka_potvrdi button_isk_pozicija" id="button_isk_pozicija" >OK</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                            {{ __('hometext.nazivPoslodavca') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn">
                                    <div class="message_user">
                                        <div class="content_message">
                                            <input class = "input_izgled" type="text" name="poslodavac_iskustvo" id="poslodavac_iskustvo" placeholder = "{{ __('hometext.Poslodavac') }}" >
											<br>
											<button class="tipka_potvrdi button_isk_poslodavac" id="button_isk_poslodavac" >OK</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                            {{ __('hometext.pocetakRada') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn" style="display: none;">
									<div class="message_user">
										<div class="content_message">

											<select class="custom-select select_izgled" name="datum_mjesec_isk" id="datum_mjesec_isk" data-live-search="true" >
													<option value="" selected disabled>{{ __('hometext.mjesec') }}</option>

													@for ($month=1; $month <= 12; $month++)
														<option value="{{ $month }}">{{ $month }}</option>
													@endfor
											</select>
											<select class="custom-select select_izgled" name="datum_god_isk" id="datum_god_isk" data-live-search="true" >
													<option value="" selected disabled>{{ __('hometext.godina') }}</option>

													@for ($year=date("Y"); $year >= $godinaRodjenja+15; $year--)
														<option value="{{ $year }}">{{ $year }}</option>
													@endfor
											</select>
											<br>
											<button class="tipka_potvrdi button_isk_datum_od" id="button_isk_datum_od" >OK</button>
										</div>
									</div>
								</div>
                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                            {{ __('hometext.krajRada') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn" style="display: none;">
									<div class="message_user">
										<div class="content_message">

											<select class="custom-select select_izgled" name="datum_mjesec_isk_do" id="datum_mjesec_isk_do" data-live-search="true" >
													<option value="" selected disabled>{{ __('hometext.mjesec') }}</option>

													@for ($month=1; $month <= 12; $month++)
														<option value="{{ $month }}">{{ $month }}</option>
													@endfor
											</select>
											<select class="custom-select select_izgled" name="datum_god_isk_do" id="datum_god_isk_do" data-live-search="true" >
													<option value="" selected disabled>{{ __('hometext.godina') }}</option>

													@for ($year=date("Y"); $year >= 1970; $year--)
														<option value="{{ $year }}">{{ $year }}</option>
													@endfor
											</select>
											<br>
											<div class="main-container__column material-checkbox-group material-checkbox-group_primary">
												<input type="checkbox" id="kri_datum_do_aktuelno" name="kri_datum_do_aktuelno" class="material-checkbox">
												<label class="material-checkbox-group__label" for="kri_datum_do_aktuelno">{{ __('hometext.Aktuelno') }}</label>
											</div>
											<button class="tipka_potvrdi button_isk_datum_do" id="button_isk_datum_do" >OK</button>

										</div>
									</div>
								</div>
                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                            {{ __('hometext.uKojemGradu') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn">
                                    <div class="message_user">
                                        <div class="content_message">
                                            <input class = "input_izgled" type="text" name="grad_iskustvo" id="grad_iskustvo" placeholder = "{{ __('hometext.Grad') }}">
											<br>
											<button class="tipka_potvrdi button_isk_grad" id="button_isk_grad" >OK</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="message zoomIn">
                                    <div class="message_bot">
                                        <div class="content_message">
                                            {{ __('hometext.iskustvoJos') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="message zoomIn ending_loop_question_iskustvo">
                                    <div class="message_user">
                                        <div class="content_message">
                                            <button class="tipka_da iskustvo_da_kraj" id="iskustvo_da_kraj" >{{ __('hometext.da') }}</button>
                                            <button class="tipka_ne iskustvo_ne_kraj" id="iskustvo_ne_kraj" >{{ __('hometext.ne') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<script>

                            </script>
                        @endif

                        {{-- BLOK ZA VJEŠTINE (za sad ga nece biti) --}}

                        {{-- BLOK ZA OSTALE JEZIKE (za sad ga nece biti) --}}

                        {{-- BLOK ZA DODAVANJE DOKUMENATA --}}
                        @if ($nalogBlokovi)
							@if ( $nalogBlokovi->nbp_korak7 == 1 )
								@if ( $nalogBlokovi->nbp_slika == 1)
									@if ( $docsSlika == null && $kandidat->kandidat_slika == "none" )
										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.dodajFoto') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<form method="POST" enctype="multipart/form-data" id="form_docs">
														@csrf
														<input type="hidden" name="document_dataid" value="{{ $kandidat->kandidat_id }}" />
														<input type="hidden" name="kandidat_id" value="{{ $kandidat->kandidat_id }}" />
														<input type="hidden" name="kandidat_check" value="0" />
														<input type="hidden" name="document_name" value="Slika" />
														<input type="hidden" name="document_desc" value="desc" />
														<div class="fileinput fileinput-new" data-provides="fileinput">
															<span class="btn btn-default btn-file">
																<span class="fileinput-new">{{ __('hometext.izaberiDoc') }}</span>
																<span class="fileinput-exists">{{ __('hometext.Promijeni') }}</span>
																<input type="file" name="document_file" id="document_file" >
															</span>
															<i class="fa fa-info-circle fa-lg " data-toggle="tooltip" data-placement="right" title="{{ __('hometext.napomena1') }} -{{ __('hometext.napomena2') }} jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
															<span class="fileinput-filename"></span>
															<script>
																$(function (){
																	$('#document_file').change(function (){

																		var f = this.files[0];

																		if (f.size > 20388608 || f.fileSize > 20388608){
																			console.log("to big");
																			this.value = null;
																		}else{
																			console.log("ok");
																		}

																		var ext = $('#document_file').val().split('.').pop().toLowerCase();

																		if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																			console.log("bad ext");
																			this.value = null;
																		}else{
																			console.log("GOOD ext");
																		}
																	})
																});
															</script>
														</div>
													</form>
													<button type="submit" class="tipka_potvrdi" form="form_docs" id="dodaj_docs" >OK</button>
													<script>
														$(document).ready(function(){
															$('#dodaj_docs').on('click',function(e){
																e.preventDefault();
																var kand_id_ajax = $(document).find('input[name="kand_id_ajax"]').val();
																var broj_srednjih_iz_baze = {{ count($kandidatSrednje) }};
																console.log(broj_srednjih_iz_baze);
																var slika_div = $(this).parent().parent().parent();
																var document_file = slika_div.find('input[name="document_file"]').val();
																var document_dataid = slika_div.find('input[name="document_dataid"]').val();
																console.log(document_file);
																if(document_file == ""){
																	alert("Molimo odaberite dokument");
																}else{

																	var form = $('#form_docs')[0];
																	var data = new FormData(form);
																	$.ajax({
																		type: "POST",
																		enctype: 'multipart/form-data',
																		url: 'https://crm.job-step.com/public_kandidati?page=add_kandidat_doc_form',
																		data: data,
																		processData: false,
																		contentType: false,
																		cache: false
																	});
																	var showNext = slika_div.next(".message");
																	if(showNext.hasClass("zadnja_poruka")){
																		var token = $('meta[name="csrf-token"]').attr('content');
																		$.ajax({
																			url: '/messenger/updateStatusObrade',
																			type: 'POST',
																			data: {"_token": token, "status": 4},
																			dataType: 'html',
																			success: function(data) {
																				if(data == 1){
																					$.ajax({
																						url: 'https://crm.job-step.com/public_kandidati?page=checkCandidateInputs',
																						type: 'POST',
																						data: {"kand_id_ajax": kand_id_ajax},
																						success: function(data) {

																						}
																					});
																				}
																			}
																		});
																		showNext.show();
																		showNext.next(".message").delay(500).show(0);

																	}else if(showNext.hasClass("diploma_doc")){
																		console.log("diploma je");
																		broj_srednjih = $(document).find('input[name="broj_srednjih"]').val();
																		console.log(broj_srednjih);
																		if(broj_srednjih == 0)
																			showNext = showNext.next(".message").next(".message").next(".message").next(".message");
																		if(broj_srednjih == undefined && broj_srednjih_iz_baze == 0)
																			showNext = showNext.next(".message").next(".message").next(".message").next(".message");
																		//console.log(showNext.next());
																		showNext.show();
																		showNext.next(".message").delay(500).show(0);
																	}
																	showNext.show();
																	showNext.next(".message").delay(500).show(0);
																	$('html, body').animate({
																		scrollTop: $('div.message_field').offset().top + $("div.message_field")[0].scrollHeight
																	}, 'slow');
																	slika_div.find("#dodaj_docs").prop('disabled', true);
																	slika_div.find("#document_file").prop('disabled', true);
																}
															});
														});
													</script>
												</div>
											</div>
										</div>
									@endif
								@endif
								{{--
								@if ( $nalogBlokovi->nbp_diploma == 1)
									@if( Auth::user()->ne_diploma != 1 )
										@if ( $docsDiploma == null )
											<div class="message zoomIn diploma_doc">
												<div class="message_bot">
													<div class="content_message">
														{{ __('hometext.diplomaSrednje') }}
													</div>
												</div>
											</div>
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<button class="tipka_da diploma_srednje_da" id="diploma_srednje_da" >{{ __('hometext.da') }}</button>
														<button class="tipka_ne diploma_srednje_ne" id="diploma_srednje_ne" >{{ __('hometext.ne') }}</button>
													</div>
												</div>
											</div>
											<div class="message zoomIn">
												<div class="message_bot">
													<div class="content_message">
														{{ __('hometext.unesiDiplomu') }}
													</div>
												</div>
											</div>
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<form method="POST" enctype="multipart/form-data" id="form_docs_diploma">
															@csrf
															<input type="hidden" name="document_dataid" value="{{ $kandidat->kandidat_id }}" />
															<input type="hidden" name="kandidat_id" value="{{ $kandidat->kandidat_id }}" />
															<input type="hidden" name="kandidat_check" value="0" />
															<input type="hidden" name="document_name" value="Diploma završene škole" />
															<div class="fileinput fileinput-new" data-provides="fileinput">
																<span class="btn btn-default btn-file">
																	<span class="fileinput-new">{{ __('hometext.izaberiDoc') }}</span>
																	<span class="fileinput-exists">cccccc</span>
																	<input type="file" name="document_file" id="document_file_diploma" >
																</span>
																<i class="fa fa-info-circle fa-lg " data-toggle="tooltip" data-placement="right" title="{{ __('hometext.napomena1') }} -{{ __('hometext.napomena2') }} jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
																<span class="fileinput-filename"></span>
																<script>
																	$(function (){
																		$('#document_file_diploma').change(function (){

																			var f = this.files[0];

																			if (f.size > 20388608 || f.fileSize > 20388608){
																				console.log("to big");
																				this.value = null;
																			}else{
																				console.log("ok");
																			}

																			var ext = $('#document_file_diploma').val().split('.').pop().toLowerCase();

																			if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																				console.log("bad ext");
																				this.value = null;
																			}else{
																				console.log("GOOD ext");
																			}
																		})
																	});
																</script>
															</div>
															<input class = "input_izgled" type="text" name="document_desc" id="desc_diploma" placeholder="{{ __('hometext.dodatniOpis') }}" />
														</form>
														<button type="submit" class="tipka_potvrdi" form="form_docs_diploma" id="dodaj_docs_diploma" >OK</button>
														<script>
															$(document).ready(function(){
																$('#dodaj_docs_diploma').on('click',function(e){
																	e.preventDefault();
																	var kand_id_ajax = $(document).find('input[name="kand_id_ajax"]').val();
																	var slika_div = $(this).parent().parent().parent();
																	var document_file = slika_div.find('input[name="document_file"]').val();
																	var document_dataid = slika_div.find('input[name="document_dataid"]').val();
																	console.log(document_dataid);
																	if(document_file == ""){
																		alert("Molimo odaberite dokument");
																	}else{
																		var form = $('#form_docs_diploma')[0];
																		var data = new FormData(form);
																		$.ajax({
																			type: "POST",
																			enctype: 'multipart/form-data',
																			url: 'https://crm.job-step.com/public_kandidati?page=add_kandidat_doc_form',
																			data: data,
																			processData: false,
																			contentType: false,
																			cache: false
																		});
																		var showNext = slika_div.next(".message");
																		showNext.show();
																		showNext.next(".message").delay(500).show(0);
																		if(showNext.hasClass("zadnja_poruka")){
																			var token = $('meta[name="csrf-token"]').attr('content');
																			$.ajax({
																				url: '/messenger/updateStatusObrade',
																				type: 'POST',
																				data: {"_token": token, "status": 4},
																				dataType: 'html',
																				success: function(data) {
																					if(data == 1){
																						$.ajax({
																							url: 'https://crm.job-step.com/public_kandidati?page=checkCandidateInputs',
																							type: 'POST',
																							data: {"kand_id_ajax": kand_id_ajax},
																							success: function(data) {

																							}
																						});
																					}
																				}
																			});
																		}
																		$('html, body').animate({
																			scrollTop: $('div.message_field').offset().top + $("div.message_field")[0].scrollHeight
																		}, 'slow');
																		slika_div.find("#dodaj_docs_diploma").prop('disabled', true);
																		slika_div.find("#document_file_diploma").prop('disabled', true);
																		slika_div.find("#desc_diploma").prop('disabled', true);
																	}
																});
															});
														</script>
													</div>
												</div>
											</div>
										@endif
									@endif
								@endif
								@if( $kandidat->kandidat_group == "2" or $kandidat->kandidat_group == "6" or $kandidat->kandidat_group == "23" )
									@if ( $nalogBlokovi->nbp_pripravnicki == 1)
										@if( Auth::user()->ne_pripravnicki != 1 )
											@if ( $docsPripravnicki == null)
												<div class="message zoomIn">
													<div class="message_bot">
														<div class="content_message">
															{{ __('hometext.uvjPripravnickiQ') }}
														</div>
													</div>
												</div>
												<div class="message zoomIn">
													<div class="message_user">
														<div class="content_message">
															<button class="tipka_da pripravnicki_da" id="pripravnicki_da" >{{ __('hometext.da') }}</button>
															<button class="tipka_ne pripravnicki_ne" id="pripravnicki_ne" >{{ __('hometext.ne') }}</button>
														</div>
													</div>
												</div>
												<div class="message zoomIn">
													<div class="message_bot">
														<div class="content_message">
															{{ __('hometext.dodajUvjPripravnicki') }}
														</div>
													</div>
												</div>
												<div class="message zoomIn">
													<div class="message_user">
														<div class="content_message">
															<form method="POST" enctype="multipart/form-data" id="form_docs_pripravnicki">
																@csrf
																<input type="hidden" name="document_dataid" value="{{ $kandidat->kandidat_id }}" />
																<input type="hidden" name="kandidat_id" value="{{ $kandidat->kandidat_id }}" />
																<input type="hidden" name="kandidat_check" value="0" />
																<input type="hidden" name="document_name" value="Uvjerenje o pripravničkom stažu" />
																<div class="fileinput fileinput-new" data-provides="fileinput">
																	<span class="btn btn-default btn-file">
																		<span class="fileinput-new">{{ __('hometext.izaberiDoc') }}</span>
																		<span class="fileinput-exists">{{ __('hometext.Promijeni') }}</span>
																		<input type="file" name="document_file" id="document_file_pripravnicki" >
																	</span>
																	<i class="fa fa-info-circle fa-lg " data-toggle="tooltip" data-placement="right" title="{{ __('hometext.napomena1') }} -{{ __('hometext.napomena2') }} jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
																	<span class="fileinput-filename"></span>
																	<script>
																		$(function (){
																			$('#document_file_pripravnicki').change(function (){

																				var f = this.files[0];

																				if (f.size > 20388608 || f.fileSize > 20388608){
																					console.log("to big");
																					this.value = null;
																				}else{
																					console.log("ok");
																				}

																				var ext = $('#document_file_pripravnicki').val().split('.').pop().toLowerCase();

																				if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																					console.log("bad ext");
																					this.value = null;
																				}else{
																					console.log("GOOD ext");
																				}
																			})
																		});
																	</script>
																</div>
																<input class = "input_izgled" type="text" name="document_desc" placeholder="{{ __('hometext.dodatniOpis') }}" />
															</form>
															<button type="submit" class="tipka_potvrdi" form="form_docs_pripravnicki" id="dodaj_docs_pripravnicki" >OK</button>
															<script>
																$(document).ready(function(){
																	$('#dodaj_docs_pripravnicki').on('click',function(e){
																		e.preventDefault();
																		var kand_id_ajax = $(document).find('input[name="kand_id_ajax"]').val();
																		var slika_div = $(this).parent().parent().parent();
																		var document_file = slika_div.find('input[name="document_file"]').val();
																		var document_dataid = slika_div.find('input[name="document_dataid"]').val();
																		console.log(document_dataid);

																		if(document_file == ""){
																			alert("Molimo odaberite dokument");
																		}else{
																			var form = $('#form_docs_pripravnicki')[0];
																			var data = new FormData(form);
																			$.ajax({
																				type: "POST",
																				enctype: 'multipart/form-data',
																				url: 'https://crm.job-step.com/public_kandidati?page=add_kandidat_doc_form',
																				data: data,
																				processData: false,
																				contentType: false,
																				cache: false
																			});
																			var showNext = slika_div.next(".message");
																			showNext.show();
																			showNext.next(".message").delay(500).show(0);
																			if(showNext.hasClass("zadnja_poruka")){
																				var token = $('meta[name="csrf-token"]').attr('content');
																				$.ajax({
																					url: '/messenger/updateStatusObrade',
																					type: 'POST',
																					data: {"_token": token, "status": 4},
																					dataType: 'html',
																					success: function(data) {
																						if(data == 1){
																							$.ajax({
																								url: 'https://crm.job-step.com/public_kandidati?page=checkCandidateInputs',
																								type: 'POST',
																								data: {"kand_id_ajax": kand_id_ajax},
																								success: function(data) {

																								}
																							});
																						}
																					}
																				});
																			}
																			$('html, body').animate({
																				scrollTop: $('div.message_field').offset().top + $("div.message_field")[0].scrollHeight
																			}, 'slow');
																			slika_div.find("#dodaj_docs_pripravnicki").prop('disabled', true);
																			slika_div.find("#document_file_pripravnicki").prop('disabled', true);
																			slika_div.find("#document_desc").prop('disabled', true);
																		}
																	});
																});
															</script>
														</div>
													</div>
												</div>
											@endif
										@endif
									@endif
									@if ( $nalogBlokovi->nbp_strucni == 1)
										@if( Auth::user()->ne_strucni != 1 )
											@if ( $docsStrucni == null)
												<div class="message zoomIn">
													<div class="message_bot">
														<div class="content_message">
															{{ __('hometext.uvjStrucniQ') }}
														</div>
													</div>
												</div>
												<div class="message zoomIn">
													<div class="message_user">
														<div class="content_message">
															<button class="tipka_da strucni_da" id="strucni_da" >{{ __('hometext.da') }}</button>
															<button class="tipka_ne strucni_ne" id="strucni_ne" >{{ __('hometext.ne') }}</button>
														</div>
													</div>
												</div>
												<div class="message zoomIn">
													<div class="message_bot">
														<div class="content_message">
															{{ __('hometext.dodajUvjStrucni') }}
														</div>
													</div>
												</div>
												<div class="message zoomIn">
													<div class="message_user">
														<div class="content_message">
															<form method="POST" enctype="multipart/form-data" id="form_docs_strucni">
																@csrf
																<input type="hidden" name="document_dataid" value="{{ $kandidat->kandidat_id }}" />
																<input type="hidden" name="kandidat_id" value="{{ $kandidat->kandidat_id }}" />
																<input type="hidden" name="kandidat_check" value="0" />
																<input type="hidden" name="document_name" value="Uvjerenje o položenom stručnom ispitu" />
																<div class="fileinput fileinput-new" data-provides="fileinput">
																	<span class="btn btn-default btn-file">
																		<span class="fileinput-new">{{ __('hometext.izaberiDoc') }}</span>
																		<span class="fileinput-exists">{{ __('hometext.Promijeni') }}</span>
																		<input type="file" name="document_file" id="document_file_strucni" >
																	</span>
																	<i class="fa fa-info-circle fa-lg " data-toggle="tooltip" data-placement="right" title="{{ __('hometext.napomena1') }} -{{ __('hometext.napomena2') }} jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
																	<span class="fileinput-filename"></span>
																	<script>
																		$(function (){
																			$('#document_file_strucni').change(function (){

																				var f = this.files[0];

																				if (f.size > 20388608 || f.fileSize > 20388608){
																					console.log("to big");
																					this.value = null;
																				}else{
																					console.log("ok");
																				}

																				var ext = $('#document_file_strucni').val().split('.').pop().toLowerCase();

																				if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																					console.log("bad ext");
																					this.value = null;
																				}else{
																					console.log("GOOD ext");
																				}
																			})
																		});
																	</script>
																</div>
																<input class = "input_izgled" type="text" name="document_desc" id="desc_strucni" placeholder="{{ __('hometext.dodatniOpis') }}" />
															</form>
															<button type="submit" class="tipka_potvrdi" form="form_docs_strucni" id="dodaj_docs_strucni" >OK</button>
															<script>
																$(document).ready(function(){
																	$('#dodaj_docs_strucni').on('click',function(e){
																		e.preventDefault();
																		var kand_id_ajax = $(document).find('input[name="kand_id_ajax"]').val();
																		var slika_div = $(this).parent().parent().parent();
																		var document_file = slika_div.find('input[name="document_file"]').val();
																		var document_dataid = slika_div.find('input[name="document_dataid"]').val();
																		if(document_file == ""){
																		alert("Molimo odaberite dokument");
																		}else{
																			var form = $('#form_docs_strucni')[0];
																			var data = new FormData(form);
																			$.ajax({
																				type: "POST",
																				enctype: 'multipart/form-data',
																				url: 'https://crm.job-step.com/public_kandidati?page=add_kandidat_doc_form',
																				data: data,
																				processData: false,
																				contentType: false,
																				cache: false
																			});
																			var showNext = slika_div.next(".message");
																			showNext.show();
																			showNext.next(".message").delay(500).show(0);
																			if(showNext.hasClass("zadnja_poruka")){
																				var token = $('meta[name="csrf-token"]').attr('content');
																				$.ajax({
																					url: '/messenger/updateStatusObrade',
																					type: 'POST',
																					data: {"_token": token, "status": 4},
																					dataType: 'html',
																					success: function(data) {
																						if(data == 1){
																							$.ajax({
																								url: 'https://crm.job-step.com/public_kandidati?page=checkCandidateInputs',
																								type: 'POST',
																								data: {"kand_id_ajax": kand_id_ajax},
																								success: function(data) {

																								}
																							});
																						}
																					}
																				});
																			}
																			$('html, body').animate({
																				scrollTop: $('div.message_field').offset().top + $("div.message_field")[0].scrollHeight
																			}, 'slow');
																			slika_div.find("#dodaj_docs_strucni").prop('disabled', true);
																			slika_div.find("#document_file_strucni").prop('disabled', true);
																			slika_div.find("#desc_strucni").prop('disabled', true);
																		}
																	});
																});
															</script>
														</div>
													</div>
												</div>
											@endif
										@endif
									@endif
								@endif
								@if ( $nalogBlokovi->nbp_jezik_cert == 1)
									@if( Auth::user()->ne_cert_jezik != 1 )
										@if ( $docsCertJezik == null)
											<div class="message zoomIn">
												<div class="message_bot">
													<div class="content_message">
														{{ __('hometext.certJezikQ') }}
													</div>
												</div>
											</div>
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<button class="tipka_da cert_jezik_da" id="cert_jezik_da" >{{ __('hometext.da') }}</button>
														<button class="tipka_ne cert_jezik_ne" id="cert_jezik_ne" >{{ __('hometext.ne') }}</button>
													</div>
												</div>
											</div>
											<div class="message zoomIn">
												<div class="message_bot">
													<div class="content_message">
														{{ __('hometext.dodajCertJezik') }}
													</div>
												</div>
											</div>
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<form method="POST" enctype="multipart/form-data" id="form_docs_cert_jezik">
															@csrf
															<input type="hidden" name="document_dataid" value="{{ $kandidat->kandidat_id }}" />
															<input type="hidden" name="kandidat_id" value="{{ $kandidat->kandidat_id }}" />
															<input type="hidden" name="kandidat_check" value="0" />
															<input type="hidden" name="document_name" value="Certifikati o poznavanju jezika" />
															<div class="fileinput fileinput-new" data-provides="fileinput">
																<span class="btn btn-default btn-file">
																	<span class="fileinput-new">{{ __('hometext.izaberiDoc') }}</span>
																	<span class="fileinput-exists">{{ __('hometext.Promijeni') }}</span>
																	<input type="file" name="document_file" id="document_file_cert_jezik" >
																</span>
																<i class="fa fa-info-circle fa-lg " data-toggle="tooltip" data-placement="right" title="{{ __('hometext.napomena1') }} -{{ __('hometext.napomena2') }} jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
																<span class="fileinput-filename"></span>
																<a href="#" class="close fileinput-exists" id="close_cert_jezik" data-dismiss="fileinput" style="float: none; opacity: 0.8;"><i class="fa fa-trash" aria-hidden="true"></i></a>
																<script>
																	$(function (){
																		$('#document_file_cert_jezik').change(function (){

																			var f = this.files[0];

																			if (f.size > 20388608 || f.fileSize > 20388608){
																				console.log("to big");
																				this.value = null;
																			}else{
																				console.log("ok");
																			}

																			var ext = $('#document_file_cert_jezik').val().split('.').pop().toLowerCase();

																			if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																				console.log("bad ext");
																				this.value = null;
																			}else{
																				console.log("GOOD ext");
																			}
																		})
																	});
																</script>
															</div>
															<input class = "input_izgled" type="text" name="document_desc" id="desc_jezik" placeholder="{{ __('hometext.dodatniOpis') }}" />
														</form>
														<button type="submit" class="tipka_potvrdi" form="form_docs_cert_jezik" id="dodaj_docs_cert_jezik" >OK</button>
														<script>
															$(document).ready(function(){
																$('#dodaj_docs_cert_jezik').on('click',function(e){
																	e.preventDefault();
																	var kand_id_ajax = $(document).find('input[name="kand_id_ajax"]').val();
																	var slika_div = $(this).parent().parent().parent();
																	var document_file = slika_div.find('input[name="document_file"]').val();
																	var document_dataid = slika_div.find('input[name="document_dataid"]').val();
																	console.log(document_dataid);
																	if(document_file == ""){
																		alert("Molimo odaberite dokument");
																	}else{
																		var form = $('#form_docs_cert_jezik')[0];
																		var data = new FormData(form);
																		$.ajax({
																			type: "POST",
																			enctype: 'multipart/form-data',
																			url: 'https://crm.job-step.com/public_kandidati?page=add_kandidat_doc_form',
																			data: data,
																			processData: false,
																			contentType: false,
																			cache: false
																		});
																		var showNext = slika_div.next(".message");
																		showNext.show();
																		showNext.next(".message").delay(500).show(0);
																		if(showNext.hasClass("zadnja_poruka")){
																			var token = $('meta[name="csrf-token"]').attr('content');
																			$.ajax({
																				url: '/messenger/updateStatusObrade',
																				type: 'POST',
																				data: {"_token": token, "status": 4},
																				dataType: 'html',
																				success: function(data) {
																					if(data == 1){
																						$.ajax({
																							url: 'https://crm.job-step.com/public_kandidati?page=checkCandidateInputs',
																							type: 'POST',
																							data: {"kand_id_ajax": kand_id_ajax},
																							success: function(data) {

																							}
																						});
																					}
																				}
																			});
																		}
																		$('html, body').animate({
																			scrollTop: $('div.message_field').offset().top + $("div.message_field")[0].scrollHeight
																		}, 'slow');
																		slika_div.find("#dodaj_docs_cert_jezik").prop('disabled', true);
																		slika_div.find("#document_file_cert_jezik").prop('disabled', true);
																		slika_div.find("#desc_jezik").prop('disabled', true);
																	}
																});
															});
														</script>
													</div>
												</div>
											</div>
										@endif
									@endif
								@endif
							--}}
                            @endif
                        @endif

						{{-- BLOK ZA OSTALE INFO (DATUM RODJENJA) --}}
						@if( $kandidat->kandidat_datumrodjenja == null )
							<div class="message zoomIn" style="display: none;">
								<div class="message_bot">
									<div class="content_message">
										{{ __('hometext.unesiDatumR') }}
									</div>
								</div>
							</div>
							<div class="message zoomIn">
								<div class="message_user">
									<div class="content_message">

										<select class="custom-select select_izgled" name="unos_datum_dan" id="unos_datum_dan" required>
												<option value="" selected disabled>DD</option>
												<?php
												for ($day=1; $day<=31; $day++){ ?>
													<option value="<?php echo $day; ?>"><?php echo $day; ?></option>
												<?php } ?>
										</select>
										<select class="custom-select select_izgled" name="unos_datum_mjesec" id="unos_datum_mjesec" required>
												<option value="" selected disabled>MM</option>
												<?php
												for ($month=1; $month<=12; $month++){ ?>
													<option value="<?php echo $month; ?>"><?php echo $month; ?></option>
												<?php } ?>
										</select>
										<select class="custom-select select_izgled" name="unos_datum_godina" id="unos_datum_mjesec" required>
												<option value="" selected disabled>YYYY</option>
												<?php
												for ($year=2005; $year>1940; $year--){ ?>
													<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
												<?php } ?>
										</select>
										<button class="tipka_potvrdi edit_dtR_unos" id="edit_dtR_unos">OK</button>
									</div>
								</div>
							</div>
						@endif

						{{-- BLOK ZA ADRESU --}}
						@if( $kandidat->kandidat_adresa == null )
							<div class="message zoomIn">
								<div class="message_bot">
									<div class="content_message">
										{{ __('hometext.unesiUlicu') }}
									</div>
								</div>
							</div>
							<div class="message zoomIn" style="display: none;">
								<div class="message_user">
									<div class="content_message">
										<input class="input_izgled" type="text" name="adresa" id="adresa" placeholder = "{{ __('hometext.ulicaBroj') }}">
										<br>
										<button class="tipka_potvrdi unos_adrese" id="unos_adrese" >OK</button>
									</div>
								</div>
							</div>
						@endif
						@if( $kandidat->kandidat_pbroj == null )
							<div class="message zoomIn">
								<div class="message_bot">
									<div class="content_message">
										{{ __('hometext.unesiPBroj') }}
									</div>
								</div>
							</div>
							<div class="message zoomIn" style="display: none;">
								<div class="message_user">
									<div class="content_message">
										<input class="input_izgled" type="text" name="adresa_pbroj" id="adresa_pbroj" placeholder = "{{ __('hometext.pBroj') }}">
										<br>
										<button class="tipka_potvrdi unos_pbroj" id="unos_pbroj" >OK</button>
									</div>
								</div>
							</div>
						@endif
						@if( $kandidat->kandidat_grad == null )
							<div class="message zoomIn">
								<div class="message_bot">
									<div class="content_message">
										{{ __('hometext.gradStanovanja') }}
									</div>
								</div>
							</div>
							<div class="message zoomIn" style="display: none;">
								<div class="message_user">
									<div class="content_message">
										<input class="input_izgled" type="text" name="adresa_grad" id="adresa_grad" placeholder = "{{ __('hometext.Grad') }}">
										<br>
										<button class="tipka_potvrdi unos_grada" id="unos_grada" >OK</button>
									</div>
								</div>
							</div>
						@endif
						@if( $kandidat->kandidat_drzava == null )
							<div class="message zoomIn">
								<div class="message_bot">
									<div class="content_message">
										{{ __('hometext.drzavaStanovanja') }}
									</div>
								</div>
							</div>
							<div class="message zoomIn" style="display: none;">
								<div class="message_user">
									<div class="content_message">
										<input class="input_izgled" type="text" name="adresa_drzava" id="adresa_drzava" placeholder = "{{ __('hometext.drzava') }}">
										<br>
										<button class="tipka_potvrdi unos_drzave" id="unos_drzave" >OK</button>
									</div>
								</div>
							</div>
						@endif
					@endif
					{{-- KRAJ BLOKA ZA NEOBRADJENE --}}

					{{-- BLOK ZA DIPL nekad akcija bila, stavljeno status88 samo da ne pita ljude ovo jer nije vise aktuelna akcija termin u njemackoj za 10 dana --}}
						@if( $kandidat->kandidat_status == 88)
							@if( $kandidat->kandidat_status == 2 or $kandidat->kandidat_status == 7 or $kandidat->kandidat_status == 8)
								@if( Auth::user()->dipl_obavijest == 3 or Auth::user()->dipl_obavijest == 5 )
									<div class="message zoomIn dipl_poc">
										<div class="message_bot">
											<div class="content_message">
												Termin u Njemačkoj ambasadi za 10 dana!
											</div>
										</div>
									</div>
									<div class="message zoomIn">
										<div class="message_bot">
											<div class="content_message">
												Da biste za 10 dana dobili termin za vizu u Njemačkoj ambasadi, na osnovu novog zakona od 01.03.2020. godine, potrebno je da ste nostrificirali Vašu diplomu u Njemačkoj.
											</div>
										</div>
									</div>
									<div class="message zoomIn">
										<div class="message_bot">
											<div class="content_message">
												Da li ste nostrificirali diplomu u Njemačkoj?
											</div>
										</div>
									</div>
									<div class="message zoomIn">
										<div class="message_user">
											<div class="content_message">
												<input type="hidden" name="dipl_kandidat_id" value="{{ $kandidat->kandidat_id }}">
												<button class="tipka_da dipl_da" id="dipl_da" >{{ __('hometext.da') }}</button>
												<button class="tipka_ne dipl_ne" id="dipl_ne" >{{ __('hometext.ne') }}</button>
											</div>
										</div>
									</div>
									<div class="message zoomIn">
										<div class="message_bot">
											<div class="content_message">
												Odlično. Naš tim će vas kontaktirati za više informacija.
											</div>
										</div>
									</div>
									<div class="message zoomIn">
										<div class="message_bot">
											<div class="content_message">
												Naš tim će Vas uskoro kontaktirati za više informacija.
											</div>
										</div>
									</div>
								@endif
							@endif
						@endif
					{{-- BLOK ZA DIPL KRAJ --}}

					{{-- BLOK ZA ISPRAVKU PODATAKA - START --}}
						@if( $kandidat->kandidat_status == 4 or $kandidat->kandidat_status == 5)


							@if(count($pogresniPodaci) == 0)
								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.podaciUObradi') }}
										</div>
									</div>
								</div>
							@else
								<div class="message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.podaciZaKorig') }}
										</div>
									</div>
								</div>
								<div class="message zoomIn ending_loop_question_iskustvo">
                                    <div class="message_user">
                                        <div class="content_message">
                                            <button class="tipka_potvrdi shownext" id="kontrola_dalje">{{ __('hometext.NASTAVI') }}</button>
                                        </div>
                                    </div>
                                </div>


								@if($val_blok_viza->isNotEmpty())
									<div class="message zoomIn" style="display: none;">
										<div class="message_bot">
											<div class="content_message">
												{{ __('hometext.vizaDokad') }}
											</div>
										</div>
									</div>

									<div class="message zoomIn unos_datuma_vize" style="display: none;">
										<div class="message_user">
											<div class="content_message">
												<input class="datumi termin_za_vizu_izgled" type="text" name="datum_vize_val" id="datum_vize_val" placeholder = "{{ __('hometext.odaberiDat') }}" >
												<br>
												<button class="tipka_potvrdi add_datum_vize_val" id="add_datum_vize_val">OK</button>
											</div>
										</div>
									</div>
									<script>
									   $(document).ready(function() {
											jQuery.noConflict();
											$( '.datumi' ).flatpickr({
												dateFormat: "d.m.Y",
												minDate: "today",
												disableMobile: "true"
											});
										});
									</script>
								@endif
								@if($val_blok_termin->isNotEmpty())
									<div class="message zoomIn" style="display: none;">
										<div class="message_bot">
											<div class="content_message">
												{{ __('hometext.terminKad') }}
											</div>
										</div>
									</div>

									<div class="message zoomIn" style="display: none;">
										<div class="message_user">
											<div class="content_message">
												<input class="datumi termin_za_vizu_izgled" type="text" name="datum_termina_val" id="datum_termina_val" placeholder = "{{ __('hometext.odaberiDat') }}" >
												<br>
												<button class="tipka_potvrdi add_datum_termina_val" id="add_datum_termina_val">OK</button>
											</div>
										</div>
									</div>
									<script>
									   $(document).ready(function() {
											jQuery.noConflict();
											$( '.datumi' ).flatpickr({
												dateFormat: "d.m.Y",
												minDate: "today",
												disableMobile: "true"
											});
										});
									</script>
								@endif
								@if($val_blok_apl->isNotEmpty())
									<div class="message zoomIn" style="display: none;">
										<div class="message_bot">
											<div class="content_message">
												{{ __('hometext.terminAplKad') }}
											</div>
										</div>
									</div>
									<div class="message zoomIn unos_datuma" style="display: none;">
										<div class="message_user">
											<div class="content_message">

												<select class="custom-select select_izgled" name="datum_mjesec_val" id="datum_mjesec_val" data-live-search="true" >
														<option value="" selected disabled>{{ __('hometext.mjesec') }}</option>

														@for ($month=1; $month <= 12; $month++)
															<option value="{{ $month }}">{{ $month }}</option>
														@endfor
												</select>
												<select class="custom-select select_izgled" name="datum_god_val" id="datum_god_val" data-live-search="true" >
														<option value="" selected disabled>{{ __('hometext.godina') }}</option>

														@for ($year=date("Y"); $year >= 2015; $year--)
															<option value="{{ $year }}">{{ $year }}</option>
														@endfor
												</select>
												<br>
												<button class="tipka_potvrdi add_datum_apl_val" id="add_datum_apl_val">OK</button>
											</div>
										</div>
									</div>
								@endif
								@if($val_blok_obr->isNotEmpty())
									@foreach($val_blok_obr as $obrazovanje)
										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
												<?php $result = HomeController::getSkolaInfo($obrazovanje->vi_podatak_id);
												?>
													{{ __('hometext.valObr1') }} {{ $result->ke_naziv }}, {{ __('hometext.Smjer') }}  {{ $result->ke_naziv_kvalifikacije }} {{ __('hometext.valObr2') }} @if( Auth::user()->jezik == "de" ) {{ $obrazovanje->ro_naziv_de }} @else {{ $obrazovanje->ro_naziv }} @endif
												</div>
											</div>
										</div>
										@if($obrazovanje->ro_dio_bloka == "obr_skola")
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<input type="hidden" name="kontrola_skola_id" value="{{$obrazovanje->vi_podatak_id}}">
														<input type="hidden" name="kontrola_id_val" value="{{$obrazovanje->vi_id}}">
														<input type="hidden" name="kontrola_naziv_podatka" value="ke_naziv">
														<input class="input_izgled" type="text" name="kontrola_skola" id="kontrola_skola" placeholder = "{{ __('hometext.NazivSkole') }}">
														<button class="tipka_potvrdi edit_skola_naziv_val" id="edit_skola_naziv_val">OK</button>
													</div>
												</div>
											</div>
										@elseif($obrazovanje->ro_dio_bloka == "obr_smjer")
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<input type="hidden" name="kontrola_skola_id" value="{{$obrazovanje->vi_podatak_id}}">
														<input type="hidden" name="kontrola_id_val" value="{{$obrazovanje->vi_id}}">
														<input type="hidden" name="kontrola_naziv_podatka" value="ke_naziv_kvalifikacije">
														<input class="input_izgled" type="text" name="kontrola_skola" id="kontrola_skola_smjer" placeholder = "{{ __('hometext.Smjer') }}">
														<button class="tipka_potvrdi edit_skola_naziv_val" id="edit_skola_smjer_val">OK</button>
													</div>
												</div>
											</div>
										@elseif($obrazovanje->ro_dio_bloka == "obr_datum")
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<input type="hidden" name="kontrola_skola_id" value="{{$obrazovanje->vi_podatak_id}}">
														<input type="hidden" name="kontrola_id_val" value="{{$obrazovanje->vi_id}}">
														<input type="hidden" name="kontrola_naziv_podatka" value="ke_datumdo">
														<select class="custom-select select_izgled datum_skole" name="kontrola_skola" id="kontrola_skola_datum">
															<option value="" selected disabled>{{ __('hometext.godinaZavr') }}</option>
															@for($i=2023; $i>$godinaRodjenja+10; $i--)
																<option value="{{ $i }}">{{ $i }}</option>
															@endfor
														</select>
														<br>
														<button class="tipka_potvrdi edit_skola_naziv_val" id="kontrola_btn_datum">OK</button>
													</div>
												</div>
											</div>
										@elseif($obrazovanje->ro_dio_bloka == "obr_grad")
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<input type="hidden" name="kontrola_skola_id" value="{{$obrazovanje->vi_podatak_id}}">
														<input type="hidden" name="kontrola_id_val" value="{{$obrazovanje->vi_id}}">
														<input type="hidden" name="kontrola_naziv_podatka" value="ke_grad">
														<input class="input_izgled" type="text" name="kontrola_skola" id="kontrola_skola_grad" placeholder = "{{ __('hometext.Grad') }}">
														<button class="tipka_potvrdi edit_skola_naziv_val" id="kontrola_btn_grad">OK</button>
													</div>
												</div>
											</div>
										@endif
									@endforeach
								@endif

								@if($val_blok_isk->isNotEmpty())
									@foreach($val_blok_isk as $iskustvo)
										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
												<?php $resultIskustva = HomeController::getIskustvoInfo($iskustvo->vi_podatak_id);
												?>
													{{ __('hometext.valIsk1') }} {{ $resultIskustva->kri_pozicija }} {{ __('hometext.valIsk2') }} {{ $resultIskustva->kri_naziv }} {{ __('hometext.valIsk3') }} @if( Auth::user()->jezik == "de" ) {{ $iskustvo->ro_naziv_de }} @else {{ $iskustvo->ro_naziv }} @endif
												</div>
											</div>
										</div>
										@if($iskustvo->ro_dio_bloka == "isk_pozicija")
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<input type="hidden" name="kontrola_iskustvo_id" value="{{$iskustvo->vi_podatak_id}}">
														<input type="hidden" name="kontrola_isk_id_val" value="{{$iskustvo->vi_id}}">
														<input type="hidden" name="kontrola_isk_naziv_podatka" value="kri_pozicija">
														<input class="input_izgled" type="text" name="kontrola_iskustvo" id="kontrola_isk_pozicija" placeholder = "{{ __('hometext.Pozicija') }}">
														<button class="tipka_potvrdi edit_iskustvo_val" id="edit_iskustvo_pozicija_val">OK</button>
													</div>
												</div>
											</div>
										@elseif($iskustvo->ro_dio_bloka == "isk_poslodavac")
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<input type="hidden" name="kontrola_iskustvo_id" value="{{$iskustvo->vi_podatak_id}}">
														<input type="hidden" name="kontrola_isk_id_val" value="{{$iskustvo->vi_id}}">
														<input type="hidden" name="kontrola_isk_naziv_podatka" value="kri_naziv">
														<input class="input_izgled" type="text" name="kontrola_iskustvo" id="kontrola_isk_poslodavac" placeholder = "{{ __('hometext.Poslodavac') }}">
														<button class="tipka_potvrdi edit_iskustvo_val" id="edit_iskustvo_poslodavac_val">OK</button>
													</div>
												</div>
											</div>
										@elseif($iskustvo->ro_dio_bloka == "isk_datum_od")
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<input type="hidden" name="kontrola_iskustvo_id" value="{{$iskustvo->vi_podatak_id}}">
														<input type="hidden" name="kontrola_isk_id_val" value="{{$iskustvo->vi_id}}">
														<input type="hidden" name="kontrola_isk_naziv_podatka" value="kri_darum_od">
														<select class="custom-select select_izgled" name="kontrola_iskustvo_mjesec" id="kontrola_iskustvo_mjesec_od" >
															<option value="" selected disabled>{{ __('hometext.mjesec') }}</option>

															@for ($month=1; $month <= 12; $month++)
																<option value="{{ $month }}">{{ $month }}</option>
															@endfor
														</select>
														<select class="custom-select select_izgled" name="kontrola_iskustvo_godina" id="kontrola_iskustvo_godina_od" >
															<option value="" selected disabled>{{ __('hometext.godina') }}</option>

															@for ($year=date("Y"); $year >= $godinaRodjenja+15; $year--)
																<option value="{{ $year }}">{{ $year }}</option>
															@endfor
														</select>
														<br>
														<button class="tipka_potvrdi edit_iskustvo_val" id="kontrola_isk_btn_datum">OK</button>
													</div>
												</div>
											</div>
										@elseif($iskustvo->ro_dio_bloka == "isk_datum_do")
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<input type="hidden" name="kontrola_iskustvo_id" value="{{$iskustvo->vi_podatak_id}}">
														<input type="hidden" name="kontrola_isk_id_val" value="{{$iskustvo->vi_id}}">
														<input type="hidden" name="kontrola_isk_naziv_podatka" value="kri_datum_do">
														<select class="custom-select select_izgled" name="kontrola_iskustvo_mjesec" id="kontrola_iskustvo_mjesec" data-live-search="true" >
															<option value="" selected disabled>{{ __('hometext.mjesec') }}</option>

															@for ($month=1; $month <= 12; $month++)
																<option value="{{ $month }}">{{ $month }}</option>
															@endfor
														</select>
														<select class="custom-select select_izgled" name="kontrola_iskustvo_godina" id="kontrola_iskustvo_godina" data-live-search="true" >
															<option value="" selected disabled>{{ __('hometext.godina') }}</option>

															@for ($year=date("Y"); $year >= $godinaRodjenja+15; $year--)
																<option value="{{ $year }}">{{ $year }}</option>
															@endfor
														</select>
														<br>
														<button class="tipka_potvrdi edit_iskustvo_val" id="kontrola_isk_btn_datum">OK</button>
													</div>
												</div>
											</div>
										@elseif($iskustvo->ro_dio_bloka == "isk_grad")
											<div class="message zoomIn">
												<div class="message_user">
													<div class="content_message">
														<input type="hidden" name="kontrola_iskustvo_id" value="{{$iskustvo->vi_podatak_id}}">
														<input type="hidden" name="kontrola_isk_id_val" value="{{$iskustvo->vi_id}}">
														<input type="hidden" name="kontrola_isk_naziv_podatka" value="kri_grad">
														<input class="input_izgled" type="text" name="kontrola_iskustvo" id="kontrola_isk_grad" placeholder = "{{ __('hometext.Grad') }}">
														<button class="tipka_potvrdi edit_iskustvo_val" id="kontrola_isk_btn_grad">OK</button>
													</div>
												</div>
											</div>
										@endif
									@endforeach
								@endif
								@if($val_blok_doc->isNotEmpty())
									@foreach($val_blok_doc as $dokumenti)
										<div class="message zoomIn">
											<div class="message_bot">
												<div class="content_message">
												<?php $resultDokumenti = HomeController::getDiplomaInfo($dokumenti->vi_podatak_id);
												?>
													{{ __('hometext.valDoc1') }}. {{ __('hometext.valDoc2') }} @if( Auth::user()->jezik == "de" ) {{ $dokumenti->ro_naziv_de }} @else {{ $dokumenti->ro_naziv }} @endif
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input type="hidden" name="kontrola_doc_id" value="{{$dokumenti->vi_podatak_id}}">
													<input type="hidden" name="kontrola_doc_id_val" value="{{$dokumenti->vi_id}}">
													<form method="POST" enctype="multipart/form-data" id="form_docs_diploma_val">
														@csrf
														<input type="hidden" name="document_dataid" value="{{ $dokumenti->vi_kandidat_id }}" />
														<input type="hidden" name="kandidat_id" value="{{ $dokumenti->vi_kandidat_id }}" />
														<input type="hidden" name="kandidat_check" value="0" />
														<input type="hidden" name="document_name" value="{{ $resultDokumenti->document_name }}" />
														<input type="hidden" name="document_desc" value="" />
														<div class="fileinput fileinput-new" data-provides="fileinput">
															<span class="btn btn-default btn-file">
																<span class="fileinput-new">{{ __('hometext.izaberiDoc') }}</span>
																<span class="fileinput-exists">{{ __('hometext.Promijeni') }}</span>
																<input type="file" name="document_file" id="document_file_diploma_val" >
															</span>
															<i class="fa fa-info-circle fa-lg " data-toggle="tooltip" data-placement="right" title="{{ __('hometext.napomena1') }} -{{ __('hometext.napomena2') }} jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
															<span class="fileinput-filename"></span>
															<script>
																$(function (){
																	$('#document_file_diploma_val').change(function (){

																		var f = this.files[0];

																		if (f.size > 20388608 || f.fileSize > 20388608){
																			console.log("to big");
																			this.value = null;
																		}else{
																			console.log("ok");
																		}

																		var ext = $('#document_file_diploma_val').val().split('.').pop().toLowerCase();

																		if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																			console.log("bad ext");
																			this.value = null;
																		}else{
																			console.log("GOOD ext");
																		}
																	})
																});
															</script>
														</div>
													</form>
													<button type="submit" class="tipka_potvrdi" form="form_docs_diploma_val" id="dodaj_docs_diploma_val" >OK</button>
													<script>
														$(document).ready(function(){
															$('#dodaj_docs_diploma_val').on('click',function(e){
																e.preventDefault();
																var kand_id_ajax = $(document).find('input[name="kand_id_ajax"]').val();
																var slika_div = $(this).parent().parent().parent();
																var document_file = slika_div.find('input[name="document_file"]').val();
																var document_dataid = slika_div.find('input[name="document_dataid"]').val();
																console.log(document_dataid);
																console.log(document_file);
																if(document_file == ""){
																	alert("Molimo odaberite dokument");
																}else{

																	var token = $('meta[name="csrf-token"]').attr('content');
																	var kontrola_doc_id = $(this).parent().find('input[name="kontrola_doc_id"]').val();
																	var document_name = $(this).parent().find('input[name="document_name"]').val();
																	var form = $('#form_docs_diploma_val')[0];
																	var data2 = new FormData(form);
																	$.ajax({
																		url: '/messenger/deleteDoc',
																		type: 'POST',
																		data: {"_token": token, 'kontrola_doc_id':kontrola_doc_id, 'document_name': document_name},
																		dataType: 'html',
																		success: function(data) {
																			console.log('uslo');
																			$.ajax({
																				type: "POST",
																				enctype: 'multipart/form-data',
																				url: 'https://crm.job-step.com/public_kandidati?page=add_kandidat_doc_form',
																				data: data2,
																				processData: false,
																				contentType: false,
																				cache: false,
																				success: function(data2) {
																					console.log("OKghfg");
																				}
																			});
																		}
																	});

																	var showNext = $(this).parent().parent().parent().next(".message");
																	showNext.show();
																	showNext.next(".message").delay(500).show(0);
																	if(showNext.hasClass("zadnja_poruka")){
																		var token = $('meta[name="csrf-token"]').attr('content');
																		$.ajax({
																			url: '/messenger/updateStatusObrade',
																			type: 'POST',
																			data: {"_token": token, "status": 4},
																			dataType: 'html',
																			success: function(data) {
																				if(data == 1){
																					$.ajax({
																						url: 'https://crm.job-step.com/public_kandidati?page=checkCandidateInputs',
																						type: 'POST',
																						data: {"kand_id_ajax": kand_id_ajax},
																						success: function(data) {

																						}
																					});
																				}
																			}
																		});
																	}
																	$('html, body').animate({
																		scrollTop: $('div.message_field').offset().top + $("div.message_field")[0].scrollHeight
																	}, 'slow');
																	slika_div.find("#dodaj_docs_diploma_val").prop('disabled', true);
																	slika_div.find("#document_file_diploma_val").prop('disabled', true);
																}
															});
														});
													</script>
												</div>
											</div>
										</div>

									@endforeach
								@endif
								@if($val_blok_ime->isNotEmpty())
									@foreach ($val_blok_ime as $blok_ime)
										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.unesiIme') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="kontrola_podatak" id="kontrola_ime" placeholder = "{{ __('hometext.Ime') }}">
													<input type="hidden" name="kontrola_kolona" value="kandidat_ime">
													<input type="hidden" name="kontrola_vi_id" value="{{ $blok_ime->vi_id}}">
													<button class="tipka_potvrdi edit_info_val" id="edit_ime_val">OK</button>
												</div>
											</div>
										</div>
									@endforeach
								@endif

								@if($val_blok_pre->isNotEmpty())
									@foreach ($val_blok_pre as $blok_pre)
										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.unesiPrezime') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="kontrola_podatak" id="kontrola_pre" placeholder = "{{ __('hometext.Prezime') }}">
													<input type="hidden" name="kontrola_kolona" value="kandidat_prezime">
													<input type="hidden" name="kontrola_vi_id" value="{{ $blok_pre->vi_id}}">
													<button class="tipka_potvrdi edit_info_val" id="edit_pre_val">OK</button>
												</div>
											</div>
										</div>
									@endforeach
								@endif
								@if($val_blok_dtR->isNotEmpty())
									@foreach ($val_blok_dtR as $blok_dtR)
										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.unesiDatumR') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">

													<select class="custom-select select_izgled" name="val_datum_dan" id="val_datum_dan" required>
															<option value="" selected disabled>DD</option>
															<?php
															for ($day=1; $day<=31; $day++){ ?>
																<option value="<?php echo $day; ?>"><?php echo $day; ?></option>
															<?php } ?>
													</select>
													<select class="custom-select select_izgled" name="val_datum_mjesec" id="val_datum_mjesec" required>
															<option value="" selected disabled>MM</option>
															<?php
															for ($month=1; $month<=12; $month++){ ?>
																<option value="<?php echo $month; ?>"><?php echo $month; ?></option>
															<?php } ?>
													</select>
													<select class="custom-select select_izgled" name="val_datum_godina" id="val_datum_mjesec" required>
															<option value="" selected disabled>YYYY</option>
															<?php
															for ($year=2005; $year>1940; $year--){ ?>
																<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
															<?php } ?>
													</select>
													<input type="hidden" name="kontrola_kolona" value="kandidat_datumrodjenja">
													<input type="hidden" name="kontrola_vi_id" value="{{ $blok_dtR->vi_id}}">
													<button class="tipka_potvrdi edit_info_val" id="edit_dtR_val">OK</button>
												</div>
											</div>
										</div>
									@endforeach
								@endif
								@if($val_blok_mjR->isNotEmpty())
									@foreach ($val_blok_mjR as $blok_mjR)
										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.unesiMjestoR') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="kontrola_podatak" id="kontrola_mjR" placeholder = "{{ __('hometext.mjestoRod') }}">
													<input type="hidden" name="kontrola_kolona" value="kandidat_mjestorodjenja">
													<input type="hidden" name="kontrola_vi_id" value="{{ $blok_mjR->vi_id}}">
													<button class="tipka_potvrdi edit_info_val" id="edit_mjR_val">OK</button>
												</div>
											</div>
										</div>
									@endforeach
								@endif
								@if($val_blok_drR->isNotEmpty())
									@foreach ($val_blok_drR as $blok_drR)
										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.unesiDrzavuR') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="kontrola_podatak" id="kontrola_drR" placeholder = "{{ __('hometext.drzavaRod') }}">
													<input type="hidden" name="kontrola_kolona" value="kandidat_drzavarodjenja">
													<input type="hidden" name="kontrola_vi_id" value="{{ $blok_drR->vi_id}}">
													<button class="tipka_potvrdi edit_info_val" id="edit_drR_val">OK</button>
												</div>
											</div>
										</div>
									@endforeach
								@endif
								@if($val_blok_adr->isNotEmpty())
									@foreach ($val_blok_adr as $blok_adr)
										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.unesiUlicu') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="kontrola_podatak" id="kontrola_adr" placeholder = "{{ __('hometext.ulicaBroj') }}">
													<input type="hidden" name="kontrola_kolona" value="kandidat_adresa">
													<input type="hidden" name="kontrola_vi_id" value="{{ $blok_adr->vi_id}}">
													<button class="tipka_potvrdi edit_info_val" id="edit_adr_val">OK</button>
												</div>
											</div>
										</div>
									@endforeach
								@endif
								@if($val_blok_gra->isNotEmpty())
									@foreach ($val_blok_gra as $blok_gra)
										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.gradStanovanja') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="kontrola_podatak" id="kontrola_gra" placeholder = "{{ __('hometext.Grad') }}">
													<input type="hidden" name="kontrola_kolona" value="kandidat_grad">
													<input type="hidden" name="kontrola_vi_id" value="{{ $blok_gra->vi_id}}">
													<button class="tipka_potvrdi edit_info_val" id="edit_gra_val">OK</button>
												</div>
											</div>
										</div>
									@endforeach
								@endif
								@if($val_blok_pbr->isNotEmpty())
									@foreach ($val_blok_pbr as $blok_pbr)
										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.unesiPBroj') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="kontrola_podatak" id="kontrola_pbr" placeholder = "{{ __('hometext.pBroj') }}">
													<input type="hidden" name="kontrola_kolona" value="kandidat_pbroj">
													<input type="hidden" name="kontrola_vi_id" value="{{ $blok_pbr->vi_id}}">
													<button class="tipka_potvrdi edit_info_val" id="edit_pbr_val">OK</button>
												</div>
											</div>
										</div>
									@endforeach
								@endif
								@if($val_blok_drz->isNotEmpty())
									@foreach ($val_blok_drz as $blok_drz)
										<div class="message zoomIn" style="display: none;">
											<div class="message_bot">
												<div class="content_message">
													{{ __('hometext.drzavaStanovanja') }}
												</div>
											</div>
										</div>
										<div class="message zoomIn">
											<div class="message_user">
												<div class="content_message">
													<input class="input_izgled" type="text" name="kontrola_podatak" id="kontrola_drz" placeholder = "{{ __('hometext.drzava') }}">
													<input type="hidden" name="kontrola_kolona" value="kandidat_drzava">
													<input type="hidden" name="kontrola_vi_id" value="{{ $blok_drz->vi_id}}">
													<button class="tipka_potvrdi edit_info_val" id="edit_drz_val">OK</button>
												</div>
											</div>
										</div>
									@endforeach
								@endif

								<div class="zadnja_poruka message zoomIn">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.hvalaKraj1') }}  {{ __('hometext.hvalaKraj2') }}  {{ __('hometext.hvalaKraj3') }}<br> {{ __('hometext.hvalaKraj4') }} .
										</div>
									</div>
								</div>
							@endif
						@elseif( $kandidat->kandidat_status == 2 AND $kandidat->kandidat_id == 11268 AND $brojObavijesti > 0)
							{{-- POKUŠAJ KREIRANJA BLOKA ZA NOVE OGLASE - AKO SE NASTAVI DEFINITVNO NE BI SMIO BITI OVDJE NEGO VAN IF-a OD ISPRAVKE PODATAKA --}}
							<input type="hidden" name="nalogOglas_id" value="{{ $nalogObavijesti->bo_nalog_id }}">
							<div class="message zoomIn oglas_poc" style="display: none;">
								<div class="message_bot">
									<div class="content_message">
										{{ __('hometext.noviOglas') }}: {{ $oglasNalog->no_nalognaziv}}
									</div>
								</div>
							</div>
							<div class="message zoomIn" style="display: none;">
								<div class="message_bot">
									<div class="content_message">
										{{ __('hometext.zainteresirani') }} {{ $nalogBlokovi->nbp_njemacki_jezik }}
									</div>
								</div>
							</div>
							<div class="message zoomIn ">
								<div class="message_user">
									<div class="content_message">
										<button class="tipka_da nOglasDa" id="nOglasDa" >{{ __('hometext.da') }}</button>
										<button class="tipka_ne nOglasNe" id="nOglasNe" >{{ __('hometext.ne') }}</button>
									</div>
								</div>
							</div>
							@switch($oglasKriteriji->nbp_njemacki_jezik)
								@case ("A1")
									@php $njem_uslov_novi = array('C1','C2','B2','B1','A2','A1'); @endphp
								@break
								@case ("A2")
									@php $njem_uslov_novi = array('C1','C2','B2','B1','A2'); @endphp
								@break
								@case ("B1")
									@php $njem_uslov_novi = array('C1','C2','B2','B1'); @endphp
								@break
								@case ("B2")
									@php $njem_uslov_novi = array('C1','C2','B2'); @endphp
								@break
								@case ("C1")
									@php $njem_uslov_novi = array('C1','C2'); @endphp
								@break
								@case ("C2")
									@php $njem_uslov_novi = array('C2'); @endphp
								@break
								@default
									@php $njem_uslov_novi = array(); @endphp
							@endswitch

							@if(in_array($kandidatJezik->kj_slusanje, $njem_uslov_novi))
							@else
								<div class="message zoomIn" style="display: none;">
									<div class="message_bot">
										<div class="content_message">
											Da li ste poboljšali svoje znanje Njemačkog jezika sa {{ $kandidatJezik->kj_slusanje }}
										</div>
									</div>
								</div>
								<div class="message zoomIn ">
									<div class="message_user">
										<div class="content_message">
											<button class="tipka_da upgJezikDa" id="upgJezikDa" >{{ __('hometext.da') }}</button>
											<button class="tipka_ne upgJezikNe" id="upgJezikNe" >{{ __('hometext.ne') }}</button>
										</div>
									</div>
								</div>
								<div class="message zoomIn" style="display: none;">
									<div class="message_bot">
										<div class="content_message">
											 {{ __('hometext.poznNjem') }}
										</div>
									</div>
								</div>
								<div class="message zoomIn">
									<div class="message_user">
										<div class="content_message">
											<div class="inputGroup">
												<input id="optionNovi1" name="option_novi_jezik" type="radio" value="A1"/>
												<label for="optionNovi1">A1</label>
											</div>

											<div class="inputGroup">
												<input id="optionNovi2" name="option_novi_jezik" type="radio" value="A2"/>
												<label for="optionNovi2">A2</label>
											</div>
											<div class="inputGroup">
												<input id="optionNovi3" name="option_novi_jezik" type="radio" value="B1"/>
												<label for="optionNovi3">B1</label>
											</div>

											<div class="inputGroup">
												<input id="optionNovi4" name="option_novi_jezik" type="radio" value="B2"/>
												<label for="optionNovi4">B2</label>
											</div>
											<div class="inputGroup">
												<input id="optionNovi5" name="option_novi_jezik" type="radio" value="C1"/>
												<label for="optionNovi5">C1</label>
											</div>

											<div class="inputGroup">
												<input id="optionNovi6" name="option_novi_jezik" type="radio" value="C2"/>
												<label for="optionNovi6">C2</label>
											</div>

											<div class="inputGroup">
												<input id="optionNovi7" name="option_novi_jezik" type="radio" value="{{ __('hometext.bezZnanja') }}"/>
												<label for="optionNovi7">{{ __('hometext.bezZnanja') }}</label>
											</div>

											<button class="njem_jezik_edit_novi tipka_potvrdi" id="njem_jezik_edit_novi"> OK</button>
										</div>
									</div>
								</div>
							@endif
							@if($oglasKriteriji->nbp_vozacka == "block" AND ($kandidat->kandidat_vozacka_dozvola != "Da"))
								<div class="message zoomIn" style="display: none;">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.vozackaQ') }}
										</div>
									</div>
								</div>
								<div class="message zoomIn ">
									<div class="message_user">
										<div class="content_message">
											<button class="tipka_da upgVozackaDa" id="upgVozackaDa" >{{ __('hometext.da') }}</button>
											<button class="tipka_ne upgVozackaNe" id="upgVozackaNe" >{{ __('hometext.ne') }}</button>
										</div>
									</div>
								</div>
							@endif
							<div class="zadnja_poruka message zoomIn kraj_novog_oglasa_poz">
								<div class="message_bot">
									<div class="content_message">
										{{ __('hometext.hvalaKraj1') }}  {{ __('hometext.hvalaKraj2') }}  {{ __('hometext.hvalaKraj3') }}<br> {{ __('hometext.hvalaKraj4') }} .
									</div>
								</div>
							</div>
							<div class="zadnja_poruka message zoomIn kraj_novog_oglasa_neg">
								<div class="message_bot">
									<div class="content_message">
										{{ __('hometext.hvalaKraj1') }} <br> {{ __('hometext.hvalaKraj4') }} .
									</div>
								</div>
							</div>


						@else
							@if( Auth::user()->notf_new_year == 1 )
								<div class="zadnja_poruka message zoomIn notf_new_year">
									<div class="message_bot">
										<div class="content_message">
											{{ __('hometext.sretnaNova') }}
										</div>
									</div>
								</div>
							@else
							<div class="zadnja_poruka message zoomIn">
								<div class="message_bot">
									<div class="content_message">
									   {{ __('hometext.hvalaKraj1') }}  {{ __('hometext.hvalaKraj2') }}  {{ __('hometext.hvalaKraj3') }} <br> {{ __('hometext.hvalaKraj4') }} .
									</div>
								</div>
							</div>
							@endif
						@endif
					{{-- BLOK ZA ISPRAVKU PODATAKA - END --}}

                        {{-- <p>{{ $nalogBlokovi->nbp_email }}</p>
                        <p>{{ $nalogBlokovi->nbp_vozacka }}</p>
                        <p>{{ $nalogBlokovi->nbp_korak2 }}</p>
                        <p>{{ $nalogBlokovi->nbp_korak3 }}</p>
                        <p>{{ $nalogBlokovi->nbp_visoko_obr }}</p>
                        <p>{{ $nalogBlokovi->nbp_korak4 }}</p>
                        <p>{{ $nalogBlokovi->nbp_korak5 }}</p>
                        <p>{{ $nalogBlokovi->nbp_ostali_jezici }}</p>
                        <p>{{ $nalogBlokovi->nbp_korak7 }}</p> --}}
                    </div>
                    {{-- <div class="row">
                    @if(count($kandidatIskustva) > 0)
                        @foreach($kandidatIskustva as $kandidatIskustvo)
                            <p>Naziv: {{$kandidatIskustvo->kri_pozicija}}
                            Kvalifikacija: {{$kandidatIskustvo->kri_naziv}} </p><hr/>
                        @endforeach
                    @endif
                    </div> --}}


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
