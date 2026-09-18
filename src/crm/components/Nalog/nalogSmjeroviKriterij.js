import { LitElement, css, html, when } from "/js/lit-all.min.js";

export class NalogSmjeroviKriterij extends LitElement { 
    static get properties() {
        return {
            nalogId: {},
            nalogVr: {},
        };
    }

    constructor() {
        super();
        this.nalogId = "";
        this.nalogVr = "";
    }

    createRenderRoot() {
        return this;
    }

    render() {

        return html`
            <div>
                ${this.nalogVr == 1 ?
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
        formData.append("nalogId", this.nalogId);
        formData.append("nalogVr", vr);

        fetch("/do.php?form=nalogSmjeroviKriterijEdit", {
            method: "POST",
            body: formData,
        }).then((response) => {
            if (response.ok) {
                this.nalogVr = vr;
            }
        });
    }
}

customElements.define("nalog-smjerovi-kriterij", NalogSmjeroviKriterij);