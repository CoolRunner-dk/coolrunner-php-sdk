# Installation
Composer

```bash
composer require coolrunner/sdk-v3
```

# Usage

Full documentation available in the wiki

## Hinting
Full PhpDoc support should be available for most IDEs

## Instanciation
CoolRunner SDK for API v3 needs to be instantiated before it can be used. 

It is a singleton, and requires your registered email and designated token to be usable.

Get you API token here: [Integration](https://coolrunner.dk/customer/integration/)

If the page is unaccessible please contact our support

```php
$api = CoolRunnerSDK\API::load('<your@email.here>', '<your token here>');
```

## Classes

### Servicepoint
Servicepoint objects contain certain information on the servicepoint it describes

__Properties__:
 - id : _int_  
 Internal ID of the servicepoint
 - name : _string_  
 Internal name of the servicepoint
 - distance : _int_  
 Distance to the servicepoint
 - address : _string|Address_  
 Address of the servicepoint
 - coordinates: _string|Coordinates_  
 Coordinates of the servicepoint
 - opening_hours: _string|OpeningHours_  
 Opening hours of the servicepoint
 
 <sub>_Distance is only available if the servicepoint has been pulled with an origin address_</sub>

__Methods__:  
 - _This class has no methods_
 
### Sub Classes

#### Address
_Address of the servicepoint_

__Properties__:
 - street : _string_  
 Street name
 - zip_code : _string_  
 City zip code
 - city : _string_  
 City name
 - country_code : _string_  
 ISO 3166-1 Alpha-2 format

__Methods__:
 - toString($format = ':street, :country-:zip :city')
    - Return Address in a specified format as a string<br>
    The default format if the object is cast as a string is ':street, :country-:zip :city' 

#### Coordinates
_Coordinates of the servicepoint_

__Properties__:
 - longitude : _float_
 - latitude : _float_
 
__Methods__:
 - _This class has no methods_

#### 

