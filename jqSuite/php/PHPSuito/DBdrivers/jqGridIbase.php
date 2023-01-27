<?php

	/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class jqGridDB
{

	public static function getInterface()
	{
		return 'ibase';
	}

	public static function prepare ($conn, $sqlElement, $params=null, $bind=true)
	{
		if($conn && strlen($sqlElement)>0) {
			
			$sql = ibase_prepare($conn, (string)$sqlElement);
			if ($sql === false) {
				throw new Exception("ibase_prepare failed; error = " 	. ibase_errmsg());
			} else {
				return $sql;
			}
		}
		return false;
	}
	public static function limit($sqlId, $dbtype, $nrows=-1,$offset=-1, $order='', $sort='' )
	{
		$psql = $sqlId;
		$offsetStr = '';
		if($offset>=0) {
			$offset++;
			$offsetStr = "$offset TO ";
		}
		//$offsetStr =($offset>=0) ? "$offset TO " : '';
		if ($nrows < 0) {
			$nrows = '18446744073709551615';
		} else {
			$nrows = (int)$offset + (int)$nrows;
		}
		$psql .= " ROWS $offsetStr$nrows";
		return $psql;
	}
	public static function execute($psql, $prm=null)
	{
		$ret = false;
		if($psql) {
			if(isset($prm)) {
				if(!is_array($prm)) {
					 return ibase_execute($psql, $prm);
				} else  {
					array_unshift($prm, $psql);
					return  call_user_func_array('ibase_execute', $prm);
				}
			} else {
				return ibase_execute($psql);
			}
		}
		return $ret;
	}
	public static function query($conn, $sql)
	{
		if($conn && strlen($sql)>0) {
			return ibase_query($conn, $sql);
		}
		return false;
	}
	public static function bindValues($stmt, $binds, $types)
	{
		return true;
	}
	public static function beginTransaction( $conn )
	{
		return ibase_trans($conn);
	}
	public static function commit( $transid )
	{
		return ibase_commit( $transid  );
	}
	public static function rollBack( $transid )
	{
		return ibase_rollback( $transid );
	}
	public static function lastInsertId($conn, $table, $IdCol, $dbtype)
	{
		// very complicated 
		return -1;
	}
	public static function fetch_object( $psql, $fetchall, $conn=null )
	{
		if($psql) {
			if(!$fetchall)
			{
				return ibase_fetch_object($psql);
			} else {
				$ret = array();
				while ($obj = ibase_fetch_object($psql))
				{
					$ret[] = $obj;
				}
				return $ret;
			}
		}
		return false;
	}
	public static function fetch_num( $psql )
	{
		if($psql)
		{
			return ibase_fetch_row( $psql );
		}
		return false;
	}
	public static function fetch_assoc( $psql, $conn )
	{
		if($psql)
		{
			return ibase_fetch_assoc($psql );
		}
		return false;
	}
	public static function closeCursor($sql)
	{
		if($sql) { 
			try {
				ibase_free_result($sql);
			} catch (Exception $e) {				
			}
		}
	}
	public static function columnCount( $rs )
	{
		if($rs) {
			return ibase_num_fields( $rs );
		} else {
			return 0;
		}
	}
	public static function getColumnMeta($index, $sql)
	{
		if($sql && $index >= 0) {
			$col_info = ibase_field_info($sql, $index);
			$newmeta = array();
			$newmeta["name"]  = $col_info['name'];
			$newmeta["native_type"]  = $col_info['type'];
			$newmeta["len"]  = $col_info['length'];
			return $newmeta;
		}
		return false;
	}
	/**
	 *
	 * Return the meta type of the field based on the underlayng db
	 * @param array $t object returned from pdo getColumnMeta
	 * @param string $dbtype the database type
	 * @return string the type of the field can be string, date, datetime, blob, int, numeric
	 */
	public static function MetaType($t,$dbtype)
	{

		if ( is_array($t)) {
			$type = $t["native_type"];
			//$len = $t["len"];
			switch(strtoupper($type))
			{
				case 'INTEGER':
				case 'BIGINT':
				case 'SMALLINT':
					return 'int';
				case 'VARCHAR':
				case 'CHAR':
					return 'string';
				case 'BLOB' : 
					return 'blob';
				case 'DATE' : //date
					return 'date';
				case 'TIME' :
				case 'TIMESTAMP' :
					return 'datetime';
				default : 
					return 'string';
			}
		}
		return 'numeric';
	}
	public static function getPrimaryKey($table, $conn, $dbtype)
	{
		/**
		* Discover metadata information about this table.
		*/
		$table = strtoupper($table);
		
		$sql = 'SELECT S.RDB$FIELD_NAME AFIELDNAME
		FROM RDB$INDICES I JOIN RDB$INDEX_SEGMENTS S ON I.RDB$INDEX_NAME=S.RDB$INDEX_NAME  
		WHERE I.RDB$RELATION_NAME=\''.$table.'\' and I.RDB$INDEX_NAME like \'RDB$PRIMARY%\'
		ORDER BY I.RDB$INDEX_NAME,S.RDB$FIELD_POSITION';

		$rs = self::query($conn, $sql);
		if($rs) {
			$res = self::fetch_num($rs);
			self::closeCursor($rs);
			if($res) {
				return trim($res[0]);
			}
		}
		return false;
	}
	public static function errorMessage ( $conn )
	{
		return ibase_errmsg();
	}
}
