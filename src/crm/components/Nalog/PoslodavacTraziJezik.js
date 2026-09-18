import { LitElement, css, html, when, nothing } from "/js/lit-all.min.js";

export class PoslodavacTraziJezik extends LitElement {
    static get properties() {
        return {
            nalogId: {},
            poslodavacTraziJezikNew: {},
            poslodavacTraziJezikOld: {},
            isEditing: {},
        };
    };

    constructor() {
        super();
        this.nalogId = null;
        this.poslodavacTraziJezikNew = null;
        this.poslodavacTraziJezikOld = null;
        this.isEditing = false;
    };

    createRenderRoot() {
        return this;
    };

    render() {
        return html`
            <div style="margin-bottom: 10px; margin-top: 10px;">
                <div class="row">
                    <strong class="col-sm-4 text-right">Poslodavac traži jezik</strong>
                    <div class="col-sm-7" style="display: ${this.isEditing ? "none" : "block"}">
                        <div class="row">
                            <div class="col-sm-8">
                                <span class="label label-${this.poslodavacTraziJezikNew == 1 ? 'success' : 'warning'} material-label material-label_${this.poslodavacTraziJezikNew == 1 ? 'success' : 'warning'} main-container__column text-center">${this.poslodavacTraziJezikNew == 1 ? 'DA' : 'NE'}</span>
                            </div>
                            <div class="col-sm-4 text-right">
                                <button class = "btn btn-success" style="padding: 0px 15px;"  @click=${this.edit}>
                                    Uredi
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7" style="display: ${this.isEditing ? "block" : "none"}">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <select class="selectpicker" id="component_poslodavac_trazi_jezik" title="Odaberite" name="component_poslodavac_trazi_jezik" @change=${(e) => {this.poslodavacTraziJezikNew = e.target.value;}}>
                                        <option value="0" ?selected="${this.poslodavacTraziJezikNew == 0 || nothing}">
                                            NE
                                        </option>
                                        <option value="1" ?selected="${this.poslodavacTraziJezikNew == 1 || nothing}">
                                            DA
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6 text-center">
                                    <button class = "btn btn-danger" style="padding: 0px 15px;"  @click=${this.close}>
                                        Odustani
                                    </button>
                                </div>
                                <div class="col-sm-6 text-center">
                                    <button class = "btn btn-success" style="padding: 0px 15px;"  @click=${this.save}>
                                        Spremi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1 text-right"><i class="fa fa-info-circle" aria-hidden="true" title="Parametar koji određuje da li poslodavac traži jezik bez obzira na ishod nostrifikacije potpuna/evaluacija."></i></div>
                </div>
            </div>
        `;
    };

    edit() {
        this.isEditing = true;
        $("#component_poslodavac_trazi_jezik").val(this.poslodavacTraziJezikNew).selectpicker("refresh");
    };

    close() {
        this.isEditing = false;
        this.poslodavacTraziJezikNew = this.poslodavacTraziJezikOld;
        $("#component_poslodavac_trazi_jezik").val(this.poslodavacTraziJezikNew).selectpicker("refresh");
    };
    
    save() {
        if (this.poslodavacTraziJezikNew != this.poslodavacTraziJezikOld && this.nalogId != null) {
            let formData = new FormData();
            formData.append("nalog_id", this.nalogId);
            formData.append("nalog_poslodavac_trazi_jezik", this.poslodavacTraziJezikNew);

            fetch("/ajax_data.php?page=editPoslodavacTraziJezik", {
                method: "POST",
                body: formData,
            }).then((response) => {
                if (response.ok) {
                    this.isEditing = false;
                    this.poslodavacTraziJezikOld = this.poslodavacTraziJezikNew;
                    $("#component_poslodavac_trazi_jezik").val(this.poslodavacTraziJezikNew).selectpicker("refresh");
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                }
            });
        } else {
            this.close();
        }
    }
}

customElements.define("poslodavac-trazi-jezik", PoslodavacTraziJezik);