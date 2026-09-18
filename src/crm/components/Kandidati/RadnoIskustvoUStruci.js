import { LitElement, css, html, when } from "/js/lit-all.min.js";

export class RadnoIskustvoUStruci extends LitElement {
  static get properties() {
    return {
      radnoIskustvo: {},
      radnoIskustvoGodine: {},
      kandidatId: {},
      isEditing: {},
    };
  }

  constructor() {
    super();
    this.radnoIskustvo = "";
    this.radnoIskustvoGodine = "";
    this.kandidatId = "";
    this.isEditing = false;
  }

  handleRadnoIskustvoChange(e) {
    this.radnoIskustvo = e.target.value;
    if(this.radnoIskustvo == 0){
      this.radnoIskustvoGodine = null;
    }
  } 
  
  handleRadnoIskustvoGodineChange(e) {
    this.radnoIskustvoGodine = e.target.value;
  }

  render() {
    return html`
      
            <div class="radnoIskustvo">
              ${when(
                this.isEditing,
                () => html`
                  <div class="container">
                    <strong class="col-sm-4 text-right">Radno iskustvo u struci:</strong>
                    <div class="col-sm-8">
                      <div>
                        <select id="radnoIskustvo" name="radnoIskustvo" @change="${this.handleRadnoIskustvoChange}">
                          <option value="0" ?selected="${this.radnoIskustvo === '0'}">Nema radno iskustvo</option>
                          <option value="1" ?selected="${this.radnoIskustvo === '1'}">Ima radno iskustvo</option>
                        </select>
                      </div>
                      <div class="button">
                        <button id="saveButton" @click=${this.save}>
                          Spremi iskustvo
                        </button>
                      </div>
                    </div>
                  </div>
                  ${this.radnoIskustvo == "1"
                  ? html`
                  <div class="container">
                    <strong class="col-sm-4 text-right">Radno iskustvo u struci u posljednjih 5 godina:</strong>
                    <div class="col-sm-8">
                      <div class="radnoIskustvoTrajanje">
                        ${this.renderRadnoIskustvoTrajanjeSelect()}
                      </div>
                      
                    </div>
                  </div>`
                  : ''}
                `,
                () => html`
                  <div class="container padding-bottom-10">
                    <strong class="col-sm-4 text-right">Radno iskustvo u struci:</strong>
                    <div class="col-sm-8">
                        ${this.renderRadnoIskustvo()}              
                        <div class="button">
                          <button id="editButton" @click=${this.edit}>
                            Uredi iskustvo
                          </button>
                        </div>
                    </div>
                  </div>
                  ${this.radnoIskustvo == "1"
                  ? html`
                  <div class="container">
                    <strong class="col-sm-4 text-right">Radno iskustvo u struci u posljednjih 5 godina:</strong>
                    <div class="col-sm-8">            
                        ${this.radnoIskustvo == "1" ? this.renderRadnoIskustvoTrajanje() : ''}
                    </div>
                  </div> `
                  : ''}
                  `
              )}
            </div>
            <!-- <div class="button">
              ${when(
                this.isEditing,
                () =>
                  html`<button id="saveButton" @click=${this.save}>
                    Spremi iskustvo
                  </button>`,
                () =>
                  html`<button id="editButton" @click=${this.edit}>
                    Uredi iskustvo
                  </button>`
              )}
            </div> -->
          </div>
      </div>
    `;
  }

  edit() {
    this.isEditing = true;
  }

  save() {
    const radnoIskustvo = this.shadowRoot.getElementById("radnoIskustvo").value;
    const radnoIskustvoGodine =  this.radnoIskustvoGodine;
    let formData = new FormData();
    formData.append("kandidatId", this.kandidatId);
    formData.append("radnoIskustvo", radnoIskustvo);
    formData.append("radnoIskustvoGodine", radnoIskustvoGodine);

    fetch("/do.php?form=updateRadnoIskustvoUStruci", {
      method: "POST",
      body: formData,
    }).then((response) => {
      if (response.ok) {
        this.radnoIskustvo = radnoIskustvo;
      }

      this.isEditing = false;
    });
  }

  renderRadnoIskustvo() {
    if (this.radnoIskustvo == "0") {
      return html`<div class="radnoIskustvoText">Nema radno iskustvo</div>`;
    } else if (this.radnoIskustvo == "1") {
      return html`<div class="radnoIskustvoText">Ima radno iskustvo</div>`;
    } else {
      return html`<div class="radnoIskustvoText">Nedefinisano</div>`;
    }
  }

  renderRadnoIskustvoTrajanje() {
    if (this.radnoIskustvoGodine == "0") {
      return html`Nema u zadnjih 5 godina`;
    } else if (this.radnoIskustvoGodine == "1") {
      return html`Manje od 1 godine`;
    } else if (this.radnoIskustvoGodine == "2") {
      return html`1 godina`;
    } else if (this.radnoIskustvoGodine == "3") {
      return html`2 godine`;
    }else if (this.radnoIskustvoGodine == "4") {
      return html`3 godine`;
    }else if (this.radnoIskustvoGodine == "5") {
      return html`4 godine`;
    }else if (this.radnoIskustvoGodine == "6") {
      return html`5 godina`;
    }else {
      return html`Nedefinisano`;
    }
  }
  
  renderRadnoIskustvoTrajanjeSelect() {
    return html`
    <select id="radnoIskustvoGodine" name="radnoIskustvoGodine" @change="${this.handleRadnoIskustvoGodineChange}">
      <option selected disabled>
        Odaberi
      </option>
      <option value="0" ?selected=${this.radnoIskustvoGodine === "0"}>
        Nema u zadnjih 5 godina
      </option>
      <option value="1" ?selected=${this.radnoIskustvoGodine === "1"}>
        Manje od 1 godine
      </option>
      <option value="2" ?selected=${this.radnoIskustvoGodine === "2"}>
        1 godina
      </option>
      <option value="3" ?selected=${this.radnoIskustvoGodine === "3"}>
        2 godine
      </option>
      <option value="4" ?selected=${this.radnoIskustvoGodine === "4"}>
        3 godine
      </option>
      <option value="5" ?selected=${this.radnoIskustvoGodine === "5"}>
        4 godine
      </option>
      <option value="6" ?selected=${this.radnoIskustvoGodine === "6"}>
        5 godina
      </option>
    </select>`;
  }

  static styles = [
    css`
      :host {
        display: block;
      }

      .container {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 15px;
      }

      .col-sm-4 {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        font-weight: bold;
        margin-right:15px;
      }

      .col-sm-8 {
        display: flex;
        flex-direction: row;
      }

     
      .radnoIskustvo {
        display: inline;
        margin-right: 30px;
      }
      .button {
        /* display: inline; */
        float: right;
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: flex-end;
        width: 60%;
        /* margin-right: 15px; */
      }
      button {
        all: unset;
        cursor: pointer;
        background-color: #5cb85c;
        border-color: #4cae4c;
        color: white;
        padding: 0px 15px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.42857143;
        box-shadow: 0 0 3px 0 rgba(0, 0, 0, 0.2);
        transition: all 0.5s;
        float:right;
      }
      button:hover {
        box-shadow: 1px 1px 3px 0 rgba(0, 0, 0, 0.5);
        background-color: #398439;
      }
      #saveButton {
        animation: saveButton 1s infinite;
      }
      select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
      }

      .radnoIskustvoTrajanje {
        margin-top: 10px;
        margin-bottom: 10px;
      }

      .text-right{
        text-align: right;
      }

      .radnoIskustvoText{
        width: 40%;
      }

      label {
        font-weight: bold;
      }
      
      .padding-bottom-10{
        padding-bottom: 10px;
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

customElements.define("radno-iskustvo", RadnoIskustvoUStruci);
