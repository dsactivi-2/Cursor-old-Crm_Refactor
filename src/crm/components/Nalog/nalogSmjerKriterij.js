import { LitElement, css, html, when } from "/js/lit-all.min.js";

export class NalogSmjerKriterij extends LitElement { 
    static get properties() {
        return {
            nalogSmjerId: {},
            nalogSmjerVr: {},
        };
    }

    constructor() {
        super();
        this.nalogSmjerId = "";
        this.nalogSmjerVr = "";
    }

    createRenderRoot() {
        return this;
    }

    render() {

        return html`
            <div>
                ${this.nalogSmjerVr == 1 ?
                    html`
                        <button vr="0" class = "btn btn-success material-btn material-btn_success" @click=${this.save}>
                            DA
                        </button>
                    `
                    :
                    html`
                        <button vr="1" class = "btn btn-danger material-btn material-btn_danger" @click=${this.save}>
                            NE
                        </button>
                    `
                }
            </div>
        `;
    }

    save(e){
        let vr = e.target.getAttribute('vr');
        let formData = new FormData();
        formData.append("nalogSmjerId", this.nalogSmjerId);
        formData.append("nalogSmjerVr", vr);

        fetch("/do.php?form=nalogSmjerKriterijEdit", {
            method: "POST",
            body: formData,
        }).then((response) => {
            if (response.ok) {
                this.nalogSmjerVr = vr;
            }
        });
    }
}

customElements.define("nalog-smjer-kriterij", NalogSmjerKriterij);