Toolbox
=======

Toolbox is a pico-framework ready to use. It acts as both a framework (url matching, session / database handling...) or as a set of really useful and ready to use classes.

With Toolbox, you can create a website really fast, putting all your configuration inside a file.

##Features

###Toolbox is modular

You can create a `Toolbox` instance and add all the libraries you will use. Because of this, you **don't** need to add all the classes, just the ones you will need. Toolbox then handles all the default configurations you need.

###Toolbox can be a library

If you don't want to use Toolbox as a framework, you can still use all the classes independently, by requiring `class.`_**`classname`**_`.php` from within the Toolbox folder. Each class has its own dependencies defined, so you won't have any problem. Plus non of them requires Toolbox to work, so it will never be instanced.

###Toolbox fits your already created project

Because Toolbox is not tight to a structure, it can sit next to another framework, perfectly fit with your already created project, making it even more awesome!

###Toolbox is easy to use

The Toolbox main motto is _"method chaining is simple as hell"_, and that's why every class in Toolbox uses this principle. Also, they all have a static method called `build()`. It sets the default values to the class without a hassle and returns an instance of itself, making an instance creation a breeze:

    Dice::build()->setMin(1)->setMax(6)->roll();

###Toolbox's Match enlightens your websites

The Uri Matcher called Match has a very easy and compact structure:

	$match = Match::build()
	->get('/user/{userid:int}', 'User::viewById')
	->get('/user/{user:string}', 'User::viewByName')
	->get('/user/{user}', 'User::view')
	->get('/user', 'User::showProfile')
	->post('/user/{user:int}', 'User::editUser')
	->post('/user', 'User::newUser')
	->matchAny('/', 'Home::index')
	->fire();

##Tools

###`Dice`

`Dice` works just like a regular dice. With `Dice` you can create a randomizer from a number to another and roll it as much as you want. It will pop random integer numbers, and even skip some of them by defining a `step`.

###`Vault`

`Vault` grabs your information and converts it into something really secure. It uses by default the 256-bit AES Encryption, so it is virtually impossible to break.

###`Keychain`

`Keychain` is your perfect tool as a Software company. It is able to generate any kind of serial number you want to use. It also checks for other serials you try, if they meet your requirements.

###`Session`

`Session` is a better way to use sessions in PHP. It lets you get and set parameters, using default values if they aren't set.

###`Match`

In case you need friendly urls, `Match` is your friend. It is a compact tool for url matching. It lets your `index.php` file really clean and small, as it gets the right methods from within independent classes, stored in a folder, wisely referred to as `matchbox`. These classes need to extend `Controller`.