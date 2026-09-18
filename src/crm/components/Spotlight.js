import { LitElement, html, css, when, unsafeHTML } from "/js/lit-all.min.js";

export class Spotlight extends LitElement {
  static properties = {
    // Trenutno napisan search query
    query: { attribute: false },
    // Sve ponuđene opcije koje se pretražuju
    options: { attribute: false },
    // Niz objekata rezultata koje je pretraga pronašla
    results: { attribute: false },
    // Index trenutno odabranog rezultata
    focusedIndex: { attribute: false },
    // Kada korisnik klikne na rezultat, input se onemogućuje
    isDisabled: { attribute: false },
  };

  firstUpdated() {
    this.shadowRoot.querySelector("input").focus();
  }

  updated(changedProperties) {
    if (
      changedProperties.has("focusedIndex") &&
      changedProperties.get("focusedIndex") != undefined
    ) {
      const res = this.shadowRoot.querySelector("#results");

      if (this.focusedIndex === 0) {
        res.scroll(0, 0);
      } else {
        const child = res.children[this.focusedIndex - 1];
        res.scroll(0, child.offsetTop - 10);
      }
    }
  }

  smartAddToResults(search, object) {
    if (object.title.toLowerCase().startsWith(search)) {
      this.results = [object, ...this.results];
    } else {
      this.results = [...this.results, object];
    }
  }

  constructor() {
    super();
    this.results = [];
    this.isDisabled = false;
    this.options = [
      {
        condition: /^(?=[kandidati])k?a?n?d?i?d?a?t?i?$/,
        addToResult: (search) => {
          if (search === this.query) {
            this.smartAddToResults(search, {
              title: "Kandidati",
              subtitle: "Lista kandidata",
              url: "/kandidati?page=list_ajax",
            });
          }
        },
      },
      {
        condition: /^(?=[kandidat0-9])k?a?n?d?i?d?a?t? [0-9]+$/,
        addToResult: async (search) => {
          let id = search.split(" ")[1];
          let res = await fetch(`/do.php?form=kandidati&id=${id}`);
          let data = await res.json();

          if (search === this.query && data) {
            this.smartAddToResults(search.split(" ")[0], {
              title: `Kandidat ${data.kandidat_ime} ${data.kandidat_prezime}`,
              subtitle: "Profil kandidata",
              url: `/kandidati?page=open&id=${id}`,
            });
          }
        },
      },
      {
        condition: /^(?=[diplnp])d?i?p?l? ?n?p?$/,
        addToResult: (search) => {
          if (search === this.query) {
            this.smartAddToResults(search, {
              title: "DIPL NP",
              subtitle: "Nadzorni panel DIPL-a",
              url: "/dipl?page=pregledDIPL&type=1",
            });
          }
        },
      },
      {
        condition: /^(?=[dipl0-9])d?i?p?l? [0-9]+$/,
        addToResult: async (search) => {
          let id = search.split(" ")[1];
          let res = await fetch(`/do.php?form=kandidatiDIPL&id=${id}`);
          let data = await res.json();

          if (search === this.query && data) {
            this.smartAddToResults(search.split(" ")[0], {
              title: `DIPL ${data.kandidat_ime} ${data.kandidat_prezime}`,
              subtitle: "DIPL profil kandidata",
              url: `nostrifikacija_diploma?page=otvori_ND_kandidata&id=${id}`,
            });
          }
        },
      },
      {
        condition: /^(?=[nalozi])n?a?l?o?z?i?$/,
        addToResult: (search) => {
          if (search === this.query) {
            this.smartAddToResults(search, {
              title: "Nalozi",
              subtitle: "Lista naloga",
              url: "/nalozi.php",
            });
          }
        },
      },
      {
        condition: /^(?=[nalog0-9])n?a?l?o?g? [0-9]+$/,
        addToResult: async (search) => {
          let id = search.split(" ")[1];
          let res = await fetch(`/do.php?form=nalozi&id=${id}`);
          let data = await res.json();
          if (search === this.query && data) {
            this.smartAddToResults(search.split(" ")[0], {
              title: `Nalog ${data.nalog_naziv}`,
              subtitle: "Stranica naloga",
              url: `/nalozi?page=open&id=${id}`,
            });
          }
        },
      },
      {
        condition: /^(?=[kompanije])k?o?m?p?a?n?i?j?e?$/,
        addToResult: (search) => {
          if (search === this.query) {
            this.smartAddToResults(search, {
              title: "Kompanije",
              subtitle: "Lista kompanija",
              url: "/companies.php?page=list",
            });
          }
        },
      },
      {
        condition: /^(?=[kampanje])k?a?m?p?a?n?j?e?$/,
        addToResult: (search) => {
          if (search === this.query) {
            this.smartAddToResults(search, {
              title: "Kampanje",
              subtitle: "Lista kampanja",
              url: "/kampanje.php?page=list",
            });
          }
        },
      },
      {
        condition: /^(?=[naslovna])n?a?s?l?o?v?n?a?$/,
        addToResult: (search) => {
          if (search === this.query) {
            this.smartAddToResults(search, {
              title: "Naslovna",
              subtitle: "Home page",
              url: "/",
            });
          }
        },
      },
      {
        condition: /^(?=[dashboardnaloga])d?a?s?h?b?o?a?r?d? ?n?a?l?o?g?a?$/,
        addToResult: (search) => {
          if (search === this.query) {
            this.smartAddToResults(search, {
              title: "Dashboard naloga",
              subtitle: "Statistika naloga",
              url: "/dashboardNaloga/companies.php",
            });
          }
        },
      },
      {
        condition: /^(?=[pp])p?p?$/,
        addToResult: (search) => {
          if (search === this.query) {
            this.smartAddToResults(search, {
              title: "Pristup poslodavcima",
              subtitle: "PP App",
              url: "/jobstep_pp/dashboard.php",
            });
          }
        },
      },
      {
        condition: /.*/,
        addToResult: async (search) => {
          let imePrezime = search.split(" ").join("");
          if (!imePrezime) return;

          if (imePrezime.length < 4) return;

          await new Promise((r) => setTimeout(r, 1000));

          if (search !== this.query) return;

          let res = await fetch(
            `/do.php?form=fuzzyKandidat&search=${imePrezime}`
          );
          let data = await res.json();
          data.forEach((kandidat) => {
            if (search === this.query) {
              this.smartAddToResults(search, {
                title: `Kandidat ${kandidat.kandidat_ime} ${kandidat.kandidat_prezime}`,
                subtitle: "Profil kandidata",
                url: `/kandidati.php?page=open&id=${kandidat.kandidat_id}`,
              });
            }
          });
        },
      },
    ];
  }

  inputChanged(event) {
    let search = event.target.value.toLowerCase();
    this.focusedIndex = 0;
    this.results = [];
    this.query = search;
    this.options.forEach((o) => {
      if (o.condition.test(search)) {
        o.addToResult(search);
      }
    });
  }

  disableAllInputs() {
    this.results = [];
    this.isDisabled = true;
  }

  handleKeypress(e) {
    if (e.key === "Enter" && this.results.length != 0) {
      window.location = this.results[this.focusedIndex].url;
      this.disableAllInputs();
    }

    if (e.key === "ArrowUp") {
      if (this.focusedIndex === 0) {
        this.focusedIndex = this.results.length - 1;
      } else {
        this.focusedIndex = this.focusedIndex - 1;
      }
      e.preventDefault();
      return false;
    }
    if (e.key === "ArrowDown") {
      this.focusedIndex = (this.focusedIndex + 1) % this.results.length;
      e.preventDefault();
      return false;
    }
  }

  render() {
    return html`
      <div id="container">
        <div id="body">
          <div id="searchContainer">
            <search-icon></search-icon>
            ${when(!this.isDisabled,
              // Show input field
              () => html` <input
                id="inputField"
                @keydown="${this.handleKeypress}"
                placeholder="Pretraži sistem"
                type="text"
                @input="${this.inputChanged}"
              />`,
              // Show loader
              () => html`<a6-loader></a6-loader>`
            )}
          </div>
          ${when(!this.isDisabled, () =>
              html`
                <search-results
                  .results=${this.results}
                  .focusedIndex=${this.focusedIndex}
                  .query=${this.query}
                ></search-results>
              `
          )}
        </div>
      </div>
    `;
  }

  static styles = css`
    input:focus-visible {
      border: none;
      outline: none;
    }
    input {
      background-color: transparent;
      box-sizing: border-box;
      color: white;
      font-size: 18px;
      font-family: "Open Sans", sans-serif;
      border: none;
      width: 100%;
      height: 100%;
      margin-left: 15px;
    }
    input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    input::-moz-placeholder {
      color: rgba(255, 255, 255, 0.5);
    }
    #searchContainer {
      height: 50px;
      padding: 5px 10px;
      display: flex;
      align-items: center;
    }
    #body {
      display: flex;
      flex-direction: column;
    }

    @keyframes slide {
      from {
        margin-bottom: 50px;
        transform: translate(-50%, -50px);
        opacity: 0;
      }
      to {
        transform: translate(-50%, 0);
        opacity: 1;
      }
    }

    #container {
      position: fixed;
      top: 30%;
      left: 50%;
      transform: translate(-50%, 0);
      border-radius: 10px;
      overflow: hidden;
      width: 500px;
      border: 1px solid black;
      box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
      background-color: rgba(30, 30, 50, 0.5);
      backdrop-filter: blur(20px);
      z-index: 10000;
      animation-name: slide;
      animation-duration: 0.2s;
    }
  `;
}

export class SearchResults extends LitElement {
  static properties = {
    results: {},
    focusedIndex: {},
    query: {},
  };
  static styles = [
    css`
      #results {
        max-height: 400px;
        overflow-y: scroll;
        scroll-behavior: smooth;
      }
      /* Customize website's scrollbar like Mac OS
        Not supports in Firefox and IE */
      .scrollbar {
        overflow: overlay;
      }

      /* total width */
      .scrollbar::-webkit-scrollbar {
        background-color: rgba(0, 0, 0, 0);
        width: 5px;
        height: 16px;
        z-index: 999999;
      }

      /* background of the scrollbar except button or resizer */
      .scrollbar::-webkit-scrollbar-track {
        background-color: rgba(0, 0, 0, 0);
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
        background-color: #00000088;
      }

      /* scrollbar when scrollbar is hovered */
      .scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #00000088;
      }
      .option {
        padding: 5px 10px;
        color: white;
        user-select: none;
        cursor: pointer;
      }
      .focusedOption {
        background-color: rgb(51, 122, 183);
      }
      .optionTitle {
        font-size: 18px;
      }
      .optionSubtitle {
        font-size: 12px;
      }
    `,
  ];
  render() {
    return html`
      <div id="results" class="scrollbar">
        ${this.results.map((res, index) => {
          return html` <div
            @mousedown="${() => {
              location = res.url;
              this.results = [];
            }}"
            class="option ${index === this.focusedIndex ? "focusedOption" : ""}"
          >
            <div class="optionTitle">
              ${unsafeHTML(this.renderTitle(res.title))}
            </div>
            <div class="optionSubtitle">${res.subtitle}</div>
          </div>`;
        })}
      </div>
    `;
  }
  renderTitle(title) {
    return title
      .split("")
      .map((c) => {
        if (this.query.includes(c.toLowerCase())) return `<b>${c}</b>`;
        else return `${c}`;
      })
      .join("");
  }
}
customElements.define("search-results", SearchResults);

class SpotlightWithModal extends LitElement {
  static properties = {
    open: { attribute: false },
  };

  constructor() {
    super();
    this.open = false;
  }

  render() {
    return html`
      ${when(
        this.open,
        () => html`
          <div @mousedown="${() => this.close()}" id="backdrop"></div>
          <a6-spotlightbase></a6-spotlightbase>
        `,
        () => html``
      )}
    `;
  }

  connectedCallback() {
    super.connectedCallback();

    addEventListener("keydown", this.handleWindowKeydown);
  }

  handleWindowKeydown = (e) => {
    if (e.key === ".") {
      const a = e.ctrlKey;
    }
    if (e.ctrlKey && e.key === ".") {
      this.open = true;
      document.body.style.overflowY = "hidden";
    }

    if (e.key === "Escape") {
      this.close();
    }
  };

  close = () => {
    this.open = false;
    document.body.style.removeProperty("overflow-y");
  };

  static styles = css`
    #backdrop {
      position: fixed;
      backdrop-filter: blur(2px);
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(20, 20, 50, 0.4);
      z-index: 9999;
      animation-name: slidein;
      animation-duration: 0.2s;
    }
    @keyframes slidein {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }
  `;
}

export class SearchIcon extends LitElement {
  static styles = [css``];

  render() {
    return html`
      <svg
        version="1.0"
        xmlns="http://www.w3.org/2000/svg"
        width="25px"
        height="25px"
        viewBox="0 0 1244.000000 1280.000000"
        preserveAspectRatio="xMidYMid meet"
      >
        <g
          transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)"
          fill="#FFFFFF"
          stroke="none"
        >
          <path
            d="M4025 12789 c-1029 -79 -1969 -501 -2704 -1214 -985 -955 -1456
                                    -2292 -1285 -3650 156 -1244 849 -2360 1899 -3059 193 -129 272 -175 470 -274
                                    452 -227 906 -362 1445 -429 207 -25 763 -25 970 0 404 50 752 138 1115 281
                                    251 98 600 283 819 433 l80 54 1075 -1073 c3835 -3827 3770 -3762 3828 -3795
                                    189 -105 411 -75 563 77 148 148 180 359 84 553 -21 43 -462 488 -2432 2459
                                    -2212 2213 -2404 2408 -2392 2425 8 10 40 47 70 83 714 836 1088 1927 1031
                                    3011 -32 610 -165 1136 -420 1664 -169 349 -340 615 -592 920 -106 128 -395
                                    417 -524 524 -687 569 -1463 900 -2336 996 -174 19 -598 27 -764 14z m780
                                    -949 c777 -118 1453 -463 1982 -1014 516 -536 829 -1194 930 -1951 24 -186 24
                                    -618 0 -810 -54 -416 -158 -758 -342 -1125 -297 -593 -779 -1101 -1360 -1432
                                    -964 -549 -2153 -590 -3152 -108 -975 470 -1667 1364 -1873 2420 -37 192 -51
                                    323 -57 555 -6 258 4 423 42 651 161 971 742 1831 1588 2348 453 278 935 434
                                1512 490 22 2 164 3 315 1 217 -3 304 -8 415 -25z"
          />
        </g>
      </svg>
    `;
  }
}
export class Loader extends LitElement {
  static styles = [
    css`
      .dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        margin-left: 3px;
        margin-right: 3px;
        animation-name: jump;
        animation-timing-function: ease-in-out;
        animation-duration: 0.7s;
        animation-direction: alternate;
        animation-iteration-count: infinite;
        transform: translate(0, -10px);
        box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.5);
      }
      @keyframes jump {
        0% {
          transform: translate(0, -10px);
          background-color: rgba(0, 200, 100, 0.7);
        }
        50% {
          background-color: rgba(0, 200, 200, 0.5);
        }
        100% {
          transform: translate(0, 10px);
          background-color: rgba(0, 200, 100, 0.7);
        }
      }
      .container {
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
      }
      #uspide {
        transform: translate(-50%, -50%) rotateX(180deg);
      }
      :host {
        position: relative;
        width: 100%;
      }
    `,
  ];

  render() {
    return html`
      <div class="container">
        ${[...Array(39).keys()].map(
          (i) =>
            html`<div
              class="dot"
              style="animation-delay: ${Math.abs(18 - i) / 18}s"
            ></div>`
        )}
      </div>
      <div id="uspide" class="container">
        ${[...Array(39).keys()].map(
          (i) =>
            html`<div
              class="dot"
              style="animation-delay: ${Math.abs(18 - i) / 18}s"
            ></div>`
        )}
      </div>
    `;
  }
}
customElements.define("a6-loader", Loader);

customElements.define("search-icon", SearchIcon);

customElements.define("a6-spotlightbase", Spotlight);
customElements.define("a6-spotlight", SpotlightWithModal);
