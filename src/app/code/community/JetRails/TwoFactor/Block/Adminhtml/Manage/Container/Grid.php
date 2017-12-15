<?php

	/**
	 * Grid.php - This class defines what collection to use for the grid collection.  It also
	 * defines all the columns for the grid widget.  Additionally it defines all the mass-action
	 * options for the widget grid.
	 * @version         1.0.10
	 * @package         JetRails® TwoFactor
	 * @category        Container
	 * @author          Rafael Grigorian - JetRails®
	 * @copyright       JetRails®, all rights reserved
	 */
	class JetRails_TwoFactor_Block_Adminhtml_Manage_Container_Grid extends Mage_Adminhtml_Block_Widget_Grid {

		/**
		 * The constructor calls the super constructor and then it sets up various settings that
		 * pertain to the grid widget.
		 */
		public function __construct () {
			// Call the super constructor
			parent::__construct ();
			// Set grid properties
			$this->setId ("username");
			$this->setDefaultSort ("username");
			$this->setDefaultDir ("ASC");
			$this->setUseAjax ( true );
			$this->setSaveParametersInSession ( true );
			$this->setFilterVisibility ( false );
		}

		/**
		 * This method loads the admin/user collection.  This collection will be used to extract
		 * admin user information, but it will also be merged with the authentication model.  The
		 * renderer classes aid in doing so.
		 * @return      Mage_Adminhtml_Block_Widget_Grid        Instance of itself
		 */
		protected function _prepareCollection () {
			// This method loads the admin user collection, but also runs the super method
			$collection = Mage::getModel ("admin/user")->getCollection ();
			$this->setCollection ( $collection );
			return parent::_prepareCollection ();
		}

		/**
		 * This method sets up all the columns that will exist in the grid widget. The columns from
		 * the admin/user collection are simply loaded, but then we extract information from the
		 * authentication model based on the admin user item using the renderer classes that are
		 * defined in the column initialization.
		 * @return      Mage_Adminhtml_Block_Widget_Grid        Instance of itself
		 */
		protected function _prepareColumns () {
			// Add all the desired columns to the table
			$this->addColumn ( "username", array (
				"header"            =>  Mage::helper ("twofactor")->__("Username"),
				"index"             =>  "username",
				"type"              =>  "text",
				"filter"            =>  false
			));
			$this->addColumn ( "firstname", array (
				"header"            =>  Mage::helper ("twofactor")->__("Firstname"),
				"index"             =>  "firstname",
				"type"              =>  "text",
				"filter"            =>  false
			));
			$this->addColumn ( "lastname", array (
				"header"            =>  Mage::helper ("twofactor")->__("Lastname"),
				"index"             =>  "lastname",
				"type"              =>  "text",
				"filter"            =>  false
			));
			$this->addColumn ( "email", array (
				"header"            =>  Mage::helper ("twofactor")->__("Email Address"),
				"index"             =>  "email",
				"type"              =>  "text",
				"filter"            =>  false
			));
			$this->addColumn ( "last_timestamp", array (
				"header"            =>  Mage::helper ("twofactor")->__("Last Authenticated On"),
				"index"             =>  "user_id",
				"renderer"          =>  "twofactor/renderer_manage_timestamp",
				"filter"            =>  false
			));
			$this->addColumn ( "last_address", array (
				"header"            =>  Mage::helper ("twofactor")->__("Last Authenticated From"),
				"index"             =>  "user_id",
				"renderer"          =>  "twofactor/renderer_manage_address",
				"filter"            =>  false
			));
			$this->addColumn ( "status", array (
				"header"            =>  Mage::helper ("twofactor")->__("2FA Enabled"),
				"index"             =>  "user_id",
				"width"             =>  "200px",
				"renderer"          =>  "twofactor/renderer_manage_status",
				"sortable"          =>  false,
				"filter"            =>  false
			));
			$this->addColumn ( "state", array (
				"header"            =>  Mage::helper ("twofactor")->__("2FA State"),
				"index"             =>  "user_id",
				"width"             =>  "200px",
				"renderer"          =>  "twofactor/renderer_manage_state",
				"sortable"          =>  false,
				"filter"            =>  false
			));
			// Return whatever the inherited method returns
			return parent::_prepareColumns ();
		}

		/**
		 * This method simply returns the grid action url. This url is used by Magento to update the
		 * grid elements dynamically through AJAX.
		 * @return      string                                  Returns grid action url
		 */
		public function getGridUrl () {
			// Return the grid action route
			$page = Mage::getModel ("twofactor/page");
			return $this->getUrl ( $page::PAGE_MANAGE_GRID, array ( "_current" => true ) );
		}

		/**
		 * This method should return a url to the edit page for a given item. If false is returned
		 * like we do in this method, then when clicking an item in the grid, it will not redirect
		 * us to a dedicated edit page for that item.
		 * @param       object              item                The item in grid to evaluate
		 * @return      boolean                                 Edit page for each entry?
		 */
		public function getRowUrl ( $item ) {
			// Disable clicking on rows (edit)
			return false;
		}

		/**
		 * This method defines all the mass-action items that will appear in the select menu.  It
		 * also defines what actions are taken when said mass-action is requested.
		 * @return      Mage_Adminhtml_Block_Widget_Grid        Instance of itself
		 */
		protected function _prepareMassaction () {
			// Load the page model
			$page = Mage::getModel ("twofactor/page");
			// Define what and how we are sending data to the action controllers
			$this->setMassactionIdField ("user_id");
			$this->getMassactionBlock ()->setFormFieldName ("ids");
			// Append actions to the mass action select menu
			$this->getMassactionBlock ()->addItem ( "enable", array (
				"label"    => Mage::helper ("twofactor")->__("Enable"),
				"url"      => $this->getUrl ( $page::PAGE_MANAGE_ENABLE ),
				"selected" => true,
			));
			$this->getMassactionBlock ()->addItem ( "disable", array (
				"label"    => Mage::helper ("twofactor")->__("Disable"),
				"url"      => $this->getUrl ( $page::PAGE_MANAGE_DISABLE ),
				"selected" => false,
			));
			$this->getMassactionBlock ()->addItem ( "unban", array (
				"label"    => Mage::helper ("twofactor")->__("Remove Temp Ban"),
				"url"      => $this->getUrl ( $page::PAGE_MANAGE_UNBAN ),
				"selected" => false,
			));
			$this->getMassactionBlock ()->addItem ( "reset", array (
				"label"    => Mage::helper ("twofactor")->__("Re-Enroll User"),
				"url"      => $this->getUrl ( $page::PAGE_MANAGE_RESET ),
				"selected" => false,
			));
			// Return an instance of this class
			return $this;
		}

	}