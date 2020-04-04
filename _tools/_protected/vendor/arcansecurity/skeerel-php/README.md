# Skeerel PHP

[![Latest Stable Version](https://poser.pugx.org/arcansecurity/skeerel-php/v/stable.svg)](https://packagist.org/packages/arcansecurity/skeerel-php)
[![License](https://poser.pugx.org/arcansecurity/skeerel-php/license.svg)](https://packagist.org/packages/arcansecurity/skeerel-php)

A PHP library for the Skeerel API [https://doc.skeerel.com](https://doc.skeerel.com)

# Requirements

Minimum PHP version: 5.4.0

# Install

## Via composer

`composer require arcansecurity/skeerel-php 2.5.2`

Or in your `composer.json` file:

```
{
  "require": {
    "arcansecurity/skeerel-php": "2.5.2"
  }
}
```

## Manually

If you do not wish to use Composer, you can download the 
[latest release](https://github.com/ArcanSecurity/skeerel-php/releases). 
Then, to use the bindings, include the `init.php` file.

`require_once('/path/to/skeerel-php/init.php');`

# Usage

### Generate a state token

When you display the login page to your user, you have to set a 
session token in order to avoid some [XSRF attacks](https://www.youtube.com/watch?v=_xrhWLqX1j0).
The following line will do the job for you

```php
\Skeerel\Skeerel::generateSessionStateParameter();

// Eventually, you can set the name of the session
\Skeerel\Skeerel::generateSessionStateParameter("my_custom_session_name");
```

### Show the button

In order to connect or pay, a user must click on the Skeerel button.

It's quite simple to insert the button on your page. Just paste this
code where you want the button to appear
```html
    <script type="text/javascript" src="https://api.skeerel.com/assets/v2/javascript/api.min.js"
        id="skeerel-api-script"
        data-website-id="YOUR_WEBSITE_ID"
        data-state="<?php echo \Skeerel\Util\Session::get(\Skeerel\Skeerel::DEFAULT_COOKIE_NAME); ?>"
        data-redirect-url="The url where the user will be redirected once he has complete"
        data-profile-id="SKEEREL_PROFILE_ID" // to force the payment being done with a particular profile
        data-need-shipping-address="" // in case you need a shipping address
        data-need-billing-address="" // in case you need a billing address
        data-delivery-methods-url="https://site.com/delivery_methods.php?user=__USER__&zip_code=__ZIP_CODE__&city=__CITY__&country=__COUNTRY__" // If you need to ship something to the user
        data-checkout="" // If the session is for the user to pay
        data-payment-test="" // If the payment must be done in test mode
        data-amount="1000" // Amount is in the smallest common currency unit. For instance here 10,00€ 10.00USD, ¥1000
        data-currency="eur" // The currency of the transaction
        data-custom="{'product_id': 3000}" // some custom data (if necessary)></script>
```

### Dealing with delivery method

When a user pays with Skeerel, it's likely that you will have to ship its order.
In that case, just set the url that Skeerel should call to get your delivery methods 
in your `data-delivery-methods-url` parameter. For instance:
```
https://site.com/path/to/delivery/methods_
https://site.com/path/to/delivery/methods?uid=__USER___
https://site.com/path/to/delivery/methods?uid=__USER__&zip_code=__ZIP_CODE&city=__CITY__&country=__COUNTRY__
https://site.com/path/to/delivery/methods?some_var=some_value&zip_code=__ZIP_CODE&city=__CITY__&country=__COUNTRY__
```

You can personalize as you like your url, so you can send accurate shipping pricing.

Note that `__USER__` (user identifier), `__ZIP_CODE__`, `__CITY__`, `__COUNTRY__` (two letters country code) are generics values that 
will be replaced automatically before calling your page.

In case of a *guest checkout*, `__USER__` value will be set to empty string.

To format delivery methods, we recommend that you use our embedded method:
```php
// A standard delivery mode
$deliveryMethodStandard = new DeliveryMethod();
$deliveryMethodStandard->setId("standard");
$deliveryMethodStandard->setType(Type::HOME());
$deliveryMethodStandard->setPrimary(true);
$deliveryMethodStandard->setName("Standard shipping");
$deliveryMethodStandard->setDeliveryTextContent("delivery in 3 days");
$deliveryMethodStandard->setPrice(499);



// But also a pick up mode
$deliveryMethodRelay = new DeliveryMethod();
$deliveryMethodRelay->setId("my_relay");
$deliveryMethodRelay->setType(Type::RELAY());
$deliveryMethodRelay->setName("Pick-up & go");
$deliveryMethodRelay->setDeliveryTextContent("Deliver on $dateTwoDays");
$deliveryMethodRelay->setPrice(299);

// Pick up points
$pickUpPoint1 = new PickUpPoint();
$pickUpPoint1->setId("1");
$pickUpPoint1->setName("Pick-up 1");
$pickUpPoint1->setAddress("Address 1");
$pickUpPoint1->setZipCode($zip);
$pickUpPoint1->setCity($city);
$pickUpPoint1->setCountry($country);
$pickUpPoint1->setDeliveryTextContent("tomorrow");
$pickUpPoint1->setDeliveryTextColor(Color::GREEN());
$pickUpPoint1->setPrice(399);
$pickUpPoint1->setPriceTextColor(Color::RED());

$pickUpPoint2 = new PickUpPoint();
$pickUpPoint2->setId("2");
$pickUpPoint2->setPrimary(true);
$pickUpPoint2->setName("Pick-up 2");
$pickUpPoint2->setAddress("address 2");
$pickUpPoint2->setZipCode($zip);
$pickUpPoint2->setCity($city);
$pickUpPoint2->setCountry($country);

$pickUpPoint3 = new PickUpPoint();
$pickUpPoint3->setId("3");
$pickUpPoint3->setName("Pick-up 3");
$pickUpPoint3->setAddress("Address 3");
$pickUpPoint3->setZipCode($zip);
$pickUpPoint3->setCity($city);
$pickUpPoint3->setCountry($country);

$pickUpPointsRelay = new PickUpPoints();
$pickUpPointsRelay->add($pickUpPoint1);
$pickUpPointsRelay->add($pickUpPoint2);
$pickUpPointsRelay->add($pickUpPoint3);

$deliveryMethodRelay->setPickUpPoints($pickUpPointsRelay);



// And why not getting the order directly in the store
$deliveryMethodCollect = new DeliveryMethod();
$deliveryMethodCollect->setId("store_collect");
$deliveryMethodCollect->setType(Type::COLLECT());
$deliveryMethodCollect->setName("Clic & collect");
$deliveryMethodCollect->setDeliveryTextContent("Available in two hours");
$deliveryMethodCollect->setPrice(0);

// Collect points
$collectPoint1 = new PickUpPoint();
$collectPoint1->setId("1");
$collectPoint1->setName("Store 1");
$collectPoint1->setAddress("Address 1");
$collectPoint1->setZipCode($zip);
$collectPoint1->setCity($city);
$collectPoint1->setCountry($country);

$collectPoint2 = new PickUpPoint();
$collectPoint2->setId("2");
$collectPoint2->setName("Store 2");
$collectPoint2->setAddress("Address 2");
$collectPoint2->setZipCode($zip);
$collectPoint2->setCity($city);
$collectPoint2->setCountry($country);

$collectPoint3 = new PickUpPoint();
$collectPoint3->setId("3");
$collectPoint3->setName("Store 3");
$collectPoint3->setAddress("Address 3");
$collectPoint3->setZipCode($zip);
$collectPoint3->setCity($city);
$collectPoint3->setCountry($country);

$pickUpPointsCollect = new PickUpPoints();
$pickUpPointsCollect->add($collectPoint1);
$pickUpPointsCollect->add($collectPoint2);
$pickUpPointsCollect->add($collectPoint3);

$deliveryMethodCollect->setPickUpPoints($pickUpPointsCollect);



// We add everything to the main object
$deliveryMethods = new DeliveryMethods();
$deliveryMethods->add($deliveryMethodStandard);
$deliveryMethods->add($deliveryMethodRelay);
$deliveryMethods->add($deliveryMethodCollect);

// And we show the json
echo $deliveryMethods->toJson();
``` 

### Get details on completion

When a user logs in or pays with Skeerel, the browser will redirect him 
automatically to your `data-redirect-url` parameter. To retrieve his data, you just have to call the 
following lines

```php
// Verify that the state parameter is the same
if (\Skeerel\Skeerel::verifyAndRemoveSessionStateParameter($_GET['state'])) {
    $skeerel = new \Skeerel\Skeerel('YOUR_WEBSITE_ID', 'YOUR_WEBSITE_SECRET');
    $data = $skeerel->getData($_GET['token']);
}
```

For more information about getting user information, you can look
at the classes under the `Skeerel/Data` directory.

### Get details of a payment

```php
$skeerel = new \Skeerel\Skeerel('YOUR_WEBSITE_ID', 'YOUR_WEBSITE_SECRET');
$payment = $skeerel->getPayment("ce365eec-c287-43b6-ad38-25d8d9029fd1");
```

### List payments

Payments are ordered by date DESC. Last payment appears first

```php
$skeerel = new \Skeerel\Skeerel('YOUR_WEBSITE_ID', 'YOUR_WEBSITE_SECRET');
$payment = $skeerel->listPayments(); // last ten payments
$payment = $skeerel->listPayments(true); // only live payments
$payment = $skeerel->listPayments(true, 15, 20); // list twenty payments starting from the fifteenth index
```

### Refund payment

```php
$skeerel = new \Skeerel\Skeerel('YOUR_WEBSITE_ID', 'YOUR_WEBSITE_SECRET');
$skeerel->refundPayment("32b2fe1a-d987-487b-9fa1-e10964212e76"); // full refund
$skeerel->refundPayment("32b2fe1a-d987-487b-9fa1-e10964212e76", 100); // partial refund (amount is in the currency's smallest unit. Ex 50€ => 5000)
```

### Reviewing a payment

When a payment seems suspect, Skeerel hold the money but does not capture it. 

This way you can review the payment manually and only capture the amount if the payment is legit (thus, avoiding dispute). 
If it is fraudulent you can just reject the payment. If the payment is not rejected within seven days, the payment is automatically canceled.

The following code shows how to capture or reject a payment.

```php
$skeerel = new \Skeerel\Skeerel('YOUR_WEBSITE_ID', 'YOUR_WEBSITE_SECRET');
$skeerel->capturePayment("32b2fe1a-d987-487b-9fa1-e10964212e76"); // payment is legit, capture it
$skeerel->rejectPayment("32b2fe1a-d987-487b-9fa1-e10964212e76"); // payment is fraudulent, reject it
```

### Get details of a website

```php
$skeerel = new \Skeerel\Skeerel('YOUR_WEBSITE_ID', 'YOUR_WEBSITE_SECRET');
$skeerel->getWebsiteDetails();
```

# Sample App

If you'd like to see an example of this library in action, 
check out the Skeerel PHP sample application 
[here](https://github.com/ArcanSecurity/skeerel-sample-php).
 
# Resources
Check out the [API documentation](http://doc.skeerel.com).  
Access your [customer dashboard](https://admin.skeerel.com).