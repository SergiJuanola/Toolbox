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

###Toolbox fitx in your server