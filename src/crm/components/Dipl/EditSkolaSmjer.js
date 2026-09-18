import { LitElement, html, nothing } from "/js/lit-all.min.js";

export class EditSkolaSmjer extends LitElement {
    static get properties() {
        return {
            kandidatId: {},
            skolaIdOld: {},
            skolaIdNew: {},
            smjerIdOld: {},
            smjerIdNew: {},
            idUstanova: {},
            isEditing: {},
            schools: {},
            directions: {},
            isModified: {},
            isProcessing: {},
        };
    }

    constructor() {
        super();
        this.kandidatId = null;
        this.skolaIdOld = null;
        this.skolaIdNew = null;
        this.smjerIdOld = null;
        this.smjerIdNew = null;
        this.idUstanova = null;
        this.isEditing = false;
        this.schools = null;
        this.directions = null;
        this.isModified = false;
        this.isProcessing = true;
    }

    selectpickerRefreshFunction(vr){
        $("#"+vr).selectpicker("refresh");
    }

    createRenderRoot() {
        return this;
    }

    firstUpdated() { 
        this.getSchools();
        this.getDirection();
    }

    updated(changedProperties) {
        if (changedProperties.has('schools')) {
            this.selectpickerRefreshFunction("schoolEdit");
            console.log("Update 1");
        }

        if (changedProperties.has('directions')) {
            this.selectpickerRefreshFunction("directionEdit");
            console.log("Update 2");
        }
    }

    render() {
        return html`
            <div class="row">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-12">
                            <h5 style = "font-weight: bold;">
                                <div class="row">
                                    <div class="col-xs-8">	
                                        <i class="fa fa-graduation-cap" style = "margin-right: 10px;" aria-hidden="true"></i>Edit škole/smjera kandidata
                                    </div>
                                    <div class="col-xs-4">
                                        <button style="float: right; display: ${this.isEditing === false ? "block" : "none"};" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" @click=${this.edit}>
                                            <i class="fa fa-pencil-square" aria-hidden="true"></i>
                                            Edit
                                        </button>
                                        <button style="float: right; display: ${this.isEditing === true ? "block" : "none"};" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column" @click=${this.close}>
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            Odustani
                                        </button>
                                    </div>
                                </div>
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="row" style="display: ${this.isModified === true ? "block" : "none"};">
                                <div class="col-xs-1">
                                    <i class="fa fa-info-circle fa-1x" aria-hidden="true" title="Informacija koja pokazuje da li navedena ustanova vrši obradu označene škole i smjera. Na stranici 'Ustanove' u rubrici 'Zanimanja' se može provjeriti koje škole i smjerove ista obrađuje. U slučaju izbora škole i smjera koji nije označen na spomenutoj stranici, spremanjem odabrane škole i smjera, automatski se unosi da je ustanova za koju je kandidat vezan nadležna za odabranu školu i smjer."></i>
                                </div>
                                <div class="col-xs-3 text-right" style = "font-weight: bold; ">
                                    Ustanova vrši obradu:
                                </div>
                                <div class="col-xs-8">
                                    <span style="padding: 0px 16px; display: ${this.isProcessing === false ? "inline-block" : "none"};" class="material-label material-label_danger main-container__column text-center">NE</span>
                                    <span style="padding: 0px 16px; display: ${this.isProcessing === true ? "inline-block" : "none"};" class="material-label material-label_success main-container__column text-center">DA</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row" style="display: ${this.isModified === true && this.isProcessing === false ? "block" : "none"};">
                                <div class="col-md-offset-2 col-sm-8">
                                    <div class="alert alert-danger text-center" style="padding: 7px;" role="alert">
                                        Spremanjem odabrane škole i smjera, izvršit će se dodavanje istih na ustanovu za koju je kandidat vezan. Odnosno, automatski se označava da je ustanova nadležna za obradu odabrane škole i smjera.
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-offset-2 col-sm-8">
                                    <div class="form-group">
                                        <div class="">
                                            <select class="selectpicker form-control" id="schoolEdit" data-live-search="true" title="Odaberite" name="schoolEdit" disabled @change=${this.changeSchool}>
                                                ${this.schools?.map((school) =>
                                                    html`
                                                        <option value="${school.id}" ?selected="${school.id === this.skolaIdNew || nothing}" data-subtext="${'DE: '+school.name_de+' TIP: '+school.type_school}">
                                                            ${school.name}
                                                        </option>
                                                    `)
                                                }
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-offset-2 col-sm-8">
                                    <div class="form-group">
                                        <div class="">
                                            <select class="selectpicker form-control" id="directionEdit" data-live-search="true" title="Odaberite" name="directionEdit" disabled @change=${this.changeDirection}>
                                                ${this.directions?.map((direction) =>
                                                    html`
                                                        <option value="${direction.id}" ?selected="${direction.id === this.smjerIdNew || nothing}" data-subtext="${'DE: '+direction.name_de}">
                                                            ${direction.name}
                                                        </option>
                                                    `)
                                                }
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row"  style="display: ${this.isModified === true ? "block" : "none"};">
                                <div class="col-md-offset-2 col-sm-8 text-center">
                                    <div class="form-group">
                                        <button class="btn material-btn material-btn-icon-success material-btn_success main-container__column" @click=${this.save}>
                                            <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                            Spremi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    edit() {
        this.isEditing = true;
        $("#schoolEdit").attr('disabled', false).selectpicker("refresh");
        $("#directionEdit").attr('disabled', false).selectpicker("refresh");
    }

    close() {
        this.skolaIdNew = this.skolaIdOld; 
        this.smjerIdNew = this.smjerIdOld;
        this.isModified = false;
        this.isEditing = false;
        this.isProcessing = true;
        this.getSchools();
        this.getDirection();

        $("#schoolEdit").attr('disabled', true).selectpicker("refresh");
        $("#directionEdit").attr('disabled', true).selectpicker("refresh");
    }

    saved(){
        this.skolaIdOld = this.skolaIdNew; 
        this.smjerIdOld = this.smjerIdNew;
        this.isModified = false;
        this.isEditing = false;
        this.isProcessing = true;
        this.getSchools();
        this.getDirection();

        $("#schoolEdit").attr('disabled', true).selectpicker("refresh");
        $("#directionEdit").attr('disabled', true).selectpicker("refresh");
    }

    save(){
        let formData = new FormData();
        formData.append("kandidatId", this.kandidatId);
        formData.append("skolaIdNew", this.skolaIdNew);
        formData.append("smjerIdNew", this.smjerIdNew);
        formData.append("skolaIdOld", this.skolaIdOld);
        formData.append("smjerIdOld", this.smjerIdOld);
        formData.append("idUstanova", this.idUstanova);
        formData.append("isProcessing", (this.isProcessing === true ? 1 : 0 ));
        
        fetch("/ajax_data.php?page=change_school_and_direction_for_component", {
            method: "POST",
            body: formData,
        }).then((response3) => {
            if (response3.ok) {
                return response3.json();
            }
        }).then((data3) => {
            if (data3 == 1) {
                this.saved();
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            }
        });
    }

    changeSchool(e){
        this.skolaIdNew = e.target.value;
        this.smjerIdNew = null;
        if ((this.skolaIdNew != this.skolaIdOld || this.smjerIdNew != this.smjerIdOld) && this.skolaIdNew != null && this.smjerIdNew != null) {
            this.isModified = true;
            this.institutionProcessing();
        } else {
            this.isModified = false;
        }
        this.getDirection();
    }

    changeDirection(e){
        this.smjerIdNew = e.target.value;
        if ((this.skolaIdNew != this.skolaIdOld || this.smjerIdNew != this.smjerIdOld) && this.skolaIdNew != null && this.smjerIdNew != null) {
            this.isModified = true;
            this.institutionProcessing();
        } else {
            this.isModified = false;
        } 
    }

    institutionProcessing(){
        if (this.skolaIdNew != null && this.smjerIdNew != null) {
            let formData = new FormData();
            formData.append("skolaIdNew", this.skolaIdNew);
            formData.append("smjerIdNew", this.smjerIdNew);
            formData.append("idUstanova", this.idUstanova);
            
            fetch("/ajax_data.php?page=get_institution_processing", {
                method: "POST",
                body: formData,
            }).then((response2) => {
                if (response2.ok) {
                    return response2.json();
                }
            }).then((data2) => {
                if (data2 == 1){
                    this.isProcessing = true;
                } else {
                    this.isProcessing = false;
                }
            });
        } else {
            this.isProcessing = false;
        }
    }

    getSchools(){
        this.schools = null;
        fetch("/ajax_data.php?page=get_schools_for_component", {
            method: "POST",
        }).then((response) => {
            if (response.ok) {
                return response.json();
            }
        }).then((data) => {
            this.schools = data;
        });
    };

    getDirection(){
        this.directions = null;
        let formData = new FormData();
        formData.append("skolaIdNew", this.skolaIdNew);

        fetch("/ajax_data.php?page=get_direction_for_component", {
            method: "POST",
            body: formData,
        }).then((response1) => {
            if (response1.ok) {
                return response1.json();
            }
        }).then((data1) => {
            console.log(data1);
            this.directions = data1;
        });
    };
}

customElements.define("edit-skola-smjer", EditSkolaSmjer);