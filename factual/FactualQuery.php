<?php


/**
 * Represents a top level Factual query. Knows how to represent the query as URL
 * encoded key value pairs, ready for the query string in a GET request. (See
 * {@link #toUrlQuery()})
 * This is a refactoring of the Factual Driver by Aaron: https://github.com/Factual/factual-java-driver
 * @author Tyler
 * @package Factual
 * @license Apache 2.0
 */

class FactualQuery {
	protected $fullTextSearch; //string
	protected $selectFields = null; //otherwise comma-delineated list of fieldnames
	protected $limit; //int
	protected $offset; //int
	protected $includeRowCount = false; //bool
	protected $geo = null; 
	protected $keyValuePairs = array(); //misc key-value pairs added as additional parameters
	protected $threshold = null; // takes an enumerated value ("confident", "default", "comprehensive")
	const RESPONSETYPE = "ReadResponse";

	/**
	 * Whether this lib must perform URL encoding.
	 * Set to avoid double or absent encoding
	 */
	const URLENCODE = true;

	/**
	 * Holds all row filters for this Query. Implicit top-level AND.
	 */
	protected $rowFilters = array ();

	/**
	 * Holds all results sorts for this Query. Example contents:
	 * <tt>"$distance:desc","name:asc","locality:asc"</tt>
	 */
	protected $sorts = array ();

	/**
	 * Sets a full text search query. Factual will use this value to perform a
	 * full text search against various attributes of the underlying table, such
	 * as entity name, address, etc.
	 * 
	 * @param string term The text for which to perform a full text search.
	 * @return obj Query
	 */
	public function search($term) {
		$this->fullTextSearch = $term;
		return $this;
	}

	public function getResponseType(){
		return self::RESPONSETYPE;
	}

	/**
	 * Sets the maximum amount of records to return from this Query.
	 * @param int limit The maximum count of records to return from this Query.
	 * @return this Query
	 */
	public function limit($limit) {
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Sets the fields to select. This is optional; default behaviour is generally
	 * to select all fields in the schema.
	 * @param mixed fields Fields to select as comma-delineated string or array
	 * @return this Query
	 * @deprecated 1.5.0 - Jul 30, 2012 Use FactualQuery::select();
	 */
	public function only($fields) {
		return $this->select($fields);
	}

	/**
	 * Sets the fields to select. This is optional; default behaviour is generally
	 * to select all fields in the schema.
	 * @param mixed fields Fields to select as comma-delineated string or array
	 * @return this Query
	 */
	public function select($fields) {
		if (is_array($fields)) {
			$fields = implode(",", $fields);
		}
		$this->selectFields = $fields;
	}

	/**
	 * @return array of select fields set by select(), null if none.
	 */
	public function getSelectFields() {
		return $this->selectFields;
	}

	public function threshold($threshold){
	       $this->threshold = $threshold;
	       return $this;
	}

	/**
	 * Sets this Query to sort field in ascending order.
	 * @param string field The field name to sort in ascending order.
	 * @return obj this Query
	 */
	public function sortAsc($field) {
		$this->sorts[] = $field . ":asc";
		return $this;
	}

	/**
	 * Sets this Query to sort field in descending order.
	 * @param string field The field to sort in descending order.
	 * @return this Query
	 */
	public function sortDesc($field) {
		$this->sorts[] = $field . ":desc";
		return $this;
	}

	/**
	 * Sets how many records in to start getting results (i.e., the page offset) for this Query.
	 * @param int offset The page offset for this Query.
	 * @return obj this Query
	 */
	public function offset($offset) {
		$this->offset = $offset;
		return $this;
	}

	/**
	 * The response will include a count of the total number of rows in the table
	 * that conform to the request based on included filters. There is a performance hit. 
	 * The default behavior is to NOT include a row count.
	 * @return this Query, marked to return total row count when run.
	 */
	public function includeRowCount() {
		return $this->includeRowCount = true;
	}

	/**
	 * When true, the response will include a count of the total number of rows in
	 * the table that conform to the request based on included filters.
	 * Requesting the row count will increase the time required to return a
	 * response. The default behavior is to NOT include a row count. 
	 * @param includeRowCount
	 *          true if you want the results to include a count of the total
	 *          number of rows in the table that conform to the request based on
	 *          included filters.
	 * @return this Query.
	 * @internal changed method name. Unsure why we return obj here but not above
	 */
	public function setIncludeRowCount(boolean $includeRowCount) {
		$this->includeRowCount = $includeRowCount;
		return $this;
	}

	/**
	 * Begins construction of a new row filter for this Query.
	 * @param string field The name of the field on which to filter.
	 * @return obj QueryBuilder A partial representation of the new row filter.
	 */
	public function field($field) {
		return new QueryBuilder($this, $field);
	}

	/**
	 * Adds a filter so that results can only be (roughly) within the specified geometry.
	 * @param circle | rectangle The circle or rectangle within which to bound the results.
	 * @return this Query.
	 */
	public function within($geo) {
		$type = get_class ($geo);
		$valid = array("FactualRectangle","FactualCircle");
		if (!in_array($type,$valid)){
			throw new Exception(__METHOD__." must take FactualCircle or FactualRectangle object as parameter");
			return false;
		}
		$this->geo = $geo;
		return $this;
	}
	
	
	

	/**
	 * Adds a filter so that results can only be obtained at a specified point
	 * @param point The point within which to center the results.
	 * @return this Query.
	 */
	public function at($point) {
		if (!$point instanceof FactualPoint){
			throw new Exception(__METHOD__." must take FactualPoint object as parameter");
			return false;
		}
		$this->geo = $point;
		return $this;
	}

	/**
	 * Used to nest AND'ed predicates.
	 * @param array queries An array of query actions
	 * @internal method renamed from Java driver due to 'and' reserved word 
	 */
	public function _and($queries) {
		return $this->popFilters("\$and", $queries);
	}

	/**
	 * Used to nest OR'ed predicates.
	 * @param mixed queries A single query object or array thereof
	 * @internal method renamed from Java driver due to 'or' reserved word 
	 */
	public function _or($queries) {
		return $this->popFilters("\$or", $queries);
	}

	/**
	 * Adds <tt>filter</tt> object to this Query.
	 * @return void
	 */
	public function add($filter) {
		$this->rowFilters[] = $filter;
	}

	/**
	 * Builds and returns the query string to represent this Query when talking to
	 * Factual's API. Provides proper URL encoding and escaping.
	 * <p>
	 * Example output:
	 * <pre>
	 * filters=%7B%22%24and%22%3A%5B%7B%22region%22%3A%7B%22%24in%22%3A%22MA%2CVT%2CNH%22%7D%7D%2C%7B%22%24or%22%3A%5B%7B%22first_name%22%3A%7B%22%24eq%22%3A%22Chun%22%7D%7D%2C%7B%22last_name%22%3A%7B%22%24eq%22%3A%22Kok%22%7D%7D%5D%7D%5D%7D
	 * </pre>
	 * <p>
	 * (After decoding, the above example would be used by the server as:)
	 * <pre>
	 * filters={"$and":[{"region":{"$in":"MA,VT,NH"}},{"$or":[{"first_name":{"$eq":"Chun"}},{"last_name":{"$eq":"Kok"}}]}]}
	 * </pre>
	 * @return string The query string to represent this Query when talking to Factual's API.
	 * @internal re-activate geobounds method
	 */
	public function toUrlQuery() {

		$temp['select'] = $this->fieldsJsonOrNull();
		$temp['q'] = $this->fullTextSearch;
		$temp['sort'] = $this->sortsJsonOrNull();
		$temp['limit'] = ($this->limit > 0 ? $this->limit : null);
		$temp['offset'] = ($this->offset > 0 ? $this->offset : null);
		$temp['include_count'] =  ($this->includeRowCount ? "true" : null);
		$temp['filters'] = $this->rowFiltersJsonOrNull();
		$temp['geo'] = $this->geoBoundsJsonOrNull();
		$temp['threshold'] = $this->thresholdOrNull();
		$temp = array_filter($temp); //remove nulls		

		//initialize
		$temp2 = array();

		//encode (cannot use http_build_query() as we need to *raw* encode adn this not provided until PHP v5.4)
		foreach ($temp as $key => $value){
			$temp2[] = $key."=".rawurlencode($value);		
		}	
		
		//process additional kay/value parameters
		foreach ($this->keyValuePairs as $key => $value){
			$temp2[] = $key."=".rawurlencode($value);	
		}
		
		return implode("&", $temp2);
	}

	/**
	 * Adds misc parameters to the URL query
	 * @param string key Key namev
	 * @param string un-URL-encoded value 
	 */
	public function addParam($key,$value){
		$this->keyValuePairs[$key] = $value;
		return $this->keyValuePairs;
	}

	public function toString() {
		try {
			return urldecode($this->toUrlQuery());
		} catch (Exception $e) {
			throw $e;
		}
	}

	protected function thresholdOrNull() {
		if ($this->threshold != null){
		        return $this->threshold;
		}else{
		        return null;
		}
	}

	protected function fieldsJsonOrNull() {
		if ($this->selectFields != null) {
			return $this->selectFields;
		} else {
			return null;
		}
	}

	protected function sortsJsonOrNull() {
		if (!empty ($this->sorts)) {
			return implode(",", $this->sorts);
		} else {
			return null;
		}
	}

	protected function geoBoundsJsonOrNull() {
		if ($this->geo != null) {
			return $this->geo->toJsonStr();
		} else {
			return null;
		}
	}

	protected function rowFiltersJsonOrNull() {
		if (empty ($this->rowFilters)) {
			return null;
		} else
			if (count($this->rowFilters) === 1) {
				return $this->rowFilters[0]->toJsonStr();
			} else {
				$filterGroup = new FilterGroup($this->rowFilters);
				return $filterGroup->toJsonStr();
			}
	}

	/**
	 * Pops the newest Filter from each of <tt>queries</tt>,
	 * grouping each popped Filter into one new FilterGroup.
	 * Adds that new FilterGroup as the newest Filter in this
	 * Query.
	 * <p>
	 * The FilterGroup's logic will be determined by <tt>op</tt>.
	 * @param string op operator name
	 * @param array Array of Query filter criteria
	 * @return obj queries Query object
	 */
	protected function popFilters($op, array $queries) {
		$group = new FilterGroup();
		$group->op($op);
		foreach ($queries as $query) {
			if (!$query->rowFilters){ //check to ensure nly filters are combined
				throw new Exception("Operator ".$op." can be used only to combine row filters");
				return false;
			} else {
				$group->add(array_pop($query->rowFilters));
			}
		}
		$this->add($group);
		return $this;
	}

}
?>
