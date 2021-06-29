<?php

return [
    '100' => 'Proceed.',
    '101' => 'Switching protocols.',
    '200' => 'Good. Client request completed successfully.',
    '201' => 'Created.',
    '202' => 'Accepted.',
    '203' => 'Unauthorized information.',
    '204' => 'Without content.',
    '205' => 'Reset content.',
    '206' => 'Partial content.',
    '207' => 'Multi-Status (WebDay).',
    '301' => 'Moved forever',
    '302' => 'Object moved.',
    '304' => 'Not modified.',
    '307' => 'Temporary redirect.',
    '400' => 'Bad request.',
    '403' => 'Unfortunately for you access is denied, you do not have permission to access this information. Please go to the main page of the site.',
    /**/
    '404' => 'Something went wrong and this page did not open. Do not despair, just go to the main page, it works 100%',
    '404.0' => '(None) - File or directory not found.',
    '404.1' => 'The website is not available on the requested port.',
    '404.2' => 'Web service extension blocking policy prevents this request.',
    '404.3' => 'MIME map policy prevents this request.',
    '405' => 'HTTP verb used to access this page is not allowed (method not allowed).',
    '406' => 'The client browser does not accept the MIME type of the requested page.',
    '407' => 'Proxy Authentication Required.',
    '412' => 'Precondition not met.',
    '413' => 'The request is too large.',
    '414' => 'URI request is too long.',
    '415' => 'Unsupported media type.',
    '416' => 'The requested range is not satisfied.',
    '417' => 'Execution failed.',
    '423' => 'Blocked error.',
    /**/
    '401' => 'Access is denied. You do not have permission to access this page.',
    '401.1' => 'Login failed.',
    '401.2' => 'Login failed due to server configuration.',
    '401.3' => 'Unauthorized due to ACL on resource.',
    '401.4' => 'Filter failed authorization.',
    '401.5' => 'Authorization failed by ISAPI / CGI application.',
    '401.7' => 'Access denied by the URL authorization policy on the web server.',
    /**/
    '500' => 'A server error has occurred, please go to the main page of the site to continue working.',
    '500.12' => 'Application is busy reloading on the web server.',
    '500.13' => 'The web server is too busy.',
    '500.15' => 'Direct requests to Global.asa are not allowed.',
    '500.16' => 'Invalid UNC authorization credentials. This error code only applies to IIS 6.0.',
    '500.18' => 'Unable to open URL authorization store. This error code only applies to IIS 6.0.',
    '500.19' => 'The data for this file is misconfigured in the metabase.',
    '500.100' => 'Internal ASP error.',
    '501' => 'Header values indicate configuration that is not implemented.',
    '502' => 'The web server received an invalid response acting as a gateway or proxy.',
    '502.1' => 'Application timeout CGI.',
    '502.2' => 'Error in CGI application.',
    '503' => 'Service is unavailable. This error code only applies to IIS 6.0.',
    '504' => 'After a long wait, we have not received a response from the server, so we suggest going to the main page of the site',
    '505' => 'HTTP version not supported.',
];