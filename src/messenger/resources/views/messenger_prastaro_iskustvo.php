//NEKAD SE KORISTILO KAD NIJE BILO LOOPA
							$(document).on('click', '.kraj_iskustva', function () {
								var cloningDiv = $(this).parent().parent().parent().parent();
								
								iskustvo_opis =  $(this).parent().find('input[name="opis_iskustvo"]').val();
								//console.log(iskustvo_opis);
								if(iskustvo_opis == "")
									alert("Molimo unesite opis!")
								else{
									
									var trenutniDiv_isk = $(this).parent().parent().parent();
									// var iskustvo_opis = trenutniDiv_isk.find('input[name="opis_iskustvo"]').val();
									
									var gradDiv_isk = trenutniDiv_isk.prev(".message").prev(".message");
									var iskustvo_grad = gradDiv_isk.find('input[name="grad_iskustvo"]').val();
									
									var datumDoDiv = gradDiv_isk.prev(".message").prev(".message");
									
									var aktuelno_check = cloningDiv.find('input[name="kri_datum_do_aktuelno"]');
									if (aktuelno_check.is(':checked')){
										/*var today = new Date();
										var isk_datum_do_mjesec = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
										var isk_datum_do_god = today.getFullYear();*/
										// console.log("chekirano");
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
									
									// console.log(iskustvo_opis);
									// console.log(iskustvo_grad);
									// console.log(datum_iskustvo_do); 
									// console.log(datum_iskustvo_od);
									// console.log(iskustvo_poslodavac);
									// console.log(iskustvo_pozicija);
									// console.log(aktuelno);
									
									var token = $('meta[name="csrf-token"]').attr('content');
									$.ajax({
										url: '/messenger/addIskustvo',
										type: 'POST',
										data: {	"_token": token,
												"iskustvo_pozicija": iskustvo_pozicija,
												"iskustvo_poslodavac": iskustvo_poslodavac,
												"datum_iskustvo_od": datum_iskustvo_od,
												"datum_iskustvo_do": datum_iskustvo_do,
												"iskustvo_grad": iskustvo_grad,
												"iskustvo_opis": iskustvo_opis,
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
									$(cloningDiv).find("#kraj_iskustva").prop('disabled', true);
									$(cloningDiv).find("#opis_iskustvo").prop('disabled', true);
								}
								
							});