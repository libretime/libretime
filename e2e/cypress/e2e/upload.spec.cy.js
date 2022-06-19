describe("upload", () => {
  it("upload a file", () => {
    cy.login();
    cy.contains("Upload").click();
    cy.url().should("include", "/plupload");
    cy.contains("Drop files here or click to browse your computer.").selectFile(
      "cypress/fixtures/sample.ogg",
      { action: "drag-drop" }
    );

    cy.contains("Recent Uploads");
    cy.contains("Pending import");
    cy.contains("Successfully imported");

    cy.contains("Tracks").click();
    cy.contains("Test Title").click();
  });
});
