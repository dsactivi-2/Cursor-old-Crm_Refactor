import { LitElement, html, css } from "/js/lit-all.min.js";
import "./ChatBotQuestion.js";

class ChatBotList extends LitElement {
  static properties = {
    data: { type: Array },
    collapsedGroups: { type: Object },
  };
  createRenderRoot() {
    return this;
  }
  constructor() {
    super();
    this.collapsedGroups = {};
  }

  toggleExpanded(question) {
    this.data = this.data.map((d) => {
      if (d.id === question.id) {
        d.isExpanded = !d.isExpanded;
      }
      return d;
    });
    this.requestUpdate();
  }

  renderQuestion2(questions, isMain = true, depth = 0) {
  if (!questions) return html``;
  if(isMain)
    questions = this.data.filter(q => !q.isSubquestion);

  return html`
    <style>
      .question-container {
      border: 1px solid #ccc;
      border-radius: 5px;
      margin-bottom: 10px;
      padding: 10px;
      cursor: pointer;
      z-index: 0;
    }

    .question-container:hover {
      background-color: #f5f5f5;
    }

    .sub-questions {
      margin-left: 20px;
    }
    </style>
    <div class="questions-container" style="margin-left: ${depth * 60}px;">
      ${questions.map((q) => html`
          <chat-bot-question
          .questionData="${q}"
          .handleButtonClick="${() => this.toggleExpanded(q)}"
          .depth="${depth}"
          ></chat-bot-question>
        ${q.isExpanded ? this.renderSubquestions(q.subquestions, depth + 1) : html``}
      `)}
    </div>
  `;
}

renderSubquestions(subquestionIds,depth) {
  const subquestions = this.data.filter(q => subquestionIds.includes(q.id));
  return this.renderQuestion2(subquestions, false, depth);
}


  render() {
    return this.renderQuestion2(this.data);
  }
}

customElements.define("chat-bot-list", ChatBotList);
