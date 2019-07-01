ZipField
========

ZipField is a field for entering postal codes into forms in ATK14 applications.

Installation
------------

Just use the Composer:

    cd path/to/your/atk14/project/
    composer require atk14/zip-field dev-master

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
          if(!$this->form->fields["zip"]->is_valid_for($d["country"],$d["zip"],$err)){
            $this->form->set_error("zip",$err);
            return;
          }

          $user = User::CreateNewRecord($d);
          // ...
        }
      }
    }

Testing
-------

At the moment testing is not possible in this project itself. ZipField is only testable in an ATK14 project:


    cd path/to/your/atk14/project/test/fields/
    ../../scripts/run_unit_tests tc_zip_field

It is expected that class TcBase exists in file test/fields/tc_base.php and is descendant of TcAtk14Field. Perfect example of that file is at https://github.com/atk14/Atk14Skelet/blob/master/test/fields/tc_base.php

License
-------

ZipField is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)

[//]: # ( vim: set ts=2 et: )
