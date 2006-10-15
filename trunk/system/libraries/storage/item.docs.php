<?
require dirname(__FILE__).'/libs/init.php';

/** Data types */
define('DT_TEXT',   'text');
define('DT_NUMBER', 'int');
define('DT_AMOUNT', 'decimal');
define('DT_FKEY',   'int');

FASE::requires('data/item');


class Company extends Item {
	var $has_many = array('product','material');
	var $fields = array(
		'name' => array(DT_TEXT, true)
	);
};

class Product extends Item {
	var $belongs_to   = array('company');
	var $has_many     = array('combination');
	var $fields       = array(
		'name'      => array(DT_TEXT, true),
		'element_1' => array(DT_TEXT, true),
		'element_2' => array(DT_TEXT, false),
		'element_3' => array(DT_TEXT, false),
		'element_4' => array(DT_TEXT, false)
	);
};

class Material extends Item {
	var $belongs_to   = array('company');
	var $fields       = array(
		'name' => array(DT_TEXT, true)
	);
};

class Combination extends Item {
	var $belongs_to   = array( 'product' );
	var $has_multiple = array( 'material' => 4 );
	var $fields       = array(
  		'price' => array(DT_AMOUNT, true)
  	);
};


/** 1.0. Clear out the database */
DB::execute("TRUNCATE combination");
DB::execute("TRUNCATE material");
DB::execute("TRUNCATE product");
DB::execute("TRUNCATE company");

/** 1.1. Create a new company */
$company = new Company();
$company->name = 'Testowa S.A.';
$company->save();

/** 1.2. Check if successfully created */
debug ($company->load());

/** 1.3. Add a new product */
$product = new Product();
$product->belongs_to($company);
$product->name = 'Kanapa trzydrzwiowa';
$product->save();

/** 1.4. Check if successfully created */
debug ($product->load());

/** 1.5. Create two new materials */
$mat_1 = new Material();
$mat_1->belongs_to($company);
$mat_1->name = 'Skóra';
$mat_1->save();

$mat_2 = new Material();
$mat_2->belongs_to($company);
$mat_2->name = 'Drewno brzozowe';
$mat_2->save();

/** 1.6. Check if materials successfully created */
debug ($mat_1->load());
debug ($mat_2->load());

/** 1.7. Create a new material combination */
$comb = new Combination();
$comb->belongs_to($product);
$comb->has_multiple($mat_1, $mat_2);
$comb->price = '12.34';
$comb->save();

/** 1.8. Check if combination successfully created */
debug($comb->load());


/*
CREATE TABLE `product` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `company_id` int(10) unsigned NOT NULL,
  `name` text collate utf8_polish_ci NOT NULL,
  `elem_1` text collate utf8_polish_ci NOT NULL,
  `elem_2` text collate utf8_polish_ci NOT NULL,
  `elem_3` text collate utf8_polish_ci NOT NULL,
  `elem_4` text collate utf8_polish_ci NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `combination` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL,
  `mat_id` int(10) unsigned default NULL,
  `mat_id_2` int(10) unsigned default NULL,
  `mat_id_3` int(10) unsigned default NULL,
  `mat_id_4` int(10) unsigned default NULL,
  `price` decimal(10,0) default NULL,
  PRIMARY KEY  (`id`),
);

CREATE TABLE `company` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` text collate utf8_polish_ci NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `mat` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `company_id` int(10) unsigned NOT NULL,
  `name` text collate utf8_polish_ci NOT NULL,
  PRIMARY KEY  (`id`)
);

*/


function between ($min, $value, $max) {
	return ($value >= $min && $value <= $max);
}

?>