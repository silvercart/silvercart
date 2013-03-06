# iPayment

iPayment is a very popular payment gateway in germany by the internet company 1&1. With iPayment Your customers can make their purchases using MasterCart, Visa, AmericanExpress and many more. iPayment also offers payment via direct debit. Credit card and direct debit are configured as separate payment methods in the backoffice.

How do I configure iPayment/Credit card payment?

The basic configuration is the same as described under “payment modules”. However there are some adjustments specific to iPayment.

![](_images/configpaymentmethodipaymentapi_1-2.png)

As long as You are in development mode You can use iPayment's development API settings. You will get Your own API credentials for the live mode if You have a contract with iPayment.

iPayment returns some payment stati which will be saved to a customers order. initially You have to attribute them:

![](_images/configpaymentmethodipaymentattributedstatus_1-2.png)
