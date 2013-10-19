iCalForce
=========

iCalendar (.ics) exporter for Salesforce/Force.com.  

You can watch Salesforce's "Event" via
Google calendar, Outlook.com, etc... by subscribing calendar URL.  
You can also import calendar to Microsoft Outlook.

**supported client:**
  * Google calendar
  * Outlook.com
  * Microsoft Outlook
  * other ics readable calendar apps (e.g. iCal, tb+lightning, ...)

**supported Salesforce/Force.com editions: EE, UE, DE**

----
### contents
  * [Warning](#warning)
  * [Setup](#setup)
  * [Security Enhancement](#security-enhancement)
    * [Don't set 'OWNERID'](#dont-set-ownerid)
    * [White-List](#white-list)
    * [Salesforce sharing configurations](#salesforce-sharing-configurations)

----
# Warning
  * This app don't have any user auth mechanism.  
    To keep calendar secret, you should the calendar url secret.  
    And if it is suspected that url is leaked, it is necessary to change the url immediately.

# Setup

  1. Download iCalForce from https://github.com/qnq777/iCalForce.git .
     ```bash
     $ git clone https://github.com/qnq777/iCalForce.git iCalForce.repo
     ```
     or download Zip file from [here](https://github.com/qnq777/iCalForce/archive/master.zip).

  1. Run setup script.
     ```bash
     $ cd iCalForce.repo/iCalForce
     $ bash ./setup.sh
     ```
  1. Set server variables
    * **USERNAME** - Salesforce account's login name.
    * **PASSWORD** - Salesforce account's concatenated login pasword + security token.  
      if password is XXXX and security token is YYYY, you should set PASSWORD XXXXYYYY.
    * **OWNERID** - 15 chars ID of User. it is used when url parameter is omitted.
    * **BASEURL** - base url of your org. default value (if you don't set) is  
      https://ap1.salesforce.com

    **httpd.conf (apache)**
    ```apache
    SetEnv USERNAME alice@example.com
    SetEnv PASSWORD passSecuritytoken
    SetEnv OWNERID  002i1234567Zz7P
    ```
    **nginx.conf (nginx)**
    ```nginx
    server {
        ...
        location ~ \.php(/|$) {
            ...
            fastcgi_param USERNAME alice@example.com;
            fastcgi_param PASSWORD passSecuritytoken;
            fastcgi_param OWNERID  002i1234567Zz7P;
        }
        ...
    }
    ```

  1. Set website's root **iCalForce.repo/iCalForce/public_html**.

    **httpd.conf (apache)**
    ```apache
    ...
    DocumentRoot "/path/to/iCalForce.repo/iCalForce/public_html"
    <Directory "/path/to/iCalForce.repo/iCalForce/public_html">
        ...
    </Directory>
    ...
    ```
    **nginx.conf (nginx)**
    ```nginx
    server {
        ...
        root /path/to/iCalForce.repo/iCalForce/public_html;
        ...
    }
    ```

  1. reboot (or reload config) web server.

  1. access the url

    ```
    https://theapp.your.domain.com?u=15charsCaseSensitiveUserId
    ```

# Security Enhancement

To use safely, we recommend that you set the optional security configrations.

## Don't set 'OWNERID'.

You can allow accessing the app root path by setting 'OWNERID'.
```
https://theapp.your.domain.com/
```
It is permanent url and you can't change it.  
By leaking of the url, you can't continue the service if you want to keep the calendar secret.

## White-List

You can restrict access the app to listed users.

### Generate white-list automatically
  1. Add custom checkbox field **"UseICalForce__c"** to **User** standard object.
  
  1. Set **UseICalForce__c** = true if the user is permitted to use this app.

  1. Edit **iCalForce.repo/iCalForce/icalforce/run-create-whitelist.sh**  
     and overwrite USERNAME, PASSWORD.
     ```bash
     #!/bin/bash
     env \
       USERNAME='alice@example.com' \
       PASSWORD='passSecuritytoken' \
       php create-whitelist.php > whitelist.php
     ```

  1. Run command
     ```bash
     $ cd iCalForce.repo/iCalForce/icalforce
     $ bash ./run-create-whitelist.sh
     ```

  1. View whitelist.php

  1. and access the url

    ```
    https://theapp.your.domain.com?t=userPublicToken
    ```
or
    ```
    https://theapp.your.domain.com?u=15charsCaseSensitiveUserId
    ```

**userPublicToken** is written in **whitelist.php** as follows:
```php
  '15charsCaseSensitiveUserId' => array('pub-token' => 'userPublicToken'),
```

**We recomend using 't=userPublicToken' style url.**  
'u=15charsCaseSensitiveUserId' style url is permanent url and you can't change it.  
By leaking of the url, you can't continue the service if you want to keep the calendar secret.

### Reset the user public token
  1. Run command
     ```bash
     $ cd iCalForce.repo/iCalForce/icalforce
     $ env \
       USERNAME='alice@example.com' \
       PASSWORD='passSecuritytoken' \
       php update-whitelist-pubtoken.php 15charsCaseSensitiveUserId > whitelist.php.new
     ```

  1. Replace the whitelist
     ```bash
     $ mv whitelist.php whitelist.php.old
     $ mv whitelist.php.new whitelist.php
     ```

  1. View whitelist.php

  1. and access the url

## Salesforce sharing configurations
We recommend that you create a **dedicated account** for this app  
and configure sharing and access control settings.

  * define dedicated account.
  * define dedicated user profile for the dedicated account.
  * allow api access.
  * limit accessible object by using user profile.  
    you should
    * allow **"View all data"** about User, Event.
    * disallow **"view"** about the others.
    * disallow **"edit/deelete"** all objects.
  * limit accessible fields by defining **field-level security**.

