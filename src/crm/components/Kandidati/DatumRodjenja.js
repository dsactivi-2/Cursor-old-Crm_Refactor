import { LitElement, css, html, when } from "/js/lit-all.min.js";

export class DatumRodjenja extends LitElement { 
    static get properties() {
        return {
            datumRodjenja: {},
            kandidatId: {},
            isEditing: {},
            oldDatumRodjenja: {}
        };
    }

    constructor() {
        const date = new Date().toJSON().slice(0, 10);
        super();
        this.datumRodjenja = date;
        this.kandidatId = "";
        this.isEditing = false;
        this.oldDatumRodjenja = date;
    }

    createRenderRoot() {
        return this;
    }

    render() { 
        const dateDisable = new Date().toJSON().slice(0, 10);
        if (this.datumRodjenja === "") {

            const date = new Date().toJSON().slice(0, 10);
            this.datumRodjenja = date;
            this.oldDatumRodjenja = date;
            //console.log("Current date " + this.datumRodjenja);

        }
        /*
        console.log("");
        console.log("Render function ------------------------------");
        console.log("Kandidat ID: " + this.kandidatId);
        console.log("Datum rodjenja: " + this.datumRodjenja);
        console.log("Old datum rodjenja: " + this.oldDatumRodjenja);
        console.log("Editing: " + this.isEditing);
        console.log("Render function ------------------------------");
        console.log("");
        */

        return html`
            <div>
                <div class="row">
                    <div class = "col-xs-12" style="display: ${this.isEditing ? "block" : "none"}">
                        <div class="form-group">
                            <div class="">
                                <div class="materail-input-block materail-input-block_success">
                                    <input class="form-control materail-input" style= "height: 23px;" type="date" max="${dateDisable}" value = "${ this.datumRodjenja }" @change=${(e) => {this.datumRodjenja = e.target.value; }} name="datumRodjenjaJob" id="datumRodjenjaJob" placeholder="Unesite datum uplate rate" required>
                                    <span class="materail-input-block__line"></span>
                                </div>
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
                            this.renderDatumRodjenja() 
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
        this.datumRodjenja = this.oldDatumRodjenja;
        $("#datumRodjenjaJob").val(this.datumRodjenja);
    }
    
    save() {
        /*
        console.log("");
        console.log("Save function ------------------------------");
        console.log("Kandidat ID save: " + this.kandidatId);
        console.log("Datum rodjenja save: " + this.datumRodjenja);
        */
        const dateSave = new Date().toJSON().slice(0, 10);
        if ( this.datumRodjenja !== "" && this.datumRodjenja !== dateSave && this.kandidatId !== "") {
            
            let formData = new FormData();

            formData.append("datumRodjenjaSave", this.datumRodjenja);
            formData.append("kandidatIdSave", this.kandidatId);

            fetch(
                "/do.php?form=updateDatumRodjenjaKandidat", 
                {
                    method: "POST",
                    body: formData,
                }
            ).then(
                ( response ) => {

                    if ( !response.ok ) {
                        
                        alert("Unos u bazu nije bio uspješan!");

                    } else {

                        this.isEditing = false;
                        this.oldDatumRodjenja = this.datumRodjenja;

                    }

                }

            );

        } else {

            alert("Provjerite ispravnost unešenih parametara! ")

        }
        /*
        console.log("Save function ------------------------------");
        console.log("");
        */
    }

    renderDatumRodjenja() {
        const dateRender = new Date().toJSON().slice(0, 10);
        /*
        console.log("");
        console.log("Render Datum rodjenja function ------------------------------");
        console.log("Current date: " + dateRender);
        console.log("Datum rodjenja: " + this.datumRodjenja);
        console.log("Datum rodjenja old: " + this.oldDatumRodjenja);
        */
        let resultRender = "";
        if ( dateRender === this.datumRodjenja) {

            resultRender = "Informacija nije unešena!";

        } else {

            resultRender = this.datumRodjenja;

        }
        /*
        console.log("Render date: " + resultRender);
        console.log("Render Datum rodjenja function------------------------------");
        console.log("");
        */

        return html`
            ${resultRender}
        `;
    }

}

customElements.define("datum-rodjenja", DatumRodjenja);