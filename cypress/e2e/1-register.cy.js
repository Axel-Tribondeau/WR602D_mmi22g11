describe('Inscriptions', () => {
  it('test 1 - inscription OK', () => {
    cy.visit('/register');

    cy.get('input[name="registration_form[email]"]').type('pitie@gmail.com');
    cy.get('input[name="registration_form[plainPassword]"]').type('test123');
    cy.get('input[name="registration_form[firstname]"]').type('test');
    cy.get('input[name="registration_form[lastname]"]').type('test');

    cy.get('button[type="submit"]').click();
    cy.url().should('include', '/');
  });

  it('test 4 - inscription KO (password vide)', () => {
    cy.visit('/register');

    cy.get('input[name="registration_form[email]"]').type('test@test.com');
    cy.get('input[name="registration_form[firstname]"]').type('test');
    cy.get('input[name="registration_form[lastname]"]').type('test');

    cy.get('button[type="submit"]').click();

    cy.get('input[name="registration_form[plainPassword]"]')
        .then(($input) => {
          expect($input[0].checkValidity()).to.be.false;
        });
  });

  it('test 5 - inscription KO (firstname vide)', () => {
    cy.visit('/register');

    cy.get('input[name="registration_form[email]"]').type('test@test.com');
    cy.get('input[name="registration_form[plainPassword]"]').type('test123');
    cy.get('input[name="registration_form[lastname]"]').type('test');

    cy.get('button[type="submit"]').click();

    cy.get('input[name="registration_form[firstname]"]')
        .then(($input) => {
          expect($input[0].checkValidity()).to.be.false;
        });
  });

  it('test 6 - inscription KO (lastname vide)', () => {
    cy.visit('/register');

    cy.get('input[name="registration_form[email]"]').type('test@test.com');
    cy.get('input[name="registration_form[plainPassword]"]').type('test');
    cy.get('input[name="registration_form[firstname]"]').type('test');

    cy.get('button[type="submit"]').click();

    cy.get('input[name="registration_form[lastname]"]')
        .then(($input) => {
          expect($input[0].checkValidity()).to.be.false;
        });
  });

  it('test 4 - inscription KO (email vide)', () => {
    cy.visit('/register');

    cy.get('input[name="registration_form[plainPassword]"]').type('test1234');
    cy.get('input[name="registration_form[firstname]"]').type('test');
    cy.get('input[name="registration_form[lastname]"]').type('test');

    cy.get('button[type="submit"]').click();

    cy.get('input[name="registration_form[email]"]')
        .then(($input) => {
          expect($input[0].checkValidity()).to.be.false;
        });
  });
});
