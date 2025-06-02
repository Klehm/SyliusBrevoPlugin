@brevo_mailer
Feature: Receiving template email from Brevo
  In order to verify that emails are sent through Brevo
  As a Developer
  I want to receive my custom verification email created in Brevo

  Background:
    Given I have set up the Brevo API Key
    And the store operates on a single channel in "en_US" locale
    And I haven't setup the "account_verification" template in Brevo

  Scenario: Receiving a template email from Brevo after customer registration
    Given on this channel account verification is required
    When I register with email "goodman@example.com" and password "heisenberg"
    Then there should be this a raw email sent to "goodman@example.com" in Brevo logs
