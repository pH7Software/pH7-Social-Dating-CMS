<?php
/*
 * oauth_client.php
 *
 * @(#) $Id: oauth_client.php,v 1.46 2013/01/10 10:11:33 mlemos Exp $
 *
 */

/*
{metadocument}<?xml version="1.0" encoding="ISO-8859-1" ?>
<class>

    <package>net.manuellemos.oauth</package>

    <version>@(#) $Id: oauth_client.php,v 1.46 2013/01/10 10:11:33 mlemos Exp $</version>
    <copyright>Copyright © (C) Manuel Lemos 2012</copyright>
    <title>OAuth client</title>
    <author>Manuel Lemos</author>
    <authoraddress>mlemos-at-acm.org</authoraddress>

    <documentation>
        <idiom>en</idiom>
        <purpose>This class serves two main purposes:<paragraphbreak />
            1) Implement the OAuth protocol to retrieve a token from a server to
            authorize the access to an API on behalf of the current
            user.<paragraphbreak />
            2) Perform calls to a Web services API using a token previously
            obtained using this class or a token provided some other way by the
            Web services provider.</purpose>
        <usage>Regardless of your purposes, you always need to start calling
            the class <functionlink>Initialize</functionlink> function after
            initializing setup variables. After you are done with the class,
            always call the <functionlink>Finalize</functionlink> function at
            the end.<paragraphbreak />
            This class supports either OAuth protocol versions 1.0, 1.0a and
            2.0. It abstracts the differences between these protocol versions,
            so the class usage is the same independently of the OAuth
            version of the server.<paragraphbreak />
            The class also provides built-in support to several popular OAuth
            servers, so you do not have to manually configure all the details to
            access those servers. Just set the
            <variablelink>server</variablelink> variable to configure the class
            to access one of the built-in supported servers.<paragraphbreak />
            If you need to access one type of server that is not yet directly
            supported by the class, you need to configure it explicitly setting
            the variables: <variablelink>oauth_version</variablelink>,
            <variablelink>url_parameters</variablelink>,
            <variablelink>authorization_header</variablelink>,
            <variablelink>request_token_url</variablelink>,
            <variablelink>dialog_url</variablelink>,
            <variablelink>append_state_to_redirect_uri</variablelink> and
            <variablelink>access_token_url</variablelink>.<paragraphbreak />
            Before proceeding to the actual OAuth authorization process, you
            need to have registered your application with the OAuth server. The
            registration provides you values to set the variables
            <variablelink>client_id</variablelink> and
            <variablelink>client_secret</variablelink>.<paragraphbreak />
            You also need to set the variables
            <variablelink>redirect_uri</variablelink> and
            <variablelink>scope</variablelink> before calling the
            <functionlink>Process</functionlink> function to make the class
            perform the necessary interactions with the OAuth
            server.<paragraphbreak />
            The OAuth protocol involves multiple steps that include redirection
            to the OAuth server. There it asks permission to the current user to
            grant your application access to APIs on his/her behalf. When there
            is a redirection, the class will set the
            <variablelink>exit</variablelink> variable to
            <booleanvalue>1</booleanvalue>. Then your script should exit
            immediately without outputting anything.<paragraphbreak />
            When the OAuth access token is successfully obtained, the following
            variables are set by the class with the obtained values:
            <variablelink>access_token</variablelink>,
            <variablelink>access_token_secret</variablelink>,
            <variablelink>access_token_expiry</variablelink>,
            <variablelink>access_token_type</variablelink>. You may want to
            store these values to use them later when calling the server
            APIs.<paragraphbreak />
            If there was a problem during OAuth authorization process, check the
            variable <variablelink>authorization_error</variablelink> to
            determine the reason.<paragraphbreak />
            Once you get the access token, you can call the server APIs using
            the <functionlink>CallAPI</functionlink> function. Check the
            <variablelink>access_token_error</variablelink> variable to
            determine if there was an error when trying to to call the
            API.<paragraphbreak />
            If for some reason the user has revoked the access to your
            application, you need to ask the user to authorize your application
            again. First you may need to call the function
            <functionlink>ResetAccessToken</functionlink> to reset the value of
            the access token that may be cached in session variables.</usage>
    </documentation>

{/metadocument}
*/

class oauth_client_class
{
/*
{metadocument}
    <variable>
        <name>error</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Store the message that is returned when an error
                occurs.</purpose>
            <usage>Check this variable to understand what happened when a call to
                any of the class functions has failed.<paragraphbreak />
                This class uses cumulative error handling. This means that if one
                class functions that may fail is called and this variable was
                already set to an error message due to a failure in a previous call
                to the same or other function, the function will also fail and does
                not do anything.<paragraphbreak />
                This allows programs using this class to safely call several
                functions that may fail and only check the failure condition after
                the last function call.<paragraphbreak />
                Just set this variable to an empty string to clear the error
                condition.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $error = '';

/*
{metadocument}
    <variable>
        <name>debug</name>
        <type>BOOLEAN</type>
        <value>0</value>
        <documentation>
            <purpose>Control whether debug output is enabled</purpose>
            <usage>Set this variable to <booleanvalue>1</booleanvalue> if you
                need to check what is going on during calls to the class. When
                enabled, the debug output goes either to the variable
                <variablelink>debug_output</variablelink> and the PHP error log.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $debug = false;

/*
{metadocument}
    <variable>
        <name>debug_http</name>
        <type>BOOLEAN</type>
        <value>0</value>
        <documentation>
            <purpose>Control whether the dialog with the remote Web server
                should also be logged.</purpose>
            <usage>Set this variable to <booleanvalue>1</booleanvalue> if you
                want to inspect the data exchange with the OAuth server.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $debug_http = false;

/*
{metadocument}
    <variable>
        <name>exit</name>
        <type>BOOLEAN</type>
        <value>0</value>
        <documentation>
            <purpose>Determine if the current script should be exited.</purpose>
            <usage>Check this variable after calling the
                <functionlink>Process</functionlink> function and exit your script
                immediately if the variable is set to
                <booleanvalue>1</booleanvalue>.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $exit = false;

/*
{metadocument}
    <variable>
        <name>debug_output</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Capture the debug output generated by the class</purpose>
            <usage>Inspect this variable if you need to see what happened during
                the class function calls.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $debug_output = '';

/*
{metadocument}
    <variable>
        <name>debug_prefix</name>
        <type>STRING</type>
        <value>OAuth client: </value>
        <documentation>
            <purpose>Mark the lines of the debug output to identify actions
                performed by this class.</purpose>
            <usage>Change this variable if you prefer the debug output lines to
                be prefixed with a different text.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $debug_prefix = 'OAuth client: ';

/*
{metadocument}
    <variable>
        <name>server</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Identify the type of OAuth server to access.</purpose>
            <usage>The class provides built-in support to several types of OAuth
                servers. This means that the class can automatically initialize
                several configuration variables just by setting this server
                variable.<paragraphbreak />
                Currently it supports the following servers:
                <stringvalue>Bitbucket</stringvalue>,
                <stringvalue>Dropbox</stringvalue>,
                <stringvalue>Facebook</stringvalue>,
                <stringvalue>Fitbit</stringvalue>,
                <stringvalue>Flickr</stringvalue>,
                <stringvalue>Foursquare</stringvalue>,
                <stringvalue>github</stringvalue>,
                <stringvalue>Google</stringvalue>,
                <stringvalue>Instagram</stringvalue>,
                <stringvalue>LinkedIn</stringvalue>,
                <stringvalue>Microsoft</stringvalue>,
                <stringvalue>Scoop.it</stringvalue>,
                <stringvalue>Tumblr</stringvalue>,
                <stringvalue>Twitter</stringvalue> and
                <stringvalue>Yahoo</stringvalue>. Please contact the author if you
                would like to ask to add built-in support for other types of OAuth
                servers.<paragraphbreak />
                If you want to access other types of OAuth servers that are not
                yet supported, set this variable to an empty string and configure
                other variables with values specific to those servers.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $server = '';

/*
{metadocument}
    <variable>
        <name>request_token_url</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>URL of the OAuth server to request the initial token for
                OAuth 1.0 and 1.0a servers.</purpose>
            <usage>Set this variable to the OAuth request token URL when you are
                not accessing one of the built-in supported OAuth
                servers.<paragraphbreak />
                For OAuth 1.0 and 1.0a servers, the request token URL can have
                certain marks that will act as template placeholders which will be
                replaced with given values before requesting the authorization
                token. Currently it supports the following placeholder
                marks:<paragraphbreak />
                {SCOPE} - scope of the requested permissions to the granted by the
                OAuth server with the user permissions</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $request_token_url = '';

/*
{metadocument}
    <variable>
        <name>dialog_url</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>URL of the OAuth server to redirect the browser so the user
                can grant access to your application.</purpose>
            <usage>Set this variable to the OAuth request token URL when you are
                not accessing one of the built-in supported OAuth servers.<paragraphbreak />
                For OAuth 2.0 servers, the dialog URL can have certain marks that
                will act as template placeholders which will be replaced with
                values defined before redirecting the users browser. Currently it
                supports the following placeholder marks:<paragraphbreak />
                {REDIRECT_URI} - URL to redirect when returning from the OAuth
                server authorization page<paragraphbreak />
                {CLIENT_ID} - client application identifier registered at the
                server<paragraphbreak />
                {SCOPE} - scope of the requested permissions to the granted by the
                OAuth server with the user permissions<paragraphbreak />
                {STATE} - identifier of the OAuth session state</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $dialog_url = '';

/*
{metadocument}
    <variable>
        <name>append_state_to_redirect_uri</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Pass the OAuth session state in a variable with a different
                name to work around implementation bugs of certain OAuth
                servers</purpose>
            <usage>Set this variable  when you are not accessing one of the
                built-in supported OAuth servers if the OAuth server has a bug
                that makes it not pass back the OAuth state identifier in a
                request variable named state.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $append_state_to_redirect_uri = '';

/*
{metadocument}
    <variable>
        <name>access_token_url</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>OAuth server URL that will return the access token
                URL.</purpose>
            <usage>Set this variable to the OAuth access token URL when you are
                not accessing one of the built-in supported OAuth servers.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $access_token_url = '';


/*
{metadocument}
    <variable>
        <name>oauth_version</name>
        <type>STRING</type>
        <value>2.0</value>
        <documentation>
            <purpose>Version of the protocol version supported by the OAuth
                server.</purpose>
            <usage>Set this variable to the OAuth server protocol version when
                you are not accessing one of the built-in supported OAuth
                servers.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $oauth_version = '2.0';

/*
{metadocument}
    <variable>
        <name>url_parameters</name>
        <type>BOOLEAN</type>
        <value>0</value>
        <documentation>
            <purpose>Determine if the API call parameters should be moved to the
                call URL.</purpose>
            <usage>Set this variable to <booleanvalue>1</booleanvalue> if the
                API you need to call requires that the call parameters always be
                passed via the API URL.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $url_parameters = false;

/*
{metadocument}
    <variable>
        <name>authorization_header</name>
        <type>BOOLEAN</type>
        <value>1</value>
        <documentation>
            <purpose>Determine if the OAuth parameters should be passed via HTTP
                Authorization request header.</purpose>
            <usage>Set this variable to <booleanvalue>1</booleanvalue> if the
                OAuth server requires that the OAuth parameters be passed using
                the HTTP Authorization instead of the request URI parameters.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $authorization_header = true;

/*
{metadocument}
    <variable>
        <name>redirect_uri</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>URL of the current script page that is calling this
                class</purpose>
            <usage>Set this variable to the current script page URL before
                proceeding the the OAuth authorization process.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $redirect_uri = '';

/*
{metadocument}
    <variable>
        <name>client_id</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Identifier of your application registered with the OAuth
                server</purpose>
            <usage>Set this variable to the application identifier that is
                provided by the OAuth server when you register the
                application.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $client_id = '';

/*
{metadocument}
    <variable>
        <name>client_secret</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Secret value assigned to your application when it is
                registered with the OAuth server.</purpose>
            <usage>Set this variable to the application secret that is provided
                by the OAuth server when you register the application.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $client_secret = '';

/*
{metadocument}
    <variable>
        <name>scope</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Permissions that your application needs to call the OAuth
                server APIs</purpose>
            <usage>Check the documentation of the APIs that your application
                needs to call to set this variable with the identifiers of the
                permissions that the user needs to grant to your application.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $scope = '';

/*
{metadocument}
    <variable>
        <name>access_token</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Access token obtained from the OAuth server</purpose>
            <usage>Check this variable to get the obtained access token upon
                successful OAuth authorization.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $access_token = '';

/*
{metadocument}
    <variable>
        <name>access_token_secret</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Access token secret obtained from the OAuth server</purpose>
            <usage>If the OAuth protocol version is 1.0 or 1.0a, check this
                variable to get the obtained access token secret upon successful
                OAuth authorization.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $access_token_secret = '';

/*
{metadocument}
    <variable>
        <name>access_token_expiry</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Timestamp of the expiry of the access token obtained from
                the OAuth server.</purpose>
            <usage>Check this variable to get the obtained access token expiry
                time upon successful OAuth authorization. If this variable is
                empty, that means no expiry time was set.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $access_token_expiry = '';

/*
{metadocument}
    <variable>
        <name>access_token_type</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Type of access token obtained from the OAuth server.</purpose>
            <usage>Check this variable to get the obtained access token type
                upon successful OAuth authorization.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $access_token_type = '';

/*
{metadocument}
    <variable>
        <name>access_token_error</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Error message returned when a call to the API fails.</purpose>
            <usage>Check this variable to determine if there was an error while
                calling the Web services API when using the
                <functionlink>CallAPI</functionlink> function.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $access_token_error = '';

/*
{metadocument}
    <variable>
        <name>authorization_error</name>
        <type>STRING</type>
        <value></value>
        <documentation>
            <purpose>Error message returned when it was not possible to obtain
                an OAuth access token</purpose>
            <usage>Check this variable to determine if there was an error while
                trying to obtain the OAuth access token.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $authorization_error = '';

/*
{metadocument}
    <variable>
        <name>response_status</name>
        <type>INTEGER</type>
        <value>0</value>
        <documentation>
            <purpose>HTTP response status returned by the server when calling an
                API</purpose>
            <usage>Check this variable after calling the
                <functionlink>CallAPI</functionlink> function if the API calls and you
                need to process the error depending the response status.
                <integervalue>200</integervalue> means no error.
                <integervalue>0</integervalue> means the server response was not
                retrieved.</usage>
        </documentation>
    </variable>
{/metadocument}
*/
    var $response_status = 0;

    var $oauth_user_agent = 'PHP-OAuth-API (http://www.phpclasses.org/oauth-api $Revision: 1.46 $)';
    var $session_started = false;

    Function SetError($error)
    {
        $this->error = $error;
        if($this->debug)
            $this->OutputDebug('Error: '.$error);
        return(false);
    }

    Function SetPHPError($error, &$php_error_message)
    {
        if(IsSet($php_error_message)
        && strlen($php_error_message))
            $error.=": ".$php_error_message;
        return($this->SetError($error));
    }

    Function OutputDebug($message)
    {
        if($this->debug)
        {
            $message = $this->debug_prefix.$message;
            $this->debug_output .= $message."\n";;
            error_log($message);
        }
        return(true);
    }

    Function GetRequestTokenURL(&$request_token_url)
    {
        $request_token_url = $this->request_token_url;
        return(true);
    }

    Function GetDialogURL(&$redirect_url)
    {
        $redirect_url = $this->dialog_url;
        return(true);
    }

    Function GetAccessTokenURL(&$access_token_url)
    {
        $access_token_url = $this->access_token_url;
        return(true);
    }

    Function GetStoredState(&$state)
    {
        if(IsSet($_SESSION['OAUTH_STATE']))
            $state = $_SESSION['OAUTH_STATE'];
        else
            $state = $_SESSION['OAUTH_STATE'] = time().'-'.substr(md5(rand().time()), 0, 6);
        return(true);
    }

    Function GetRequestState(&$state)
    {
        $check = (strlen($this->append_state_to_redirect_uri) ? $this->append_state_to_redirect_uri : 'state');
        $state = (IsSet($_GET[$check]) ? $_GET[$check] : null);
        return(true);
    }

    Function GetRequestCode(&$code)
    {
        $code = (IsSet($_GET['code']) ? $_GET['code'] : null);
        return(true);
    }

    Function GetRequestError(&$error)
    {
        $error = (IsSet($_GET['error']) ? $_GET['error'] : null);
        return(true);
    }

    Function GetRequestDenied(&$denied)
    {
        $denied = (IsSet($_GET['denied']) ? $_GET['denied'] : null);
        return(true);
    }

    Function GetRequestToken(&$token, &$verifier)
    {
        $token = (IsSet($_GET['oauth_token']) ? $_GET['oauth_token'] : null);
        $verifier = (IsSet($_GET['oauth_verifier']) ? $_GET['oauth_verifier'] : null);
        return(true);
    }

    Function GetRedirectURI(&$redirect_uri)
    {
        if(strlen($this->redirect_uri))
            $redirect_uri = $this->redirect_uri;
        else
            $redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        return true;
    }

/*
{metadocument}
    <function>
        <name>StoreAccessToken</name>
        <type>BOOLEAN</type>
        <documentation>
            <purpose>Store the values of the access token when it is succefully
                retrieved from the OAuth server.</purpose>
            <usage>This function is meant to be only be called from inside the
                class. By default it stores access tokens in a session variable
                named <stringvalue>OAUTH_ACCESS_TOKEN</stringvalue>.<paragraphbreak />
                Actual implementations should create a sub-class and override this
                function to make the access token values be stored in other types
                of containers, like for instance databases.</usage>
            <returnvalue>This function should return
                <booleanvalue>1</booleanvalue> if the access token was stored
                successfully.</returnvalue>
        </documentation>
        <argument>
            <name>access_token</name>
            <type>HASH</type>
            <documentation>
                <purpose>Associative array with properties of the access token.
                    The array may have set the following
                    properties:<paragraphbreak />
                    <stringvalue>value</stringvalue>: string value of the access
                        token<paragraphbreak />
                    <stringvalue>authorized</stringvalue>: boolean value that
                        determines if the access token was obtained
                        successfully<paragraphbreak />
                    <stringvalue>expiry</stringvalue>: (optional) timestamp in ISO
                        format relative to UTC time zone of the access token expiry
                        time<paragraphbreak />
                    <stringvalue>type</stringvalue>: (optional) type of OAuth token
                        that may determine how it should be used when sending API call
                        requests.</purpose>
            </documentation>
        </argument>
        <do>
{/metadocument}
*/
    Function StoreAccessToken($access_token)
    {
        $_SESSION['OAUTH_ACCESS_TOKEN'][$this->access_token_url] = $access_token;
        return true;
    }
/*
{metadocument}
        </do>
    </function>
{/metadocument}
*/

/*
{metadocument}
    <function>
        <name>GetAccessToken</name>
        <type>BOOLEAN</type>
        <documentation>
            <purpose>Retrieve the OAuth access token if it was already
                previously stored by the
                <functionlink>StoreAccessToken</functionlink> function.</purpose>
            <usage>This function is meant to be only be called from inside the
                class. By default it retrieves access tokens stored in a session
                variable named
                <stringvalue>OAUTH_ACCESS_TOKEN</stringvalue>.<paragraphbreak />
                Actual implementations should create a sub-class and override this
                function to retrieve the access token values from other types of
                containers, like for instance databases.</usage>
            <returnvalue>This function should return
                <booleanvalue>1</booleanvalue> if the access token was retrieved
                successfully.</returnvalue>
        </documentation>
        <argument>
            <name>access_token</name>
            <type>STRING</type>
            <out />
            <documentation>
                <purpose>Return the properties of the access token in an
                    associative array. If the access token was not yet stored, it
                    returns an empty array. Otherwise, the properties it may return
                    are the same that may be passed to the
                    <functionlink>StoreAccessToken</functionlink>.</purpose>
            </documentation>
        </argument>
        <do>
{/metadocument}
*/
    Function GetAccessToken(&$access_token)
    {
        if(!$this->session_started
        && !session_start())
            return($this->SetPHPError('it was not possible to start the PHP session', $php_error_message));
        $this->session_started = true;
        if(IsSet($_SESSION['OAUTH_ACCESS_TOKEN'][$this->access_token_url]))
            $access_token = $_SESSION['OAUTH_ACCESS_TOKEN'][$this->access_token_url];
        else
            $access_token = array();
        return true;
    }
/*
{metadocument}
        </do>
    </function>
{/metadocument}
*/

/*
{metadocument}
    <function>
        <name>ResetAccessToken</name>
        <type>BOOLEAN</type>
        <documentation>
            <purpose>Reset the access token to a state back when the user has
                not yet authorized the access to the OAuth server API.</purpose>
            <usage>Call this function if for some reason the token to access
                the API was revoked and you need to ask the user to authorize
                the access again.<paragraphbreak />
                By default the class stores and retrieves access tokens in a
                session variable named
                <stringvalue>OAUTH_ACCESS_TOKEN</stringvalue>.<paragraphbreak />
                This function must be called when the user is accessing your site
                pages, so it can reset the information stored in session variables
                that cache the state of a previously retrieved access
                token.<paragraphbreak />
                Actual implementations should create a sub-class and override this
                function to reset the access token state when it is stored in
                other types of containers, like for instance databases.</usage>
            <returnvalue>This function should return
                <booleanvalue>1</booleanvalue> if the access token was resetted
                successfully.</returnvalue>
        </documentation>
        <do>
{/metadocument}
*/
    Function ResetAccessToken()
    {
        if($this->debug)
            $this->OutputDebug('Resetting the access token status for OAuth server located at '.$this->access_token_url);
        if(!$this->session_started
        && !session_start())
            return($this->SetPHPError('it was not possible to start the PHP session', $php_error_message));
        $this->session_started = true;
        if(IsSet($_SESSION['OAUTH_ACCESS_TOKEN'][$this->access_token_url]))
            Unset($_SESSION['OAUTH_ACCESS_TOKEN'][$this->access_token_url]);
        return true;
    }
/*
{metadocument}
        </do>
    </function>
{/metadocument}
*/

    Function Encode($value)
    {
        return(is_array($value) ? $this->EncodeArray($value) : str_replace('%7E', '~', str_replace('+',' ', RawURLEncode($value))));
    }

    Function EncodeArray($array)
    {
        foreach($array as $key => $value)
            $array[$key] = $this->Encode($value);
        return $array;
    }

    Function HMAC($function, $data, $key)
    {
        switch($function)
        {
            case 'sha1':
                $pack = 'H40';
                break;
            default:
                if($this->debug)
                    $this->OutputDebug($function.' is not a supported an HMAC hash type');
                return('');
        }
        if(strlen($key) > 64)
            $key = pack($pack, $function($key));
        if(strlen($key) < 64)
            $key = str_pad($key, 64, "\0");
        return(pack($pack, $function((str_repeat("\x5c", 64) ^ $key).pack($pack, $function((str_repeat("\x36", 64) ^ $key).$data)))));
    }

    Function SendAPIRequest($url, $method, $parameters, $oauth, $options, &$response)
    {
        $this->response_status = 0;
        $http = new http_class;
        $http->debug = ($this->debug && $this->debug_http);
        $http->log_debug = true;
        $http->sasl_authenticate = 0;
        $http->user_agent = $this->oauth_user_agent;
        if($this->debug)
            $this->OutputDebug('Accessing the '.$options['Resource'].' at '.$url);
        $arguments = array();
        $method = strtoupper($method);
        $authorization = '';
        $type = (IsSet($options['RequestContentType']) ? strtolower(trim(strtok($options['RequestContentType'], ';'))) : 'application/x-www-form-urlencoded');
        if(IsSet($oauth))
        {
            $values = array(
                'oauth_consumer_key'=>$this->client_id,
                'oauth_nonce'=>md5(uniqid(rand(), true)),
                'oauth_signature_method'=>'HMAC-SHA1',
                'oauth_timestamp'=>time(),
                'oauth_version'=>'1.0',
            );
            if($this->url_parameters
            && $type === 'application/x-www-form-urlencoded'
            && count($parameters))
            {
                $first = (strpos($url, '?') === false);
                foreach($parameters as $parameter => $value)
                    $url .= ($first ? '?' : '&').UrlEncode($parameter).'='.UrlEncode($value);
                $parameters = array();
            }
            $value_parameters = ($type !== 'application/x-www-form-urlencoded' ? array() : $parameters);
            $values = array_merge($values, $oauth, $value_parameters);
            $uri = strtok($url, '?');
            $sign = $method.'&'.$this->Encode($uri).'&';
            $first = true;
            $sign_values = $values;
            $u = parse_url($url);
            if(IsSet($u['query']))
            {
                parse_str($u['query'], $q);
                foreach($q as $parameter => $value)
                    $sign_values[$parameter] = $value;
            }
            KSort($sign_values);
            foreach($sign_values as $parameter => $value)
            {
                $sign .= $this->Encode(($first ? '' : '&').$parameter.'='.$this->Encode($value));
                $first = false;
            }
            $key = $this->Encode($this->client_secret).'&'.$this->Encode($this->access_token_secret);
            $values['oauth_signature'] = base64_encode($this->HMAC('sha1', $sign, $key));
            if($this->authorization_header)
            {
                $authorization = 'OAuth';
                $first = true;
                foreach($values as $parameter => $value)
                {
                    $authorization .= ($first ? ' ' : ',').$parameter.'="'.$this->Encode($value).'"';
                    $first = false;
                }
            }
            else
            {
                if($method === 'GET')
                {
                    $first = (strcspn($url, '?') == strlen($url));
                    foreach($values as $parameter => $value)
                    {
                        $url .= ($first ? '?' : '&').$parameter.'='.$this->Encode($value);
                        $first = false;
                    }
                    $post_values = array();
                }
                else
                    $post_values = $values;
            }
        }
        if(strlen($error = $http->GetRequestArguments($url, $arguments)))
            return($this->SetError('it was not possible to open the '.$options['Resource'].' URL: '.$error));
        if(strlen($error = $http->Open($arguments)))
            return($this->SetError('it was not possible to open the '.$options['Resource'].' URL: '.$error));
        $arguments['RequestMethod'] = $method;
        switch($type)
        {
            case 'application/x-www-form-urlencoded':
                if(IsSet($options['RequestBody']))
                    return($this->SetError('the request body is defined automatically from the parameters'));
                $arguments['PostValues'] = $parameters;
                break;
            case 'application/json':
                $arguments['Headers']['Content-Type'] = $options['RequestContentType'];
                if(!IsSet($options['RequestBody']))
                {
                    $arguments['Body'] = json_encode($parameters);
                    break;
                }
            default:
                if(!IsSet($options['RequestBody']))
                    return($this->SetError('it was not specified the body value of the of the API call request'));
                $arguments['Headers']['Content-Type'] = $options['RequestContentType'];
                $arguments['Body'] = $options['RequestBody'];
                break;
        }
        $arguments['Headers']['Accept'] = (IsSet($options['Accept']) ? $options['Accept'] : '*/*');
        if(strlen($authorization))
            $arguments['Headers']['Authorization'] = $authorization;
        if(strlen($error = $http->SendRequest($arguments))
        || strlen($error = $http->ReadReplyHeaders($headers)))
        {
            $http->Close();
            return($this->SetError('it was not possible to retrieve the '.$options['Resource'].': '.$error));
        }
        $error = $http->ReadWholeReplyBody($data);
        $http->Close();
        if(strlen($error))
        {
            return($this->SetError('it was not possible to access the '.$options['Resource'].': '.$error));
        }
        $this->response_status = intval($http->response_status);
        $content_type = (IsSet($headers['content-type']) ? strtolower(trim(strtok($headers['content-type'], ';'))) : 'unspecified');
        switch($content_type)
        {
            case 'text/javascript':
            case 'application/json':
                if(!function_exists('json_decode'))
                    return($this->SetError('the JSON extension is not available in this PHP setup'));
                $object = json_decode($data);
                switch(GetType($object))
                {
                    case 'object':
                        if(!IsSet($options['ConvertObjects'])
                        || !$options['ConvertObjects'])
                            $response = $object;
                        else
                        {
                            $response = array();
                            foreach($object as $property => $value)
                                $response[$property] = $value;
                        }
                        break;
                    case 'array':
                        $response = $object;
                        break;
                    default:
                        if(!IsSet($object))
                            return($this->SetError('it was not returned a valid JSON definition of the '.$options['Resource'].' values'));
                        $response = $object;
                        break;
                }
                break;
            case 'application/x-www-form-urlencoded':
            case 'text/plain':
            case 'text/html':
                parse_str($data, $response);
                break;
            default:
                $response = $data;
                break;
        }
        if($this->response_status >= 200
        && $this->response_status < 300)
            $this->access_token_error = '';
        else
        {
            $this->access_token_error = 'it was not possible to access the '.$options['Resource'].': it was returned an unexpected response status '.$http->response_status.' Response: '.$data;
            if($this->debug)
                $this->OutputDebug('Could not retrieve the OAuth access. Error: '.$this->access_token_error);
            if(IsSet($options['FailOnAccessError'])
            && $options['FailOnAccessError'])
            {
                $this->error = $this->access_token_error;
                return false;
            }
        }
        return true;
    }

/*
{metadocument}
    <function>
        <name>CallAPI</name>
        <type>BOOLEAN</type>
        <documentation>
            <purpose>Send a HTTP request to the Web services API using a
                previously obtained authorization token via OAuth.</purpose>
            <usage>This function can be used to call an API after having
                previously obtained an access token through the OAuth protocol
                using the <functionlink>Process</functionlink> function, or by
                directly setting the variables
                <variablelink>access_token</variablelink>, as well as
                <variablelink>access_token_secret</variablelink> in case of using
                OAuth 1.0 or 1.0a services.</usage>
            <returnvalue>This function returns <booleanvalue>1</booleanvalue> if
                the call was done successfully.</returnvalue>
        </documentation>
        <argument>
            <name>url</name>
            <type>STRING</type>
            <documentation>
                <purpose>URL of the API where the HTTP request will be sent.</purpose>
            </documentation>
        </argument>
        <argument>
            <name>method</name>
            <type>STRING</type>
            <documentation>
                <purpose>HTTP method that will be used to send the request. It can
                be <stringvalue>GET</stringvalue>,
                <stringvalue>POST</stringvalue>,
                <stringvalue>DELETE</stringvalue>, <stringvalue>PUT</stringvalue>,
                etc..</purpose>
            </documentation>
        </argument>
        <argument>
            <name>parameters</name>
            <type>HASH</type>
            <documentation>
                <purpose>Associative array with the names and values of the API
                    call request parameters.</purpose>
            </documentation>
        </argument>
        <argument>
            <name>options</name>
            <type>HASH</type>
            <documentation>
                <purpose>Associative array with additional options to configure
                    the request. Currently it supports the following
                    options:<paragraphbreak />
                    <stringvalue>Resource</stringvalue>: string with a label that
                        will be used in the error messages and debug log entries to
                        identify what operation the request is performing. The default
                        value is <stringvalue>API call</stringvalue>.<paragraphbreak />
                    <stringvalue>ConvertObjects</stringvalue>: boolean option that
                        determines if objects should be converted into arrays when the
                        response is returned in JSON format. The default value is
                        <booleanvalue>0</booleanvalue>.<paragraphbreak />
                    <stringvalue>FailOnAccessError</stringvalue>: boolean option
                        that determines if this functions should fail when the server
                        response status is not between 200 and 299. The default value
                        is <booleanvalue>0</booleanvalue>.<paragraphbreak />
                    <stringvalue>Accept</stringvalue>: content type value of the
                        Accept HTTP header to be sent in the API call HTTP request.
                        Some APIs require that a certain value be sent to specify
                        which version of the API is being called. The default value is
                        <stringvalue>*&#47;*</stringvalue>.<paragraphbreak />
                    <stringvalue>RequestContentType</stringvalue>: content type that
                        should be used to send the request values. It can be either
                        <stringvalue>application/x-www-form-urlencoded</stringvalue>
                        for sending values like from Web forms, or
                        <stringvalue>application/json</stringvalue> for sending the
                        values encoded in JSON format. Other types are accepted if the
                        <stringvalue>RequestBody</stringvalue> option is specified.
                        The default value is
                        <stringvalue>application/x-www-form-urlencoded</stringvalue>.<paragraphbreak />
                    <stringvalue>RequestBody</stringvalue>: request body data of a
                        custom type. The <stringvalue>RequestContentType</stringvalue>
                        option must be specified, so the
                        <stringvalue>RequestBody</stringvalue> option is considered.</purpose>
            </documentation>
        </argument>
        <argument>
            <name>response</name>
            <type>STRING</type>
            <out />
            <documentation>
                <purpose>Return the value of the API response. If the value is
                    JSON encoded, this function will decode it and return the value
                    converted to respective types. If the value is form encoded,
                    this function will decode the response and return it as an
                    array. Otherwise, the class will return the value as a
                    string.</purpose>
            </documentation>
        </argument>
        <do>
{/metadocument}
*/
    Function CallAPI($url, $method, $parameters, $options, &$response)
    {
        if(!IsSet($options['Resource']))
            $options['Resource'] = 'API call';
        if(!IsSet($options['ConvertObjects']))
            $options['ConvertObjects'] = false;
        switch(intval($this->oauth_version))
        {
            case 1:
                $oauth = array(
                    'oauth_token'=>$this->access_token
                );
                break;

            case 2:
                $oauth = null;
                $url .= (strcspn($url, '?') < strlen($url) ? '&' : '?').'access_token='.UrlEncode($this->access_token);
                break;

            default:
                return($this->SetError($this->oauth_version.' is not a supported version of the OAuth protocol'));
        }
        return($this->SendAPIRequest($url, $method, $parameters, $oauth, $options, $response));
    }
/*
{metadocument}
        </do>
    </function>
{/metadocument}
*/

/*
{metadocument}
    <function>
        <name>Initialize</name>
        <type>BOOLEAN</type>
        <documentation>
            <purpose>Initialize the class variables and internal state. It must
                be called before calling other class functions.</purpose>
            <usage>Set the <variablelink>server</variablelink> variable before
                calling this function to let it initialize the class variables to
                work with the specified server type. Alternatively, you can set
                other class variables manually to make it work with servers that
                are not yet built-in supported.</usage>
            <returnvalue>This function returns <booleanvalue>1</booleanvalue> if
                it was able to successfully initialize the class for the specified
                server type.</returnvalue>
        </documentation>
        <do>
{/metadocument}
*/
    Function Initialize()
    {
        switch($this->server)
        {
            case '';
                break;

            case 'Bitbucket':
                $this->oauth_version = '1.0a';
                $this->request_token_url = 'https://bitbucket.org/!api/1.0/oauth/request_token';
                $this->dialog_url = 'https://bitbucket.org/!api/1.0/oauth/authenticate';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'https://bitbucket.org/!api/1.0/oauth/access_token';
                $this->authorization_header = true;
                $this->url_parameters = true;
                break;

            case 'Dropbox':
                $this->oauth_version = '1.0';
                $this->request_token_url = 'https://api.dropbox.com/1/oauth/request_token';
                $this->dialog_url = 'https://www.dropbox.com/1/oauth/authorize';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'https://api.dropbox.com/1/oauth/access_token';
                $this->authorization_header = false;
                $this->url_parameters = false;
                break;

            case 'Facebook':
                $this->oauth_version = '2.0';
                $this->request_token_url = '';
                $this->dialog_url = 'https://www.facebook.com/dialog/oauth?client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}&scope={SCOPE}&state={STATE}';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'https://graph.facebook.com/oauth/access_token';
                $this->authorization_header = true;
                $this->url_parameters = false;
                break;

            case 'Fitbit':
                $this->oauth_version = '1.0a';
                $this->request_token_url = 'http://api.fitbit.com/oauth/request_token';
                $this->dialog_url = 'http://api.fitbit.com/oauth/authorize';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'http://api.fitbit.com/oauth/access_token';
                $this->authorization_header = true;
                $this->url_parameters = false;
                break;

            case 'Flickr':
                $this->oauth_version = '1.0a';
                $this->request_token_url = 'http://www.flickr.com/services/oauth/request_token';
                $this->dialog_url = 'http://www.flickr.com/services/oauth/authorize';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'http://www.flickr.com/services/oauth/access_token';
                $this->authorization_header = false;
                $this->url_parameters = false;
                break;

            case 'Foursquare':
                $this->oauth_version = '2.0';
                $this->request_token_url = '';
                $this->dialog_url = 'https://foursquare.com/oauth2/authorize?client_id={CLIENT_ID}&scope={SCOPE}&response_type=code&redirect_uri={REDIRECT_URI}&state={STATE}';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'https://foursquare.com/oauth2/access_token';
                $this->authorization_header = true;
                $this->url_parameters = false;
                break;

            case 'github':
                $this->oauth_version = '2.0';
                $this->request_token_url = '';
                $this->dialog_url = 'https://github.com/login/oauth/authorize?client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}&scope={SCOPE}&state={STATE}';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'https://github.com/login/oauth/access_token';
                $this->authorization_header = true;
                $this->url_parameters = false;
                break;

            case 'Google':
                $this->oauth_version = '2.0';
                $this->request_token_url = '';
                $this->dialog_url = 'https://accounts.google.com/o/oauth2/auth?response_type=code&client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}&scope={SCOPE}&state={STATE}';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'https://accounts.google.com/o/oauth2/token';
                $this->authorization_header = true;
                $this->url_parameters = false;
                break;

            case 'Instagram':
                $this->oauth_version = '2.0';
                $this->request_token_url = '';
                $this->dialog_url ='https://api.instagram.com/oauth/authorize/?client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}&scope={SCOPE}&response_type=code&state={STATE}';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'https://api.instagram.com/oauth/access_token';
                $this->authorization_header = true;
                $this->url_parameters = false;
                break;

            case 'LinkedIn':
                $this->oauth_version = '1.0a';
                $this->request_token_url = 'https://api.linkedin.com/uas/oauth/requestToken?scope={SCOPE}';
                $this->dialog_url = 'https://api.linkedin.com/uas/oauth/authenticate';
                $this->access_token_url = 'https://api.linkedin.com/uas/oauth/accessToken';
                $this->append_state_to_redirect_uri = '';
                $this->authorization_header = true;
                $this->url_parameters = true;
                break;

            case 'Microsoft':
                $this->oauth_version = '2.0';
                $this->request_token_url = '';
                $this->dialog_url = 'https://login.live.com/oauth20_authorize.srf?client_id={CLIENT_ID}&scope={SCOPE}&response_type=code&redirect_uri={REDIRECT_URI}&state={STATE}';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'https://login.live.com/oauth20_token.srf';
                $this->authorization_header = true;
                $this->url_parameters = false;
                break;

            case 'Scoop.it':
                $this->oauth_version = '1.0a';
                $this->request_token_url = 'https://www.scoop.it/oauth/request';
                $this->dialog_url = 'https://www.scoop.it/oauth/authorize';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'https://www.scoop.it/oauth/access';
                $this->authorization_header = false;
                $this->url_parameters = false;
                break;

            case 'Tumblr':
                $this->oauth_version = '1.0a';
                $this->request_token_url = 'http://www.tumblr.com/oauth/request_token';
                $this->dialog_url = 'http://www.tumblr.com/oauth/authorize';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'http://www.tumblr.com/oauth/access_token';
                $this->authorization_header = true;
                $this->url_parameters = false;
                break;

            case 'Twitter':
                $this->oauth_version = '1.0a';
                $this->request_token_url = 'https://api.twitter.com/oauth/request_token';
                $this->dialog_url = 'https://api.twitter.com/oauth/authenticate';
                $this->append_state_to_redirect_uri = '';
                $this->access_token_url = 'https://api.twitter.com/oauth/access_token';
                $this->authorization_header = true;
                $this->url_parameters = true;
                break;

            case 'Yahoo':
                $this->oauth_version = '1.0a';
                $this->request_token_url = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
                $this->dialog_url = 'https://api.login.yahoo.com/oauth/v2/request_auth';
                $this->access_token_url = 'https://api.login.yahoo.com/oauth/v2/get_token';
                $this->append_state_to_redirect_uri = '';
                $this->authorization_header = false;
                $this->url_parameters = false;
                break;

            default:
                return($this->SetError($this->server.' is not yet a supported type of OAuth server. Please contact the author Manuel Lemos <mlemos@acm.org> to request adding built-in support to this type of OAuth server.'));
        }
        return(true);
    }
/*
{metadocument}
        </do>
    </function>
{/metadocument}
*/

/*
{metadocument}
    <function>
        <name>Process</name>
        <type>BOOLEAN</type>
        <documentation>
            <purpose>Process the OAuth protocol interaction with the OAuth
                server.</purpose>
            <usage>Call this function when you need to retrieve the OAuth access
                token. Check the <variablelink>access_token</variablelink> to
                determine if the access token was obtained successfully.</usage>
            <returnvalue>This function returns <booleanvalue>1</booleanvalue> if
                the OAuth protocol was processed without errors.</returnvalue>
        </documentation>
        <do>
{/metadocument}
*/
    Function Process()
    {
        switch(intval($this->oauth_version))
        {
            case 1:
                $one_a = ($this->oauth_version === '1.0a');
                if($this->debug)
                    $this->OutputDebug('Checking the OAuth token authorization state');
                if(!$this->GetAccessToken($access_token))
                    return false;
                if(IsSet($access_token['authorized'])
                && IsSet($access_token['value']))
                {
                    $expired = (IsSet($access_token['expiry']) && strcmp($access_token['expiry'], gmstrftime('%Y-%m-%d %H:%M:%S')) <= 0);
                    if(!$access_token['authorized']
                    || $expired)
                    {
                        if($this->debug)
                        {
                            if($expired)
                                $this->OutputDebug('The OAuth token expired on '.$access_token['expiry'].'UTC');
                            else
                                $this->OutputDebug('The OAuth token is not yet authorized');
                            $this->OutputDebug('Checking the OAuth token and verifier');
                        }
                        if(!$this->GetRequestToken($token, $verifier))
                            return false;
                        if(!IsSet($token)
                        || ($one_a
                        && !IsSet($verifier)))
                        {
                            if(!$this->GetRequestDenied($denied))
                                return false;
                            if(IsSet($denied)
                            && $denied === $access_token['value'])
                            {
                                if($this->debug)
                                    $this->OutputDebug('The authorization request was denied');
                                $this->authorization_error = 'the request was denied';
                                return true;
                            }
                            else
                            {
                                if($this->debug)
                                    $this->OutputDebug('Reset the OAuth token state because token and verifier are not both set');
                                $access_token = array();
                            }
                        }
                        elseif($token !== $access_token['value'])
                        {
                            if($this->debug)
                                $this->OutputDebug('Reset the OAuth token state because token does not match what as previously retrieved');
                            $access_token = array();
                        }
                        else
                        {
                            if(!$this->GetAccessTokenURL($url))
                                return false;
                            $oauth = array(
                                'oauth_token'=>$token,
                            );
                            if($one_a)
                                $oauth['oauth_verifier'] = $verifier;
                            $this->access_token_secret = $access_token['secret'];
                            if(!$this->SendAPIRequest($url, 'GET', array(), $oauth, array('Resource'=>'OAuth access token'), $response))
                                return false;
                            if(strlen($this->access_token_error))
                            {
                                $this->authorization_error = $this->access_token_error;
                                return true;
                            }
                            if(!IsSet($response['oauth_token'])
                            || !IsSet($response['oauth_token_secret']))
                            {
                                $this->authorization_error= 'it was not returned the access token and secret';
                                return true;
                            }
                            $access_token = array(
                                'value'=>$response['oauth_token'],
                                'secret'=>$response['oauth_token_secret'],
                                'authorized'=>true
                            );
                            if(IsSet($response['oauth_expires_in']))
                            {
                                $expires = $response['oauth_expires_in'];
                                if(strval($expires) !== strval(intval($expires))
                                || $expires <= 0)
                                    return($this->SetError('OAuth server did not return a supported type of access token expiry time'));
                                $this->access_token_expiry = gmstrftime('%Y-%m-%d %H:%M:%S', time() + $expires);
                                if($this->debug)
                                    $this->OutputDebug('Access token expiry: '.$this->access_token_expiry.' UTC');
                                $access_token['expiry'] = $this->access_token_expiry;
                            }
                            else
                                $this->access_token_expiry = '';

                            if(!$this->StoreAccessToken($access_token))
                                return false;
                            if($this->debug)
                                $this->OutputDebug('The OAuth token was authorized');
                        }
                    }
                    elseif($this->debug)
                        $this->OutputDebug('The OAuth token was already authorized');
                    if(IsSet($access_token['authorized'])
                    && $access_token['authorized'])
                    {
                        $this->access_token = $access_token['value'];
                        $this->access_token_secret = $access_token['secret'];
                        return true;
                    }
                }
                else
                {
                    if($this->debug)
                        $this->OutputDebug('The OAuth access token is not set');
                    $access_token = array();
                }
                if(!IsSet($access_token['authorized']))
                {
                    if($this->debug)
                        $this->OutputDebug('Requesting the unauthorized OAuth token');
                    if(!$this->GetRequestTokenURL($url))
                        return false;
                    $url = str_replace('{SCOPE}', UrlEncode($this->scope), $url);
                    if(!$this->GetRedirectURI($redirect_uri))
                        return false;
                    $oauth = array(
                        'oauth_callback'=>$redirect_uri,
                    );
                    if(!$this->SendAPIRequest($url, 'GET', array(), $oauth, array('Resource'=>'OAuth request token'), $response))
                        return false;
                    if(strlen($this->access_token_error))
                    {
                        $this->authorization_error = $this->access_token_error;
                        return true;
                    }
                    if(!IsSet($response['oauth_token'])
                    || !IsSet($response['oauth_token_secret']))
                    {
                        $this->authorization_error = 'it was not returned the requested token';
                        return true;
                    }
                    $access_token = array(
                        'value'=>$response['oauth_token'],
                        'secret'=>$response['oauth_token_secret'],
                        'authorized'=>false
                    );
                    if(!$this->StoreAccessToken($access_token))
                        return false;
                }
                if(!$this->GetDialogURL($url))
                    return false;
                $url .= '?oauth_token='.$access_token['value'];
                if(!$one_a)
                {
                    if(!$this->GetRedirectURI($redirect_uri))
                        return false;
                    $url .= '&oauth_callback='.UrlEncode($redirect_uri);
                }
                if($this->debug)
                    $this->OutputDebug('Redirecting to OAuth authorize page '.$url);
                Header('HTTP/1.0 302 OAuth Redirection');
                Header('Location: '.$url);
                $this->exit = true;
                return true;

            case 2:
                if($this->debug)
                    $this->OutputDebug('Checking if OAuth access token was already retrieved from '.$this->access_token_url);
                if(!$this->GetAccessToken($access_token))
                    return false;
                if(IsSet($access_token['value']))
                {
                    if(IsSet($access_token['expiry'])
                    && strcmp($this->access_token_expiry = $access_token['expiry'], gmstrftime('%Y-%m-%d %H:%M:%S')) < 0)
                    {
                        if($this->debug)
                            $this->OutputDebug('The OAuth access token expired in '.$this->access_token_expiry);
                    }
                    else
                    {
                        $this->access_token = $access_token['value'];
                        if(IsSet($access_token['type']))
                            $this->access_token_type = $access_token['type'];
                        if($this->debug)
                            $this->OutputDebug('The OAuth access token '.$this->access_token.' is valid');
                        if(strlen($this->access_token_type)
                        && $this->debug)
                            $this->OutputDebug('The OAuth access token is of type '.$this->access_token_type);
                        return true;
                    }
                }
                if($this->debug)
                    $this->OutputDebug('Checking the authentication state in URI '.$_SERVER['REQUEST_URI']);
                if(!$this->GetStoredState($stored_state))
                    return false;
                if(strlen($stored_state) == 0)
                    return($this->SetError('it was not set the OAuth state'));
                if(!$this->GetRequestState($state))
                    return false;
                if($state === $stored_state)
                {
                    if($this->debug)
                        $this->OutputDebug('Checking the authentication code');
                    if(!$this->GetRequestCode($code))
                        return false;
                    if(strlen($code) == 0)
                    {
                        if(!$this->GetRequestError($this->authorization_error))
                            return false;
                        if(IsSet($this->authorization_error))
                        {
                            if($this->debug)
                                $this->OutputDebug('Authorization failed with error code '.$this->authorization_error);
                            switch($this->authorization_error)
                            {
                                case 'invalid_request':
                                case 'unauthorized_client':
                                case 'access_denied':
                                case 'unsupported_response_type':
                                case 'invalid_scope':
                                case 'server_error':
                                case 'temporarily_unavailable':
                                case 'user_denied':
                                    return true;
                                default:
                                    return($this->SetError('it was returned an unknown OAuth error code'));
                            }
                        }
                        return($this->SetError('it was not returned the OAuth dialog code'));
                    }
                    if(!$this->GetAccessTokenURL($url))
                        return false;
                    if(!$this->GetRedirectURI($redirect_uri))
                        return false;
                    $values = array(
                        'code'=>$code,
                        'client_id'=>$this->client_id,
                        'client_secret'=>$this->client_secret,
                        'redirect_uri'=>$redirect_uri,
                        'grant_type'=>'authorization_code'
                    );
                    if(!$this->SendAPIRequest($url, 'POST', $values, null, array('Resource'=>'OAuth access token', 'ConvertObjects'=>true), $response))
                        return false;
                    if(strlen($this->access_token_error))
                    {
                        $this->authorization_error = $this->access_token_error;
                        return true;
                    }
                    if(!IsSet($response['access_token']))
                    {
                        if(IsSet($response['error']))
                        {
                            $this->authorization_error = 'it was not possible to retrieve the access token: it was returned the error: '.$response['error'];
                            return true;
                        }
                        return($this->SetError('OAuth server did not return the access token'));
                    }
                    $access_token = array(
                        'value'=>$this->access_token = $response['access_token'],
                        'authorized'=>true
                    );
                    if($this->debug)
                        $this->OutputDebug('Access token: '.$this->access_token);
                    if(IsSet($response['expires'])
                    || IsSet($response['expires_in']))
                    {
                        $expires = (IsSet($response['expires']) ? $response['expires'] : $response['expires_in']);
                        if(strval($expires) !== strval(intval($expires))
                        || $expires <= 0)
                            return($this->SetError('OAuth server did not return a supported type of access token expiry time'));
                        $this->access_token_expiry = gmstrftime('%Y-%m-%d %H:%M:%S', time() + $expires);
                        if($this->debug)
                            $this->OutputDebug('Access token expiry: '.$this->access_token_expiry.' UTC');
                        $access_token['expiry'] = $this->access_token_expiry;
                    }
                    else
                        $this->access_token_expiry = '';
                    if(IsSet($response['token_type']))
                    {
                        $this->access_token_type = $response['token_type'];
                        if($this->debug)
                            $this->OutputDebug('Access token type: '.$this->access_token_type);
                        $access_token['type'] = $this->access_token_type;
                    }
                    else
                        $this->access_token_type = '';
                    if(!$this->StoreAccessToken($access_token))
                        return false;
                }
                else
                {
                    if(!$this->GetDialogURL($url))
                        return false;
                    if(strlen($url) == 0)
                        return($this->SetError('it was not set the OAuth dialog URL'));
                    if(!$this->GetRedirectURI($redirect_uri))
                        return false;
                    if(strlen($this->append_state_to_redirect_uri))
                        $redirect_uri .= (strpos($redirect_uri, '?') === false ? '?' : '&').$this->append_state_to_redirect_uri.'='.$stored_state;
                    $url = str_replace(
                        '{REDIRECT_URI}', UrlEncode($redirect_uri), str_replace(
                        '{CLIENT_ID}', UrlEncode($this->client_id), str_replace(
                        '{SCOPE}', UrlEncode($this->scope), str_replace(
                        '{STATE}', UrlEncode($stored_state),
                        $url))));
                    if($this->debug)
                        $this->OutputDebug('Redirecting to OAuth Dialog '.$url);
                    Header('HTTP/1.0 302 OAuth Redirection');
                    Header('Location: '.$url);
                    $this->exit = true;
                }
                break;

            default:
                return($this->SetError($this->oauth_version.' is not a supported version of the OAuth protocol'));
        }
        return(true);
    }
/*
{metadocument}
        </do>
    </function>
{/metadocument}
*/

/*
{metadocument}
    <function>
        <name>Finalize</name>
        <type>BOOLEAN</type>
        <documentation>
            <purpose>Cleanup any resources that may have been used during the
                OAuth protocol processing or execution of API calls.</purpose>
            <usage>Always call this function as the last step after calling the
                functions <functionlink>Process</functionlink> or
                <functionlink>CallAPI</functionlink>.</usage>
            <returnvalue>This function returns <booleanvalue>1</booleanvalue> if
                the function cleaned up any resources successfully.</returnvalue>
        </documentation>
        <argument>
            <name>success</name>
            <type>BOOLEAN</type>
            <documentation>
                <purpose>Pass the last success state returned by the class or any
                    external code processing the class function results.</purpose>
            </documentation>
        </argument>
        <do>
{/metadocument}
*/
    Function Finalize($success)
    {
        return($success);
    }
/*
{metadocument}
        </do>
    </function>
{/metadocument}
*/

/*
{metadocument}
    <function>
        <name>Output</name>
        <type>VOID</type>
        <documentation>
            <purpose>Display the results of the OAuth protocol processing.</purpose>
            <usage>Only call this function if you are debugging the OAuth
                authorization process and you need to view what was its
                results.</usage>
        </documentation>
        <do>
{/metadocument}
*/
    Function Output()
    {
        if(strlen($this->authorization_error)
        || strlen($this->access_token_error)
        || strlen($this->access_token))
        {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>OAuth client result</title>
</head>
<body>
<h1>OAuth client result</h1>
<?php
            if(strlen($this->authorization_error))
            {
?>
<p>It was not possible to authorize the application.<?php
                if($this->debug)
                {
?>
<br>Authorization error: <?php echo HtmlSpecialChars($this->authorization_error);
                }
?></p>
<?php
            }
            elseif(strlen($this->access_token_error))
            {
?>
<p>It was not possible to use the application access token.
<?php
                if($this->debug)
                {
?>
<br>Error: <?php echo HtmlSpecialChars($this->access_token_error);
                }
?></p>
<?php
            }
            elseif(strlen($this->access_token))
            {
?>
<p>The application authorization was obtained successfully.
<?php
                if($this->debug)
                {
?>
<br>Access token: <?php echo HtmlSpecialChars($this->access_token);
                    if(IsSet($this->access_token_secret))
                    {
?>
<br>Access token secret: <?php echo HtmlSpecialChars($this->access_token_secret);
                    }
                }
?></p>
<?php
                if(strlen($this->access_token_expiry))
                {
?>
<p>Access token expiry: <?php echo $this->access_token_expiry; ?> UTC</p>
<?php
                }
            }
?>
</body>
</html>
<?php
        }
    }
/*
{metadocument}
        </do>
    </function>
{/metadocument}
*/

};

/*

{metadocument}
</class>
{/metadocument}

*/

?>
