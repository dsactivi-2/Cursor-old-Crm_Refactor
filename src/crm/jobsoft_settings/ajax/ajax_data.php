<?php 
    include($_SERVER['DOCUMENT_ROOT'].'/includes/functions.php');
    if ($logged_employee_id != 0) {
        $page = $_REQUEST["page"] ?? null; 
        switch($page) {

            case 'viewPredefinedQuestion': 
                $question_data = $_REQUEST['question_data'] ?? null; 
                if ($question_data == null) {
                    http_response_code(400);
                    die("Undefined data parameter!");
                }

                $flagEnpalAccess = getCompanyAccessR($question_data['pqOrder']);

                ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <?php
                                if ($question_data['pqcId'] != null) {
                                    ?>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <p style = "margin: 10px 0px 10px; color: #1F2E45; font-style: normal; font-weight: 600; font-size: 19px;"><?php echo $question_data['pqcName'].' / '.$question_data['pqcNameDe'];?></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <hr>
                                            </div>
                                        </div>
                                    <?php 
                                }
                            ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <?php 
                                                        $classQuestion = "";
                                                        $classRating = "";
                                                        $classDropdown = "";

                                                        if ($question_data['hasRating'] == 1 AND $question_data['hasDropdown'] == 1){
                                                            $classQuestion = "col-sm-6";
                                                            $classRating = "col-sm-3 text-center";
                                                            $classDropdown = "col-sm-3 text-center";
                                                        } else if ($question_data['hasRating'] == 1 AND $question_data['hasDropdown'] == 0) {
                                                            $classQuestion = "col-sm-6";
                                                            $classRating = "col-sm-6 text-right";
                                                            $classDropdown = "hidden";
                                                        } else if ($question_data['hasRating'] == 0 AND $question_data['hasDropdown'] == 1) {
                                                            $classQuestion = "col-sm-6";
                                                            $classRating = "hidden";
                                                            $classDropdown = "col-sm-6 text-right";
                                                        } else {
                                                            $classQuestion = "col-sm-12";
                                                            $classRating = "hidden";
                                                            $classDropdown = "hidden";
                                                        }
                                                    ?>
                                                    <div class="row">
                                                        <div class="<?php echo $classQuestion; ?>">
                                                            <p style = "margin: 0px 0px 0px; color: #1F2E45; font-style: normal; font-weight: 600; font-size: 16px;"><?php echo $question_data['pqQuestion'];?></p>
                                                        </div>
                                                        <?php 
                                                            if ($question_data['hasRating'] == 1) {
                                                                ?>
                                                                    <div class="<?php echo $classRating; ?>">
                                                                        <i class="fa fa-star fa-2x" style = "color: #FDD878;" aria-hidden="true"></i>
                                                                        <i class="fa fa-star fa-2x" style = "color: #FDD878;" aria-hidden="true"></i>
                                                                        <i class="fa fa-star-o fa-2x" style = "color: #FDD878;" aria-hidden="true"></i>
                                                                        <i class="fa fa-star-o fa-2x <?php echo(($flagEnpalAccess == 1) ? 'hidden' : ''); ?>" style = "color: #FDD878;" aria-hidden="true"></i>
                                                                        <i class="fa fa-star-o fa-2x <?php echo(($flagEnpalAccess == 1) ? 'hidden' : ''); ?>" style = "color: #FDD878;" aria-hidden="true"></i>
                                                                    </div>
                                                                <?php 
                                                            }
                                                            if ($question_data['hasDropdown'] == 1) {
                                                                if (count($question_data['optionsData']) > 0) {
                                                                    ?>
                                                                        <div class="<?php echo $classDropdown; ?>">
                                                                            <?php 
                                                                                $optionsData = $question_data['optionsData']; 
                                                                                $optionsDataExp = array();
                                                                                $optionsDataImp = '';
                                                                                foreach ($optionsData AS $optionData) {
                                                                                    array_push($optionsDataExp, '<option value="'.$optionData['pqoValue'].'" '.(($optionData['pqoValueSubtextDe'] != NULL) ? 'data-subtext=" '.$optionData['pqoValueSubtextDe'].' " ' : '').'>'.$optionData['pqoValueTextDe'].'</option>');
                                                                                    unset($optionData);
                                                                                }
                                                                                unset($optionsData);
            
                                                                                $optionsDataImp = '
                                                                                    <div class="row">
                                                                                        <div class="col-xs-12"> 
                                                                                            <select class="selectpicker" title="Kliknite za prikaz opcija">
                                                                                                '.implode('', $optionsDataExp).'
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                ';
                                                                                unset($optionsDataExp);
            
                                                                                echo $optionsDataImp;
                                                                                unset($optionsDataImp);
                                                                            ?>
                                                                        </div>
                                                                    <?php
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php 
                                                if ($question_data['pqHasText'] == 1) {
                                                    ?>
                                                        <div class="row" style="margin-top: 20px;">
                                                            <div class="col-xs-12">
                                                                <textarea style="border: 2px solid #eee;" class="form-control materail-input material-textarea" placeholder="Unesite text" rows="4"></textarea>
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
                    </div>
                <?php 
            break;

            case 'casting_dates_for_nalog':
                $order_id = $_POST['order_id'] ?? null;
                if ($order_id == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                $result = array(
                    'status' => 0, 
                    'message' => 'default', 
                    'data' => array()
                );

                $query = $db->prepare("
                    SELECT 
                        pap.pap_id,  
                        pap.pap_date,
                        pap.pap_city
                    FROM 
                        idk_pp_appointments pap
                    JOIN
                        idk_pp_appointments_questions papq 
                    ON 
                        pap.pap_id = papq.papq_appointment_id 
                    WHERE 
                        pap.pap_nalog_id = :order_id
                    GROUP BY pap.pap_id
                    ORDER BY pap.pap_id DESC
                ");
                $query->execute(array(
                    ':order_id' => $order_id
                ));
                if ($query->rowCount() > 0) {
                    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
                    $result['status'] = 1; 
                    $result['message'] = 'Uspješno učitani podaci';
                    $result['data'] = $rows;
                    unset($rows);
                } else {
                    $result['status'] = 0; 
                    $result['message'] = 'Sistem nije pronašao ni jedan dan castinga sa postavljenim pitanjima! Koristite drugu opciju!';
                }

                echo json_encode($result);
                
            break; 

            case 'checking_sent_messages':
                $pap_id = $_POST['pap_id'] ?? null;

                if ($pap_id == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                $result = array(
                    'status' => 0, 
                    'message' => 'default', 
                    'first_message_counter' => 0, 
                    'second_message_counter' => 0,
                    'past_date' => 0
                );

                $query = $db->prepare("
                    SELECT 
                        pap.pap_id,
                        pap.pap_date,  
                        COUNT(CASE WHEN ail.counter_sent = 1 THEN ail.id ELSE NULL END) AS count_first_sent_messages,
                        COUNT(CASE WHEN ail.counter_sent = 2 THEN ail.id ELSE NULL END) AS count_second_sent_messages
                    FROM 
                        idk_pp_appointments pap
                    LEFT JOIN 
                        idk_pp_cand_appts pca 
                    ON 
                        pap.pap_id = pca.pca_appointment_id 
                    LEFT JOIN 
                        idk_appointment_invite_links ail 
                    ON 
                        pca.pca_id = ail.interview_id 
                    WHERE 
                        pap.pap_id = :pap_id
                    GROUP BY
                        pap.pap_id,
                        pap.pap_date
                ");
                $query->execute(array(
                    ':pap_id' => $pap_id
                ));
                if ($query->rowCount() > 0) {
                    $rows = $query->fetch();
                    $result['status'] = 1; 
                    $result['message'] = 'Uspješno učitani podaci';
                    $result['first_message_counter'] = $rows['count_first_sent_messages'];
                    $result['second_message_counter'] = $rows['count_second_sent_messages'];
                    $currentDate = time();
                    $appointmentDate = strtotime($rows['pap_date']);
                    $result['past_date'] = (time() > strtotime($rows['pap_date'])) ? 1 : 0;
                    unset($rows);
                } else {
                    $result['status'] = 0; 
                    $result['message'] = 'Za ovaj datum termina prilikom provjere, sistem nije pronašao poslane poruke!';
                }

                echo json_encode($result);

            break; 

            case 'question_has_used':
                $pqu_id = $_POST['pqu_id'] ?? null; 
                if ($pqu_id == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                $result = array(
                    'status' => 0, 
                    'message' => 'default', 
                    'count_question_ratings' => 0
                );

                $query = $db->prepare("
                    SELECT 
                        pqu.pqu_id, 
                        COUNT(pra.pra_appointment_question_id) AS count_question_ratings
                    FROM 
                        idk_pp_questions pqu 
                    LEFT JOIN  
                        idk_pp_appointments_questions papq
                    ON 
                        pqu.pqu_id = papq.papq_question_id
                    LEFT JOIN 
                        idk_pp_ratings pra
                    ON 
                        papq.papq_id = pra.pra_appointment_question_id
                    WHERE 
                        pqu.pqu_id = :pqu_id
                    GROUP BY
                        pqu.pqu_id
                ");
                $query->execute(array(
                    ':pqu_id' => $pqu_id
                ));
                if ($query->rowCount() > 0) {
                    $rows = $query->fetch();
                    if ($rows['count_question_ratings'] == 0) {
                        $result['status'] = 1;
                        $result['count_question_ratings'] = $rows['count_question_ratings']; 
                        $result['message'] = 'Pitanje nije korišteno i moguće je izvršiti uređivanje pitanja!';
                    } else {
                        $result['status'] = 0;
                        $result['count_question_ratings'] = $rows['count_question_ratings']; 
                        $result['message'] = 'Pitanje je korišteno '.$rows['count_question_ratings'].' put/a i zbog toga nije moguće izvršiti uređivanje!';
                    }
                    unset($rows);
                } else {
                    http_response_code(404);
                    die("Prilikom provjere korištenja pitanja, sistem nije pronašao informacije o traženom pitanju! Kontaktirajte administratora sistema!");
                }
                //print("<pre>".print_r($result,true)."</pre>");
                echo json_encode($result);
            break; 
            
            case 'question_for_appointment':
                $pap_id = $_POST['pap_id'] ?? null;
                $order_id = $_POST['order_id'] ?? null;
                if ($pap_id == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                $result = array(
                    'status' => 0, 
                    'message' => 'default',
                    'categories' => array(), 
                    'questions' => array()
                );

                $flagEnpalAccess = getCompanyAccessR($order_id);

                $query_questions = $db->prepare("
                    SELECT
                        pap.pap_id AS papId, 
                        pap.pap_date AS papDate, 
                        papq.papq_order AS pqPosition,
                        pqu.pqu_id AS pqId,
                        pqu.pqu_nalog_id AS pqOrder, 
                        pqu.pqu_question AS pqQuestion, 
                        pqc.pqc_id AS pqcId,
                        pqc.pqc_name AS pqcName, 
                        pqc.pqc_name_de AS pqcNameDe,
                        pqu.pqu_has_text AS pqHasText, 
                        pqu.pqu_has_rating AS hasRating, 
                        pqu.pqu_has_dropdown AS hasDropdown, 
                        pqo.pqo_id AS pqoId, 
                        pqo.pqo_value AS pqoValue,
                        pqo.pqo_value_text AS pqoValueText,
                        pqo.pqo_value_text_de AS pqoValueTextDe,
                        pqo.pqo_value_subtext AS pqoValueSubtext,
                        pqo.pqo_value_subtext_de AS pqoValueSubtextDe
                    FROM 
                        idk_pp_appointments pap
                    JOIN 
                        idk_pp_appointments_questions papq
                    ON 
                        pap.pap_id = papq.papq_appointment_id 
                    JOIN 
                        idk_pp_questions pqu
                    ON
                        papq.papq_question_id = pqu.pqu_id
                    LEFT JOIN 
                        idk_pp_question_categories pqc
                    ON 
                        pqu.pqu_category_id = pqc.pqc_id
                    LEFT JOIN 
                        idk_pp_question_options pqo 
                    ON 
                        pqu.pqu_id = pqo.pqo_question_id
                        AND 
                        pqu.pqu_has_dropdown = 1
                    WHERE 
                        pap.pap_id = :pap_id
                    ORDER BY
                        papq.papq_order ASC, papq.papq_id ASC
                "); 
                $query_questions->execute(array(
                    ':pap_id' => $pap_id
                ));
                if ($query_questions->rowCount() > 0) {
                    $rows_questions = $query_questions->fetchAll(PDO::FETCH_ASSOC);
                    $groupedData = array();
                    $questionCategories = array();
                    foreach ($rows_questions as $row_questions) {
                        $pqcExists = false; 
                        foreach($questionCategories AS $questionCategory) {
                            if ($questionCategory['pqcId'] == $row_questions['pqcId']) {
                                $pqcExists = true;
                                break;
                            }
                            unset($questionCategory); 
                        }
                        if (!$pqcExists) {
                            $questionCategories[] = array(
                                'pqcId' => $row_questions['pqcId'],
                                'pqcName' => $row_questions['pqcName'],
                                'pqcNameDe' => $row_questions['pqcNameDe']
                            );
                        }
                        unset($pqcExists);

                        $pqExists = false;
                        $pqIndex = -1;
                        foreach($groupedData AS $index => $groupedRow) {
                            if ($groupedRow['pqId'] === $row_questions['pqId']) {
                                $pqExists = true;
                                $pqIndex = $index;
                                break;
                            }
                            unset($groupedRow);
                        }
                        if (!$pqExists) {
                            $groupedData[] = array(
                                'papId' => $row_questions['papId'],
                                'papDate' => $row_questions['papDate'],
                                'pqPosition' => ($row_questions['pqPosition']),
                                'pqId' => $row_questions['pqId'],
                                'pqOrder' => $row_questions['pqOrder'],
                                'pqEnpalAccess' => $flagEnpalAccess,
                                'pqQuestion' => $row_questions['pqQuestion'],
                                'pqcId' => $row_questions['pqcId'],
                                'pqcName' => $row_questions['pqcName'],
                                'pqcNameDe' => $row_questions['pqcNameDe'],
                                'pqHasText' => $row_questions['pqHasText'],
                                'hasRating' => $row_questions['hasRating'],
                                'hasDropdown' => $row_questions['hasDropdown'],
                                'optionsData' => array()
                            );
                            end($groupedData);
                            $pqIndex = key($groupedData);
                            if ($groupedData[$pqIndex]['pqPosition'] === null) {
                                $groupedData[$pqIndex]['pqPosition'] = $pqIndex + 1;
                            }
                            reset($groupedData);
                        }
                        if($row_questions['pqoId'] !== NULL && $pqIndex !== -1) {
                            $data = array( 
                                'pqoId' => $row_questions['pqoId'],
                                'pqoValue' => $row_questions['pqoValue'],
                                'pqoValueText' => $row_questions['pqoValueText'],
                                'pqoValueTextDe' => $row_questions['pqoValueTextDe'],
                                'pqoValueSubtext' => $row_questions['pqoValueSubtext'],
                                'pqoValueSubtextDe' => $row_questions['pqoValueSubtextDe']
                            );
                            $groupedData[$pqIndex]['optionsData'][$row_questions['pqoId']] = $data;
                            unset($data);
                        }
                        unset($row_questions); 
                    }
                    unset($rows_questions);
                    $result['status'] = 1; 
                    $result['message'] = 'Uspješno učitani podaci';
                    $result['categories'] = $questionCategories; 
                    $result['questions'] = $groupedData; 
                    unset($groupedData);
                    unset($questionCategories);
                } else {
                    $result['status'] = 0; 
                    $result['message'] = 'Sistem nije pronašao postavljena pitanja za traženi dan castinga! Birajte drugi dan castinga ili koristite drugu opciju!';
                }
                
                echo json_encode($result);

            break;
            
            case 'question_for_order':
                $order_id = $_POST['order_id'] ?? null;
                $pqu_ids = $_POST['pqu_ids'] ?? null;
                if ($order_id == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                if ($pqu_ids != null) {
                    $pqu_ids_clean = str_replace(['[', ']', '"'], '', $pqu_ids);
                    $pqu_ids_exp = explode(',', $pqu_ids_clean);
                    $pqu_ids_imp = ((count($pqu_ids_exp) > 0) ? implode(',', $pqu_ids_exp) : 0);
                    unset($pqu_ids_clean);
                    unset($pqu_ids_exp);
                } else {
                    $pqu_ids_imp = 0;
                }

                $result = array(
                    'status' => 0, 
                    'message' => 'default', 
                    'data' => array()
                );

                $query = $db->prepare("
                    SELECT 
                        pqu.pqu_id,  
                        pqu.pqu_question, 
                        (
                            CASE 
                                WHEN pqu.pqu_category_id IS NOT NULL THEN CONCAT(pqc.pqc_name, ' / ', pqc.pqc_name_de)
                                ELSE 'Bez kategorije / Ohne Kategorie'
                            END
                        ) AS pqc_catrgory_value
                    FROM 
                        idk_pp_questions pqu
                    LEFT JOIN 
                        idk_pp_question_categories pqc
                    ON 
                        pqu.pqu_category_id = pqc.pqc_id
                    WHERE 
                        pqu.pqu_nalog_id = :order_id
                        AND 
                        pqu.pqu_id NOT IN (".$pqu_ids_imp.")
                    ORDER BY 
                        pqu.pqu_id 
                    ASC
                ");
                $query->execute(array(
                    ':order_id' => $order_id
                ));
                if ($query->rowCount() > 0) {
                    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
                    $result['status'] = 1; 
                    $result['message'] = 'Uspješno učitani podaci';
                    $result['data'] = $rows;
                    unset($rows);
                } else {
                    $result['status'] = 0; 
                    $result['message'] = 'Sistem nije pronašao ni jedano predefinisano pitanje ovog naloga! Koristite opciju <strong>Dodaj pitanje</strong> u <strong>Predefinisanim pitanjima</strong>!';
                }

                echo json_encode($result);
            break; 

            case 'questions_for_order_with_ids':
                $pqu_ids = $_POST['pqu_ids'] ?? null;
                $order_id = $_POST['order_id'] ?? null;
                if ($pqu_ids == null OR $order_id == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                $pqu_ids_imp = implode(',', $pqu_ids);

                $result = array(
                    'status' => 0, 
                    'message' => 'default',
                    'categories' => array(), 
                    'questions' => array()
                );

                $flagEnpalAccess = getCompanyAccessR($order_id);

                $query_questions = $db->prepare("
                    SELECT
                        pqu.pqu_id AS pqId,
                        pqu.pqu_nalog_id AS pqOrder, 
                        pqu.pqu_question AS pqQuestion, 
                        pqc.pqc_id AS pqcId,
                        pqc.pqc_name AS pqcName, 
                        pqc.pqc_name_de AS pqcNameDe,
                        pqu.pqu_has_text AS pqHasText, 
                        pqu.pqu_has_rating AS hasRating, 
                        pqu.pqu_has_dropdown AS hasDropdown, 
                        pqo.pqo_id AS pqoId, 
                        pqo.pqo_value AS pqoValue,
                        pqo.pqo_value_text AS pqoValueText,
                        pqo.pqo_value_text_de AS pqoValueTextDe,
                        pqo.pqo_value_subtext AS pqoValueSubtext,
                        pqo.pqo_value_subtext_de AS pqoValueSubtextDe
                    FROM  
                        idk_pp_questions pqu
                    LEFT JOIN 
                        idk_pp_question_categories pqc
                    ON 
                        pqu.pqu_category_id = pqc.pqc_id
                    LEFT JOIN 
                        idk_pp_question_options pqo 
                    ON 
                        pqu.pqu_id = pqo.pqo_question_id
                        AND 
                        pqu.pqu_has_dropdown = 1
                    WHERE 
                        pqu.pqu_id IN (".$pqu_ids_imp.")
                    ORDER BY
                        pqu.pqu_id ASC
                "); 
                $query_questions->execute();
                if ($query_questions->rowCount() > 0) {
                    $rows_questions = $query_questions->fetchAll(PDO::FETCH_ASSOC);
                    $groupedData = array();
                    $questionCategories = array();
                    foreach ($rows_questions as $row_questions) {
                        $pqcExists = false; 
                        foreach($questionCategories AS $questionCategory) {
                            if ($questionCategory['pqcId'] == $row_questions['pqcId']) {
                                $pqcExists = true;
                                break;
                            }
                            unset($questionCategory); 
                        }
                        if (!$pqcExists) {
                            $questionCategories[] = array(
                                'pqcId' => $row_questions['pqcId'],
                                'pqcName' => $row_questions['pqcName'],
                                'pqcNameDe' => $row_questions['pqcNameDe']
                            );
                        }
                        unset($pqcExists);

                        $pqExists = false;
                        $pqIndex = -1;
                        foreach($groupedData AS $index => $groupedRow) {
                            if ($groupedRow['pqId'] === $row_questions['pqId']) {
                                $pqExists = true;
                                $pqIndex = $index;
                                break;
                            }
                            unset($groupedRow);
                        }
                        if (!$pqExists) {
                            $groupedData[] = array(
                                'pqId' => $row_questions['pqId'],
                                'pqOrder' => $row_questions['pqOrder'],
                                'pqEnpalAccess' => $flagEnpalAccess,
                                'pqQuestion' => $row_questions['pqQuestion'],
                                'pqcId' => $row_questions['pqcId'],
                                'pqcName' => $row_questions['pqcName'],
                                'pqcNameDe' => $row_questions['pqcNameDe'],
                                'pqHasText' => $row_questions['pqHasText'],
                                'hasRating' => $row_questions['hasRating'],
                                'hasDropdown' => $row_questions['hasDropdown'],
                                'optionsData' => array()
                            );
                            end($groupedData);
                            $pqIndex = key($groupedData);
                            reset($groupedData);
                        }
                        if($row_questions['pqoId'] !== NULL && $pqIndex !== -1) {
                            $data = array( 
                                'pqoId' => $row_questions['pqoId'],
                                'pqoValue' => $row_questions['pqoValue'],
                                'pqoValueText' => $row_questions['pqoValueText'],
                                'pqoValueTextDe' => $row_questions['pqoValueTextDe'],
                                'pqoValueSubtext' => $row_questions['pqoValueSubtext'],
                                'pqoValueSubtextDe' => $row_questions['pqoValueSubtextDe']
                            );
                            $groupedData[$pqIndex]['optionsData'][$row_questions['pqoId']] = $data;
                            unset($data);
                        }
                        unset($row_questions); 
                    }
                    unset($rows_questions);
                    $result['status'] = 1; 
                    $result['message'] = 'Uspješno učitani podaci';
                    $result['categories'] = $questionCategories; 
                    $result['questions'] = $groupedData; 
                    unset($groupedData);
                    unset($questionCategories);
                } else {
                    $result['status'] = 0; 
                    $result['message'] = 'Sistem nije pronašao tražena pitanja! Birajte druga pitanja ili kontaktirajte administratora sistema u slučaju potencijalne greške!';
                }
                
                echo json_encode($result);
            break; 

            default:
                http_response_code(500);
                die("Undefined page parameter!");
            break;
        }
    } else {
        header('Location: login.php');
    }
?>