import { LitElement, css, html, when } from "/js/lit-all.min.js";

export class IdeNaGlossu extends LitElement {
  static get properties() {
    return {
      ideNaGlossu: {},
      nalogId: {},
      isEditing: {},
    };
  }

  constructor() {
    super();
    this.ideNaGlossu = "0";
    this.nalogId = "";
    this.isEditing = false;
  }

  render() {
    return html`
      <div>
        <div class="ideNaGlossu">
          ${when(
            this.isEditing,
            () => html`
              <select id="ideNaGlossu" name="ideNaGlossu">
                <option value="0" ?selected=${this.ideNaGlossu == "0"}>
                  Ne
                </option>
                <option value="1" ?selected=${this.ideNaGlossu == "1"}>
                  Da
                </option>
              </select>
            `,
            () => html`${this.renderIdeNaGlossu()}`
          )}
        </div>
        <div class="button">
          ${when(
            this.isEditing,
            () =>
              html`<button id="saveButton" @click=${this.save}>
                Spremi promjene
              </button>`,
            () =>
              html`<button id="editButton" @click=${this.edit}>
                Uredi slanje na Glossu
              </button>`
          )}
        </div>
      </div>
    `;
  }

  edit() {
    this.isEditing = true;
  }

  save() {
    const ideNaGlossu = this.shadowRoot.getElementById("ideNaGlossu").value;
    let formData = new FormData();
    formData.append("nalogId", this.nalogId);
    formData.append("ideNaGlossu", ideNaGlossu);

    fetch("/do.php?form=updateSlanjeKandidataNaGlossu", {
      method: "POST",
      body: formData,
    }).then((response) => {
      if (response.ok) {
        this.ideNaGlossu = ideNaGlossu;
      }

      this.isEditing = false;
    });
  }

  renderIdeNaGlossu() {
    if (this.ideNaGlossu == "0") {
      return html`Ne`;
    } else if (this.ideNaGlossu == "1") {
      return html`Da`;
    } else {
      return html`Nedefinisano`;
    }
  }

  static styles = [
    css`
      :host {
        display: block;
      }
      .ideNaGlossu {
        display: inline;
        margin-right: 30px;
      }
      .button {
        display: inline;
      }
      button {
        all: unset;
        cursor: pointer;
        background-color: #68c368;
        color: white;
        padding: 2px 5px;
        border-radius: 2px;
        font-weight: bold;
        box-shadow: 0 0 3px 0 rgba(0, 0, 0, 0.2);
        transition: all 0.5s;
      }
      button:hover {
        box-shadow: 1px 1px 3px 0 rgba(0, 0, 0, 0.5);
        background-color: #66d566;
      }
      #saveButton {
        animation: saveButton 1s infinite;
      }
      @keyframes saveButton {
        0% {
            background-color: #68c368;
            box-shadow: 0 0 3px 0 rgba(0, 0, 0, 0.2);
        }
        50% {
            background-color: #66d566;
            box-shadow: 0 0 3px 0 rgba(0, 0, 0, 0.7);
        }
        100% {
            background-color: #68c368;
            box-shadow: 0 0 3px 0 rgba(0, 0, 0, 0.2);
        }
    `,
  ];
}

customElements.define("ide-na-glossu", IdeNaGlossu);