# First deploying to Heroku

```bash
$ mkdir myApp
$ cd myApp
$ git init
$ heroku create -s cedar -b git://github.com/iphoting/heroku-buildpack-php-tyler.git#php/5.4.9
$ mkdir conf
```

Download iCalForce as a [zip](https://github.com/qnq777/iCalForce/archive/master.zip) and extract as iCalForce-master.  
Download [nginx.conf.erb](https://github.com/iphoting/heroku-buildpack-php-tyler/blob/php/5.4.9/conf/nginx.conf.erb) and move to ```conf/.```.

```
myApp/
  |
  +- .git/
  +- conf/
  |    |
  |    +- nginx.conf.erb
  +- iCalForce-master
       |
       + iCalForce/
       + README.md
       + LICENSE
```

and edit nginx.conf.erb
```nginx
...
http {
...
  server {
...
    root              /app/iCalForce-master/iCalForce/public_html;
...
```

```bash
$ pwd
/path/to/myApp

$ cd ./iCalForce-master/iCalForce
$ bash setup.sh
$ cd tools
env \
   USERNAME='alice@example.com' \
   PASSWORD='passSecuritytoken' \
   php create-whitelist.php > ../config/whitelist.php

$ cd ../../..
$ pwd
/path/to/myApp

$ git add -A
$ git commit -m "init app"
$ git push heroku master
$ heroku config:add USERNAME=alice@example.com
$ heroku config:add PASSWORD=passSecuritytoken
$ heroku config:add CLIENT_ID=ABCDEFG1234567890
$ heroku config:add CLIENT_SECRET=0123456
```

# Updating app

```bash
$ git add -A
$ git commit -m "init app"
$ git push heroku master
```
