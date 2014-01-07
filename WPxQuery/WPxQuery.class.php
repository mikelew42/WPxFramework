<?php

class WPxQuery extends WPxBase {

	protected $select;
	protected $from; //
	protected $as; // str alias for table
	protected $where; // array of where_conditions
	protected $group_by; // col_name
	protected $group_order; // ASC / DESC
	protected $order_by; // col_name
	protected $order; // ASC / DESC
	protected $offset;
	protected $limit;
}