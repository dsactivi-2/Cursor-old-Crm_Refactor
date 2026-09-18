beforeEach(() => {
  cy.viewport(1920, 1080);
  cy.loginIntoJobsoft({
    email: "e.korjenic@job-step.net",
    password: "v0bd3gvx",
  });
});

describe("JobstepPP main dashboard", () => {
  beforeEach(() => {
    cy.visit("/jobstep_pp/dashboard.php");
  });

  it("renders the header correctly", () => {
    // Header is visible and has children
    cy.get("body > div.container > header > div").should("be.visible");
    cy.get("body > div.container > header > div").children().should("exist");

    // Forecast is visible and has text
    cy.get("#forecast_view").should("be.visible");
    cy.get("#forecast_view").should("have.text", "Forecast");

    // Dashboard Kandidaten is visible and has text
    cy.get("#casting_view").should("be.visible");
    cy.get("#casting_view").should("have.text", "Dashboard Kandidaten");
    cy.get("#casting_view").should("have.class", "view_selected");

    // Dashboard Personal is visible and has text
    cy.get("#departure_view").should("be.visible");
    cy.get("#departure_view").should("have.text", "Dashboard Personal");
  });
});
