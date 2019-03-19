GoAway AutoCorrect links after rendering html page
===================================

## Default Config

```
[
    'noReplaceLocalDomain'              => true,
    'redirectRoute'                      => '/externallinks/redirect',
    'redirectRouteParam'                 => 'url',
    'enabledB64Encode'                  => true,
    'noReplaceLinksOnDomains'           => [
        'site1.ru',
        'www.site1.ru',
        'site2.ru',
    ],
],
```