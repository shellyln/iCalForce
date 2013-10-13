iCalForce
=========

iCalendar (.ics) exporter for Salesforce/Force.com. You can watch Salesforce's "Event" via Google calendar, Outlook.com,...

# Setup

  1. Download iCalForce from https://github.com/qnq777/iCalForce.git/iCalForce.
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
      if password is XXXX and security token is YYYY, you should set PASSWORD xxxxyyyy.
    * **OWNERID** - 15 chars ID of User. it is used when url parameter is omitted.
    * **BASEURL** - base url of your org. default value (if you don't set) is  
      https://ap1.salesforce.com

    httpd.conf (apache)
    ```httpd.conf
    SetEnv USERNAME alice@example.com
    SetEnv PASSWORD passSecuritytoken
    SetEnv OWNERID  002i1234567Zz7P
    ```
    nginx.conf (nginx)
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

    nginx.conf (nginx)
    ```nginx.conf
    server {
        ...
        root /path/to/iCalForce.repo/iCalForce/public_html;
        ...
    }
    ```

  1. a

# Security Enhancement
