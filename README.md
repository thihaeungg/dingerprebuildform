Composer Package Template
============

[![Latest Stable Version](https://packagist.org/packages/hmm/dingerprebuildform)]

Requirements
------------

* PHP >= 8.0;
* composer.

Features
--------

* PSR-4 autoloading compliant structure;
* Easy to use with Laravel framework;
* Useful tools for better code included.

Installation
============

    composer require hmm/dingerprebuildform
    
<!-- This will create a basic project structure for you: -->

<!-- * **/build** is used to store code coverage output by default;
* **/src** is where your codes will live in, each class will need to reside in its own file inside this folder;
* **/tests** each class that you write in src folder needs to be tested before it was even "included" into somewhere else. So basically we have tests classes there to test other classes;
* **.gitignore** there are certain files that we don't want to publish in Git, so we just add them to this fle for them to "get ignored by git";
* **CHANGELOG.md** to keep track of package updates;
* **CONTRIBUTION.md** Contributor Covenant Code of Conduct;
* **LICENSE** terms of how much freedom other programmers is allowed to use this library;
* **README.md** it is a mini documentation of the library, this is usually the "home page" of your repo if you published it on GitHub and Packagist;
* **composer.json** is where the information about your library is stored, like package name, author and dependencies;
* **phpunit.xml** It is a configuration file of PHPUnit, so that tests classes will be able to test the classes you've written;
* **.travis.yml** basic configuration for Travis CI with configured test coverage reporting for code climate. -->

<!-- Please refer to original [article](http://www.darwinbiler.com/creating-composer-package-library/) for more information. -->

Set Up Tools
============

Running Command:
--------------------------

    php artisan vendor:publish --provider="Hmm\Dingerprebuildform\DingerServiceProvider" --tag="config"

    #Note

    This command will create dinger.php file inside config folder like this, 


    <?php

        return [

            #Dinger Callback Key ထည့်ပါ။
            "callback_key" => null,

            #Dinger Public Key ထည့်ပါ။
            "public_key" =>   null


            #Dinger Secret Key ထည့်ပါ။
            "secret_key" => null,
        
            #Dinger Prebuild url ထည့်ပါ။
            #production url ကို ဤနေရာတွင် ပြောင်းလဲ ဖြည့်စွက်နိုင်သည်။ 
            #sample - https://form.dinger.asia
            "url" => "https://prebuilt.dinger.asia",

            #project name ထည့်ပါ။
            "project_name" => null,

            #merchant name ထည့်ပါ။
            "merchant_name" =>  null,

            #client id ထည့်ပါ။
            "client_id" =>  null,

            #merchant key ထည့်ပါ။
            "merchant_key" =>  null
        ];
    ?>

    #Important - You need fill the dinger info in this config file for package usage.



Package Usage:
--------------------------
 
Generate Prebuild Url:
----------------

    use Hmm\Dingerprebuildform\Dinger;

    Dinger::load(@multidimensionalArray $items,@String $customerName, @Int $totalAmount, @String $merchantOrderId);

    #Note 

    #$items array must be include name, amount, quantity.
    #customerName must be string.
    #totalAmount must be integer.
    #merchantOrderId must be string.

    #Output 

    #This will generate a dinger prebuild form url.    

Extract Callback Data:
----------------

    use Hmm\Dingerprebuildform\Dinger;

    Dinger::callback(@String $paymentResult,@String $checkSum);

    #Note 

    #paymentResult must be string.
    #checkSum must be string.


    #ImportantForCallbackApi ( Not belong with this function. Just FYI) 

    #That callback data need to call with array square brackets.Not with object arrow.

    #Output 

    This will generate callback data array.    

<!-- Changelog
========= -->

<!-- To keep track, please refer to [CHANGELOG.md](https://github.com/GinoPane/composer-package-template/blob/master/CHANGELOG.md). -->

<!-- Contributing
============

1. Fork it.
2. Create your feature branch (git checkout -b my-new-feature).
3. Make your changes.
4. Run the tests, adding new ones for your own code if necessary (phpunit).
5. Commit your changes (git commit -am 'Added some feature').
6. Push to the branch (git push origin my-new-feature).
7. Create new pull request.

Also please refer to [CONTRIBUTION.md](https://github.com/GinoPane/composer-package-template/blob/master/CONTRIBUTION.md). -->

License
=======

HMM Reserved Since 2023.