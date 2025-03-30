describe('Formulaire de Connexion', () => {
    it('test 1 - connexion OK', () => {
        cy.visit('/login');

        cy.get('#username').type('axeltribondeau@gmail.com');
        cy.get('#password').type('test123');
        cy.get('button[type="submit"]').click();

        // Vérifie que l’on arrive bien sur la page d’accueil
        ;cy.contains('Bonjour !').should('exist')
    });

    it('test 2 - connexion KO', () => {
        cy.visit('/login');
        cy.get('#username').type('axeltribondeau@gmail.com');
        cy.get('#password').type('wrongpassword');
        cy.get('button[type="submit"]').click();
        cy.contains('Invalid credentials.').should('exist');
    });
});
