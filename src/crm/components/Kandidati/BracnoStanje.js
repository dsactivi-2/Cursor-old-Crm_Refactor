import { LitElement, css, html, when, nothing } from "/js/lit-all.min.js";

export class BracnoStanje extends LitElement {
    static get properties() {
        return {
            kandidatId: {},
            bracnoStanje: {},
            bracnoStanjeOld: {},
            isEditing: {},
        };
    }

    constructor() {
        super();
        this.kandidatId = null;
        this.bracnoStanje = null;
        this.bracnoStanjeOld = null;
        this.isEditing = false;
    }

    createRenderRoot() {
        return this;
    }

    render() {
        return html`
            <div>
                <div class="row">
                    <div class = "col-xs-12" style="display: ${this.isEditing ? "block" : "none"}">
                        <div class="form-group">
                            <div class="">
                                <select class="selectpicker" id="bracnoStanje" title="Odaberite" name="bracnoStanje" @change=${(e) => {this.bracnoStanje = e.target.value;}}>
                                    <option value="0" disabled ?selected="${this.bracnoStanje == 0 || nothing}">
                                        Nepoznato
                                    </option>
                                    <option value="1" data-subtext="Ledig" ?selected="${this.bracnoStanje == 1 || nothing}">
                                        Neoženjen/Neudana
                                    </option>
                                    <option value="2" data-subtext="Verheiratet" ?selected="${this.bracnoStanje == 2 || nothing}">
                                        Oženjen/Udana
                                    </option>
                                    <option value="3" data-subtext="Verwitwet" ?selected="${this.bracnoStanje == 3 || nothing}">
                                        Udovac/Udovica
                                    </option>
                                    <option value="4" data-subtext="Geschieden" ?selected="${this.bracnoStanje == 4 || nothing}">
                                        Razveden/Razvedena
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class = "row">
                                <div class = "col-xs-6 text-center">
                                    <button class = "btn btn-success" style="padding: 0px 15px;" @click=${this.save}>
                                        Spremi
                                    </button>
                                </div>
                                <div class = "col-xs-6 text-center">
                                    <button class = "btn btn-danger" style="padding: 0px 15px;" @click=${this.close}>
                                        Odustani
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class = "col-xs-8" style="display: ${this.isEditing ? "none" : "block"}">
                        ${
                            this.renderBracnoStanje()
                        }
                    </div>
                    <div class = "col-xs-4" style="display: ${this.isEditing ? "none" : "block"}">
                        <div class = "row">
                            <div class = "col-xs-12 text-right">
                                <button class = "btn btn-success" style="padding: 0px 15px;" @click=${this.edit}>
                                    Uredi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    edit() {
        this.isEditing = true;
    }

    close() {
        this.isEditing = false;
        this.bracnoStanje = this.bracnoStanjeOld;
        $("#bracnoStanje").val(this.bracnoStanje).selectpicker("refresh");
    }

    renderBracnoStanje() {
        let resultRender = "";

        if (this.bracnoStanje == 1){
            resultRender = "Neoženjen/Neudana";
        } else if (this.bracnoStanje == 2){
            resultRender = "Oženjen/Udana";
        } else if (this.bracnoStanje == 3){
            resultRender = "Udovac/Udovica";
        } else if (this.bracnoStanje == 4){
            resultRender = "Razveden/Razvedena";
        } else {
            resultRender = "Nepoznato";
        }

        return html`
            ${resultRender}
        `;
    }

    save() {
        let formData = new FormData();
        formData.append("kandidatId", this.kandidatId);
        formData.append("bracnoStanje", this.bracnoStanje);

        fetch("/do.php?form=bracnoStanjeKandidatEdit", {
            method: "POST",
            body: formData,
        }).then((response) => {
            if (response.ok) {
                this.bracnoStanjeOld = this.bracnoStanje;
            }
            this.isEditing = false;
        });
    }

}

customElements.define("bracno-stanje", BracnoStanje);