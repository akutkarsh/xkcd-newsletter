# XKCD NEWSLETTER

A simple PHP application that accepts a visitor's email address and emails them random XKCD comics every five minutes.
The biggest thing I was refreshed on was the importance of learning how to read documentation for different php functions and 

- **No libraries/framework** has been used for the whole app.
- The app is written in **core php**.
- For the front-end web pages, **HTML** and **CSS** is used.
- **MARIADB** is used for the database handling.

## App Structure

```
.
├── README.md
├── cron.txt
├── cronscript.php
├── d.env
├── for_developers
│   └── xkcd_newsletter.sql
├── phpcs.xml
└── public
    ├── class
    │   ├── UIMsg.php
    │   ├── api.php
    │   ├── database.php
    │   └── mailer.php
    ├── index.php
    ├── subscribe.php
    ├── test.php
    ├── unsubscribe.php
    ├── validate.php
    └── views
        ├── UIMsg.css
        ├── comicMail.css
        ├── comicMail.html
        ├── genericError.html
        ├── specificMsg.html
        ├── subscribe.css
        ├── subscribe.html
        ├── unsubscribeMsg.html
        ├── validationMail.css
        └── validationMail.html
```

### What I learned ?

- PHP
- To handle secrets to abstract API keys/passwords (and other important information) away from public facing files. 
- OOPS Implementation in php.
- Using PHP Namespaces and how its affects classes.

### Features

- This app includes email verification to avoid people using other's email addresses.
- Emails contains an unsubscribe link so a user can stop getting emails.

### Application Workflow

1. The visitor visits the site and inputs email.
2. The input email is validated and then passed to database and an email for subscription validation is sent to the visitor. If the email already exists, a message is displayed 'You are already subscribed !'.
3. The user confirms the subscription using the link sent in the validation mail. 
4. The first mail comes in under 5 minutes and the subsequent mail's every five minutes with unsubscribe link. By clicking on the link in the mail user can unsubscribe.

## USAGE 

### Requirement

- [lando](https://docs.lando.dev/getting-started/installation.html)
- docker

### Configuration

Open .lando.yml and change environment variables for database

### Local Deployment
Local Deployment is handled by lando, which is wrapper of docker for managing local deployment.
You can check `.lando.yml` to change different options like volume, environmental variables etc

#### Run
```
git clone https://github.com/akutkarsh/xkcd-newsletter
cd xkcd-newsletter
lando start
```
- Their will be links for different containers access.
- Use phymyadmin and import for_developers/xkcd_newsletter.sql.
- Then you are good to go just open the link that will be like https://xkcd_newsletter.lando.site

#### Helpful Commands
- `lando start ` : Start all the containers.
- `lando stop` : Stop all the containers.
- `lando destroy` : Destory all the containers.
- `lando rebuild` : Rebuild all the containers.

