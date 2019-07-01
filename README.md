ZipField
========

ZipField is a field for entering postal codes into forms in ATK14 applications.

Installation
------------

Just use the Composer:

    cd path/to/your/atk14/project/
    composer require atk14/zip-field

Optionally you can symlink the ZipField files into your project:

    ln -s ../../vendor/atk14/zip-field/src/app/fields/zip_field.php app/fields/zip_field.php
		ln -s ../../vendor/atk14/zip-field/test/tc_zip_field.php test/fields/tc_zip_field.php

Usage in a ATK14 application
----------------------------

In a form:

    <?php
    // file: app/forms/users/create_new_form.php
    class CreateNewForm extends ApplicationForm {

      function set_up(){
        $this->add_field("zip", new ZipField([
          "label" => "ZIP Code",
        ]));

      }
    }

License
-------

ZipField is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)
