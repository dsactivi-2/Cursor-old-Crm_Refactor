import {LitElement, css, html, when, unsafeCSS, nothing } from '/js/lit-all.min.js';
import '/Tooltip.js';
class ArrivalProjectionForm extends LitElement {

    static properties = {
        currentStatuses: {},
        newStatuses: {},
        isFetching: {type: Boolean},
    };

    fetchStatuses()
    {
        fetch("/do.php?form=getProjectionDuration")
            .then(response => response.json())
            .then(data => {
                this.currentStatuses = data;
                this.newStatuses = structuredClone(data);
            });
    }

    constructor() {
        super();
        this.currentStatuses = [];
        this.newStatuses = [];
        this.isFetching = false;
        this.fetchStatuses();
    }

    async changeValue(event, index, groupIndex){
        const int = parseInt(event.target.value);
        if(!isNaN(int)) {
            this.newStatuses[groupIndex].statuses[index].statusDuration = int;
            this.newStatuses = structuredClone(this.newStatuses);
        }
        else {
            this.newStatuses[groupIndex].statuses[index].statusDuration = this.currentStatuses[groupIndex].statuses[index].statusDuration;
            this.newStatuses = structuredClone(this.newStatuses);
        }
    }

    async focusLost(event, index, groupIndex){
        const int = parseInt(event.target.value);
        if(isNaN(int))
        {
            this.newStatuses[groupIndex].statuses[index].statusDuration = this.currentStatuses[groupIndex].statuses[index].statusDuration;
            event.target.value = this.currentStatuses[groupIndex].statuses[index].statusDuration;
            this.newStatuses = structuredClone(this.newStatuses);
        }
    }


    render(){
        return html`
        <div style="position:relative">
            <div style="display: flex; flex-direction: column; align-items: center">
                <h1>Trajanje statusa za projekciju odlaska</h1>
                <div id="promjeneSpremljene">
                    <span style="font-size: 24px; color: white">Spremljeno!</span>
                </div>
            </div>
            <br />
            <div class="main">
                <form>
                    ${this.newStatuses.map((statusGroup, groupIndex) => html`
                        <div class="grupa-statusa">
                            <!-- NASLOV GRUPE STATUSA -->
                            <form-group-title title="${statusGroup.groupTitle}"></form-group-title>

                            <!-- INPUTI ZA STATUSE -->
                            ${statusGroup.statuses.map((status, statusIndex) => html`
                                <div class="form-group">
                                    <label class="control-label" style="display:flex; justify-content: space-between" for="${status.statusTitle}">
                                        <span>${status.statusTitle}</span>
                                        <tool-tip ?right="${groupIndex == 0}" ?left="${groupIndex == 3}">
                                            <span slot="content" style="cursor: pointer; font-size: 18px"><b>?</b></span>
                                            <span slot="tooltip">${status.statusDescription}</span>
                                        </tool-tip>
                                    </label>
                                    <div style="display:flex">
                                        <input 
                                             .value="${status.statusDuration}" 
                                             @input="${(event) => this.changeValue(event, statusIndex, groupIndex)}" 
                                             @focusout="${(event) => this.focusLost(event, statusIndex, groupIndex)}" 
                                             type="number" 
                                             min="0" 
                                             class="
                                                    form-control 
                                                    ${status.statusDuration !== this.currentStatuses[groupIndex].statuses[statusIndex].statusDuration ? "changed" : "blas"}
                                                    " 
                                             id="${status.statusId}"
                                             ?disabled=${this.isFetching}
                                            >
                                        <div class="inputSuffix">dana</div>
                                    </div>
                                </div>`
                            )}
                        </div>
                    `)}
                </form>

            </div>
            <div style="display:flex; justify-content:center; width:100%">
                <button 
                    @click="${(event) => { event.preventDefault(); this.postChanges(event); return false; }}"
                    class="${JSON.stringify(this.newStatuses) === JSON.stringify(this.currentStatuses) ? "invisible" : ""}"
                >
                    Spremi
                </button>
            </div>
        </div>


            <link href="/css/bootstrap.min.css" rel="stylesheet">
            <link href="/css/style.css" rel="stylesheet">
        `;
    }

    async postChanges(event){
        this.isFetching = true;

        let data = new FormData();
        this.newStatuses.forEach((group, groupIndex) => {
            group.statuses.forEach((status, statusIndex) => {
                if(status.statusDuration != this.currentStatuses[groupIndex].statuses[statusIndex].statusDuration)
                    data.append(status.statusId, status.statusDuration);
            })
        })

        try {
            let res = await fetch(`/do.php?form=setProjectionDuration`, {
                method: 'POST',
                body: data
            });

            let ret = await res.text();

            if(res.status >= 400 && res.status < 600){
                throw new Error(ret);
            }

            this.fetchStatuses();
            this.shadowRoot.querySelector("#promjeneSpremljene").style.opacity = 1;

            setTimeout(()=>{
                this.shadowRoot.querySelector("#promjeneSpremljene").style.opacity = 0;
            }, 3000);

        } catch (error) {
            alert(`Došlo je do problema! Kontaktirajte programere\n ${error}`);
        }
        this.updateSavedCandidateProjections();
        this.isFetching = false;
    }

    async updateSavedCandidateProjections(){
        try {
            let res = await fetch(`/cron_projekcije.php`, {
                method: 'POST'
            });

            let ret = await res.text();

            if(res.status >= 400 && res.status < 600){
                throw new Error(ret);
            }
        } catch (error) {
            alert(`Došlo je do problema! Kontaktirajte programere\n ${error}`);
        }
    }
    static styles = css`
            #promjeneSpremljene{
                position: absolute;
                top: 0px;
                right: 0px;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 10px;
                border-radius: 10px;
                background-color: #5cb85c;
                opacity: 0;
                transition: all 0.2s;
            }
            button {
                all: unset;
                margin-right: 5px;
                margin-left: 5px;

                cursor: pointer;

                border-radius: 5px;
                background-color: rgb(31, 102, 163);
                font-family: "Open Sans", sans-serif;
                font-size: 18px;
                padding-left: 5px;
                padding-right: 5px;
                height: 40px;
                width: 150px;
                text-align: center;
                color: white;
                opacity: 1;
                transition: all 0.3s;
            }
            .invisible{
                opacity: 0;
            }
            .changed{
                box-shadow: 0px 0px 5px rgba(50,150,150, 0.5);
                border: 2px solid red;
            }
            /* Chrome, Safari, Edge, Opera */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
              -webkit-appearance: none;
              margin: 0;
            }

            /* Firefox */
            input[type=number] {
              -moz-appearance: textfield;
            }
            .form-control{
                text-align:right;
            }
            .inputSuffix{
                user-select: none;
                margin-left: -5px;
                background-color: #e9ecef;
                vertical-align:middle;
                display:flex;
                justify-content: center;
                align-items:center;
                padding-left:5px;
                padding-right:5px;
                border-radius: 0px 4px 4px 0px;
                border: 1px solid lightgray;
            }
            .main{
                position: relative;
                display: flex;
                justify-content: center;
            }
            .form-group{
                padding-left: 20px;
                padding-right: 20px;
            }
            form{
                display: flex;
                justify-content: center;
                min-width: 80%;
            }
            .grupa-statusa{
                margin-left: 30px;
                margin-right: 30px;
            }
    `;
}

customElements.define('arrival-projection-form', ArrivalProjectionForm);


class FormGroupTitle extends LitElement {

    static properties = {
        title: {},
        color: {}
    };

    constructor() {
        super();
    }

    connectedCallback()
    {
        super.connectedCallback();
    }

    render(){
        return html`
            <div class="sep">
                <div class="myHR"></div>
                <span>${this.title}</span>
                <div class="myHR"></div>
            </div>
            <style></style>
        `;
    }
    static styles = css`
        .sep{
            display: flex;
            align-items: center;
            color: #bbb;
            font-size: 18px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .sep span{
            margin-left:  5px;
            margin-right: 5px;
        }
        .myHR{
            height: 1px;
            flex-grow: 1;
            background-color: #bbb;
        }
    `;
}
customElements.define('form-group-title', FormGroupTitle);