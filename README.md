# cradle-mail
Google Captcha Handling for Cradle

## 1. Requirements

You should be using CradlePHP currently at `dev-master`. See
[https://cradlephp.github.io/](https://cradlephp.github.io/) for more information.

## 2. Install

```
composer require cblanquera/cradle-captcha
```

Then in `/bootstrap.php`, add

```
->register('cblanquera/cradle-captcha')
```

## 3. Setup

Go to [https://www.google.com/recaptcha/](https://www.google.com/recaptcha/) and
register for a token and secret.

Open `/config/services.php` and add

```
'captcha-main' => array(
    'token' => '<Google Token>',
    'secret' => '<Google Secret>'
),
```

## 4. Recipes

Once the database is installed open up `/public/index.php` and add the following.

```
<?php

use Cradle\Framework\Flow;

return cradle()
    //add routes here
    ->get('/captcha/test', 'Captcha Page')
    ->post('/captcha/test', 'Captcha Process')

    //add flows here
    //renders a table display
    ->flow('Captcha Page',
        Flow::captcha()->load,
        Flow::captcha()->render,
        'TODO: form page'
    )
    ->flow('Captcha Process',
        Flow::captcha()->check,
        array(
            Flow::captcha()->yes,
            'TODO: process'
        ),
        array(
            Flow::captcha()->no,
            'TODO: deny'
        )
    );
```
