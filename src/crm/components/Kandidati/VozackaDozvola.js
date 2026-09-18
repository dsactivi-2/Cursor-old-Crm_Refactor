import { LitElement, css, html, when, nothing } from "/js/lit-all.min.js";

export class VozackaDozvola extends LitElement {
    static get properties() {
        return {
            vozackaDozvola: {},
            vozackaDozvolaKategorija: {converter:{
                fromAttribute: (value, Array) => {
                    if(value)
                    {
                        return value.split(",");
                    }

                    return null;
                }
            }},
            kandidatId: {},
            isEditing: {},
            oldVozackaDozvola: {},
            oldVozackaDozvolaKategorije: {converter:{
                fromAttribute: (value, Array) => {
                    if(value)
                    {
                        return value.split(",");
                    }

                    return null;
                }
            }}
        };
    }

    constructor() {
        super();
        this.vozackaDozvola = "";
        this.vozackaDozvolaKategorija = null;
        this.kandidatId = "";
        this.isEditing = false;
        this.oldVozackaDozvola = "";
        this.oldVozackaDozvolaKategorije = null;
    }

    createRenderRoot() {
        return this;
    }

    render() {
        /*
        console.log("///////////////////////////////////////////////////////////////");
        console.log("");
        console.log("Render function ----------------------------------");
        console.log("Vozacka dozvola: " + this.vozackaDozvola);
        console.log("Vozacka dozvola kategorije: " + this.vozackaDozvolaKategorija);
        console.log("Kandidat: " + this.kandidatId);
        console.log("Editing: " + this.isEditing);
        console.log("Vozacka old: " + this.oldVozackaDozvola);
        console.log("Kategorija old: " + this.oldVozackaDozvolaKategorije);
        console.log("Render function ----------------------------------")
        console.log("");
        */
        return html`
            <div>
                <div class="row">
                    <div class = "col-xs-12" style="display: ${this.isEditing ? "block" : "none"}">
                        <div class="form-group">
                            <div class="">
                                <select class="selectpicker" id="vozackaDozvola" title="Odaberite" name="vozackaDozvola" @change=${(e) => {this.vozackaDozvola = e.target.value; if(this.vozackaDozvola === "Ne") this.vozackaDozvolaKategorija = null; $("#vozackaDozvolaKategorija").val(this.vozackaDozvolaKategorija).selectpicker("refresh"); }}>
                                    <option value="Ne" selected="${this.vozackaDozvola === "Ne" || nothing}">
                                        NE
                                    </option>
                                    <option value="Da" selected="${this.vozackaDozvola === "Da" || nothing}">
                                        DA
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="display: ${this.isEditing && this.vozackaDozvola === "Da" ? "block" : "none"}">
                            <div class="">
                                <select multiple class="selectpicker" id="vozackaDozvolaKategorija" title="Odaberite" name="vozackaDozvolaKategorija" @change=${(e) => this.vozackaDozvolaKategorija = $(e.target).val()} >
                                    <option value="B" selected="${this.vozackaDozvolaKategorija?.includes("B") || nothing}">
                                        B
                                    </option>
                                    <option value="C1" selected="${this.vozackaDozvolaKategorija?.includes("C1") || nothing}">
                                        C1
                                    </option>
                                    <option value="C" selected="${this.vozackaDozvolaKategorija?.includes("C") || nothing}">
                                        C
                                    </option>
                                    <option value="BE" selected="${this.vozackaDozvolaKategorija?.includes("BE") || nothing}">
                                        BE
                                    </option>
                                    <option value="C1E" selected="${this.vozackaDozvolaKategorija?.includes("C1E") || nothing}">
                                        C1E
                                    </option>
                                    <option value="CE" selected="${this.vozackaDozvolaKategorija?.includes("CE") || nothing}">
                                        CE
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
                            this.renderVozackaDozvola()
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

    close() {
        this.isEditing = false;
        this.vozackaDozvola = this.oldVozackaDozvola;
        this.vozackaDozvolaKategorija = this.oldVozackaDozvolaKategorije;
        $("#vozackaDozvola").val(this.vozackaDozvola).selectpicker("refresh");
        $("#vozackaDozvolaKategorija").val(this.vozackaDozvolaKategorija).selectpicker("refresh");
    }

    edit() {
        this.isEditing = true;
    }

    save() {
        /*
        console.log("");
        console.log("Save Vozacka dozvola ------------------------------");
        console.log("Vozacka dozvola: " + this.vozackaDozvola);
        console.log("Vozacka dozvola kategorije: " + this.vozackaDozvolaKategorija);
        console.log("Kandidat: " + this.kandidatId);
        */

        let flagSave = false; 
        let formData = new FormData();
        formData.append("kandidatId", this.kandidatId);
        if ( this.kandidatId !== "" ) {
            if ( this.vozackaDozvola === "Da" && this.vozackaDozvolaKategorija === null ) {
                //console.log("Prvi uslov");
                flagSave = false; 

            } else if ( this.vozackaDozvola === "Da" && this.vozackaDozvolaKategorija !== null ) {
                //console.log("Drugi uslov");
                flagSave = true; 
                formData.append("vozackaDozvola", this.vozackaDozvola);
                formData.append("vozackaDozvolaKategorija", this.vozackaDozvolaKategorija.join(","));

            } else if ( this.vozackaDozvola === "Ne"  && this.vozackaDozvolaKategorija === null ) {
                //console.log("Treći uslov");
                flagSave = true; 
                formData.append("vozackaDozvola", this.vozackaDozvola);

            } else if ( this.vozackaDozvola === "Ne"  && this.vozackaDozvolaKategorija !== null ) {
                //console.log("Četvrti uslov");
                flagSave = false; 
            }
        }
        
        //console.log("Flag: " + flagSave); 

        if ( flagSave === true ) {
            fetch("/do.php?form=updateVozackaDozvolaKandidat", {
                method: "POST",
                body: formData,
            }).then((response) => {
                if (!response.ok) {

                    alert("Desio se problem sa unosom u bazu!");

                } else {

                    this.oldVozackaDozvola = this.vozackaDozvola;
                    this.oldVozackaDozvolaKategorije = this.vozackaDozvolaKategorija;

                }

                this.isEditing = false;

            });
        } else {

            alert("Provjerite da li ste unijeli sve parametre!"); 

        }
        /*
        console.log("Editing: " + this.isEditing);
        console.log("Vozacka old: " + this.oldVozackaDozvola);
        console.log("Kategorija old: " + this.oldVozackaDozvolaKategorije);
        console.log("Save Vozacka dozvola ------------------------------");
        console.log("");
        */
    }

    renderVozackaDozvola() {
        /*
        console.log("");
        console.log("Render Vozacka dozvola ------------------------------");
        console.log("Vozacka dozvola: " + this.vozackaDozvola);
        console.log("Vozacka dozvola kategorije: " + this.vozackaDozvolaKategorija);
        console.log("Kandidat: " + this.kandidatId);
        console.log("Editing: " + this.isEditing);
        console.log("Vozacka old: " + this.oldVozackaDozvola);
        console.log("Kategorija old: " + this.oldVozackaDozvolaKategorije);
        */
        let resultRender = "";

        if (this.vozackaDozvola === "") {
            resultRender = "Informacija nije unešena!";
        } else if (this.vozackaDozvola === "Da"){
            if (this.vozackaDozvolaKategorija !== null) {
                resultRender = this.vozackaDozvolaKategorija.join(",");
            } else {
                resultRender = "Neadekvatna vrijednost!";
            }
        } else if ( this.vozackaDozvola === "Ne" ) {
            resultRender = "Kandidat nema vozačku dozvolu!";
        }
        /*
        console.log("Render Result: " + resultRender);
        console.log("Render Vozacka dozvola ------------------------------");
        console.log("");
        */
        return html`
            ${resultRender}
        `;

    }
}

customElements.define("vozacka-dozvola", VozackaDozvola);