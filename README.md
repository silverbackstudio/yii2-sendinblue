# Yii2 Framework Sendinblue Mailer and Contacts integration

Provide the following classes:

* **[yii/sendinblue/transactional/Mailer](src/transactional/Mailer.php)**: A class that implements [MailerInterface](https://www.yiiframework.com/doc/api/2.0/yii-mail-mailerinterface) and uses [Sendinblue API v3](https://developers.sendinblue.com/v3.0/reference) to send email
* **[yii/sendinblue/transactional/Message](src/transactional/Message.php)**: A class that implements [MessageInterface](https://www.yiiframework.com/doc/api/2.0/yii-mail-messageinterface) for standard emails

The choice between the two message types is made automatically by the `compose()` method.

## Installation

Install this package in Yii project root with [Composer](https://getcomposer.org/).

`composer require silverback/yii2-sendinblue`

## Setup

Setup the Sendinblue mailer in app config:

```php

    'components' => [
        //...
        
        'mailer' => [
            'class' => 'yii\sendinblue\transactional\Mailer',
            'apikey' => 'your-sedinblue-api-key',
        ],  
        
        //...
    ]
```

## Usage

### Send email using a view (standard Yii behavior)

```php

$viewAttributes = array(
    'attribute1' => 'value1',
);

$message = \Yii::$app->mailer->compose('view-name', $viewAttributes);
$message->setFrom( 'noreply@example.com' );
$message->setSubject( 'Subject' );
$message->setTo( 'user@example.com' );

if ( $message->send() ) {
    echo "Sent successfully";
}

```

### Send email with custom text

```php

$message = \Yii::$app->mailer->compose();
$message->setFrom( 'noreply@example.com' );
$message->setSubject( 'Subject' );
$message->setTo( 'user@example.com' );
$message->setTextBody( 'test content' );

if ( $message->send() ) {
    echo "Sent successfully";
}

```

### Send email with Sendinblue template

```php

$template_id = 1;

$templateAttributes = array(
    'attr1' => 'value1',
    'attr2' => array(
        'subattr1' => 'value2',
        'subattr2' => array(
            'subsubattr1' => 'value2',
        )
    ),
);

// The class uses Sendiblue templates when the view name is an integer instead of string.

$message = \Yii::$app->mailer->compose( $template_id, $templateAttributes );
$message->setTo( 'user@example.com' );

if ( $message->send() ) {
    echo "Sent successfully";
}

```

The following attributes will be available as replacements in the template:

```
%ATTR1%
%ATTR2__SUBATTR1%
%ATTR2__SUBATTR2__SUBSUBATTR1%
```
All attributes will be converted and must be used in uppercase.

## Testing

This class uses [PHPUnit](https://phpunit.de/) as test suite, to test the classes and functions follow this steps.

Copy the file `phpunit.xml.dist` in `phpunit.xml` in the library folder and define Api-Key and addresses inside it:

```xml
	<php>
		<const name="SENDINBLUE_API_KEY" value="{your-key}"/>		
		<const name="SENDINBLUE_TEMPLATE" value="1"/>		
		<const name="SENDINBLUE_FROM" value="from@example.com"/>		
        ...
	</php>
```

Launch a `composer update` to install all the dependencies and test suite.

Run the test with the following commands

```bash
./vendor/bin/phpunit  tests/  # all tests
./vendor/bin/phpunit  tests/TemplateMessageTest # single test
```
