describe("template spec", () => {
    it("passes", () => {
        cy.visit("https://example.cypress.io")

        cy.contains("type").click()

        cy.url().should("include", "/commands/actions")
    })
})
