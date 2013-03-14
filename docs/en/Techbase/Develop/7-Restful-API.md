# Restful API

SilverCart offers a restful api service that can be used to request data about orders etc.

The service is reachable under "http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/".


## Security

By default, only users with administrator privileges are allowed to call the service.

Unlike the default Silverstripe API you don't need to be logged in already to perform requests. If you're not logged in already, a HTTP Basic Authentification will be used to gain access.


## Request modifiers

Every request can be modified with URL parameters. At this moment you can use the following ones:

- &limit=<numeric>: Limit the result set
- &sort=<myfield>&dir=<asc|desc>
- &add_fields=<string>: Comma-separated list of fields to export. This list replaces the default fields.


## How to request

There's a common pattern to invoke requests with SilvercartRestfulServer.

- GET /api/silvercart/(ClassName)/(ID) - gets a database record
- GET /api/silvercart/(ClassName)/(ID)/(Relation) - get all of the records linked to this database record by the given reatlion
- GET /api/silvercart/(ClassName)?(Field)=(Val)&(Field)=(Val) - searches for matching database records


## Supported request classes

Currently the following SilverCart classes support API access:

- Member
- SilvercartOrder
- SilvercartPosition


## Requesting orders

URL: http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder


## Requesting a single order

URL: http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder/{ORDER_ID}


## Requesting all positions for an order

URL: http://{URL_TO_YOUR_WEBSHOP}/api/silvercart/SilvercartOrder/{ORDER_ID}/SilvercartOrderPositions


## Example of an order export

    <DataObjectSet totalSize="32888">
        <SilvercartOrder href="http://jurashop.juradoctor.skoehler.intern.pixeltricks.de/api/v1/SilvercartOrder/32889.xml">
            <AmountTotal>67.34</AmountTotal>
            <PriceType>gross</PriceType>
            <HandlingCostPayment>0</HandlingCostPayment>
            <HandlingCostShipment>3.76</HandlingCostShipment>
            <TaxRatePayment>0</TaxRatePayment>
            <TaxRateShipment>19</TaxRateShipment>
            <TaxAmountPayment>0</TaxAmountPayment>
            <TaxAmountShipment>0.600336</TaxAmountShipment>
            <Note>TEST</Note>
            <WeightTotal>0</WeightTotal>
            <CustomersEmail>skoehler@pixeltricks.de</CustomersEmail>
            <OrderNumber>200830</OrderNumber>
            <AmountGrossTotal/>
            <DirectDebitBankAccountHolder/>
            <DirectDebitBankCode/>
            <DirectDebitBankAccountNumber/>
            <DirectDebitBankName/>
            <DirectDebitBankIban/>
            <DirectDebitBankBic/>
            <ID>32889</ID>
            <SilvercartOrderShippingAddress href="http://jurashop.juradoctor.skoehler.intern.pixeltricks.de/api/v1/SilvercartOrderShippingAddress/88405.xml">
                <TaxIdNumber>1234567890</TaxIdNumber>
                <Company>pixeltricks GmbH & Sonderzeichen</Company>
                <Salutation>Herr</Salutation>
                <FirstName>Sascha</FirstName>
                <Surname>Köhler</Surname>
                <Addition>Mein Adresszusatz</Addition>
                <PostNumber/>
                <Packstation/>
                <Street>Merkurstr.</Street>
                <StreetNumber>9</StreetNumber>
                <Postcode>67663</Postcode>
                <City>Kaiserslautern</City>
                <PhoneAreaCode>0631</PhoneAreaCode>
                <Phone>35477282</Phone>
                <Fax>9876543210</Fax>
                <IsPackstation>0</IsPackstation>
                <SilvercartCountryISO2>DE</SilvercartCountryISO2>
                <SilvercartCountryISO3>DEU</SilvercartCountryISO3>
                <SilvercartCountryISON>276</SilvercartCountryISON>
                <SilvercartCountryFIPS>GM</SilvercartCountryFIPS>
                <ID>88405</ID>
            </SilvercartOrderShippingAddress>
            <SilvercartOrderInvoiceAddress href="http://jurashop.juradoctor.skoehler.intern.pixeltricks.de/api/v1/SilvercartOrderInvoiceAddress/88406.xml">
                <TaxIdNumber>1234567890</TaxIdNumber>
                <Company>pixeltricks GmbH & Sonderzeichen</Company>
                <Salutation>Herr</Salutation>
                <FirstName>Sascha</FirstName>
                <Surname>Köhler</Surname>
                <Addition>Mein Adresszusatz</Addition>
                <PostNumber/>
                <Packstation/>
                <Street>Merkurstr.</Street>
                <StreetNumber>9</StreetNumber>
                <Postcode>67663</Postcode>
                <City>Kaiserslautern</City>
                <PhoneAreaCode>0631</PhoneAreaCode>
                <Phone>35477282</Phone>
                <Fax>9876543210</Fax>
                <IsPackstation>0</IsPackstation>
                <SilvercartCountryISO2>DE</SilvercartCountryISO2>
                <SilvercartCountryISO3>DEU</SilvercartCountryISO3>
                <SilvercartCountryISON>276</SilvercartCountryISON>
                <SilvercartCountryFIPS>GM</SilvercartCountryFIPS>
                <ID>88406</ID>
            </SilvercartOrderInvoiceAddress>
            <Member href="http://jurashop.juradoctor.skoehler.intern.pixeltricks.de/api/v1/Member/2.xml">
                <FirstName>Sascha</FirstName>
                <Surname>Köhler</Surname>
                <Email>skoehler@pixeltricks.de</Email>
                <Salutation>Herr</Salutation>
                <NewsletterOptInStatus>0</NewsletterOptInStatus>
                <SubscribedToNewsletter>0</SubscribedToNewsletter>
                <Birthday/>
                <CustomerNumber>100001</CustomerNumber>
                <ID>2</ID>
            </Member>
            <SilvercartOrderPositions linktype="has_many" href="http://jurashop.juradoctor.skoehler.intern.pixeltricks.de/api/v1/SilvercartOrder/32889/SilvercartOrderPositions.xml">
                <SilvercartOrderPosition href="http://jurashop.juradoctor.skoehler.intern.pixeltricks.de/api/v1/SilvercartOrderPosition/65630.xml">
                    <Price>64.95</Price>
                    <PriceTotal>64.95</PriceTotal>
                    <isChargeOrDiscount>0</isChargeOrDiscount>
                    <isIncludedInTotal>0</isIncludedInTotal>
                    <chargeOrDiscountModificationImpact>none</chargeOrDiscountModificationImpact>
                    <Tax>10.3702</Tax>
                    <TaxTotal>10.3702</TaxTotal>
                    <TaxRate>19</TaxRate>
                    <ProductDescription>...</ProductDescription>
                    <Quantity>1.00</Quantity>
                    <Title>6 Claris white Filter + 25 Tabletten</Title>
                    <ProductNumber>12405W</ProductNumber>
                    <SilvercartVoucherCode/>
                    <SilvercartVoucherValue/>
                    <ID>65630</ID>
                </SilvercartOrderPosition>
                <SilvercartOrderPosition href="http://jurashop.juradoctor.skoehler.intern.pixeltricks.de/api/v1/SilvercartOrderPosition/65631.xml">
                    <Price>-1.37</Price>
                    <PriceTotal>-1.37</PriceTotal>
                    <isChargeOrDiscount>1</isChargeOrDiscount>
                    <isIncludedInTotal>0</isIncludedInTotal>
                    <chargeOrDiscountModificationImpact>totalValue</chargeOrDiscountModificationImpact>
                    <Tax>0</Tax>
                    <TaxTotal>-0.218739</TaxTotal>
                    <TaxRate>19</TaxRate>
                    <ProductDescription>Abzug für Vorkasse (2%)</ProductDescription>
                    <Quantity>1.00</Quantity>
                    <Title>Abzug für Vorkasse (2%)</Title>
                    <ProductNumber/>
                    <SilvercartVoucherCode/>
                    <SilvercartVoucherValue/>
                    <ID>65631</ID>
                </SilvercartOrderPosition>
            </SilvercartOrderPositions>
        </SilvercartOrder>
        ...
    </DataObjectSet>