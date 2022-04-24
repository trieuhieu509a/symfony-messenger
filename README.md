New features in the next major Symfony release (5)
New features in the next major Symfony release (5.0 version)


Surprise!

According to the Symfony documentation, there are no any new features, breaking changes in Symfony 5.0 compared to Symfony 4.4. Only deprecated code was removed.

Symfony 5.1 is expected to have new features (release date May 2020). Weird but it seems like Symfony standard. See this link for more details: https://symfony.com/blog/category/living-on-the-edge

See also this link: https://symfony.com/releases to see what is the latest stable Symfony version recommended for most users.



Below I will only summarize the content from those links. I will list only the changes that are most related to the applications we created in the course and which may require some actions from you. The other things are just improvements, better security actions, improved visibility of something etc., that basically you do not need to care about much. But I recommend to read the content from the links above.



So let's get started:



What's new in Symfony 4.2
Ability to clear form errors after checking that the form is valid

$task = ...;
$form = $this->createForm(TaskType::class, $task);
// ...
// check if sent form is valid
// then you may if you want:
$form->clearErrors();
$form->clearErrors(true); // this removes errors from the form and all its children forms




New component -  VarExporter

Example:

$data = array(123, array('abc'));
$result = VarExporter::export($data); // result similar to var_dump function but returns valid  php code and is better formatted than native php var_export function




Binding services by type and name at the same time, not mandatory

# config/services.yaml
services:
    _defaults:
        bind:
            string $adminEmail: 'manager@example.com' # added string type to the $adminEmail name
            Psr\Log\LoggerInterface $requestLogger: '@monolog.logger.request' # added $requestLogger name to the Psr\Log\LoggerInterface type




New constraints

    /**
     * @Assert\DivisibleBy(0.25)
     */
    protected $weight; // 0.5 is valid, also 1 but 1.3 is invalid
    /**
     * @Assert\DivisibleBy(
     *     value = 5,
     *     message = "This item requires to be stocked in multiples of 5 units."
     * )
     */
    protected $quantity;




Simpler functional tests

// Before

$client->request('GET', '/');
$link = $crawler->selectLink('Login')->link();
$crawler = $client->click($link);
// After

$client->request('GET', '/');
$crawler = $client->clickLink('Login');




// Before

$client->request('GET', '/register');
$form = $crawler->selectButton('Sign Up')->form();
$crawler = $client->submit($form, [
    'name' => 'Jane Doe',
    'username' => 'jane',
    'password' => 'my safe password',
]);
// After

$client->request('GET', '/register');
$crawler = $client->submitForm('Sign Up', [
    'name' => 'Jane Doe',
    'username' => 'jane',
    'password' => 'my safe password',
]);




What's new in Symfony 4.3
New component -  Mailer Component

Out of the box this component provides support for the most popular services: Amazon SES, MailChimp, Mailgun, Gmail, Postmark and SendGrid. They are installed separately, so if your app uses for example Amazon SES, run this command:

composer require symfony/amazon-mailer





Automatic extract translations string and save to translation file

Example

php bin/console translation:update --dump-messages --force fr

As explained in the docs, the main limitation of this command is that it can only extract contents from templates. In Symfony 4.3, Symfony improved this command to also extract translation contents from PHP files, such as controllers and services.





Better security

# config/packages/security.yaml
security:
    # ...
    encoders:
        App\Entity\User:
-            algorithm: 'bcrypt'
-            algorithm: 'argon2i'
-            algorithm: 'sodium' # added in 4.3. This new encoder relies on libsodium to select the best possible Argon2 variant
+            algorithm: 'auto'   # or you can just type 'auto' to let Symfony choose the best encoder for you






New component - HttpClient

Making HTTP requests (e.g. to third-party APIs) is a frequent need for developers working on web applications

Example

use Symfony\Component\HttpClient\HttpClient;
$httpClient = HttpClient::create();
$response = $httpClient->request('GET', 'https://api.github.com/repos/symfony/symfony-docs');
$statusCode = $response->getStatusCode();
// $statusCode = 200
$content = $response->getContent();
// returns the raw content returned by the server (JSON in this case)
// $content = '{"id":521583, "name":"symfony-docs", ...}'
$content = $response->toArray();
// transforms the response JSON content into a PHP array
// $content = ['id' => 521583, 'name' => 'symfony-docs', ...]






Added failed transport in Symfony Messenger Component

In the following example, if the ampq transport fails, doctrine transport will be used

framework:
    messenger:
        failure_transport: failed  # added in Symfony 4.3
 
        transports:
            async:
                dsn: 'amqp://'
            failed: # 4.3
                dsn: 'doctrine://default?queue_name=failed'
 
        routing:
            'App\Message\SmsNotification': async






New assertions

Positive, PositiveOrZero, Negative and NegativeOrZero

Examples

PositiveOrZero

/** @Assert\PositiveOrZero */
    protected $siblings;




NotCompromisedPassword

 /**
     * @Assert\NotCompromisedPassword
     */
 protected $rawPassword; // Symfony makes internal api call to service that checks if password has not been compromised, it compares sha1 hash of the password for example. Users that set their password to any of the publicly exposed passwords are a serious security problem for web sites and applications.




// Before

$this->assertSame(200, $client->getResponse()->getStatusCode());

// After

$this->assertResponseIsSuccessful();
see for full list https://symfony.com/blog/new-in-symfony-4-3-better-test-assertions





** @ORM\Entity */
class SomeEntity
{
    // ...
    /** @ORM\Column(length=4) */
    public $pinCode; // In Symfony 4.3 it is improved by introducing automatic validation based on Doctrine mapping. In other words you do not have to make additional constraint as it was before
}




Unique constraint

/**
     * @Assert\Unique(message="The {‌{ value }} email is repeated.")
     */
    protected $contactEmails;




/**
     * @Assert\NotBlank(allowNull = true) // allowNull option added n 4.3
     */
    protected $someProperty;




New UrlHelper class

Symfony 4.3 extracted the internal logic used by the Twig functions into a new class called Symfony\Component\HttpFoundation\UrlHelper that you can inject as a service anywhere in your application. This class provides two public methods called getAbsoluteUrl() and getRelativePath().





Changed dispatch function arguments order

// ...
$order = new Order();
$newOrderEvent = new OrderPlacedEvent($order);
// Before
$dispatcher->dispatch(OrderEvents::NEW_ORDER, $newOrderEvent);
// After
$dispatcher->dispatch($newOrderEvent, OrderEvents::NEW_ORDER);




What's new in Symfony 4.4 and Symfony 5.0
Lazy firewalls

When a stateful firewall is configured, a user token is always created from the session for every request, no matter if the user is actually used or not by the application. This means that all those responses are uncacheable (because they use the session).

In Symfony 4.4, firewalls can define lazy as the value of their anonymous configuration option:

# config/packages/security.yaml
security:
    # ...
    firewalls:
        main:
            pattern: ^/
            anonymous: lazy
This tells Symfony to only load the user (and start the session) if the application actually access the user object (e.g. via a is_granted() call in a template or a isGranted() call in a controller or service). This means that all those URLs/actions that don't need the user will now be public and cacheable, improving the performance of your application.





New NotificationEmail service

use Symfony\Bridge\Twig\Mime\NotificationEmail;

This allows to send an email to yourself, see example https://symfony.com/blog/new-in-symfony-4-4-notification-emails





Encrypted secret configurations

Imagine that you want to keep the entire DATABASE_URL content secret to avoid leaking the database connection credentials

To do so run:

php bin/console secrets:generate-keys produces two keys: private and public, do not commit private key

then upload private key using ssh to config/secrets/<environment>/ folder

see more https://symfony.com/blog/new-in-symfony-4-4-encrypted-secrets-management





Simpler Event Listeners

services:
    App\EventListener\:
        resource: ../src/EventListener/*
        tags: [kernel.event_listener]


namespace App\EventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
final class MyRequestListener
{
    public function __invoke(RequestEvent $event): void // starting from Symfony 4.4 you can use invoke method with the type of event
    {
        // ...
    }
}




New beautiful welcome page

see https://symfony.com/blog/new-in-symfony-4-4-improved-welcome-page





Improved Messenger Component

Added a new middleware to clear Doctrine's entity manager after each message is consumed. The advantage of this middleware is that it reduces the memory consumption when handling messages in long-running processes.

framework:
    messenger:
        buses:
            messenger.bus.default:
                default_middleware: false
                middleware:
                    # ...
                    - 'doctrine_clear_entity_manager'