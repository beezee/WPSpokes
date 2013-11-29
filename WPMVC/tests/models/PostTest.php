<?php

use \WPMVC\Framework\Models\Post;

class PostTest extends \Enhance\TestFixture
{
	public function testPostRequiredFields()
	{
		$p = new \WPMVC\Framework\Models\Post();
		$p->post_title = '';
		$p->post_name = '';
		$p->post_status = '';
		$p->post_type = '';
		\Enhance\Assert::isFalse($p->validate());
		\Enhance\Assert::areIdentical('Post Title is required', $p->errors['post_title'][0]);
		\Enhance\Assert::areIdentical('Post Name is required', $p->errors['post_name'][0]);
		\Enhance\Assert::areIdentical('Post Status is required', $p->errors['post_status'][0]);
		\Enhance\Assert::areIdentical('Post Type is required', $p->errors['post_type'][0]);

	}

	public function testPostStatusMustBeValidValue()
	{
		$p = new \WPMVC\Framework\Models\Post();
		foreach(array('publish', 'draft', 'pending', 'future', 'trash') as $status)
		{
			$p->post_status = $status;
			$p->validate();
			\Enhance\Assert::isFalse(array_key_exists('post_status', $p->errors));
		}
		$p->post_status = 'invalid_status';
		$p->validate();
		\Enhance\Assert::areIdentical('Post Status contains invalid value', $p->errors['post_status'][0]);
	}

	public function testPostNameMustBeValidSlug()
	{
		$p = new \WPMVC\Framework\Models\Post();
		$p->post_name = 'good-slug';
		$p->validate();
		\Enhance\Assert::isFalse(array_key_exists('post_name', $p->errors));
		$p->post_name = '<invalid%20slug>';
		$p->validate();
		\Enhance\Assert::areIdentical('Post Name must contain only letters a-z, numbers 0-9, dashes and underscores',
				$p->errors['post_name'][0]);
	}

	public function testPostContentIsCorrectlySanitized()
	{
		$p = new \WPMVC\Framework\Models\Post();
		$content = '<script src="http://hacked.js"></script>';
		$content .= $valid_content = '<a href="http://google.com">link</a>';
		$content .= '<iframe src="http://hacked.com"></iframe>';
		\Enhance\Assert::isFalse($content == $valid_content);
		$p->post_content = $content;
		\Enhance\Assert::areIdentical($content, $p->post_content);
		$p->validate();
		\Enhance\Assert::areIdentical($valid_content, $p->post_content);
	}

	public function testPostTitleIsStrippedOfHtml()
	{
		$p = new \WPMVC\Framework\Models\Post();
		$p->post_title = '<a href="http://google.com">Title</a>';
		$p->validate();
		\Enhance\Assert::areIdentical('Title', $p->post_title);
	}

	public function testPostDateFieldsAreUpdated()
	{
		$p = new \WPMVC\Framework\Models\Post();
		foreach(array('post_modified', 'post_modified_gmt') as $property)
		{
			$p->$property = date('Y-m-d', strtotime('yesterday'));
			\Enhance\Assert::isTrue($p->$property == date('Y-m-d', strtotime('yesterday')));
			$p->validate();
			\Enhance\Assert::isTrue($p->$property > date('Y-m-d', strtotime('yesterday')));
		}
		foreach(array('post_date', 'post_date_gmt') as $property)
		{
			$p->$property = '';
			\Enhance\Assert::isTrue($p->$property == '');
			$p->validate();
			\Enhance\Assert::isTrue($p->$property > date('Y-m-d', strtotime('yesterday')));
		}
	}

	public function testPostGeneratesUniqueSlug()
	{
		$p = new Post();
		$p->post_title = 'test'.microtime();
		$p->post_type = 'post';
		$p->post_status = 'draft';
		$p->save();
		$p2 = new Post();
		$p2->post_title = $p->post_title;
		$p2->validate();
		$p->delete();
		\Enhance\Assert::isTrue(preg_match('/^'.$p->post_name.'-([\d]+)$/', $p2->post_name) > 0);
	}

	public function testPostGeneratesSlugOnlyOnce()
	{
		$p = new Post();
		$p->post_title = 'testing';
		$p->validate();
		$slug = $p->post_name;
		$p->post_title = 'new testing';
		$p->validate();
		\Enhance\Assert::areIdentical($slug, $p->post_name);
	}
}
