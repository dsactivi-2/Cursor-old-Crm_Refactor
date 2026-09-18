<?php 

?>
<!-- Dugme na osnovu uslova START -->
<div class="row pt-3">
    <div class="col-12">
        <div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
            <div class = "row">
                <div class = "col-12">
                    <p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Historija dokumenata"][$languageUser];?></p>
                </div>
            </div>
            <div class="row pt-3">
                <div class="col-12 text-center">
                <button 
                    type="button" 
                    class="btn btn-light"
                    data-bs-toggle="modal"
                    data-bs-target="#documentHistory"
                >
                    <i class="fa fa-info-circle me-1" aria-hidden="true"></i>
                    <span><?php echo $txtArray["Pogledajte detalje"][$languageUser];?></span>
                </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Dugme na osnovu uslova END -->

<script>
    function enablePopoversAndTooltipsDH(){
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    };
    $(document).ready(function() {
        $('.documentDetailsHistory').hide(function(){
            $('.documentDetailsHistoryBody').html("");
        });
    });
    /*function clearDocumentDetailsHistory(){
        $('.documentDetailsHistory').hide('fade', 250, function(){
            $(".documentDetailsHistoryBody").html("");
        });
    };*/
</script>

<!-- Modal START -->
<div class="modal fade scrollBarVertical" id="documentHistory" aria-hidden="true" aria-labelledby="documentHistoryLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom-0 text-center">
                <h5 class="modal-title w-100" id="documentHistoryLabel"><?php echo $txtArray["Historija dokumenata"][$languageUser]; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class = "row h-100">
                    <div class = "col-6 text-center h-100 overflow-auto scrollBarVertical">
                        <div class="row">
							<div class="col-12">
								<div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
									<div class = "row">
										<div class = "col-12">
											<p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Potrebna dokumentacija"][$languageUser]; ?></p>
										</div>
									</div>
                                    <div class = "row pt-3">
                                        <div class = "col-12 scrollBarHorizontal" style="overflow-y: auto !important; user-select: none;">
                                            <?php 
                                                $countDocumentsH = getCountDocuments($candidateNalog, $kandidat_id);

                                                if($countDocumentsH != 0){
                                            ?>
                                            <script>
                                                $(document).ready(function() {
                                                    $('#requiredDocumentHistory').DataTable({
                                                        responsive: false,
                                                        "order": [[ 0, "desc" ]],
                                                        "bAutoWidth": false,
                                                        "bPaginate" : false,
                                                        "bLengthChange": false,
                                                        "bInfo": false,
                                                        "bFilter": false,
                                                        "aoColumns": [
                                                            { "width": "30%"},
                                                            { "width": "7.5%", "bSortable": false },
                                                            { "width": "25%", "bSortable": false },
                                                            { "width": "12.5%", "bSortable": false },
                                                            { "width": "12.5%", "bSortable": false },
                                                            { "width": "12.5%", "bSortable": false }
                                                        ]
                                                    });
                                                    enablePopoversAndTooltipsDH(); 
                                                    $('.simClickClass').trigger('click'); // Za prvi u listi uzmi njegove detalje
                                                });
                                            </script>
                                            <!-- TABLE DOCUMENT HISTORI -->
                                            <table id="requiredDocumentHistory" class="display" cellspacing="0" width = "100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center"><?php echo $txtArray["Naziv dokumenta"][$languageUser]; ?></th>
                                                        <th class="text-center"><?php echo $txtArray["Pregled dokumenta"][$languageUser]; ?></th>
                                                        <th class="text-center"><?php echo $txtArray["Status dokumenta"][$languageUser]; ?></th>
                                                        <th class="text-center"><?php echo $txtArray["Datum"][$languageUser]; ?></th>
                                                        <th class="text-center"><?php echo $txtArray["Korisnik"][$languageUser]; ?></th>
                                                        <th class="text-center"><?php echo $txtArray["Detalji"][$languageUser]; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                        if($kandidat_id != 0 AND $candidateNalog != 0){
                                                            //RD - requered documents
                                                            $nacinOdlaskaRDH = getCandidateNacinOdlaska($kandidat_id);
                                                            $potpunaNostrifikacija = getCandidateFullRecognition($kandidat_id);
                                                            $uslovRDH = "";
                                                            if ( $nacinOdlaskaRDH == 2 OR $potpunaNostrifikacija == 1 OR $potpunaNostrifikacija == 2) {
                                                                $uslovRDH = "AND nrd.nrd_west_balkan = 1";
                                                            } else if ( $nacinOdlaskaRDH == 3 ) {
                                                                $uslovRDH = "AND nrd.nrd_work_experience = 1";
                                                            } else if ( $nacinOdlaskaRDH == 0 ) {
                                                                $uslovRDH = "AND nrd.nrd_skilled_candidates = 1";
                                                            }
                                                            $queryRDH = $db->prepare("
                                                                SELECT 
                                                                    dt.doc_type_id, dt.doc_type_name, dt.doc_type_name_de, nrd.nrd_id, nrd.nrd_comment, d.doc_id, d.doc_file_name, d.doc_status, ds.ds_name_pp, ds.ds_name_de_pp, dsl.dsl_date, dsl.dsl_user_id, dsl.dsl_pp_user_id, dsl.dsl_comment
                                                                FROM 
                                                                    idk_pp_document_types dt
                                                                INNER JOIN 
                                                                    idk_pp_nalog_required_documents nrd
                                                                ON 
                                                                    dt.doc_type_id = nrd.nrd_type_id AND nrd.nrd_done_by = 1 AND nrd.nrd_nalog_id = :nalogId AND nrd.nrd_status = 1 ".$uslovRDH."
                                                                LEFT JOIN 
                                                                    idk_pp_documents d 
                                                                ON 
                                                                    d.doc_nrd_id = nrd.nrd_id AND d.doc_candidate_id = :candidateId AND d.doc_crd_id is null
                                                                LEFT JOIN 
                                                                    idk_pp_documents_statuses ds 
                                                                ON 
                                                                    ds.ds_id = d.doc_status
                                                                LEFT JOIN 
                                                                    idk_pp_documents_status_logs dsl
                                                                ON 
                                                                    dsl.dsl_doc_id = d.doc_id AND dsl.dsl_days_count is null
                                                                ORDER BY 
                                                                    nrd.nrd_id 
                                                                ASC
                                                            ");
                                                            $queryRDH->execute(array(
                                                                ':nalogId' => $candidateNalog,
                                                                ':candidateId' => $kandidat_id
                                                            ));
                                                            if($queryRDH->rowCount() != 0){
                                                                $cntRDH = 0;
                                                                while($rowRDH = $queryRDH->fetch()){
                                                                    $cntRDH++;
                                                                    $simulateClass = "";
                                                                    if($cntRDH == 1){
                                                                        $simulateClass = "simClickClass";
                                                                    }
                                                                    $details = "";
                                                                    $dsl_user = "";
                                                                    $doc_file = "";
                                                                    $commentNRD = "";
                                                                    //START
                                                                    $doc_type_id = intval($rowRDH["doc_type_id"]);
                                                                    if($languageUser == 1){
                                                                        $doc_type_name = $rowRDH["doc_type_name_de"];
                                                                    }else{
                                                                        $doc_type_name = $rowRDH["doc_type_name"];
                                                                    }
                                                                    $nrd_id = intval($rowRDH["nrd_id"]);
                                                                    $nrd_comment = $rowRDH["nrd_comment"];
                                                                    if($nrd_comment != NULL){
                                                                        $commentNRD = '<i class="fa fa-comment text-success blinkingAnimation fa-2x" role="status" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$nrd_comment.'!" aria-hidden="true"></i>';
                                                                    }else{
                                                                        $commentNRD = '<i class="fa fa-comment text-danger fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Komentar nije napisan"][$languageUser].'!" aria-hidden="true"></i>';
                                                                    }
                                                                    $doc_id = intval($rowRDH["doc_id"]); 
                                                                    $doc_file_name = $rowRDH["doc_file_name"];
                                                                    if($doc_file_name != NULL){
                                                                        $doc_file = '<a href="'.$doc_file_name.'" target="_blank"><button type="button" class="btn btn-success"><i class="fa fa-file" aria-hidden="true"></i></button></a>';
                                                                    }else{
                                                                        $doc_file = '<button type="button" class="btn btn-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Dokument nije uploadan"][$languageUser].'"><i class="fa fa-file" aria-hidden="true"></button>';
                                                                    }
                                                                    $doc_status = intval($rowRDH["doc_status"]);
                                                                    if($languageUser == 1){
                                                                        $ds_name = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($doc_status).'>'.$rowRDH["ds_name_de_pp"].'</span>';
                                                                    }else{
                                                                        $ds_name = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($doc_status).'>'.$rowRDH["ds_name_pp"].'</span>';
                                                                    }
                                                                    $dsl_date = date("d.m.Y", strtotime($rowRDH["dsl_date"]));
                                                                    $dsl_user_id = intval($rowRDH["dsl_user_id"]);
                                                                    $dsl_pp_user_id = intval($rowRDH["dsl_pp_user_id"]);
                                                                    if($dsl_user_id == 0 AND $dsl_pp_user_id == 0){
                                                                        $dsl_user = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
                                                                    }else if($dsl_user_id != 0 AND $dsl_pp_user_id == 0){
                                                                        $dsl_user = '<i class="fa fa-user-circle fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.getEmployeeFullNameById($dsl_user_id).'" aria-hidden="true"></i>';
                                                                    }else if($dsl_user_id == 0 AND $dsl_pp_user_id != 0){
                                                                        $dsl_user = '<i class="fa fa-user-circle fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.getFirstAndLastNameUserR($dsl_pp_user_id).'" aria-hidden="true"></i>';
                                                                    }else{
                                                                        $dsl_user = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
                                                                    } 
                                                                    $dsl_comment = $rowRDH["dsl_comment"];

                                                                    $details = '
                                                                        <button 
                                                                            type="button" 
                                                                            class="btn btn-info '.$simulateClass.'"
                                                                            onclick="documentDetailsHistory(this)" 
                                                                            data-nrd_id = "'.$nrd_id.'"
                                                                            data-candidat_id = "'.$kandidat_id.'"
                                                                            data-nalog_id = "'.$candidateNalog.'"
                                                                            data-doc_id = "'.$doc_id.'" 
                                                                        >
                                                                            <i class="fa fa-info-circle " aria-hidden="true"></i>
                                                                        </button>
                                                                    ';
                                                                    //END
                                                                    //Ispis
                                                                    if($doc_status == 0){
                                                                        echo '
                                                                            <tr>
                                                                                <td class="text-center">'.$doc_type_name.'</td>
                                                                                <td colspan="4" class="text-center"><span class="badge rounded-pill bg-danger fs-5">'.$txtArray["Dokument nije uploadan"][$languageUser].'</span></td>
                                                                                <td style="display: none;"></td>
                                                                                <td style="display: none;"></td>
                                                                                <td style="display: none;"></td>
                                                                                <td class="text-center">'.$commentNRD.'</td>
                                                                            </tr>
                                                                        ';
                                                                    }else{
                                                                        echo '
                                                                            <tr>
                                                                                <td class="text-center">'.$doc_type_name.'</td>
                                                                                <td class="text-center">'.$doc_file.'</td>
                                                                                <td class="text-center">'.$ds_name.'</td>
                                                                                <td class="text-center">'.$dsl_date.'</td>
                                                                                <td class="text-center">'.$dsl_user.'</td>
                                                                                <td class="text-center">'.$details.'</td>
                                                                            </tr>
                                                                        ';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                            <!-- TABLE DOCUMENT HISTORY -->

                                            <!-- MODAL ZA PREGLED DETALJA O DOKUMENTU -->
                                            <script>
                                                function documentDetailsHistory(thisRow) {
                                                    //Ovdje ce ici provjera za remindere nekad kad budu bili
                                                    //clearDocumentDetailsHistory();
                                                    var nrd_id = parseInt($(thisRow).data("nrd_id"));
                                                    var doc_id = parseInt($(thisRow).data("doc_id"));
                                                    var candidat_id = parseInt($(thisRow).data("candidat_id"));
                                                    var nalog_id = parseInt($(thisRow).data("nalog_id"));
                                                    
                                                    //console.log(nrd_id+ " " + doc_id + " " + candidat_id + " " + nalog_id);

                                                    if(nrd_id != 0 && doc_id != 0 && candidat_id != 0 && nalog_id != 0){
                                                        $.ajax({
                                                            url: 'ajax.php?action=documentDetails',
                                                            type: 'POST',
                                                            dataType: 'html',
                                                            data:{
                                                                'nrd_id': nrd_id,
                                                                'doc_id': doc_id,
                                                                'candidat_id': candidat_id,
                                                                'nalog_id': nalog_id,
                                                            },
                                                            success : function (result){
                                                                $('.documentDetailsHistory').hide('fade', 250, function(){
                                                                    $(".documentDetailsHistoryBody").html("");
                                                                    $(".documentDetailsHistoryBody").html(result);
                                                                    enablePopoversAndTooltipsDH();
                                                                    if(result != ""){
                                                                        $('.documentDetailsHistory').show('fade', 500);
                                                                    }
                                                                });
                                                            },
                                                            error: function (xhr, ajaxOptions, thrownError) {
                                                                alert(xhr.status);
                                                                alert(thrownError);
                                                            }
                                                        });
                                                    }
                                                    
                                                };
                                            </script>
                                            <!-- MODAL ZA PREGLED DETALJA O DOKUMENTU -->
                                            <?php 
                                                }else{
                                            ?>
                                            <div class="alert alert-warning text-center" role="alert">
												<?php echo $txtArray["U procesu prikupljanja dokumenata niste zaduženi ni za jedan dokument"][$languageUser]."!"; ?>
											</div>
                                            <?php 
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            $queryVisaIncompleteH = $db->prepare("
                                SELECT 
                                    vi.vi_id, vi.vi_type
                                FROM 
                                    idk_pp_visa_incomplete vi
                                WHERE 
                                    vi.vi_cand_id = :vi_cand_id
                                    AND 
                                    vi.vi_status = 0
                                    AND 
                                    vi.vi_nalog_id = :vi_nalog_id
                            ");
                            $queryVisaIncompleteH->execute(array(
                                ':vi_cand_id' => $kandidat_id,
                                ':vi_nalog_id' => $candidateNalog
                            ));
                            if($queryVisaIncompleteH->rowCount() != 0){
                                while($rowVisaIncompleteH = $queryVisaIncompleteH->fetch()){
                                    //VIH - Visa incomplete History
                                    $idVIH = intval($rowVisaIncompleteH["vi_id"]);
                                    $typeVIH = intval($rowVisaIncompleteH["vi_type"]);
                                    if($typeVIH == 1){
                                        $titleVIH = $txtArray["Dopuna"][$languageUser];
                                    }else{
                                        $titleVIH = $txtArray["Odbijenica"][$languageUser];
                                    }
                                    $detailsVIH = getDetailsForVisaIncompleteArrayR($idVIH);
                        ?>
                        <div class="row pt-3">
                            <div class="col-12">
                                <div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
                                    <div class = "row">
                                        <div class = "col-12">
                                            <p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $titleVIH; ?></p>
                                        </div>
                                    </div>
                                    <div class = "row pt-3">
                                        <div class = "col-12 scrollBarHorizontal" style="overflow-y: auto !important; user-select: none;">
                                            <div class="row">
												<div class="col-lg-12">
													<?php 
														$flagDocumentsVIH = 0;
														if($detailsVIH["vi_type"][0] == 1 OR ($detailsVIH["vi_type"][0] == 2 AND $detailsVIH["vi_complaint"][0] == 1)){
															$flagDocumentsVIH = 1;
													?> 
													<table class="table table-borderless">
														<thead>
															<tr>
																<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Zaprimio"][$languageUser];?></p></th>
																<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Datum prijema"][$languageUser];?></p></th>
																<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Krajnji datum"][$languageUser];?></p></th>
															</tr>
														</thead>
														<tbody>
															<?php 
																if($detailsVIH["vi_date_received_candidate"][0] != NULL AND $detailsVIH["vi_deadline_date_candidate"][0] != NULL){
															?>
															<tr>
																<td class="text-center pt-0"><span class="badge bg-secondary"><?php echo $txtArray["Kandidat"][$languageUser];?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y H:i", strtotime($detailsVIH["vi_date_received_candidate"][0]));?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y", strtotime($detailsVIH["vi_deadline_date_candidate"][0]));?></span></td>
															</tr>
															<?php 
																}
																if($detailsVIH["vi_date_received_employer"][0] != NULL AND $detailsVIH["vi_deadline_date_employer"][0] != NULL){
															?>
															<tr>
																<td class="text-center pt-0"><span class="badge bg-secondary"><?php echo $txtArray["Poslodavac"][$languageUser];?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y H:i", strtotime($detailsVIH["vi_date_received_employer"][0]));?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y", strtotime($detailsVIH["vi_deadline_date_employer"][0]));?></span></td>
															</tr>
															<?php 
																}
															?>
														</tbody>
													</table>
													<?php 
														}else{
															$flagDocumentsVIH = 0;
													?>
													<table class="table table-borderless">
														<thead>
															<tr>
																<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Zaprimio"][$languageUser];?></p></th>
																<th class="text-center"><p class="mb-0" style="color: #B0B4B7; font-style: normal; font-weight: normal; font-size: 16px;"><?php echo $txtArray["Datum prijema"][$languageUser];?></p></th>
															</tr>
														</thead>
														<tbody>
															<?php 
																if($detailsVIH["vi_date_received_candidate"][0] != NULL){
															?>
															<tr>
																<td class="text-center pt-0"><span class="badge bg-secondary"><?php echo $txtArray["Kandidat"][$languageUser];?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y H:i", strtotime($detailsVIH["vi_date_received_candidate"][0]));?></span></td>
															</tr>
															<?php 
																}
																if($detailsVIH["vi_date_received_employer"][0] != NULL){
															?>
															<tr>
																<td class="text-center pt-0"><span class="badge bg-secondary"><?php echo $txtArray["Poslodavac"][$languageUser];?></span></td>
																<td class="text-center pt-0"><span class="badge bg-light text-dark"><?php echo date("d.m.Y H:i", strtotime($detailsVIH["vi_date_received_employer"][0]));?></span></td>
															</tr>
															<?php 
																}
															?>
														</tbody>
													</table>
													<?php 
														}
													?>
												</div>
											</div>
                                            <?php 
												if($flagDocumentsVIH == 1){
											?>
											<hr class="mb-0 mt-0">
											<div class="row">
												<div class="col-lg-12">
                                                    <?php 
                                                        $countDocVisaIncompleteH = getCountDocumentsVisaIncomplete($kandidat_id, $candidateNalog, $idVIH);
                                                        if($countDocVisaIncompleteH != 0){
                                                    ?>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('#visaIncompleteDocumentsHistory<?php echo $idVIH; ?>').DataTable({
                                                                responsive: false,
                                                                "order": [[ 0, "desc" ]],
                                                                "bAutoWidth": false,
                                                                "bPaginate" : false,
                                                                "bLengthChange": false,
                                                                "bInfo": false,
                                                                "bFilter": false,
                                                                "aoColumns": [
                                                                    { "width": "30%"},
                                                                    { "width": "7.5%", "bSortable": false },
                                                                    { "width": "25%", "bSortable": false },
                                                                    { "width": "12.5%", "bSortable": false },
                                                                    { "width": "12.5%", "bSortable": false },
                                                                    { "width": "12.5%", "bSortable": false }
                                                                ]
                                                            });
                                                            enablePopoversAndTooltipsDH();
                                                        });
                                                    </script>
													<table id="visaIncompleteDocumentsHistory<?php echo $idVIH; ?>" class="display" cellspacing="0" width = "100%">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center"><?php echo $txtArray["Naziv dokumenta"][$languageUser];?></th>
                                                                <th class="text-center"><?php echo $txtArray["Pregled dokumenta"][$languageUser];?></th>
                                                                <th class="text-center"><?php echo $txtArray["Status dokumenta"][$languageUser];?></th>
                                                                <th class="text-center"><?php echo $txtArray["Datum"][$languageUser];?></th>
                                                                <th class="text-center"><?php echo $txtArray["Korisnik"][$languageUser];?></th>
                                                                <th class="text-center"><?php echo $txtArray["Detalji"][$languageUser];?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php 
                                                            if($kandidat_id != 0 AND $candidateNalog != 0 AND $idVIH != 0){
                                                                $queryVIH = $db->prepare("
                                                                    SELECT 
                                                                        dt.doc_type_id, dt.doc_type_name, dt.doc_type_name_de, crd.crd_id, crd.crd_comment, d.doc_id, d.doc_file_name, d.doc_status, ds.ds_name_pp, ds.ds_name_de_pp, dsl.dsl_date, dsl.dsl_user_id, dsl.dsl_pp_user_id, dsl.dsl_comment
                                                                    FROM 
                                                                        idk_pp_document_types dt
                                                                    INNER JOIN 
                                                                        idk_pp_cand_required_documents crd
                                                                    ON 
                                                                        dt.doc_type_id = crd.crd_type_id AND crd.crd_done_by = 1 AND crd.crd_nalog_id = :nalogId AND crd.crd_vi_id = :viId
                                                                    LEFT JOIN 
                                                                        idk_pp_documents d 
                                                                    ON 
                                                                        d.doc_crd_id = crd.crd_id AND d.doc_candidate_id = :candidateId AND d.doc_nrd_id is null
                                                                    LEFT JOIN 
                                                                        idk_pp_documents_statuses ds 
                                                                    ON 
                                                                        ds.ds_id = d.doc_status
                                                                    LEFT JOIN 
                                                                        idk_pp_documents_status_logs dsl
                                                                    ON 
                                                                        dsl.dsl_doc_id = d.doc_id AND dsl.dsl_days_count is null
                                                                    ORDER BY 
                                                                        crd.crd_id 
                                                                    ASC
                                                                ");
                                                                $queryVIH->execute(array(
                                                                    ':nalogId' => $candidateNalog,
                                                                    ':candidateId' => $kandidat_id,
                                                                    ':viId' => $idVIH
                                                                ));
                                                                if($queryVIH->rowCount() != 0){
                                                                    while($rowVIH = $queryVIH->fetch()){
                                                                        $detailsVIH = "";
                                                                        $dsl_userVIH = "";
                                                                        $doc_fileVIH = "";
                                                                        $commentCRDVIH = "";
                                                                        $doc_type_idVIH = intval($rowVIH["doc_type_id"]);
                                                                        if($languageUser == 1){
                                                                            $doc_type_nameVIH = $rowVIH["doc_type_name_de"];
                                                                        }else{
                                                                            $doc_type_nameVIH = $rowVIH["doc_type_name"];
                                                                        }
                                                                        $crd_idVIH = intval($rowVIH["crd_id"]);
                                                                        $crd_commentVIH = $rowVIH["crd_comment"];
                                                                        if($crd_commentVIH != NULL){
                                                                            $commentCRDVIH = '<i class="fa fa-comment text-success blinkingAnimation fa-2x" role="status" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$crd_commentVIH.'!" aria-hidden="true"></i>';
                                                                        }else{
                                                                            $commentCRDVIH = '<i class="fa fa-comment text-danger fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Komentar nije napisan"][$languageUser].'!" aria-hidden="true"></i>';
                                                                        }
                                                                        $doc_idVIH = intval($rowVIH["doc_id"]); 
                                                                        $doc_file_nameVIH = $rowVIH["doc_file_name"];
                                                                        if($doc_file_nameVIH != NULL){
                                                                            $doc_fileVIH = '<a href="'.$doc_file_nameVIH.'" target="_blank"><button type="button" class="btn btn-success"><i class="fa fa-file" aria-hidden="true"></i></button></a>';
                                                                        }else{
                                                                            $doc_fileVIH = '<button type="button" class="btn btn-danger" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Dokument nije uploadan"][$languageUser].'"><i class="fa fa-file" aria-hidden="true"></button>';
                                                                        }
                                                                        $doc_statusVIH = intval($rowVIH["doc_status"]);
                                                                        if($languageUser == 1){
                                                                            $ds_nameVIH = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($doc_statusVIH).'>'.$rowVIH["ds_name_de_pp"].'</span>';
                                                                        }else{
                                                                            $ds_nameVIH = '<span class="badge rounded-pill fs-6" '.getDocumentStatusColorR($doc_statusVIH).'>'.$rowVIH["ds_name_pp"].'</span>';
                                                                        }
                                                                        $dsl_dateVIH = date("d.m.Y", strtotime($rowVIH["dsl_date"]));
                                                                        $dsl_user_idVIH = intval($rowVIH["dsl_user_id"]);
                                                                        $dsl_pp_user_idVIH = intval($rowVIH["dsl_pp_user_id"]);
                                                                        if($dsl_user_idVIH == 0 AND $dsl_pp_user_idVIH == 0){
                                                                            $dsl_userVIH = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
                                                                        }else if($dsl_user_idVIH != 0 AND $dsl_pp_user_idVIH == 0){
                                                                            $dsl_userVIH = '<i class="fa fa-user-circle fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.getEmployeeFullNameById($dsl_user_idVIH).'" aria-hidden="true"></i>';
                                                                        }else if($dsl_user_idVIH == 0 AND $dsl_pp_user_idVIH != 0){
                                                                            $dsl_userVIH = '<i class="fa fa-user-circle fa-2x" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.getFirstAndLastNameUserR($dsl_pp_user_idVIH).'" aria-hidden="true"></i>';
                                                                        }else{
                                                                            $dsl_userVIH = '<i class="fa fa-user-times fa-2x" style="color:red;" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="'.$txtArray["Nema podataka o korisniku!"][$languageUser].'" aria-hidden="true"></i>';
                                                                        } 
                                                                        $dsl_commentVIH = $rowVIH["dsl_comment"];
                                                    
                                                                        $detailsVIH = '
                                                                            <button 
                                                                                type="button" 
                                                                                class="btn btn-info" 
                                                                                onclick="documentDetailsHistoryVI(this)" 
                                                                                data-crd_id = "'.$crd_idVIH.'"
                                                                                data-candidat_id = "'.$kandidat_id.'"
                                                                                data-nalog_id = "'.$candidateNalog.'"
                                                                                data-doc_id = "'.$doc_idVIH.'"
                                                                                data-vi_id = "'.$idVIH.'"
                                                                            >
                                                                                <i class="fa fa-info-circle " aria-hidden="true"></i>
                                                                            </button>
                                                                        ';
                                                                        if($doc_statusVIH == 0){
                                                                            echo '
                                                                                <tr>
                                                                                    <td class="text-center">'.$doc_type_nameVIH.'</td>
                                                                                    <td colspan="4" class="text-center"><span class="badge rounded-pill bg-danger fs-5">'.$txtArray["Dokument nije uploadan"][$languageUser].'</span></td>
                                                                                    <td style="display: none;"></td>
                                                                                    <td style="display: none;"></td>
                                                                                    <td style="display: none;"></td>
                                                                                    <td class="text-center">'.$commentCRDVIH.'</td>
                                                                                </tr>
                                                                            ';
                                                                        }else{
                                                                            echo '
                                                                                <tr>
                                                                                    <td class="text-center">'.$doc_type_nameVIH.'</td>
                                                                                    <td class="text-center">'.$doc_fileVIH.'</td>
                                                                                    <td class="text-center">'.$ds_nameVIH.'</td>
                                                                                    <td class="text-center">'.$dsl_dateVIH.'</td>
                                                                                    <td class="text-center">'.$dsl_userVIH.'</td>
                                                                                    <td class="text-center">'.$detailsVIH.'</td>
                                                                                </tr>
                                                                            ';
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        ?>
                                                        </tbody>
													</table>
                                                    <?php 
                                                        }else{
                                                    ?>
                                                    <div class="alert alert-warning text-center mt-3" role="alert">
                                                        <?php echo $txtArray["Za ovu dopunu/odbijenicu niste zaduženi ni za jedan dokument"][$languageUser]."!"; ?>
                                                    </div>
                                                    <?php 
                                                        }
                                                    ?>
												</div>
											</div>
											<?php 
												}else{
                                            ?>
                                            <hr class="mb-3 mt-0">
											<div class="row">
												<div class="col-lg-12">
                                                    <div class="alert alert-warning text-center" role="alert">
                                                        <?php echo $txtArray["Kod odbijenice nema uslova za žalbu!"][$languageUser]; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php   
                                                }
											?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 

                                }
                        ?>
                        <!-- MODAL ZA PREGLED DETALJA O DOKUMENTU -->
                        <script>
                            function documentDetailsHistoryVI(thisRow) {
                                //Ovdje ce ici provjera za remindere nekad kad budu bili
                                //clearDocumentDetailsHistory();
                                var crd_id = parseInt($(thisRow).data("crd_id"));
                                var doc_id = parseInt($(thisRow).data("doc_id"));
                                var candidat_id = parseInt($(thisRow).data("candidat_id"));
                                var nalog_id = parseInt($(thisRow).data("nalog_id"));
                                var vi_id = parseInt($(thisRow).data("vi_id"));
                                
                                //console.log(nrd_id+ " " + doc_id + " " + candidat_id + " " + nalog_id);

                                if(crd_id != 0 && doc_id != 0 && candidat_id != 0 && nalog_id != 0 && vi_id != 0){
                                    $.ajax({
                                        url: 'ajax.php?action=documentDetailsVisaIncomplete',
                                        type: 'POST',
                                        dataType: 'html',
                                        data:{
                                            'crd_id': crd_id,
                                            'doc_id': doc_id,
                                            'candidat_id': candidat_id,
                                            'nalog_id': nalog_id,
                                            'vi_id': vi_id
                                        },
                                        success : function (result){
                                            $('.documentDetailsHistory').hide('fade', 250, function(){
                                                $(".documentDetailsHistoryBody").html("");
                                                $(".documentDetailsHistoryBody").html(result);
                                                enablePopoversAndTooltipsDH();
                                                if(result != ""){
                                                    $('.documentDetailsHistory').show('fade', 500);
                                                }
                                            });
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                                            alert(thrownError);
                                        }
                                    });
                                }
                                
                            };
                        </script>
                        <!-- MODAL ZA PREGLED DETALJA O DOKUMENTU -->
                        <?php 
                            }else{
                        ?>
                        <div class="row pt-3">
                            <div class="col-12">
                                <div style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
                                    <div class = "row">
                                        <div class = "col-12">
                                            <p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Dopuna"][$languageUser]."/".$txtArray["Odbijenica"][$languageUser]; ?></p>
                                        </div>
                                    </div>
                                    <div class = "row pt-3">
                                        <div class = "col-12">
                                            <div class="alert alert-danger text-center" role="alert">
                                                <?php echo $txtArray["Ne postoje informacije o prošlim dopunama/odbijenicama za ovog kandidata!"][$languageUser]; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            }
                        ?>
                    </div>
                    <div class="col-6 h-100 overflow-auto scrollBarVertical">
                        <div class="documentDetailsHistory" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
                            <div class = "row align-items-center">
                                <div class = "col-12">
                                    <p class = "mb-0" style = "color: #1D84C0; font-style: normal; font-weight: 600; font-size: 18px; line-height: 22px;"><?php echo $txtArray["Detalji dokumenta"][$languageUser]; ?></p>
                                </div>
                            </div>
                            <div class = "row pt-3">
                                <div class="documentDetailsHistoryBody col-12 scrollBarHorizontal text-center" style="overflow-y: auto !important; user-select: none;">
                                    
                                </div>
                            </div>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal END -->