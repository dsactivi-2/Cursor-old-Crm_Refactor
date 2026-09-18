import { LitElement, css, html, when } from "/js/lit-all.min.js";

export class Pokrajina extends LitElement {
  static get properties() {
    return {
      regija: {},
      regijaName: {},
      regijaOptions: { type: Array },
      grad: {},
      gradOptions: { type: Array },
      gradName: {},
      kandidatId: {},
      isEditing: {},
    };
  }

  constructor() {
    super();
    this.regija = "";
    this.regijaName = "";
    this.regijaOptions = [];
    this.grad = "";
    this.gradOptions = [];
    this.gradName = "";
    this.kandidatId = "";
    this.isEditing = false;
  }

  async connectedCallback() {
    super.connectedCallback();
    await this.fetchAndSetRegijaOptions();
    await this.fetchAndSetGradOptions();
  }

  handleRegijaChange(e) {
    this.regija = e.target.value;
    if(this.regija == 0){
      this.grad = null;
    }
    this.fetchAndSetGradOptions();
  } 
  
  handleGradChange(e) {
    this.grad = e.target.value;
  }

  render() {
    return html`
      
            <div class="regija">
              ${when(
                this.isEditing,
                () => html`
                  <div class="container">
                    <strong class="col-sm-4 text-right">Željena regija:</strong>
                    <div class="col-sm-8">
                      <div>
                        ${this.renderRegijaSelect()}
                      </div>
                      <div class="button">
                        <button id="saveButton" @click=${this.save}>
                          Spremi regiju
                        </button>
                      </div>
                    </div>
                  </div>
                  ${this.regija != null && this.regija != 0 && false
                  ? html`
                  <div class="container">
                    <strong class="col-sm-4 text-right">Željeni grad:</strong>
                    <div class="col-sm-8">
                      <div class="grad">
                        ${this.renderGradSelect()}
                      </div>
                    </div>
                  </div>`
                  : ''}
                `,
                () => html`
                  <div class="container padding-bottom-10">
                    <strong class="col-sm-4 text-right">Željena regija:</strong>
                    <div class="col-sm-8">
                        ${this.renderRegija()}              
                        <div class="button">
                          <button id="editButton" @click=${this.edit}>
                            Uredi regiju
                          </button>
                        </div>
                    </div>
                  </div>
                  ${this.regija != null && this.regija != 0  && false
                  ? html`
                  <div class="container">
                    <strong class="col-sm-4 text-right">Željeni grad:</strong>
                    <div class="col-sm-8">            
                        ${this.regija != null ? this.renderGrad() : ''}
                    </div>
                  </div> `
                  : ''}
                  `
              )}
            </div>
          </div>
      </div>
    `;
  }

  edit() {
    this.isEditing = true;
  }

  save() {
    let formData = new FormData();
    formData.append("kandidat_id", this.kandidatId);
    formData.append("regija_id", this.regija);
    // formData.append("grad_id", this.grad);

    fetch("/do.php?form=update_kandidat_regija_grad", {
      method: "POST",
      body: formData,
    }).then((response) => {
      if (response.ok) {
        
      }

      this.isEditing = false;
    });
  }



  async fetchRegijaValue() {
    const formData = new FormData();
    formData.append('regija_id', this.regija);

    try {
      const response = await fetch('/do.php?form=get_regija', {
        method: 'POST',
        body: formData,
      });

      if (response.ok) {
        const regijaName = await response.text();
        this.regijaName = regijaName;
      } else {
        console.error('Error fetching regija value');
      }
    } catch (error) {
      console.error('Fetch error:', error);
    }
  }

  renderRegija() {
    this.fetchRegijaValue();
    if(this.regija === null){
        return html`<div class="regijaText">Nedefinisano</div>`;
    }
    else if(this.regija === '0'){
        return html`<div class="regijaText">Nema preferenciju</div>`;
    }else{
        return html`<div class="regijaText">${this.regijaName}</div>`;
    }
  }

  async fetchGradValue() {
    const formData = new FormData();
    formData.append('grad_id', this.grad);

    try {
      const response = await fetch('/do.php?form=get_grad', {
        method: 'POST',
        body: formData,
      });

      if (response.ok) {
        const gradName = await response.text();
        this.gradName = gradName;
      } else {
        console.error('Error fetching grad value');
      }
    } catch (error) {
      console.error('Fetch error:', error);
    }
  }

  renderGrad() {
    this.fetchGradValue();

    if(this.grad === null){
        return html`Nedefinisano`;
    }
    else if(this.grad === '0'){
        return html`Nema preferenciju`;
    }else{
        return html`${this.gradName}`;
    }
  }

  renderRegijaSelect() {
    return html`
      <select id="regija" name="regija" @change="${this.handleRegijaChange}">
        <option selected>Odaberi</option>
        <option value="0" ?selected="${this.regija === '0'}">Nema preferenciju</option>
        ${this.regijaOptions.map(
          option => html`
            <option value="${option.value}" ?selected="${this.regija === option.value}">
              ${option.label}
            </option>
          `
        )}
      </select>
    `;
  }
  
  async fetchAndSetRegijaOptions() {
    try {
      const response = await fetch('/do.php?form=get_regija_options', {
        method: 'POST',
      });

      if (response.ok) {
        const data = await response.json();
        this.regijaOptions = data.map(item => ({
          value: item.pr_id,
          label: item.pr_name,
        }));
      } else {
        console.error('Error fetching regija options');
        this.regijaOptions = []; // Handle error by setting regijaOptions to an empty array
      }
    } catch (error) {
      console.error('Fetch error:', error);
      this.regijaOptions = []; // Handle error by setting regijaOptions to an empty array
    }
  }

  renderGradSelect() {
    return html`
      <select id="grad" name="grad" @change="${this.handleGradChange}">
        <option selected>Odaberi</option>
        <option value="0" ?selected="${this.grad === '0'}">Nema preferenciju</option>
        ${this.gradOptions.map(
          option => html`
            <option value="${option.value}" ?selected="${this.grad === option.value}">
              ${option.label}
            </option>
          `
        )}
      </select>
    `;
  }
  
  async fetchAndSetGradOptions() {
    const formData = new FormData();
    formData.append('regija_id', this.regija);
    console.log(this.regija);
    try {
      const response = await fetch('/do.php?form=get_grad_options', {
        method: 'POST',
        body: formData
      });

      if (response.ok) {
        const data = await response.json();
        this.gradOptions = data.map(item => ({
          value: item.pc_id,
          label: item.pc_name,
        }));
      } else {
        console.error('Error fetching grad options');
        this.gradOptions = [];
      }
    } catch (error) {
      console.error('Fetch error:', error);
      this.gradOptions = [];
    }
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

     
      .regija {
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

      .grad {
        margin-top: 10px;
        margin-bottom: 10px;
      }

      .text-right{
        text-align: right;
      }

      .regijaText{
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

customElements.define("kandidat-pokrajina", Pokrajina);
