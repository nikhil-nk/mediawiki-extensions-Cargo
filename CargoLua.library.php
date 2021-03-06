<?php

class CargoLuaLibrary extends Scribunto_LuaLibraryBase {

	function register() {
		$lib = array(
			'query' => array( $this, 'cargoQuery' )
		);
		return $this->getEngine()->registerInterface( __DIR__ . '/cargo.lua', $lib, array() );
	}

	function cargoQuery( $tables, $fields, $args ) {
		$this->checkType( 'query', 1, $tables, 'string' );
		$this->checkType( 'query', 2, $fields, 'string' );
		$this->checkTypeOptional( 'query', 3, $args, 'table', array() );

		if ( isset( $args['where'] ) ) {
			$where = $args['where'];
		} else {
			$where = null;
		}
		if ( isset( $args['join'] ) ) {
			$join = $args['join'];
		} else {
			$join = null;
		}
		if ( isset( $args['groupBy'] ) ) {
			$groupBy = $args['groupBy'];
		} else {
			$groupBy = null;
		}
		if ( isset( $args['having'] ) ) {
			$having = $args['having'];
		} else {
			$having = null;
		}
		if ( isset( $args['orderBy'] ) ) {
			$orderBy = $args['orderBy'];
		} else {
			$orderBy = null;
		}
		if ( isset( $args['limit'] ) ) {
			$limit = $args['limit'];
		} else {
			$limit = null;
		}
		if ( isset( $args['offset'] ) ) {
			$offset = $args['offset'];
		} else {
			$offset = null;
		}

		$query = CargoSQLQuery::newFromValues( $tables, $fields, $where, $join,
			$groupBy, $having, $orderBy, $limit, $offset );
		$rows = $query->run();

		$result = array();

		$fieldArray = CargoUtils::smartSplit( ',', $fields );

		$rowIndex = 1; // because Lua arrays start at 1
		foreach ( $rows as $row ) {
			$values = array();
			foreach ( $fieldArray as $fieldString ) {
				$alias = $query->getAliasForFieldString( $fieldString );
				$nameArray = CargoUtils::smartSplit( '=', $fieldString );
				$name = $nameArray[ count( $nameArray ) - 1 ];
				$values[$name] = htmlspecialchars_decode( $row[$alias] );
			}
			$result[$rowIndex++] = $values;
		}

		return array( $result );
	}
}
