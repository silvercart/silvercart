# Stock Management

SilverCart offers a stock management as of version 1.1. In the general configuration the stock management can be switched on with a checkbox. If stock management is enabled SilverCart start subtracting sales quantities of a product from the stock quantity of a product. If additionally the checkbox “Is the stock quantity of a product generally overbookable?” the stock quantities of a product can get negative values. Additionally each product has a checkbox “Is the stock quantity of this product overbookable?”.

![](_images/1-2-generalconfiguration-stock.png)

## Examples
- - -

**Stock management is enabled:**


* A product can only be put into the cart as long as its stock quantity is larger than zero.
* If a customer tries to put 10 peaces of a product into the cart but only 5 are in stock he gets only 5 into the cart.
* The cart page shows a notification that the quantity was adjusted.
* If the quantity gets less during the customer checks out his quantity in his cart will be decremented to the available quantity.
* There will be a notification message on the checkout page that shows the changed position.
* Anyways a products quantity can never be below zero. If it reached zero the product can not be added to a cart any more.


**Stock management is enabled and quantities are set to overbookable:**


* SilverCart counts the sales of a product but there are no restrictions for a customer.
* Even if a products quantity is below zero it can still be bought in any quantity.


**Stock management is enabled but not set to overbookable, but a certain product is set to overbookable:**


* Everything works like in the first example except that tis certain product can be overbooked like in the second example.


