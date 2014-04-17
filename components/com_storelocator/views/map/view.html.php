<?php
/**
 * @version     2.0.0
 * @package     com_storelocator
 * @copyright   Copyright (C) 2013. Sysgen Media LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Sysgen Media <support@sysgenmedia.com> - http://www.sysgenmedia.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Storelocator.
 */
class StorelocatorViewMap extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
    protected $params;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{

		
		/* 
		 $this->state		= $this->get('State');
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->params       = $app->getParams('com_storelocator');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {;
            throw new Exception(implode("\n", $errors));
        }
        
        $this->_prepareDocument();
        parent::display($tpl);
		*/

		
		$app 		= JFactory::getApplication('site');
		$document 	= JFactory::getDocument();
		$jinput 	= JFactory::getApplication()->input;	
		$params 	= $app->getParams('com_storelocator');
		$model		= $this->getModel();
		
		// Get the parameters of the active menu item
		$menuitemid = $jinput->get( 'Itemid' );
		if ($menuitemid)
		{
			
			$menus = $app->getMenu();
			$menu    = $menus->getActive();
			$menuparams = $menus->getParams( $menuitemid );
			$params->merge( $menuparams );
		}
		
		// Optional Key
		$googleKey = $params->get( 'google_maps_v3_api_key', '' );
		$document->addScript( 'https://maps.googleapis.com/maps/api/js?v=3.9&amp;sensor=false&amp;key='.$googleKey );  // Load v3 API with Key
	
		
		// Implement Cluster Scripts
		$cluster_method = $params->get( 'map_cluster_method', 0 );
		
		if ( $cluster_method == 'MarkerCluster')
			$document->addScript( 'components/com_storelocator/assets/markercluster.min.js'); // Load Google MarkerClusterPlus
			
		if ( $cluster_method == 'Spiderfier')
			$document->addScript( 'components/com_storelocator/assets/ows.min.js'); // Spider Script
		
		// Category Filter
		$catReq = array();
		$filterCats = array();
		$catResult = $model->getCategories();
		$mod_cat = $jinput->post->get('catid', '-1','INT');
		
		if (!$params->get('show_all', 1))
		{
			if ($catid = $params->get( 'categories', 0 ))
				if (is_array($catid))	
					$catReq = $catid;
				else
					$catReq[] = $catid;
			$categories = implode('-', $catReq);
			
			// Filter out non existant cats only if requesting cats.. Bug Fix for no menu items
			foreach($catResult as $obj)
				if(array_search($obj->value,$catReq) !== false)
					$filterCats[] = $obj;

		} else {
			$filterCats = $catResult;
		}

		$catmode = $params->get('cat_mode', 1);
		if ($catmode == 1)
		{
			array_unshift($filterCats, (object)array('value'=>-1,'text'=>JText::_('ALL_CATEGORIES')));
			$catsearchHTML = JHTML::_('select.genericlist',   $filterCats, 'catid', 'class="inputbox" size="1" onchange="searchLocations()"', 'value', 'text', $mod_cat );
		}
		else
		{
			$catsearchHTML = '';
			foreach($filterCats as $obj)
				$catsearchHTML .= ' <label class="checkbox inline">
										<input type="checkbox" name="catid" value="'.$obj->value.'" '.(($mod_cat == -1 || $mod_cat == $obj->value)?"checked=\"checked\"":"").
										' id="catid_'.$obj->value.'" onchange="searchLocations();"> '.$obj->text.'
									</label>';
		}
		
		
		// Tag Filter
		$tagReq = array();
		$filterTags = array();
		$tagResult = $model->getTags();		
		
		
		if (!$params->get('show_all_tags', 1))
		{
			if ($tagid = $params->get( 'tags', 0 ))
			{
				if (is_array($tagid))	
					$tagReq = $tagid;
				else
					$tagReq[] = $tagid;
			}
					
			$tags = implode('-', $tagReq);
			
			// Filter out non existant tags only if requesting tags.. Bug Fix for no menu items

			foreach($tagResult as $obj)
				if(array_search($obj->value,$tagReq) !== false)
					$filterTags[] = $obj;
					
		} else {
			$filterTags = $tagResult;
		}
		
		
		// Decide on Display Options
		$tagsearchHTML = '';
		$tagmode = intval($params->get('tag_mode', 2));
		
		if ($tagmode == 1)
		{
			array_unshift($filterTags, (object)array('value'=>-1,'text'=>JText::_('ALL_TAGS')));
			$tagsearchHTML = JHTML::_('select.genericlist',   $filterTags, 'tagid', 'class="inputbox" size="1" onchange="searchLocations()" ', 'value', 'text');
		}
		else if ($tagmode == 2)
		{
			foreach($filterTags as $obj)
				$tagsearchHTML .= ' <label class="checkbox inline">
										<input type="checkbox" name="tagid" value="'.$obj->value.'" id="tagid_'.$obj->value.'" onchange="searchLocations();"> '.$obj->text.'
									</label>';
		}
		
		

		// Featured Filter		
		$featstate = array();
		$featstate[] = (object)array('value'=>0,'text'=>JText::_('ALL_LOCATIONS'));
		$featstate[] = (object)array('value'=>1,'text'=>JText::_('ONLY_FEATURED'));


        $featstateHTML = JHTML::_('input.checkbox',   $featstate, 'featstate', 'class="span2" size="1" onchange="searchLocations()" ', 'value', 'text', (bool)$jinput->post->get('featstate', 0, 'BOOLEAN'));

        //$featstateHTML = JHTML::_('input.checkbox',   $featstate, 'featstate', 'class="span2" size="1" onchange="searchLocations()" ', 'value', 'text', (bool)$jinput->post->get('featstate', 0, 'BOOLEAN'));

				
		// Module Support
		$this->addressInput = $jinput->post->get('mod_addressInput', '');
		$this->name_search = $jinput->post->get('mod_name_search', '');
		$this->radiusSelect = $jinput->post->get('radiusselect', $params->get( 'default_radius', '25'),'INT');
		$this->isModSearch = (bool)$jinput->post->get('mod_storelocator_search', 0,'BOOLEAN');  
		
		
		// Gather Needed Parameters
		$this->include_mootools		= $params->get( 'include_mootools', 1 );
		$this->include_css			= $params->get( 'include_css', 1 );
		$this->fix_jquery 			= $params->get( 'fix_jquery', 0 );		
		$this->map_width 			= $params->get( 'map_width', 450 );
		$this->map_height			= $params->get( 'map_height', 400 );
		$this->search_enabled		= $params->get( 'search_enabled', 1 );
		$this->list_enabled			= $params->get( 'list_enabled', 1 );
		$this->hide_list_onload		= $params->get( 'hide_list_onload', 0 );
		$this->catsearch_enabled	= $catmode;
		$this->catsearch			= $catsearchHTML;
		$this->tagsearch_enabled	= $tagmode;
		$this->tagsearch			= $tagsearchHTML;
		$this->featsearch_enabled	= $params->get( 'featsearch_enabled', 1 );
		$this->featsearch			= $featstateHTML;
		$this->map_units	 		= $params->get( 'map_units', 1 );
		$this->radius_list			= explode(',',$params->get( 'radius_list', "25,50,100"));
		$this->menuitemid			= $menuitemid;
		$this->params	 			= $params;
		
		parent::display($tpl);
		
	}


	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('com_storelocator_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}    
    	
}
