describe('Génération de PDF', () => {
    beforeEach(() => {
        // Connexion avant chaque test
        cy.visit('/login');
        cy.get('#username').type('axeltribondeau@gmail.com');
        cy.get('#password').type('test123');
        cy.get('button[type="submit"]').click();

        // Redirection vers la page de génération
        cy.visit('/generate-pdf');
    });

    it('test 1 - Générer un PDF via URL', () => {
        cy.visit('/generate-pdf');

        cy.get('label[for="toggle-url"]').click();
        cy.get('input[name="url_form[url]"]').type('https://docs.mmi-troyes.fr/login', { force: true });
        cy.get('form[name="url_form"] button[type="submit"]').click({ force: true });

        // Attendre le rechargement et vérifier que le contenu est un PDF
        cy.document().its('contentType').should('eq', 'application/pdf');
        cy.screenshot('pdf-generated-from-url');
    });
});
