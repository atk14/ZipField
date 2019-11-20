ZipField
========

ZipField is a field for entering postal codes into forms in ATK14 applications.

Installation
------------

Just use the Composer:

    cd path/to/your/atk14/project/
    composer require atk14/zip-field

Optionally you can symlink the ZipField file into your project:

    ln -s ../../vendor/atk14/zip-field/src/app/fields/zip_field.php app/fields/zip_field.php

Usage in a ATK14 application
----------------------------

ZipField has method is_valid_for() for re-validation in context of the selected country. 

In a form:

    <?php
    // file: app/forms/users/create_new_form.php
    class CreateNewForm extends ApplicationForm {

      function set_up(){
        // ...
        $this->add_field("zip", new ZipField([
          "label" => "ZIP Code",
        ]));
        $this->add_field("country",new ChoiceField([
          "label" => "Country",
          "choices" => [
            "CZ" => "Czech Republic",
            "SK" => "Slovakia",
            "AT" => "Austria",
            "PL" => "Poland",
            // ...
          ],
        ]));
      }
    }

In a controller:

    <?php
    // file: app/controllers/users_controller.php
    class UsersController extends ApplicationController {

      function create_new(){
        // ...
        if($this->request->post() && ($d = $this->form->validate($this->params))){
          // postal code re-validation for the selected country
          if(!$this->form->fields["zip"]->is_valid_for($d["country"],$d["zip"],$err)){
            $this->form->set_error("zip",$err);
            return;
          }

          $user = User::CreateNewRecord($d);
          // ...
        }
      }
    }

It's possible to set up ZipField only to accept postal codes from one specific country. Re-validation is not necessary in this case.

    <?php
    // file: app/forms/users/create_new_form.php
    class CreateNewForm extends ApplicationForm {

      function set_up(){
        // ...
        $this->add_field("zip", new ZipField([
          "label" => "ZIP Code",
          "country" => "CZ"
        ]));
      }
    }

Error message for invalid zip code or format hints can be specified

    <?php
    // file: app/forms/users/create_new_form.php
    class CreateNewForm extends ApplicationForm {

      function set_up(){
        // ...
        $this->add_field("zip", new ZipField([
          "error_messages" => [
            "invalid" => _("Invalid ZIP code"),
          ],
          "format_hints" => [
            "CZ" => _("Please use format NNN NN"),
            "AT" => _("Please use format NNNN"),
          ],
        ]));
      }
    }

Testing
-------

    composer update --dev
    cd test
    ../vendor/bin/run_unit_tests

License
-------

ZipField is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)

[//]: # ( vim: set ts=2 et: )
