beforeEach(() => {
  cy.viewport(1920, 1080);
  cy.loginIntoCRM({
    email: "h.kucuk@job-step.net",
    password: "efMhZE2fvpyFXgq",
  });
});

describe("JobstepCRM main dashboard", () => {
  beforeEach(() => {
    cy.visit("/");
  });

  it("shows the sidebar menu", () => {
    cy.get("#sidebar").should("be.visible");
  });

  specify("sidebar has ul child", () => {
    cy.get("#sidebar").then(($sidebar) => {
      expect($sidebar.children("ul")).to.exist;
    });
  });

  it("goes to dashboard naloga", () => {
    cy.get("#content > div > div > a").click();
    cy.location("pathname").should("eq", "/dashboardNaloga/companies.php");
  });
});


