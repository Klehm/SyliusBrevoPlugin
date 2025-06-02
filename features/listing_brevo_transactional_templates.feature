@brevo_mailer
Feature: Listing Brevo transactional templates
  In order to list transactional templates in Brevo
  As a Developer
  I want to be able to list all transactional templates created in Brevo

  Background:
    Given I have set up the Brevo API Key

  @cli
  Scenario: Listing all transactional templates in Brevo
    When I run the Brevo templates list command
    Then the response should be successful
