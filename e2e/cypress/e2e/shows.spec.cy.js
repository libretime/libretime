import dayjs from "dayjs";

describe("shows", () => {
  it("add a show", () => {
    const now = dayjs(Date()).add(1, "day");
    cy.clock(now.toDate());

    cy.login();
    cy.contains("Calendar").click();
    cy.url().should("include", "/schedule");

    cy.get("button.add-button").click();
    cy.get("input#add_show_name").clear().type("My show name");
    cy.get("input#add_show_start_now-future").click();
    cy.get("input#add_show_start_date").clear().type(now.format("YYYY-MM-DD"));

    cy.contains("Add this show").click();
    cy.contains("My show name").click();
    cy.contains("Delete").click();
  });
});
