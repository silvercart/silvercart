# Restful API

SilverCart offers a restful api service that can be used to request data about orders etc.

The service is reachable under "http://{URL\_TO\_YOUR\_WEBSHOP}/api/silvercart/".


## Security

By default, only users with administrator privileges are allowed to call the service.

Unlike the default Silverstripe API you don't need to be logged in to perform requests. If you're not logged in already, an HTTP Basic Authentification will be used to gain access.


## Response structure

The API responses are always embedded into a the root element "DataObjectSet".

The root element has an attribute "totalSize" containing the number of found records.

    <DataObjectSet totalSize="3">
        ...
    </DataObjectSet>


## Request modifiers

Every request can be modified with URL parameters. At this moment you can use the following ones:

- &start=<numeric>: Start at a position in the result set
- &limit=<numeric>: Limit the result set
- &sort={FIELD_NAME}&dir={asc|desc}
- &add_fields=<string>: Comma-separated list of fields to export. This list replaces the default fields.


## How to start a request

There's a common pattern to invoke requests with SilvercartRestfulServer.

- GET /api/silvercart/(ClassName)/(ID) - gets a database record
- GET /api/silvercart/(ClassName)/(ID)/(Relation) - get all of the records linked to this database record by the given reatlion
- GET /api/silvercart/(ClassName)?(Field)=(Val)&(Field)=(Val) - searches for matching database records


## Supported request classes

Currently the following SilverCart classes support API access:

- Member
- Group
- SilvercartOrder
- SilvercartOrderPosition
- SilvercartOrderStatus
- SilvercartPaymentMethod
- SilvercartShippingMethod


## Requesting orders

Orders can be requested by invoking the URL

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder


### Search orders by date and date ranges

Searches by the order date should be applied to the "Created" field. There's one caveout though: dates need to be in german format (dd.mm.yyyy).

**Examples:**

*Get all orders from may 6th, 2013:*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?Created=06.03.2013

*Get all orders from may 6th, 2013 to may 14th, 2013:*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?Created=06.03.2013-14.03.2013


### Search orders by order status

Searches by order status should be applied to the "SilvercartOrderStatus\_\_ID" field. It's possible to search for more than one order status, so the field needs to be an array ("SilvercartOrderStatus\_\_ID[]").

Every order status can be set to influence the result positively or negatively ("SilvercartOrderStatus\_\_ID[]=1 or "SilvercartOrderStatus\_\_ID[]=0").

**Examples:**

*Get all orders with a given order status*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?SilvercartOrderStatus__ID[{ORDER_STATUS_ID}]=1

*Get all orders by multiple given order status*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?SilvercartOrderStatus__ID[{ORDER_STATUS_ID_1}]=1&SilvercartOrderStatus__ID[{ORDER_STATUS_ID_2}]=1

*Get all orders that don't have a given order status*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?SilvercartOrderStatus__ID[{ORDER_STATUS_ID}]=0

*Get all orders that don't have any of the given order status*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?SilvercartOrderStatus__ID[{ORDER_STATUS_ID_1}]=0&SilvercartOrderStatus__ID[{ORDER_STATUS_ID_2}]=0

### Request a single order

URL: http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder/{ORDER_ID}


### Request all positions for an order

URL: http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder/{ORDER_ID}/SilvercartOrderPositions


### Example of an order export




## Request all order status

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrderStatus


## Request all payment methods

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartPaymentMethod


## Request all shipping methods

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartShippingMethod
