describe("webstreams", () => {
  it("add a webstream", () => {
    cy.login();
    cy.contains("Webstreams").click();
    cy.get('button[id="sb-new"]').click();

    cy.get('input[class="playlist_name_display"]').clear().type("Test Name");
    cy.get('dd[id="description-element"] > textarea').type("Test Description");

    cy.get('dd[id="streamurl-element"] > input')
      .clear()
      .type("http://localhost:8000/main");

    cy.get('button[id="webstream_save"]').click();
  });
});
