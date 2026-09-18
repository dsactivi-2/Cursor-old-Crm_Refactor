<?php 
    if ($orderId != 0) {  

        /*
            Short form for OP - Order Partners
        */

        $queryGetOrderInfoOP = $db->prepare('
            SELECT 
                nal.nalog_id AS orderId,
                nal.nalog_naziv AS orderName,
                nal.kompanija_id AS orderCompanyId, 
                comp.company_name AS orderCompanyName,
                pp.ppa_id AS partnerId,
                (
                    CASE 
                        WHEN pp.ppa_status = 1 THEN "Active"
                        WHEN pp.ppa_status = 0 THEN "Inactive"
                        ELSE "Undefined"
                    END
                ) AS partnerStatus,
                pp.ppa_company_id AS partnerCompanyId, 
                ppcomp.company_name AS partnerCompanyName
            FROM  
                idk_nalozi nal
            JOIN 
                idk_companies comp
            ON 
                nal.kompanija_id = comp.company_id 
            JOIN 
                idk_pp_partners pp 
            ON 
                nal.nalog_id = pp.ppa_nalog_id 
            JOIN 
                idk_companies ppcomp
            ON 
                pp.ppa_company_id = ppcomp.company_id
            WHERE 
                nal.nalog_id = :orderId
        ');
        $queryGetOrderInfoOP->execute(array(
            ':orderId' => $orderId
        )); 
        if ($queryGetOrderInfoOP->rowCount() > 0) {
            $rowsGetOrderInfoOP = $queryGetOrderInfoOP->fetchAll(PDO::FETCH_ASSOC);
            $groupedData = array();
            foreach ($rowsGetOrderInfoOP as $row) {
                if (!isset($groupedData[$row['orderId']])) {
                    $groupedData[$row['orderId']] = array(
                        'orderId' => $row['orderId'],
                        'orderName' => $row['orderName'],
                        'orderCompanyId' => $row['orderCompanyId'],
                        'orderCompanyName' => $row['orderCompanyName'],
                        'data' => array()
                    );
                }
                $data = array( 
                    'partnerId' => $row['partnerId'],
                    'partnerCompanyId' => $row['partnerCompanyId'],
                    'partnerCompanyName' => $row['partnerCompanyName'],
                    'partnerStatus' => $row['partnerStatus']
                );
                $groupedData[$row['orderId']]['data'][$row['partnerId']] = $data;
                unset($row);
                unset($data);
            }
            ?>
                <!-- 
                    Order Info START
                    -->
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <style>
                                            #orderPartnersSettings #orderInfoOP>tbody>tr>td, #orderPartnersSettings #orderInfoOP>thead>tr>th {
                                                vertical-align: middle;
                                            }
                                            #orderPartnersSettings #panelOrderInfoOP .scrollBarHorizontal::-webkit-scrollbar {
                                                height:5px;
                                                margin-top: 10px;
                                            }
                                            #orderPartnersSettings  #panelOrderInfoOP .scrollBarHorizontal::-webkit-scrollbar-thumb {
                                                background: #1D84C0;
                                                border-radius: 5px;
                                            }
                                            #orderPartnersSettings  #panelOrderInfoOP .scrollBarHorizontal::-webkit-scrollbar-track {
                                                background-color: #e9ecef;
                                                border-radius: 5px;
                                            }
                                        </style>
                                        <div class="row">
                                            <div class="col-lg-offset-2 col-lg-8" id="panelOrderInfoOP">
                                                <div class="table-responsive scrollBarHorizontal">
                                                    <table class="table table-bordered table-sm" id = "orderInfoOP">
                                                        <thead>
                                                            <th class="text-center">Nalog</th>
                                                            <th class="text-center">Kompanija</th>
                                                            <th class="text-center">Partner</th>
                                                            <th class="text-center">Partner status</th>
                                                            <th class="text-center">Partner uloga</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                                foreach ($groupedData as $dataOrder) {
                                                                    $dataOrderInfoFlag = false; 
                                                                    foreach($dataOrder['data'] as $dataPartner) {
                                                                        ?>  
                                                                            <tr>
                                                                                <?php 
                                                                                    if (!$dataOrderInfoFlag) {
                                                                                        ?>
                                                                                            <td class="text-center" rowspan="<?php echo count($dataOrder['data']); ?>"><?php echo $dataOrder['orderName']; ?></td>
                                                                                            <td class="text-center" rowspan="<?php echo count($dataOrder['data']); ?>"><?php echo $dataOrder['orderCompanyName']; ?></td>
                                                                                        <?php
                                                                                        $dataOrderInfoFlag = true; 
                                                                                    }
                                                                                ?>
                                                                                <td class="text-center">
                                                                                    <a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $dataPartner['partnerCompanyId']; ?>&tab=company_users" title="Pogledajte korisnike kompanije" target="_blank">
                                                                                        <?php echo $dataPartner['partnerCompanyName']; ?>
                                                                                    </a>
                                                                                </td>
                                                                                <td class="text-center"><?php echo ( ($dataPartner['partnerStatus'] != 'Undefined') ? ( ($dataPartner['partnerStatus'] == 'Active') ? '<span class="label label-success">Aktivan</span>' : '<span class="label label-danger">Neaktivan</span>' ) : '<span class="label label-default">Nepoznat</span>' ); ?></td>
                                                                                <td class="text-center" >
                                                                                    <?php 
                                                                                        if ($dataPartner['partnerCompanyId'] == $dataOrder['orderCompanyId']) {
                                                                                            echo '<span class="label label-primary">Glavna kompanija</span>';
                                                                                        } else {
                                                                                            echo '<span class="label label-warning">Partner kompanija</span>';
                                                                                        }
                                                                                    ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                        unset($dataPartner); 
                                                                    } 
                                                                    unset($dataOrder);
                                                                    unset($dataOrderInfoFlag);
                                                                }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- 
                    Order Info END 
                -->
            <?php 
            unset($groupedData);
        } else {
            ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-warning text-center" role="alert">
                            <h4><strong>Upozorenje</strong></h4>
                            <br>
                            Nije aktiviran pristup JobSoftu za ovaj nalog!
                        </div>
                    </div>
                </div>
            <?php 
        }
    }
?>