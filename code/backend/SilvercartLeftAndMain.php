<?php
class SilvercartLeftAndMain extends DataObjectDecorator {

    public function SilvercartMainMenu() {
		// Don't accidentally return a menu if you're not logged in - it's used to determine access.
		if(!Member::currentUser()) return new DataObjectSet();

		// Encode into DO set
		$menu = new DataObjectSet();
		$menuItems = CMSMenu::get_viewable_menu_items();
		if($menuItems) foreach($menuItems as $code => $menuItem) {
			// alternate permission checks (in addition to LeftAndMain->canView())
			if(
				isset($menuItem->controller) 
				&& $this->owner->hasMethod('alternateMenuDisplayCheck')
				&& !$this->owner->alternateMenuDisplayCheck($menuItem->controller)
			) {
				continue;
			}

			$linkingmode = "";
			
			if(strpos($this->owner->Link(), $menuItem->url) !== false) {
				if($this->owner->Link() == $menuItem->url) {
					$linkingmode = "current";
				
				// default menu is the one with a blank {@link url_segment}
				} else if(singleton($menuItem->controller)->stat('url_segment') == '') {
					if($this->owner->Link() == $this->owner->stat('url_base').'/') $linkingmode = "current";

				} else {
					$linkingmode = "current";
				}
			}
            if (!empty($menuItem->controller)) {
                $urlSegment = singleton($menuItem->controller)->stat('url_segment');

                if (substr($urlSegment, 0, 10) === 'silvercart') {
                    continue;
                }
            }

			// already set in CMSMenu::populate_menu(), but from a static pre-controller
			// context, so doesn't respect the current user locale in _t() calls - as a workaround,
			// we simply call LeftAndMain::menu_title_for_class() again if we're dealing with a controller
			if($menuItem->controller) {
				$defaultTitle = LeftAndMain::menu_title_for_class($menuItem->controller);
				$title = _t("{$menuItem->controller}.MENUTITLE", $defaultTitle);
			} else {
				$title = $menuItem->title;
			}

			$menu->push(new ArrayData(array(
				"MenuItem" => $menuItem,
				"Title" => Convert::raw2xml($title),
				"Code" => $code,
				"Link" => $menuItem->url,
				"LinkingMode" => $linkingmode
			)));
		}
		
		// if no current item is found, assume that first item is shown
		//if(!isset($foundCurrent)) 
		return $menu;
    }

    public function getCmsSection() {
        $section = '';
        $urlSegment = $this->owner->stat('url_segment')."<br />";

        if (substr($urlSegment, 0, 10) !== 'silvercart') {
            $section = ': '.$this->owner->SectionTitle();
        }

        return $section;
    }
}
