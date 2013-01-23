Toolbox
=======

Toolbox is a pico-framework ready to use. It acts as both a framework (url matching, session / database handling...) or as a set of really useful and simple classes.

With Toolbox, you can create a website really fast, putting all your configuration inside a file. 

##Features

###Toolbox is modular

You can create a `Toolbox` instance and add all the libraries you will use. Because of this, you **don't** need to add all the classes, just the ones you will need. Toolbox then handles all the default configurations you need.

###Toolbox can be a library

If you don't want to use Toolbox as a framework, you can still use all the classes independently, by requiring `class.<classname>.php` from within the Toolbox folder. Each class has its own dependencies defined, so you won't have any problem. Plus non of them requires Toolbox to work, so it will never be instanced.

###Toolbox fits your already created project

Because Toolbox is not tight to a structure, it can sit next to another framework, perfectly fit with your already created project, making it even more awesome!

###Toolbox is easy to use

The Toolbox main motto is _"method chaining is simple as hell"_, and that's why every class in Toolbox uses this principle. Also, they all have a static method called `build()`. It sets the default values to the class without a hassle and returns an instance of itself, making an instance creation a breeze:

    Dice::build()->setMin(1)->setMax(6)->roll();

###Toolbox's Match enlightens your websites

The Uri Matcher called `Match` has a very easy and compact structure:

	$match = Match::build()
	->get('/user/{userid:int}', 'User::viewById')
	->get('/user/{user:string}', 'User::viewByName')
	->get('/user/{user}', 'User::view')
	->get('/user', 'User::showProfile')
	->post('/user/{user:int}', 'User::editUser')
	->post('/user', 'User::newUser')
	->matchAny('/', 'Home::index')
	->fire();

###Toolbox loves multilanguage websites

Toolbox is really loose, but there is one thing that Match, Brush and Dictionary love to do really tight: localize websites. Match lets you decide whether your site is single or multilanguage, and when your urls are common for every language or they should be different for every language.

Brush is able to connect to Match and get the urls translated in every language.

Dictionary lets you translate texts using files for other locales.

	/*
		The 'locales' item in 'match' configuration lets you
		add as many locales as you need. The first one will 
		be the default one. Once you add any locale in the 
		array, Match sets the url matching as multilanguage.

		Dictionary needs the directory for the dictionary
		files. They need to be named as the locale and with
		a .php extension.
	*/

	$app = Toolbox::build(array(
		'match'=>array(
			'matchbox' => __DIR__.'/matchbox/',
			'locales'=>array(
				'es',
				'en',
			)
		),
		'brush'=>array(
			'views'=>__DIR__.'/views/',
			'layout'=>'layout.php',
		),
		'dictionary'=>array(
			'dictionaries'=>__DIR__.'/dictionaries/',
		),
	));

	/*
		Match commands can get two parameters, if the specified
		URI is common in every language (like /data or /) or
		three parameters, meaning the third an array of URIs,
		one per locale, without the locale reference. In this
		case, The index / will be the same for each locale,
		finally being represented by /es/ and /en/, depending
		on the language. However, the last rule will apply to
		/es/sobre-nosotros and /en/about-us and both will call
		Home::about action. In this case, /sobre-nosotros will
		be used as an alias, in case we want to link to this
		URI in a localized way. If we write in a layout/view
		$this->url('/sobre-nosotros'), it will return us the
		localised link to this alias.
	*/

	$match = Match::build()
	->post('/data', 'Webservice::postData')
	->get('/', 'Home::index')
	->get('/sobre-nosotros', 'Home::about', array(	
			'es' => '/sobre-nosotros',
			'en' => '/about-us',
		)
	)
	->fire();

##Tools

Toolbox includes a lot of useful tools and examples of classes you can use to create your own ones. The most important ones are:

###Dice

`Dice` works just like a regular dice. With `Dice` you can create a randomizer from a number to another and roll it as much as you want. It will pop random integer numbers, and even skip some of them by defining a `step`.

###Vault

`Vault` grabs your information and converts it into something really secure. It uses by default the 256-bit AES Encryption, so it is virtually impossible to break.

###Keychain

`Keychain` is your perfect tool as a Software company. It is able to generate any kind of serial number you want to use. It also checks for other serials you try, if they meet your requirements. However, the possibilities are endless, as you can define unique identifiers for your users, instead of the classic ID.

###Session

`Session` is a better way to use sessions in PHP. It lets you get and set parameters, using default values if they aren't set.

###Match

In case you need friendly urls, `Match` is your friend. It is a compact tool for url matching. It lets your `index.php` file really clean and small, as it gets the right methods from within independent classes, stored in a folder, wisely referred to as `matchbox`. These classes need to extend `Controller`.

###Brush

`Brush` is a really simple rendering class. It is as simple as it can, actually. You can define a layout for every brush or use one by default, and then just hit `paint()`. Due to the extended use of `render()` named methods, it is an alias for `paint()`.

###Orm

`Orm`, a really simple database abstraction, lets you operate through your database in a clean, controlled way. Get objects or assoc arrays, to adapt your needs. Use `save()` to decide whether to insert a new row or update an old one. It's as easy as it sounds.

###Dictionary

`Dictionary` is your best friend if your website is multilanguage. It will let you store any text that needs to be translated in a single file, and then call something like this:

    Dictionary::build()->t('About us', 'es');

, popping a message in the selected locale ('es') or writing back About us if you haven't translated it yet. In case you don't choose to write a locale, and you have linked your `Match` instance to `Dictionary`, you can just let it automagically choose the locale for you.

###Marker

`Marker` lets you use Markdown inside your pages. It uses the PHP Markdown & Extra library from [Michel Fortin](http://michelf.com)

##Setup

Do you want to start a new project with **Toolbox**? In that case, just download the latest copy of Toolbox, uncompress it into a folder (in terms of organisation, it should be stored outside the html accessible folder). For our convenience, we will call this folder `/toolbox`, and we will store one level up from our root.

Open your website root and write the following in an index.php file:

	<?php 
	require_once '../toolbox/class.toolbox.php';

	$app = Toolbox::build(array(
		'match' 	=>	array(
							'matchbox'	=>	__DIR__.'/matchbox/'
						),
		'brush' 	=>	array(
							'views'	=>	__DIR__.'/views/',
							'layout' => 'layout.php',
						)
	));

This creates a brand new entry point with all you need to create a friendly url website with templates. You should also create an .htaccess to redirect everything to index.php:

	RewriteEngine on
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule . index.php

Then, you can create a `Match` instance to start url matching:

	<?php
	// following index.php...
	$match = Match::build()
	->matchAny('/', 'Home::index')
	->fire();

In this case, we are matching the home screen, and sending its request to the `HomeController`, which is saved inside `/matchbox`. Then, we call `index()` within `HomeController`. This is an example of `controller.home.php`, hosted inside `/matchbox`, and holding only a method:

	<?php 

	require_once('../toolbox/class.controller.php');

	class HomeController extends Controller {
		public function index()
		{
			Brush::build()->paint('home.php', array('var1'=>'val1'));
		}
	}
