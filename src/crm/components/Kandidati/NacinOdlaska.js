import { LitElement, css, html, when, nothing } from "/js/lit-all.min.js";

export class NacinOdlaska extends LitElement {
    static get properties() {
        return {
            candidateId: {},
            departureType: {},
            departureTypeOld: {},
            parallelAppliesWestBalkan: {},
            parallelAppliesWestBalkanOld: {},
            isEditing: {},
            enableEditing: {},
            departureStatusPrijave: {},
        };
    }

    constructor() {
        super();
        this.candidateId = null;
        this.departureType = null;
        this.departureTypeOld = null;
        this.parallelAppliesWestBalkan = null;
        this.parallelAppliesWestBalkanOld = null;
        this.isEditing = false;
        this.enableEditing = 0;
        this.departureStatusPrijave = 0;
    }

    createRenderRoot() {
        return this;
    }

    handleDepartureTypeChange(e) {
        this.departureType = e.target.value;
        if (this.departureType == 0) {
            this.parallelAppliesWestBalkan = this.parallelAppliesWestBalkanOld;
            $("#parallelAppliesWestBalkan").val(this.parallelAppliesWestBalkan).selectpicker("refresh");
        } else {
            this.parallelAppliesWestBalkan = 0;
            $("#parallelAppliesWestBalkan").val(this.parallelAppliesWestBalkan).selectpicker("refresh");
        }
    }

    render() {
        return html`
            <div class="${this.isEditing ? "panel panel-primary": ""}" style="${this.isEditing ? "margin-top:25px;": ""}">
                <div class="${this.isEditing ? "panel-heading text-center": ""}" style="display: ${this.isEditing ? "block" : "none"}">Uredi način odlaska</div>
                <div class="${this.isEditing ? "panel-body": ""}">
                    <div class="row" style="margin-top: 10px; margin-bottom: 10px;">
                        <strong class="col-sm-4 text-right">Način odlaska:</strong>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class = "col-xs-12" style="display: ${this.isEditing ? "block" : "none"}">
                                    <div class="form-group">
                                        <div class="">
                                            <select class="selectpicker" id="departureType" title="Odaberite" name="departureType" @change="${this.handleDepartureTypeChange}">
                                                <option value="0" ?selected="${this.departureType == 0 || nothing}">
                                                    Stručni kadar
                                                </option>
                                                <option value="2" ?selected="${this.departureType == 2 || nothing}">
                                                    Zapadno-balkanski sistem
                                                </option>
                                                <option value="3" ?selected="${this.departureType == 3 || nothing}">
                                                    Radno iskustvo
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class = "col-xs-8" style="display: ${this.isEditing ? "none" : "block"}">
                                    ${
                                        this.renderDepartureType()
                                    }
                                </div>
                                <div class = "col-xs-4" style="display: ${this.isEditing ? "none" : "block"}">
                                    <div class = "row">
                                        <div class = "col-xs-12 text-right">
                                            <button class = "btn ${this.departureType == 1 || this.enableEditing == 0 ? "btn-danger":"btn-success"}" style="padding: 0px 15px;"  @click=${ this.departureType == 1 || this.enableEditing == 0 ? this.noEdit : this.edit}>
                                                Uredi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px; margin-bottom: 10px; display: ${this.departureType == 0 ? "block" : "none"};">
                        <strong class="col-sm-4 text-right">Paralelno aplicira Zapadni balkan:</strong>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class = "col-xs-12" style="display: ${this.isEditing ? "block" : "none"}">
                                    <div class="form-group">
                                        <div class="">
                                            <select class="selectpicker" id="parallelAppliesWestBalkan" title="Odaberite" name="parallelAppliesWestBalkan" @change=${(e) => {this.parallelAppliesWestBalkan = e.target.value;}}>
                                                <option value="0" ?selected="${this.parallelAppliesWestBalkan == 0 || nothing}">
                                                    NE
                                                </option>
                                                <option value="1" ?selected="${this.parallelAppliesWestBalkan == 1 || nothing}">
                                                    DA
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class = "col-xs-8" style="display: ${this.isEditing ? "none" : "block"}">
                                    ${
                                        this.renderParallelAppliesWestBalkan()
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px; margin-bottom: 10px; display: ${this.isEditing ? "block" : "none"}">
                        <strong class="col-sm-4"></strong>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class = "col-xs-12">
                                    <div class = "row">
                                        <div class = "col-xs-6 text-center">
                                            <button class = "btn btn-success" style="padding: 0px 15px;" @click=${this.save}>
                                                Spremi
                                            </button>
                                        </div>
                                        <div class = "col-xs-6 text-center">
                                            <button class = "btn btn-danger" style="padding: 0px 15px;" @click=${this.closeEdit}>
                                                Odustani
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    noEdit() {
        alert("Nije moguće izvršiti editovanje načina posredovanja kandidata jer je kod kandidata označeno da je EU državljanin ili se kandidat ne nalazi na određenom statusu prijave na kojem je moguće editovanje načina odlaska!");
    }

    edit() {
        this.isEditing = true;
    }

    renderParallelAppliesWestBalkan() {
        let resultRender = '';
        let resultStyle = '';

        if (this.parallelAppliesWestBalkan == 1) {
            resultStyle = 'success';
            resultRender = 'DA';
        } else if (this.parallelAppliesWestBalkan == 0) {
            resultStyle = 'warning';
            resultRender = 'NE';
        } else {
            resultStyle = 'default';
            resultRender = 'Nepoznato';
        }

        return html`<span class='label label-${resultStyle} material-label material-label_${resultStyle} main-container__column text-center'>
            ${resultRender}
        </span>`;
    }

    renderDepartureType() {

        let resultRender = '';
        let resultStyle = '';

        if (this.departureType == 0){
            resultStyle = 'success';
            resultRender = 'Stručni kadar';
        } else if (this.departureType == 1){
            resultStyle = 'danger';
            resultRender = 'EU Kandidat';
        } else if (this.departureType == 2){
            resultStyle = 'info';
            resultRender = 'Zapadno-balkanski sistem';
        } else if (this.departureType == 3){
            resultStyle = 'primary';
            resultRender = 'Radno iskustvo';
        } else {
            resultStyle = 'default';
            resultRender = 'Nepoznato';
        }

        return html`<span class='label label-${resultStyle} material-label material-label_${resultStyle} main-container__column text-center'>
            ${resultRender}
        </span>`;
    }

    closeEdit() {
        this.isEditing = false;
        this.departureType = this.departureTypeOld;
        this.parallelAppliesWestBalkan = this.parallelAppliesWestBalkanOld;
        $("#departureType").val(this.departureType).selectpicker("refresh");
        $("#parallelAppliesWestBalkan").val(this.parallelAppliesWestBalkan).selectpicker("refresh");
    }

    setNewValue() {
        this.isEditing = false;
        this.departureTypeOld = this.departureType;
        this.parallelAppliesWestBalkanOld = this.parallelAppliesWestBalkan; 
        $("#departureType").val(this.departureType).selectpicker("refresh");
        $("#parallelAppliesWestBalkan").val(this.parallelAppliesWestBalkan).selectpicker("refresh");
    }

    save() {

        let formData = new FormData();
        formData.append("candidateId", this.candidateId);
        formData.append("departureType", this.departureType);
        formData.append("departureTypeOld", this.departureTypeOld);
        formData.append("parallelAppliesWestBalkan", this.parallelAppliesWestBalkan);
        formData.append("parallelAppliesWestBalkanOld", this.parallelAppliesWestBalkanOld);

        fetch("/ajax_data.php?page=editDepartureTypeForCandidate", {
            method: "POST",
            body: formData,
        }).then((response) => {
            this.isEditing = false;
            if (response.ok) {
                this.setNewValue();
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            } else {
                response.text().then(errorMessage => {
                    alert("Greška: " + errorMessage);
                    this.closeEdit();
                });
            }
        });

    }

};

customElements.define("nacin-odlaska", NacinOdlaska);