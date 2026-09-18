<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = explode(',', getEmployeeStatus());


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>FAQ</title>

    <?php include('includes/head.php');
    if (in_array($getUserIp, $getIpWhiteList)) {
    ?>

</head>
<style>
    .custom-checkbox-label {
        margin-left: 5px;
    }

    .custom-checkbox-disabled {
        appearance: none;
        content: "-";
        width: 31px;
        height: 31px;
        border: 2px solid #d3d3d3;
        border-radius: 3px;
        display: inline-block;
        position: relative;
        vertical-align: middle;
        margin: 0px;
        background-color: #d3d3d3;
    }

    .custom-checkbox {
        appearance: none;
        width: 31px;
        height: 31px;
        border: 2px solid #4caf50;
        border-radius: 3px;
        display: inline-block;
        position: relative;
        vertical-align: middle;
        cursor: pointer;
        margin: 0px;
    }

    .custom-checkbox:checked {
        background-color: #4caf50;
        border: 2px solid #4caf50;
    }

    .custom-checkbox:checked:after {
        content: "✓";
        font-size: 18px;
        color: white;
        position: absolute;
        top: 0;
        left: 6px;
    }
</style>

<body>
    <header>
        <?php include('header.php'); ?>
    </header>
    <div id="sidebar">
        <?php include('menu.php'); ?>
    </div>
    <?php
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            $isMain = 0;
        } else {
            $id = NULL;
            $isMain = 1;
        }
        $chatbotData = getChatDetails($id, $isMain);
        $chatbotDataWithCurrentType = array_map(function ($item) {
            $faq_type = $_GET["type"] ? $_GET["type"] : 1;
            $item['currentType'] = $faq_type;
            return $item;
        }, $chatbotData);
    ?>
    <div id="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12" style="padding-bottom: 50px;">
                    <div class="col-xs-4">
                        <?php
                        if (isset($_GET["id"])) {
                            $getQStmt = $db->prepare("SELECT question FROM idk_chat_question WHERE id = $id");
                            $getQStmt->execute();
                            $getQ = $getQStmt->fetch();
                            $prevQuestion = $getQ["question"];
                            $questionData = json_decode($prevQuestion, true);
                            $values = array_values($questionData);
                            $firstLanguageData = $values[0];
                            $titleText = "pitanja - $firstLanguageData";
                            $buttonBack = true;
                        } else {
                            $titleText = "kategorija";
                            $buttonBack = false;
                        }
                        ?>
                        <h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> FAQ vidljivost <?php echo $titleText; ?></h1>
                    </div>
                    <div class="col-xs-4 text-center">
                        <a href="<?php getSiteURL(); ?>partner_faq_bot" type="button" class="btn btn-success">
                            Pregled svega
                        </a>
                    </div>
                    <div class="col-xs-4 text-right">
                        <?php if ($buttonBack) {
                        ?>
                            <a href="<?php getSiteURL(); ?>faq_overview" type="button" class="btn btn-success">
                                Nazad
                            </a>
                        <?php
                        } ?>
                    </div>
                </div>
                <div class="col-xs-12">
                    <hr />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="content_box">

                        <div class="row">
                            <div class="col-xs-12">
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        $('#idk_table').DataTable({

                                            responsive: true,

                                            "order": [
                                                [0, "asc"]
                                            ],

                                            "bAutoWidth": false,

                                            "aoColumns": [{
                                                    "width": "80%"
                                                },
                                                {
                                                    "width": "5%"
                                                },
                                                {
                                                    "width": "5%"
                                                },
                                                {
                                                    "width": "5%"
                                                },
                                                {
                                                    "width": "5%"
                                                }
                                            ]
                                        });
                                    });
                                </script>
                                <table id="idk_table" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Pitanja (u prva dva jezika)</th>
                                            <th>Partner App</th>
                                            <th>Jobstep.com</th>
                                            <th>Recruitment app</th>
                                            <th>Jobsoft</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($chatbotData as $row) { ?>

                                            <tr>
                                                <td style='cursor: pointer' onclick="window.location.href=`<?php getSiteURL(); ?>faq_overview?id=<?php echo $row['id'] ?>`;">
                                                    <?php
                                                    $languages = array_slice($row['selectedLanguages'], 0, 2);
                                                    $firstQuestions = [];

                                                    foreach ($languages as $language) {
                                                        $questionKey = 'question' . $language;
                                                        $answerKey = 'answer' . $language;

                                                        if (isset($row[$questionKey]) && isset($row[$answerKey])) {
                                                            $firstQuestions[] = [
                                                                'question' => $row[$questionKey],
                                                                'lang' => $language
                                                            ];

                                                            if (count($firstQuestions) >= 2) {
                                                                break;
                                                            }
                                                        }
                                                    }

                                                    foreach ($firstQuestions as $question) {
                                                        echo "<a> <strong>" . $question["lang"] . "</strong>" . ": " . $question['question'] . "</a> <br>";
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" <?php if (in_array(1, $row["type"])) echo "checked"; ?> class="custom-checkbox" id="<?php echo $row['id'] . "-" . 1; ?>">
                                                </td>
                                                <td class="text-center">
                                                    <input disabled type="checkbox" class="custom-checkbox-disabled" id="<?php echo $row['id'] . "-" . 2; ?>">
                                                </td>
                                                <td class="text-center">
                                                    <input disabled type="checkbox" class="custom-checkbox-disabled" id="<?php echo $row['id'] . "-" . 3; ?>">
                                                </td>
                                                <td class="text-center">
                                                    <input disabled type="checkbox" class="custom-checkbox-disabled" id="<?php echo $row['id'] . "-" . 4; ?>">
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <script>
                                    $(document).ready(function() {
                                        $('input[type="checkbox"]').on('click', function() {
                                            var id = $(this).attr('id').split('-')[0];
                                            var type = $(this).attr('id').split('-')[1];
                                            var visibility = $(this).is(':checked') ? 1 : 0;


                                            $(this).attr('disabled', true);

                                            $.ajax({
                                                url: '/do.php?form=edit_question_visibility',
                                                type: 'POST',
                                                contentType: 'application/json',
                                                data: JSON.stringify({
                                                    id: id,
                                                    visibility: visibility,
                                                    type: type
                                                }),
                                                success: function(response) {
                                                    console.log(response);
                                                    $('input[type="checkbox"]').attr('disabled', false);
                                                    window.location.reload();
                                                },
                                                error: function(xhr, status, error) {
                                                    console.error(xhr.responseText);
                                                    $('input[type="checkbox"]').attr('disabled', false);
                                                    window.location.reload();
                                                }
                                            });
                                        });
                                    });
                                </script>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer><?php getCopyright(); ?></footer>
        </div>
    </div>
</body>

</html>
<?php } else {
        echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
';
    } ?>