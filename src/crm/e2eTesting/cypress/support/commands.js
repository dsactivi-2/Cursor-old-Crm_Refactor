Cypress.Commands.add("loginIntoCRM", (user) => {
  cy.session(
    user,
    () => {
      cy.visit("/login.php");
      cy.get('input[name="login_email"]').type(user.email);
      cy.get('input[name="login_password"]').type(user.password);
      cy.get("button").click();
    },
    {
      validate: () => {
        cy.getCookie("idk_session").should("exist");
      },
    }
  );
});

Cypress.Commands.add("loginIntoJobsoft", (user) => {
  cy.session(
    user,
    () => {
      cy.visit("/jobstep_pp/login.php");
      cy.get('input[name="input_email"]').type(user.email);
      cy.get('input[name="input_password"]').type(user.password);
      cy.get("#btn_log_in").click();
      // Pathname should be /jobstep_pp/dashboard.php
      cy.location("pathname").should("eq", "/jobstep_pp/dashboard.php");
    },
    {
      validate: () => {
        //cy.location('pathname').should('eq', '/jobstep_pp/dashboard.php')
        cy.getCookie("ppJobStepSession").should("exist");
      },
    }
  );
});
