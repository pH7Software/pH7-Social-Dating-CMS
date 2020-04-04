Clickatell V2 SMS Library
=========================

This library allows integration with the new [Clickatell](https://portal.clickatell.com) website.

**Please Note:** Customers with accounts registered on the old [central.clickatell.com](https://central.clickatell.com) should use the tagged released or dev branches for version 2 of the repository, version 3 and up is for the new platform and older accounts will not work with this.

## Usage

The new APIs only support `sendMessage` call and webhooks for outgoing and inbound messages via a *RESTful* interface.

``` php
use Clickatell\Rest;
use Clickatell\ClickatellException;

$clickatell = new \Clickatell\Rest('token');

// Full list of support parameters can be found at https://www.clickatell.com/developers/api-documentation/rest-api-request-parameters/
try {
    $result = $clickatell->sendMessage(['to' => ['27111111111'], 'content' => 'Message Content']);

    foreach ($result['messages'] as $message) {
        var_dump($message);

        /*
        [
            'apiMsgId'  => null|string,
            'accepted'  => boolean,
            'to'        => string,
            'error'     => null|string
        ]
        */
    }

} catch (ClickatellException $e) {
    // Any API call error will be thrown and should be handled appropriately.
    // The API does not return error codes, so it's best to rely on error descriptions.
    var_dump($e->getMessage());
}
```

### Status/Reply Callback

After configuring your webhooks/callbacks inside the developer portal, you can use the static callback methods to listen for web requests from Clickatell. These callbacks will extract the supported fields from the request body.

``` php
use Clickatell\Rest;
use Clickatell\ClickatellException;

// Outgoing traffic callbacks (MT callbacks)
Rest::parseStatusCallback(function ($result) {
    var_dump($result);
    // This will execute if the request to the web page contains all the values
    // specified by Clickatell. Requests that omit these values will be ignored.
});

// Incoming traffic callbacks (MO/Two Way callbacks)
Rest::parseReplyCallback(function ($result) {
    var_dump($result);
    // This will execute if the request to the web page contains all the values
    // specified by Clickatell. Requests that omit these values will be ignored.
});

```

