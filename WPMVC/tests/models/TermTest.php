<?php

use \WPMVC\Framework\Models\Term;

class TermTest extends \Enhance\TestFixture
{

	public function testTermRequiresName()
	{
		$t = new Term();
		\Enhance\Assert::isFalse($t->validate());
		\Enhance\Assert::areIdentical('Name is required', $t->value('errors.name.0'));
	}

	public function testTermStripsTagsFromName()
	{
		$t = new Term();
		$t->name = '<script src="hacked"><script>';
		\Enhance\Assert::isFalse($t->validate());
		\Enhance\Assert::areIdentical('Name is required', $t->value('errors.name.0'));
		$t->name = '<script src="hacked"></script><a href="google">a name</a>';
		\Enhance\Assert::isTrue($t->validate());
		\Enhance\Assert::areIdentical('a name', $t->name);
	}

	public function testTermGeneratesUniqueSlug()
	{
		$p = new Term();
		$p->name = 'test'.microtime();
		$p->save();
		$p2 = new Term();
		$p2->name = $p->name;
		$p2->validate();
		$p->delete();
		\Enhance\Assert::isTrue(preg_match('/^'.$p->slug.'-([\d]+)$/', $p2->slug) > 0);
	}

	public function testTermGeneratesSlugOnlyOnce()
	{
		$p = new Term();
		$p->name = 'testing';
		$p->validate();
		$slug = $p->slug;
		$p->name = 'new testing';
		$p->validate();
		\Enhance\Assert::areIdentical($slug, $p->slug);
	}

}
