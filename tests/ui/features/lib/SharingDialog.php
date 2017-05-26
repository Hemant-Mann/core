<?php

/**
* ownCloud
*
* @author Artur Neumann
* @copyright 2017 Artur Neumann artur@jankaritech.com
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

namespace Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;

class SharingDialog extends OwnCloudPage
{
	/**
	 *
	 * @var string $path
	 */
	protected $path = '/index.php/apps/files/';

	protected $shareWithFieldXpath = ".//*[contains(@class,'shareWithField')]";
	protected $shareWithTooltipXpath = "/..//*[@class='tooltip-inner']";
	protected $shareWithLoadingIndicatorXpath = ".//*[contains(@class,'shareWithLoading')]";
	protected $shareWithAutocompleteListXpath = ".//ul[contains(@class,'ui-autocomplete')]";
	protected $autocompleItemsTextXpath = "//*[@class='autocomplete-item-text']";
	protected $suffixToIdentifyGroups = " (group)";

	/**
	 * 
	 * @throws \SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException
	 * @return \Behat\Mink\Element\NodeElement|NULL
	 */
	private function _findShareWithField ()
	{
		$shareWithField = $this->find("xpath", $this->shareWithFieldXpath);
		if ($shareWithField === null) {
			throw new ElementNotFoundException("could not find share-with-field");
		}
		return $shareWithField;
	}
	 /**
	 * fills the "share-with" input field
	 * @param string $input
	 * @param number $timeout how long to wait till the autocomplete comes back
	 * @return \Behat\Mink\Element\NodeElement AutocompleteElement
	 * @throws \SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException
	 */
	public function fillShareWithField ($input, $timeout = 10)
	{
		$shareWithField = $this->_findShareWithField();
		$shareWithField->setValue($input);
		$this->_waitForShareWithLoadingIndicator($timeout);
		return $this->getAutocompleteNodeElement();
	}

	/**
	 * gets the NodeElement of the autocomplete list
	 * @return \Behat\Mink\Element\NodeElement
	 * @throws \SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException
	 */
	public function getAutocompleteNodeElement()
	{
		$autocompleteNodeElement = $this->find("xpath", $this->shareWithAutocompleteListXpath);
		if ($autocompleteNodeElement === null) {
			throw new ElementNotFoundException("could not find autocompleteNodeElement");
		}
		return $autocompleteNodeElement;
	}

	/**
	 * gets the users listed in the autocomplete list as array
	 * @return array
	 * @throws \SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException
	 */
	public function getAutocompleteUsersList()
	{
		$usersArray = array();
		$userElements = $this->getAutocompleteNodeElement()->findAll(
			"xpath", 
			$this->autocompleItemsTextXpath
		);
		foreach ( $userElements as $user ) {
			array_push($usersArray,$user->getText());
		}
		return $usersArray;
	}

	/**
	 * 
	 * @param string $name
	 * @param bool $canShare not implemented yet
	 * @param bool $canEdit not implemented yet
	 * @param bool $createPermission not implemented yet
	 * @param bool $changePermission not implemented yet
	 * @param bool $deletePermission not implemented yet
	 * @throws \SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException
	 */
	public function shareWithUser($name, $canShare = true, $canEdit = true, 
		$createPermission = true, $changePermission = true,
		$deletePermission = true)
	{
		if ($canShare !== true || $canEdit !== true ||
			$createPermission !== true || $changePermission !== true ||
			$deletePermission !== true) {
				throw new \Exception("this function is not implemented");
			}
		$autocompleteNodeElement = $this->fillShareWithField($name);
		$userElements = $autocompleteNodeElement->findAll(
			"xpath", $this->autocompleItemsTextXpath
		);
		
		$userFound = false;
		foreach ( $userElements as $user ) {
			if ($user->getText() === $name) {
				$user->click();
				$this->_waitForShareWithLoadingIndicator();
				$userFound = true;
			}
		}
		
		if ($userFound !== true) {
			throw new ElementNotFoundException("could not share with '$name'");
		}
	}

	/**
	 *
	 * @param string $name
	 * @param bool $canShare not implemented yet
	 * @param bool $canEdit not implemented yet
	 * @param bool $createPermission not implemented yet
	 * @param bool $changePermission not implemented yet
	 * @param bool $deletePermission not implemented yet
	 * @throws \SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException
	 */
	public function shareWithGroup($name)
	{
		return $this->shareWithUser($name . $this->suffixToIdentifyGroups);
	}

	/**
	 * gets the text of the tooltip associated with the "share-with" input
	 * @throws \SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException
	 * @return string
	 */
	public function getShareWithTooltip()
	{
		$shareWithField = $this->_findShareWithField();
		$shareWithTooltip = $shareWithField->find("xpath", $this->shareWithTooltipXpath);
		if ($shareWithTooltip === null) {
			throw new ElementNotFoundException("could not find share-with-tooltip");
		}
		return $shareWithTooltip->getText();
	}
	
	private function _waitForShareWithLoadingIndicator ($timeout = 10)
	{
		$counter = 0;
		do {
			sleep(1);
			$counter++;
			$loadingIndicatorClass = $this->find(
				"xpath",
				$this->shareWithLoadingIndicatorXpath)->getAttribute("class");
		} while (strpos($loadingIndicatorClass, "hidden") === false && $counter <= $timeout);
	}
}