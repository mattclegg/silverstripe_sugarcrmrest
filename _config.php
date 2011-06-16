<?php

DataObject::add_extension('Member', 'Member_SugarCRM');

SugarRestfulService::set_url('http://andromeda.enableit.org.uk/solihull/service/v3_1/rest.php');
SugarRestfulService::set_username_password('matt','enableit');