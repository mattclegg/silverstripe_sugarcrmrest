<?php

DataObject::add_extension('Member', 'Member_SugarCRM');

SugarRestfulService::set_url('your sugar url');
SugarRestfulService::set_username_password('username','password');
