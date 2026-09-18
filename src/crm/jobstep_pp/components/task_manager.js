import { LitElement, css, html, when } from "../javascript/lit-all.min.js";
class TranslationModuleLit extends LitElement {
  constructor() {
    super();
    this.translations = [];
    this.user_language = [];
  }

  async initialize() {
    await fetch("ajax.php?action=get_translation")
      .then((response) => response.json())
      .then((data) => {
        this.translations = JSON.parse(data.translation);
        this.user_language = data.user_language;
      });
  }

  getTranslation(word) {
    return this.translations[word][this.user_language];
  }
}
class TaskManager extends TranslationModuleLit {
  static properties = {
    selectedReminderTypes: {},
    company_name: {},
    translationModule: {},
  };
  static styles = css`
    .button_back_to_dashboard {
      cursor: pointer;
      margin-top: 1.5rem;
      margin-bottom: 1.5rem;
      margin-left: 1rem;
      position: relative;
      z-index: 2;
    }
    .company_name {
      margin: 0px 0px 0px 4.5rem;
      position: absolute;
      top: 46%;
      transform: translateY(-50%);
      padding-left: 20%;
      font-size: 20px;
      z-index: 1;
    }
    .text_task_manager {
      text-align: right;
      font-size: 20px;
      color: rgb(112, 112, 126);
      margin: 0px;
      position: absolute;
      top: 46%;
      transform: translateY(-50%);
      width: 95%;
    }
  `;
  constructor() {
    super();
    this.selectedReminderTypes = "";
    this.getCompanyNameByUserID();
  }

  getCompanyNameByUserID() {
    fetch("do.php?page=tm_get_company_name_by_user_id")
      .then((response) => response.json())
      .then((data) => {
        this.company_name = data;
      });
  }

  updateSelectedReminderTypes(reminder_ids) {
    if (reminder_ids != null) {
      this.selectedReminderTypes = reminder_ids.join(",");
    }
  }
  backToDashboard() {
    $.ajax({
      url: "ajax.php?action=return_reminder_number",
      type: "POST",
      dataType: "json",
      dataType: "html",
      success: function (data) {
        document.querySelector(
          "#main-navbar > header > ul > li.nav-item.me-2.ms-2 > div"
        ).innerHTML = data;
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      },
    });

    $("task-manager").hide("fade", function () {
      $("#main-navbar").show("blind", 300, function () {
        $("#main-container").show(function () {
          //   location.reload(); // Add this line to refresh the page
        });
      });
    });
  }
  render() {
    return html`
            <div style = "display:flex;position:relative">
                <div style = "width:20%;flex: 0 0 auto;font-size:20px; position: relative;" >
                    <img class = "button_back_to_dashboard" @click=${this.backToDashboard} src = "images/fa-angle-left.svg">
                </div>

                <div class = "company_name">${this.company_name}</div>
                <div class = "text_task_manager">TASK MANAGER</div>
            </div>
            <div style = "display:flex;width:100%;height:90%">
                <div style = "width:20%;flex: 0 0 auto;">
                    <tm-menu></tm-menu>
                </div>
                    <tm-unassigned-tasks @reminderTypeSelected=${this.handleReminderSelected} style = "width: 100%;height: 100%;"></tm-unassigned-tasks>
                </div>
            </div>
        `;
  }
}

class TMMenu extends TaskManager {
  static properties = {
    reminderTypes: {},
  };

  static styles = css`
    .tm_menu_selected {
      background-color: #eeeef2;
    }
    .tm_menu {
      padding-top: 10px;
      padding-bottom: 10px;
      padding-right: 3rem;
      cursor: pointer;
      font-size: 14px;
      padding-left: 2rem;
    }
    .tm_box_shadow {
      background-color: white;
      box-shadow: 0px 8px 24px rgb(112 144 176 / 15%);
    }
    .tm_box_radius_top {
      border-top-right-radius: 30px;
      border-top-left-radius: 30px;
    }
    .tm_box_radius_bot {
      border-bottom-left-radius: 30px;
      border-bottom-right-radius: 30px;
    }
    .notification_count {
      color: white;
      background-color: #e91c24;
      text-align: center;
      border-radius: 50%;
      position: absolute;
      height: 25px;
      width: 25px;
      left: 18%;
    }
    .display_none {
      display: none;
    }
  `;

   // Implement the requestUpdate() method
  manualUpdate() {
    this.getReminderTypes();
  }

  getReminderTypes() {
    fetch("do.php?page=tm_get_reminder_types")
      .then((response) => response.json())
      .then((data) => {
        this.reminderTypes = structuredClone(data);
      })
      .then((data) => {
        let tempReminderTypes = this.reminderTypes;
        let flag_found = false;
        tempReminderTypes.forEach((reminderType) => {
          if (reminderType.reminder_count && !flag_found) {
            flag_found = true;
            reminderType.selected = true;
            this.handleOnLoadReminders(reminderType.reminder_type);
          }
        });
        this.reminderTypes = tempReminderTypes;
      });
  }

  constructor() {
    super();
    this.getReminderTypes();
  }
  handleOnLoadReminders(selected_reminder) {
    let selected_reminders = [];

    selected_reminders.push(selected_reminder);
    super.updateSelectedReminderTypes(selected_reminders);
    window.dispatchEvent(
      new CustomEvent("reminderTypeSelected", {
        detail: { selected_reminders: selected_reminders.join(",") },
      })
    );
  }
  handleReminderTypeClick(e) {
    var selected_reminder = e.target.getAttribute("id");
    let temp = structuredClone(this.reminderTypes);
    let selected_reminders = [];
    var flag_already_selected = false;
    temp.forEach((reminderType) => {
      if (reminderType.reminder_type == selected_reminder) {
        if (reminderType.selected) {
          flag_already_selected = true;
          reminderType.selected = !reminderType.selected;
        }
      }
    });
    if (flag_already_selected) {
      super.updateSelectedReminderTypes(selected_reminders);
      window.dispatchEvent(
        new CustomEvent("reminderTypeSelected", {
          detail: { selected_reminders: selected_reminders.join(",") },
        })
      );
    } else {
      temp.forEach((reminderType) => {
        if (reminderType.reminder_type == selected_reminder) {
          reminderType.selected = !reminderType.selected;
          selected_reminders.push(reminderType.reminder_type);
          super.updateSelectedReminderTypes(selected_reminders);
          window.dispatchEvent(
            new CustomEvent("reminderTypeSelected", {
              detail: { selected_reminders: selected_reminders.join(",") },
            })
          );
        } else {
          reminderType.selected = false;
        }
      });
    }
    this.reminderTypes = temp;
  }

  render() {
    return html`
      <div
        class="tm_box_shadow tm_box_radius_top"
        style="min-height:100%;padding-top: 2rem;"
      >
        ${this.reminderTypes?.map(
          (row) => html`
            <div
              id="${row.reminder_type}"
              is_selected="${row.selected}"
              @click=${this.handleReminderTypeClick}
              class=${row.selected ? "tm_menu tm_menu_selected" : "tm_menu"}
            >
              <span
                class=${row.reminder_count
                  ? "notification_count"
                  : "display_none"}
                >${row.reminder_count}</span
              >
              ${row.reminder_name}
            </div>
          `
        )}
      </div>
    `;
  }
}
class TMUnassignedTasks extends TranslationModuleLit {
  static properties = {
    selectedReminderTypes: {},
    assignedCandidateCardsInfo: {},
    unassignedCandidateCardsInfo: {},
    assigned: {},
    isCardSelected: {},

    text_assigned_tasks: {},
    text_assign: {},
    text_task_assignment: {},
    text_message_assigned_singular: {},
    text_message_taken_singular: {},
    text_message_finished_singular: {},
    text_message_assigned_plural: {},
    text_message_taken_plural: {},
    text_message_finished_plural: {},
  };

  static styles = css`
    .unassigned_task_card_selected {
      background-color: #1c84ee;
    }
    /* .unassigned_task_card{
            cursor:pointer;
            min-width:100%;
            box-shadow: 0px 8px 24px rgb(112 144 176 / 15%);
            border-radius:10px;
            padding:5px;
            height: 115px;
            
        } */
    .unassigned_task_box_shadow {
      background-color: white;
      box-shadow: 0px 8px 24px rgb(112 144 176 / 15%);
      border-radius: 30px;
      overflow: auto;
      /* margin: 0px 0% 10%; */
      height: 60%;
      -ms-overflow-style: none;
      scrollbar-width: none;
      overflow-y: scroll;
    }
    /* .unassigned_task_box_shadow::-webkit-scrollbar {
      display: none;
    } */
    .scrollBarVertical::-webkit-scrollbar {
        width:12px;
        margin-top: 10px;
    }
    .scrollBarVertical::-webkit-scrollbar-thumb {
        background: #1D84C0;
        border-radius: 6px;
    }
    .scrollBarVertical::-webkit-scrollbar-track {
        background-color: #E9ECEF;
        border-radius: 5px;
    }
    .assigned_task_box_shadow {
      overflow: auto;
      margin: 20px;
      height: 36.5%;
      -ms-overflow-style: none;
      scrollbar-width: none;
      overflow-y: scroll;
    }
    .assigned_task_box_shadow::-webkit-scrollbar {
      display: none; 
    }
    .display_none {
      display: none;
    }
    .display_block {
      display: block;
    }
    .button_take_assignment {
      background-color: white;
      box-shadow: 0px 8px 24px rgb(112 144 176 / 15%);
      border-radius: 9px;
      padding-top: 5px;
      padding-bottom: 5px;
      padding-right: 15px;
      padding-left: 15px;
      margin: 20px;
      width: fit-content;
      font-weight: bold;
      position: relative;
      right: -91.5%;
      cursor: pointer;
    }
    .button_take_assignment_selected {
      background-color: #1c84ee;
      color: white;
    }
    .assigned_task_card {
      position: relative;
      cursor: pointer;
      box-shadow: rgb(112 144 176 / 15%) 0px 8px 24px;
      border-radius: 9px;
      padding: 5px;
      background-color: white;
      padding-right: 15px;
      padding-left: 15px;
      float: left;
      margin: 5px;
      min-width: 19%;
    }
    a:link {
      text-decoration: none;
      color: black;
    }

    a:visited {
      text-decoration: none;
      color: black;
    }

    a:hover {
      text-decoration: none;
      color: black;
    }

    a:active {
      text-decoration: none;
      color: black;
    }
  `;

  constructor() {
    super();
    this.selectedReminderTypes = [];
    this.assignedCandidateCardsInfo = [];
    this.unassignedCandidateCardsInfo = [];
    this.isCardSelected = false;

    this.text_assigned_tasks = "";
    this.text_assign = "";
    this.text_task_assignment = "";
    this.text_message_assigned_singular = "";
    this.text_message_taken_singular = "";
    this.text_message_finished_singular = "";
    this.text_message_assigned_plural = "";
    this.text_message_taken_plural = "";
    this.text_message_finished_plural = "";
    this.initializeTranslations();
  }

  async initializeTranslations() {
    await super.initialize();

    this.text_assigned_tasks = super.getTranslation("Preuzeti zadaci");
    this.text_assign = super.getTranslation("Preuzmi zadatak");
    this.text_task_assignment = super.getTranslation("Dodjela zadataka");
    this.text_message_assigned_singular = super.getTranslation(
      "novi zadatak Vam je dodijeljen!"
    );
    this.text_message_taken_singular = super.getTranslation(
      "odabrani zadatak je već zauzet!"
    );
    this.text_message_finished_singular = super.getTranslation(
      "odabrana zadatka su već zauzeta!"
    );
    this.text_message_assigned_plural = super.getTranslation(
      "nova zadatka su Vam dodijeljena!"
    );
    this.text_message_taken_plural = super.getTranslation(
      "odabrani zadatak je već završen!"
    );
    this.text_message_finished_plural = super.getTranslation(
      "odabrana zadatka su već završena!"
    );
  }

  isAnyCardSelected() {
    var flag_found_selected = false;
    this.unassignedCandidateCardsInfo.forEach((cardData) => {
      if (cardData.is_selected) {
        flag_found_selected = true;
      }
    });
    if (flag_found_selected) this.isCardSelected = true;
    else this.isCardSelected = false;
  }

  connectedCallback() {
    super.connectedCallback();
    window.addEventListener(
      "reminderTypeSelected",
      this.handleReminderSelected
    );
  }
  fetchCandidateCardsInfo() {
    fetch(
      "do.php?page=tm_get_candidate_cards&selected_reminders=" +
        this.selectedReminderTypes
    )
      .then((response) => response.json())
      .then((data) => {
        var assigned_tasks = [];
        var unassigned_tasks = [];
        data.forEach(function (reminder) {
          if (reminder.reminderStatus == 1) {
            unassigned_tasks.push(reminder);
          } else if (reminder.reminderStatus == 2) {
            assigned_tasks.push(reminder);
          }
        });
        this.assignedCandidateCardsInfo = structuredClone(assigned_tasks);
        this.unassignedCandidateCardsInfo = structuredClone(unassigned_tasks);
        this.isAnyCardSelected();
      });
  }
  handleReminderSelected = (e) => {
    this.selectedReminderTypes = e.detail.selected_reminders;
    this.fetchCandidateCardsInfo();
  };
  handleUnassignedCardClick = (e) => {
    var selected_card = e.target.getAttribute("id");
    let temp = structuredClone(this.unassignedCandidateCardsInfo);
    temp.forEach((cardData) => {
      if (cardData.reminderId == selected_card) {
        cardData.is_selected = !cardData.is_selected;
      }
    });
    this.unassignedCandidateCardsInfo = temp;
    this.isAnyCardSelected();
  };

  handleTakeTaskClick() {
    var reminders_assigned = 0;
    var reminders_taken = 0;
    var reminders_finished = 0;
    var count_tasks_selected = 0;
    var selected_tasks_handled = 0;

    let self = this;

    this.unassignedCandidateCardsInfo.forEach(function (taskCard) {
      if (taskCard.is_selected) {
        count_tasks_selected++;
      }
    });
    if (count_tasks_selected != 0) {
      getLoaderBig();
    }
    this.unassignedCandidateCardsInfo.forEach(function (taskCard) {
      if (taskCard.is_selected) {
        getAccessControl(
          taskCard.nalogId,
          taskCard.candidateCheck,
          taskCard.partnerId,
          taskCard.reminderType,
          function (response_access_control) {
            selected_tasks_handled++;
            var reminder_status = response_access_control["status"];
            var reminder_id = response_access_control["id"];
            var reminder_assigned_user = response_access_control["assigned"];
            var reminder_is_logged_user = response_access_control["isLogged"];
            var reminder_response_message = response_access_control["message"];

            if (
              reminder_status == 101 ||
              reminder_status == 1 ||
              (reminder_status == 2 && reminder_is_logged_user == 1)
            ) {
              reminders_assigned++;

              updateReminderStatus(taskCard.reminderId, 2, function () {
                self.fetchCandidateCardsInfo();
              });
            } else if (reminder_status == 104) {
              showToast(reminder_response_message, reminder_response_title);
            } else if (reminder_status == 2 && reminder_is_logged_user == 0) {
              reminders_taken++;
            } else if (reminder_status == 3) {
              reminders_finished++;
            }
            if (count_tasks_selected == selected_tasks_handled) {
              var message = "";
              var flag_show_toast = 0;
              if (reminders_assigned) {
                message +=
                  "<b>" +
                  reminders_assigned +
                  "</b> " +
                  (reminders_assigned == 1
                    ? self.text_message_assigned_singular
                    : self.text_message_assigned_plural) +
                  "<br>";
                flag_show_toast = 1;
              }
              if (reminders_taken) {
                message +=
                  "<b>" +
                  reminders_taken +
                  "</b> " +
                  (reminders_taken == 1
                    ? self.text_message_taken_singular
                    : self.text_message_taken_plural) +
                  "<br>";
                flag_show_toast = 1;
              }
              if (reminders_finished) {
                message +=
                  "<b>" +
                  reminders_finished +
                  "</b> " +
                  (reminders_finished == 1
                    ? self.text_message_finished_singular
                    : self.text_message_finished_plural) +
                  "<br>";
                flag_show_toast = 1;
              }
              showToast(message, self.text_task_assignment);
              removeLoader();
            }
          }
        );
      }
    });
  }
  render() {
    return html`
      <div style="width:90%;flex: 0 0 auto;height:86vh;margin-left: 4.5rem;">
        <div class="unassigned_task_box_shadow scrollBarVertical">
          <div style="background-color:white;min-height:100%;padding:10px;">
            ${this.unassignedCandidateCardsInfo?.map(
              (cardData) => html`
                <unassigned-card
                  .cardData=${cardData}
                  .click=${this.handleUnassignedCardClick}
                ></unassigned-card>
              `
            )}
          </div>
        </div>
        <div
          class=${this.isCardSelected
            ? "button_take_assignment button_take_assignment_selected"
            : "button_take_assignment"}
          @click=${this.handleTakeTaskClick}
        >
          ${this.text_assign}
        </div>
        <div class="assigned_task_box_shadow">
          <b> ${this.text_assigned_tasks}</b>
          <div style="min-height:100%;padding:10px;">
            ${this.assignedCandidateCardsInfo?.map(
              (cardData) => html`
                <div
                  id="${cardData.reminderId}"
                  is_selected="${cardData.is_selected}"
                  class="assigned_task_card"
                >
                  <div
                    style="width: 10%;position: relative;float: left;text-align: center;padding-top: 3px;"
                  >
                    <img src="images/fa-user-circle.png" />
                  </div>
                  <div style="width: 87%;float: left;">
                    <a
                      target="_blank"
                      href="/profile?kandidat_id=${cardData.candidateCheck}&n=${cardData.nalogId}"
                      >${cardData.candidateFname} ${cardData.candidateLname}</a
                    >
                  </div>
                </div>
              `
            )}
          </div>
        </div>
      </div>
    `;
  }
}

export class UnassignedCard extends TranslationModuleLit {
  static get properties() {
    return {
      cardData: {},
      click: {},

      text_accept_task: {},
      text_dont_accept_task: {},
    };
  }

  constructor() {
    super();
    this.text_accept_task = "";
    this.text_dont_accept_task = "";
    this.initializeTranslations();
  }

  async initializeTranslations() {
    await super.initialize();

    this.text_accept_task = super.getTranslation("Preuzet");
    this.text_dont_accept_task = super.getTranslation("Nije preuzet");
  }
  static styles = [
    css`
      @keyframes wave {
        0% {
          clip-path: inset(0px 100% 0px 0px);
        }
        100% {
          clip-path: inset(0px 0px 0px 0px);
        }
      }
      :host {
        min-width: 30%;
        padding-top: 10px;
        padding-bottom: 10px;
        padding-right: 20px;
        padding-left: 20px;
        float: left;
        margin-top: 10px;
      }
      .unassigned_task_card {
        position: relative;
        /* background: #FFFFFF; */
        height: 50vh;
        cursor: pointer;
        min-width: 100%;
        box-shadow: rgb(0 0 0 / 16%) 0px 10px 30px;
        border-radius: 9px;
        padding: 5px;
        height: 115px;
        overflow: hidden;
        user-select: none;
        transition: box-shadow 0.2s ease-in-out;
      }
      /* .unassigned_task_card_selected{
                box-shadow: 0px 0px 18px rgb(112 144 176 / 10%), 0px 0px 10px inset rgb(112 144 176 / 32%);
            } */

      .wave {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
        pointer-events: none;
        animation: wave 0.5s ease-in-out forwards;
      }

      .wave svg {
        position: relative;
        display: block;
        width: calc(245% + 1.3px);
        height: 70%;
        pointer-events: none;
      }

      .wave .wave-fill {
        fill: #1c84ee;
        pointer-events: none;
      }

      .badge{
        background-color: rgba(220, 228, 235, 255);
        display: inline-block;
        padding: 0.25em 0.75em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 1.25rem;
        border: 3px solid white;
      }

    `,
  ];

  reminderLevelView(level){

    var result = "";
    
    if (level < 5) {
      result = level;
    } else {
      result = "5+";
    }
    
    return html`<span class="badge">Level ${result}</span>`;
  
  }

  render() {
    return html`
      <div
        id="${this.cardData.reminderId}"
        is_selected="${this.cardData.is_selected}"
        @click=${this.click}
        class=${this.cardData.is_selected
          ? "unassigned_task_card unassigned_task_card_selected"
          : "unassigned_task_card"}
      >
        ${when(
          this.cardData.is_selected,
          () => html`
            <div class="wave">
              <svg
                data-name="Layer 1"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 1050 120"
                preserveAspectRatio="none"
              >
                <path
                  d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z"
                  class="wave-fill"
                ></path>
              </svg>
            </div>
          `,
          () => html``
        )}
        <div
          style="width:100%; height: 100%; padding:5% 5% 5% 2%; display: flex; box-sizing: border-box; pointer-events: none"
        >
          <div style="position: relative; height:100%; flex: 1;">
            <div
              style="background-color:rgba(220, 228, 235, 255); position: absolute; top:50%; left:50%; transform: translate(-50%, -50%); width: 60px; height: 60px; border-radius:50%; border: 3px solid white; overflow: hidden; display:flex; align-items:center; justify-content: center"
            >
              <div
                style="white-space:nowrap; font-size: 28px; letter-spacing:-2px; text-transform: capitalize; text-align:center;"
              >
                ${this.cardData.candidateFname.charAt(0)}
                ${this.cardData.candidateLname.charAt(0)}
              </div>
            </div>
          </div>
          <div
            style="height:100%; flex: 5; display: flex; flex-direction: column; justify-content: space-between; z-index:1; padding-left: 20px; padding-right: 10px"
          >
            <div style="font-size: 18px; font-weight: bold; display:flex; justify-content: space-between;">
              <div>
                ${this.cardData.candidateFname} ${this.cardData.candidateLname} 
              </div>    
              <div style="display: flex; align-items: center">
                ${this.reminderLevelView(this.cardData.reminderLevel)}
              </div>
            </div>
            <div
              style="font-size: 14px; display:flex; justify-content: space-between; align-items: center; color: ${this
                .cardData.is_selected
                ? "white"
                : "black"}; transition: color 0.5s;"
            >
              <div>${this.cardData.reminderDate}</div>
              <div style="display: flex; align-items: center">
                <!-- Fake checkbox -->
                <div
                  style="margin-right: 5px; width: 20px; height: 20px; border: ${this
                    .cardData.is_selected
                    ? "1px solid white"
                    : "1px solid black"}; border-radius: 3px; transition: all 0.5s"
                >
                  ${when(
                    this.cardData.is_selected,
                    () => html`
                      <svg
                        style="width: 100%; height: 100%; fill: white"
                        viewBox="0 0 24 24"
                      >
                        <path
                          d="M9,16.17L4.83,12L3.41,13.41L9,19L21,7L19.59,5.59L9,16.17Z"
                        />
                      </svg>
                    `,
                    () => html``
                  )}
                </div>

                ${when(
                  this.cardData.is_selected,
                  () => html`<!-- this.text_accept_task -->`,
                  () => html`<!-- ${this.text_dont_accept_task} -->`
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }
}
customElements.define("unassigned-card", UnassignedCard);

class TMAssignedTasks extends TaskManager {
  render() {
    return html` <h1>Assigned</h1> `;
  }
}
customElements.define("task-manager", TaskManager);
customElements.define("tm-menu", TMMenu);
customElements.define("tm-unassigned-tasks", TMUnassignedTasks);
customElements.define("tm-assigned-tasks", TMAssignedTasks);
