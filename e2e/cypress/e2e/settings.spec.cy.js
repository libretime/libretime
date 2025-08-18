describe("settings", () => {
  it("list users", () => {
    cy.login();
    cy.contains("Settings").click();
    cy.get("#sub-menu").children().contains("Users").click();

    cy.contains("Manage Users");
    cy.contains("admin");
  });

  it("working streaming status", () => {
    cy.login();
    cy.contains("Settings").click();
    cy.get("#sub-menu").children().contains("Streams").click();

    cy.contains("Connected to the streaming server");
  });
});
