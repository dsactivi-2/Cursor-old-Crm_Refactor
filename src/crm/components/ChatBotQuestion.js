import { LitElement, html, css } from "/js/lit-all.min.js";

class ChatBotQuestion extends LitElement {
  static properties = {
    questionData: { type: Object },
    isEdited: { type: Boolean },
    isAddModalVisible: { type: Boolean },
    newQuestionText: { type: Object },
    newAnswerText: { type: Object },
    selectedLanguages: { type: Array },
    selectedLanguagesTemp: { type: Array },
    isButtonRotated: { type: Boolean },
    languages: { type: Array },
    isApiCallInProgress: { type: Boolean },
    handleButtonClick: { type: () => {} },
    depth: { type: Number },
  };
  createRenderRoot() {
    return this;
  }

  constructor() {
    super();
    this.isEdited = false;
    this.isAddModalVisible = false;
    this.newQuestionText = {};
    this.newAnswerText = {};
    this.selectedLanguages = [];
    this.isButtonRotated = false;
    this.isTypeOneChecked = false;
    this.languages = [
      { code: "En", label: "Engleski" },
      { code: "De", label: "Njemački" },
      { code: "Hr", label: "Hrvatski" },
      { code: "Ba", label: "Bosanski" },
      { code: "Sr", label: "Srpski" },
      { code: "Es", label: "Španski" },
      { code: "Fr", label: "Francuski" },
      { code: "It", label: "Italijanski" },
      { code: "Pt", label: "Portugalski" },
      { code: "Nl", label: "Holandski" },
      { code: "Be", label: "Flamanski" },
      { code: "Da", label: "Danski" },
      { code: "Fi", label: "Finski" },
      { code: "No", label: "Norveški" },
      { code: "Sv", label: "Švedski" },
    ];

    this.isApiCallInProgress = false;
    this.selectedLanguagesTemp = [];
    this.handleButtonClick;
  }
  firstUpdated() {
    console.log(this.depth);
    if (this.questionData) {
      this.selectedLanguages = this.questionData.selectedLanguages || [];
    }
  }
  handleLanguageChange(e) {
    const selectedOptions = e.target.selectedOptions;
    this.selectedLanguages = Array.from(selectedOptions).map(
      (option) => option.value
    );
  }

  handleEdit() {
    setTimeout(() => {
      $(`#languageSelect_${this.questionData.id}`).selectpicker();
    }, 100);
    this.selectedLanguagesTemp = this.selectedLanguages;
    this.isEdited = true;
  }

  async handleDelete() {
    const confirmDelete = window.confirm(
      "Da li ste sigurni da želite izbrisati pitanje"
    );

    if (!confirmDelete) {
      return;
    }

    try {
      let res = await fetch(`/do.php?form=delete_question`, {
        method: "POST",
        body: this.questionData.id,
      });

      let ret = await res.text();

      if (res.status >= 400 && res.status < 600) {
        throw new Error(ret);
      } else {
        window.location.reload();
      }
    } catch (error) {
      alert(`Došlo je do problema!\n ${error}`);
    }
  }

  async handleSave() {
    const dataObject = {
      id: this.questionData.id,
      question_json: {},
      answer_json: {},
      visibility: {},
      type: {},
    };
    const checkbox = document.querySelector("#inAppVisibilityCheckbox");
    const visibilityValue = checkbox.checked ? 1 : 0;
    dataObject.visibility = visibilityValue;
    dataObject.type = this.questionData.currentType;
    this.selectedLanguages.forEach((language) => {
      const editedQuestion = document.querySelector(
        `#editedQuestion${language}`
      ).value;
      const editedAnswer = document.querySelector(
        `#editedAnswer${language}`
      ).value;

      dataObject.question_json[language.toLowerCase()] = editedQuestion;
      dataObject.answer_json[language.toLowerCase()] = editedAnswer;
    });
    try {
      let res = await fetch(`/do.php?form=edit_question`, {
        method: "POST",
        body: JSON.stringify(dataObject),
      });

      let ret = await res.text();

      if (res.status >= 400 && res.status < 600) {
        throw new Error(ret);
      } else {
        window.location.reload();
      }
    } catch (error) {
      alert(`Došlo je do problema!\n ${error}`);
    }

    this.isEdited = false;
  }

  handleAdd() {
    this.isAddModalVisible = true;
  }

  async handleConfirmAdd() {
    const id = this.questionData.id;
    const question_json = {
      en: this.newQuestionTextEn,
      de: this.newQuestionTextDe,
    };

    const answer_json = {
      en: this.newAnswerTextEn,
      de: this.newAnswerTextDe,
    };
    const dataObject = {
      type: this.questionData.type.join(","),
      question_json: question_json,
      answer_json: answer_json,
      parent_id: id,
    };

    try {
      let res = await fetch(`/do.php?form=add_question`, {
        method: "POST",
        body: JSON.stringify(dataObject),
      });

      let ret = await res.text();

      if (res.status >= 400 && res.status < 600) {
        throw new Error(ret);
      } else {
        window.location.reload();
      }
    } catch (error) {
      alert(`Došlo je do problema!\n ${error}`);
    }

    this.isAddModalVisible = false;
    this.questionData.type = "";
    this.newQuestionTextEn = "";
    this.newAnswerTextEn = "";
    this.newQuestionTextDe = "";
    this.newAnswerTextDe = "";
  }

  handleCancelAdd() {
    this.isAddModalVisible = false;
    this.selectedLanguages.forEach((language) => {
      this[`newQuestionText${language}`] = "";
      this[`newAnswerText${language}`] = "";
    });
  }

  handleCancel() {
    this.selectedLanguages = this.selectedLanguagesTemp;
    this.isEdited = false;
  }

  render() {
    const isButtonDisabled =
      !this.questionData.subquestions ||
      this.questionData.subquestions.length === 0 ||
      !this.questionData.type.includes(
        this.questionData.currentType.toString()
      );

    return html`
      <style>
        .main {
          display: block;
          margin-bottom: 20px;
          border-radius: 5px;
          border: 1px #4caf50 solid;
        }
        .main-category{
          border: 1px #337ab7 solid;
        }
        .custom-input {
          width: 100%;
          margin-bottom: 10px;
        }

        .custom-button {
          background-color: #4caf50;
          color: white;
          padding: 8px 16px;
          border: none;
          border-radius: 5px;
          cursor: pointer;
          margin-right: 5px;
        }
        .custom-button-category{
          background-color: #337ab7;
        }

        .custom-button:hover {
          background-color: #45a049;
        }

        .custom-button-category:hover {
          background-color: #2581d1;
        }
        .button-disabled {
          opacity: 0.5;
          cursor: not-allowed;
          background-color: #4caf50;
        }
        
        .button-disabled-category {
          opacity: 0.5;
          cursor: not-allowed;
          background-color: #7aa8d0;
        }

        .custom-button:disabled {
          background-color: #cccccc;
          cursor: not-allowed;
        }

        .modal-custom-container {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.5);
          justify-content: center;
          align-items: center;
          z-index: 1000;
        }

        .modal-custom {
          min-width: 500px;
          background-color: white;
          padding: 20px;
          border: 1px solid #ccc;
          border-radius: 5px;
        }

        .form-group {
          margin-bottom: 1rem;
        }

        .form-control {
          width: 100%;
          padding: 0.375rem 0.75rem;
          font-size: 1rem;
          line-height: 1.5;
          border: 1px solid #ced4da;

          border-radius: 0.25rem;
        }

        .custom-checkbox-label {
          margin-left: 5px;
        }

        .custom-checkbox {
          appearance: none;
          width: 31px;
          height: 31px;
          border: 2px solid #4caf50;
          border-radius: 3px;
          display: inline-block;
          position: relative;
          vertical-align: middle;
          cursor: pointer;
          margin: 0px;
        }

        .custom-checkbox-category {
          border: 2px solid #337ab7;
        }

        .custom-checkbox:checked {
          background-color: #4caf50;
          border: 2px solid #4caf50;
        }
        
        .custom-checkbox-category:checked {
          background-color: #337ab7;
          border: 2px solid #337ab7;
        }

        .custom-checkbox:checked:after {
          content: "\\2714";
          font-size: 18px;
          color: white;
          position: absolute;
          top: 0;
          left: 6px;
        }

        .checkbox-loader {
          border: 2px solid lightgray;
          width: 24px;
          height: 24px;

          border-top: 2px solid green;
          border-radius: 50%;

          animation: spin 1s linear infinite;
        }

        @keyframes spin {
          0% {
            transform: rotate(0deg);
          }

          100% {
            transform: rotate(360deg);
          }
        }
      </style>
      <div class="main ${this.depth == 0 ? "main-category" : ""}"> 
        ${
          this.isEdited
            ? html`<div style="display: flex; justify-content: space-between">
                <div class="" style="padding: 12px; width: 40%;">
                  <label for="languageSelect_${this.questionData.id}">
                    Select Languages:
                  </label>
                  <select
                    id="languageSelect_${this.questionData.id}"
                    @change="${this.handleLanguageChange}"
                    multiple
                    class="selectpicker"
                    title="Odaberite"
                    name="languageSelect_${this.questionData.id}[]"
                  >
                    ${this.languages.map(
                      (language) => html`
                        <option
                          value="${language.code}"
                          ?selected="${this.selectedLanguages.includes(
                            language.code
                          )}"
                          data-subtext="${language.label}"
                        >
                          ${language.label}
                        </option>
                      `
                    )}
                  </select>
                </div>

                <div
                  style="width: 20%;margin-right: 5px;display: flex;align-items: center; flex-direction: row-reverse"
                >
                  <div style="padding: 10px">
                    <label
                      for="inAppVisibilityCheckbox"
                      class="custom-checkbox-label"
                      >Vidljivo</label
                    >
                    <input
                      id="inAppVisibilityCheckbox"
                      type="checkbox"
                      ?checked="${this.questionData.type.includes(
                        this.questionData.currentType.toString()
                      )}"
                      class="custom-checkbox  ${this.depth == 0
                        ? "custom-checkbox-category"
                        : ""}"
                    />
                  </div>
                </div>
              </div>`
            : ""
        }
        <div
          style="padding: 10px;"
        >
          <div>
            ${this.selectedLanguages.map(
              (lang) => html`
                <p style="padding-top: 10px">
                  <strong>Pitanje(${lang}):</strong>
                  ${this.isEdited
                    ? html`<input
                        id="editedQuestion${lang}"
                        type="text"
                        .value="${this.questionData["question" + lang] || ""}"
                        class="custom-input"
                      />`
                    : html` ${this.questionData["question" + lang]}`}
                </p>
                <p>
                  <strong>Odgovor(${lang}):</strong>
                  ${this.isEdited
                    ? html`<input
                        id="editedAnswer${lang}"
                        type="text"
                        .value="${this.questionData["answer" + lang] || ""}"
                        class="custom-input"
                      />`
                    : this.questionData["answer" + lang]}
                </p>
                <hr />
              `
            )}
          </div>
        </div>
        <div style="display:flex; justify-content: space-between;">
          <div
            style="width: 35%;padding: 10px;"
          >
            ${
              this.isEdited
                ? html` <button
                      class="custom-button ${this.depth == 0
                        ? "custom-button-category"
                        : ""}"
                      @click="${this.handleSave}"
                    >
                      Spremi
                    </button>
                    <button
                      class="custom-button ${this.depth == 0
                        ? "custom-button-category"
                        : ""}"
                      @click="${this.handleCancel}"
                    >
                      Otkaži
                    </button>`
                : html` <button
                      class="custom-button ${this.depth == 0
                        ? "custom-button-category"
                        : ""}"
                      style=""
                      @click="${this.handleEdit}"
                    >
                      Uredi
                    </button>
                    <button
                      class="custom-button ${this.depth == 0
                        ? "custom-button-category"
                        : ""}"
                      @click="${this.handleDelete}"
                    >
                      Izbriši
                    </button>
                    <button
                      class="custom-button ${this.depth == 0
                        ? "custom-button-category"
                        : ""}"
                      @click="${this.handleAdd}"
                    >
                      Dodaj ${this.depth == 0 ? "pitanje" : "podpitanje"}
                    </button>`
            }
          </div>
          <div
            style="width: 30%;"
          ></div>
          <div
           
            style="padding: 0px; width: 10%; min-width: 180px;"
          >
            <button @click="${!isButtonDisabled ? this.handleButtonClick : ''}"
              class="custom-button ${isButtonDisabled ? "button-disabled" : ""} 
                    ${isButtonDisabled && this.depth == 0 ? "button-disabled-category" : "" }  
                    ${this.depth == 0 ? "custom-button-category" : ""}"
              style="z-index: 2; width: 90%; height: 80%;"
            >
              Prikaži ${this.depth == 0 ? "pitanja" : "podpitanja"}
            </button>
          </div>
        </div>
        </div>
        
        <div
          class="modal-custom-container"
          style="${
            this.isAddModalVisible ? "display: flex;" : "display: none;"
          }"
        >
          <div class="modal-custom">
            <div class="form-group">
              <p>Dodaj novo ${this.depth == 0 ? "pitanje" : "podpitanje"}</p>
              <label for="newQuestionTextEn">Pitanje (en):</label>
              <input
                id="newQuestionTextEn"
                type="text"
                class="form-control custom-input"
                .value="${this.newQuestionTextEn ? this.newQuestionTextEn : ""}"
                @input="${(e) => (this.newQuestionTextEn = e.target.value)}"
              />
            </div>

            <div class="form-group">
              <label for="newAnswerTextEn">Odgovor (en):</label>
              <textarea
                id="newAnswerTextEn"
                class="form-control custom-input"
                .value="${this.newAnswerTextEn ? this.newAnswerTextEn : ""}"
                @input="${(e) => (this.newAnswerTextEn = e.target.value)}"
              ></textarea>
            </div>

            <div class="form-group">
              <label for="newQuestionTextDe">Pitanje (de):</label>
              <input
                id="newQuestionTextDe"
                type="text"
                class="form-control custom-input" 
                .value="${this.newQuestionTextDe ? this.newQuestionTextDe : ""}"
                @input="${(e) => (this.newQuestionTextDe = e.target.value)}"
              />
            </div>

            <div class="form-group">
              <label for="newAnswerTextDe">Odgovor (de):</label>
              <textarea
                id="newAnswerTextDe"
                class="form-control custom-input"
                .value="${this.newAnswerTextDe ? this.newAnswerTextDe : ""}"
                @input="${(e) => (this.newAnswerTextDe = e.target.value)}"
              ></textarea>
              <button class="custom-button ${
                this.depth == 0 ? "custom-button-category" : ""
              }" @click="${this.handleConfirmAdd}">Potvrdi</button>
              <button class="custom-button ${
                this.depth == 0 ? "custom-button-category" : ""
              } " @click="${this.handleCancelAdd}">Zatvori</button>
            </div>
          </div>
        </div>
      </div>
    `;
  }
}

customElements.define("chat-bot-question", ChatBotQuestion);
