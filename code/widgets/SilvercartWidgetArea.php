<?php
/**
 * Provides an extended WidgetControllers method.
 *
 * @package Silvercart
 * @subpacke Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 17.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartWidgetArea extends WidgetArea {
    
    /**
	 * Used in template instead of {@link Widgets()}
	 * to wrap each widget in its controller, making
	 * it easier to access and process form logic
	 * and actions stored in {@link Widget_Controller}.
	 * 
	 * @return DataObjectSet Collection of {@link Widget_Controller}
	 */
	function WidgetControllers($controllerObject = null) {
		$controllers = new DataObjectSet();
        
		foreach($this->ItemsToRender() as $widget) {
			// find controller
			$controllerClass = '';
			foreach(array_reverse(ClassInfo::ancestry($widget->class)) as $widgetClass) {
				$controllerClass = "{$widgetClass}_Controller";
				if(class_exists($controllerClass)) break;
			}
			$controller = new $controllerClass($widget, $controllerObject);
			$controller->init();
			$controllers->push($controller);
		}

		return $controllers;
	}
}