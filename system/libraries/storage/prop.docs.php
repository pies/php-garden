<?

/**
 * prop            (field)
 *   prop.type     (field.type)
 *   prop.id       (field.colname)
 *   prop.data     (field.value)
 * 
 * item            (row)
 *   item.id       (row.key)
 *   item.data     (row.values)
 * 
 * group           (table)
 *   group.id      (table.name)
 *   group.data    (table.rows)
 * 
 * store           (database)
 * 
 * filter = selective group
 * 
 */

class Types {
	const 
	LINE   = 'string', 
	MEMO   = 'array-of-strings', 
	NUMBER = 'integer',
	AMOUNT = 'float',
	PRICE  = 'fixed-float', 
	CHOICE = 'integer',
	SET    = 'array-of-integers';
}


include 'prop.php';


include 'prop-string.php';


include 'prop-number.php';
$price = new PriceProp(120);
$price->string = '56.50';
print "<p>{$price->html} - {$price->form}</p>";


include 'prop-date.php';
$date  = new DateProp('2006/10/08');


?>