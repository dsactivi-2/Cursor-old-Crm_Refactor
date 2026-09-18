const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
    baseUrl: "http://localhost",
    videosFolder: "cypress/videos",
    screenshotsFolder: "cypress/screenshots"
  },
});

