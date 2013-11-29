<?php

use \WPMVC\Framework\Models\User;

class UserTest extends \Enhance\TestFixture
{

    public function testUserRequiredAttributes()
    {
        $u = new User();
        \Enhance\Assert::isFalse($u->validate());
        $required_attributes = array('user_login', 'user_email', 'user_registered',
                        'user_nicename', 'user_pass', 'display_name');
        foreach($required as $required_attribute)
            \Enhance\Assert::areIdentical(
                ucwords(join(' ', explode('_', $required_attribute))).' is required',
                    $u->errors[$required_attribute][0]);
    }

    public function testUserEmailMustBeValidEmail()
    {
        $u = new User();
        $u->user_email = 'sharknado';
        \Enhance\Assert::isFalse($u->validate());
        \Enhance\Assert::areIdentical('User Email is not a valid email address',
            $u->errors['user_email'][0]);
        $u->user_email = 'shark@nado.com';
        $u->validate();
        \Enhance\Assert::isFalse(array_key_exists('user_email', $u->errors));
    }

    public function testUserRegisteredDateIsUpdatedCorrectly()
    {
        $u = new User();
        $time = time();
        sleep(1);
        $u->validate();
        $assigned_time = strtotime($u->user_registered);
        \Enhance\Assert::isTrue($assigned_time > $time);
        $u->validate();
        \Enhance\Assert::areIdentical($assigned_time, strtotime($u->user_registered));
    }

    public function testUserPasswordIsHashedOnAssignment()
    {
        $u = new User();
        $u->password = 'sharknado';
        \Enhance\Assert::isTrue(wp_check_password('sharknado', $u->user_pass));
        \Enhance\Assert::isFalse(wp_check_password('frognado', $u->user_pass));
    }

	public function testUserGeneratesUniqueSlug()
	{
        $u = new User();
        $u->display_name = 'test '.microtime();
        $u->validate();
		$u->save(array('validate' => false));
        $u2 = new User();
        $u2->display_name = $u->display_name;
		$u2->validate();
		$u->delete();
        \Enhance\Assert::isTrue($u->user_nicename != $u2->user_nicename);
		\Enhance\Assert::isTrue(preg_match('/^'.$u->user_nicename.'-([\d]+)$/', $u2->user_nicename) > 0);
	}

	public function testUserGeneratesSlugOnlyOnce()
	{
        $u = new User();
		$u->display_name = 'testing';
		$u->validate();
		$slug = $u->user_nicename;
		$u->display_name = 'new testing';
		$u->validate();
		\Enhance\Assert::areIdentical($slug, $u->user_nicename);
	}
}

