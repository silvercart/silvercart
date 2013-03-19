# Restful API

SilverCart offers a restful api service that can be used to request data about orders, customers, payment methods etc.

## Security
- - -

By default, only users with administrator privileges are allowed to call the service.

Unlike the default Silverstripe API a requester doesn't need to be logged in to perform requests. If one's not logged in already, an HTTP Basic Authentification will be used to gain access.

Some SilverCart classes provide special access restrictions (e.g. SilvercartOrder) that have to be taken into account.


## How to start a request
- - -

The service is reachable under "http://{URL\_TO\_YOUR\_WEBSHOP}/api/silvercart/".

There's a common pattern to invoke requests with SilvercartRestfulServer.

- GET /api/silvercart/(ClassName) *- gets all database records of the given class name*
- GET /api/silvercart/(ClassName)/(ID) *- gets a database record*
- GET /api/silvercart/(ClassName)/(ID)/(Relation) *- get all of the records linked to this database record by the given relation*
- GET /api/silvercart/(ClassName)?(Field)=(Val)&(Field)=(Val) *- searches for matching database records*


## Request modifiers
- - -

Every request can be modified with URL parameters. At this moment you can use the following ones:

- &start=<numeric> *Start at a position in the result set*
- &limit=<numeric> *Limit the result set*
- &sort={FIELD_NAME}&dir={asc|desc} *sort the result set ascending or descending*
- &add_fields=<string> *Comma-separated list of fields to export. This list replaces the default fields.*


## Supported request classes
- - -

Currently the following SilverCart classes support API access:

- Member
- Group
- SilvercartOrder
- SilvercartOrderPosition
- SilvercartOrderStatus
- SilvercartPaymentMethod
- SilvercartShippingMethod


## Request structure and examples
- - -

### Request orders

Orders can be requested by invoking the URL

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder


#### Search orders by date and date ranges

Searches by the order date should be applied to the "Created" field. There's one caveout though: dates need to be in german format (dd.mm.yyyy).

**Examples:**

*Get all orders from may 6th, 2013:*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?Created=06.03.2013

*Get all orders from may 6th, 2013 to may 14th, 2013:*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?Created=06.03.2013-14.03.2013


#### Search orders by order status

Searches by order status should be applied to the "SilvercartOrderStatus\_\_ID" field. It's possible to search for more than one order status, so the field needs to be an array ("SilvercartOrderStatus\_\_ID[]").

Every order status can be set to influence the result positively or negatively ("SilvercartOrderStatus\_\_ID[]=1 or "SilvercartOrderStatus\_\_ID[]=0").

**Examples:**

*Get all orders with a given order status*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?SilvercartOrderStatus__ID[{ORDER\_STATUS_ID}]=1

*Get all orders by multiple given order status*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?SilvercartOrderStatus__ID[{ORDER_STATUS_ID_1}]=1&SilvercartOrderStatus__ID[{ORDER_STATUS_ID_2}]=1

*Get all orders that don't have a given order status*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?SilvercartOrderStatus__ID[{ORDER_STATUS_ID}]=0

*Get all orders that don't have any of the given order status*

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder?SilvercartOrderStatus__ID[{ORDER_STATUS_ID_1}]=0&SilvercartOrderStatus__ID[{ORDER_STATUS_ID_2}]=0


#### Request a single order

    URL: http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder/{ORDER_ID}


#### Request all positions for an order

    URL: http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder/{ORDER_ID}/SilvercartOrderPositions


### Request order status

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrderStatus


### Request payment methods

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartPaymentMethod


### Request shipping methods

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartShippingMethod


### Request members

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/Member


#### Request all orders for a member

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/Member/{MEMBER_ID}/SilvercartOrder


### Request groups

    http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/Group


## Response structure and examples
- - -

The API responses are always embedded into the root element "DataObjectSet" which has an attribute "totalSize" containing the number of found records.

    <DataObjectSet totalSize="3">
        ...
    </DataObjectSet>


### Response for an order list
- - -

    <DataObjectSet totalSize="{number of total records found, e.g. 1560}">
        <SilvercartOrder href="{SHOP_URL}/api/silvercart/SilvercartOrder/32889.xml">
            <AmountTotal>{e.g. 67.34}</AmountTotal>
            <PriceType>{can be "gross" or "net"}</PriceType>
            <HandlingCostPayment>{e.g. 0}</HandlingCostPayment>
            <HandlingCostShipment>{e.g. 3.76}</HandlingCostShipment>
            <TaxRatePayment>{e.g. 0}</TaxRatePayment>
            <TaxRateShipment>{e.g. 19}</TaxRateShipment>
            <TaxAmountPayment>0</TaxAmountPayment>
            <TaxAmountShipment>0.600336</TaxAmountShipment>
            <Note>{The customer's notes}</Note>
            <WeightTotal>0</WeightTotal>
            <CustomersEmail>{EmailAdress}}</CustomersEmail>
            <OrderNumber>200830</OrderNumber>
            <AmountGrossTotal/>
            <DirectDebitBankAccountHolder/>
            <DirectDebitBankCode/>
            <DirectDebitBankAccountNumber/>
            <DirectDebitBankName/>
            <DirectDebitBankIban/>
            <DirectDebitBankBic/>
            <ID>{ID}</ID>

            <SilvercartOrderShippingAddress href="{SHOP_URL}/api/silvercart/SilvercartOrderShippingAddress/88405.xml">
                <TaxIdNumber>{TaxIdNumber}</TaxIdNumber>
                <Company>{CompanyName}</Company>
                <Salutation>{Salutation}</Salutation>
                <FirstName>{FirstName}</FirstName>
                <Surname>{Surname}</Surname>
                <Addition>{Address addition}</Addition>
                <PostNumber/>
                <Packstation/>
                <Street>{StreetName}</Street>
                <StreetNumber>{StreetNumber}</StreetNumber>
                <Postcode>{PostCode}</Postcode>
                <City>{City}</City>
                <PhoneAreaCode>{PhoneAreaCode}</PhoneAreaCode>
                <Phone>{Phone}</Phone>
                <Fax>{Fax}}</Fax>
                <IsPackstation>{0 or 1 if address is a Packstation address}</IsPackstation>
                <SilvercartCountryISO2>DE</SilvercartCountryISO2>
                <SilvercartCountryISO3>DEU</SilvercartCountryISO3>
                <SilvercartCountryISON>276</SilvercartCountryISON>
                <SilvercartCountryFIPS>GM</SilvercartCountryFIPS>
                <ID>88405</ID>
            </SilvercartOrderShippingAddress>

            <SilvercartOrderInvoiceAddress href="{SHOP_URL}/api/silvercart/SilvercartOrderInvoiceAddress/88406.xml">
                <TaxIdNumber>{TaxIdNumber}</TaxIdNumber>
                <Company>{CompanyName}</Company>
                <Salutation>{Salutation}</Salutation>
                <FirstName>{FirstName}</FirstName>
                <Surname>{Surname}</Surname>
                <Addition>{Address addition}</Addition>
                <PostNumber/>
                <Packstation/>
                <Street>{StreetName}</Street>
                <StreetNumber>{StreetNumber}</StreetNumber>
                <Postcode>{PostCode}</Postcode>
                <City>{City}</City>
                <PhoneAreaCode>{PhoneAreaCode}</PhoneAreaCode>
                <Phone>{Phone}</Phone>
                <Fax>{Fax}}</Fax>
                <IsPackstation>{0 or 1 if address is a Packstation address}</IsPackstation>
                <SilvercartCountryISO2>DE</SilvercartCountryISO2>
                <SilvercartCountryISO3>DEU</SilvercartCountryISO3>
                <SilvercartCountryISON>276</SilvercartCountryISON>
                <SilvercartCountryFIPS>GM</SilvercartCountryFIPS>
                <ID>{ID}</ID>
            </SilvercartOrderInvoiceAddress>

            <SilvercartPaymentMethod href="{SHOP_URL}/api/silvercart/SilvercartPaymentPrepayment/4.xml">
                <PaymentChannel>prepayment</PaymentChannel>
                <ID>4</ID>
            </SilvercartPaymentMethod>

            <SilvercartShippingMethod href="{SHOP_URL}/api/silvercart/SilvercartShippingMethod/5.xml">
                <ID>5</ID>
            </SilvercartShippingMethod>

            <SilvercartOrderStatus href="{SHOP_URL}/api/silvercart/SilvercartOrderStatus/3.xml">
                <Code>pending</Code>
                <ID>3</ID>
            </SilvercartOrderStatus>

            <Member href="{SHOP_URL}/api/silvercart/Member/2.xml">
                <FirstName>{FirstName}</FirstName>
                <Surname>{Surname}</Surname>
                <Email>{Email}</Email>
                <Salutation>{Salutation, can be "Herr" or "Frau"}</Salutation>
                <NewsletterOptInStatus>{0 or 1, NewsletterOptInStatus}</NewsletterOptInStatus>
                <SubscribedToNewsletter>{0 or 1, SubscribedToNewsletter}</SubscribedToNewsletter>
                <Birthday/>
                <CustomerNumber>{CustomerNumber}</CustomerNumber>
                <ID>{ID}</ID>
            </Member>

            <SilvercartOrderPositions linktype="has_many" href="{SHOP_URL}/api/silvercart/SilvercartOrder/32889/SilvercartOrderPositions.xml">
                <SilvercartOrderPosition href="{SHOP_URL}/api/silvercart/SilvercartOrderPosition/65630.xml">
                    <Price>{Price, e.g. 64.95)}</Price>
                    <PriceTotal>{Price total, e.g. 64.95}</PriceTotal>
                    <isChargeOrDiscount>{0 or 1, indicates if this position is a charge or discount}</isChargeOrDiscount>
                    <isIncludedInTotal>{0 or 1, indicates if this position is already included in the total price}</isIncludedInTotal>
                    <chargeOrDiscountModificationImpact>{"none", "productValue" or "totalValue"}</chargeOrDiscountModificationImpact>
                    <Tax>{Tax, e.g. 10.3702}</Tax>
                    <TaxTotal>{Tax total, e.g. 10.3702}</TaxTotal>
                    <TaxRate>{Tax rate, e.g. 19}</TaxRate>
                    <ProductDescription/>
                    <Quantity>{Quantity, e.g. 1.00}</Quantity>
                    <Title>{Title}</Title>
                    <ProductNumber>{Product number, e.g. 7d56405W}</ProductNumber>
                    <SilvercartVoucherCode/>
                    <SilvercartVoucherValue/>
                    <ID>{ID}</ID>
                </SilvercartOrderPosition>

                <SilvercartOrderPosition href="{SHOP_URL}/api/silvercart/SilvercartOrderPosition/65631.xml">
                    ...
                </SilvercartOrderPosition>
            </SilvercartOrderPositions>
        </SilvercartOrder>
    </DataObjectSet>


### Response for an order position
- - -

    <DataObjectSet totalSize="1">
        <SilvercartOrderPosition href="{SHOP_URL}/api/silvercart/SilvercartOrderPosition/65630.xml">
            <Price>{Price, e.g. 64.95)}</Price>
            <PriceTotal>{Price total, e.g. 64.95}</PriceTotal>
            <isChargeOrDiscount>{0 or 1, indicates if this position is a charge or discount}</isChargeOrDiscount>
            <isIncludedInTotal>{0 or 1, indicates if this position is already included in the total price}</isIncludedInTotal>
            <chargeOrDiscountModificationImpact>{"none", "productValue" or "totalValue"}</chargeOrDiscountModificationImpact>
            <Tax>{Tax, e.g. 10.3702}</Tax>
            <TaxTotal>{Tax total, e.g. 10.3702}</TaxTotal>
            <TaxRate>{Tax rate, e.g. 19}</TaxRate>
            <ProductDescription/>
            <Quantity>{Quantity, e.g. 1.00}</Quantity>
            <Title>{Title}</Title>
            <ProductNumber>{Product number, e.g. 7d56405W}</ProductNumber>
            <SilvercartVoucherCode/>
            <SilvercartVoucherValue/>
            <ID>{ID}</ID>
        </SilvercartOrderPosition>
    </DataObjectSet>


### Response for an order status list
- - -

    <DataObjectSet totalSize="2">
        <SilvercartOrderStatus href="{SHOP_URL}/api/silvercart/SilvercartOrderStatus/21.xml">
            <Code>finished</Code>
            <ID>21</ID>
        </SilvercartOrderStatus>
        <SilvercartOrderStatus href="{SHOP_URL}/api/silvercart/SilvercartOrderStatus/3.xml">
            <Code>pending</Code>
            <ID>3</ID>
        </SilvercartOrderStatus>
    </DataObjectSet>


### Response for a payment method list
- - -

    <DataObjectSet totalSize="8">
        <SilvercartPaymentMethod href="{SHOP_URL}/api/silvercart/SilvercartPaymentIPayment/1.xml">
            <PaymentChannel>cc</PaymentChannel>
            <Name>Kreditkarte / Debitkarte (iPayment)</Name>
            <ID>1</ID>
        </SilvercartPaymentMethod>
        <SilvercartPaymentMethod href="{SHOP_URL}/api/silvercart/SilvercartPaymentIPayment/2.xml">
            <PaymentChannel>elv</PaymentChannel>
            <Name>Lastschrift (iPayment)</Name>
            <ID>2</ID>
        </SilvercartPaymentMethod>
        <SilvercartPaymentMethod href="{SHOP_URL}/api/silvercart/SilvercartPaymentPaypal/3.xml">
            <Name>Paypal</Name>
            <ID>3</ID>
        </SilvercartPaymentMethod>
        <SilvercartPaymentMethod href="{SHOP_URL}/api/silvercart/SilvercartPaymentPrepayment/4.xml">
            <PaymentChannel>prepayment</PaymentChannel>
            <Name>Vorkasse</Name>
            <ID>4</ID>
        </SilvercartPaymentMethod>
        <SilvercartPaymentMethod href="{SHOP_URL}/api/silvercart/SilvercartPaymentPrepayment/5.xml">
            <PaymentChannel>invoice</PaymentChannel>
            <Name>Rechnung</Name>
            <ID>5</ID>
        </SilvercartPaymentMethod>
        <SilvercartPaymentMethod href="{SHOP_URL}/api/silvercart/SilvercartPaymentSaferpay/6.xml">
            <Name>Kreditkarte</Name>
            <ID>6</ID>
        </SilvercartPaymentMethod>
        <SilvercartPaymentMethod href="{SHOP_URL}/api/silvercart/SilvercartPaymentCOD/7.xml">
            <Name>Nachnahme</Name>
            <ID>7</ID>
        </SilvercartPaymentMethod>
        <SilvercartPaymentMethod href="{SHOP_URL}/api/silvercart/SilvercartPaymentDirectDebit/8.xml">
            <Name>Lastschrift</Name>
            <ID>8</ID>
        </SilvercartPaymentMethod>
    </DataObjectSet>


### Response for a shipping method list
- - -

    <DataObjectSet totalSize="3">
        <SilvercartShippingMethod href="{SHOP_URL}/api/silvercart/SilvercartShippingMethod/4.xml">
            <Title>DHL Paket</Title>
            <ID>4</ID>
        </SilvercartShippingMethod>
        <SilvercartShippingMethod href="{SHOP_URL}/api/silvercart/SilvercartShippingMethod/5.xml">
            <Title>Hermes Versand</Title>
            <ID>5</ID>
        </SilvercartShippingMethod>
        <SilvercartShippingMethod href="{SHOP_URL}/api/silvercart/SilvercartShippingMethod/6.xml">
            <Title>Briefpost</Title>
            <ID>6</ID>
        </SilvercartShippingMethod>
    </DataObjectSet>


### Response for a member list
- - -

    <DataObjectSet totalSize="22022">
        <Member href="{SHOP_URL}/api/silvercart/Member/4.xml">
            <FirstName/>
            <Surname/>
            <Email/>
            <Salutation/>
            <NewsletterOptInStatus/>
            <SubscribedToNewsletter/>
            <Birthday/>
            <CustomerNumber/>
            <ID/>
        </Member>
    </DataObjectSet>


### Response for a group list
- - -

    <DataObjectSet totalSize="5">
        <Group href="URL_TO_YOUR_WEBSHOP/api/silvercart/Group/1.xml">
            <Title>Inhaltsautoren</Title>
            <Description/>
            <Code>content-authors</Code>
            <Pricetype>---</Pricetype>
            <ID>1</ID>
        </Group>
        <Group href="URL_TO_YOUR_WEBSHOP/api/silvercart/Group/2.xml">
            <Title>Administratoren</Title>
            <Description/>
            <Code>administrators</Code>
            <Pricetype>---</Pricetype>
            <ID>2</ID>
        </Group>
        <Group href="URL_TO_YOUR_WEBSHOP/api/silvercart/Group/3.xml">
            <Title>Anonymer Kunde</Title>
            <Description/>
            <Code>anonymous</Code>
            <Pricetype>gross</Pricetype>
            <ID>3</ID>
        </Group>
        <Group href="URL_TO_YOUR_WEBSHOP/api/silvercart/Group/4.xml">
            <Title>Händler</Title>
            <Description/>
            <Code>b2b</Code>
            <Pricetype>gross</Pricetype>
            <ID>4</ID>
        </Group>
        <Group href="URL_TO_YOUR_WEBSHOP/api/silvercart/Group/5.xml">
            <Title>Endkunde</Title>
            <Description/>
            <Code>b2c</Code>
            <Pricetype>gross</Pricetype>
            <ID>5</ID>
        </Group>
    </DataObjectSet>
