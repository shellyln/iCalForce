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

**supported Salesforce/Force.com editions: EE, UE, DE**

----
### contents
  * [Setup](#setup)
  * [Security Enhancement](#security-enhancement)
    * [White-List](#white-list)
    * [Salesforce sharing configurations](#salesforce-sharing-configurations)

----
# Setup

  1. Download iCalForce from https://github.com/qnq777/iCalForce.git .
     ```shell
     $ git clone https://github.com/qnq777/iCalForce.git iCalForce.repo
     ```
     or download Zip file from [here](https://github.com/qnq777/iCalForce/archive/master.zip).

  1. Run setup script.
     ```shell
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
    ```httpd.conf
    SetEnv USERNAME alice@example.com
    SetEnv PASSWORD passSecuritytoken
    SetEnv OWNERID  002i1234567Zz7P
    ```
    **nginx.conf (nginx)**
    ```nginx.conf
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
    ```httpd.conf
    ...
    DocumentRoot "/path/to/iCalForce.repo/iCalForce/public_html"
    <Directory "/path/to/iCalForce.repo/iCalForce/public_html">
        ...
    </Directory>
    ...
    ```
    **nginx.conf (nginx)**
    ```nginx.conf
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

## White-List

### Generate white-list automatically
  1. Add custom checkbox field **"UseICalForce__c"** to **User** standard object.
  
  1. Set **UseICalForce__c** = true if the user is permitted to use this app.

  1. Edit **iCalForce.repo/iCalForce/icalforce/run-create-whitelist.sh**  
     and overwrite USERNAME, PASSWORD.
     ```shell
     #!/bin/bash
     env \
       USERNAME='alice@example.com' \
       PASSWORD='passSecuritytoken' \
       php create-whitelist.php > whitelist.php
     ```

  1. Run command
     ```shell
     $ cd iCalForce.repo/iCalForce/icalforce
     $ bash ./run-create-whitelist.sh
     ```

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

