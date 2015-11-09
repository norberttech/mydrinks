Feature: Create new drink recipe 
  As a bartender 
  In order to add new drinks into My Drinks Encyclopedia
  I need to be able to create drink recipe step by step
       
  Scenario: Create Screwdriver drink recipe
    When I decide to create new recipe called "Screwdriver"
    And as first step I add: prepare Highball glass with 240 ml capacity
    And I add step: add 4 "Ice" cubes into glass
    And I add step: pour 60 ml of "Vodka" into glass
    And I add step: pour 160 ml of "Orange juice" into glass
    And I add step: garnish glass with "Orange slice"
    Then "Screwdriver" recipe should be made from 5 following steps
      | Type           | Name         | Capacity | Amount |
      | Prepare glass  | Highball     | 240      | -      |
      | Add ingredient | Ice          | -        | 4      |
      | Pour           | Vodka        | 60       | -      |
      | Pour           | Orange juice | 160      | -      |
      | Garnish        | Orange slice | -        | -      |
    And the recipe should not be published 
    
  Scenario: Attempt to create Screwdriver drink recipe using too many liquids
    When I decide to create new recipe called "Screwdriver"
    And as first step I add: prepare Highball glass with 240 ml capacity
    And I add step: add 4 "Ice" cubes into glass
    And I add step: pour 60 ml of "Vodka" into glass
    And I add step: pour 200 ml of "Orange juice" into glass
    Then I should be noticed that glass from recipe can't took more than 240 ml of liquids

  Scenario: Attempt to create Screwdriver drink without glass
    When I decide to create new recipe called "Screwdriver"
    And I add step: add 4 "Ice" cubes into glass
    Then I should be noticed that first step should be glass preparation

  Scenario: Attempt to add step about pouring to shaker without shaker
    When I decide to create new recipe called "Screwdriver"
    And as first step I add: prepare Highball glass with 240 ml capacity
    And I add step: pour 50 ml of "Ice" into shaker
    Then I should be noticed that first step should be shaker preparation
    
  Scenario: Attempt to remove significant step from the recipe
    When I decide to create new recipe called "Screwdriver"
    And as first step I add: prepare Highball glass with 240 ml capacity
    And I add step: add 4 "Ice" cubes into glass
    And I remove first step from the recipe
    Then I should be noticed that first step should be glass preparation
    
  