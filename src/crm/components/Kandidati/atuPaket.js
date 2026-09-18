import { LitElement, css, html, when, nothing } from "/js/lit-all.min.js";

export class Atupaket extends LitElement {
    static get properties() {
        return {
            kandidatId: {},
            paket: {},
            paketOld: {},
            isEditing: {},
        };
    }

    constructor() {
        super();
        this.kandidatId = null;
        this.paket = null;
        this.paketOld = null;
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
                                <select class="selectpicker" id="paket" title="Odaberite" name="paket" @change=${(e) => {this.paket = e.target.value;}}>
                                    <option value="0" disabled ?selected="${this.paket == 0 || nothing}">
                                        Odaberi
                                    </option>
                                    <option value="1" data-subtext="Oglašavanje, slanje profila, prikupljanje dokumentacije, aplikacija za vizu, cijena 800 EUR." ?selected="${this.paket == 1 || nothing}">
                                        Paket 1
                                    </option>
                                    <option value="2" data-subtext="Oglašavanje, slanje profila, prikupljanje dokumentacije, kontakt sa ATU zbog nostrifikacije i vize, cijena 1300 EUR." ?selected="${this.paket == 2 || nothing}">
                                        Paket 2
                                    </option>
                                    <option value="3" data-subtext="Oglašavanje, slanje profila, prikupljanje dokumentacije, kontakt sa ATU zbog nostrifikacije i vize, potraga za stanom, prijava, cijena 2500 EUR." ?selected="${this.paket == 3 || nothing}">
                                        Paket 3
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
                            this.renderAtuPaket()
                        }
                    </div>
                    <div class = "col-xs-4" style="display: ${this.isEditing ? "none" : "block"}">
                        <div class = "row">
                            <div class = "col-xs-12 text-right">
                                <button class = "btn btn-success" style="padding: 0px 15px;" @click=${this.edit}>
                                    Uredi paket
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
        this.paket = this.paketOld;
        $("#paket").val(this.paket).selectpicker("refresh");
    }

    renderAtuPaket() {
        let resultRender = "";

        if (this.paket == 1){
            resultRender = "Paket 1 (Oglašavanje, slanje profila, prikupljanje dokumentacije, aplikacija za vizu, cijena 800 EUR.)";
        } else if (this.paket == 2){
            resultRender = "Paket 2 (Oglašavanje, slanje profila, prikupljanje dokumentacije, kontakt sa ATU zbog nostrifikacije i vize, cijena 1300 EUR.)";
        } else if (this.paket == 3){
            resultRender = "Paket 3 (Oglašavanje, slanje profila, prikupljanje dokumentacije, kontakt sa ATU zbog nostrifikacije i vize, potraga za stanom, prijava, cijena 2500 EUR.)";
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
        formData.append("paketAtu", this.paket);

        fetch("/do.php?form=atuPaketEdit", {
            method: "POST",
            body: formData,
        }).then((response) => {
            if (response.ok) {
                this.paketOld = this.paket;
            }
            this.isEditing = false;
        });
    }

}

customElements.define("kandidat-atupaket", Atupaket);