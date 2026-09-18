class DocumentChecklist extends HTMLElement{
    constructor() {
        super();
        this.DATA = [];
        this.kandidat_id = this.getAttribute("kandidat_id");
        this.employee_id = this.getAttribute("employee_id");
        this.kandidat_status = this.getAttribute("kandidat_status");
        this.allDocsReadyEvent = new CustomEvent("allReady", {
            bubbles: true,
            cancelable: false,
            composed: true
        })


        this.key = this.kandidat_id;

        if(this.hasAttribute("dopuna"))
        {
            this.dopuna = this.getAttribute("dopuna");

            this.key = this.key + "_" + this.dopuna;
            this.GET_URL = `/do.php?form=documentsIncomplete&kandidat_id=${this.kandidat_id}&incomplete_id=${this.dopuna}`;
        }
        else
        {
            this.GET_URL = `/do.php?form=documents&kandidat_id=${this.kandidat_id}`;
        }

        this.fetchAndLoadTable();

    }


    fetchAndLoadTable()
    {
        fetch(this.GET_URL)
            .then((res)=>res.json())
            .then((data)=>{
                this.DATA = data;
                this.loadTable();
                //
                // Dispatch event saying all documents are ready
                if(this.allDocumentsReady(data))
                    this.dispatchEvent(this.allDocsReadyEvent);

        });
    }

    getNewData()
    {
        fetch(this.GET_URL)
            .then((res)=>res.json())
            .then((data)=>{
                // Set data
                this.DATA = data;

                // Reload table
                $(`#documentsTable${this.key}`, this.shadowRoot).DataTable().clear().rows.add(this.DATA).draw();

                // Dispatch event saying all documents are ready
                if(this.allDocumentsReady(data))
                    this.dispatchEvent(this.allDocsReadyEvent);
        });
    }

    allDocumentsReady(documents)
    {
        return documents.length > 0 && documents.every((doc) => doc.statuses.spreman !== null);
    }


    loadTable()
    {
        $(`#documentsTable${this.key}`, this.shadowRoot).DataTable({
            data: this.DATA,
            columns: this.COLUMNS,
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
                "emptyTable": "Nema dokumenata za ispis"
            }
        });
    }
    connectedCallback() {
        this.attachShadow({mode: 'open'});

        const table = document.createElement("table");
        table.setAttribute("class",         "hover row-border");
        table.setAttribute("style",         "white-space:nowrap; width: 70%");
        table.setAttribute("id",            `documentsTable${this.key}`);

        const modal = document.createElement("div");
        modal.setAttribute("class",         "modal material-modal material-modal_success fade");
        modal.setAttribute("id",            "changeDocumentStatusModal");

        /* MODAL HEADER */
        const modalHeader = document.createElement("div");
        modalHeader.setAttribute("class",   "modal-header material-modal__header");

        const closeModalButton = document.createElement("button");
        closeModalButton.setAttribute("class",          "close material-modal__close");
        closeModalButton.setAttribute("id",             "closeStatusModal");
        closeModalButton.setAttribute("data-dismiss",   "modal");
        closeModalButton.textContent = '×';

        const modalTitle = document.createElement("h4");
        modalTitle.setAttribute("class",        "modal-title material-modal__title");
        modalTitle.setAttribute("id",           "statusModalTitle");

        modalHeader.append(closeModalButton, modalTitle);

        /* MODAL BODY */
        const modalBody = document.createElement("div");
        modalBody.setAttribute("class",     "modal-body material-modal__body");
        modalBody.setAttribute("id",        "statusModalBody");

        /* MODAL CONTENT */
        const modalContent = document.createElement("div");
        modalContent.setAttribute("class",     "modal-content material-modal__content");

        modalContent.append(modalHeader, modalBody);

        /* MODAL DIALOG */
        const modalDialog = document.createElement("div");
        modalDialog.setAttribute("class",     "modal-dialog");
        modalDialog.append(modalContent);

        /* MODAL */
        modal.append(modalDialog);
        this.modal = modal;

        const css3 = document.createElement("link");
        css3.setAttribute("href", "/css/bootstrap.css");
        css3.setAttribute("rel", "stylesheet");

        const css4 = document.createElement("link");
        css4.setAttribute("href", `/css/style.css?${(new Date()).getTime()}`);
        css4.setAttribute("rel", "stylesheet");

        const css1 = document.createElement("link");
        css1.setAttribute("href", "/css/jquery.dataTables.min.css");
        css1.setAttribute("rel", "stylesheet");

        const css2 = document.createElement("link");
        css2.setAttribute("href", "/css/responsive.dataTables.min.css");
        css2.setAttribute("rel", "stylesheet");

        const style = document.createElement("style");
        style.textContent = `
            span{
                cursor: pointer !important;
                user-select: none !important;
            }
            a{
                user-select: none !important;
            }
            .a6_tooltip {
            position: relative;
            display: inline-block;
            }

            /* Tooltip text */
            .a6_tooltip .a6_tooltip_content {
                font-family: "Open Sans", sans-serif;
                pointer-events: none;
                user-select: none;
                visibility: hidden;
                opacity: 0;
                transition: opacity 0.2s ease;
                width: max-content;
                max-width: 400px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items:center;
                box-shadow: 1px 1px 5px rgba(0,0,0,0.5);
                text-align: center;
                background-color: white;
                white-space: normal;
                
                padding: 15px;
                border-radius: 6px;

                /* Position the tooltip text - see examples below! */
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translate(-50%,0);
                z-index: 1;
            }
            .a6_tooltip .a6_tooltip_content::after {
            content: " ";
            position: absolute;
            top: 100%; /* At the bottom of the tooltip */
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: white transparent transparent transparent;
            }

            /* Show the tooltip text when you mouse over the tooltip container */
            .a6_tooltip:hover .a6_tooltip_content {
                visibility: visible;
                opacity: 1.0;
            }

        `;

        this.shadowRoot.append(table, modal, style, css1, css2, css3, css4);

    }
    createCell = (td, cellData, rowData, row, col) =>
    {
        let currentColumnName = this.COLUMNS[col].data;

        if(rowData.statuses.nijeProsaoProvjeru)
        {
            let span = this.renderFailedStatus(cellData,rowData,row,col);
            $(td).innerHTML = "";
            $(td).append(span);
            return ;
        }

        // Završen?
        if(cellData)
        {
            let span = this.renderFinishedStatus(cellData, rowData, row, col);
            $(td).innerHTML = "";
            $(td).append(span);
            return ;
        }

        if (this.buttonShouldBeDisabled(currentColumnName, rowData))
        {
            let span = this.renderDisabledStatus(currentColumnName, rowData.zaduzeni);
            $(td).innerHTML = "";
            $(td).append(span);
            return;
        }
        else
        {
            let span = this.renderEnabledStatus(row, col);
            $(td).innerHTML = "";
            $(td).append(span);
        }
    }
    renderEnabledStatus = (row, col) =>
    {
        const span = document.createElement("span");
        span.setAttribute("class", "label label-danger material-label material-label_danger main-container__column");
        span.addEventListener("mouseenter", (event) => this.labelMouseEnter(span));
        span.addEventListener("mouseleave", (event) => this.labelMouseLeave(span));
        span.addEventListener("click", (event) => this.check(row,col));
        span.textContent = "🛇";
        return span;

    }
    renderDisabledStatus = (currentColumnName, zaduzeni) => 
    {
        if(currentColumnName === "statuses.poslan" && zaduzeni === "Poslodavac")
        {
        return `<div class="a6_tooltip">
                    <span class="label label-default material-label material-label_default main-container__column">
                        🛇
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
                    🛇
                </span>`
    }
    buttonShouldBeDisabled = (currentColumnName, row) => 
    {
        return  (currentColumnName === "statuses.provjeren"         && row.statuses.uploadan        === null) ||
                (currentColumnName === "statuses.poslan"            && row.statuses.cekamoOriginal  === null) ||
                (currentColumnName === "statuses.imamoOriginal"     && row.statuses.poslan          === null) ||
                (currentColumnName === "statuses.spreman"           && row.statuses.imamoOriginal   === null);
    }
    
    devalidateContract = async(id) =>
    {
        let result = confirm("Da li ste sigurni da želite devalidirati dokument?");
        if (result == true) {
            
            await fetch(`/do.php?form=devalidate_document&document_id=${id}`).then(() => {
                this.getNewData();
            });
            

        } else {
        }
        
    }
    renderFinishedStatus = (cellData, rowData, row, col) => 
    {
        let currentColumnName = this.COLUMNS[col].data;
        let commentHtml = rowData.statuses[currentColumnName.split(".")[1]].comment.length > 0 ? 
                                `<hr style='margin: 2px' /> 
                                <p style='font-size: 16px; margin: 0'><b>Komentar</b></p> 
                                ${rowData.statuses[currentColumnName.split(".")[1]].comment}` : 
                                "" ;
        let devalidate_span = '';
        // Uploadan
        if(col == 2)
        {
            // console.log(rowData);
            if(rowData.name == "Ugovor o radu" && (this.kandidat_status == 12 || this.kandidat_status == 15 || this.kandidat_status == 18 ) ){
                devalidate_span = `<br><br><button onclick="document.querySelector('#procesOdlaskaChecklist').devalidateContract(${rowData.id})" class="label label-danger material-label material-label_danger main-container__column">Devalidiraj dokument</button>`
            }
            // console.log(asdasdasd);
            return `
                <div class="a6_tooltip">
                    <a target="_blank" href="${rowData.filename}">
                        <span class="label label-success material-label material-label_success main-container__column">
                            🗎
                        </span>
                    </a>
                    <div style="pointer-events: unset" class="a6_tooltip_content">
                        <div style="min-width: 200px">
                            <div style="display: flex; justify-content: space-between">
                                <p style="font-size: 16px; margin: 0">${this.COLUMNS[col].title}</p>
                                <div style="width: 50px"></div>
                                <p style="font-size: 16px; margin: 0">${new Date(rowData.statuses[currentColumnName.split(".")[1]].date).toISOString().split("T")[0].split("-").reverse().join(".")}</p>
                            </div>
                            <a target="_blank" href="${rowData.filename}">
                                Otvori dokument
                            </a>
                            ${commentHtml}
                            
                            ${devalidate_span}
                            
                        </div>
                    </div>
                </div>
                    `
        }
        return  `<div class="a6_tooltip">
                    <span class="label label-success material-label material-label_success main-container__column">
                        ✓
                    </span>
                    <div class="a6_tooltip_content">
                        <div style="min-width: 200px">
                            <div style="display: flex; justify-content: space-between">
                                <p style="font-size: 16px; margin: 0">${this.COLUMNS[col].title}</p>
                                <div style="width: 50px"></div>
                                <p style="font-size: 16px; margin: 0">${new Date(rowData.statuses[currentColumnName.split(".")[1]].date).toISOString().split("T")[0].split("-").reverse().join(".")}</p>
                            </div>
                            ${commentHtml}
                        </div>
                    </div>
                </div>
                `
    }
    renderFailedStatus = (cellData, rowData, row, col) =>
    {
        if(col == 2)
        {
            var container = document.createDocumentFragment();
            var e_0 = document.createElement("div");
            var e_1 = document.createElement("div");
            e_1.setAttribute("class", "a6_tooltip");
            var e_2 = document.createElement("span");
            e_2.addEventListener("click", () => this.check(row,2));
            e_2.setAttribute("style", "cursor: pointer");
            e_2.setAttribute("class", "label label-warning material-label material-label_warning main-container__column");
            e_2.textContent = "🗎";
            e_1.appendChild(e_2);
            var e_4 = document.createElement("div");
            e_4.setAttribute("class", "a6_tooltip_content");
            e_4.setAttribute("style", "bottom: 120%");
            var e_5 = document.createElement("div");
            e_5.setAttribute("style", "min-width: 200px");
            var e_6 = document.createElement("div");
            e_6.setAttribute("style", "display: flex; justify-content: center");
            e_6.appendChild(document.createTextNode("\nUploadajte novu verziju dokumenta.\n"));
            e_5.appendChild(e_6);
            e_4.appendChild(e_5);
            e_1.appendChild(e_4);
            e_0.appendChild(e_1);
            container.appendChild(e_0);

            return container;
        }
        if(col == 3)
        {
            let commentHtml = rowData.statuses.nijeProsaoProvjeru.comment.length > 0 ? 
                                    `<hr style='margin: 2px' /> 
                                    <p style='font-size: 16px; margin: 0'><b>Komentar</b></p> 
                                    ${rowData.statuses.nijeProsaoProvjeru.comment}` : 
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
                                    <p style="font-size: 16px; margin: 0">${new Date(rowData.statuses.nijeProsaoProvjeru.date).toISOString().split("T")[0].split("-").reverse().join(".")}</p>
                                </div>
                                <a target="_blank" href="${rowData.filename}">
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
    check = (row, column) =>
    {
        const doc = this.DATA[row];
        const status = this.COLUMNS[column].data;
        const kandidat_id = this.kandidat_id;
        const nalog_id = doc.nalog_id;

        const employee_id = this.employee_id;
        const nrd_id = doc.nrd_id;
        const crd_id = doc.crd_id;
        const doc_id = doc.id;


        switch(column)
        {
            case 2:
                // Uploadovan
                this.modal.querySelector("#statusModalTitle").innerHTML = `Upload dokumenta - <em>${doc.name}</em>`;

                var container = document.createDocumentFragment();
                var e_0 = document.createElement("div");
                var e_1 = document.createElement("form");
                e_1.setAttribute("class", "form");
                e_1.setAttribute("id", `uploadForm${row}`);
                e_1.addEventListener("submit", (e) => {this.changeStatus(row, status); e.preventDefault(); return false;});
                e_1.setAttribute("style", "display:flex; flex-direction: column; align-items:center");
                e_1.setAttribute("enctype", "multipart/form-data");
                var e_2 = document.createElement("div");
                e_2.setAttribute("class", "fileinput fileinput-new");
                e_2.setAttribute("data-provides", "fileinput");
                var e_3 = document.createElement("span");
                e_3.setAttribute("class", "btn btn-default btn-file");
                var e_4 = document.createElement("span");
                e_4.setAttribute("class", "fileinput-new");
                e_4.appendChild(document.createTextNode("Izaberi dokument"));
                e_3.appendChild(e_4);
                var e_5 = document.createElement("span");
                e_5.setAttribute("class", "fileinput-exists");
                e_5.appendChild(document.createTextNode("Promijeni"));
                e_3.appendChild(e_5);
                var e_6 = document.createElement("input");
                e_6.setAttribute("type", "file");
                e_6.setAttribute("name", "document");
                e_6.setAttribute("id", `pickDocument${row}`);
                e_6.setAttribute("required", "");
                e_3.appendChild(e_6);
                e_2.appendChild(e_3);
                var e_7 = document.createElement("i");
                e_7.setAttribute("class", "fa fa-info-circle fa-lg idk_margin_left10");
                e_7.setAttribute("data-toggle", "tooltip");
                e_7.setAttribute("data-placement", "right");
                e_7.setAttribute("title", "Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png");
                e_7.setAttribute("aria-hidden", "true");
                e_2.appendChild(e_7);
                var e_8 = document.createElement("span");
                e_8.setAttribute("class", "fileinput-filename");
                e_2.appendChild(e_8);
                var e_9 = document.createElement("a");
                e_9.setAttribute("href", "#");
                e_9.setAttribute("class", "close fileinput-exists");
                e_9.setAttribute("data-dismiss", "fileinput");
                e_9.setAttribute("style", "float: none");
                e_9.appendChild(document.createTextNode("×"));
                e_2.appendChild(e_9);
                e_1.appendChild(e_2);
                var e_10 = document.createElement("textarea");
                e_10.setAttribute("style", "margin-bottom:15px");
                e_10.setAttribute("class", "form-control material-input material-textarea");
                e_10.setAttribute("name", "statusKomentar");
                e_10.setAttribute("id", "statusKomentar");
                e_10.setAttribute("placeholder", "Komentar");
                e_10.setAttribute("rows", "6");
                e_1.appendChild(e_10);
                var e_11 = document.createElement("div");
                e_11.setAttribute("class", "form-group");
                var e_12 = document.createElement("input");
                e_12.setAttribute("class", "btn btn-primary material-btn material-btn_primary");
                e_12.setAttribute("id", `uploadDocument${row}`);
                e_12.setAttribute("type", "submit");
                e_12.setAttribute("value", "Upload");
                e_11.appendChild(e_12);
                e_1.appendChild(e_11);
                e_0.appendChild(e_1);
                container.appendChild(e_0);

                this.modal.querySelector("#statusModalBody").innerHTML = "";
                this.modal.querySelector("#statusModalBody").appendChild(container);

                //$("#openDocumentStatusModal", this.shadowRoot).click();
                $(this.modal).modal("show");

                break;
            case 3:
                this.modal.querySelector("#statusModalTitle").innerHTML = `${doc.name} - <em>${this.COLUMNS[column].title}</em>`;
                var container = document.createDocumentFragment();
                var e_0 = document.createElement("div");
                var e_1 = document.createElement("form");
                e_1.setAttribute("class", "form");
                e_1.setAttribute("id", `uploadForm${row}`);
                e_1.addEventListener("submit", (e) => {this.changeStatus(row, status); e.preventDefault(); return false;});
                e_1.setAttribute("style", "display:flex; flex-direction: column; align-items:center");
                e_1.setAttribute("enctype", "multipart/form-data");
                var e_2 = document.createElement("div");
                e_2.setAttribute("style", "display: flex; width: 80%; justify-content:space-between");
                var e_3 = document.createElement("label");
                e_3.setAttribute("checked", "");
                e_3.setAttribute("class", "main-container__column material-radio-group material-radio-group_success");
                e_3.setAttribute("for", "provjeraStatusDa");
                var e_4 = document.createElement("input");
                e_4.setAttribute("type", "radio");
                e_4.setAttribute("checked", "");
                e_4.setAttribute("name", "provjeraStatus");
                e_4.setAttribute("id", "provjeraStatusDa");
                e_4.setAttribute("class", "material-radiobox");
                e_4.setAttribute("value", "6");
                e_3.appendChild(e_4);
                var e_5 = document.createElement("span");
                e_5.setAttribute("class", "material-radio-group__element material-radio-group__check-radio");
                e_3.appendChild(e_5);
                var e_6 = document.createElement("span");
                e_6.setAttribute("class", "material-radio-group__element material-radio-group__caption");
                e_6.appendChild(document.createTextNode("Dokument prošao provjeru"));
                e_3.appendChild(e_6);
                e_2.appendChild(e_3);
                var e_7 = document.createElement("label");
                e_7.setAttribute("class", "main-container__column material-radio-group material-radio-group_danger");
                e_7.setAttribute("for", "provjeraStatusNe");
                var e_8 = document.createElement("input");
                e_8.setAttribute("type", "radio");
                e_8.setAttribute("name", "provjeraStatus");
                e_8.setAttribute("id", "provjeraStatusNe");
                e_8.setAttribute("class", "material-radiobox");
                e_8.setAttribute("value", "21");
                e_7.appendChild(e_8);
                var e_9 = document.createElement("span");
                e_9.setAttribute("class", "material-radio-group__element material-radio-group__check-radio");
                e_7.appendChild(e_9);
                var e_10 = document.createElement("span");
                e_10.setAttribute("class", "material-radio-group__element material-radio-group__caption");
                e_10.appendChild(document.createTextNode("Dokument pao provjeru"));
                e_7.appendChild(e_10);
                e_2.appendChild(e_7);
                e_1.appendChild(e_2);
                var e_11 = document.createElement("textarea");
                e_11.setAttribute("style", "margin-bottom:15px");
                e_11.setAttribute("class", "form-control materail-input material-textarea");
                e_11.setAttribute("name", "statusKomentar");
                e_11.setAttribute("id", "statusKomentar");
                e_11.setAttribute("placeholder", "Komentar");
                e_11.setAttribute("rows", "6");
                e_1.appendChild(e_11);
                var e_12 = document.createElement("div");
                e_12.setAttribute("class", "form-group");
                var e_13 = document.createElement("input");
                e_13.setAttribute("class", "btn btn-primary material-btn material-btn_primary");
                e_13.setAttribute("id", `uploadDocument${row}`);
                e_13.setAttribute("type", "submit");
                e_13.setAttribute("value", "Spremi");
                e_12.appendChild(e_13);
                e_1.appendChild(e_12);
                e_0.appendChild(e_1);
                container.appendChild(e_0);

                this.modal.querySelector("#statusModalBody").innerHTML = "";
                this.modal.querySelector("#statusModalBody").appendChild(container);

                //$("#openDocumentStatusModal", this.shadowRoot).click();
                $(this.modal).modal("show");
                break;
            default:
                this.modal.querySelector("#statusModalTitle").innerHTML = `${doc.name} - <em>${this.COLUMNS[column].title}</em>`;

                var container = document.createDocumentFragment();
                var e_0 = document.createElement("div");
                var e_1 = document.createElement("form");
                e_1.setAttribute("class", "form");
                e_1.setAttribute("id", `uploadForm${row}`);
                e_1.addEventListener("submit", (e) => {this.changeStatus(row, status); e.preventDefault(); return false;});
                e_1.setAttribute("style", "display:flex; flex-direction: column; align-items:center");
                e_1.setAttribute("enctype", "multipart/form-data");
                var e_2 = document.createElement("textarea");
                e_2.setAttribute("style", "margin-bottom:15px");
                e_2.setAttribute("class", "form-control materail-input material-textarea");
                e_2.setAttribute("name", "statusKomentar");
                e_2.setAttribute("id", "statusKomentar");
                e_2.setAttribute("placeholder", "Komentar");
                e_2.setAttribute("rows", "6");
                e_1.appendChild(e_2);
                var e_3 = document.createElement("div");
                e_3.setAttribute("class", "form-group");
                var e_4 = document.createElement("input");
                e_4.setAttribute("class", "btn btn-primary material-btn material-btn_primary");
                e_4.setAttribute("id", `uploadDocument${row}`);
                e_4.setAttribute("type", "submit");
                e_4.setAttribute("value", "Spremi");
                e_3.appendChild(e_4);
                e_1.appendChild(e_3);
                e_0.appendChild(e_1);
                container.appendChild(e_0);

                this.modal.querySelector("#statusModalBody").innerHTML = "";
                this.modal.querySelector("#statusModalBody").appendChild(container);

                //$("#openDocumentStatusModal", this.shadowRoot).click();
                $(this.modal).modal("show");

                break;
        }
    }
    labelMouseEnter = (e) =>
    {
        e.innerHTML='✎';
        e.classList.replace("label-danger", "label-warning");
        e.classList.replace("material-label_danger", "material-label_warning");
    }

    labelMouseLeave = (e) =>
    {
        let labelInnerHtmlDefault = '🛇';
        e.innerHTML=labelInnerHtmlDefault;
        e.classList.replace("label-warning", "label-danger");
        e.classList.replace("material-label_warning", "material-label_danger");
    }
    changeStatus = async (row, statusName) =>
    {
        document.querySelector(`#uploadDocument${row}`).setAttribute("disabled", true);
        const doc = this.DATA[row];
        const kandidat_id = this.kandidat_id;
        const nalog_id = doc.nalog_id;
        const doc_id = doc.id;

        const employee_id = this.employee_id;
        const nrd_id = doc.nrd_id || "";
        const crd_id = doc.crd_id || "";

        const statusComment = document.querySelector(`#statusKomentar`).value;

        let data = new FormData();


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
            this.getNewData();
            document.querySelector(`#uploadDocument${row}`).setAttribute("disabled", false);
            $(this.modal).modal("toggle");
        });
    };

    COLUMNS = [
            {
                data: "name",
                title: "Naziv",
            },
            {
                data: "zaduzeni",
                title: "Zadužen",
            },
            {
                data: "statuses.uploadan",
                title: "Uploadan",
                createdCell: this.createCell,
                render: ()=>{ return "";}
            },
            {
                data: "statuses.provjeren",
                title: "Provjeren",
                createdCell: this.createCell,
                render: ()=>{ return "";}
            },
            {
                data: "statuses.poslan",
                title: "Poslan original",
                createdCell: this.createCell,
                render: ()=>{ return "";}
            },
            {
                data: "statuses.imamoOriginal",
                title: "Imamo original",
                createdCell: this.createCell,
                render: ()=>{ return "";}

            },
            {
                data: "statuses.spreman",
                title: "Spreman",
                createdCell: this.createCell,
                render: ()=>{ return "";}
            },
        ];
};

customElements.define('document-checklist', DocumentChecklist);

