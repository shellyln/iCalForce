# First deploying to Heroku

```bash
$ cd ~/Heroku
$ mkdir myApp
$ cd myApp
$ git init
$ heroku login
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

# Testing / Staging

```bash
$ cd ~/Heroku
$ heroku fork -a appname-on-heroku appname-on-heroku-t131231-001
$ git clone git@heroku.com:appname-on-heroku-t131231-001.git -o heroku
$ cd ./appname-on-heroku-t131231-001
```
and apply new version, edit some files...
```bash
$ git add -A
$ git commit -m "update app (testing #t131231-001)"
$ git push heroku master
```

# Updating app

```bash
$ cd ~/Heroku/myApp
```
and apply new version, edit some files...
```bash
$ git add -A
$ git commit -m "update app"
$ git push heroku master
```

# Fetching app repository to new PC

```bash
$ cd ~/Heroku
$ heroku login
$ git clone git@heroku.com:appname-on-heroku.git -o heroku
$ cd ./appname-on-heroku
$ git status
```

