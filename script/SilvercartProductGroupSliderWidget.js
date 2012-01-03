/**
 * Slider for product group images.
 * 
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 03.01.2012
 */
var ProductRotator = function () {
    var self        = this;
    self.v          = {ani: {duration:600} };
    self.el         = {};
    self.products   = [];
    
    /**
     * Creates a product object and returns it
     * 
     * @param string name      The name of the product group
     * @param string url       The url to the product group page
     * @param string img       The image file path
     * @param string thumb     The thumbnail file path
     * @param string img_alt   The alt text for the image
     * @param string thumb_alt The alt text for the thumbnail
     * 
     * @return Object Product
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.01.2012
     */
    self.Product = function (name, url, img, thumb, img_alt, thumb_alt) {
        var product = this;
        //
        // Init
        product.init = function (name, url, img, thumb, img_alt, thumb_alt) {
            product.name = name;
            product.url = url;
            product.img = img;
            product.thumb = thumb;
            product.img_alt = img_alt;
            product.thumb_alt = thumb_alt;
            return product;
        };
        return product.init(name, url, img, thumb, img_alt, thumb_alt);
    };
    
    /**
     * Add a product to the queue.
     *
     * @param string name      The name of the product group
     * @param string url       The url to the product group page
     * @param string img       The image file path
     * @param string thumb     The thumbnail file path
     * @param string img_alt   The alt text for the image
     * @param string thumb_alt The alt text for the thumbnail
     *
     * @return Object ProductRotator
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.01.2012
     */
    self.addProduct = function (name, url, img, thumb, img_alt, thumb_alt) {
        var product = new self.Product(name, url, img, thumb, img_alt, thumb_alt);
        self.products.push(product);
        return self;
    };
    
    /**
     * Returns the next index of the queue.
     * 
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.01.2012
     */
    self.getNext = function(i) {
        if (++i >= self.products.length) {
            return 0;
        } else {
            return i;
        }
    };
    
    /**
     * Returns the previous index of the queue.
     * 
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.01.2012
     */
    self.getPrev = function(i) {
        if (--i < 0) {
            return self.products.length - 1;
        } else {
            return i;
        }
    };
    
    /**
     * Sets the given index of the queue as main display.
     * 
     * @return Object
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.01.2012
     */
    self.setHero = function(i) {
        var product = self.products[i];
        var link = $('<a/>')
            .attr('href', product.url)
            .appendTo(self.el.heroContainer);
        var productEl = $('<img/>')
            .attr('src', product.img)
            .attr('alt', product.img_alt)
            .addClass('silvercart-productgroup-slider-image')
            .appendTo(link);
        return productEl;
    };
    
    /**
     * Set the given index of the queue as preceding display
     * 
     * @return Object
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.01.2012
     */
    self.setLeft = function(i) {
        var product = self.products[i];
        var productEl = $('<img/>')
            .attr('src', product.thumb)
            .addClass('silvercart-productgroup-slider-image')
            .appendTo(self.el.leftContainer)
            .click(
                function() {
                    ProductRotatorResetAnimation(pr);
                    self.goTo(i, -1);
                }
            );
        return productEl;
    };
    
    /**
     * Set the given index of the queue as consecutive display
     * 
     * @return Object
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.01.2012
     */
    self.setRight = function(i) {
        var product = self.products[i];
        var productEl = $('<img/>')
            .attr('src', product.thumb)
            .addClass('silvercart-productgroup-slider-image')
            .appendTo(self.el.rightContainer)
            .click(
                function() {
                    ProductRotatorResetAnimation(pr);
                    self.goTo(i, 1);
                }
            );
        return productEl;
    };
    
    /**
     * Set the given index of the queue as main display and animate accordingly.
     * 
     * @param int i   The index of the queue to go to
     * @param int dir The direction to scroll
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.01.2012
     */
    self.goTo = function(i, dir) {
        
        // Delete main image and fade in the new main image
        self.el.heroContainer.find('img').fadeOut('slow');
        self.setHero(i).hide().fadeIn('slow');
        
        var leftDisplay  = self.getPrev(i);
        var rightDisplay = self.getNext(i);
        
        // Fade out left display image
        self.el.leftContainer.find('img')
            .animate({
                'left':     -150*dir,
                'opacity':  0
            }, {
                duration: self.v.ani.duration,
                complete: function(){ $(this).remove(); }
            });
            
        // Fade in left display new image
        self.setLeft(leftDisplay).css(
            {
                'left':     150*dir,
                'opacity':  0
            }
        ).animate(
            {
                'left':     0,
                'opacity':  1
            }, {
                duration: self.v.ani.duration
            }
        );
        
        // Fade out right display image
        self.el.rightContainer.find('img')
            .animate({
                'right':    150*dir,
                'opacity':  0
            }, {
                duration: self.v.ani.duration,
                complete: function(){ $(this).remove(); }
            });
        
        // Fade in right display new image
        self.setRight(rightDisplay).css(
            {
                'right':    -150*dir,
                'opacity':  0
            }
        ).animate(
            {
                'right':    0,
                'opacity':  1
            }, {
                duration: self.v.ani.duration
            }
        );
        
        delete(leftDisplay);
        delete(rightDisplay);
    };
    
    /**
     * Set the initial displays.
     * 
     * @return Object ProductRotator
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.01.2012
     */
    self.start = function(){
        self.setHero(0).hide().fadeIn('slow');
        self.setLeft(self.getPrev(0)).hide().fadeIn('slow');
        self.setRight(self.getNext(0)).hide().fadeIn('slow');
        
        ProductRotatorResetAnimation();
        return self;
    };
    
    /**
     * Initialise the ProductRotator.
     * 
     * @return Object ProductRotator
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 03.01.2012
     */
    self.init = function(){
        self.el.leftContainer = $('.silvercart-productgroup-slider-navigation-panel-left');
        self.el.rightContainer = $('.silvercart-productgroup-slider-navigation-panel-right');
        self.el.leftForeground = $('.silvercart-productgroup-slider-navigation-blind-left');
        self.el.rightForeground = $('.silvercart-productgroup-slider-navigation-blind-right');
        self.el.heroContainer = $('.silvercart-productgroup-slider-presentation-panel');
        self.el.productRotator = $('.silvercart-productgroup-slider');
        return self;
    };
    
    return self.init();
};

/**
 * Switch to the next element of the product rotator
 * 
 * @param Object ProductRotator The ProductRotator object
 * 
 * @return void
 * 
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 03.01.2012
 */
function ProductRotatorResetAnimation(ProductRotator) {
    window.clearInterval(productRotatorAnimation);
    
    productRotatorAnimation = window.setInterval(
        "ProductRotatorAnimation(pr)", 7000
    );
}

/**
 * Switch to the next element of the product rotator
 * 
 * @param Object ProductRotator The ProductRotator object
 * 
 * @return void
 * 
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 03.01.2012
 */
function ProductRotatorAnimation(ProductRotator) {
    ProductRotator.el.rightContainer.find('img').click();
    
    return true;
}