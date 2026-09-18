import { LitElement, html, css, keyed, ref, createRef } from "../js/lit-all.min.js";
import ScrollBooster from "./scrollbooster.js";

export class CandidateProjectionCalendar extends LitElement {
  static get properties() {
    return {
      // Data set after fetching from API
      data: {},
      // Candidate id attribute
      kandidat_id: {},
      // Boost scroll object (for drag scrolling)
      boost: {},
      // Width of a day in pixels, used for sizing table columns
      pixelsPerDay: {},
      // Months to display, generated from data
      months: {},
      // Used to set width of the overlay
      tableWidth: {},
      // Projected date when the candidate should start working
      candidateStartDate: 0
    };
  }

  constructor() {
    super();
    this.pixelsPerDay = 5;
  }

  firstUpdated(){
    this.fetchData();
  }

  render() {
    if (!this.data) 
      return html`
        <h1 style="text-align: center">Projekciju nije moguće prikazati.</h1>
        <h2 style="text-align: center">Molimo da obavijestite programere.</h2>
        `;

    return html`
      <!--
      <input type="number" @change="${(e) => this.updateCandidate(e.target.value)}" />
      <h1>${this.kandidat_id}</h1>
      -->

      ${this.createTitle()}

      <div @mousedown=${(e) => this.mouseDown(e)} id="wrap" class="scrollbar">
        <table>
          <thead>
            ${this.createHeading()}
          </thead>
          <tbody>
            <!-- Recruiting -->
            ${this.createTableRow(this.data[0])}
            ${this.createDetailRows(this.data[0])}
            <!-- DIPL -->
            ${this.createTableRow(this.data[1])}
            ${this.createDetailRows(this.data[1])}
            <!-- Language -->
            ${this.createTableRow(this.data[2])}
            ${this.createDetailRows(this.data[2])}
            <!-- Visa -->
            ${this.createTableRow(this.data[3])}
            ${this.createDetailRows(this.data[3])}
          </tbody>
        </table>
        ${this.createSidebarBorderHack()}
        ${this.createTodayMarkerLine()} 
        ${this.createDayLines()}
      </div>
      ${this.createZoomButtons()}
    `;
  }

  async fetchData() {
    let response = await fetch(`/dashboardNaloga/API_Projekcija.php?page=singleCandidate&kandidat_id=${this.kandidat_id}`);
    let data = await response.json();
    
    this.data = this.extractAndFormatData(data);
    this.months = this.generateMonths();
  }

  updated(changed) {
    this.createBoostScroll();
    this.updateTableScrollPosition(changed);
  }

  createBoostScroll(){
    if (!this.boost && this.shadowRoot?.querySelector("#wrap")) {
      this.boost = new ScrollBooster({
        viewport: this.shadowRoot.querySelector("#wrap"),
        scrollMode: "native",
        direction: "horizontal",
        emulateScroll: false,
      });
    }
  }
  updateTableScrollPosition(changed){
    if (
      (!this.boost || changed.has("data") || changed.has("pixelsPerDay")) &&
      this.shadowRoot?.querySelector("#wrap")
    ) {

      this.boost.updateMetrics();
      if(changed.has("data")){
        this.setScrollToThisMonth();
      }

      if(changed.has("pixelsPerDay")){
        const scroll =
          (this.boost.getState().position.x / changed.get("pixelsPerDay")) *
          this.pixelsPerDay;
        this.shadowRoot.querySelector("#wrap").scrollLeft = scroll;
      }
    }
  }

  setScrollToThisMonth(){
    this.shadowRoot.querySelector("#wrap").scrollLeft = this.generateThisMonthOffset()+1;
  }

  generateFirstAndLastMonths(){
    let firstStepDates = this.data.map((group) => group.steps[0].startDate.getTime());
    let lastStepDates = this.data.map((group) => group.steps[0].endDate.getTime());

    let firstDate = new Date(Math.min(...firstStepDates));
    let lastDate = new Date(Math.max(...lastStepDates));

    let firstMonth = new Date(firstDate.getFullYear(), firstDate.getMonth(), 1);
    // set firstMonth to previous month
    firstMonth.setMonth(firstMonth.getMonth() - 1);
    let lastMonth = new Date(lastDate.getFullYear(), lastDate.getMonth() + 7, 1);

    return [firstMonth, lastMonth];
  }

  generateMonths() {
    const [first, last] = this.generateFirstAndLastMonths();

    // add a month to last
    last.setMonth(last.getMonth() + 1);
    //generate array of months between the two dates
    // if the array has less than 13 months, pad it
    // with months from the other end
    let months = [];
    let month = first;
    while (month <= last) {
      months.push(month);
      month = new Date(month.getFullYear(), month.getMonth() + 1, 1);
    }

    month = new Date(last.getFullYear(), last.getMonth() + 1, 1);
    while (months.length < 13) {
      months.push(month);
      month = new Date(month.getFullYear(), month.getMonth() + 1, 1);
    }

    return months;
  }

  extractAndFormatData(data){
    var returnData = [];
    if(data[1].length){
      returnData.push({
        name: "Recruiting",
        color: "blue",
        isExpanded: false,
        startDate: new Date(data[1][0].start_date * 1000),
        endDate: new Date(data[1][data[1].length - 1].end_date * 1000),
        duration: Math.ceil(
          (data[1][data[1].length - 1].end_date - data[1][0].start_date) /
            86400
        ),
        steps: data[1].map((step) => {
          return {
            name: step.status_name,
            isFinal: step.is_final,
            startDate: new Date(step.start_date * 1000),
            endDate: new Date(step.end_date * 1000),
            duration: (() => {
              let duration = Math.ceil(
                (step.end_date - step.start_date) / 86400
              );
              return duration === 0 ? 1 : duration;
            })(),
          };
        }),
      });
    }
    if(data[2].length){
      returnData.push({
        name: "DIPL",
        color: "orange",
        isExpanded: false,
        startDate: new Date(data[2][0].start_date * 1000),
        endDate: new Date(data[2][data[2].length - 1].end_date * 1000),
        duration: Math.ceil(
          (data[2][data[2].length - 1].end_date - data[2][0].start_date) /
            86400
        ),
        steps: data[2].map((step) => {
          return {
            name: step.status_name,
            isFinal: step.is_final,
            startDate: new Date(step.start_date * 1000),
            endDate: new Date(step.end_date * 1000),
            duration: Math.ceil((step.end_date - step.start_date) / 86400),
          };
        }),
      });
    }
    if(data[3].length){
      returnData.push({
        name: "Jezik",
        color: "purple",
        isExpanded: false,
        startDate: new Date(data[3][0].start_date * 1000),
        endDate: new Date(data[3][data[3].length - 1].end_date * 1000),
        duration: Math.ceil(
          (data[3][data[3].length - 1].end_date - data[3][0].start_date) /
            86400
        ),
        steps: data[3].map((step) => {
          return {
            name: step.status_name,
            isFinal: step.is_final,
            startDate: new Date(step.start_date * 1000),
            endDate: new Date(step.end_date * 1000),
            duration: Math.ceil((step.end_date - step.start_date) / 86400),
          };
        }),
      });
    }
    if(data[4].length){
      returnData.push({
        name: "Viza",
        color: "green",
        isExpanded: false,
        startDate: new Date(data[4][0].start_date * 1000),
        endDate: new Date(data[4][data[4].length - 1].end_date * 1000),
        duration: Math.ceil(
          (data[4][data[4].length - 1].end_date - data[4][0].start_date) /
            86400
        ),
        steps: data[4].map((step) => {
          return {
            name: step.status_name,
            isFinal: step.is_final,
            startDate: new Date(step.start_date * 1000),
            endDate: new Date(step.end_date * 1000),
            duration: Math.ceil((step.end_date - step.start_date) / 86400),
          };
        }),
      });
    }
    this.candidateStartDate = data[5]; 
    return returnData;
  }

  /**
   * Returns the description of the step
   * @param {String} statusName Name of the status
   * @param {Date} fromDate Start date of the status
   * @param {Date} tillDate End date of the status
   * @param {Number} duration Duration of the status in days
   * @returns 
   */
  createLineDescription(statusName, fromDate, tillDate, duration, isFinal){
    let fromDateFormatted = fromDate.toLocaleDateString("sr-Latn-BA", { month: "short", day: "numeric" });
    let tillDateFormatted = tillDate.toLocaleDateString("sr-Latn-BA", { month: "short", day: "numeric" });

    if(isFinal){
      return `${statusName} ● ${fromDateFormatted}`;
    }
    else{
      return `${statusName} ● ${fromDateFormatted} - ${tillDateFormatted} ● ${duration} dana `;
    }
  }

  /**
   * Draws the line, starting at the given date, for the given duration
   * Depends on pixelsPerDay
   * @param {Date} fromDate Beginning of the period
   * @param {Date} tillDate End of the period
   * @param {Number} duration Duration of the period in days
   * @param {String} color Color of the period
   * @param {String} statusName Name of the status
   * @returns {html}
   */
  drawLine(fromDate, tillDate, duration, color, statusName, isFinal) {
    return html`
      <div
        data-content="${this.createLineDescription(statusName, fromDate, tillDate, duration, isFinal)}"
        style="width: ${duration *
        this.pixelsPerDay}px; left: ${(fromDate.getDate() - 1) *
        this.pixelsPerDay}px"
        class="${color} durationLine"
      ></div>
    `;
  }

  /**
   * When scrolling by dragging, change the mouse cursor to a grabbing cursor
   * @param {*} event 
   */
  mouseDown(event) {
    let el = this.shadowRoot.querySelector("#wrap");
    el.style.cursor = "grabbing";

    window.addEventListener("mouseup", this.mouseUp);
  }

  /**
   * When scrolling by dragging stops, change the mouse cursor back to default
   * @param {*} event 
   */
  mouseUp = (event) => {
    let el = this.shadowRoot.querySelector("#wrap");
    el.style.cursor = "grab";
    window.removeEventListener("mouseup", this.mouseUp);
  };

  /**
   * Expands or collapses the given step
   * @param {} data 
   */
  expandRow(data){
    data.isExpanded = !data.isExpanded; 
    this.requestUpdate(); 
  }

  /**
   *  Generates HTML for the sidebar cells (Recruiting, DIPL, Jezik, Viza)
   * @param {} data 
   * @returns {html}
   */
  createSidebarCell(data){
    return html`
        <td style="${data.name === "Viza" ? "border-right: none;" : ""}" @click=${() => this.expandRow(data)}>
          <div class="sidebar">
            <div class="circle ${data.color}"></div>
            <div style="display: flex; flex-grow:1; align-items: center; justify-content: space-between">
              <h2>${data.name}</h2>
            </div>
          </div>
        </td>
    `
  }

  generateThisMonthOffset(){
    let first = new Date(this.months[0].getTime());
    let today = new Date();
    today.setDate(1);
    let days = 0;

    while (
      first.getFullYear() !== today.getFullYear() ||
      first.getMonth() != today.getMonth() ||
      first.getDate() !== today.getDate()
    ) {
      days++;
      first.setDate(first.getDate() + 1);
    }

    return days * this.pixelsPerDay;

  }

  generateTodayOffset(){
    let first = new Date(this.months[0].getTime());
    let today = new Date();
    let days = 0;

    while (
      first.getFullYear() !== today.getFullYear() ||
      first.getMonth() != today.getMonth() ||
      first.getDate() !== today.getDate()
    ) {
      days++;
      first.setDate(first.getDate() + 1);
    }

    return days * this.pixelsPerDay;
  }

  /**
   * Checks if the two dates are in the same month
   * @param {Date} date1 
   * @param {Date} date2 
   * @returns 
   */
  isSameMonth(date1, date2){
    return date1.getMonth() === date2.getMonth() && date1.getFullYear() === date2.getFullYear();
  }

  /**
   * Generates the main table rows (Recruiting, DIPL, Jezik, Viza)
   * @param {} data 
   * @returns {html}
   */
  createTableRow(data) {
    if(data)
    return html`
      <tr>
        ${this.createSidebarCell(data)}
        ${this.months.map((month, index) => {
          if (this.isSameMonth(month, data.startDate)) {
            return html`
              <td class="${index === 0 ? "noLeftBorder" : ""}">
                ${this.drawLine( data.startDate, data.endDate, data.duration, data.color, `${data.name}`, false)}
              </td>`;
          } else {
            return html`<td class="${index === 0 ? "noLeftBorder" : ""}"></td>`;
          }
        })}
      </tr>
    `;
  }

  /**
   * Generates HTML for the detail rows
   * @param {} data 
   * @returns {html}
   */
  createDetailRows(data){
    if(data)
    return html`
      ${data.steps.map((step) => {
        return html`
          <tr>
            <td style="z-index: 3; height: unset; border: none" class="detailRow">
              <div class="innerTableWrapper innerSidebar ${data.isExpanded ? "active" : ""}">
                ${step.name}
              </div>
            </td>
            <td style="height: unset; border: none" colspan="${this.months.length}" >
              <div class="innerTableWrapper ${data.isExpanded ? "active" : ""}">
                <table>
                  <thead>
                    <tr>
                      ${this.months.map((month, index) => {
                        return html`
                          <th style="width: ${this.numberOfDays(month) * this.pixelsPerDay}px;
                                     ${index === 0 ? "border: none" : ""}">
                          </th>
                        `;
                      })}
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      ${this.months.map((month, index) => {
                        if (this.isSameMonth(month, step.startDate)) {
                          return html`
                          <td class="${index === 0 ? "noLeftBorder" : ""} detailRow">
                            ${this.drawLine(step.startDate, step.endDate, step.duration, data.color, step.name, step.isFinal)}
                          </td>`;
                        } else {
                          return html`
                          <td class="${index === 0 ? "noLeftBorder" : ""} detailRow"></td>`;
                        }
                      })}
                    </tr>
                  </tbody>
                </table>
              </div>
            </td>
          </tr>
        `;
      })}
    `
  }

  /**
   * Counts number of days in a month
   * @param {Date} month 
   * @returns {Date}
   */
  numberOfDays(month) {
    return new Date(month.getFullYear(), month.getMonth() + 1, 0).getDate();
  }

  calculateTableWidth(){
    return this.generateDaysOfAllMonths().length*this.pixelsPerDay+260
  }


  /**
   * Generates HTML for the table heading
   * @returns {html}
   */
  createHeading() {
    return html`
      <tr>
        <th class="noBorder" style="width: 260px;"></th>
        ${this.months.map((month, index) => {
          return html`
            <th
              style="width: ${this.numberOfDays(month) *
              this.pixelsPerDay}px; ${index === 0 ? "border: none" : ""}"
            >
              <h3>
                ${month.toLocaleDateString("default", {
                  month: "short",
                  year: "2-digit",
                })}
              </h3>
            </th>
          `;
        })}
      </tr>
    `;
  }

  /**
   * Generates HTML for the component title && subtitle
   * @returns {html}
   */
  createTitle() {
    let endDate = this.candidateStartDate;
    return html`
      <div style="display: flex; justify-content: space-between; align-items: flex-end">
        <h1>Status za projekciju odlaska</h1>
        <h2>Početak rada: ${endDate}</h2>
      </div>
    `;
  }

  /**
   * Generates HTML for the blinking line that indicates the current date
   * @returns {html}
   */
  createTodayMarkerLine() {

    let offset = this.generateTodayOffset() + 260;

    return html`
    <div id="todayMarker" style=" left: ${offset}px;">
    </div>`;
  }

  /**
   * Generates an array of dates for the months that are displayed
   * Used to draw the lines for the days
   * @returns {Array<Date>}
   */
  generateDaysOfAllMonths() {
    const allMonths = this.generateMonths();
    let first = allMonths[0];
    let last = allMonths[allMonths.length - 1];
    last = new Date(last.getFullYear(), last.getMonth() + 1, 0);

    let days = [];

    while (first <= last) {
      days.push(new Date(first));
      first.setDate(first.getDate() + 1);
    }

    return days;
  }


  /**
   * Generates HTML for the vertical lines that mark days in the calendar
   * Displayed only when the zoom level is set to 25px/day or more
   * @returns {html}
   */
  createDayLines() {
    if (this.pixelsPerDay < 25) return html``;

    let days = this.generateDaysOfAllMonths();

    const ret = days.map((day, index) => {
      return html` 
      <div class="day" style="position:absolute; height:100%; width: ${this .pixelsPerDay}px; top:0; left: ${260 + index * this.pixelsPerDay}px; ">
        <div style="height: 60px; width: 100%">
          <div style="font-size: 12px; position: absolute; top: 39px; width: 100%; text-align: center; color: rgba(0,0,0,0.5);  ">
            ${day.getDate()}
          </div>
        </div>
      </div>`;
    });

    return ret;
  }

  /**
   * Generates HTML for the zoom buttons
   * @returns {html}
   */
  createZoomButtons() {
    return html`
    <div class="zoomContainer">
      <div @click="${this.zoomIn}" class="zoomButton">+</div>
      <div @click="${this.zoomOut}" class="zoomButton">-</div>
    </div>`;
  }

  /**
   * Generates HTML for the overlay above the first column of the table
   * This is needed to make the table border rounded and is disgusting
   * Sorry, future me.
   * Blame the designer.
   * @returns {html}
   */
  createSidebarBorderHack() {
    return html`
      ${keyed(
        this.pixelsPerDay,
        html`
          <div id="overlay" style="position: absolute; left: 0; bottom: -1px; height: calc(100% + 2px); width: ${this.calculateTableWidth()}px">
            <div id="sidebarOverlay"></div>
          </div>
        `
      )}
    `;
  }

  zoomIn() {
    this.pixelsPerDay += 5;
  }

  zoomOut() {
    if (this.pixelsPerDay > 5) {
      this.pixelsPerDay -= 5;
    }
  }

  static styles = [
    css`
      :host {
        display: block;
        user-select: none;
        position: relative;
      }

      table {
        border-collapse: collapse;
        border-style: hidden;
        table-layout: fixed;
        width: 100%;
      }

      table td,
      table th {
        border-top: 1px solid #b8b8b8;
        border-left: 1px solid #b8b8b8;
        border-right: 1px solid #b8b8b8;
        background-color: white;
        padding: 0;
        box-sizing: border-box;
      }

      table table th,
      table table td {
        border-top: none;
        border-left: 1px solid #b8b8b8;
        border-right: 1px solid #b8b8b8;
      }

      td {
        height: 65px;
        position: relative;
      }

      /* First column stays fixed on scroll */
      td:first-child,
      th:first-child {
        position: sticky;
        left: 0;
        z-index: 3;
      }

      table table td:first-child {
        z-index: unset;
      }

      .noBorder {
        border: 0;
      }

      .day {
        border-left: 1px solid rgba(0, 0, 0, 0.03);
        z-index: 2;
        transition: background-color 0.05s;
      }

      .day:hover {
        transition: background-color 0s;
        background-color: rgba(50, 150, 250, 0.2);
      }

      .noLeftBorder {
        border-left: 1px solid white;
      }

      #sidebarOverlay {
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
        pointer-events: none;
        z-index: 3;
        position: sticky;
        width: 261px;
        height: 100%;
        left: 0;
        bottom: 0;
        border-right: 1px solid #b8b8b8;
        border-top: 1px solid #b8b8b8;
        border-bottom: 1px solid #b8b8b8;
        border-bottom-right-radius: 20px;
        border-top-right-radius: 20px;
        box-sizing: border-box;
      }
      h1,
      h2,
      h3 {
        font-family: "Open Sans", sans-serif;
        font-weight: 400;
      }

      .sidebar {
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: box-shadow 0.2s;
        margin-left: 20px;
        margin-right: 20px;
      }
      .circle {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        margin-right: 20px;
      }
      .blue {
        background-color: #0077ff;
      }
      .orange {
        background-color: #ffaa00;
      }
      .purple {
        background-color: #ff00b2;
      }
      .green {
        background-color: #0dbf4e;
      }
      #wrap {
        user-select: none;
        border: 1px solid #707070;
        border-radius: 20px;
        overflow-y: hidden;
        position: relative;
        cursor: grab;
      }

      .detailRow {
        height: 40px;
      }
      .active {
        height: 40px !important;
      }
      /* Customize website's scrollbar like Mac OS
        Not supports in Firefox and IE */
      .scrollbar {
        overflow-x: overlay;
      }

      /* total width */
      .scrollbar::-webkit-scrollbar {
        background-color: rgba(0, 0, 0, 0);
        width: 5px;
        height: 0px;
        z-index: 999999;
      }

      /* background of the scrollbar except button or resizer */
      .scrollbar::-webkit-scrollbar-track {
        background-color: rgba(0, 0, 0, 0);
        margin-left: 20px;
        margin-right: 20px;
      }

      /* scrollbar itself */
      .scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0);
        border-radius: 16px;
        border: 0px solid #fff;
      }

      /* set button(top and bottom of the scrollbar) */
      .scrollbar::-webkit-scrollbar-button {
        display: none;
      }

      /* scrollbar when element is hovered */
      .scrollbar:hover::-webkit-scrollbar-thumb {
        background-color: #b8b8b855;
      }

      /* scrollbar when scrollbar is hovered */
      .scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #b8b8b855;
      }
      .innerSidebar {
        padding-left: 30px;
      }
      .innerTableWrapper {
        height: 0;
        transition: height 0.2s ease-in-out;
        overflow-y: hidden;
      }
      .durationLine {
        position: absolute;
        top: 50%;
        transform: translate(0, -50%);
        height: 3px;
        z-index: 1;
        border-radius: 3px;
      }
      .durationLine::before {
        content: attr(data-content);
        position: absolute;
        bottom: 5px;
        left: 2px;
        font-size: 12px;
        white-space: nowrap;
        background-color: rgba(255, 255, 255, 0.4);
      }
      #todayMarker {
        background-color: rgba(50, 150, 250, 0.5);
        position: absolute;
        width: 3px;
        height: 100%;
        top: 0;
        animation-name: blink;
        animation-duration: 0.8s;
        animation-direction: alternate;
        animation-iteration-count: infinite;
        animation-timing-function: ease-in-out;
      }
      @keyframes blink {
        from {
          opacity: 0;
        }
        to {
          opacity: 1;
        }
      }

      .zoomButton {
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 50;
        width: 30px;
        height: 30px;
        border: 1px solid #b8b8b8;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        background: white;
        cursor: pointer;
      }

      .zoomContainer{
        position: absolute;
        bottom: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        z-index: 10;
      }
    `,
  ];
}
customElements.define("candidate-projection", CandidateProjectionCalendar);
