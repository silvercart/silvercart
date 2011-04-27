<?php

/**
 * tests for SilvercartShoppingCart
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 25.04.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShoppingCartTest extends SapphireTest {

    public static $fixture_file = 'silvercart/tests/SilvercartShoppingCartTest.yml';
    
    /**
     * Do all positions of the cart get deleted?
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 25.4.2011
     * @return void
     */
    public function testDelete() {
        //do all 4 positions get loaded?
        $cart = $this->objFromFixture("SilvercartShoppingCart", "ShoppingCart");
        $this->assertEquals(4, $cart->SilvercartShoppingCartPositions()->Count());
        
        //do all 4 positions get deleted?
        $cart->delete();
        $this->assertEquals(0, $cart->SilvercartShoppingCartPositions()->Count());
    }
    
    

}

