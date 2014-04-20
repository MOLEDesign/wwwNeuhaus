<?php
// Store Locator template override by MOLEDesign (on behalf of Jamaza)
?>

<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php 
 	if($this->include_mootools) JHtml::_('behavior.framework');
 	
	$document = JFactory::getDocument();
	$document->addScript( 'components/com_storelocator/assets/map.js' );
	if($this->include_css) $document->addStyleSheet('components/com_storelocator/assets/styles.css');
	
	if ($this->params->get( 'menu-meta_description', '' )) 	$document->setMetaData('description', 	$this->params->get( 'menu-meta_description', '' ));
	if ($this->params->get( 'menu-meta_keywords', '' )) 	$document->setMetaData('keywords', 		$this->params->get( 'menu-meta_keywords', '' ));
	if ($this->params->get( 'robots', '' )) 					$document->setMetaData('robots', 		$this->params->get( 'robots', '' ));
?>
<?php if($this->fix_jquery): ?>
<script type="text/javascript">if ('undefined' != typeof jQuery) jQuery.noConflict();</script>
<?php endif; ?>
<script type="text/javascript">

	// Define Language Strings
    var NO_RESULTS			= "<?php echo JText::_( 'NO_RESULTS' ); ?>";
    var SEARCH_RESULTS_FOR 	= "<?php echo JText::_( 'SEARCH_RESULTS_FOR' ); ?>";
    var MI 					= "<?php echo JText::_( 'MI' ); ?>";
    var KM 					= "<?php echo JText::_( 'KM' ); ?>";
    var PHONE 				= "<?php echo JText::_( 'PHONE' ); ?>";
    var WEBSITE 			= "<?php echo JText::_( 'WEBSITE' ); ?>";
    var GET_DIRECTIONS 		= "<?php echo JText::_( 'GET_DIRECTIONS' ); ?>";
    var FROM_ADDRESS 		= "<?php echo JText::_( 'FROM_ADDRESS' ); ?>";
    var GO 					= "<?php echo JText::_( 'GO' ); ?>";
    var DIR_EXAMPLE 		= "<?php echo JText::_( 'DIR_EXAMPLE' ); ?>";
    var VISIT_SITE 			= "<?php echo JText::_( 'VISIT_SITE' ); ?>";
    var CATEGORY 			= "<?php echo JText::_( 'CATEGORY' ); ?>";
	var TAGS 				= "<?php echo JText::_( 'TAGS' ); ?>";
    var DID_YOU_MEAN 		= "<?php echo JText::_( 'DID_YOU_MEAN' ); ?>";
    var LIMITED_RESULTS 	= "<?php echo str_replace( '%s', substr($this->params->get('search_limit_period', '-1 Day'), 1), JText::_( 'LIMITED_RESULTS' )); ?>";
    var JOOMLA_ROOT			= "<?php echo JURI::root( true ); ?>";
    
    //Define Parameters
    var show_all_onload 		= <?php echo $this->params->get( 'show_all_onload', '1' )?>;
    var map_auto_zoom 			= <?php echo $this->params->get( 'map_auto_zoom', '1' )?'true':'false'?>;  
    var map_directions  		= <?php echo $this->params->get( 'map_directions', '1' )?'true':'false'?>;
    var google_suggest 			= <?php echo $this->params->get( 'google_suggest', '1' )?'true':'false'?>; 
    var map_center_lat 			= <?php echo $this->params->get( 'map_center_lat', 40  )?>;
    var map_center_lon 			= <?php echo $this->params->get( 'map_center_lon', -100 )?>;
    var map_default_zoom_level 	= <?php echo $this->params->get( 'map_default_zoom_level', 3 )?>;
    var catsearch_enabled  		= <?php echo (int)$this->params->get( 'cat_mode', 1 )>-1?'true':'false'?>;
    var featsearch_enabled 		= <?php echo $this->params->get( 'featsearch_enabled', 1 )?'true':'false'?>;
    var tagsearch_enabled  		= <?php echo (int)$this->params->get( 'tag_mode', 2 )>0?'true':'false'?>;
    var map_units 				= <?php echo $this->params->get( 'map_units', 1 )?>;
    var isModSearch 			= <?php echo $this->isModSearch?'true':'false'?>;
    var base_country 			= "<?php echo $this->params->get( 'base_country', '' )?>";
    var fix_jquery 				=  (('undefined' != typeof jQuery) && <?php echo $this->params->get( 'fix_jquery', 0 )?'true':'false'?>);
    var hide_list_onload  		= <?php echo $this->params->get( 'hide_list_onload', 0 )?'true':'false'?>;
    var list_enabled  			= <?php echo $this->params->get( 'list_enabled', 1 )?'true':'false'?>;
    var map_width  				= <?php echo $this->params->get( 'map_width', 450 )?>;
    var map_type_id 			= google.maps.MapTypeId.<?php echo $this->params->get( 'map_view_state', 'ROADMAP' )?>;
    var scroll_wheel			= <?php echo $this->params->get( 'map_zoom_wheel', '1' )?'true':'false'?>;
    var cat_mode				= <?php echo $this->params->get( 'cat_mode', '1' ); ?>;
    var tag_mode 				= <?php echo $this->params->get( 'tag_mode', '2' ); ?>;
    var menuitemid 				= "<?php echo $this->menuitemid;?>";
    var map_include_terrain		= <?php echo $this->params->get( 'map_include_terrain', '0' )?'true':'false'?>;
    var sl_settings 			= <?php print_r( json_encode($this->params->toArray() )); ?>;
</script>
<?php if ($this->params->get('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<?php if ( $this->params->get( 'articleid_head', 0 ) ) : ?>
	<?php $articleid_head = StorelocatorHelper::getArticle($this->params->get( 'articleid_head', 0 )); ?>
	<div class="sl_article_top"><?php echo $articleid_head->introtext . $articleid_head->fulltext; ?></div>
<?php endif; ?>

<div id="sl_search_container" <?php if(!$this->search_enabled) echo 'style="display:none;"'; ?>>
    <form action="#" onsubmit="searchLocations(); return false;" id="locate_form" class="form-inline">
    
        <div class="row-fluid">
        	<p><?php echo JText::_( 'INSTRUCTIONS' ); ?></p>
            <span class="help-inline"><?php echo JText::_( 'ADDRESS' ); ?></span>
            <input type="text" id="addressInput" value="<?php echo $this->addressInput?>" class="span2" placeholder="<?php echo JText::_( 'ADDRESS_PLACEHOLDER' ); ?>" onkeydown="if (event.keyCode == 13) { searchLocations(); return false; }" />


            <!-- New Radius search poisiton -->
            <?php if ( $radius_search = $this->params->get( 'radiussearch_enabled', 1 ) ) : ?>
                <span class="help-inline"><?php echo JText::_( 'RADIUS' ); ?></span>
                <select id="radiusSelect" class="span2 rightmargin10">
                    <?php
                    foreach( $this->radius_list as $radius )
                        printf("<option value=\"%d\" %s>%d %s</option>", $radius, ($this->radiusSelect==$radius)?'selected="selected"':'', $radius, ($this->map_units?JText::_( 'MILES' ):JText::_( 'KILOMETERS' )));
                    ?>
                </select>
            <?php endif; ?>
            <!-- End radius search -->

            <!-- New name search position -->
            <span class="help-inline"><?php echo JText::_( 'NAME' ); ?></span>
            <input type="text" id="name_search" value="<?php echo $this->name_search?>" class="span2" placeholder="<?php echo JText::_( 'COM_STORELOCATOR_NAME_SEARCH_LABEL' ); ?>" onkeydown="if (event.keyCode == 13) { searchLocations(); return false; }" />

            <!-- End name search -->

            <!-- New featured search position -->
            <?php if($featsearch_enabled = $this->params->get( 'featsearch_enabled', 1 ) ) : ?>
                <span class="help-inline"><?php echo JText::_( 'FEATURED' ); ?></span>
                <?php echo $this->featsearch; ?>
            <?php endif;?>
            <!-- End featured search -->


            <input type="button" class="btn btn-primary" onclick="searchLocations()" value="<?php echo JText::_( 'COM_STORELOCATOR_SEARCH_BTN_LABEL' ); ?>"/>
            <img src="components/com_storelocator/assets/spinner.gif" alt="Loading" style="display:none; padding-left:3px; vertical-align:middle;" id="sl_map_spinner" />
        </div>

        
        <?php if($this->catsearch_enabled > -1): ?>
        <div class="row-fluid">
            <h5><?php echo JText::_( 'CATEGORY' ); ?> <small><?php echo JText::_( 'COM_STORELOCATOR_OPTIONAL_LABEL' ); ?></small></h5>
            <?php echo $this->catsearch; ?>
        </div>
        <?php endif;?>
        
        <?php if($this->tagsearch_enabled > 0): ?>
        <div class="row-fluid">
            <h5><?php echo JText::_( 'TAGS' ); ?> <small><?php echo JText::_( 'COM_STORELOCATOR_OPTIONAL_LABEL' ); ?></small></h5>
            <?php echo $this->tagsearch; ?>
        </div>
        <?php endif;?>
    
       <?php if ( !$radius_search ) : ?>
       <input type="hidden" id="radiusSelect" name="radiusSelect" value="<?php echo $this->radiusSelect; ?>"  />
       <?php endif; ?>
   </form>
</div>
<br/>
  
<div id="sl_results_container">

    <div class="row-fluid">
        <div id="sl_sidebar" style="height: <?php echo $this->map_height?>px;<?php if(!$this->list_enabled || $this->hide_list_onload) echo 'display:none;'; ?>" class="span3"><?php echo JText::_( 'NO_RESULTS' ); ?></div>
        <div id="map" style="height: <?php echo intval($this->map_height)?>px" class="<?php echo (!$this->list_enabled || $this->hide_list_onload)?'span12':'span9'; ?>"></div>

    </div>


    <div class="row-fluid">
      <div class="span12" id="sl_locate_results" <?php if(!$this->search_enabled) echo 'style="display:none;"'; ?>><?php echo JText::_( 'PRESEARCH_TEXT' ); ?></div>
    </div>




</div>
<?php if ( $this->params->get( 'articleid_foot', 0 ) ) : ?>
	<?php $articleid_foot = StorelocatorHelper::getArticle($this->params->get( 'articleid_foot', 0 )); ?>
	<div class="sl_article_bottom"><?php echo $articleid_foot->introtext . $articleid_foot->fulltext; ?></div>
<?php endif; ?>