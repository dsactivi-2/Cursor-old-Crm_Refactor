<table class="hover row-border" style="white-space:nowrap; width: 70%" id="vizaDokumentiTable"></table>

<div class="modal material-modal material-modal_success fade" id="changeDocumentStatusModal">
    <div class="modal-dialog">
        <div class="modal-content material-modal__content">
            <div class="modal-header material-modal__header">
                <button id="closeStatusModal" class="close material-modal__close" data-dismiss="modal">&times;</button>
                <h4 id="statusModalTitle" class="modal-title material-modal__title"></h4>
            </div>
            <div id="statusModalBody" class="modal-body material-modal__body">
            </div>
        </div>
    </div>
</div>
<button id="openDocumentStatusModal" style="display: none" data-toggle="modal" data-target="#changeDocumentStatusModal"> </button>

<?php
if(getCheckVisaProcessDocumentsR($kandidat_id) == 1)
{
?>
    <hr />
    <h1>Ispunjeni su uslovi za unos termina vize!</h1>
    <br />
    <a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#kandidat_unos_termina_m"><i class="fa fa-plus" aria-hidden="true"></i> <span><?php echo $termin_btn; ?></span></a>
<?php
}
?>
<script>
    let globalData;

    $(function() {
        fetchAndLoadTable();
    });

    /*                              FUNKCIJE                            */
    function fetchAndLoadTable()
    {
        $.ajax({
            url: "/do.php?form=documents&kandidat_id=<?php echo $kandidat_id; ?>",
            success: function(data) {
                destroyTable();
                loadTable(data, columns);
                globalData = data;
            }
        });
    }

    function loadTable(data, columns)
    {
        $("#vizaDokumentiTable").DataTable({
            data: data,
            columns:columns,
            dom: 't',
            ordering: false,
            columnDefs: [
                {
                    targets: "_all",
                    className: 'dt-center',
                    width: "14%"
                },
            ],
             createdRow: function(row, data, dataIndex){
                 if(data.statuses.nijeProsaoProvjeru)
                 {
                    $(row).css("background-color","rgba(255,0,0,0.1)");
                    $('td:eq(3)', row).attr('colspan', '4');
                    $('td:eq(3)', row).css('text-align', 'right');

                    $('td:eq(4)', row).css('display', 'none');
                    $('td:eq(5)', row).css('display', 'none');
                    $('td:eq(6)', row).css('display', 'none');

                }
            },
            autoWidth: false,
            "language": {
                "emptyTable": "Nema dokumenata definisanih za nalog!"
            }
        });
    }

    const renderStatus = ( data, type, row, meta ) => {
        let currentColumnName = columns[meta.col].data;

        if(row.statuses.nijeProsaoProvjeru)
        {
            return renderFailedStatus(data,type,row,meta);
        }

        // Završen?
        if(data)
        {
            return renderFinishedStatus(data, type, row, meta);
        }

        if (buttonShouldBeDisabled(currentColumnName, row))
        {
            return renderDisabledStatus(currentColumnName, row.zaduzeni);
        }
        else
        {
            return renderEnabledStatus(meta.row, meta.col);
        }
        
        return data;
    };

    const changeStatus = async (row, statusName) =>
    {
        const doc = globalData[row];
        const kandidat_id = <?php echo $kandidat_id; ?>;
        const nalog_id = doc.nalog_id;
        const doc_id = doc.id;

        const employee_id = <?php echo $logged_employee_id; ?>;
        const nrd_id = doc.nrd_id || "";
        const crd_id = doc.crd_id || "";

        const statusComment = document.querySelector(`#statusKomentar`).value;

        data = new FormData();


        let status_id;

        switch(statusName)
        {
            case "statuses.uploadan":
                const file = document.querySelector(`#pickDocument${row}`);
                data.append("document", file.files[0], file.files[0].name);
                status_id = 3;
                break;
            case "statuses.provjeren":
                // Provjera
                let provjeraStatus = document.querySelector('input[name="provjeraStatus"]:checked').value;
                status_id = provjeraStatus;
                break;
            case "statuses.poslan":
                // Provjera
                status_id = 12;
                break;
            case "statuses.imamoOriginal":
                // Provjera
                status_id = 15;
                break;
        }


        data.append("kandidat_id",      kandidat_id);
        data.append("nalog_id",         nalog_id);
        data.append("status_comment",   statusComment);
        data.append("nrd_id",           nrd_id);
        data.append("crd_id",           crd_id);
        data.append("employee_id",      employee_id);
        data.append("doc_id",           doc_id);
        data.append("new_status_id",    status_id);

        await fetch(`/do.php?form=changeDocumentStatus`, {
            method: 'POST',
            body: data
        }).then(() => {
            fetchAndLoadTable();
            $("#closeStatusModal").click();
        });
    };

    const renderFailedStatus = (data, type, row, meta) =>
    {
        if(meta.col == 2)
        {
            return `
                    <div class="a6_tooltip">
                        <span 
                            onclick="check(${meta.row},2)" style="cursor: pointer" 
                            class="label label-warning material-label material-label_warning main-container__column">
                            <i class="fa fa-upload"></i>
                        </span>
                        <div class="a6_tooltip_content" style="bottom: 120%">
                            <div style="min-width: 200px">
                                <div style="display: flex; justify-content: center">
                                    Uploadajte novu verziju dokumenta.
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
        }
        if(meta.col == 3)
        {
            let commentHtml = row.statuses.nijeProsaoProvjeru.comment.length > 0 ? 
                                    `<hr style='margin: 2px' /> 
                                    <p style='font-size: 16px; margin: 0'><b>Komentar</b></p> 
                                    ${row.statuses.nijeProsaoProvjeru.comment}` : 
                                    "" ;
            return `<div class="a6_tooltip">
                        <span class="label label-danger material-label material-label_danger main-container__column">
                            Dokument nije prošao provjeru
                        </span>
                        <div style="pointer-events:unset" class="a6_tooltip_content">
                            <div style="min-width: 200px">
                                <div style="display: flex; justify-content: space-between">
                                    <p style="font-size: 16px; margin: 0">Nije prošao provjeru</p>
                                    <div style="width: 50px"></div>
                                    <p style="font-size: 16px; margin: 0">${new Date(row.statuses.nijeProsaoProvjeru.date).toISOString().split("T")[0].split("-").reverse().join(".")}</p>
                                </div>
                                <a target="_blank" href="${row.filename}">
                                    Otvori dokument
                                </a>
                                ${commentHtml}
                            </div>
                        </div>
                    </div>`
        }
        else
            return ``;
    }



    const renderEnabledStatus = (row, col) =>
    {
        return `<span 
                    onmouseenter="labelMouseEnter(this)"
                    onmouseleave="labelMouseLeave(this)" 
                    onclick="check(${row},${col})" style="cursor: pointer" 
                    class="label label-danger material-label material-label_danger main-container__column">
                    <i class="fa fa-ban"></i>
                </span>
                `;
    }

    const renderDisabledStatus = (currentColumnName, zaduzeni) => 
    {
        if(currentColumnName === "statuses.poslan" && zaduzeni === "Poslodavac")
        {
        return `<div class="a6_tooltip">
                    <span class="label label-default material-label material-label_default main-container__column">
                        <i class="fa fa-ban"></i>
                    </span>
                    <div class="a6_tooltip_content">
                        <div style="min-width: 200px">
                            Svi dokumenti moraju biti provjereni prije nego ih poslodavac može poslati!
                        </div>
                    </div>
                </div>
            `;
        }

        return `<span 
                    class="label label-default material-label material-label_default main-container__column">
                    <i class="fa fa-ban"></i>
                </span>`
    }

    const buttonShouldBeDisabled = (currentColumnName, row) => 
    {
        return  (currentColumnName === "statuses.provjeren"         && row.statuses.uploadan        === null) ||
                (currentColumnName === "statuses.poslan"            && row.statuses.cekamoOriginal  === null) ||
                (currentColumnName === "statuses.imamoOriginal"     && row.statuses.poslan          === null) ||
                (currentColumnName === "statuses.spreman"           && row.statuses.imamoOriginal   === null);
    }

    const renderFinishedStatus = (data, type, row, meta) => 
    {
        let currentColumnName = columns[meta.col].data;
        let commentHtml = row.statuses[currentColumnName.split(".")[1]].comment.length > 0 ? 
                                `<hr style='margin: 2px' /> 
                                <p style='font-size: 16px; margin: 0'><b>Komentar</b></p> 
                                ${row.statuses[currentColumnName.split(".")[1]].comment}` : 
                                "" ;
        // Uploadan
        if(meta.col == 2)
        {
            return `
                <div class="a6_tooltip">
                    <a target="_blank" href="${row.filename}">
                        <span class="label label-success material-label material-label_success main-container__column">
                            <i class="fa fa-file-text-o"></i>
                        </span>
                    </a>
                    <div style="pointer-events: unset" class="a6_tooltip_content">
                        <div style="min-width: 200px">
                            <div style="display: flex; justify-content: space-between">
                                <p style="font-size: 16px; margin: 0">${columns[meta.col].title}</p>
                                <div style="width: 50px"></div>
                                <p style="font-size: 16px; margin: 0">${new Date(row.statuses[currentColumnName.split(".")[1]].date).toISOString().split("T")[0].split("-").reverse().join(".")}</p>
                            </div>
                            <a target="_blank" href="${row.filename}">
                                Otvori dokument
                            </a>
                            ${commentHtml}
                        </div>
                    </div>
                </div>
                    `
        }
        return  `<div class="a6_tooltip">
                    <span class="label label-success material-label material-label_success main-container__column">
                        <i class="fa fa-check"></i>
                    </span>
                    <div class="a6_tooltip_content">
                        <div style="min-width: 200px">
                            <div style="display: flex; justify-content: space-between">
                                <p style="font-size: 16px; margin: 0">${columns[meta.col].title}</p>
                                <div style="width: 50px"></div>
                                <p style="font-size: 16px; margin: 0">${new Date(row.statuses[currentColumnName.split(".")[1]].date).toISOString().split("T")[0].split("-").reverse().join(".")}</p>
                            </div>
                            ${commentHtml}
                        </div>
                    </div>
                </div>
                `
    }

    const columns = [
                {
                    data: "name",
                    title: "Naziv",
                },
                {
                    data: "zaduzeni",
                    title: "Zadužen"
                },
                {
                    data: "statuses.uploadan",
                    title: "Uploadan",
                    render: renderStatus
                },
                {
                    data: "statuses.provjeren",
                    title: "Provjeren",
                    render: renderStatus
                },
                {
                    data: "statuses.poslan",
                    title: "Poslan original",
                    render: renderStatus
                },
                {
                    data: "statuses.imamoOriginal",
                    title: "Imamo original",
                    render: renderStatus
                },
                {
                    data: "statuses.spreman",
                    title: "Spreman",
                    render: renderStatus
                },
            ];



    function destroyTable() {
        if ($.fn.DataTable.isDataTable('#vizaDokumentiTable')) {
            $("#vizaDokumentiTable").DataTable().clear().destroy();
            $("#vizaDokumentiTable").empty();
        }
    }
    function reloadTable(data, columns)
    {
        destroyTable();
        loadTable(data,columns);
    }
    const labelMouseEnter = (e) =>
    {
        e.innerHTML='<i class="fa fa-edit"></i>';
        e.classList.replace("label-danger", "label-warning");
        e.classList.replace("material-label_danger", "material-label_warning");
    }

    const labelMouseLeave = (e) =>
    {
        let labelInnerHtmlDefault = '<i class="fa fa-ban"></i>';
        e.innerHTML=labelInnerHtmlDefault;
        e.classList.replace("label-warning", "label-danger");
        e.classList.replace("material-label_warning", "material-label_danger");
    }

    function check(row, column)
    {
        const doc = globalData[row];
        const status = columns[column].data;
        const kandidat_id = <?php echo $kandidat_id; ?>;
        const nalog_id = doc.nalog_id;

        const employee_id = <?php echo $logged_employee_id; ?>;
        const nrd_id = doc.nrd_id;
        const crd_id = doc.crd_id;
        const doc_id = doc.id;



        switch(column)
        {
            case 2:
                // Uploadovan
                document.querySelector("#statusModalTitle").innerHTML = `Upload dokumenta - <em>${doc.name}</em>`;
                document.querySelector("#statusModalBody").innerHTML = `
                    <form class="form" id="uploadForm${row}" onSubmit="changeStatus(${row}, '${status}'); return false;" style="display:flex; flex-direction: column; align-items:center" enctype="multipart/form-data">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <span class="btn btn-default btn-file">
                                    <span class="fileinput-new">Izaberi dokument</span>
                                    <span class="fileinput-exists">Promijeni</span>
                                    <input type="file" name="document" id="pickDocument${row}" required>
                                </span>
                                <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
                                <span class="fileinput-filename"></span>
                                <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a>
                            </div>

                            <textarea style="margin-bottom:15px" class="form-control materail-input material-textarea" name="statusKomentar" id="statusKomentar" placeholder="Komentar" rows="6"></textarea>

                        <div class="form-group">
                            <input class="btn btn-primary material-btn material-btn_primary" id="uploadDocument${row}" type="submit" value="Upload" />
                        </div>
                    </form>
                `;
                $("#openDocumentStatusModal").click();


                break;
            case 3:
                document.querySelector("#statusModalTitle").innerHTML = `${doc.name} - <em>${columns[column].title}</em>`;
                document.querySelector("#statusModalBody").innerHTML = `
                    <form class="form" id="uploadForm${row}" onSubmit="changeStatus(${row}, '${status}'); return false;" style="display:flex; flex-direction: column; align-items:center" enctype="multipart/form-data">
                        <div style="display: flex; width: 80%; justify-content:space-between">
                            <label checked class="main-container__column material-radio-group material-radio-group_success" for="provjeraStatusDa">
                                <input type="radio" checked name="provjeraStatus" id="provjeraStatusDa" class="material-radiobox" value="6">
                                <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                <span class="material-radio-group__element material-radio-group__caption">Dokument prošao provjeru</span>

                            </label>
                            <label class="main-container__column material-radio-group material-radio-group_danger" for="provjeraStatusNe">
                                <input type="radio" name="provjeraStatus" id="provjeraStatusNe" class="material-radiobox" value="21">
                                <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                <span class="material-radio-group__element material-radio-group__caption">Dokument pao provjeru</span>
                            </label>
                        </div>
                        <textarea style="margin-bottom:15px" class="form-control materail-input material-textarea" name="statusKomentar" id="statusKomentar" placeholder="Komentar" rows="6"></textarea>

                        <div class="form-group">
                            <input class="btn btn-primary material-btn material-btn_primary" id="uploadDocument${row}" type="submit" value="Spremi" />
                        </div>
                    </form>
                `;
                $("#openDocumentStatusModal").click();
                break;
            default:
                document.querySelector("#statusModalTitle").innerHTML = `${doc.name} - <em>${columns[column].title}</em>`;
                document.querySelector("#statusModalBody").innerHTML = `
                    <form class="form" id="uploadForm${row}" onSubmit="changeStatus(${row}, '${status}'); return false;" style="display:flex; flex-direction: column; align-items:center" enctype="multipart/form-data">
                        <textarea style="margin-bottom:15px" class="form-control materail-input material-textarea" name="statusKomentar" id="statusKomentar" placeholder="Komentar" rows="6"></textarea>

                        <div class="form-group">
                            <input class="btn btn-primary material-btn material-btn_primary" id="uploadDocument${row}" type="submit" value="Spremi" />
                        </div>
                    </form>
                `;
                $("#openDocumentStatusModal").click();

                break;
        }
    }
</script>
