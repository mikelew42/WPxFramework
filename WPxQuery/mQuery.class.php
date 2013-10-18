<?php

/*
 * 
 * 1) Reverse engineer $wpdb
 * http://mbe.ro/2009/08/30/fast-and-easy-php-mysql-class/
 * 2) Add this functionality
 * 
 * SELECT select_expr [, select_expr ...]
 * FROM
 * 		[tbl_name [AS alias]] [, JOIN table_name [AS alias] ON conditional_expr]
 * WHERE where_condition
 * GROUP BY col_name [ ASC | DESC ] 
 * ORDERY BY col_name [ ASC | DESC ]
 * LIMIT [offset,] row_count
 * 
 * 
 * Building blocks:
 * mQSelect
 * 		To begin, just implode an array
 * mQFrom
 * 		If !from, return false;
 * mQJoin
 * 		To begin, just implode an array
 * 		I don't believe join order matters
 * mQWhere
 * mQGroup
 * mQOrder
 * mQLimit
 * 
 * 
 * EX:  To query wp_posts AND wp_terms, you need a double join:
 * 			JOIN wp_term_relationships tr ON tr.object_id = ID
			JOIN wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
	and a WHERE:
			AND tt.term_id IN (  $this->get_term_ids_as_string() )
 * 
 * 
 * For all these - you should be able to remove/edit certain pieces.  Either
 * A) Use an assoc array, and use the array key as a way to reaccess this item
 * B) 
 * 
 * 
 * multiple joins?  nested joins?  cross, straight, natural, inner, outer, etc?
 * 		It appears join, cross join, and inner join are all equivalent
 * 
 * indexes?  use index hinting for joins?  http://dev.mysql.com/doc/refman/5.0/en/join.html
 */
/*
class mQuery extends mBase {
	protected $where = array(); // array of mQWhere
	protected $select; // mQSelect
	
	
}
*/
/*
 * This class is for the actual $sql?
 */
/*
class mSQL extends mBase {
	
}
*/