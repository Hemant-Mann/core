Feature: Sharee - autocompletion

	Background:
		Given regular users exist
		And a regular user exists
		And I am logged in as a regular user
		And I am on the files page
		
	Scenario: autocompletion of regular existing users
		And the share dialog for the folder "simple-folder" is open
		And I type "user" in the share-with-field
		Then all users that contain the string "user" in their username should be listed in the autocomplete list
		And my own name should not be listed in the autocomplete list
		
	Scenario: autocompletion for a pattern that does not match any user or group
		And the share dialog for the folder "simple-folder" is open
		And I type "doesnotexist" in the share-with-field
		Then a tooltip with the text "No users or groups found for doesnotexist" should be shown near the share-with-field
		And the autocomplete list should not be displayed
		
	Scenario: autocompletion of a pattern that matches regular existing users but also a user whith whom the item is already shared (folder)
		And the folder "simple-folder" is shared with the user "user1"
		And the share dialog for the folder "simple-folder" is open
		And I type "user" in the share-with-field
		Then all users that contain the string "user" in their username should be listed in the autocomplete list except "user1"
		And my own name should not be listed in the autocomplete list

	Scenario: autocompletion of a pattern that matches regular existing users but also a user whith whom the item is already shared (file)
		And the file "data.zip" is shared with the user "user1"
		And the share dialog for the file "data.zip" is open
		And I type "user" in the share-with-field
		Then all users that contain the string "user" in their username should be listed in the autocomplete list except "user1"
		And my own name should not be listed in the autocomplete list