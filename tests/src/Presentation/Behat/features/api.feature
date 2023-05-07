Feature: Api Context

  In order to test our API context
  I want to be able to test all possible scenarios that exist in the API context
  So, I can be sure that the API context is working as expected
  For this, I will use the routes of PageApiController as an example


  Scenario: Page Listing
    When I send a "GET" request to "/api/pages"
    And I set "content-type" header equal to "application/json"
    Then the response status code should be 200
    And the response should be a valid JSON
    And the response header "Content-Type" should be equal to "application/json"
    And the response JSON expression match "response.[*].id" contains "1" as "int"
    And the number of results in the JSON response should be equal to 10


  Scenario: Page Creation
    When I send a "POST" request to "/api/pages" with body:
    """
    {
        "title": "Page Title",
        "description": "Page description"
    }
    """
    Then the response status code should be 201
    And the JSON response key "id" should exist
    And the response JSON expression match "response.title" contains "Page Title"
    And the response JSON expression match "response.description" contains "Page description"

  Scenario: Page creation with file
    Given I set "content-type" header equal to "multipart/form-data; boundary=--abcd64"
    And I attach the file "sample.png" to request with key "file"
    When I send a "POST" request to "/api/pages/upload"
    Then the response status code should be 201
    And the response JSON expression match "name" == "sample.png"
    And the JSON response key "extension" should be equal "png"
    And the JSON response content should be:
    """
     {
        "name": "sample.png",
        "size": 503111,
        "extension": "png"
      }
    """

  Scenario: Page Update
    When I send a "PUT" request to "/api/pages/5" with body:
    """
    {
        "title": "Page Title Updated",
        "description": "Page description updated"
    }
    """
    Then the response status code should be 200
    And the JSON response key "id" should exist
    And the JSON response content should be:
    """
    {
        "id": 5,
        "title": "Page Title Updated",
        "description": "Page description updated"
    }
    """

  Scenario: Page show
    When I send a "GET" request to "/api/pages/5"
    Then the response status code should be 200
    And the JSON response key "id" should exist
    And the JSON response content should be:
    """
    {
        "id": 5,
        "title": "Title",
        "description": "Description"
    }
    """

  Scenario: Page deletion
    When I send a "DELETE" request to "/api/pages/5"
    Then the response status code should be 200
    And the JSON response key "message" should exist
    And the response JSON expression match "response.message" contains "Page deleted"
