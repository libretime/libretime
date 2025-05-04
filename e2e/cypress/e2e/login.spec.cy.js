import { PUBLIC_URL } from "../constants";

describe("login", () => {
  it("works", () => {
    cy.login();

    cy.url().should("include", "/showbuilder#tracks");

    cy.get('a[href="/login/logout"]').click();
    cy.url().should("include", "/");
  });

  it("fails with bad credentials", () => {
    cy.visit(PUBLIC_URL + "/login");
    cy.get('input[name="username"]').type("admin");
    cy.get('input[name="password"]').type("bad password");
    cy.get('input[name="submit"]').click();

    cy.contains("Wrong username or password provided. Please try again.");

    cy.url().should("include", "/login");
  });
});
