<?php
class Account extends AppModel
{
public $validation = array(
'username' => array(
'length' => array(
'validate_between', 5, 25,
),
),
'password' => array(
'length' => array(
'validate_between', 5, 25,
),
),
);

}