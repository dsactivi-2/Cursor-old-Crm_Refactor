import { LitElement, css, html, when, nothing } from "/js/lit-all.min.js";

export class PoslodavacKoristiPP extends LitElement {
    static get properties() {
        return {
            nalogId: {},
            poslodavacKoristiPPNew: {},
            poslodavacKoristiPPOld: {},
            isEditing: {},
        };
    };

    constructor() {
        super();
        this.nalogId = null;
        this.poslodavacKoristiPPNew = null;
        this.poslodavacKoristiPPOld = null;
        this.isEditing = false;
    };

    createRenderRoot() {
        return this;
    };

    render() {
        return html`
            <div style="margin-bottom: 10px; margin-top: 10px;">
                <div class="row">
                    <strong class="col-sm-4 text-right">Poslodavac koristi JobSoft</strong>
                    <div class="col-sm-7" style="display: ${this.isEditing ? "none" : "block"}">
                        <div class="row">
                            <div class="col-sm-8">
                                <span class="label label-${this.poslodavacKoristiPPNew == 1 ? 'success' : 'warning'} material-label material-label_${this.poslodavacKoristiPPNew == 1 ? 'success' : 'warning'} main-container__column text-center">${this.poslodavacKoristiPPNew == 1 ? 'DA' : 'NE'}</span>
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
                                    <select class="selectpicker" id="component_nalog_poslodavac_koristi_pp" title="Odaberite" name="component_nalog_poslodavac_koristi_pp" @change=${(e) => {this.poslodavacKoristiPPNew = e.target.value;}}>
                                        <option value="0" ?selected="${this.poslodavacKoristiPPNew == 0 || nothing}">
                                            NE
                                        </option>
                                        <option value="1" ?selected="${this.poslodavacKoristiPPNew == 1 || nothing}">
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
                    <div class="col-sm-1 text-right"><i class="fa fa-info-circle" aria-hidden="true" title="Po ugovoru poslodavac se obavezuje na korištenje aplikacije JobSoft. Odnosno, obavezuje se na to da ako ne uradi neku akciju za nekog kandidata na istoj aplikaciji a JobStep ga pri tom obavijesti N puta, slijede ugovorom definisane akcije sa JobStep strane."></i></div>
                </div>
            </div>
        `;
    };

    edit() {
        this.isEditing = true;
        $("#component_nalog_poslodavac_koristi_pp").val(this.poslodavacKoristiPPNew).selectpicker("refresh");
    };

    close() {
        this.isEditing = false;
        this.poslodavacKoristiPPNew = this.poslodavacKoristiPPOld;
        $("#component_nalog_poslodavac_koristi_pp").val(this.poslodavacKoristiPPNew).selectpicker("refresh");
    };
    
    save() {
        if (this.poslodavacKoristiPPNew != this.poslodavacKoristiPPOld && this.nalogId != null) {
            let formData = new FormData();
            formData.append("nalog_id", this.nalogId);
            formData.append("nalog_poslodavac_koristi_pp", this.poslodavacKoristiPPNew);

            fetch("/ajax_data.php?page=editPoslodavacKoristiPP", {
                method: "POST",
                body: formData,
            }).then((response) => {
                if (response.ok) {
                    this.isEditing = false;
                    this.poslodavacKoristiPPOld = this.poslodavacKoristiPPNew;
                    $("#component_nalog_poslodavac_koristi_pp").val(this.poslodavacKoristiPPNew).selectpicker("refresh");
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

customElements.define("poslodavac-koristi-pp", PoslodavacKoristiPP);