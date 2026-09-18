class Tooltip extends HTMLElement{
    constructor() {
        super();
    }

    connectedCallback() {
        if(this.shadowRoot)
            return;
        this.attachShadow({mode: 'open'}).innerHTML = `
            <div class="a6_tooltip">
                <slot name="content"></slot>
                <div class="
                                a6_tooltip_content 
                                ${this.hasAttribute("left") ? "left" : ""} 
                                ${this.hasAttribute("right") ? "right" : ""}
                                ${!this.hasAttribute("right") && !this.hasAttribute("left") ? "top" : ""}
                            " 
                >
                    <div style="min-width: 200px">
                        <div style="display: flex; justify-content: center">
                            <slot name="tooltip"></slot>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                .a6_tooltip {
                    position: relative;
                    display: inline-block;
                }

                .left{
                    right: 200%;
                    top: 50%;
                    transform: translate(0,-50%);
                }

                .right{
                    left: 200%;
                    top: 50%;
                    transform: translate(0,-50%);
                }

                .top{
                    bottom: 120%;
                    left: 50%;
                    transform: translate(-50%,0);
                }

                /* Tooltip text */
                .a6_tooltip .a6_tooltip_content {
                    font-family: "Open Sans", sans-serif;
                    pointer-events: none;
                    user-select: none;
                    visibility: hidden;
                    opacity: 0;
                    transition: opacity 0.2s ease;
                    width: max-content;
                    max-width: 400px;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items:center;
                    box-shadow: 1px 1px 5px rgba(0,0,0,0.5);
                    text-align: center;
                    background-color: white;
                    white-space: normal;
                    
                    padding: 15px;
                    border-radius: 6px;

                    /* Position the tooltip text - see examples below! */
                    position: absolute;
                    z-index: 1;
                }
                .a6_tooltip .a6_tooltip_content::after {
                  content: " ";
                  position: absolute;
                }

                .top::after{
                  top: 100%; /* At the bottom of the tooltip */
                  left: 50%;
                  margin-left: -5px;
                  border-width: 5px;
                  border-style: solid;
                  border-color: white transparent transparent transparent;
                }
                .left::after{
                  left: 100%; /* At the bottom of the tooltip */
                  top: 50%;
                  margin-top: -5px;
                  border-width: 5px;
                  border-style: solid;
                  border-color: transparent transparent transparent white;
                }
                .right::after{
                  right: 100%; /* At the bottom of the tooltip */
                  top: 50%;
                  margin-top: -5px;
                  border-width: 5px;
                  border-style: solid;
                  border-color: transparent white transparent transparent;
                }

                /* Show the tooltip text when you mouse over the tooltip container */
                .a6_tooltip:hover .a6_tooltip_content {
                    visibility: visible;
                    opacity: 1.0;
                }
            </style>
        `;

    }
};

customElements.define('tool-tip', Tooltip);
