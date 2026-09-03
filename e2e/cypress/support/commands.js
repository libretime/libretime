// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
import { PUBLIC_URL } from "../constants";

Cypress.Commands.add("login", (userType, options = {}) => {
  cy.on("uncaught:exception", (err, runnable) => {
    if (err.message.includes("dt is undefined")) return false;
    return true;
  });

  cy.visit(PUBLIC_URL + "/login");
  cy.get('input[name="username"]').type("admin");
  cy.get('input[name="password"]').type("admin");
  cy.get('select[name="locale"]').select("en_US");
  cy.get('input[name="submit"]').click();
});
