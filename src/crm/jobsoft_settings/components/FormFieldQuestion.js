import {LitElement, html, nothing} from '../js/lit/lit-all.min.js';

export class FormFieldQuestion extends LitElement {
    static get properties() {
        return {
            panel_id: {reflect: true},
            pqu_id: {},
            pqu_question: {},
            pqu_question_old: {},
            pqu_nalog_id: {},
            pqu_has_text: {},
            pqu_has_rating: {},
            pqu_has_dropdown: {}, 
            pqc_options: {type: Array},
            pqc_id: {},
            pqc_name: {},
            pqc_name_de: {},
            checked: {},
            is_editing_text: {},
            enable_editing_text: {}, 
            flag_enpal_access: {},
            question_in_form: {},
            is_edited_text: {}
        };
    };

    constructor() {
        super();
        this.panel_id = 0; 
        this.pqu_id = null;
        this.pqu_question = null;
        this.pqu_question_old = null;
        this.pqu_nalog_id = null;
        this.pqu_has_text = 1;
        this.pqu_has_rating = 0;
        this.pqu_has_dropdown = 0; 
        this.pqc_options = [];
        this.pqc_id = null;
        this.pqc_name = null;
        this.pqc_name_de = null;
        this.checked = 0;
        this.is_editing_text = 0;
        this.enable_editing_text = 0;
        this.flag_enpal_access = 0; 
        this.question_in_form = 0;
        this.is_edited_text = 0;
    };

    firstUpdated() {
        const selects = this.querySelectorAll('.selectpicker');
        selects.forEach(select => $(select).selectpicker('refresh'));
    };

    createRenderRoot() {
        return this;
    }; 

    getGridClass() {

        let grid_class = {
            questionClass: '',
            ratingClass: '',
            dropdownClass: '',
            textClass: '',
            switchClass: {
                firstClass: '',
                secondClass: ''
            }
        };

        if (this.pqu_has_rating == 1 && this.pqu_has_dropdown == 1) {
            grid_class['questionClass'] = 'col-xs-6';
            grid_class['ratingClass'] = 'col-xs-3 text-center';
            grid_class['dropdownClass'] = 'col-xs-3 text-center';
        } else if (this.pqu_has_rating == 1 && this.pqu_has_dropdown == 0) {
            grid_class['questionClass'] = 'col-xs-6';
            grid_class['ratingClass'] = 'col-xs-6 text-right';
            grid_class['dropdownClass'] = 'hidden';
        } else if (this.pqu_has_rating == 0 && this.pqu_has_dropdown == 1) {
            grid_class['questionClass'] = 'col-xs-6';
            grid_class['ratingClass'] = 'hidden';
            grid_class['dropdownClass'] = 'col-xs-6 text-right';
        } else {
            grid_class['questionClass'] = 'col-xs-12';
            grid_class['ratingClass'] = 'hidden';
            grid_class['dropdownClass'] = 'hidden';
        }

        if (this.pqu_has_text == 1) {
            grid_class['textClass'] = '';
        } else {
            grid_class['textClass'] = 'hidden';
        }

        if (this.question_in_form == 1) {
            grid_class['switchClass']['firstClass'] = 'col-xs-2';
            grid_class['switchClass']['secondClass'] = 'col-xs-10';
        } else {
            grid_class['switchClass']['firstClass'] = 'hidden';
            grid_class['switchClass']['secondClass'] = 'col-xs-12';
        }

        return grid_class;
    };

    renderText() {
        if (this.enable_editing_text == 1 && this.question_in_form == 1) {
            if (this.is_editing_text == 1) {
                return html`
                    <div class="row">
                        <div class="col-xs-8">
                            <textarea class="materail-input material-textarea" name="pqu_text_questions_${this.panel_id}" id="pqu_text_questions_${this.panel_id}" rows="4" @input="${this.handleTextInput}">${this.pqu_question}</textarea>
                        </div>
                        <div class="col-xs-2">
                            <span @click="${this.saveText}" class="btn btn-success material-btn material-btn_success"><i class="fa fa-floppy-o" aria-hidden="true"></i></span>
                        </div>
                        <div class="col-xs-2">
                            <span @click="${this.closeText}" class="btn btn-danger material-btn material-btn_danger"><i class="fa fa-times-circle-o" aria-hidden="true"></i></span>
                        </div>
                    </div>
                `;
            } else {
                return html`
                    <div class="row">
                        <div class="col-xs-12">
                            <textarea class="materail-input material-textarea hidden" name="pqu_text_questions_${this.panel_id}" id="pqu_text_questions_${this.panel_id}" rows="4">${this.pqu_question}</textarea>
                            <p class="question-text m-0" @click="${this.editText}">${this.pqu_question}</p>
                        </div>
                    </div>
                `;
            }
        } else {
            return html`
                <p class="question-text m-0">${this.pqu_question}</p>
            `;
        }
    };

    handleTextInput(event) {
        this.pqu_question = event.target.value;
    };

    editText() {
        this.is_editing_text = 1;
    };

    closeText() {
        this.is_editing_text = 0;
        this.pqu_question = this.pqu_question_old;
    };
    
    saveText() {
        if (this.pqu_question_old != this.pqu_question) {
            this.is_edited_text = 1;
        }
        this.pqu_question_old = this.pqu_question; 
        this.is_editing_text = 0;
    };

    renderRating() {
        return html`
            <i class="fa fa-star fa-2x" aria-hidden="true"></i>
            <i class="fa fa-star fa-2x" aria-hidden="true"></i>
            <i class="fa fa-star-o fa-2x " aria-hidden="true"></i>
            <i class="fa fa-star-o fa-2x" aria-hidden="true"></i>
            <i class="fa fa-star-o fa-2x ${(this.flag_enpal_access == 1) ? 'hidden' : ''}" aria-hidden="true"></i>
        `;
    }; 

    renderDropDown() {
        /*let optionsHtml = this.pqc_options.map(option => html`
            <option value="${option.pqoValue}" data-subtext="${option.pqoValueSubtext || ''} / ${option.pqoValueSubtextDe || ''}">
                ${option.pqoValueText || 'None'} / ${option.pqoValueTextDe || 'None'}
            </option>
        `);*/

        let optionsHtml = this.pqc_options.map(option => html`
            <option value="${option.pqoValue}" data-subtext="${option.pqoValueSubtextDe || ''}">
                ${option.pqoValueTextDe || 'None'}
            </option>
        `);

        return html`
            <select class="selectpicker" title="Prikaži opcije">
                ${optionsHtml}
            </select>
        `;
    };

    renderTextBox() {
        return html`
            <div class="col-xs-12">
                <textarea class="materail-input material-textarea" placeholder="Korisniku na JobSoft-u će prema postavkama ovog pitanja biti prikazano polje za unos teksta ovog pitanja" rows="2" readonly></textarea>
            </div>
        `;
    };

    render () {

        let grid_class = this.getGridClass();

        return html`
            <div class="panel panel-default my-3" id="${this.panel_id}">
                <input type="hidden" name="pqu_id_${this.panel_id}" id="pqu_id_${this.panel_id}" value="${this.pqu_id}" />
                <input type="hidden" name="pqu_edited_${this.panel_id}" id="pqu_edited_${this.panel_id}" value="${this.is_edited_text}" />
                <input type="hidden" name="pqu_order_${this.panel_id}" id="pqu_order_${this.panel_id}" value="${this.panel_id}"/>
                <div class="panel-heading ${(this.question_in_form != 1) ? 'hidden' : ''}">
                    <h3 class="panel-title"><i class="fa fa-arrows-v" aria-hidden="true"></i></h3>
                </div>
                <div class="panel-body p-5">
                    <div class="row">
                        <div class="${grid_class['switchClass']['firstClass']} text-center">
                            <div class="main-container__column materail-switch materail-switch_primary">
                                <input class="materail-switch__element" type="checkbox" id="pqu_included_${this.panel_id}" name="pqu_included_${this.panel_id}" value="1" ?checked="${this.checked == 1}">
                                <label class="materail-switch__label" for="pqu_included_${this.panel_id}"></label>
                            </div>
                        </div>
                        <div class="${grid_class['switchClass']['secondClass']}">
                            <div class="row mb-5">
                                <div class="${grid_class['questionClass']}">
                                    ${this.renderText()}
                                </div>
                                <div class="${grid_class['ratingClass']}">
                                    ${this.renderRating()}
                                </div>
                                <div class="${grid_class['dropdownClass']}">
                                    ${this.renderDropDown()}
                                </div>
                            </div>
                            <div class="row ${grid_class['textClass']}">
                                ${this.renderTextBox()}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    };

    updated(changedProperties) {
        if (changedProperties.has('panel_id')) {
            this.updateIdReferences();
        }
    };

    updateIdReferences() {
        const panelId = this.panel_id;
        this.querySelectorAll('input, textarea, select, .panel').forEach(element => {
            if (element.id) {
                element.id = element.id.replace(/\d+$/, panelId);
            }
            if (element.name) {
                element.name = element.name.replace(/\d+$/, panelId);
            }
            if (element.htmlFor) {
                element.htmlFor = element.htmlFor.replace(/\d+$/, panelId);
            }
        });
    }
}; 

customElements.define("form-field-question", FormFieldQuestion);