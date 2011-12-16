var ProductRotator = function () {
    var self = this;
    self.v = {ani: {duration:600} };
    self.el = {};
    self.products = [];
    
    // Product
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
    self.addProduct = function (name, url, img, thumb, img_alt, thumb_alt) {
        var product = new self.Product(name, url, img, thumb, img_alt, thumb_alt);
        self.products.push(product);
        return self;
    };
    self.getNext = function(i) {
        if (++i >= self.products.length) {
            return 0;
        } else {
            return i;
        }
    };
    self.getPrev = function(i) {
        if (--i < 0) {
            return self.products.length - 1;
        } else {
            return i;
        }
    };
    
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
    self.setLeft = function(i) {
        var product = self.products[i];
        var productEl = $('<img/>')
            .attr('src', product.thumb)
            .addClass('silvercart-productgroup-slider-image')
            .appendTo(self.el.leftContainer)
            .click(function(){ self.goTo(i, -1) });
        return productEl;
    };
    self.setRight = function(i) {
        var product = self.products[i];
        var productEl = $('<img/>')
            .attr('src', product.thumb)
            .addClass('silvercart-productgroup-slider-image')
            .appendTo(self.el.rightContainer)
            .click(function(){ self.goTo(i, 1) });
        return productEl;
    };
    
    self.goTo = function(i, dir) {
        // hero
        //self.el.heroContainer.find('img, div').empty();
        self.el.heroContainer.html('');
        self.setHero(i).hide().fadeIn('slow');
        self.el.leftContainer.find('img')
            .animate({
                'left': -150*dir,
                'opacity': 0
            }, {
                duration: self.v.ani.duration,
                complete: function(){ $(this).remove(); }
            });
        self.setLeft(self.getPrev(i))
            .css({
                'left': 150*dir,
                'opacity': 0
            })
            .animate({
                'left': 0,
                'opacity': 1
            }, {
                duration: self.v.ani.duration
            });
        // right
        self.el.rightContainer.find('img')
            .animate({
                'right': 150*dir,
                'opacity': 0
            }, {
                duration: self.v.ani.duration,
                complete: function(){ $(this).remove(); }
            });
        self.setRight(self.getNext(i))
            .css({
                'right': -150*dir,
                'opacity': 0
            })
            .animate({
                'right': 0,
                'opacity': 1
            }, {
                duration: self.v.ani.duration
            });
    };
    //
    // Start
    self.start = function(){
        self.setHero(0).hide().fadeIn('slow');
        self.setLeft(self.getPrev(0)).hide().fadeIn('slow');
        self.setRight(self.getNext(0)).hide().fadeIn('slow');
        return self;
    };
    //
    // Init
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
