<?php

class Address
{

	public $cyr = [
		'а',
		'б',
		'в',
		'г',
		'д',
		'e',
		'ж',
		'з',
		'и',
		'й',
		'к',
		'л',
		'м',
		'н',
		'о',
		'п',
		'р',
		'с',
		'т',
		'у',
		'ф',
		'х',
		'ц',
		'ч',
		'ш',
		'щ',
		'ъ',
		'ь',
		'ю',
		'я',
		'А',
		'Б',
		'В',
		'Г',
		'Д',
		'Е',
		'Ж',
		'З',
		'И',
		'Й',
		'К',
		'Л',
		'М',
		'Н',
		'О',
		'П',
		'Р',
		'С',
		'Т',
		'У',
		'Ф',
		'Х',
		'Ц',
		'Ч',
		'Ш',
		'Щ',
		'Ъ',
		'Ь',
		'Ю',
		'Я',
		'ы',
	];

	public $lat = [
		'a',
		'b',
		'v',
		'g',
		'd',
		'e',
		'zh',
		'z',
		'i',
		'y',
		'k',
		'l',
		'm',
		'n',
		'o',
		'p',
		'r',
		's',
		't',
		'u',
		'f',
		'h',
		'ts',
		'ch',
		'sh',
		'sht',
		'a',
		'y',
		'yu',
		'ya',
		'A',
		'B',
		'V',
		'G',
		'D',
		'E',
		'Zh',
		'Z',
		'I',
		'Y',
		'K',
		'L',
		'M',
		'N',
		'O',
		'P',
		'R',
		'S',
		'T',
		'U',
		'F',
		'H',
		'Ts',
		'Ch',
		'Sh',
		'Sht',
		'A',
		'Y',
		'Yu',
		'Ya',
		'y',
	];


	public $arr_sp = [];

	public $arr_kladr = [];

	public $arr_res = [];

	public $res_k;

	public $bad_townid;

	public $logdebug;

	public $source_id;

	public $types = [];

	public $errmsg = [];

	private $nostreet = [ // параметры улицы "Нет улицы"
	                      "ID" => "nostreet",
	                      "CODE" => "00000000000000099",
	                      "NAME" => "Нет улицы",
	                      "AOT_NAME" => "Нет типа",
	                      "AOT_CODE" => 500,
	];

	/** @var DB_Sql */
	public $db;

	/** @var Auth */
	public $perm;

	/** @var Logging */
	protected $logging;

	public function __construct(\DB_Sql $db, \Auth $perm, Logging $logging)
	{
		$this->db = $db;
		$this->perm = $perm;
		$this->source_id = 1; // по умолчанию источник - Техноград
		$this->logdebug = false; // дебаг выключен
		$this->logging = $logging;
	}

	public function SearchCity($search)
	{
		$query = "SELECT ID, CANONICAL_NAME as NAME FROM FOO_GIS.ATD_OBJECT
              WHERE UPPER(NAME) LIKE :1 AND FOO_GIS.CHECKCITY(:2, ID)=1 AND ACTUALSTATUS>0 AND ATDOBJ_TYPE IS NOT NULL ORDER BY 2";
		$param = [
			"1" => "%" . mb_strtoupper($search) . "%",
			"2" => $this->perm->UserID,
		];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	public function SearchStreet($search, $city)
	{
		$query = "SELECT st.ID,  ot.ABBR || '. ' ||st.NAME as NAME
              FROM FOO_GIS.ATD_STREET st, FOO_GIS.ATD_OBJ_TYPE ot
              WHERE st.ATDOBJ_TYPE=ot.CODE AND st.OBJ_ID=:1 AND UPPER(st.NAME) LIKE :2 AND ACTUALSTATUS>0";
		$param = [
			"1" => $city,
			"2" => "%" . mb_strtoupper($search) . "%",
		];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	public function KLADRSearchStreet($search, $city)
	{
		$query = "SELECT ks.ID,  ks.SOCR || '. ' || ks.NAME as NAME
              FROM FOO_GIS.KLADR_MAIN km, FOO_GIS.KLADR_STREET ks, FOO_GIS.ATD_OBJECT ao
              WHERE ao.ID=:1 AND km.CODE=ao.CODE AND ks.MAIN_ID=km.ID AND UPPER(ks.NAME) LIKE :2";
		$param = [
			"1" => $city,
			"2" => "%" . mb_strtoupper($search) . "%",
		];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	public function SearchHouses($city, $street)
	{
		$query = "SELECT ADDR_ID, FOO_GIS.GET_HOUSENUMBER(ADDR_ID) as HOUSE
              FROM FOO_GIS.ATD_ADDRESS WHERE STR_STD1=:1 AND ACTUALSTATUS>0
              ORDER BY NUMBER1, LIT_N1, TANK, LIT_TN, STRUCT, LIT_ST, VLADENIE, LIT_VL";
		$param = [ "1" => $street ];

		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	public function GetAddressInfoInCity($city)
	{
		$query = "SELECT FOO_GIS.GET_ADDRESS(a.ADDR_ID) as ADDR, a.*, r.*,
                     z.CL_NAME as CLNAME,
                     (SELECT ccc. CLOUD_NUM FROM FOO.CLOUDS_ADDRESS ca, FOO.CLUSTERS ccc WHERE ca.OT_ID=ccc.CL_ID AND ca.ADDR_ID=a.ADDR_ID) as CLOUD
              FROM FOO_GIS.ATD_ADDRESS a, FOO_GIS.RTK_ADDRESS r, FOO_GIS.ATD_STREET s,
                   (SELECT t.ADDR_ID, (SELECT tt.CL_NAME FROM FOO.CLUSTERS tt WHERE tt.CL_TYPE=2 CONNECT BY PRIOR tt.CL_PID=tt.CL_ID
                                       START WITH tt.CL_ID=t.CL_ID) as CL_NAME FROM FOO.CLUSTERS t WHERE t.CL_TYPE in (4,5)
                    CONNECT BY PRIOR t.CL_ID = t.CL_PID START WITH t.CL_ID IN (SELECT d.CL_ID from  FOO.CLUSTERS d WHERE d.CL_TYPE=2)) z
              WHERE a.ADDR_ID=r.ADDR_ID AND a.STR_STD1 IN (SELECT s.ID FROM FOO_GIS.ATD_STREET s WHERE s.OBJ_ID=:1)
                    AND a.STR_STD1=s.ID AND a.ADDR_ID=z.ADDR_ID(+)
              ORDER BY lower(s.NAME), a.NUMBER1, a.LIT_N1, a.TANK, a.LIT_TN, a.STRUCT, a.LIT_ST, a.VLADENIE, a.LIT_VL";
		$param = [ "1" => $city ];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	public function getHierarchyByCity($city_id)
	{
		$h = [];
		$a = [];

		$query = "
      SELECT adh.AU_DEPNAME AS MRF, ad.AU_DEPNAME AS RF
      FROM
      FOO_AUTH.AUTH_DEPARTMENTS ad,
      FOO_AUTH.AUTH_DEPARTMENTS adh,
      FOO_AUTH.AUTH_DEPARTMENT_AREAS ada,
      FOO_AUTH.AUTH_DEPARTMENT_CITIES adc

      WHERE
      adc.CITY_ID = :1 AND
      ada.AREA_ID = adc.AREA_ID AND
      ad.AU_DEPID = ada.AU_DEPID AND
      adh.AU_DEPID = ad.AU_DEPPARID
      ";
		$param = [ "1" => $city_id ];
		$a = $this->db->queryArray($query, $param);
		$h = $a[ 0 ];

		$query = "
      SELECT NAME, ATDOBJ_TYPE FROM FOO_GIS.ATD_OBJECT

      CONNECT BY PRIOR PAR_ID = ID START WITH ID = (SELECT PAR_ID FROM FOO_GIS.ATD_OBJECT WHERE ID = :1)
      ";
		$param = [ "1" => $city_id ];
		$a = $this->db->queryArray($query, $param);
		foreach ($a as $v) {
			if (!isset($h[ "AREA" ])) {
				if ($v[ "ATDOBJ_TYPE" ] > 100 && $v[ "ATDOBJ_TYPE" ] < 200) {
					$h[ "AREA" ] = $v[ "NAME" ];
				}
			}
			if (!isset($h[ "MUN" ])) {
				if ($v[ "ATDOBJ_TYPE" ] > 200 && $v[ "ATDOBJ_TYPE" ] < 300) {
					$h[ "MUN" ] = $v[ "NAME" ];
				}
			}
		}

		return ($h);
	}

	public function AddressReportByCity($city_id, &$report, $sep, $encoding, $fttc = 0)
	{
		if ($fttc == 0) {
			$do_fttc = " AND r.TYPE_ODF != 'FTTC' ";
		} else {
			$do_fttc = " AND r.TYPE_ODF = 'FTTC' ";
		}
		$h = $this->getHierarchyByCity($city_id);

		$query = "
        SELECT c.CL_ID, r.*,a.*,s.NAME as STREET, aot.NAME as STREET_TYPE,
        (SELECT distinct NAME FROM FOO_GIS.ATD_OBJECT WHERE ID = :1) as CITY,
        (SELECT distinct aom.NAME FROM FOO_GIS.ATD_OBJECT aom WHERE aom.ATDOBJ_TYPE = 399 CONNECT BY PRIOR aom.PAR_ID = aom.ID START WITH aom.ID = a.OBJ_ID) AS MUNC

        FROM
        FOO_GIS.RTK_ADDRESS r,
        FOO_GIS.ATD_ADDRESS a,
        FOO_GIS.ATD_STREET s,
        FOO_GIS.ATD_OBJ_TYPE aot,
        FOO.CLUSTERS c

        WHERE
        a.ADDR_ID=r.ADDR_ID AND
        a.ADDR_ID = c.ADDR_ID(+) AND
        a.STR_STD1 = s.ID AND
        s.OBJ_ID=:1 AND
        (c.CL_TYPE in (4,5) OR c.CL_TYPE is null) AND
        aot.CODE = s.ATDOBJ_TYPE $do_fttc
        ORDER BY lower(s.NAME), a.NUMBER1, a.LIT_N1, a.TANK, a.LIT_TN, a.STRUCT, a.LIT_ST, a.VLADENIE, a.LIT_VL";


		$param = [ "1" => $city_id ];

		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";

		$num = 1;
		$str = "Nпп" . $sep . "МРФ" . $sep . "РФ" . $sep . "Область" . $sep . "Район" . $sep . "Населенный пункт" . $sep . "Район города" . $sep . "Улица" . $sep . "Тип улицы" . $sep . "Номер дома" . $sep . "Дробь" . $sep . "Номер владения" . $sep . "Номер корпуса" . $sep . "Номер строения" . $sep . "Тип объекта" . $sep . "Наличие подвала" . $sep . "Наличие чердака" . $sep . "Количество этажей" . $sep . "Количество подъездов" . $sep . "Количество квартир на площадке" . $sep . "Количество квартир в доме (число ОТА_MSAN)" . $sep . "Начальный номер квартиры" . $sep . "Количество SOHO" . $sep . "Количество SMP" . $sep . "Количество KK" . $sep . "Индекс АТС" . $sep . "Растояние от объекта до АТС по КК(дм)" . $sep . "Признак сущ технологии" . $sep . "Количество абонентов ШПД (b2c)" . $sep . "Количество абонентов ШПД (b2b)" . $sep . "Количество абонентов SIP" . $sep . "Количество абонентов ОТА" . $sep . "Признак технологии, план" . $sep . "Признак типа дома" . $sep . "Тип кабельного ввода" . $sep . "Количество каналов ввода" . $sep . "Количество магистральных волокон" . $sep . "Номинал сплиттера в ОРШ" . $sep . "Номинал сплиттера на этаже" . $sep . "Число ONT для SoHo к ОРК" . $sep . "Число ONT для СМП ОРК к ОРШ" . $sep . "Число ONT для КК к выделененым ОВ" . $sep . "Идентификатор базового дома" . $sep . "Номер опорной АТС" . "\n";


		foreach ($a as $v) {
			if (($v[ "ODF_TYPE" ]) != "MSAN" && ($v[ "OTA_MSAN" ] > 0)) {
				$v[ "OBJECT_TYPE" ] = "Жилое";
			} else {
				$v[ "OBJECT_TYPE" ] = "Нежилое";
			}
			if ($v[ "BASEMENT" ] == 0) {
				$v[ "BASEMENT" ] = "нет";
			} else {
				$v[ "BASEMENT" ] = "да";
			}
			if ($v[ "LOFT" ] == 0) {
				$v[ "LOFT" ] = "нет";
			} else {
				$v[ "LOFT" ] = "да";
			}
			if (isset($v[ "CL_ID" ])) {
				// адрес в кластере
				$cl_id = $v[ "CL_ID" ];
				$v[ "base" ] = $report[ $cl_id ][ "base" ];
				$v[ "ot_num" ] = $report[ $cl_id ][ "ot_num" ];
				$v[ "ns2" ] = $report[ $cl_id ][ "ns2" ];
				$v[ "ns2e" ] = $report[ $cl_id ][ "ns2e" ];
				$v[ "plen" ] = $report[ $cl_id ][ "plen" ];
				//$v["plen"] = round($report[$cl_id]["plen"]/10,1);
				$v[ "pairs" ] = $report[ $cl_id ][ "pairs" ];

			}
			$str .= $num . $sep . $h[ "MRF" ] . $sep . $h[ "RF" ] . $sep . $h[ "AREA" ] . $sep . $h[ "MUN" ] . $sep . $v[ "CITY" ] . $sep . $v[ "MUNC" ] . $sep . $v[ "STREET" ] . $sep . $v[ "STREET_TYPE" ] . $sep . $v[ "NUMBER1" ] . $v[ "LIT_N1" ] . $sep . $v[ "NUMBER2" ] . $v[ "LIT_N2" ] . $sep . $v[ "VLADENIE" ] . $v[ "LIT_VL" ] . $sep . $v[ "TANK" ] . $v[ "LIT_TN" ] . $sep . $v[ "STRUCT" ] . $v[ "LIT_ST" ] . $sep . $v[ "OBJECT_TYPE" ] . $sep . $v[ "BASEMENT" ] . $sep . $v[ "LOFT" ] . $sep . $v[ "MAX_ETAZH" ] . $sep . $v[ "CNT_PODJEZD" ] . $sep . $v[ "FLAT_AT_FLOOR" ] . $sep . $v[ "OTA_MSAN" ] . $sep . $v[ "FIRST_FLATNUM" ] . $sep . $v[ "CNT_SOHO" ] . $sep . $v[ "CNT_SMP" ] . $sep . $v[ "CNT_KK" ] . $sep . " " . $v[ "ATS_INDEX" ] . " " . $sep . $v[ "plen" ] . $sep . $v[ "CONN_TECHNOLOGY" ] . $sep . $v[ "CNT_BB_B2C" ] . $sep . $v[ "CNT_BB_B2B" ] . $sep . $v[ "CNT_SIP" ] . $sep . $v[ "CNT_OTA" ] . $sep . $v[ "TYPE_ODF" ] . $sep . $v[ "TYPE_ODF" ] . $sep . $v[ "TYPE_INCABLE" ] . $sep . $v[ "NUM_INCABLE" ] . $sep . $v[ "pairs" ] . $sep . $v[ "ns2" ] . $sep . $v[ "ns2e" ] . $sep . $v[ "" ] . $sep . $v[ "" ] . $sep . $v[ "" ] . $sep . $v[ "base" ] . $sep . " " . $v[ "ot_num" ] . " " . "\n";

			$num++;
		}
		if ($encoding != "UTF-8") {
			$str = iconv("UTF-8", $encoding, $str);
		}
		header('Content-type: text/csv; charset=' . $encoding);
		header('Content-Disposition: attachment; filename=Addr_Report.csv');
		echo $str;
	}


	public function sendAddressInfoInCity2CSVHTML($info, $sep, $encoding)
	{

		$head = "ADDR" . $sep . "CNT_PODJEZD" . $sep . "MAX_ETAZH" . $sep . "FLAT_AT_FLOOR" . $sep . "OTA_MSAN" . $sep . "FIRST_FLATNUM" . $sep . "CNT_SOHO" . $sep . "CNT_SMP" . $sep . "CNT_KK" . $sep . "CONN_TECHNOLOGY" . $sep . "CNT_BB_B2C" . $sep . "CNT_BB_B2B" . $sep . "CNT_SIP" . $sep . "CNT_OTA" . $sep . "TYPE_ODF" . $sep . "TYPE_INCABLE" . $sep . "NUM_INCABLE" . $sep . "LOFT" . $sep . "BASEMENT" . $sep . "ATS_INDEX" . $sep . "CLUSTER" . $sep . "CLOUD" . "\n";
		echo $head;

		foreach ($info as $v) {
			$str = $v[ "ADDR" ] . $sep . $v[ "CNT_PODJEZD" ] . $sep . $v[ "MAX_ETAZH" ] . $sep . $v[ "FLAT_AT_FLOOR" ] . $sep . $v[ "OTA_MSAN" ] . $sep . $v[ "FIRST_FLATNUM" ] . $sep . $v[ "CNT_SOHO" ] . $sep . $v[ "CNT_SMP" ] . $sep . $v[ "CNT_KK" ] . $sep . $v[ "CONN_TECHNOLOGY" ] . $sep . $v[ "CNT_BB_B2C" ] . $sep . $v[ "CNT_BB_B2B" ] . $sep . $v[ "CNT_SIP" ] . $sep . $v[ "CNT_OTA" ] . $sep . $v[ "TYPE_ODF" ] . $sep . $v[ "TYPE_INCABLE" ] . $sep . $v[ "NUM_INCABLE" ] . $sep . $v[ "LOFT" ] . $sep . $v[ "BASEMENT" ] . $sep . $v[ "ATS_INDEX" ] . $sep . $v[ "CLNAME" ] . $sep . $v[ "CLOUD" ] . "\n";
			if ($encoding != "UTF-8") {
				$str = iconv("UTF-8", $encoding, $str);
			}
			echo $str;
		}
	}

	public function GetAddressInfoInCity2CSV($city_name, $info, $sep, $dir)
	{

		$filename = $dir . "/" . str_replace($this->cyr, $this->lat, $city_name) . ".csv";
		$fd = fopen($filename, 'w');
		$head = "ADDR" . $sep . "CNT_PODJEZD" . $sep . "MAX_ETAZH" . $sep . "FLAT_AT_FLOOR" . $sep . "OTA_MSAN" . $sep . "FIRST_FLATNUM" . $sep . "CNT_SOHO" . $sep . "CNT_SMP" . $sep . "CNT_KK" . $sep . "CONN_TECHNOLOGY" . $sep . "CNT_BB_B2C" . $sep . "CNT_BB_B2B" . $sep . "CNT_SIP" . $sep . "CNT_OTA" . $sep . "TYPE_ODF" . $sep . "TYPE_INCABLE" . $sep . "NUM_INCABLE" . $sep . "LOFT" . $sep . "BASEMENT" . $sep . "ATS_INDEX" . $sep . "CLUSTER" . $sep . "CLOUD" . "\n";
		fwrite($fd, $head);

		foreach ($info as $v) {
			$str = $v[ "ADDR" ] . $sep . $v[ "CNT_PODJEZD" ] . $sep . $v[ "MAX_ETAZH" ] . $sep . $v[ "FLAT_AT_FLOOR" ] . $sep . $v[ "OTA_MSAN" ] . $sep . $v[ "FIRST_FLATNUM" ] . $sep . $v[ "CNT_SOHO" ] . $sep . $v[ "CNT_SMP" ] . $sep . $v[ "CNT_KK" ] . $sep . $v[ "CONN_TECHNOLOGY" ] . $sep . $v[ "CNT_BB_B2C" ] . $sep . $v[ "CNT_BB_B2B" ] . $sep . $v[ "CNT_SIP" ] . $sep . $v[ "CNT_OTA" ] . $sep . $v[ "TYPE_ODF" ] . $sep . $v[ "TYPE_INCABLE" ] . $sep . $v[ "NUM_INCABLE" ] . $sep . $v[ "LOFT" ] . $sep . $v[ "BASEMENT" ] . $sep . $v[ "ATS_INDEX" ] . $sep . $v[ "CLNAME" ] . $sep . $v[ "CLOUD" ] . "\n";
			fwrite($fd, $str);
		}

		fclose($fd);
	}

	/*
      *
      */
	public function loadKLADR($file)
	{
		$retvar = "";

		for ($i = 0; $i < count($file[ 'name' ]); $i++) {

			switch (mb_strtolower($file[ 'name' ][ $i ])) {
				case "kladr.zip":
					$nameo = $file[ 'tmp_name' ][ $i ];
					$namen = "$nameo.unzip";
					//echo "<br>usr/bin/unzip -p $nameo > $namen<br>";
					if (system("/usr/bin/unzip -p $nameo > $namen", $retvar) !== false) {
						$namen = "$nameo.unzip";
						echo "<br>Файл " . $file[ 'name' ][ $i ] . " разархивирован<br>";
					} else {
						echo "<br>Ошибка разархивирования загруженного файла $nameo с диагностикой $retvar<br>";
						break;
					}
				case "kladr.dbf":
					$ret = $this->loadKLADR_MAIN($namen, $file[ 'name' ][ $i ]);
					if ($ret != 0) {
						return (-1);
					}

					break;
				case "street.zip":
					$nameo = $file[ 'tmp_name' ][ $i ];
					$namen = "$nameo.unzip";
					//echo "<br>usr/bin/unzip -p $nameo > $namen<br>";
					if (system("/usr/bin/unzip -p $nameo > $namen", $retvar) !== false) {
						$namen = "$nameo.unzip";
					} else {
						echo "<br>Ошибка разархивирования загруженного файла $nameo с диагностикой $retvar<br>";
						break;
					}
				case "street.dbf":
					$ret = $this->loadKLADR_STREET($namen, $file[ 'name' ][ $i ]);
					if ($ret != 0) {
						return (-1);
					}

					break;
				/* case "ALTNAMES.DBF":
                    $ret = $this->loadKLADR_ALTNAMES($namen,$file['name'][$i]);
                    if($ret != 0) {
                        return(-1);
                    }

                    break;*/
			}

		}
		$this->db->commit();
		if (strstr($namen, ".unzip") !== false) {
			echo "<br>Удаление временного файла $namen<br>";
			system("/bin/rm $namen", $retvar);
			echo "<br>$retvar<br>";
		}
	}

	/*
       *
       */
	private function iconvKLADR($array)
	{
		if (is_array($array)) {
			return array_map([ 'Address', 'iconvKLADR' ], $array);
		} else {
			return iconv('CP866', 'UTF-8', $array);
		}
	}

	/*
    *
    */
	public function loadKLADR_MAIN($filename, $name)
	{


		$dbf = dbase_open($filename, 0);

		if ($dbf) {
			echo "<br>" . date(DATE_RFC2822) . " Файл " . $name . " открыт<br>";
			echo "<br>" . date(DATE_RFC2822) . " Загрузка населенных пунктов<br>";
			flush();

			$record_numbers = dbase_numrecords($dbf);
			echo "<br>" . date(DATE_RFC2822) . " В файле $record_numbers записей<br>";
			flush();
			$query = "delete from FOO_GIS.KLADR_MAIN";
			$param = [];
			$a = $this->db->query($query, $param);
			//echo "<br>$query<br>";
			for ($i = 1; $i <= $record_numbers; $i++) {
				$row = $this->iconvKLADR(dbase_get_record_with_names($dbf, $i));
				if (
					!isset($row[ 'NAME' ]) ||
					!isset($row[ 'SOCR' ]) ||
					!isset($row[ 'CODE' ]) ||
					!isset($row[ 'INDEX' ]) ||
					!isset($row[ 'GNINMB' ]) ||
					!isset($row[ 'UNO' ]) ||
					!isset($row[ 'OCATD' ])
				) {
					echo "<br>" . date(DATE_RFC2822) . " Ошибка, неверный формат файла $name<br>";
					flush();
					dbase_close($dbf);

					return (-1);

				}

				//echo "<pre>"; print_r($row); echo "</pre>";
				$query = "
                INSERT INTO FOO_GIS.KLADR_MAIN (ID, NAME,SOCR,CODE,\"INDEX\",GNINMB,UNO,OCATD,STATUS)
                VALUES
                (FOO_GIS.KLADR_MAIN_SEQ.nextval,trim(:1),trim(:2),:3,:4,:5,:6,:7,:8)";
				$param = [
					"1" => $row[ "NAME" ],
					"2" => $row[ "SOCR" ],
					"3" => $row[ "CODE" ],
					"4" => $row[ "INDEX" ],
					"5" => $row[ "GNINMB" ],
					"6" => $row[ "UNO" ],
					"7" => $row[ "OCATD" ],
					"8" => $row[ "STATUS" ],
				];

				$a = $this->db->query($query, $param);
				if ($i % (round($record_numbers / 20)) == 0) {
					echo ".";
				}
				//echo "<br>$query<br>";
				//echo "<pre>"; print_r($param); echo "</pre>";
			}
			echo "<br>" . date(DATE_RFC2822) . " Загружено " . ($i - 1) . " населенных пунктов<br>";
			flush();
			dbase_close($dbf);
			if ($i > 1) {
				echo "<br>" . date(DATE_RFC2822) . " Файл " . $name . " загружен и закрыт<br>";
				flush();
				$query = "
                MERGE INTO FOO_GIS.KLADR_MAIN b
                USING (select * from FOO_GIS.KLADR_MAIN) a
                ON (a.CODE = FOO_GIS.GET_KLADR_PARENT(b.CODE))
                WHEN MATCHED THEN UPDATE SET b.PAR_ID = a.ID";
				$param = [];
				$a = $this->db->query($query, $param);
				echo "<br>" . date(DATE_RFC2822) . " Населенные пункты объединены в иерархическое дерево<br>";
				flush();
				$query = "update FOO_GIS.KLADR_MAIN set STATE=to_number(substr(CODE,12,2))";
				$param = [];
				$a = $this->db->query($query, $param);
				echo "<br>" . date(DATE_RFC2822) . " Обновлены статусы загруженных населенных пунктов<br>";
				flush();
			}
		} else {
			echo "<br>" . date(DATE_RFC2822) . " Ошибка открытия файла " . $name . "<br>";
			flush();

			return (-1);
		}

		return (0);
	}

	/*
       *
       */
	public function loadKLADR_STREET($filename, $name)
	{


		$dbf = dbase_open($filename, 0);

		if ($dbf) {
			echo "<br>" . date(DATE_RFC2822) . " Файл " . $name . " открыт<br>";
			echo "<br>" . date(DATE_RFC2822) . " Загрузка улиц населенных пунктов<br>";
			flush();

			$record_numbers = dbase_numrecords($dbf);
			echo "<br>" . date(DATE_RFC2822) . " В файле $record_numbers записей<br>";
			flush();
			$query = "delete from FOO_GIS.KLADR_STREET";
			$param = [];
			$a = $this->db->query($query, $param);
			//echo "<br>$query<br>";
			for ($i = 1; $i <= $record_numbers; $i++) {
				$row = $this->iconvKLADR(dbase_get_record_with_names($dbf, $i));


				if (
					!isset($row[ 'NAME' ]) ||
					!isset($row[ 'SOCR' ]) ||
					!isset($row[ 'CODE' ]) ||
					!isset($row[ 'INDEX' ]) ||
					!isset($row[ 'GNINMB' ]) ||
					!isset($row[ 'UNO' ]) ||
					!isset($row[ 'OCATD' ])
				) {
					echo "<br>" . date(DATE_RFC2822) . " Ошибка, неверный формат файла $name<br>";
					flush();
					dbase_close($dbf);

					return (-1);

				}

				//echo "<pre>"; print_r($row); echo "</pre>";
				$query = "
                INSERT INTO FOO_GIS.KLADR_STREET (ID, NAME,SOCR,CODE,\"INDEX\",GNINMB,UNO,OCATD)
                VALUES
                (FOO_GIS.KLADR_STREET_SEQ.nextval,trim(:1),trim(:2),:3,:4,:5,:6,:7)";
				$param = [
					"1" => $row[ "NAME" ],
					"2" => $row[ "SOCR" ],
					"3" => $row[ "CODE" ],
					"4" => $row[ "INDEX" ],
					"5" => $row[ "GNINMB" ],
					"6" => $row[ "UNO" ],
					"7" => $row[ "OCATD" ],
				];

				$a = $this->db->query($query, $param);
				if ($i % (round($record_numbers / 20)) == 0) {
					echo ".";
				}
				//echo "<br>$query<br>";
				//echo "<pre>"; print_r($param); echo "</pre>";

			}

			echo "<br>" . date(DATE_RFC2822) . " Загружено " . ($i - 1) . " улиц населенных пунктов<br>";
			flush();
			dbase_close($dbf);
			if ($i > 1) {
				echo "<br>" . date(DATE_RFC2822) . " Файл " . $name . " загружен и закрыт<br>";
				flush();
				$query = "
                MERGE INTO FOO_GIS.KLADR_STREET b
                USING (select * from FOO_GIS.KLADR_MAIN) a
                ON (a.CODE = FOO_GIS.GET_KLADR_PARENT(b.CODE, 17))
                WHEN MATCHED THEN UPDATE SET b.MAIN_ID = a.ID";
				$param = [];
				$a = $this->db->query($query, $param);
				echo "<br>" . date(
						DATE_RFC2822
					) . " Улицы объединены в иерархическое дерево с населенными пунктами<br>";
				flush();
				$query = "update FOO_GIS.KLADR_STREET set STATE=to_number(substr(CODE,16,2))";
				$param = [];
				$a = $this->db->query($query, $param);
				echo "<br>" . date(DATE_RFC2822) . " Обновлены статусы загруженных улиц<br>";
				flush();
			}

		} else {
			echo "<br>" . date(DATE_RFC2822) . " Ошибка открытия файла " . $name . "<br>";
			flush();

			return (-1);
		}

		return (0);
	}

	/*
           *
           */
	public function loadKLADR_ALTNAMES($filename, $name)
	{


		$dbf = dbase_open($filename, 0);

		if ($dbf) {
			echo "<br>" . date(DATE_RFC2822) . " Файл " . $name . " открыт<br>";
			echo "<br>" . date(DATE_RFC2822) . " Загрузка изменений улиц или населенных пунктов<br>";
			flush();
			$record_numbers = dbase_numrecords($dbf);
			echo "<br>" . date(DATE_RFC2822) . " В файле $record_numbers записей<br>";
			flush();
			$query = "delete from FOO_GIS.KLADR_ALTNAMES";
			$param = [];
			$a = $this->db->query($query, $param);
			//echo "<br>$query<br>";
			for ($i = 1; $i <= $record_numbers; $i++) {
				$row = $this->iconvKLADR(dbase_get_record_with_names($dbf, $i));


				if (
					!isset($row[ 'OLDCODE' ]) ||
					!isset($row[ 'NEWCODE' ]) ||
					!isset($row[ 'LEVEL' ])
				) {
					echo "<br>" . date(DATE_RFC2822) . " Ошибка, неверный формат файла $name<br>";
					flush();
					dbase_close($dbf);

					return (-1);

				}

				//echo "<pre>"; print_r($row); echo "</pre>";
				$query = "
                INSERT INTO FOO_GIS.KLADR_ALTNAMES (ID, OLDCODE, NEWCODE, LEVELID)
                VALUES
                (:4,:1,:2,:3)";
				$param = [
					"1" => $row[ "OLDCODE" ],
					"2" => $row[ "NEWCODE" ],
					"3" => $row[ "LEVEL" ],
					"4" => $i,
				];

				$a = $this->db->query($query, $param);
				if ($i % (round($record_numbers / 20)) == 0) {
					echo ".";
				}
			}

			echo "<br>" . date(DATE_RFC2822) . " Загружено " . ($i - 1) . " изменений улиц или населенных пунктов<br>";
			flush();
			dbase_close($dbf);
			echo "<br>" . date(DATE_RFC2822) . " Файл " . $name . " загружен и закрыт<br>";
			flush();
		} else {
			echo "<br>" . date(DATE_RFC2822) . " Ошибка открытия файла " . $name . "<br>";
			flush();

			return (-1);
		}

		return (0);
	}

	/*
             *
             */
	public function checkKLADRcities()
	{

		$query = "
          select ID, trim(lower(regexp_replace(SYS_CONNECT_BY_PATH(NAME, ';'), ';([^;]+) /',''))) as SPNAME
          from FOO_GIS.ATD_OBJECT
          CONNECT BY PRIOR ID = PAR_ID START WITH PAR_ID is null
        ";
		$param = [];
		$a = $this->db->queryArray($query, $param);
		$query = "
          select trim(lower(SYS_CONNECT_BY_PATH(NAME, ';'))) as KLNAME
          from FOO_GIS.KLADR_MAIN
          CONNECT BY PRIOR ID = PAR_ID START WITH PAR_ID is null
        ";
		$param = [];

		$b = $this->db->queryArray($query, $param);
		echo "<br>Загружено " . count($a) . " улиц СП и " . count($b) . " улиц КЛАДР. Сверка<br>";
		for ($i = 0; $i < count($a); $i++) {
			for ($j = 0; $j < count($b); $j++) {
				if ($this->mb_strcmp($a[ $i ][ "SPNAME" ], $b[ $j ][ "KLNAME" ]) == 0) {
					echo "<br>" . $a[ $i ][ "ID" ] . " " . $a[ $i ][ "SPNAME" ] . " " . $b[ $j ][ "KLNAME" ] . " " . $b[ $j ][ "CODE" ] . "<br>";
				}
			}

		}
	}

	/*
    * Поиск и связывание совпадающих улиц СитПлан
    */
	private function preSPdoubles()
	{

		for ($i = 0; $i < count($this->arr_sp); $i++) {
			if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
				continue;
			}
			for ($j = $i + 1; $j < count($this->arr_sp); $j++) {
				if (!isset($this->arr_sp[ $j ][ "NAME" ])) {
					continue;
				}
				// не совпали типы -продолжаем
				if ($this->mb_strcmp($this->arr_sp[ $i ][ "ABBR" ], $this->arr_sp[ $j ][ "ABBR" ]) != 0) {
					continue;
				} // совпали и типы и развания - связываем
				if ($this->mb_strcmp($this->arr_sp[ $i ][ "CH_NAME" ], $this->arr_sp[ $j ][ "CH_NAME" ]) == 0) {
					$this->arr_sp[ $i ][ "have_doubles" ][] = $j;
					$this->arr_sp[ $j ][ "is_doubles" ][] = $i;

					//echo "<br>СитПлан: дубли полные ($i-$j) (". $this->arr_sp[$i]["ABBR"] .") (". $this->arr_sp[$i]["CH_NAME"] . ") и (". $this->arr_sp[$j]["ABBR"] .") (".$this->arr_sp[$j]["CH_NAME"].")<br>";
				} // не совпали названия, но вес вхождения <=1 - связываем
				/*
                else if($this->calcInstrKLADR($this->arr_sp[$i]["CH_NAME"], $this->arr_sp[$j]["CH_NAME"]) <= 1) {
                    $this->arr_sp[$i]["doubles"][$j] = $j;
                    $this->arr_sp[$j]["doubles"][$i] = $i;
                    echo "<br>дубли по весу ($i-$j) (". $this->arr_sp[$i]["ABBR"] .") (". $this->arr_sp[$i]["CH_NAME"] . ") и (". $this->arr_sp[$j]["ABBR"] .") (".$this->arr_sp[$j]["CH_NAME"].")<br>";
                }
                */
			}
		}
	}

	private function connectSPwKLADR($i, $j, $level = 0, $res = 0, $len = 0)
	{
		//echo "<br>провязка " . $this->arr_sp[$i]["CH_NAME"] ."<br>";
		//echo "<pre>";
		//print_r($this->arr_sp[$i]["have_doubles"]);
		//echo "</pre>";
		if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
			return;
		}
		$this->arr_res[ $this->res_k ][ "res" ] = $res;
		$this->arr_res[ $this->res_k ][ "len" ] = $len;
		$this->arr_res[ $this->res_k ][ "double" ] = $level;
		$this->arr_res[ $this->res_k ][ "ID" ] = $this->arr_sp[ $i ][ "ID" ];
		$this->arr_res[ $this->res_k ][ "SOCR" ] = $this->arr_kladr[ $j ][ "SOCR" ];
		$this->arr_res[ $this->res_k ][ "ABBR" ] = $this->arr_sp[ $i ][ "ABBR" ];
		$this->arr_res[ $this->res_k ][ "KLNAME" ] = $this->arr_kladr[ $j ][ "NAME" ];
		$this->arr_res[ $this->res_k ][ "SPNAME" ] = $this->arr_sp[ $i ][ "NAME" ] . (($level > 0) ? " (дубль)" : "");
		$this->arr_res[ $this->res_k ][ "KLCHNAME" ] = $this->arr_kladr[ $j ][ "CH_NAME" ];
		$this->arr_res[ $this->res_k ][ "SPCHNAME" ] = $this->arr_sp[ $i ][ "CH_NAME" ];
		$this->arr_res[ $this->res_k ][ "CODE" ] = $this->arr_kladr[ $j ][ "CODE" ];
		$this->arr_res[ $this->res_k ][ "cnt" ] = $this->res_k + 1;
		$this->res_k++;
		foreach ($this->arr_sp[ $i ][ "have_doubles" ] as $v) {
			//echo "<br>провязка дублей<br>";
			$this->connectSPwKLADR($v, $j, 1, $res, $len);
		}
		unset($this->arr_sp[ $i ][ "NAME" ]);
		if ($level == 0) {
			unset($this->arr_kladr[ $j ][ "NAME" ]);
		}
		//echo "<pre>";
		//print_r($this->arr_sp[$i]["have_doubles"]);
		//echo "</pre>";
	}

	/*
     * Поиск и связывание совпадающих улиц СитПлан разных типов при условии однозначности в КЛАДР
     */
	private function preSPdoublesKLADR()
	{
		unset($this->arr_res);
		$this->res_k = 0;

		for ($i = 0; $i < count($this->arr_sp); $i++) {
			if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
				continue;
			}
			for ($j = $i + 1; $j < count($this->arr_sp); $j++) {
				if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
					continue;
				}
				// совпали названия
				if ($this->mb_strcmp($this->arr_sp[ $i ][ "CH_NAME" ], $this->arr_sp[ $j ][ "CH_NAME" ]) == 0) {
					// совпавшие типы уже были обработаны при полном совпадении, пропускаем
					if ($this->mb_strcmp($this->arr_sp[ $i ][ "ABBR" ], $this->arr_sp[ $j ][ "ABBR" ]) == 0) {
						continue;
					}
					$sz = -1;
					$siz = -1;
					$sjz = -1;
					$cnt = 0;
					$cnti = 0;
					$cntj = 0;
					// цикл поиска по улицам КЛАДР
					for ($z = 0; $z < count($this->arr_kladr); $z++) {
						// нашли совпадение
						if ($this->mb_strcmp(
								$this->arr_sp[ $i ][ "CH_NAME" ],
								$this->arr_kladr[ $z ][ "CH_NAME" ]
							) == 0
						) {
							$sz = $z;
							$cnt++;

							// совпали типы с улицей $i СитПлан
							if ($this->mb_strcmp(
									$this->arr_sp[ $i ][ "ABBR" ],
									$this->arr_kladr[ $z ][ "SOCR" ]
								) == 0
							) {

								$siz = $z;
								$cnti++;
							} // совпали типы с улицей $j СитПлан
							else {
								if ($this->mb_strcmp(
										$this->arr_sp[ $j ][ "ABBR" ],
										$this->arr_kladr[ $z ][ "SOCR" ]
									) == 0
								) {

									$sjz = $z;
									$cntj++;
								}
							}

						}
					}
					// найдено единственное совпадение с КЛАДР улицы $i, связываем с ней
					if ($cnt == 1) {


						if ($cnti == 1) {
							//echo "<br>СитПлан: дубли по имени и однозначному совпадению в КЛАДР ($i-$j) (". $this->arr_sp[$i]["ABBR"] .") (". $this->arr_sp[$i]["CH_NAME"] . ") и (". $this->arr_sp[$j]["ABBR"] .") (".$this->arr_sp[$j]["CH_NAME"].") КЛАДР (".$this->arr_kladr[$siz]["SOCR"].") (".$this->arr_kladr[$siz]["CH_NAME"].")<br>";
							$this->arr_sp[ $i ][ "have_doubles" ][] = $j;
							$this->arr_sp[ $j ][ "is_doubles" ][] = $i;
							//$this->connectSPwKLADR($i, $siz);
						} // найдено единственное совпадение с КЛАДР улицы $j, связываем с ней
						else {
							if ($cntj == 1) {
								//echo "<br>СитПлан: дубли по имени и однозначному совпадению в КЛАДР ($i-$j) (". $this->arr_sp[$i]["ABBR"] .") (". $this->arr_sp[$i]["CH_NAME"] . ") и (". $this->arr_sp[$j]["ABBR"] .") (".$this->arr_sp[$j]["CH_NAME"].") КЛАДР (".$this->arr_kladr[$sjz]["SOCR"].") (".$this->arr_kladr[$sjz]["CH_NAME"].")<br>";
								$this->arr_sp[ $j ][ "have_doubles" ][] = $i;
								$this->arr_sp[ $i ][ "is_doubles" ][] = $j;
								//$this->connectSPwKLADR($j, $sjz);
							}
						}
					}
				}
			}
		}

		return;
	}

	/**
	 * Найти пригород из названия улицы в скобках
	 *
	 * @param $city_id
	 * @param $st
	 */
	public function findCityByStreet($city_id, &$st)
	{
		$st_types = [
			"пгт",
			"снт",
			"пос",
			"п",
			"г",
			"с",
		];
		$str = $st[ "NAME" ];
		if (mb_strpos($str, "(") == false) {
			// это строка вида "г.Колпино, Губина" без скобок
			$b = mb_split(",", $str);
			if (count($b) == 2) {
				// делаем строку вида Губина (г.Колпино)
				$str = $b[ 1 ] . " (" . $b[ 0 ] . ")";
				//echo "<br>$str<br>";
			}
		}
		//echo "<br>street=$str<br>";
		// короткое имя
		$st[ "SHORTNAME" ] = trim(mb_ereg_replace("\((.*)\)", "", $str));
		/* нашли конструкцию (текст) в названии улицы:
                   1. Выделяем название (нормализуя и удаляя сокращения)
                   2. Ищем город уровнем ниже
                   3. Ищем город на уровне
                   4. Для Спб. ищем в Ленинградской области (хардкод)
                   5. Если не нашли - заменяем строкой без скобок для дальнейшего поиска
                   */
		//echo "<br>($str)<br>";
		// выделяем название в скобках
		$str = mb_strtolower(
			trim(mb_substr($str, mb_strpos($str, "(") + 1, mb_strpos($str, ")") - mb_strpos($str, "(") - 1))
		);
		$city = $str;

		$query = "select km.*, ao.NAME from FOO_GIS.ATD_OBJECT ao, FOO_GIS.KLADR_MAIN km where
                ao.ID = :1 AND
                FOO_GIS.GET_KLADR_PARENT(km.CODE) = ao.CODE AND
                lower(km.NAME) = :2";
		$param = [ "1" => $city_id, "2" => $city ];
		$a = $this->db->queryArray($query, $param);
		if (count($a) == 1) {
			if ($a[ 0 ][ "STATE" ] != 0) {
				// перенос
				$st[ "CITY_ID" ] = $city_id;
				$st[ "CITY_NAME" ] = $a[ 0 ][ "NAME" ];
				$st[ "SHORTNAME" ] = $st[ "NAME" ];
				$st[ "FICT" ] = 1; // признак фиктивно найденного города

				return (true);
			}
		}
		//echo "<br>city=$city<br>";
		// пытаемся выделить тип

		$a = mb_split("\.", $str);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		if (count($a) > 1) {
			$city = trim($a[ 1 ]);
			// в тексте встретилась точка, пытаемся сравнить с сокращением
			for ($i = 0; $i < count($st_types); $i++) {
				if ($this->mb_strcmp($a[ 0 ], $st_types[ $i ]) == 0) {
					$type = $a[ 0 ];
					$city = trim($a[ 1 ]);
					//echo "<br>type=($type) city=$city<br>";
					break;
				}
			}

		}
		$b = mb_split(",", $city);
		if (count($b) > 1) {
			$city = $b[ 0 ];
		}
		// найти город уровнем ниже текущего города
		if (isset($type)) {
			// поискать с типом
			$query = "
          select
          ao.ID,
          ao.NAME
          from
          FOO_GIS.ATD_OBJECT ao,FOO_GIS.ATD_OBJ_TYPE aot
          where
          lower(ao.NAME) = lower(:2) and
          aot.CODE = ao.ATDOBJ_TYPE and
          lower(aot.ABBR) = lower(:3)
          connect by prior ao.ID = ao.PAR_ID
          start with ao.ID = :1";

			$param = [
				"1" => $city_id,
				"2" => $city,
				"3" => $type,
			];

			$a = $this->db->queryArray($query, $param);

			if (count($a) == 1) {
				// нашли единственный город уровнем ниже

				$st[ "CITY_ID" ] = $a[ 0 ][ "ID" ];
				$st[ "CITY_NAME" ] = $a[ 0 ][ "NAME" ];
				//echo "<br>Найден город с типом уровнем ниже<br>";
				//echo "<pre>";
				//print_r($st);
				//echo "</pre>";

				return (true);
			}
		}
		// поискать без типа
		$query = "
          select
          ao.ID,
          ao.NAME
          from
          FOO_GIS.ATD_OBJECT ao
          where
          lower(ao.NAME) = lower(:2)
          connect by prior ao.ID = ao.PAR_ID
          start with ao.ID = :1";

		$param = [
			"1" => $city_id,
			"2" => $city,
		];

		$a = $this->db->queryArray($query, $param);

		if (count($a) == 1) {
			// нашли единственный город уровнем ниже
			$st[ "CITY_ID" ] = $a[ 0 ][ "ID" ];
			$st[ "CITY_NAME" ] = $a[ 0 ][ "NAME" ];
			//echo "<br>Найден город без типа уровнем ниже<br>";
			//echo "<pre>";
			//print_r($st);
			//echo "</pre>";

			return (true);
		}
		// найти город на уровне текущего города
		if (isset($type)) {
			// поискать с типом
			$query = "
          select
          ao.ID,
          ao.NAME
          from
          FOO_GIS.ATD_OBJECT ao,FOO_GIS.ATD_OBJ_TYPE aot
          where
          lower(ao.NAME) = lower(:2) and
          aot.CODE = ao.ATDOBJ_TYPE and
          lower(aot.ABBR) = lower(:3)
          connect by prior ao.ID = ao.PAR_ID
          start with ao.ID = (select PAR_ID from FOO_GIS.ATD_OBJECT where ID = :1)";

			$param = [
				"1" => $city_id,
				"2" => $city,
				"3" => $type,
			];

			$a = $this->db->queryArray($query, $param);

			if (count($a) == 1) {
				// нашли единственный город уровнем рядом
				$st[ "CITY_ID" ] = $a[ 0 ][ "ID" ];
				$st[ "CITY_NAME" ] = $a[ 0 ][ "NAME" ];
				//echo "<br>Найден город с типом уровнем рядом<br>";
				//echo "<pre>";
				//print_r($st);
				//echo "</pre>";

				return (true);
			}
		}

		// поискать без типа
		$query = "
          select
          ao.ID,
          ao.NAME
          from
          FOO_GIS.ATD_OBJECT ao
          where lower(ao.NAME) = lower(:2)
          connect by prior ao.ID = ao.PAR_ID
          start with ao.ID = (select PAR_ID from FOO_GIS.ATD_OBJECT where ID = :1)";

		$param = [
			"1" => $city_id,
			"2" => $city,
		];

		$a = $this->db->queryArray($query, $param);

		if (count($a) == 1) {
			// нашли единственный город уровнем рядом
			$st[ "CITY_ID" ] = $a[ 0 ][ "ID" ];
			$st[ "CITY_NAME" ] = $a[ 0 ][ "NAME" ];
			//echo "<br>Найден город без типа уровнем рядом<br>";
			//echo "<pre>";
			//print_r($st);
			//echo "</pre>";

			return (true);
		}
		// хпрдкод для Москвы и Спб
		// поискать без типа
		$query = "select CODE from FOO_GIS.ATD_OBJECT where ID = :1";
		$param = [ "1" => $city_id ];
		$a = $this->db->queryArray($query, $param);
		$code = $a[ 0 ][ "CODE" ];
		// Москва
		if ($code == "7700000000000") {
			// область
			$parcode = "5000000000000";
		}
		// Спб
		if ($code == "7800000000000") {
			// область
			$parcode = "4700000000000";
		}
		if (isset($parcode)) {
			// поискать в области рядом
			if (isset($type)) {
				// поискать с типом
				$query = "
          select
          ao.ID,
          ao.NAME
          from
          FOO_GIS.ATD_OBJECT ao,FOO_GIS.ATD_OBJ_TYPE aot
          where lower(ao.NAME) = lower(:2) and
          aot.CODE = ao.ATDOBJ_TYPE and
          lower(aot.ABBR) = lower(:3)
          connect by prior ao.ID = ao.PAR_ID
          start with ao.CODE = :1";

				$param = [
					"1" => $parcode,
					"2" => $city,
					"3" => $type,
				];

				$a = $this->db->queryArray($query, $param);

				if (count($a) == 1) {
					// нашли единственный город уровнем рядом
					$st[ "CITY_ID" ] = $a[ 0 ][ "ID" ];
					$st[ "CITY_NAME" ] = $a[ 0 ][ "NAME" ];
					//echo "<br>Найден город с типом уровнем в области рядом<br>";
					//echo "<pre>";
					//print_r($st);
					//echo "</pre>";

					return (true);
				}
			}
			// поискать в области без типа
			$query = "
          select
          ao.ID,
          ao.NAME
          from
          FOO_GIS.ATD_OBJECT ao
          where lower(ao.NAME) = lower(:2)
          connect by prior ao.ID = ao.PAR_ID
          start with ao.CODE = :1";

			$param = [
				"1" => $parcode,
				"2" => $city,
			];

			$a = $this->db->queryArray($query, $param);

			if (count($a) == 1) {
				// нашли единственный город уровнем рядом
				$st[ "CITY_ID" ] = $a[ 0 ][ "ID" ];
				$st[ "CITY_NAME" ] = $a[ 0 ][ "NAME" ];
				//echo "<br>Найден город без типа уровнем в области рядом<br>";
				//echo "<pre>";
				//print_r($st);
				//echo "</pre>";

				return (true);
			}
		}
		$st[ "SHORTNAME" ] = $st[ "NAME" ];

		return (false);
	}

	/**
	 * Перенести улицы города вида <название (пригород)> в пригород
	 *
	 * @param $city_id
	 * @param $doupdate
	 */
	public function moveKLADRstreet($city_id, $doupdate)
	{
		$report = [];
		$reps = [];
		$cities = [];
		$total = 0;
		$moved = 0;
		$city_ids = [];

		unset($this->arr_res);
		$this->res_k = 0;

		// выбрать улицы из БД

		$query = "
              SELECT st.ID, st.NAME, nvl(aot.ABBR,'ул') as ABBR
              FROM FOO_GIS.ATD_STREET st, FOO_GIS.ATD_OBJ_TYPE aot
              WHERE st.OBJ_ID = :1 AND
              (st.NAME like '%(%)%' or st.NAME like '%,%') AND
              nvl(st.ATDOBJ_TYPE,0) = aot.CODE(+)
              order by st.NAME";
		$param = [
			"1" => $city_id,
		];
		$this->arr_sp = $this->db->queryArray($query, $param);
		//echo "<br>Загружено " . count($this->arr_sp) . " улиц<br>";
		$total = count($this->arr_sp);
		//echo "<pre>";
		//print_r($this->arr_sp);
		//echo "</pre>";

		//echo "<br>-----------------------------<br>";
		for ($i = 0; $i < count($this->arr_sp); $i++) {
			if ($this->findCityByStreet($city_id, $this->arr_sp[ $i ]) == true) {
				$moved++;
				$city_ids[ $this->arr_sp[ $i ][ "CITY_ID" ] ] = 1;
				if ($doupdate) {
					$this->moveObjectsByStreet(
						$this->arr_sp[ $i ][ "ID" ],
						$city_id,
						$this->arr_sp[ $i ][ "CITY_ID" ],
						$this->arr_sp[ $i ][ "SHORTNAME" ],
						$this->arr_sp[ $i ][ "FICT" ]

					);
				}
			}
		}


		//echo "<pre>";
		//print_r($cities);
		//echo "</pre>";
		//echo "<br>-----------------------------<br>";

		//echo "<pre>";
		//print_r($this->arr_sp);
		//echo "</pre>";

		$report[ "total" ] = $total;
		$report[ "moved" ] = $moved;
		$report[ "cities" ] = count($city_ids);
		$report[ "names" ] = $this->arr_sp;

		return ($report);
	}

	/**
	 * перенести улицу с домами в новый населенный пункт
	 *
	 * @param $st_id
	 * @param $city_id
	 * @param $new_city_id
	 * @param $street_name
	 */
	public function moveObjectsByStreet($st_id, $city_id, $new_city_id, $street_name, $fict)
	{
		if ($city_id != $new_city_id) {
			// перенести элементы кластера ОРК и ОРШ домов на улице
			$query = "MERGE INTO FOO.CLUSTERS cl
                    USING (select * from FOO_GIS.ATD_ADDRESS) ad
                    ON (ad.STR_STD1 = :st_id and
                    CL.ADDR_ID is not null and
                    cl.ADDR_ID = ad.ADDR_ID and
                    cl.CL_TYPE in (4,5))
                    WHEN MATCHED THEN
                        UPDATE SET
                        cl.CITY_ID = :new_city_id";
			$param = [
				"st_id" => $st_id,
				"new_city_id" => $new_city_id,
			];
			$this->db->query($query, $param);
			//echo "<br>query = $query<br>";
			//echo "<pre>";
			//print_r($param);
			//echo "</pre>";
			// перенести дома на улице
			$query = "update FOO_GIS.ATD_ADDRESS set OBJ_ID = :new_city_id where STR_STD1 = :st_id";
			$param = [
				"st_id" => $st_id,
				"new_city_id" => $new_city_id,
			];
			$this->db->query($query, $param);
			//echo "<br>query = $query<br>";
			//echo "<pre>";
			//print_r($param);
			//echo "</pre>";


			// перенести улицу
			$query = "update FOO_GIS.ATD_STREET set OBJ_ID = :new_city_id, NAME = :street_name where ID = :st_id";
			$param = [
				"st_id" => $st_id,
				"new_city_id" => $new_city_id,
				"street_name" => $street_name,
			];
			$this->db->query($query, $param);
			//echo "<br>query = $query<br>";
			//echo "<pre>";
			//print_r($param);
			//echo "</pre>";

			$this->db->commit();
		} else {
			// один и тот же город
			if ($fict != 1) {
				// если это честный переезд, обновим улицу
				// перенести улицу
				$query = "update FOO_GIS.ATD_STREET set NAME = :street_name where ID = :st_id";
				$param = [
					"st_id" => $st_id,
					"street_name" => $street_name,
				];
				$this->db->query($query, $param);
				$this->db->commit();

			}
		}
	}

	/*
     * * Нормализация названий улиц СитПлан
     *
     */
	private function normSP($city_id, &$cities, $table)
	{

		for ($i = 0; $i < count($this->arr_sp); $i++) {
			if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
				continue;
			}


			if (!isset($this->arr_sp[ $i ][ "CH_NAME" ])) {

				$str = preg_replace(
					'/\s{2,}/',
					' ',
					mb_ereg_replace("ё", "е", mb_strtolower(trim($this->arr_sp[ $i ][ "NAME" ])))
				);
				$str = mb_ereg_replace("\.", ". ", $str);
				$str = preg_replace('/\s{2,}/', ' ', $str);

				//mb_ereg_replace("\.", ". ", $str);
				//preg_replace('/\s{2,}/', ' ', $this->arr_sp[$i]["CH_NAME"]);
				$st_types = [
					"адмирала",
					"генерала",
					"маршала",
					"академика",
					"поляна",
					" городок",
					"городок ",
					"проспект",
					"шоссе ",
					" шоссе",
					"улица",
					"проезд",
					"бульвар ",
					" бульвар",
					"аллея",
					" пер$",
					"бул\.",
					"ул\.",
					"пр\.",
					"ш\.",
				];
				for ($zz = 0; $zz < count($st_types); $zz++) {
					$str = trim(mb_ereg_replace($st_types[ $zz ], "", $str));
				}
				$this->arr_sp[ $i ][ "CH_NAME" ] = $str;
				/*
                                if ((mb_strpos($str, "(") !== false) && (mb_strpos($str, ")") !== false)) {


                                    $savedstr = $str;
                                    if ($this->checkCityByStreet($city_id, $str, $this->arr_sp[ $i ], $cities, $table)) {
                                        // нашли город и сохранили в массиве, улицу из общего списка удаляем
                                        unset($this->arr_sp[ $i ][ "NAME" ]);
                                        continue;
                                    } else {

                                        // цикл по улицам КЛАДР в поисках однозначного сопадения
                                        for ($j = 0; $j < count($this->arr_kladr); $j++) {
                                            if (!isset($this->arr_kladr[ $j ][ "NAME" ])) {
                                                continue;
                                            }
                                            if ($this->mb_strcmp($savedstr, $this->arr_kladr[ $j ][ "NAME" ]) == 0) {
                                                if ($this->mb_strcmp(
                                                        $this->arr_sp[ $i ][ "ABBR" ],
                                                        $this->arr_kladr[ $j ][ "SOCR" ]
                                                    ) == 0
                                                ) {
                                                    // нашли полное совпадение в КЛАДР - оставляем строку как есть !!!
                                                    $str = $savedstr;
                                                    break;
                                                }

                                            }
                                        }
                                        // городов не нашли, обрезали строку и добавляем ее в общий список
                                        $this->arr_sp[ $i ][ "CH_NAME" ] = $str;
                                        //echo "<br>Не найден подгород из улицы ".$this->arr_sp[$i]["NAME"].", будем искать улицу (".$this->arr_sp[$i]["CH_NAME"].")<br>";
                                    }


                                } else {
                                    $this->arr_sp[ $i ][ "CH_NAME" ] = $str;
                                }
                            */
				if (mb_strlen($this->arr_sp[ $i ][ "CH_NAME" ]) == 0) {
					unset($this->arr_sp[ $i ][ "NAME" ]);
					continue;
				}
			}
		}

		return;
	}

	/*
     *
     */
	private function checkCityByStreet($city_id, &$str, $st, &$cities, $table)
	{
		/* нашли конструкцию (текст) в названии улицы:
                   1. Выделяем название (нормализуя и удаляя сокращения)
                   2. Ищем город уровнем ниже
                   3. Ищем город на уровне
                   4. Для Спб. ищем в Ленинградской области (хардкод)
                   5. Если не нашли - заменяем строкой без скобок для дальнейшего поиска
                   */
		//echo "<br>($str)<br>";


		$st_types = [ "с.", "пгт.", "снт.", "п\.", "г\.", "пос\.", "\)", "\(" ];
		$city = trim(mb_substr($str, mb_strpos($str, "("), mb_strpos($str, ")")));
		for ($i = 0; $i < count($st_types); $i++) {
			$city = trim(mb_ereg_replace($st_types[ $i ], "", $city));
		}

		$street = trim(mb_ereg_replace("\((.*)\)", "", $str));
		// найти город уровнем ниже текущего города
		$query = "
          select
                        ao2.ID,
                        ao2.NAME
                        from
                        FOO_GIS." . $table . "_OBJECT ao1,
                        FOO_GIS." . $table . "_OBJECT ao2
                        where
                        ao1.ID = :1 AND
                        FOO_GIS.GET_KLADR_PARENT(ao2.CODE) = ao1.CODE AND
                        lower(ao2.NAME) = :2";

		$param = [
			"1" => $city_id,
			"2" => $city,
		];
		if (strcmp($table, "SLTY") == 0) {
			$query .= " AND ao2.SOURCE_ID = :3";
			$param[ "3" ] = $this->source_id;
		}
		$a = $this->db->queryArray($query, $param);

		if (count($a) == 1) {
			// нашли город уровне ниже
			$st[ "CH_NAME" ] = $street;
			$st[ "NAME" ] = "Подгород " . $a[ 0 ][ "NAME" ] . " : " . $st[ "NAME" ];
			$cities[ $a[ 0 ][ "ID" ] ][] = $st;

			//echo "<br>Найден на уровне ниже ($str)->($city)->($street)<br>";
			return (true);
		} else {
			// найти город на уровне текущего города
			$query = "select
                        ao2.ID
                        from
                        FOO_GIS." . $table . "_OBJECT ao1,
                        FOO_GIS." . $table . "_OBJECT ao2
                        where
                        ao1.ID = :1 AND
                        FOO_GIS.GET_KLADR_PARENT(ao2.CODE) = FOO_GIS.GET_KLADR_PARENT(ao1.CODE) AND
                        lower(ao2.NAME) = :2";
			$param = [
				"1" => $city_id,
				"2" => $city,
			];
			if (strcmp($table, "SLTY") == 0) {
				$query .= " AND ao2.SOURCE_ID = :3";
				$param[ "3" ] = $this->source_id;
			}
			$a = $this->db->queryArray($query, $param);
			if (count($a) == 1) {
				// нашли город уровне
				$st[ "CH_NAME" ] = $street;
				$st[ "NAME" ] = "Подгород " . $a[ 0 ][ "NAME" ] . " : " . $st[ "NAME" ];
				$cities[ $a[ 0 ][ "ID" ] ][] = $st;

				//echo "<br>Найден на уровне ($str)->($city)->($street)<br>";
				return (true);
			} else {
				// город не найден , поискать как улицу текущего города
				$str = $street;

				//echo "<br>Не найден ($street)<br>";
				return (false);
			}

		}

		return (false);
	}

	/*
     *
     */
	private function normKLADR()
	{

		for ($j = 0; $j < count($this->arr_kladr); $j++) {
			if (!isset($this->arr_kladr[ $j ][ "NAME" ])) {
				continue;
			}
			if (!isset($this->arr_kladr[ $j ][ "CH_NAME" ])) {

				$this->arr_kladr[ $j ][ "CH_NAME" ] = mb_ereg_replace(
					'/\s{2,}/',
					' ',
					mb_ereg_replace("ё", "е", mb_strtolower(trim($this->arr_kladr[ $j ][ "NAME" ])))
				);

				$this->arr_kladr[ $j ][ "CH_NAME" ] = mb_ereg_replace("\.", ". ", $this->arr_kladr[ $j ][ "CH_NAME" ]);
				$this->arr_kladr[ $j ][ "CH_NAME" ] = mb_ereg_replace(
					'/\s{2,}/',
					' ',
					$this->arr_kladr[ $j ][ "CH_NAME" ]
				);

				$st_types = [
					"адмирала",
					"генерала",
					"маршала",
					"им ",
					"академика",
					"героя",
					"советского",
					"союза",
					"сдт ",

				];
				$str = $this->arr_kladr[ $j ][ "CH_NAME" ];
				for ($zz = 0; $zz < count($st_types); $zz++) {
					$str = trim(mb_ereg_replace($st_types[ $zz ], "", $str));
				}
				$this->arr_kladr[ $j ][ "CH_NAME" ] = $str;
				if (mb_strlen($this->arr_kladr[ $j ][ "CH_NAME" ]) == 0) {
					unset($this->arr_kladr[ $j ][ "NAME" ]);
					continue;
				}

			}
		}
	}

	/*
     * Поход 1 по полному совпадению типов и названий
     */
	private function checkPh1()
	{
		$this->res_k = 0;
		unset($this->arr_res);
		/* Проход 1 по полному совпадению */
		for ($i = 0; $i < count($this->arr_sp); $i++) {
			if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
				continue;
			}
			// Это чужой дубль, пропускаем
			if (isset($this->arr_sp[ $i ][ "is_doubles" ])) {
				continue;
			}
			$s = -1;
			$cnt = 0;

			for ($j = 0; $j < count($this->arr_kladr); $j++) {
				if (!isset($this->arr_kladr[ $j ][ "NAME" ])) {
					continue;
				}
				if ($this->mb_strcmp($this->arr_sp[ $i ][ "ABBR" ], $this->arr_kladr[ $j ][ "SOCR" ]) != 0) {
					continue;
				}
				//if($this->arr_sp[$i]["ID"] == 8599) {
				//    echo "<br>(".$this->arr_sp[$i]["CH_NAME"].")".mb_strlen($this->arr_sp[$i]["CH_NAME"])."<br>";
				//    echo "<br>(".$this->arr_kladr[$j]["CH_NAME"].")".mb_strlen($this->arr_kladr[$j]["CH_NAME"])."<br>";
				//}
				if ($this->mb_strcmp($this->arr_sp[ $i ][ "CH_NAME" ], $this->arr_kladr[ $j ][ "CH_NAME" ]) == 0) {
					//if($this->arr_sp[$i]["ID"] == 8599) {
					//    echo "<br>совпало<br>";
					//}
					// счетчик совпадений
					$cnt++;
					// запомнили строку
					$s = $j;
				}
			}
			if ($cnt == 1) {
				// однозначное совпадение - провязываем
				$this->connectSPwKLADR($i, $s);
			}
		}

		return;
	}

	/*
     * Вычисляет количество совпадающих с $str слов в названиях улиц СитПлан
     * (за исключением улиц - дублей)
     */
	private function countSPword($str)
	{
		$cnt = 0;
		for ($i = 0; $i < count($this->arr_sp); $i++) {
			if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
				continue;
			}
			// Это чужой дубль, пропускаем
			if (isset($this->arr_sp[ $i ][ "is_doubles" ])) {
				continue;
			}
			/* разбить название улицы по пробелам */
			$c = mb_split(" ", $this->arr_sp[ $i ][ "CH_NAME" ]);
			if (isset($c)) {
				/* цикл по словам названия */
				for ($m = 0; $m < count($c); $m++) {
					if ($this->mb_strcmp($str, $c[ $m ]) == 0) {
						/* нашли полное совпадение, увеличиваем счетчик*/
						$cnt++;
					}
				}
			}
		}

		return ($cnt);
	}

	/*
     * Проход 2 по однозначному совпадению одного из слов длиной более 4х символов в строках
     */
	private function checkPh2()
	{
		$this->res_k = 0;
		unset($this->arr_res);
		$s = -1;
		/* Проход 2 по однозначному совпадению одного из слов длиной более 4х символов в строках */
		for ($i = 0; $i < count($this->arr_sp); $i++) {
			if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
				continue;
			}
			// Это чужой дубль, пропускаем
			if (isset($this->arr_sp[ $i ][ "is_doubles" ])) {
				continue;
			}
			$cnt = 0;
			$s = -1;
			/* разбить название улицы по пробелам */
			$c = mb_split(" ", $this->arr_sp[ $i ][ "CH_NAME" ]);
			if (isset($c)) {
				/* цикл по словам названия СитПлан */
				for ($m = 0; $m < count($c); $m++) {
					$cnt = 0;
					$s = -1;
					/* не проверять слова короче 4х символов */
					if (mb_strlen($c[ $m ]) < 4) {
						continue;
					}
					/* не проверять слова, у которых есть дубли в СитПлан (за исключением улиц-дублей)*/
					if ($this->countSPword($c[ $m ]) > 1) {
						continue;
					}
					$s = -1;
					$cnt = 0;
					for ($j = 0; $j < count($this->arr_kladr); $j++) {
						if (!isset($this->arr_kladr[ $j ][ "NAME" ])) {
							continue;
						}
						// проверка по совпадению типов
						//if($this->mb_strcmp($this->arr_sp[$i]["ABBR"],$this->arr_kladr[$j]["SOCR"]) != 0) {
						//    continue;
						//}

						/* разбить название улицы по пробелам */
						$d = mb_split(" ", $this->arr_kladr[ $j ][ "CH_NAME" ]);
						if (isset($d)) {
							/* цикл по словам названия */
							for ($p = 0; $p < count($d); $p++) {
								/* не проверять слова короче 4х символов */
								if (mb_strlen($d[ $p ]) < 4) {
									continue;
								}

								/* нашли часть названия улицы в улице КЛАДР */
								if ($this->mb_strcmp($d[ $p ], $c[ $m ]) == 0) {
									$s = $j;
									$cnt++;
								}
							}
						}
					}
					/* проверили одно слово улицы СитПлан со всеми словами всех улиц КЛАДР */
					if ($cnt == 1) {
						/* совпадение однозначное - связать и закончить с улицей СитПлан */
						$this->connectSPwKLADR($i, $s);
						break;
					}
				}
			}
		}

		return;
	}

	/*
     * Сформировать массив непровязанных улиц СитПлан
     */
	private function checkBadSP($levelfull = true)
	{
		unset($this->arr_res);
		$this->res_k = 0;
		if ($levelfull) {
			for ($i = 0, $this->res_k = 0; $i < count($this->arr_sp); $i++) {
				if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
					continue;
				}
				$this->arr_res[ $this->res_k ][ "ID" ] = $this->arr_sp[ $i ][ "ID" ];
				$this->arr_res[ $this->res_k ][ "ABBR" ] = $this->arr_sp[ $i ][ "ABBR" ];
				$this->arr_res[ $this->res_k ][ "SPNAME" ] = $this->arr_sp[ $i ][ "NAME" ];
				$this->arr_res[ $this->res_k ][ "SPCHNAME" ] = $this->arr_sp[ $i ][ "CH_NAME" ];
				$this->arr_res[ $this->res_k ][ "cnt" ] = $this->res_k + 1;
				$this->res_k++;
			}
		}

		return;
	}

	/*
     * непривязанные КЛАДР
     */
	private function checkBadKLADR()
	{
		unset($this->arr_res);
		$this->res_k = 0;
		/* непривязанные КЛАДР */
		for ($j = 0; $j < count($this->arr_kladr); $j++) {
			if (!isset($this->arr_kladr[ $j ][ "NAME" ])) {
				continue;
			}

			$this->arr_res[ $this->res_k ][ "SOCR" ] = $this->arr_kladr[ $j ][ "SOCR" ];
			$this->arr_res[ $this->res_k ][ "KLNAME" ] = $this->arr_kladr[ $j ][ "NAME" ];
			$this->arr_res[ $this->res_k ][ "KLCHNAME" ] = $this->arr_kladr[ $j ][ "CH_NAME" ];
			$this->arr_res[ $this->res_k ][ "CODE" ] = $this->arr_kladr[ $j ][ "CODE" ];
			$this->arr_res[ $this->res_k ][ "cnt" ] = $this->res_k + 1;
			$this->res_k++;
		}

		return;
	}

	/*
     * Проход 3 по весу и вхождению в строку
     */
	private function checkPh3()
	{
		unset($this->arr_res);
		$this->res_k = 0;

		/* Проход 3 по весу и вхождению в строку */
		for ($i = 0; $i < count($this->arr_sp); $i++) {
			if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
				continue;
			}
			// Это чужой дубль, пропускаем
			if (isset($this->arr_sp[ $i ][ "is_doubles" ])) {
				continue;
			}
			$s = -1;
			$bestres = 100000;
			for ($j = 0; $j < count($this->arr_kladr); $j++) {
				if (!isset($this->arr_kladr[ $j ][ "NAME" ])) {
					continue;
				}
				// совпали типы улиц
				if ($this->mb_strcmp($this->arr_sp[ $i ][ "ABBR" ], $this->arr_kladr[ $j ][ "SOCR" ]) == 0) {

					/* вычислить вес совпадения */
					$res = $this->calcInstrKLADR($this->arr_sp[ $i ][ "CH_NAME" ], $this->arr_kladr[ $j ][ "CH_NAME" ]);
					//if($this->arr_sp[$i]["ID"] == 20577) {
					//    echo "<br>Вес=$res(".$this->arr_sp[$i]["CH_NAME"].")".mb_strlen($this->arr_sp[$i]["CH_NAME"])."---";
					//    echo "<br>(".$this->arr_kladr[$j]["CH_NAME"].")".mb_strlen($this->arr_kladr[$j]["CH_NAME"])."<br>";
					//}
					if ($res < $bestres) {
						$bestres = $res;
						$s = $j;
					}
				}
			}
			//if($this->arr_sp[$i]["ID"] == 20577) {
			//    echo "<br>Вес=$bestres(".$this->arr_sp[$i]["CH_NAME"].")".mb_strlen($this->arr_sp[$i]["CH_NAME"])."<br>";
			//    echo "<br>(".$this->arr_kladr[$s]["CH_NAME"].")".mb_strlen($this->arr_kladr[$s]["CH_NAME"])."<br>";
			//}
			if (($bestres <= 4) && ($s >= 0)) {
				/* если вес < 4 - проверить, что строка с лучшим весом - одна из лучших по длине вхождения */

				$maxlen = 0;
				/* цикл по улицам КЛАДР */
				for ($j = 0; $j < count($this->arr_kladr); $j++) {
					if (!isset($this->arr_kladr[ $j ][ "NAME" ])) {
						continue;
					}
					// совпали типы улиц
					if ($this->mb_strcmp($this->arr_sp[ $i ][ "ABBR" ], $this->arr_kladr[ $j ][ "SOCR" ]) == 0) {
						$len = $this->calcSubstrKLADR(
							$this->arr_sp[ $i ][ "CH_NAME" ],
							$this->arr_kladr[ $j ][ "CH_NAME" ]
						);
						//if($this->arr_sp[$i]["ID"] == 20577) {
						//    echo "<br>Длина=$len/$maxlen(".$this->arr_sp[$i]["CH_NAME"].")".mb_strlen($this->arr_sp[$i]["CH_NAME"])."<br>";
						//    echo "<br>(".$this->arr_kladr[$j]["CH_NAME"].")".mb_strlen($this->arr_kladr[$j]["CH_NAME"])."<br>";
						//}
						if ($len >= $maxlen) {
							$maxlen = $len;
						}
					}
				}
				/* вычислили лучшую длину вхождения, вычисляем длину лучше строки по весу  */
				$len = $this->calcSubstrKLADR($this->arr_sp[ $i ][ "CH_NAME" ], $this->arr_kladr[ $s ][ "CH_NAME" ]);
				//if($this->arr_sp[$i]["ID"] == 20577) {
				//    echo "<br>Длина=$len/$maxlen(".$this->arr_sp[$i]["CH_NAME"].")".mb_strlen($this->arr_sp[$i]["CH_NAME"])."<br>";
				//    echo "<br>(".$this->arr_kladr[$s]["CH_NAME"].")".mb_strlen($this->arr_kladr[$s]["CH_NAME"])."<br>";
				//}
				if ($len >= 4 && $len >= $maxlen) {
					//if($this->arr_sp[$i]["ID"] == 20577) {
					//    echo "<br>совпало<br>";
					//}
					$this->connectSPwKLADR($i, $s, 0, $bestres, $len);
				}
			}
		}

		return;
	}

	/*
    * Проход 4 по однозначному совпадению одного из слов длиной более 4х символов в строках
    */
	private function checkPh4()
	{
		unset($this->arr_res);
		$this->res_k = 0;

		/* Проход 4 по однозначному совпадению одного из слов длиной более 4х символов в строках  */
		for ($i = 0; $i < count($this->arr_sp); $i++) {
			if (!isset($this->arr_sp[ $i ][ "NAME" ])) {
				continue;
			}
			// Это чужой дубль, пропускаем
			if (isset($this->arr_sp[ $i ][ "is_doubles" ])) {
				continue;
			}
			$cnt = 0;
			$s = -1;
			/* разбить название улицы по пробелам */
			$c = mb_split(" ", $this->arr_sp[ $i ][ "CH_NAME" ]);
			if (isset($c)) {
				/* цикл по словам названия СитПлан */
				for ($m = 0; $m < count($c); $m++) {
					$cnt = 0;
					$s = -1;
					/* не проверять слова короче 4х символов */
					if (mb_strlen($c[ $m ]) < 4) {
						continue;
					}
					$s = -1;
					$cnt = 0;
					for ($j = 0; $j < count($this->arr_kladr); $j++) {
						if (!isset($this->arr_kladr[ $j ][ "NAME" ])) {
							continue;
						}
						// проверка по совпадению типов
						if ($this->mb_strcmp($this->arr_sp[ $i ][ "ABBR" ], $this->arr_kladr[ $j ][ "SOCR" ]) != 0) {
							continue;
						}

						/* разбить название улицы по пробелам */
						$d = mb_split(" ", $this->arr_kladr[ $j ][ "CH_NAME" ]);
						if (isset($d)) {
							/* цикл по словам названия */
							for ($p = 0; $p < count($d); $p++) {
								/* не проверять слова короче 4х символов */
								if (mb_strlen($d[ $p ]) < 4) {
									continue;
								}

								/* нашли часть названия улицы в улице КЛАДР */
								if ($this->mb_strcmp($d[ $p ], $c[ $m ]) == 0) {
									$s = $j;
									$cnt++;
								}
							}
						}
					}
					/* проверили одно слово улицы СитПлан со всеми словами всех улиц КЛАДР */
					if ($cnt == 1) {
						/* совпадение однозначное - связать и закончить с улицей СитПлан */
						$this->connectSPwKLADR($i, $s);
						break;
					}
				}
			}
		}

		return;
	}

	/*
     *
     */
	private function updSPstreets($arr, $table)
	{
		for ($i = 0; $i < count($arr); $i++) {
			$query = "update FOO_GIS." . $table . "_STREET set CODE = :1 where ID = :2";
			$param = [
				"1" => $arr[ $i ][ "CODE" ],
				"2" => $arr[ $i ][ "ID" ],
			];
			//echo "<pre>";
			//echo "<br>query=($query)<br>";
			//print_r($param);
			//echo "</pre>";
			$this->db->query($query, $param);
		}
	}

	/**
	 * Получить список непривязанных к КЛАДР улиц СП
	 *
	 * @param $city_id
	 */
	public function getnotconnStreet($city_id)
	{
		$query = "select
          (select count(ADDR_ID) from FOO_GIS.ATD_ADDRESS ad where ad.STR_STD1 = ast.ID) as HOUSES,
          ast.ID, ast.NAME, ast.ATDOBJ_TYPE, aot.ABBR
          from FOO_GIS.ATD_STREET ast, FOO_GIS.ATD_OBJ_TYPE aot where
              ast.OBJ_ID = :city_id and ast.CODE is null and ast.ATDOBJ_TYPE = aot.CODE order by ast.NAME";
		$param = [
			"city_id" => $city_id,
		];
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		return ($a);
	}

	public function findStreetInArr($arr, $id)
	{
		if ($arr[ "cnt" ] > 0) {
			for ($i = 0; $i < count($arr[ "res" ]); $i++) {
				if ($arr[ "res" ][ $i ][ "ID" ] == $id) {
					return ($arr[ "res" ][ $i ]);
				}
			}
		}

		return (null);
	}

	public function checkLStreet($city_code, $street_abbr, $street_name)
	{
		//echo "<br>checkLStreet($city_code,$street_abbr, $street_name)<br>";
		$res = [];
		$kladr = [];
		$city = $this->getSPbyCode($city_code);

		if (!isset($city[ "ID" ])) {
			$kladr[ "RESULT" ] = -1;
		}
		$city_id = $city[ "ID" ];
		$street = [ "0" => [ "ID" => 123456789, "NAME" => $street_name, "ABBR" => $street_abbr ] ];
		$result = $this->checkKLADRcity($city_id, $street);

		if (!($res = $this->findStreetInArr($result[ "s0" ][ "good" ], $street[ 0 ][ "ID" ]))) {
			if (!($res = $this->findStreetInArr($result[ "s1" ][ "good" ], $street[ 0 ][ "ID" ]))) {
				if (!($res = $this->findStreetInArr($result[ "s2" ][ "good" ], $street[ 0 ][ "ID" ]))) {
					if (!($res = $this->findStreetInArr($result[ "s3" ][ "good" ], $street[ 0 ][ "ID" ]))) {
						if (!($res = $this->findStreetInArr($result[ "s4" ][ "good" ], $street[ 0 ][ "ID" ]))) {
							$kladr[ "RESULT" ] = -1;
							unset($result);

							return ($kladr);
						}
					}
				}
			}
		}
		$res[ "RESULT" ] = 0;

		$kladr = $this->getKLADRSbyCode($res[ "CODE" ]);
		$kladr[ "RESULT" ] = 0;
		//echo "<pre>";
		//print_r($kladr);
		//echo "</pre>";
		return ($kladr);

	}

	/*
              *
              */
	public function checkKLADRcity(
		$city_id,
		$sp_street = false,
		$kladr_street = false,
		$doupdate = false,
		$table = "ATD",
		$levelfull = true
	) {
		$report = [];
		$cities = [];

		unset($this->arr_res);
		$this->res_k = 0;

		// если это первый проход - выбрать улицы из БД
		if ($sp_street === false) {
			$query = "
              SELECT st.ID, st.NAME, nvl(aot.ABBR,'ул') as ABBR
              FROM FOO_GIS." . $table . "_STREET st, FOO_GIS.ATD_OBJ_TYPE aot
              WHERE st.OBJ_ID = :1 AND
              nvl(st.ATDOBJ_TYPE,0) = aot.CODE(+)
              order by st.NAME";
			$param = [
				"1" => $city_id,
			];
			$this->arr_sp = $this->db->queryArray($query, $param);
			//echo "<br>Загружено " . count($this->arr_sp) . " улиц<br>";
			//echo "<pre>";
			//print_r($this->arr_sp);
			//echo "</pre>";

		} else {
			// если это второй проход, то остаток неразпознанных улиц передан в $sp_strret

			$this->arr_sp = $sp_street;
			//echo "<br>Получено " . count($this->arr_sp) . " улиц<br>";
		}
		// если это первый проход - выбрать улицы из БД
		if ($kladr_street === false) {
			$query = "
              SELECT * FROM FOO_GIS.KLADR_STREET
                WHERE MAIN_ID =
                  (SELECT ID FROM FOO_GIS.KLADR_MAIN
                    WHERE CODE = (SELECT CODE FROM FOO_GIS." . $table . "_OBJECT
                      WHERE ID = :1) AND STATE = 0) AND STATE = 0 order by NAME";
			$param = [
				"1" => $city_id,
			];
			$this->arr_kladr = $this->db->queryArray($query, $param);
			//echo "<br>Загружено " . count($this->arr_kladr) . " улиц КЛАДР<br>";
		} else {
			// если это второй проход, то остаток неразпознанных улиц передан в $kladr_strret
			$this->arr_kladr = $kladr_street;
		}

		// нормализация названий улиц
		$this->normKLADR();
		$this->normSP($city_id, $cities, $table);

		// препроцессинг улиц

		$this->preSPdoubles();
		$this->preSPdoublesKLADR();
		$report[ "s0" ][ "good" ][ "res" ] = $this->arr_res;
		$report[ "s0" ][ "good" ][ "cnt" ] = $this->res_k;
		if ($doupdate) {
			$this->updSPstreets($report[ "s0" ][ "good" ][ "res" ], $table);
		}
		/* Проход 1 по полному совпадению */
		$this->checkPh1();
		$report[ "s1" ][ "good" ][ "res" ] = $this->arr_res;
		$report[ "s1" ][ "good" ][ "cnt" ] = $this->res_k;
		if ($doupdate) {
			$this->updSPstreets($report[ "s1" ][ "good" ][ "res" ], $table);
		}

		$this->checkBadSP($levelfull);
		$report[ "s1" ][ "bad" ][ "res" ] = $this->arr_res;
		$report[ "s1" ][ "bad" ][ "cnt" ] = $this->res_k;

		/* Проход 2 по однозначному совпадению одного из слов длиной более 4х символов в строках */
		$this->checkPh2();
		$report[ "s2" ][ "good" ][ "res" ] = $this->arr_res;
		$report[ "s2" ][ "good" ][ "cnt" ] = $this->res_k;
		if ($doupdate) {
			$this->updSPstreets($report[ "s2" ][ "good" ][ "res" ], $table);
		}
		$this->checkBadSP($levelfull);
		$report[ "s2" ][ "bad" ][ "res" ] = $this->arr_res;
		$report[ "s2" ][ "bad" ][ "cnt" ] = $this->res_k;
		if ($levelfull) {// эти проходы НЕ мспользуются в циклической привязке всех НП региона
			/* Проход 3 по весу и вхождению в строку */
			$this->checkPh3();
			$report[ "s3" ][ "good" ][ "res" ] = $this->arr_res;
			$report[ "s3" ][ "good" ][ "cnt" ] = $this->res_k;
			if ($doupdate) {
				$this->updSPstreets($report[ "s3" ][ "good" ][ "res" ], $table);
			}
			$this->checkBadSP($levelfull);
			$report[ "s3" ][ "bad" ][ "res" ] = $this->arr_res;
			$report[ "s3" ][ "bad" ][ "cnt" ] = $this->res_k;
		}
		/* Проход 4 по однозначному совпадению одного из слов длиной более 4х символов в строках без типа улицы */
		$this->checkPh4();
		$report[ "s4" ][ "good" ][ "res" ] = $this->arr_res;
		$report[ "s4" ][ "good" ][ "cnt" ] = $this->res_k;
		if ($doupdate) {
			$this->updSPstreets($report[ "s4" ][ "good" ][ "res" ], $table);
		}
		$this->checkBadSP($levelfull);
		$report[ "s4" ][ "bad" ][ "res" ] = $this->arr_res;
		$report[ "s4" ][ "bad" ][ "cnt" ] = $this->res_k;
		if ($levelfull) {// эти проходы НЕ мспользуются в циклической привязке всех НП региона
			/* непривязанные КЛАДР */
			$this->checkBadKLADR();
			$report[ "last" ][ "bad" ][ "res" ] = $this->arr_res;
			$report[ "last" ][ "bad" ][ "cnt" ] = $this->res_k;
		}
		/* найденные города с улицами для дальнейшего поиска */
		//echo "<pre>";
		//print_r($cities);
		//echo "</pre>";
		$report[ "cities" ] = $cities;
		if ($doupdate) {
			$this->db->commit();
		}

		return ($report);
	}

	/*
     * Вычисления веса расхождения двух строк (0 - совпали)
     */
	private function calcInstrKLADR(&$s1, &$s2)
	{
		$cr = [
			'а',
			'б',
			'в',
			'г',
			'д',
			'e',
			'ё',
			'ж',
			'з',
			'и',
			'й',
			'к',
			'л',
			'м',
			'н',
			'о',
			'п',
			'р',
			'с',
			'т',
			'у',
			'ф',
			'х',
			'ц',
			'ч',
			'ш',
			'щ',
			'ы',
			'ъ',
			'ь',
			'э',
			'ю',
			'я',
			'1',
			'2',
			'3',
			'4',
			'5',
			'6',
			'7',
			'8',
			'9',
			'0',
		];

		$w1 = [];
		$w2 = [];
		$res = 0;
		/* подсчет вхождений символов в первой строке */
		for ($i = 0; $i < mb_strlen($s1); $i++) {
			$s = mb_substr($s1, $i, 1);
			$char = mb_convert_encoding($s, "UCS-4BE");
			$order = unpack("N", $char);
			$c = ($order ? $order[ 1 ] : null);
			$w1[ $c ]++;
		}
		/* подсчет вхождений символов во второй строке */
		for ($i = 0; $i < mb_strlen($s2); $i++) {
			$s = mb_substr($s2, $i, 1);
			$char = mb_convert_encoding($s, "UCS-4BE");
			$order = unpack("N", $char);
			$c = ($order ? $order[ 1 ] : null);
			$w2[ $c ]++;
		}

		// цикл по алфавиту
		for ($i = 0; $i < count($cr); $i++) {
			$s = $cr[ $i ];
			$char = mb_convert_encoding($s, "UCS-4BE");
			$order = unpack("N", $char);
			// код символа алфавита
			$c = ($order ? $order[ 1 ] : null);
			// количество этого символа в первой строке
			$n1 = (isset($w1[ $c ]) ? $w1[ $c ] : 0);
			// количество этого символа во второй строке
			$n2 = (isset($w2[ $c ]) ? $w2[ $c ] : 0);
			// дельта символа
			$delta = abs($n1 - $n2);
			// все несовпадения цифр увеличиваем втрое
			if (is_int($s)) {
				$delta *= 3;
			}
			$res += $delta;
		}

		return ($res);
	}

	/*
    * Вычисление длины вхождения одной строки в другую (частями строк)
    */
	private function calcSubstrKLADR(&$a, &$b, $debug = false)
	{

		if (mb_strlen($a) < mb_strlen($b)) {
			$s1 = &$a;
			$s2 = &$b;
		} else {
			$s2 = &$a;
			$s1 = &$b;
		}
		$d = mb_strlen($s1);
		$len = 0;
		/* Цикл (1-d)(1-(d-1))(1-(d-2) .... (1-2) --- (2-(д-1)…(2 –(д-2)…(2-(3)) ... */
		for ($x = 0; $x < $d; $x++) {
			for ($z = $d; $z > $x; $z--) {
				$needle = mb_substr($s1, $x, ($z - $x));
				if (mb_strpos($s2, $needle) !== false) {
					if ($debug) {
						echo "<br>($s2)-($needle)-($len)-(" . ($z - $x) . ")<br>";
					}
					if ($len < ($z - $x)) {
						$len = ($z - $x);
					}
				}
			}
		}

		return ($len);
	}

	/*
    *
    */
	public function checkFTTC($city_id)
	{

		//echo "<br>" .date(DATE_RFC2822)." выборка FTTC вне кластеров города<br>";
		$query = "select ad.ADDR_ID from
          FOO_GIS.ATD_ADDRESS ad,
          FOO_GIS.RTK_ADDRESS rtk
          where
          ad.STR_STD1 in (select ID from FOO_GIS.ATD_STREET where OBJ_ID = :1) AND
          ad.ADDR_ID not in (select nvl(ADDR_ID,0) as ADDR_ID from FOO.CLUSTERS) AND
          ad.ADDR_ID = rtk.ADDR_ID AND
          rtk.TYPE_ODF = 'FTTC'";

		$param = [
			"1" => $city_id,
		];
		$a = $this->db->queryArray($query, $param);
		//echo "<br>" .date(DATE_RFC2822)." выбрано " . count($a) . " зданий FTTC. Цикл удаления<br>";
		for ($i = 0; $i < count($a); $i++) {
			unset($param);
			$query = "delete from FOO_GIS.RTK_ADDRESS where ADDR_ID = :1 AND TYPE_ODF = 'FTTC'";
			$param = [
				"1" => $a[ $i ][ "ADDR_ID" ],
			];
			$b = $this->db->query($query, $param);
			//echo "<br>" .date(DATE_RFC2822)." $i RTK_ADDRESS<br>";
			$query = "delete from FOO.CLOUDS_ADDRESS where ADDR_ID = :1";
			$param = [
				"1" => $a[ $i ][ "ADDR_ID" ],
			];
			$c = $this->db->query($query, $param);
			$query = "delete from FOO_GIS.ATD_ADDRESS where ADDR_ID = :1";
			$param = [
				"1" => $a[ $i ][ "ADDR_ID" ],
			];
			$d = $this->db->query($query, $param);
			//echo "<br>" .date(DATE_RFC2822)." $i ATD_ADDRESS<br>";
			$this->db->commit();
		}
		echo "<br>" . date(DATE_RFC2822) . " удалено $i зданий FTTC<br>";

		$query = "select st.ID, aot.ABBR, st.NAME from FOO_GIS.ATD_STREET st,
          FOO_GIS.ATD_OBJ_TYPE aot
          where st.OBJ_ID = :1 AND
          st.ID not in (select STR_STD1 from FOO_GIS.ATD_ADDRESS) AND
          aot.CODE = st.ATDOBJ_TYPE";

		$param = [
			"1" => $city_id,
		];
		$f = $this->db->queryArray($query, $param);
		echo "<br>" . date(DATE_RFC2822) . " найдено " . count($f) . " пустых улиц города<br>";
		if (count($f) > 0) {
			$query = "delete from FOO_GIS.ATD_STREET where OBJ_ID = :1 AND
              ID not in (select STR_STD1 from FOO_GIS.ATD_ADDRESS)";

			$param = [
				"1" => $city_id,
			];
			$e = $this->db->query($query, $param);
			$this->db->commit();
			echo "<br>" . date(DATE_RFC2822) . " удалены пустые улицы города<br>";
		}

		return ($f);
	}

	/*
     *
     */
	private function mb_strcmp($s1, $s2)
	{
		$cs1 = iconv("UTF-8", "CP1251", $s1);
		$cs2 = iconv("UTF-8", "CP1251", $s2);

		return (strcmp($cs1, $cs2));
	}

	/**
	 * Получить список дочерних населенных пунктов СП
	 *
	 * @param $ao_id
	 */
	public function getAObyParId($ao_id)
	{
		$param = [];
		if (isset($ao_id)) {
			$aotxt = " = :ao_id ";
			$param = [ "ao_id" => $ao_id ];
		} else {
			$aotxt = " is null ";
			$param = [];
		}
		$query = "SELECT ao.ID, ao.NAME || ' ' || aot.NAME as NAME FROM FOO_GIS.ATD_OBJECT ao,
          FOO_GIS.ATD_OBJ_TYPE aot
          where  ao.PAR_ID $aotxt and
          nvl(AO.ATDOBJ_TYPE,0) = AOT.CODE(+)
          ORDER BY ao.NAME";

		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	/**
	 * Получить отчетные суммы параметров для населенных пунктов
	 *
	 * @param $ao_id
	 */
	public function getRegparams($ao_id)
	{
		$tot = [];
		$data = [];
		$ao = $this->getAObyParId($ao_id);
		for ($i = 0; $i < count($ao); $i++) {
			$data[ $i ][ "ao" ] = $ao[ $i ];
			$data[ $i ][ "params" ] = $this->getAOParams($ao[ $i ][ "ID" ]);
			foreach ($data[ $i ][ "params" ] as $k => $v) {
				$tot[ $k ] += $v;
			}
		}
		$data[ $i ][ "ao" ] = [ "NAME" => "Итого" ];
		$data[ $i ][ "params" ] = $tot;

		return ($data);
	}

	/**
	 *
	 *
	 */
	public function getSPdoublesAO()
	{
		$res = [];
		// получить список НП ТД, содержащих скобки в названии
		$query = "select ao.ID, ao.CODE, ao.NAME,
            FOO_GIS.FULLNAME_OBJECT(ao.ID) as FULLNAME,
            FOO_GIS.GET_OBJECT_PREFIX(ao.ID) as PREFIX,
            nvl(rc.PEOPLE_NUM,0) as PEOPLE_NUM, nvl(rc.AP_NUM,0) as AP_NUM from
            FOO_GIS.RTK_CITYPARAMS rc,
            FOO_GIS.ATD_OBJECT ao
            WHERE
            rc.AP_NUM > 0 AND
            rc.ID = ao.ID AND
            ao.NAME like '%(%)'
            order by FULLNAME";
		$param = [];
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		for ($i = 0; $i < count($a); $i++) {
			// убрать из названия НП текст в скобках
			$name = trim(mb_ereg_replace("\((.*)\)", "", $a[ $i ][ "NAME" ]));
			$code = substr($a[ $i ][ "CODE" ], 0, 5);
			$query = "select ao.ID, ao.CODE, ao.NAME,
            FOO_GIS.FULLNAME_OBJECT(ao.ID) as FULLNAME,
            FOO_GIS.GET_OBJECT_PREFIX(ao.ID) as PREFIX,
             nvl(rc.PEOPLE_NUM,0) as PEOPLE_NUM, nvl(rc.AP_NUM,0) as AP_NUM from
            FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            WHERE
            ao.ID != :ao_id and
            rc.ID (+)= ao.ID and
            to_number(substr(ao.CODE,1,5)) = :code and lower(ao.NAME) like :name";
			$param = [ "name" => "%" . mb_strtolower($name) . "%", "code" => $code, "ao_id" => $a[ $i ][ "ID" ] ];
			$b = $this->db->queryArray($query, $param);
			//echo "<pre>";
			//print_r($b);
			//echo "</pre>";
			if (count($b) > 0) {
				// есть дубли
				$a[ $i ][ "CNT" ] = count($b);
				$a[ $i ][ "doubles" ] = $b;
				$res[] = $a[ $i ];
			}
		}
		//echo "<pre>";
		//print_r($res);
		//echo "</pre>";
		return ($res);
	}

	public function getTDclustered($ao_id = "", $dep_id = "", $odf_type)
	{

		if ($ao_id != "") {
			$txt = " = :ao_id";
			$param = [ "ao_id" => $ao_id, "odf_type" => $odf_type ];
		}
		if ($dep_id != "") {
			$txt = "in
            (select AREA_ID from FOO_AUTH.AUTH_DEPARTMENT_AREAS where AU_DEPID in
            (select AU_DEPID from FOO_AUTH.AUTH_DEPARTMENTS
            connect by prior AU_DEPID = AU_DEPPARID start with AU_DEPID = :dep_id))";
			$param = [ "dep_id" => $dep_id, "odf_type" => $odf_type ];
		}
		$query = "select sum(1) as TD_CL_NUM from
            FOO.CLUSTERS cl
            where
            cl.ADDR_ID in
            (select distinct rta.ADDR_ID from FOO_GIS.RTK_TD_ADDRESS rta
            where rta.ID in
            (select distinct ao.ID from FOO_GIS.ATD_OBJECT ao
            connect by prior ao.ID = ao.PAR_ID
            start with ao.ID $txt) and rta.ODF_TYPE = :odf_type) and  cl.cl_TYPE = 4";


		$a = $this->db->queryArray($query, $param);
		//echo "<br>query=$query<br>";
		//echo "<pre>";
		//print_r($param);
		//print_r($a);
		//echo "</pre>";

		return $a[ 0 ];
	}

	/**
	 * получить количество кластеризованных элементов по филиалу
	 *
	 * @param $dep_id
	 *
	 * @return mixed
	 *
	 */
	public function getTDclusteredDep($dep_id, $odf_type)
	{

		$query = "select sum(1) as TD_CL_NUM from
            FOO_GIS.RTK_TD_ADDRESS rta,
            FOO_GIS.ATD_OBJECT ao,
            FOO.CLUSTERS cl
            where
              rta.ID (+)= ao.ID and
              rta.ID is not null and
              rta.ODF_TYPE = :odf_type and
            cl.ADDR_ID (+)= rta.ADDR_ID and
            cl.ADDR_ID is not null and
            cl.cl_TYPE in (4)
            connect by prior ao.ID = ao.PAR_ID
            start with ao.ID in
            (select AREA_ID from FOO_AUTH.AUTH_DEPARTMENT_AREAS where AU_DEPID in
            (select AU_DEPID from FOO_AUTH.AUTH_DEPARTMENTS
            connect by prior AU_DEPID = AU_DEPPARID start with AU_DEPID = :dep_id))";
		$param = [ "dep_id" => $dep_id, "odf_type" => $odf_type ];
		$a = $this->db->queryArray($query, $param);


		return $a[ 0 ];
	}

	/**
	 * @param $dep_id
	 *
	 * @return mixed
	 *
	 *
	 */
	public function getDepParams($dep_id)
	{

		$query = "select sum(rc.AP_NUM) as AP_NUM,
            sum(rc.TFONES_NUM) as TFONES_NUM,
            sum(rc.PKD_NUM) as PKD_NUM,
            sum(rc.OE_NUM) as OE_NUM,
            sum(rc.TDRTK_NUM) as TDRTK_NUM
            from FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            where rc.ID (+)= ao.ID and rc.ID is not null connect by prior ao.ID = ao.PAR_ID start with ao.ID in
            (select AREA_ID from FOO_AUTH.AUTH_DEPARTMENT_AREAS where AU_DEPID in
            (select AU_DEPID from FOO_AUTH.AUTH_DEPARTMENTS
            connect by prior AU_DEPID = AU_DEPPARID start with AU_DEPID = :dep_id))";
		$param = [ "dep_id" => $dep_id ];
		$a = $this->db->queryArray($query, $param);

		$query = "select sum(1) as AP_NUM_CITY
            from FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            where rc.ID (+)= ao.ID and rc.ID is not null and rc.AP_NUM>0 connect by prior ao.ID = ao.PAR_ID start with ao.ID in
            (select AREA_ID from FOO_AUTH.AUTH_DEPARTMENT_AREAS where AU_DEPID in
            (select AU_DEPID from FOO_AUTH.AUTH_DEPARTMENTS
            connect by prior AU_DEPID = AU_DEPPARID start with AU_DEPID = :dep_id))";
		$param = [ "dep_id" => $dep_id ];
		$b = $this->db->queryArray($query, $param);
		$a[ 0 ][ "AP_NUM_CITY" ] = $b[ 0 ][ "AP_NUM_CITY" ];

		$query = "select sum(1) as OE_NUM_CITY
            from FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            where rc.ID (+)= ao.ID and rc.ID is not null and rc.OE_NUM>0 connect by prior ao.ID = ao.PAR_ID start with ao.ID in
            (select AREA_ID from FOO_AUTH.AUTH_DEPARTMENT_AREAS where AU_DEPID in
            (select AU_DEPID from FOO_AUTH.AUTH_DEPARTMENTS
            connect by prior AU_DEPID = AU_DEPPARID start with AU_DEPID = :dep_id))";
		$param = [ "dep_id" => $dep_id ];
		$b = $this->db->queryArray($query, $param);
		$a[ 0 ][ "OE_NUM_CITY" ] = $b[ 0 ][ "OE_NUM_CITY" ];

		$query = "select sum(1) as TDRTK_NUM_CITY
            from FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            where rc.ID (+)= ao.ID and rc.ID is not null and rc.TDRTK_NUM>0 connect by prior ao.ID = ao.PAR_ID start with ao.ID in
            (select AREA_ID from FOO_AUTH.AUTH_DEPARTMENT_AREAS where AU_DEPID in
            (select AU_DEPID from FOO_AUTH.AUTH_DEPARTMENTS
            connect by prior AU_DEPID = AU_DEPPARID start with AU_DEPID = :dep_id))";
		$param = [ "dep_id" => $dep_id ];
		$b = $this->db->queryArray($query, $param);
		$a[ 0 ][ "TDRTK_NUM_CITY" ] = $b[ 0 ][ "TDRTK_NUM_CITY" ];

		return $a[ 0 ];
	}

	public function getAOParams($ao_id)
	{

		$query = "select sum(rc.AP_NUM) as AP_NUM,
            sum(rc.TFONES_NUM) as TFONES_NUM,
            sum(rc.PKD_NUM) as PKD_NUM,
            sum(rc.OE_NUM) as OE_NUM,
            sum(rc.TDRTK_NUM) as TDRTK_NUM
            from FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            where rc.ID (+)= ao.ID and rc.ID is not null connect by prior ao.ID = ao.PAR_ID start with ao.ID = :ao_id
        ";
		$param = [ "ao_id" => $ao_id ];
		$a = $this->db->queryArray($query, $param);
		$query = "select sum(1) as AP_NUM_CITY
            from FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            where rc.ID (+)= ao.ID and rc.ID is not null and rc.AP_NUM>0 connect by prior ao.ID = ao.PAR_ID start with ao.ID = :ao_id
        ";
		$param = [ "ao_id" => $ao_id ];
		$b = $this->db->queryArray($query, $param);
		$a[ 0 ][ "AP_NUM_CITY" ] = $b[ 0 ][ "AP_NUM_CITY" ];

		$query = "select sum(1) as TFONES_NUM_CITY
            from FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            where rc.ID (+)= ao.ID and rc.ID is not null and rc.TFONES_NUM>0 connect by prior ao.ID = ao.PAR_ID start with ao.ID = :ao_id
        ";
		$param = [ "ao_id" => $ao_id ];
		$b = $this->db->queryArray($query, $param);
		$a[ 0 ][ "TFONES_NUM_CITY" ] = $b[ 0 ][ "TFONES_NUM_CITY" ];

		$query = "select sum(1) as PKD_NUM_CITY
            from FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            where rc.ID (+)= ao.ID and rc.ID is not null and rc.PKD_NUM>0 connect by prior ao.ID = ao.PAR_ID start with ao.ID = :ao_id
        ";
		$param = [ "ao_id" => $ao_id ];
		$b = $this->db->queryArray($query, $param);
		$a[ 0 ][ "PKD_NUM_CITY" ] = $b[ 0 ][ "PKD_NUM_CITY" ];

		$query = "select sum(1) as OE_NUM_CITY
            from FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            where rc.ID (+)= ao.ID and rc.ID is not null and rc.OE_NUM>0 connect by prior ao.ID = ao.PAR_ID start with ao.ID = :ao_id
        ";
		$param = [ "ao_id" => $ao_id ];
		$b = $this->db->queryArray($query, $param);
		$a[ 0 ][ "OE_NUM_CITY" ] = $b[ 0 ][ "OE_NUM_CITY" ];

		$query = "select sum(1) as TDRTK_NUM_CITY
            from FOO_GIS.ATD_OBJECT ao, FOO_GIS.RTK_CITYPARAMS rc
            where rc.ID (+)= ao.ID and rc.ID is not null and rc.TDRTK_NUM>0 connect by prior ao.ID = ao.PAR_ID start with ao.ID = :ao_id
        ";
		$param = [ "ao_id" => $ao_id ];
		$b = $this->db->queryArray($query, $param);
		$a[ 0 ][ "TDRTK_NUM_CITY" ] = $b[ 0 ][ "TDRTK_NUM_CITY" ];

		return $a[ 0 ];
	}

	/**
	 * Получить список регионов СитПлан
	 *
	 * @return array|int
	 */
	public function getSPregions($dofull = true, $withltc = false)
	{
		$param = [];
		if (!$dofull) {
			// ограничиться списком регионов департамента
			$dofulltxt = "FOO_GIS.CHECKCITY(:1, ao.ID) = 1 and";
			$param = [ "1" => $this->perm->UserID ];
		}
		$query = "SELECT ao.ID, ao.NAME || ' ' || aot.NAME as NAME FROM FOO_GIS.ATD_OBJECT ao,
          FOO_GIS.ATD_OBJ_TYPE aot
          where  ao.PAR_ID is null and
          $dofulltxt
          nvl(AO.ATDOBJ_TYPE,0) = AOT.CODE(+)
          and AO.ACTUALSTATUS > 0
          ORDER BY ao.NAME";

		$a = $this->db->queryArray($query, $param);
		if (count($a) > 0) {
			return $a;
		} else {
			if ($withltc) { // если для сотрудника ЛТС - то получить его вышестоящий субъект РФ
				$a = $this->getAoListForLtc("", $withltc);

				return $a;
			}
		}

		return [];
	}

	public function getSPsubregions($ao_id, $dofull = true)
	{
		$param = [];
		if (!$dofull) {
			// ограничиться списком регионов департамента
			$dofulltxt = "FOO_GIS.CHECKCITY(:1, ao.ID) = 1 and";
			$param = [ "1" => $this->perm->UserID ];
		}
		$param[ "ao_id" ] = $ao_id;
		$query = "SELECT ao.ID, ao.NAME || ' ' || aot.NAME as NAME FROM FOO_GIS.ATD_OBJECT ao,
          FOO_GIS.ATD_OBJ_TYPE aot
          where  ao.PAR_ID = :ao_id and
          $dofulltxt
          nvl(AO.ATDOBJ_TYPE,0) = AOT.CODE(+)
          ORDER BY ao.NAME";

		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	/**
	 * Получить список муниципальных районов
	 *
	 * @param bool $dofull
	 *
	 * @return array|int
	 *
	 */
	public function getSPmuns($dofull = true)
	{
		$param = [];
		if (!$dofull) {
			// ограничиться списком регионов департамента
			$dofulltxt = "FOO_GIS.CHECKCITY(:1, ao.ID) = 1";
			$param[ "1" ] = $this->perm->UserID;
		}
		$query = "SELECT ao.ID,
                  ao.NAME || ' ' || aot.NAME as NAME,
                  aop.NAME || ' ' || aotp.NAME as PNAME
                  FROM
                  FOO_GIS.ATD_OBJECT ao,
                  FOO_GIS.ATD_OBJECT aop,
                  FOO_GIS.ATD_OBJ_TYPE aot,
                  FOO_GIS.ATD_OBJ_TYPE aotp
          where
          aop.PAR_ID is null and
          ao.PAR_ID = aop.ID and
          nvl(AO.ATDOBJ_TYPE,0) = AOT.CODE(+) and
          nvl(AOP.ATDOBJ_TYPE,0) = AOTP.CODE(+) and
          $dofulltxt
          ORDER BY PNAME, NAME";

		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	public function getDepName($ao_id)
	{
		$res = "";
		$query = "select SYS_CONNECT_BY_PATH(AU_DEPNAME, '/') as NAME
          from FOO_AUTH.AUTH_DEPARTMENTS
          where AU_DEPPARID is null
          connect by prior AU_DEPPARID = AU_DEPID
          start with AU_DEPID in (select AU_DEPID from FOO_AUTH.AUTH_DEPARTMENT_CITIES where CITY_ID = :ao_id)";
		$param = [ "ao_id" => $ao_id ];
		$a = $this->db->queryArray($query, $param);
		if (count($a) > 0) {
			$b = explode("/", $a[ 0 ][ "NAME" ]);
			for ($i = count($b) - 1; $i >= 0; $i--) {
				$res .= "/" . $b[ $i ];
			}
		}

		return ($res);
	}

	/*
     *
     */
	public function getFreeObjectsByRegion($ao_id, $table_object = "ATD_OBJECT", $table_street = "ATD_STREET")
	{
		if (strcmp($table_object, "SLTY_OBJECT") == 0) {
			$query = "select
          sa.SOURCE_DESC, ao.STATE, ao.ID, ao.PAR_ID, ao.CODE, nvl(ao.ATDOBJ_TYPE,500) as ATDOBJ_TYPE,
          nvl(aot.ABBR, ' ') as ABBR,
          sys_connect_by_path(ao.NAME, '|') as NAME,
          CONNECT_BY_ISLEAF AS ISLEAF,
          LEVEL
          from
          FOO_GIS.$table_object ao,
          FOO_GIS.ATD_OBJ_TYPE aot,
          FOO_GIS.SOURCE_ADDRESS sa
          where
          ao.ID not in (select distinct OBJ_ID from FOO_GIS.$table_street) AND
          sa.SOURCE_ID = ao.SOURCE_ID AND
          aot.CODE (+)= ao.ATDOBJ_TYPE
          connect by prior ao.ID = ao.PAR_ID
          start with ao.id = :1";
		} else {
			$query = "select
          ao.STATE, ao.ID, ao.PAR_ID, ao.CODE, nvl(ao.ATDOBJ_TYPE,500) as ATDOBJ_TYPE,
          nvl(aot.ABBR, ' ') as ABBR,
          sys_connect_by_path(ao.NAME, '|') as NAME,
          CONNECT_BY_ISLEAF AS ISLEAF,
          LEVEL
          from
          FOO_GIS.$table_object ao,
          FOO_GIS.ATD_OBJ_TYPE aot
          where
          ao.ID not in (select distinct OBJ_ID from FOO_GIS.$table_street) AND

          aot.CODE (+)= ao.ATDOBJ_TYPE
          connect by prior ao.ID = ao.PAR_ID
          start with ao.id = :1";
		}
		$param = [ "1" => $ao_id ];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	/*
     *
     */
	public function getBusyObjectsByRegion($ao_id, $table_object = "ATD_OBJECT", $table_street = "ATD_STREET")
	{
		if (strcmp($table_object, "SLTY_OBJECT") == 0) {
			$query = "select
          sa.SOURCE_DESC, ao.STATE, ao.ID, ao.PAR_ID, ao.CODE, nvl(ao.ATDOBJ_TYPE,500) as ATDOBJ_TYPE,
          nvl(aot.ABBR, ' ') as ABBR,
          sys_connect_by_path(ao.NAME, '|') as NAME,
          CONNECT_BY_ISLEAF AS ISLEAF,
          LEVEL
          from
          FOO_GIS.$table_object ao,
          FOO_GIS.ATD_OBJ_TYPE aot,
          FOO_GIS.SOURCE_ADDRESS sa
          where
          ao.ID in (select distinct OBJ_ID from FOO_GIS.$table_street) AND
          sa.SOURCE_ID = ao.SOURCE_ID AND
          aot.CODE (+)= ao.ATDOBJ_TYPE
          connect by prior ao.ID = ao.PAR_ID
          start with ao.id = :1";
		} else {
			$query = "select
          FOO_GIS.FULLNAME_OBJECT(ao.ID) as FNAME,
          ao.STATE, ao.ID, ao.PAR_ID, ao.CODE, nvl(ao.ATDOBJ_TYPE,500) as ATDOBJ_TYPE,
          nvl(aot.ABBR, ' ') as ABBR,
          sys_connect_by_path(ao.NAME, '|') as NAME,
          CONNECT_BY_ISLEAF AS ISLEAF,
          LEVEL
          from
          FOO_GIS.$table_object ao,
          FOO_GIS.ATD_OBJ_TYPE aot
          where
          ao.ID in (select distinct OBJ_ID from FOO_GIS.$table_street) AND
          aot.CODE (+)= ao.ATDOBJ_TYPE
          connect by prior ao.ID = ao.PAR_ID
          start with ao.id = :1";
		}
		$param = [ "1" => $ao_id ];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}


	/*
     * *
     */
	public function delSPAObject($ao_id)
	{
		$query = "delete from
          FOO_AUTH.AUTH_DEPARTMENT_CITIES where CITY_ID = :1";

		$param = [ "1" => $ao_id ];
		$a = $this->db->query($query, $param);

		$query = "delete from
          FOO_GIS.ATD_OBJECT where ID = :1";

		$param = [ "1" => $ao_id ];
		$a = $this->db->query($query, $param);
		$this->db->commit();

		return ("Населенный пункт успешно удален");
	}

	/**
	 * Получить список дублированных по коду КЛАДР улиц СЛТУ
	 *
	 * @param $ao_id
	 * @param $source_id
	 */
	public function getSltyDupStreet($ao_id, $source_id)
	{
		$query = "select ao.ABBR as AO_ABBR, o.NAME as AO_NAME, a.ABBR as STREET_ABBR, s.NAME as STREET_NAME, s.SLTY_ID, k.SOCR, k.NAME as KLADR_NAME, s.CODE from
         FOO_GIS.SLTY_OBJECT o,
         FOO_GIS.ATD_OBJ_TYPE ao,
         FOO_GIS.ATD_OBJ_TYPE a,
         FOO_GIS.SLTY_STREET s,
         FOO_GIS.KLADR_STREET k
          WHERE
          s.SOURCE_ID = :source_id and
          s.OBJ_ID (+)= o.ID and
          s.ID is not null and
          s.CODE in (select distinct CODE from FOO_GIS.SLTY_STREET where SOURCE_ID = :source_id having count(1) > 1 group by CODE) and
          nvl(o.ATDOBJ_TYPE,500) = ao.CODE and
          nvl(s.ATDOBJ_TYPE,500) = a.CODE and
          k.CODE (+)= s.CODE
          connect by prior o.ID = o.PAR_ID
          start with o.ID = :ao_id
          order by s.CODE, s.NAME
          ";
		$param = [
			"ao_id" => $ao_id,
			"source_id" => $source_id,
		];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/*
     *
     */
	public function delFreeObjectsByRegion($ao_id)
	{
		//echo "<br>delFreeObjectsByRegion $ao_id<br>";
		$arr = $this->getFreeObjectsByRegion($ao_id);
		//echo "<pre>";
		//print_r($arr);
		//echo "</pre>";
		foreach ($arr as $val) {
			//echo "<pre>";
			//print_r($val);
			//echo "</pre>";
			if ($val[ "ISLEAF" ] != 1) {
				continue;
			}
			$type = $val[ "ATDOBJ_TYPE" ];
			if ($type < 300) {
				continue;
			}
			if ($type == 399) {
				continue;
			}
			$query = "delete from
          FOO_AUTH.AUTH_DEPARTMENT_CITIES where CITY_ID = :1";
			$id = 0;
			$id = $val[ "ID" ];
			$param = [ "1" => $id ];
			$a = $this->db->query($query, $param);
			//$this->db->commit();
			//echo "<br>($query  ($id)) ($type)<br>";
			$query = "delete from
          FOO_GIS.ATD_OBJECT where ID = :1";
			$id = 0;
			$id = $val[ "ID" ];
			$param = [ "1" => $id ];
			$a = $this->db->query($query, $param);
			$this->db->commit();
			//echo "<br>($query  ($id)) ($type)<br>";
			flush();
		}

		$a = $this->getFreeObjectsByRegion($ao_id);

		return $a;
	}

	/*
     *
     */
	public function conFreeObjectsByRegion($ao_id)
	{
		$query = "select
          ao.ID, ao.PAR_ID, nvl(ao.ATDOBJ_TYPE,500) as ATDOBJ_TYPE,
          CONNECT_BY_ISLEAF AS ISLEAF,
          LEVEL, ao.CODE, ao.NAME
          from
          FOO_GIS.ATD_OBJECT ao

          connect by prior ao.ID = ao.PAR_ID
          start with ao.id = :1";
		$param = [ "1" => $ao_id ];
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		for ($i = 0; $i < count($a); $i++) {
			$a[ $i ][ "parind" ] = -1;
			for ($j = 0; $j < count($a); $j++) {
				if ($a[ $i ][ "PAR_ID" ] == $a[ $j ][ "ID" ]) {
					$a[ $i ][ "parind" ] = $j;
					break;
				}
			}
		}
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		for ($i = 0; $i < count($a); $i++) {
			/*
            $curcode = $a[$i]["ATDOBJ_TYPE"];
            if($curcode == 399)
                continue;
            if($curcode >= 300){
                $newcode = 300;
            }
            else if ($curcode >= 200) {
                $newcode = 200;
            }
            else {
                continue;
            }
            */
			for ($parind = $a[ $i ][ "parind" ]; $parind >= 0; $parind = $a[ $parind ][ "parind" ]) {
				if ($a[ $i ][ "ID" ] == 27111) {
					echo "<br>ID=" . $a[ $i ][ "ID" ] . " родитель PAR_ID=" . $a[ $i ][ "PAR_ID" ] . " проверяем ID=" . $a[ $parind ][ "ID" ] . "с кодом (" . $a[ $parind ][ "CODE" ] . ")<br>";
				}
				/* if($a[$parind]["ATDOBJ_TYPE"] < $newcode) { */
				//if($a[$i]["ID"] == 3308) {
				//    echo "<br>Родитель ID=(".$a[$parind]["ID"].") название (".$a[$parind]["NAME"].") код КЛАДР=(".$a[$parind]["NAME"].")<br>";
				//}
				if (($a[ $parind ][ "CODE" ] != "") && (ctype_digit($a[ $parind ][ "CODE" ]))) {
					//if($a[$i]["ID"] == 3308) {
					//    echo "<br>Нашли<br>";
					//}
					if ($a[ $i ][ "PAR_ID" ] == $a[ $parind ][ "ID" ]) {
						break;
					}
					//if($a[$i]["ID"] == 3308) {
					//    echo "<br>Обновляем<br>";
					//}
					$query = "update FOO_GIS.ATD_OBJECT set PAR_ID = :1 where ID = :2";
					$param = [
						"1" => $a[ $parind ][ "ID" ],
						"2" => $a[ $i ][ "ID" ],
					];
					//echo "<br>($query) перепривязываем ID=".$a[$i]["ID"]." от родителя PAR_ID=".$a[$i]["PAR_ID"]." к родителю PAR_ID=".$a[$parind]["ID"]."<br>";
					$this->db->query($query, $param);
					$this->db->commit();
					break;
				}
			}
		}
		$a = $this->getFreeObjectsByRegion($ao_id);

		return $a;
	}

	/*
        *
        */
	public function conKLADRObjectsByRegion($ao_id)
	{
		$result = [];

		$st_types = [
			"автономный округ",
			"автономная область",
			"край",
			"область",
			"район",
			"республика",
			"автономный",
		];


		$query = "select
          ao.COD_OKATO, ao.ID, ao.NAME,ao.PAR_ID, nvl(ao.ATDOBJ_TYPE,500) as ATDOBJ_TYPE
          from
          FOO_GIS.ATD_OBJECT ao
          connect by prior ao.ID = ao.PAR_ID
          start with ao.id = :1";
		$param = [ "1" => $ao_id ];
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		for ($i = 0; $i < count($a); $i++) {
			$str = mb_strtolower(trim($a[ $i ][ "NAME" ]));
			for ($zz = 0; $zz < count($st_types); $zz++) {
				$str = trim(mb_ereg_replace($st_types[ $zz ], "", $str));
			}
			$str = trim(mb_ereg_replace("\((.*)\)", "", $str));
			$a[ $i ][ "CH_NAME" ] = $str;
			$query = "
                select * from FOO_GIS.KLADR_MAIN where lower(trim(NAME)) = :1 and OCATD = :2 AND STATE = 0";
			$param = [
				"1" => $a[ $i ][ "CH_NAME" ],
				"2" => $a[ $i ][ "COD_OKATO" ],
			];
			$b = $this->db->queryArray($query, $param);
			if (count($b) == 1) {
				$query = "update FOO_GIS.ATD_OBJECT set CODE = :1,NAME = :3 where ID = :2";
				$param = [
					"1" => $b[ 0 ][ "CODE" ],
					"2" => $a[ $i ][ "ID" ],
					"3" => $b[ 0 ][ "NAME" ],
				];
				$this->db->query($query, $param);
				$this->db->commit();
				//echo "<br>Нашли для ".$a[$i]["NAME"]."-".$a[$i]["COD_OKATO"]." запись в КЛАДР ".$b[0]["NAME"]."-".$b[0]["OKATD"]." с кодом ".$b[0]["CODE"]."<br>";
			} else {
				//echo "<br>Не нашли для ".$a[$i]["NAME"]."-".$a[$i]["COD_OKATO"]." запись в КЛАДР ".count($b)."<br>";
				$a[ $i ][ "cnt" ] = count($b);
				$result[] = $a[ $i ];
			}
		}

		return $result;
	}

	/*
     *
     */
	public function loadKLADRObjectsByRegion2($ao_id, $level)
	{
		//echo "<br>Уровень $level<br>";
		if ($level == 2) {
			$codeb = 199;
			$codee = 300;
		} else {
			if ($level == 3) {
				$codeb = 299;
				$codee = 400;
			} else {
				if ($level == 4) {
					$codeb = 399;
					$codee = 500;
				}
			}
		}
		$parb = 1;
		$parc = 2;
		$result = [];
		// Получить код кладр элемента СитПлан
		$query = "select ID,NAME,CODE from FOO_GIS.ATD_OBJECT where ID = :1";
		$param = [ "1" => $ao_id ];
		$a = $this->db->queryArray($query, $param);

		if (!ctype_digit($a[ 0 ][ "CODE" ])) {
			echo "<br>Ошибка в коде КЛАДР (" . $a[ 0 ][ "CODE" ] . ") населенного пункта " . $a[ 0 ][ "NAME" ] . "<br>";

			return $result;
		}
		$code = substr($a[ 0 ][ "CODE" ], $parb - 1, $parc);
		// Получить список адресов КЛАДР имеющих родителем уровня наш код
		$query = "select * from FOO_GIS.KLADR_MAIN where substr(CODE, $parb, $parc) = :1 and STATE = 0";
		$param = [ "1" => $code ];
		$a = $this->db->queryArray($query, $param);

		// Цикл по элементам КЛАДР
		for ($i = 0; $i < count($a); $i++) {
			//echo "<pre>";
			//print_r($a[$i]);
			//echo "</pre>";
			$codei = $a[ $i ][ "CODE" ];
			$p1 = substr($codei, 0, 2);
			$p2 = substr($codei, 2, 3);
			$p3 = substr($codei, 5, 3);
			$p4 = substr($codei, 8, 3);
			if ($p4 != 0) {
				$lv = 4;
			} else {
				if ($p3 != 0) {
					$lv = 3;
				} else {
					if ($p2 != 0) {
						$lv = 2;
					} else {
						$lv = 1;
					}
				}
			}
			if ($lv == $level) {
				//if(strcmp($codei,'7600300010000') == 0) {
				//    echo "<br>Уровень $level (". $a[$i].") (".$codei.")<br>";
				//}
				// нашли элемент нужного уровня
				//echo "<br>Найден элемент уровня $level<br>";
				// Получить элемент СитПлан по коду КЛАДР
				$query = "select ID, PAR_ID, NAME,CODE from FOO_GIS.ATD_OBJECT where CODE = :1";
				$param = [ "1" => $codei ];
				$b = $this->db->queryArray($query, $param);
				//echo "<br>Ситплан<br>";
				//echo "<pre>";
				//print_r($b);
				//echo "</pre>";
				$codep = $this->getKLADRparent($codei);
				//echo "<br>Код (".$codei.") родитель (".$codep.")<br>";
				// Получить родителя элемента СитПлан по коду КЛАДР
				$query = "select ID, PAR_ID, NAME,CODE from FOO_GIS.ATD_OBJECT where CODE = :1";
				$param = [ "1" => $codep ];
				$c = $this->db->queryArray($query, $param);
				//echo "<br>Ситплан родитель<br>";
				//echo "<pre>";
				//print_r($c);
				//echo "</pre>";

				if (count($c) == 0) { // такого родителя нет в СитПлан
					$result[ "noparent" ][] = $a[ $i ];
					continue;
				}
				//echo "<br>Найден родитель<br>";

				if (count($b) == 0) { // такого элемента нет в СитПлан
					$query = "insert into FOO_GIS.ATD_OBJECT (
                  ID,
                  PAR_ID,
                  ATDOBJ_TYPE,
                  COD_OKATO,
                  NAME,
                  SNAME,
                  FULLNAME,
                  CODE,
                  STATE)
                  values (
                  FOO_GIS.ATD_OBJECT_SEQ.nextval,
                  :1,
                  (select CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :2 and CODE < $codee and CODE > $codeb),
                  :3,
                  :4,
                  :4,
                  :4,
                  :5,
                   0)";
					$param = [
						"1" => $c[ 0 ][ "ID" ],
						"2" => $a[ $i ][ "SOCR" ],
						"3" => $a[ $i ][ "OCATD" ],
						"4" => $a[ $i ][ "NAME" ],
						"5" => $a[ $i ][ "CODE" ],
					];
					$this->db->query($query, $param);
					$this->db->commit();
					$result[ "loaded" ][] = $a[ $i ];
					//echo "<br>вставили<br>";
					//echo "<br>($query)<br>";
					//echo "<pre>";
					//print_r($param);
					//echo "</pre>";
				} else {
					//echo "<br>Элемент уже есть<br>";
					$result[ "notloaded" ][] = $a[ $i ];
				}

			}
		}

		return $result;
	}

	/*
     * вычислить код КЛАДР родительского элемента
     */
	private function getKLADRparent($code)
	{
		$le = 0;
		$p1 = substr($code, 0, 2);
		$p2 = substr($code, 2, 3);
		$p3 = substr($code, 5, 3);
		$p4 = substr($code, 8, 3);
		if (strlen($code) == 17) {
			$p5 = substr($code, 11, 4);
			if ($p5 != 0) {
				$le = 5;
			}
		}
		if ($le == 0) {
			if ($p4 != 0) {
				$le = 4;
			} else {
				if ($p3 != 0) {
					$le = 3;
				} else {
					if ($p2 != 0) {
						$le = 2;
					} else {
						$le = 1;
					}
				}
			}
		}
		if ($le == 5) {
			$par_code = sprintf("%02d%03d%03d%03d00", $p1, $p2, $p3, $p4);
		} else {
			if ($le == 4) {
				$par_code = sprintf("%02d%03d%03d00000", $p1, $p2, $p3);
			} else {
				if ($le == 3) {
					$par_code = sprintf("%02d%03d00000000", $p1, $p2);
				} else {
					if ($le == 2) {
						$par_code = sprintf("%02d00000000000", $p1);
					} else {
						$par_code = '';
					}
				}
			}
		}

		return ($par_code);
	}

	/*
     *
     */
	public function loadCityParams($file, $issum = true, $logmode = 0)
	{
		$log = "";
		$bg = 0;

		$notloaded = [];
		$res = "";
		//echo "<pre>";
		//print_r($file);
		//echo "</pre>";
		for ($i = 0; $i < count($file[ 'name' ]); $i++) {
			if (isset($file[ 'name' ][ $i ]) && strlen($file[ 'name' ][ $i ]) != 0) {
				//echo "Загружаем файл '". $file['name'][$i]."', который залит на сервер с именем '".$file['tmp_name'][$i]."'<br>";
				// открыть файл на чтение
				if (!($fp = fopen($file[ 'tmp_name' ][ $i ], 'r'))) {
					$result[ 'error' ][] = "ошибка открытия файла " . $file[ 'tmp_name' ][ $i ];
					continue;
				}
				$x = 1;
				$good_cnt = 0;
				$cnt = 0;
				if (!($line = fgets($fp))) {
					$result[ 'error' ][] = "on line $x: ошибка чтения файла " . $file[ 'name' ][ $i ];
					fclose($fp);
					continue;
				}
				// разобрать строку на элементы
				$names = $this->explodeLine($line);

				if ((count($names) != 6) && (count($names) != 7)) {
					$result[ 'error' ][] = "on line $x: ошибка формата файла " . $file[ 'name' ][ $i ] . ",получено " . count(
							$names
						) . " полей, ожидается 6 или 7";
					fclose($fp);
					continue;
				}
				$columns = $this->getTableColumns("FOO_GIS.RTK_CITYPARAMS");
				for ($k = 0; $k < count($columns); $k++) {
					if (strcmp($names[ 5 ], $columns[ $k ][ "NAME" ]) == 0) {
						$param = $columns[ $k ];
						break;
					}
				}
				if (!isset($param)) {
					$result[ 'error' ][] = "on line $x: ошибка формата файла " . $file[ 'name' ][ $i ] . ",неизвестен параметр " . $names[ 5 ];
					fclose($fp);
					continue;
				}

				//echo "<pre>";
				//print_r($param);
				//print_r($columns);
				//echo "</pre>";
				//continue;
				// цикл чтения
				$ln = iconv('cp1251', 'UTF-8', $line);
				if (count($names) == 6) {
					$ln .= "|Идентификатор населенного пункта СП";
				}
				echo "$ln<br>";
				while (($line = fgets($fp)) !== false) {
					$x++;
					$cnt++;
					try { // разобрать строку на элементы
						$line = str_replace("\xA0", " ", $line);
						$ln = iconv('cp1251', 'UTF-8', $line);
						$arr = $this->explodeLine($ln);
						if (count($arr) == 7) {
							// есть AO_ID
							$ao_id = $arr[ 6 ];
							//echo "ao_id = $ao_id<br>";
						} else {
							unset($ao_id);
						}
						if ((count($arr) != 6) && (count($arr) != 7)) {
							$result[ 'error' ][] = "on line $x: ошибка формата файла " . $file[ 'name' ][ $i ] . ",получено " . count(
									$arr
								) . " полей, ожидается 6 или 7";
							//fclose($fp);
							break;
							//continue;
						}
						//echo "<pre>";
						//print_r($arr);
						//echo "</pre>";
						$err = "";
						$log = "";
						if ($this->checkSPparamsType($param[ "NAME" ], $arr[ 5 ], $err) != 0) {
							$ln .= " : $err";
							$res = 0;
						} else {
							$log = "";
							$res = $this->loadCityParamLineDB($arr, $param, $ln, $issum, $ao_id, $log, $logmode);
						}
						if ($res != 0) {
							$good_cnt++;
						}
						if ($res != 0) {
							if (count($arr) == 6) {
								// нашли населенный пункт и загрузили параметр, сохраняем исходную строку

								$ao_id = $res;

								$ln = trim($ln) . "|$res";
							}
							if ($logmode != 0) {
								$ln = trim($ln) . "|$log";
							}
							// сохраняем исходную строку
							//$par[ "VALUE" ] = trim(mb_ereg_replace("\|/**/", ";", trim($ln)));
							//$par[ "NAME" ] = "REPORT_STR";
							//$ret = $this->insSPparam($ao_id, $par, $issum);
						} else {
							$ln = trim($ln) . "|$log";
						}

						echo "$ln<br>";
						//$notloaded[]["LINE"] = $ln;
						//$result[ 'error' ][ ] = "on line $i: ошибка ".$res;
						//}
						//echo ". ";
						//ob_flush();
						//flush();


					} catch (Exception $e) {
						$result[ 'error' ][] = "on line $x: " . $e->getMessage();
						continue;
					}
				}
			}
			fclose($fp);
			echo "<br>Обработано строк: $cnt, загружено: $good_cnt<br>";
		}
		fclose($fp);
		for ($i = 0; $i < count($result[ "error" ]); $i++) {
			echo "<br>" . $result[ "error" ][ $i ] . "<br>";
		}

		return ($notloaded);
	}

	/*
       *
       */
	public function loadTehnograd($file)
	{
		$address = [];
		$bg = 0;
		$types = $this->getallTypes();

		$notloaded = [];

		for ($i = 0; $i < count($file[ 'name' ]); $i++) {

			if (isset($file[ 'name' ][ $i ]) && strlen($file[ 'name' ][ $i ]) != 0) {
				if (mb_strstr(mb_strtolower($file[ 'name' ][ $i ]), ".") !== false) {
					$nameo = $file[ 'tmp_name' ][ $i ];
					$namen = "$nameo.unzip";
					//echo "<br>usr/bin/unzip -p $nameo > $namen<br>";
					if (system("/usr/bin/unzip -p $nameo > $namen", $retvar) !== false) {
						$namen = "$nameo.unzip";
						echo "<br>Файл " . $file[ 'name' ][ $i ] . " разархивирован<br>";
					} else {
						echo "<br>Ошибка разархивирования загруженного файла $nameo с диагностикой $retvar<br>";
						break;
					}
				} else {
					$namen = $file[ 'tmp_name' ][ $i ];
				}
				//echo "Загружаем файл '". $file['name'][$i]."', который залит на сервер с именем '".$file['tmp_name'][$i]."'<br>";
				// открыть файл на чтение
				if (!($fp = fopen($namen, 'r'))) {
					echo "<br>ошибка открытия файла " . $namen . "<br>";
					break;
				}
				$i = 1;

				if (!($line = fgets($fp))) {
					echo "<br>on line $i: ошибка чтения файла " . $namen . "<br>";
					fclose($fp);
					break;
				}

				while (($line = fgets($fp)) !== false) {
					$i++;
					try { // разобрать строку на элементы
						$ln = iconv('cp1251', 'UTF-8', $line);
						$lnarr = $this->explodeLine($ln, ";", true);
						//echo "<pre>";
						//print_r($lnarr);
						//echo "</pre>";
						if (count($lnarr) != 9) {
							echo "<br>on line $i: ошибка формата файла " . $file[ 'name' ][ $i ] . ",получено " . count(
									$lnarr
								) . " полей, ожидается 9<br>";
							fclose($fp);
							break;
						}
						$res = $this->loadTehnoDB($lnarr, $types, $ln);
						if ($res != 0) {
							if ($bg == 0) {
								echo "<br>Не загружено строк файла<br>";
								$bg = 1;
							}
							echo "<br>$ln<br>";
							//$notloaded[]["LINE"] = $ln;
							//$result[ 'error' ][ ] = "on line $i: ошибка ".$res;
						}
					} catch (Exception $e) {
						echo "<br>on line $i: " . $e->getMessage() . "<br>";
						break;
					}
				}
			}
		}
		fclose($fp);
		if (strstr($namen, ".unzip") !== false) {
			echo "<br>Удаление временного файла $namen<br>";
			system("/bin/rm $namen", $retvar);
			echo "<br>$retvar<br>";
		}
		//echo "<pre>";
		//echo "<br>Полученная адресная база:<br>";
		//print_r($address);
		//echo "</pre>";
		return ($notloaded);
	}

	/*
    * Получить элемент КЛАДР по его идентификатору
    */
	public function getKLADRbyID($kladr_city_id)
	{
		$query = "select * from FOO_GIS.KLADR_MAIN where ID = :1";
		$param = [ "1" => $kladr_city_id ];
		$a = $this->db->queryArray($query, $param);

		return ($a[ 0 ]);
	}

	/*
    * Получить элемент КЛАДР по его коду
    */
	public function getKLADRbyCode($code)
	{
		$query = "select * from FOO_GIS.KLADR_MAIN where CODE = :1";
		$param = [ "1" => $code ];
		$a = $this->db->queryArray($query, $param);

		return ($a[ 0 ]);
	}

	/*
        * Получить элемент КЛАДР по его коду
        */
	public function getKLADRSbyCode($code)
	{
		$query = "select * from FOO_GIS.KLADR_STREET where CODE = :1";
		$param = [ "1" => $code ];
		$a = $this->db->queryArray($query, $param);

		return ($a[ 0 ]);
	}

	/*
   * Получить элемент СП по его коду
   */
	public function getSPbyCode($code)
	{
		$query = "select * from FOO_GIS.ATD_OBJECT where CODE = :1";
		$param = [ "1" => $code ];
		$a = $this->db->queryArray($query, $param);

		return ($a[ 0 ]);
	}

	/*
     * Получить элемент СП по его идентификатору
     */
	public function getSPbyID($id)
	{
		$query = "select * from FOO_GIS.ATD_OBJECT where ID = :1";
		$param = [ "1" => $id ];
		$a = $this->db->queryArray($query, $param);

		return ($a[ 0 ]);
	}

	/*
  * Получить элемент СЛТУ по его коду
  */
	public function getSLTYbyCode($code)
	{
		$query = "select * from FOO_GIS.SLTY_OBJECT where CODE = :1";
		$param = [ "1" => $code ];
		$a = $this->db->queryArray($query, $param);

		return ($a[ 0 ]);
	}

	/*
    * вычислить уровень по коду КЛАДР
    */
	public function calcKLADRlevelByCode($codei)
	{

		$p1 = substr($codei, 0, 2);
		$p2 = substr($codei, 2, 3);
		$p3 = substr($codei, 5, 3);
		$p4 = substr($codei, 8, 3);
		if ($p4 != 0) {
			$lv = 4;
		} else {
			if ($p3 != 0) {
				$lv = 3;
			} else {
				if ($p2 != 0) {
					$lv = 2;
				} else {
					$lv = 1;
				}
			}
		}

		return ($lv);
	}

	/*
    * Загрузить элемент СЛТУ по элементу КЛАДР
    */
	public function insSLTYbyKLADR($a, $par_id)
	{
		$query = "insert into
                FOO_GIS.SLTY_OBJECT (ID, PAR_ID, NAME, ABBR, SLTY_ID, CODE, SOURCE_ID, ATDOBJ_TYPE)
                values (FOO_GIS.SLTY_OBJECT_SEQ.nextval,:3, :1, :2,:4, :6, :7,
                    nvl(
                        (select distinct CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :2 and FLOOR(CODE/100) = :5),
                        nvl(
                            (select distinct CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :2 and FLOOR(CODE/100) = (:5+1)),
                            (select distinct CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :2 and FLOOR(CODE/100) = (:5-1))
                        )
                    )
                )
            ";
		$level = $this->calcKLADRlevelByCode($a[ "CODE" ]);
		$param = [
			"1" => $a[ "NAME" ],
			"2" => $a[ "SOCR" ],
			"3" => $par_id,
			"4" => null,
			"5" => $level,
			"6" => $a[ "CODE" ],
			"7" => $this->source_id,
		];
		$this->db->query($query, $param);
		$this->db->commit();

		return;
	}

	/*
     * Создать цепочку населенных пунктов в SLTY_OBJECT
     */
	public function createSLTYcity($kladr_city_id)
	{
		// получить элемент КЛАДР по его идентификатору
		$a = $this->getKLADRbyID($kladr_city_id);
		// получить элемент СЛТУ по коду элемента КЛАДР
		$b = $this->getSLTYbyCode($a[ "CODE" ]);
		//echo "<pre>";
		//echo "<br>A<br>";
		//print_r($a);
		//echo "<br>Bcur<br>";
		//print_r($b);
		//echo "</pre>";
		if (isset($b[ "ID" ])) {
			// элемент найден - восстанавливать не надо

		} else {
			if (isset($a[ "PAR_ID" ]) && (strlen($a[ "PAR_ID" ]) != 0)) {
				// проверить-создать родительский элемент
				$c = $this->createSLTYcity($a[ "PAR_ID" ]);
				//echo "<pre>";
				//echo "<br>C<br>";
				//print_r($c);
				//echo "</pre>";
				$par_id = $c[ "ID" ];
			} else {
				$par_id = null;
			}
			// создать элемент СЛТУ из элемента КЛАДР
			$this->insSLTYbyKLADR($a, $par_id);
			// получить элемент СЛТУ по коду элемента КЛАДР
			$b = $this->getSLTYbyCode($a[ "CODE" ]);
			//echo "<pre>";
			//echo "<br>Bnew<br>";
			//print_r($b);
			//echo "</pre>";

		}

		return ($b);
	}

	/*
    * Загрузить файл Спб
    */
	public function loadSPbCSV($file, $kladr_city_id, $source_id = 1)
	{
		$bg = 0;
		$iszip = false;
		$notloaded = [];
		$i = 0;
		$badcnt = 0;
		$goodcnt = 0;

		//echo "<br>$kladr_city_id $source_id<br>";
		$this->source_id = $source_id; // установить источник данных
		// проверить-создать цепочку населенных пунктов КЛАДР в СЛТУ
		$a = $this->createSLTYcity($kladr_city_id);
		// идентификатор созданного-проверенного населенного пункта СЛТУ
		$city_id = $a[ "ID" ];
		// загрузка файла
		$this->types = $this->getallTypes();
		if (isset($file[ 'name' ]) && strlen($file[ 'name' ]) != 0) {
			if (mb_strstr(mb_strtolower($file[ 'name' ]), ".zip") !== false) {
				$nameo = $file[ 'tmp_name' ];
				$namen = "$nameo.unzip";
				//echo "<br>usr/bin/unzip -p $nameo > $namen<br>";
				if (system("/usr/bin/unzip -p $nameo > $namen", $retvar) !== false) {
					$namen = "$nameo.unzip";
					echo "<br>Файл " . $file[ 'name' ] . " разархивирован<br>";
				} else {
					echo "<br>Ошибка разархивирования загруженного файла $nameo с диагностикой $retvar<br>";

					return;
				}
				$iszip = true;
			} else {
				$namen = $file[ 'tmp_name' ];
			}
			//echo "Загружаем файл '". $file['name'][$i]."', который залит на сервер с именем '".$file['tmp_name'][$i]."'<br>";
			// открыть файл на чтение
			if (!($fp = fopen($namen, 'r'))) {
				throw new Exception("Ошибка открытия файла $namen");
			}


			if (!($line = fgets($fp))) {
				throw new Exception("Оn line $i: ошибка чтения файла $namen");
			}

			while (($line = fgets($fp)) !== false) {
				$i++;
				try { // разобрать строку на элементы
					$ln = iconv('cp1251', 'UTF-8', $line);
					$lnarr = $this->explodeLine($ln, "|", true);

					/*
                    * Наименование улицы
                    * тип улицы
                     * Номер дома
                     * Корпус
                    */
					if (count($lnarr) != 12) {
						throw new Exception(
							"On line $i: ошибка формата файла " . $file[ 'name' ] . ",получено " . count(
								$lnarr
							) . " полей, ожидается 12"
						);
					}
					$res = $this->loadSPbDB($lnarr, $city_id);
					if ($res != 0) {
						$badcnt++;
						if ($bg == 0) {
							echo "<br>Не загружено строк файла<br>";
							$bg = 1;
						}
						echo "<br>$ln<br>";
					} else {
						$goodcnt++;
					}
				} catch (Exception $e) {
					echo "<br>on line $i: " . $e->getMessage() . "<br>";
					if ($fp) {
						fclose($fp);
						unset($fp);
					}
					if ($iszip) {
						echo "<br>Удаление временного файла $namen<br>";
						system("/bin/rm $namen", $retvar);
						echo "<br>$retvar<br>";
					}

					return;
				}
			}
		}
		if ($fp) {
			fclose($fp);
			unset($fp);
		}
		if ($iszip) {
			echo "<br>Удаление временного файла $namen<br>";
			system("/bin/rm $namen", $retvar);
			echo "<br>$retvar<br>";
		}
		echo "<br>Загрузка завершена: в файле $i строк, обработано успешно $goodcnt, обработано с ошибкой $badcnt<br>";

		return;
	}

	/*
   *
   */
	private function normSPbType($type)
	{

		$types = [
			"бульвар" => "б-р",
			"бул" => "б-р",
			"пр" => "пр-кт",
			"шоссе" => "ш",
		];
		$type = $this->normTehnoStrNew(mb_ereg_replace("\.", "", $type));
		// замена сокращений
		foreach ($types as $k => $v) {
			$type = mb_ereg_replace($k, $v, $type);
		}
		if (mb_strlen($type) == 0) {
			$type = 'ул';
		}

		return ($type);
	}

	/*
     *
     */
	private function loadSpbDB(&$arr, $city_id)
	{
		//echo "<pre>";
		//print_r($arr);
		//echo "</pre>";
		$st = [];
		$ad = [];

		$st[ "NORM" ] = $this->normTehnoStrNew($arr[ 0 ]);
		$st[ "NAME" ] = $arr[ 0 ];
		// тип улицы
		$st[ "ABBR" ] = $this->normSPbType($arr[ 1 ]);
		$st[ "LEVEL" ] = 5;
		$st[ "TABLE" ] = "SLTY_STREET";
		$st[ "PAR_ID" ] = $city_id;
		//echo "<pre>";
		//echo "<br>STREET<br>";
		//print_r($st);
		//echo "</pre>";

		// проверить элемент в БД
		$a = $this->getSltyByName($st);
		if ($st[ "RESULT" ] == 0) {
			// Элемента еще нет в БД
			$this->insSltyByName($st);
			$a = $this->getSltyByName($st);
			if ($st[ "RESULT" ] == 0) {
				echo "<br>Ошибка добавления в БД элемента <br>";
				echo "<pre>";
				print_r($st);
				print_r($a);
				echo "</pre>";

				return (-1);
			}
		}
		// загружена-проверена улица
		$street_id = $st[ "ID" ];
		//echo "<pre>";
		//echo "<br>STREET in DB<br>";
		//print_r($st);
		//echo "</pre>";
		$ad[ "STR_STD1" ] = $street_id;
		$ad[ "CITY_ID" ] = $city_id;
		$ad[ "OBJ_ID" ] = $city_id;
		if (isset($arr[ 2 ]) && (strlen($arr[ 2 ]) != 0)) {
			$ad[ "NUMBER1" ] = $arr[ 2 ];
		} else {
			$ad[ "NUMBER1" ] = "";
		}
		if (isset($arr[ 3 ]) && (strlen($arr[ 3 ]) != 0)) {
			$ad[ "NUMBER2" ] = $arr[ 3 ];
		} else {
			$ad[ "NUMBER2" ] = "";
		}
		if (isset($arr[ 4 ]) && (strlen($arr[ 4 ]) != 0)) {
			$ad[ "VLADENIE" ] = $arr[ 4 ];
		} else {
			$ad[ "VLADENIE" ] = "";
		}
		if (isset($arr[ 5 ]) && (strlen($arr[ 5 ]) != 0)) {
			$ad[ "TANK" ] = $arr[ 5 ];
		} else {
			$ad[ "TANK" ] = "";
		}
		if (isset($arr[ 6 ]) && (strlen($arr[ 6 ]) != 0)) {
			$ad[ "STRUCT" ] = $arr[ 6 ];
		} else {
			$ad[ "STRUCT" ] = "";
		}
		if (isset($arr[ 7 ]) && (strlen($arr[ 7 ]) != 0)) {
			$ad[ "LIT_N1" ] = $arr[ 7 ];
		} else {
			$ad[ "LIT_N1" ] = "";
		}
		if (isset($arr[ 8 ]) && (strlen($arr[ 8 ]) != 0)) {
			$ad[ "LIT_N2" ] = $arr[ 8 ];
		} else {
			$ad[ "LIT_N2" ] = "";
		}
		if (isset($arr[ 9 ]) && (strlen($arr[ 9 ]) != 0)) {
			$ad[ "LIT_TN" ] = $arr[ 9 ];
		} else {
			$ad[ "LIT_TN" ] = "";
		}
		if (isset($arr[ 10 ]) && (strlen($arr[ 10 ]) != 0)) {
			$ad[ "LIT_ST" ] = $arr[ 10 ];
		} else {
			$ad[ "LIT_ST" ] = "";
		}
		if (isset($arr[ 11 ]) && (strlen($arr[ 11 ]) != 0)) {
			$ad[ "LIT_VL" ] = $arr[ 11 ];
		} else {
			$ad[ "LIT_VL" ] = "";
		}

		$ad[ "SOURCE_ID" ] = $this->source_id;
		$a = $this->getSLTYaddress($ad);
		//echo "<pre>";
		//print_r($ad);
		//echo "</pre>";
		if ($ad[ "RESULT" ] == 0) {
			// Элемента еще нет в БД
			$this->insSLTYaddress($ad);
			$a = $this->getSLTYaddress($ad);
			if ($ad[ "RESULT" ] == 0) {
				echo "<br>Ошибка добавления в БД элемента <br>";
				echo "<pre>";
				print_r($ad);
				print_r($a);
				echo "</pre>";

				return (-1);
			}
		} else {
			//echo "<pre>";
			//echo "<br>Адрес уже в БД<br>";
			//print_r($st);
			//print_r($ad);
			//echo "</pre>";
		}

		return (0);
	}

	/*
           * удалить регион
           */
	public function delSLTYregion($ao_id, $docommit = false)
	{
		//echo "<br>go delSLTYRegion: ao_id=$ao_id<br>";
		$param = [
			"1" => $ao_id,
			"2" => $this->source_id,
		];
		// получить список населенных пунктов
		$query = "select * from FOO_GIS.SLTY_OBJECT
          where
          SOURCE_ID = :2 AND
          ID in (select OBJ_ID from FOO_GIS.SLTY_STREET where SOURCE_ID = :2)
          connect by prior id = par_id start with id = :1";
		$param = [
			"1" => $ao_id,
			"2" => $this->source_id,
		];
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		// цикл удаления населенных пунктов
		for ($i = 0; $i < count($a); $i++) {
			//echo "<br>go delSLTYcity: name=".$a[$i]["NAME"]." id=".$a[$i]["ID"]."<br>";
			$this->delSLTYcity($a[ $i ][ "ID" ]);
		}

		// удалить оставшиеся населенные пункты
		while (1) {
			// бесконечный цикл выборки дерева
			$query = "select ID, CONNECT_BY_ISLEAF as ISLEAF from FOO_GIS.SLTY_OBJECT WHERE
            SOURCE_ID = :2 CONNECT BY PRIOR ID = PAR_ID START WITH ID = :1";
			$param = [
				"1" => $ao_id,
				"2" => $this->source_id,
			];
			$a = $this->db->queryArray($query, $param);
			//echo "<pre>";
			//print_r($a);
			//echo "</pre>";
			// если удалили все - выход из цикла
			if (count($a) == 0) {
				break;
			}
			// цикл по элементам дерева
			for ($i = 0; $i < count($a); $i++) {
				// удаляем листья
				if ($a[ $i ][ "ISLEAF" ] != 1) {
					continue;
				}
				// удалить лист
				$query = "delete from FOO_GIS.SLTY_OBJECT where ID = :1 AND SOURCE_ID = :2";
				$param = [
					"1" => $a[ $i ][ "ID" ],
					"2" => $this->source_id,
				];
				$this->db->query($query, $param);
			}
		}
		if ($docommit) {
			$this->db->commit();
		}
	}

	/*
       * удалить населенный пункт
       */
	public function delSLTYcity($city_id, $docommit = false)
	{
		$param = [
			"1" => $city_id,
			"2" => $this->source_id,
		];
		//echo "<br>delSLTYcity: city_id=$city_id<br>";
		// удалить дома
		$query = "delete from FOO_GIS.SLTY_ADDRESS where CITY_ID = :1 AND SOURCE_ID = :2";
		$param = [
			"1" => $city_id,
			"2" => $this->source_id,
		];
		$this->db->query($query, $param);
		// удалить улицы
		$query = "delete from FOO_GIS.SLTY_STREET where OBJ_ID = :1 AND SOURCE_ID = :2";
		$param = [
			"1" => $city_id,
			"2" => $this->source_id,
		];
		$this->db->query($query, $param);
		// удалить населенный пункт
		$query = "delete from FOO_GIS.SLTY_OBJECT where ID = :1 AND SOURCE_ID = :2";
		$param = [
			"1" => $city_id,
			"2" => $this->source_id,
		];
		$this->db->query($query, $param);
		if ($docommit) {
			$this->db->commit();
		}
	}

	/*
        *
        */
	public function insSLTYaddress($ad)
	{
		$param = [];
		$query = "insert into FOO_GIS.SLTY_ADDRESS (ADDR_ID";
		$past = " values (FOO_GIS.SLTY_ADDRESS_SEQ.nextval";
		foreach ($ad as $k => $v) {
			//echo "<br>($k) ($v)<br>";
			if (strcmp($k, "RESULT") != 0) {
				if (strlen($v) != 0) {
					$query .= ",$k";
					$past .= ",:$k";
					$param[ $k ] = $v;
				}
			}
		}
		$query = $query . ")" . $past . ")";
		reset($ad);
		//echo "<pre>";
		//echo "<br>query = ($query)<br>";
		//print_r($param);
		//echo "</pre>";
		$this->db->query($query, $param);
		$this->db->commit();

		return;
	}

	/*
     *
     */
	public function getSLTYaddress(&$ad)
	{
		$param = [];
		$bg = 0;
		//echo "<pre>";
		//print_r($ad);
		//echo "</pre>";
		$query = "select * from FOO_GIS.SLTY_ADDRESS where ";
		foreach ($ad as $k => $v) {
			//echo "<br>($k) ($v)<br>";
			if (strcmp($k, "RESULT") != 0) {
				if ($bg == 0) {
					$bg = 1;
				} else {
					$query .= " AND ";
				}
				if (strlen($v) == 0) {
					$query .= " $k is null ";
				} else {
					$query .= " $k = :$k ";
					$param[ $k ] = $v;
				}
			}
		}
		reset($ad);
		//echo "<pre>";
		//echo "<br>query = ($query)<br>";
		//print_r($param);
		//echo "</pre>";
		$a = $this->db->queryArray($query, $param);
		$ad[ "RESULT" ] = count($a);
		if ($ad[ "RESULT" ] == 1) {
			$ad[ "ADDR_ID" ] = $a[ 0 ][ "ADDR_ID" ];
		}

		return ($a);
	}

	/*
 * Загрузить файл Спб
 */
	public function loadTehnogradNewly($file, $source_id = 1)
	{
		$bg = 0;
		$iszip = false;
		$notloaded = [];
		$i = 0;
		$badcnt = 0;
		$goodcnt = 0;
		$this->errmsg = [];

		//echo "<br>$kladr_city_id $source_id<br>";
		$this->source_id = $source_id; // установить источник данных

		// загрузка файла
		$this->types = $this->getallTypes();
		if (isset($file[ 'name' ]) && strlen($file[ 'name' ]) != 0) {
			if (mb_strstr(mb_strtolower($file[ 'name' ]), ".zip") !== false) {
				$nameo = $file[ 'tmp_name' ];
				$namen = "$nameo.unzip";
				//echo "<br>usr/bin/unzip -p $nameo > $namen<br>";
				if (system("/usr/bin/unzip -p $nameo > $namen", $retvar) !== false) {
					$namen = "$nameo.unzip";
					echo "<br>Файл " . $file[ 'name' ] . " разархивирован<br>";
				} else {
					echo "<br>Ошибка разархивирования загруженного файла $nameo с диагностикой $retvar<br>";

					return;
				}
				$iszip = true;
			} else {
				$namen = $file[ 'tmp_name' ];
			}
			//echo "Загружаем файл '". $file['name'][$i]."', который залит на сервер с именем '".$file['tmp_name'][$i]."'<br>";
			// открыть файл на чтение
			if (!($fp = fopen($namen, 'r'))) {
				throw new Exception("Ошибка открытия файла $namen");
			}


			if (!($line = fgets($fp))) {
				throw new Exception("Оn line $i: ошибка чтения файла $namen");
			}

			while (($line = fgets($fp)) !== false) {
				$i++;
				try { // разобрать строку на элементы
					$ln = iconv('cp1251', 'UTF-8', $line);
					$lnarr = $this->explodeLine($ln, ";", true);
					/*
                                             * номер строки
                                            * Наименование региона
                                            * Тип региона
                                            * Наименование автономии
                                            * Тип автономии
                                            * Наименование района
                                            * тип района
                                            * Наименование города
                                            * тип города
                                            * Наименование населенного пункта
                                            * тип населенного пункта
                                            * Наименование улицы
                                            * тип улицы
                                             * ИД
                     * ИД города
                                             * ИД населенного пункта
                                             * ИД улицы
                                             * ИД дома
                                             * Номер дома
                                             * Полная строка адреса
                                            */

					if (count($lnarr) != 19) {
						throw new Exception(
							"On line $i: ошибка формата файла " . $file[ 'name' ] . ",получено " . count(
								$lnarr
							) . " полей, ожидается 19"
						);
					}
					$res = $this->loadTehnoDBNew($lnarr, $this->types, $ln);
					if ($res != 0) {
						$badcnt++;
						//if ($bg == 0) {
						//    echo "<br>Не загружено строк файла<br>";
						//    $bg = 1;
						//}
						//echo "<br>on line $i: $ln<br>";
					} else {
						$goodcnt++;
					}
				} catch (Exception $e) {
					echo "<br>on line $i: " . $e->getMessage() . "<br>";
					if ($fp) {
						fclose($fp);
						unset($fp);
					}
					if ($iszip) {
						echo "<br>Удаление временного файла $namen<br>";
						system("/bin/rm $namen", $retvar);
						echo "<br>$retvar<br>";
					}

					return;
				}
			}
		}
		if ($fp) {
			fclose($fp);
			unset($fp);
		}
		if ($iszip) {
			echo "<br>Удаление временного файла $namen<br>";
			system("/bin/rm $namen", $retvar);
			echo "<br>$retvar<br>";
		}
		echo "<br>Загрузка завершена: в файле $i строк, обработано успешно $goodcnt, обработано с ошибкой $badcnt<br>";
		if (count($this->errmsg) > 0) {
			echo "<br>В том числе:<br>";

			foreach ($this->errmsg as $k => $v) {
				echo "<br>$k: $v<br>";
			}
		}

		return;
	}


	/*
    *
    */
	public function loadTehnogradNew($file)
	{
		$address = [];
		$bg = 0;
		$types = $this->getallTypes();
		$this->types = $types;
		$this->bad_townid = -1;

		$notloaded = [];

		for ($i = 0; $i < count($file[ 'name' ]); $i++) {

			if (isset($file[ 'name' ][ $i ]) && strlen($file[ 'name' ][ $i ]) != 0) {
				if (mb_strstr(mb_strtolower($file[ 'name' ][ $i ]), ".zip") !== false) {
					$nameo = $file[ 'tmp_name' ][ $i ];
					$namen = "$nameo.unzip";
					//echo "<br>usr/bin/unzip -p $nameo > $namen<br>";
					if (system("/usr/bin/unzip -p $nameo > $namen", $retvar) !== false) {
						$namen = "$nameo.unzip";
						echo "<br>Файл " . $file[ 'name' ][ $i ] . " разархивирован<br>";
					} else {
						echo "<br>Ошибка разархивирования загруженного файла $nameo с диагностикой $retvar<br>";
						break;
					}
				} else {
					$namen = $file[ 'tmp_name' ][ $i ];
				}
				//echo "Загружаем файл '". $file['name'][$i]."', который залит на сервер с именем '".$file['tmp_name'][$i]."'<br>";
				// открыть файл на чтение
				if (!($fp = fopen($namen, 'r'))) {
					echo "<br>ошибка открытия файла " . $namen . "<br>";
					break;
				}
				$i = 1;

				if (!($line = fgets($fp))) {
					echo "<br>on line $i: ошибка чтения файла " . $namen . "<br>";
					fclose($fp);
					break;
				}

				while (($line = fgets($fp)) !== false) {
					$i++;
					try { // разобрать строку на элементы
						$ln = iconv('cp1251', 'UTF-8', $line);
						$lnarr = $this->explodeLine($ln, ";", true);
						//echo "<pre>";
						//print_r($lnarr);
						//echo "</pre>";
						/*
                         * номер строки
                        * Наименование региона
                        * Тип региона
                        * Наименование автономии
                        * Тип автономии
                        * Наименование района
                        * тип района
                        * Наименование города
                        * тип города
                        * Наименование населенного пункта
                        * тип населенного пункта
                        * Наименование улицы
                        * тип улицы
                         * ИД
                         * ИД населенного пункта
                         * ИД улицы
                         * ИД дома
                         * Номер дома
                         * Полная строка адреса
                        */
						if (count($lnarr) != 20) {
							echo "<br>on line $i: ошибка формата файла " . $file[ 'name' ][ $i ] . ",получено " . count(
									$lnarr
								) . " полей, ожидается 20<br>";
							fclose($fp);
							break;
						}
						$res = $this->loadTehnoDBNew($lnarr, $types, $ln);
						if ($res != 0) {
							if ($bg == 0) {
								echo "<br>Не загружено строк файла<br>";
								$bg = 1;
							}
							echo "<br>$ln<br>";
							//$notloaded[]["LINE"] = $ln;
							//$result[ 'error' ][ ] = "on line $i: ошибка ".$res;
						}
					} catch (Exception $e) {
						echo "<br>on line $i: " . $e->getMessage() . "<br>";
						break;
					}
				}
			}
		}
		fclose($fp);
		if (strstr($namen, ".unzip") !== false) {
			echo "<br>Удаление временного файла $namen<br>";
			system("/bin/rm $namen", $retvar);
			echo "<br>$retvar<br>";
		}
		//echo "<pre>";
		//echo "<br>Полученная адресная база:<br>";
		//print_r($address);
		//echo "</pre>";
		return ($notloaded);
	}

	private function getTypeByName($abbr)
	{
		$query = "select * from FOO_GIS.ATD_OBJ_TYPE where lower(ABBR) = :1";
		$param = [ "1" => $abbr ];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/*
    *
    */
	private function getKLADRbyEl(&$el, $table = "", $parcode = "")
	{
		$param = [ "1" => $el[ "NORM" ], "2" => $el[ "ABBR" ] ];
		if (strlen($parcode) > 0) {
			if (strcmp($table, "KLADR_STREET") == 0) {
				$partxt = " AND FOO_GIS.GET_KLADR_PARENT(CODE,17) = :3";
			} else {
				$partxt = " AND FOO_GIS.GET_KLADR_PARENT(CODE) = :3";
			}
			$param[ "3" ] = $parcode;
		} else {
			$partxt = "";
		}

		$query = "select * from FOO_GIS.$table where lower(NAME) = :1 AND lower(SOCR) = :2 AND STATE = 0 $partxt";
		$a = $this->db->queryArray($query, $param);
		$cnt = count($a);

		if ($cnt == 1) {
			// однозначное совпадение
			$el[ "CODE" ] = $a[ 0 ][ "CODE" ];
		} else {
			if (count($a) == 0) {
				echo "<br>Не найден код элемента (" . $el[ "NAME" ] . ") норм (" . $el[ "NORM" ] . ") тип (" . $el[ "ABBR" ] . ") код родителя (" . $parcode . ")<br>";
			} else {
				echo "<br>Не найден однозначный код элемента (" . $el[ "NAME" ] . ") норм (" . $el[ "NORM" ] . ") тип (" . $el[ "ABBR" ] . ") код родителя (" . $parcode . ")<br>";
				echo "<pre>";
				print_r($a);
				echo "</pre>";
			}
		}

		return ($a);
	}

	/**
	 * Получить список регионов СитПлан
	 *
	 * @return array|int
	 */
	public function getGSregions($par_id = "", $level = 0, &$res, $withltc = false)
	{
		//echo "<br>-------------------------------------------------------<br>";
		//echo "<br>getGSregions: par_id=($par_id) $level=($level) res:<br>";
		//echo "<pre>";
		//print_r($res);
		//echo "</pre>";
		//echo "<br>-------------------------------------------------------<br>";
		if ($level > 1) {
			if (strlen($par_id) > 0) {
				// найти родителя родителя
				$query = "select PAR_ID from FOO_GIS.ATD_OBJECT where ID = :1";
				$param = [ "1" => $par_id ];
				$a = $this->db->queryArray($query, $param);
				$cpar_id = $a[ 0 ][ "PAR_ID" ];

			}
			$this->getGSregions($cpar_id, $level - 1, $res, $withltc);
			$res[ $level - 1 ][ "ao_id" ] = $par_id;
		}
		$param = [ "1" => $this->perm->UserID ];
		if (!$par_id) {
			$partxt = " ao.PAR_ID is null and ";
		} else {
			$partxt = " ao.PAR_ID = :2 and ";
			$param[ "2" ] = $par_id;
		}
		$query = "SELECT

          ao.STATE, ao.ID, ao.NAME || ' ' || aot.NAME as NAME FROM FOO_GIS.ATD_OBJECT ao,
          FOO_GIS.ATD_OBJ_TYPE aot
          where $partxt

          FOO_GIS.CHECKCITY(:1, ao.ID) = 1 and

          nvl(AO.ATDOBJ_TYPE,0) = AOT.CODE(+)
          ORDER BY ao.NAME";

		$a = $this->db->queryArray($query, $param);
		if (count($a) > 0) {
			$res[ $level ][ "list" ] = $a;
		} else {
			if ($withltc) { // если требуется выбрать НП для пользователя ЛТЦ
				$a = $this->getAoListForLtc($par_id, $withltc);
				if (count($a) > 0) {
					$res[ $level ][ "list" ] = $a;
				}
			}
		}

		return;
	}

	/**
	 * получить список НП для пользователя ЛТЦ
	 *
	 * @param string $par_id
	 * @param bool   $withltc
	 *
	 * @return array|int
	 */
	public function getAoListForLtc($par_id = "", $withltc = false)
	{
		$a = [];
		if ($withltc) {
			$params = [
				"au_id" => $this->perm->UserID,
			];
			if (!$par_id) {
				$partxt = " ao.PAR_ID is null ";
			} else {
				$partxt = " ao.PAR_ID = :par_id ";
				$params[ "par_id" ] = $par_id;
			}
			$query = "
            select 
                distinct ao.ID, 
                ao.PAR_ID, 
                ao.NAME || ' ' || aot.NAME as NAME
            from 
              FOO_GIS.ATD_OBJECT ao,
              FOO_GIS.ATD_OBJ_TYPE aot
            where 
              $partxt
              and aot.CODE (+)= ao.ATDOBJ_TYPE
            connect by prior 
              ao.PAR_ID = ao.ID
            start with ao.ID in
              (select
                ada.AREA_ID
              from
                (select 
                    ad.AU_DEPID
                from
                    FOO_AUTH.AUTH_USER_DEPARTMENTS aud,
                    FOO_AUTH.AUTH_DEPARTMENTS ad
                where 
                  aud.AU_ID = :au_id
                  and ad.AU_DEPID = aud.AU_DEPID
                  and ad.AU_DEPTYPE = 'ЛТЦ'
                ) user_deps,
                FOO_AUTH.AUTH_DEPARTMENT_AREAS ada
                where 
                  ada.AU_DEPID = user_deps.AU_DEPID
              )
            ";

			$a = $this->db->queryArray($query, $params);
			if (count($a) > 0) {
				return $a;
			}
		}

		return [];
	}

	/*
   *
   */
	public function getGSTypes($level = 0)
	{
		$level = $level * 100;
		$query = "select NAME || ' (' || CODE || ')' as NAME, CODE
            from FOO_GIS.ATD_OBJ_TYPE
            where length(trim(ABBR))!=0 and
            CODE < 500 and CODE > $level and
            round(code/100) != CODE/100
            order by CODE, NAME";

		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/*
     * *
     */
	public function addGSObject($par_id, $ao_name, $ao_type)
	{
		if (mb_strlen($ao_name) == 0) {
			$res[ "error" ] = "Ошибка: пустое название населенного пункта";

			return ($res);
		}
		if ($ao_type == 0) {
			$res[ "error" ] = "Ошибка: не выбран тип населенного пункта";

			return ($res);
		}

		$query = "insert into FOO_GIS.ATD_OBJECT (
                  ID,
                  PAR_ID,
                  ATDOBJ_TYPE,
                  NAME,
                  SNAME,
                  FULLNAME,
                  STATE)
                  values (
                  FOO_GIS.ATD_OBJECT_SEQ.nextval,
                  :1,
                  :2,
                  :3,
                  :3,
                  :3,
                   2)";
		$param = [
			"1" => $par_id,
			"2" => $ao_type,
			"3" => $ao_name,
		];
		$this->db->query($query, $param);
		$query = "insert into FOO_AUTH.ATD_OBJECT (
                  ID,
                  PAR_ID,
                  ATDOBJ_TYPE,
                  NAME,
                  SNAME,
                  FULLNAME,
                  STATE)
                  values (
                  FOO_GIS.ATD_OBJECT_SEQ.nextval,
                  :1,
                  :2,
                  :3,
                  :3,
                  :3,
                   2)";
		//$this->db->commit();
		$res[ "ok" ] = "Населенный пункт успешно добавлен";

		return ($res);
	}

	/*
     * *
     */
	public function getRegionByAO($ao_id)
	{
		$query = "select ID from FOO_GIS.ATD_OBJECT where PAR_ID is null connect by prior PAR_ID = ID start with ID = :1";
		$param = [ "1" => $ao_id ];
		$a = $this->db->queryArray($query, $param);

		return ($a[ 0 ][ "ID" ]);
	}

	/*
     *
     */
	public function getallTypes()
	{
		$query = "select *
            from FOO_GIS.ATD_OBJ_TYPE
            where length(trim(ABBR))!=0
            order by length(trim(ABBR)) desc";
		$param = [];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/*
     *
     */
	private function normTehnoAO($name)
	{
		$a = [];
		$s = mb_split("\|", $name);
		// заполнение массива
		// оригинальное название
		$a[ "NAME" ] = trim($name);
		// пормализованное название
		$a[ "NORM" ] = trim($s[ 2 ]);
		// тип населенного пункта
		$a[ "ABBR" ] = trim($s[ 1 ]);

		return ($a);
	}

	/*
         *
         */
	private function normTehnoStr($name)
	{
		// пары сивпадающих латинских и кириллических букв (после перевода в нижний регистр)
		$latin_types = [
			"e" => "е",
			"x" => "х",
			"a" => "а",
			"c" => "с",
			"o" => "о",
			"p" => "р",
			"h" => "н",
			"k" => "к",
			"b" => "в",
			"m" => "м",
			"t" => "т",
		];
		$types = [
			" ж/д ст\. " => "|ж/д_ст|",
			"обл\. " => "|обл|",
			" р-н\. " => "|р-н|",
			" район\. " => "|мкр|",
			" д\. " => "|д|",
			" ул\. " => "|ул|",
			" с\. " => "|с|",
			" пер\. " => "|пер|",
			" п\. " => "|п|",
			" корп\. " => "|корп|",
			" х\. " => "|х|",
			" тер\. " => "|тер|",
			" дп\. " => "|дп|",
			" км\. " => "|км|",
			"н\\у\." => "||",
			" н.у\. " => "||",
			" н\\у\. " => "||",
			" н\у\. " => "||",
			" н\\у\. " => "|н\у|",
			" н\у\. " => "|н\у|",
			" п/о\. " => "|п/о|",
			" ст\. " => "|ст|",
			" сл\. " => "|сл|",
			" г\. " => "|г|",
			" \?\. н\\у\|" => "|н\у|",
			" \?\. н\у\|" => "|н\у|",
			" пгт\. " => "|пгт|",
			" проезд\. " => "|проезд|",
			//            " \?\. ж/д " => "|ж/д|",
			" нп\. " => "|нп|",
			" жт\. " => "|жт|",
			"  рзд\. " => "| рзд|",
			"  городок\. " => "| городок|",
			" корп\. " => "|корп|",
			" пл\. " => "|пл|",
			" пр-кт\. " => "|пр-кт|",
			" ж/д будка\. " => "|ж/д_будка|",
			" б-р\. " => "|б-р|",
			" казарма\. " => "|казарма|",
			" мкр\. " => "|мкр|",
			" ш\. " => "|ш|",
			" \?\. " => "||",
		];

		// в нижний регистр
		$str = mb_strtolower(trim($name));
		// замена похожих латинских букв на кириллические
		foreach ($latin_types as $k => $v) {
			$str = mb_ereg_replace($k, $v, $str);
		}
		// убрать пробелы внутри текста
		$str = preg_replace('/\s{2,}/', ' ', $str);
		// замена сокращений
		foreach ($types as $k => $v) {
			$str = mb_ereg_replace($k, $v, $str);
		}
		// замена ё на е
		$str = mb_ereg_replace("ё", "е", $str);
		// замена спецсимволов
		$str = mb_ereg_replace("\"", "", $str);

		return ($str);
	}

	/*
     *
     */
	private function parseTehnoStr(&$obj, &$types)
	{
		$names = [];
		$el = [];
		$i = 0;

		$town_id = $obj[ 2 ];
		$street_id = $obj[ 3 ];

		//echo "<br>$obj[8]<br>";
		// Проверить улицу по идентификатору в БД
		$a = $this->getSltyStreetByID($street_id);
		if (count($a) == 1) {
			//echo "<br>Улица с идентификатором $street_id уже загружена в БД<br>";
			return;
		}
		// город
		$city = $this->normTehnoStr($obj[ 7 ]);
		// улица
		$street = $this->normTehnoStr($obj[ 6 ]);
		// нормализованная строка
		$str = $this->normTehnoStr($obj[ 8 ]);

		//echo "<br>str=($str)<br>";

		if (mb_strlen(trim($street)) == 0) {
			$knownpart = "\|[^|.]*?\|" . preg_quote($city);
		} else {
			$knownpart = "\|[^|.]*?\|" . preg_quote($city) . "\|[^|.]*?\|" . preg_quote($street);
		}
		//echo "<br>knownpart=($knownpart)<br>";

		mb_ereg_search_init($str);
		if (($aa = mb_ereg_search_regs($knownpart)) === false) {
			echo "<br>Ошибка парсинга строки ($str) по части ($knownpart)<br>";

			return;
		}
		//echo "<pre>";
		//echo "<br>---<br>";
		//print_r($aa);
		//echo "<br>---<br>";
		//echo "</pre>";

		//$pos = $aa[0];
		//$len = $aa[1];
		//$knowntext = mb_substr($str, $pos, $len);
		//echo "<br>knowntext=($knowntext)<br>";
		$knowntext = $aa[ 0 ];
		//echo "<br>knowntext=($knowntext)<br>";

		$lost = mb_substr($str, mb_strpos($str, $knowntext) + mb_strlen($knowntext));
		//echo "<br>lost=($lost)<br>";
		$streetpart = trim(mb_substr($knowntext, mb_strpos($knowntext, $city) + mb_strlen($city)));
		//echo "<br>streetpart=($streetpart)<br>";
		$streetnm = $this->normTehnoAO($streetpart);
		//echo "<pre>";

		//print_r($streetnm);

		//echo "</pre>";
		if ($this->mb_strcmp($streetnm[ "NORM" ], ".") == 0) {
			//echo "<br>Улица (".$streetnm["NORM"].") игнорируется в обработке<br>";
			return;
		}
		if ($this->mb_strcmp($streetnm[ "NORM" ], "н\у") == 0) {
			//echo "<br>Улица (".$streetnm["NORM"].") игнорируется в обработке<br>";
			return;
		}
		// Проверить город по идентификатору в БД
		$a = $this->getSltyObjectByID($town_id);
		if (count($a) == 1) {
			//echo "<br>Город с идентификатором $town_id уже загружен в БД<br>";
			$par_id = $a[ 0 ][ "ID" ];
		} else {
			if (count($a) == 0) {

				$citypart = trim(mb_substr($knowntext, 0, mb_strlen($knowntext) - mb_strlen($streetpart)));
				//echo "<br>citypart=($citypart)<br>";
				$last = mb_substr($str, 0, mb_strlen($str) - mb_strlen($knowntext) - mb_strlen($lost));
				//echo "<br>last=($last)<br>";

				$str = trim($last);
				//echo "<br>str=($str)<br>";
				//$city = $this->normTehnoAO($obj[7], $types)["NORM"];

				mb_ereg_search_init($str);
				while (($arr = mb_ereg_search_regs("(\|[^|]*\|[^|]*(?=\|))|(\|[^|]*\|[^|]*(?=$))")) !== false) {
					//echo "<pre>";
					//print_r($arr);
					//echo "</pre>";
					if (isset($arr[ 0 ])) {
						$el[ $i ] = $arr[ 0 ];
						$i++;
					} else {
						echo "<br>Ошибка парсинга строки ($str)<br>";
					}
				}

				//echo "<pre>";
				//print_r($el);
				//echo "</pre>";
				for ($i = 0; $i < count($el); $i++) {
					$nm = $this->normTehnoAO($el[ $i ]);
					if ($this->mb_strcmp($nm[ "ABBR" ], "р-н") == 0) {
						$names[ 0 ][ "MUN" ] = true;
					}
					$nm[ "LEVEL" ] = $i + 1;
					$nm[ "TABLE" ] = "SLTY_OBJECT";
					$names[ $i ] = $nm;
				}
				// населенный пункт
				$nm = $this->normTehnoAO($citypart);
				$nm[ "LEVEL" ] = $i + 1;
				$nm[ "TABLE" ] = "SLTY_OBJECT";
				$nm[ "SLTY_ID" ] = $town_id;
				$names[ $i ] = $nm;
				$i++;
			} else {
				echo "<br>Ошибка: в БД найдено " . count($a) . " населенных пунктов с идентификатором $town_id<br>";

				return;
			}
		}
		// улица

		$nm = $streetnm;
		$nm[ "LEVEL" ] = 5;
		$nm[ "TABLE" ] = "SLTY_STREET";
		$nm[ "SLTY_ID" ] = $street_id;
		if (isset($par_id)) {
			$nm[ "PAR_ID" ] = $par_id;
		}
		$names[ $i ] = $nm;

		//echo "<pre>";
		//print_r($names);
		//echo "</pre>";

		$this->addSlty($names, $obj[ 8 ]);

		return;
	}

	/*
          *
          */
	private function normTehnoStrNew($name)
	{
		// пары совпадающих латинских и кириллических букв (после перевода в нижний регистр)
		$latin_types = [
			"e" => "е",
			"x" => "х",
			"a" => "а",
			"c" => "с",
			"o" => "о",
			"p" => "р",
			"h" => "н",
			"k" => "к",
			"b" => "в",
			"m" => "м",
			"t" => "т",
		];
		// в нижний регистр
		$str = mb_strtolower(trim($name));
		// замена похожих латинских букв на кириллические
		foreach ($latin_types as $k => $v) {
			$str = mb_ereg_replace($k, $v, $str);
		}
		// убрать пробелы внутри текста
		$str = preg_replace('/\s{2,}/', ' ', $str);
		// замена ё на е
		$str = mb_ereg_replace("ё", "е", $str);
		// замена спецсимволов
		$str = mb_ereg_replace("\"", "", $str);

		return ($str);
	}

	/*
          *
          */
	private function normTehnoAOType($level, $type, &$types)
	{
		$a = [];
		$j = 0;

		for ($i = 0; $i < count($types); $i++) {
			if ($this->mb_strcmp($types[ $i ][ "NAME" ], $type) == 0) {
				$a[ $j ] = $types[ $i ];
				$j++;
			}
		}

		if (count($a) > 0) {
			for ($i = 0; $i < count($a); $i++) {
				if (floor($a[ $i ][ "CODE" ] / 100) == $level) {
					return ($a[ $i ]);
				}
			}

			return ($a[ 0 ]);
		}

		return;
	}

	/*
      *
      */
	private function normTehnoAONew($name, $type, $level, &$types)
	{
		$a = [];
		//echo "<br>name=($name) type-($type) level=($level)<br>";
		// заполнение массива
		// оригинальное название
		//$a["NAME"] = $name;
		// пормализованное название
		$a[ "NORM" ] = $this->normTehnoStrNew($name);
		$a[ "NAME" ] = $a[ "NORM" ];
		// тип населенного пункта
		$a[ "TYPE" ] = $this->normTehnoAOType($level, $type, $types);
		$a[ "ABBR" ] = $a[ "TYPE" ][ "ABBR" ];
		$a[ "LEVEL" ] = $level;
		if ($level == 5) // улицы
		{
			$a[ "TABLE" ] = "SLTY_STREET";
		} else {
			if ($level < 5) // объекты
			{
				$a[ "TABLE" ] = "SLTY_OBJECT";
			} else { // дома ???
				unset($a[ "NORM" ]);
			}
		}

		return ($a);
	}

	/*
   *
   */
	private function parseTehnoStrNew(&$obj, &$types)
	{
		$names = [];

		$i = 0;

		// населенный пункт
		// Проверить н.п. по идентификатору
		$town_id = $obj[ 14 ];
		$full_str = $obj[ 18 ];
		if (!is_numeric($town_id)) {
			echo "<br>Населенный пункт ($full_str) с нецифровым TOWN_ID=($town_id), игнорируем<br>";

			return (0);
		}
		if ($this->bad_townid == $town_id) {
			// полный адрес
			//$full_str = $obj[19];
			//echo "<br>---<br>";
			//echo "<br>$full_str<br>";
			//echo "<br>Населенный пункт с TOWN_ID=$town_id не удалось привязать ранее, игнорируем<br>";
			return (-1);
		}
		// улица
		$street_id = $obj[ 15 ];
		if (is_numeric($street_id)) {
			// Проверить улицу по идентификатору в БД
			$a = $this->getSltyStreetByID($street_id);
			if (count($a) == 1) {
				// улица с таким ИД уже загружена в БД, пропускаем строку
				//echo "<br>Улица с идентификатором $street_id уже загружена в БД<br>";
				return (0);
			}
			$street_name = $obj[ 11 ];
			$street_type = $obj[ 12 ];
			$a = $this->normTehnoAONew($street_name, $street_type, 5, $types);
			// игнорировать пустые улицы
			if ($this->mb_strcmp($a[ "NORM" ], "") == 0) {
				//echo "<br>Улица (".$streetnm["NORM"].") игнорируется в обработке<br>";
				$this->errmsg[ "Пустая улица" ]++;

				return (-1);
			}
			if ($this->mb_strcmp($a[ "NORM" ], ".") == 0) {
				//echo "<br>Улица (".$streetnm["NORM"].") игнорируется в обработке<br>";
				$this->errmsg[ "Улица '.'" ]++;

				return (-1);
			}
			if ($this->mb_strcmp($a[ "NORM" ], "н\у") == 0) {
				//echo "<br>Улица (".$streetnm["NORM"].") игнорируется в обработке<br>";
				$this->errmsg[ "Улица 'н\у'" ]++;

				return (-1);
			}
			// улица
			$a[ "SLTY_ID" ] = $street_id;
			// сохранить улицу
			$st = $a;
		}
		// населенный пункт
		// Проверить н.п. по идентификатору в БД
		//$town_id = $obj[ 15 ];
		$town_name = $obj[ 9 ];
		$town_type = $obj[ 10 ];
		$a = $this->getSltyObjectByID($town_id);
		if (count($a) == 1) {
			//echo "<br>Город с идентификатором $town_id уже загружен в БД<br>";
			if (!isset($st)) {
				// улицы нет, нечего делать
				//echo "<br>Улицы нет, завершаем обработку<br>";
				return (0);
			}
			$st[ "PAR_ID" ] = $a[ 0 ][ "ID" ];
		} else {
			if (count($a) == 0) {

				// номер строки
				$num = $obj[ 0 ];
				// регион
				$region_name = $obj[ 1 ];
				$region_type = $obj[ 2 ];
				$a = $this->normTehnoAONew($region_name, $region_type, 1, $types);
				// игнорировать пустые элементы
				if ($this->mb_strcmp($a[ "NORM" ], "") != 0) {
					$names[ $i ] = $a;
					$i++;
				}
				// автономия
				$ao_name = $obj[ 3 ];
				$ao_type = $obj[ 4 ];
				$a = $this->normTehnoAONew($ao_name, $ao_type, 1, $types);
				// игнорировать пустые элементы
				if ($this->mb_strcmp($a[ "NORM" ], "") != 0) {
					$names[ $i ] = $a;
					$i++;
				}
				// район
				$district_name = $obj[ 5 ];
				$district_type = $obj[ 6 ];
				$a = $this->normTehnoAONew($district_name, $district_type, 2, $types);
				// игнорировать пустые элементы
				if ($this->mb_strcmp($a[ "NORM" ], "") != 0) {
					$names[ $i ] = $a;
					$i++;
				}
				// город
				$city_name = $obj[ 7 ];
				$city_type = $obj[ 8 ];
				//$city_id = $obj[ 14 ];
				$k = 0;
				if (($this->mb_strcmp($city_type, $town_type) != 0) || ($this->mb_strcmp(
							$city_name,
							$town_name
						) != 0)) {
					// если город не совпал с населенным пунктом
					$a = $this->normTehnoAONew($city_name, $city_type, 3, $types);
					// игнорировать пустые элементы
					if ($this->mb_strcmp($a[ "NORM" ], "") != 0) {
						//$a[ "SLTY_ID" ] = $city_id;
						$names[ $i ] = $a;
						$tw = $i;
						$k = 1;
						$i++;
					}
				}
				// населенный пункт
				$a = $this->normTehnoAONew($town_name, $town_type, 3 + $k, $types);

				// игнорировать пустые элементы
				if ($this->mb_strcmp($a[ "NORM" ], "") != 0) {
					$a[ "SLTY_ID" ] = $town_id;
					$names[ $i ] = $a;
					$i++;
				}
				//}

				// полный адрес
				$full_str = $obj[ 18 ];

			} else {
				echo "<br>Ошибка: в БД найдено " . count($a) . " населенных пунктов с идентификатором $town_id<br>";
				$this->errmsg[ "Дубль в БД" ]++;

				return (-1);
			}
		}
		if (isset($st)) {
			// улица
			$names[ $i ] = $st;
		}
		//echo "<pre>";
		//echo "<br>$full_str<br>";
		//print_r($names);
		//echo "</pre>";

		$ret = $this->addSlty($names, $full_str);

		return ($ret);
	}

	/*
    *
    */
	private function getSltyObjectByID($id)
	{
		$query = "select * from FOO_GIS.SLTY_OBJECT where SLTY_ID = :1";
		$param = [
			"1" => $id,
		];
		//echo "<br>getSltyObjectByID:$id<br>";
		//echo "<pre>";
		//echo "<br>query=($query)<br>";
		//print_r($param);
		//echo "</pre>";
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";

		//print_r($a);
		//echo "</pre>";
		return ($a);
	}

	/*
    *
    */
	private function getSltyStreetByID($id)
	{
		$query = "select * from FOO_GIS.SLTY_STREET where SLTY_ID = :1";
		$param = [
			"1" => $id,
		];
		//echo "<br>getSltyStreetByID:$id<br>";
		//echo "<pre>";
		//echo "<br>query=($query)<br>";
		//print_r($param);
		//echo "</pre>";
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";

		//print_r($a);
		//echo "</pre>";
		return ($a);
	}

	/*
    *
    */
	private function getSltyByName(&$nm)
	{
		$name = $nm[ "NORM" ];
		$type = $nm[ "ABBR" ];
		$table = $nm[ "TABLE" ];
		if (strcmp($table, "SLTY_OBJECT") == 0) {
			$query = "select * from FOO_GIS.$table where NAME = :1 and nvl(ABBR,0) = nvl(:2,0) and SOURCE_ID = :source_id";
			$param = [
				"1" => $name,
				"2" => $type,
				"source_id" => $this->source_id,
			];
			if (isset($nm[ "PAR_ID" ])) {
				$param[ "3" ] = $nm[ "PAR_ID" ];
				$query = $query . " and PAR_ID = :3";
			} else {
				$query = $query . " and PAR_ID is null";
			}

		} else {
			$query = "select * from FOO_GIS.$table where NAME = :1 and nvl(ABBR,0) = nvl(:2,0) and SOURCE_ID = :source_id";
			$param = [
				"1" => $name,
				"2" => $type,
				"source_id" => $this->source_id,
			];
			if (isset($nm[ "PAR_ID" ])) {
				$param[ "3" ] = $nm[ "PAR_ID" ];
				$query = $query . " and OBJ_ID = :3";
			} else {
				$query = $query . " and OBJ_ID is null";
			}

		}
		if (isset($nm[ "SLTY_ID" ])) {
			$query = $query . " and SLTY_ID = :4";
			$param[ "4" ] = $nm[ "SLTY_ID" ];
		}
		//echo "<pre>";
		//echo "<br>query=($query)<br>";
		//print_r($param);
		//echo "</pre>";
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//echo "<br>getSltyByName:<br>";
		//print_r($a);
		//echo "</pre>";
		if (count($a) == 1) {
			$nm[ "KL_CODE" ] = $a[ 0 ][ "CODE" ];
			$nm[ "ID" ] = $a[ 0 ][ "ID" ];
			$nm[ "RESULT" ] = count($a);
		} else {
			$nm[ "RESULT" ] = count($a);
		}

		return ($a);
	}

	/*
   *
   */
	private function insSltyByName($nm)
	{
		$name = $nm[ "NORM" ];
		$type = $nm[ "ABBR" ];
		$table = $nm[ "TABLE" ];

		if (isset($nm[ "SLTY_ID" ])) {
			$slty_id = $nm[ "SLTY_ID" ];
		} else {
			$slty_id = null;
		}
		if (isset($nm[ "PAR_ID" ])) {
			$par_id = $nm[ "PAR_ID" ];
		} else {
			$par_id = null;
		}
		if (strcmp($table, "SLTY_OBJECT") == 0) {
			$query = "insert into
                FOO_GIS.$table (ID, PAR_ID, NAME, ABBR, SLTY_ID, CODE, SOURCE_ID, ATDOBJ_TYPE)
                values (FOO_GIS." . $table . "_SEQ.nextval,:3, :1, :2,:4, :6, :7,
                    nvl(
                        (select distinct CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :2 and FLOOR(CODE/100) = :5),
                        nvl(
                            (select distinct CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :2 and FLOOR(CODE/100) = (:5+1)),
                            (select distinct CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :2 and FLOOR(CODE/100) = (:5-1))
                        )
                    )
                )
            ";
			$param = [
				"1" => $name,
				"2" => $type,
				"3" => $par_id,
				"4" => $slty_id,
				"5" => $nm[ "LEVEL" ],
				"6" => $nm[ "KL_CODE" ],
				"7" => $this->source_id,
			];
		} else {

			$query = "insert into
                FOO_GIS.$table (ID, OBJ_ID, NAME, ABBR, SLTY_ID,SOURCE_ID, ATDOBJ_TYPE)
                values (FOO_GIS." . $table . "_SEQ.nextval,:3, :1, :2,:4, :6,
                    nvl(
                        (select distinct CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :2 and FLOOR(CODE/100) = :5),
                        nvl(
                            (select distinct CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :2 and FLOOR(CODE/100) = (:5+1)),
                            (select distinct CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :2 and FLOOR(CODE/100) = (:5-1))
                        )
                    )
                )
            ";
			$param = [
				"1" => $name,
				"2" => $type,
				"3" => $par_id,
				"4" => $slty_id,
				"5" => $nm[ "LEVEL" ],
				"6" => $this->source_id,
			];
		}
		//if ($this->mb_strcmp($name, "воронеж") == 0) {
		//echo "<br>------------------------------<br>";
		//echo "<pre>";
		//echo "<br>insSltyByName:<br>";
		//print_r($nm);
		//echo "<br>query=($query)<br>";
		//print_r($param);
		//echo "</pre>";
		//echo "<br>------------------------------<br>";
		//}
		$this->db->query($query, $param);
		$this->db->commit();
	}

	/*
     *
     */
	private function addSlty($names, $pstr)
	{
		$par_id = "";

		for ($i = 0; $i < count($names); $i++) {
			if (mb_strlen($names[ $i ][ "NORM" ]) == 0) {
				continue;
			}
			if ($i > 0) {
				// установить PAR_ID из родителя
				$names[ $i ][ "PAR_ID" ] = $names[ $i - 1 ][ "ID" ];
				// установить код КЛАДР из родителя
				$names[ $i ][ "KL_PAR_CODE" ] = $names[ $i - 1 ][ "KL_CODE" ];
			}
			// проверить элемент в БД
			$a = $this->getSltyByName($names[ $i ]);
			if ($names[ $i ][ "RESULT" ] == 1) {
				// элемент уже в СУБД
				continue;
			} else {
				if ($names[ $i ][ "RESULT" ] > 1) {
					echo "<br>------<br>$pstr<br>";
					echo "<br>Ошибка: в БД найдено " . $names[ $i ][ "RESULT" ] . " элементов<br>";
					echo "<pre>";
					print_r($names[ $i ]);
					print_r($a);
					echo "</pre>";
					$this->errmsg[ "Дубль в БД" ]++;

					return (-1);
				} else {
					//echo "<br>Элемента $i еще нет в БД<br>";
					// Элемента еще нет в БД
					if (strcmp($names[ $i ][ "TABLE" ], "SLTY_OBJECT") == 0) {
						// только для населенных пунктов
						$e = [];
						$res = "";
						// поиск в КЛАДР по имени, типу и коду родителя
						$this->searchKLADR_AO($names[ $i ]);

						if ($names[ $i ][ "RESULT" ] != 1) {
							// не нашли - поиск только по имери и родителю, без типа
							$res .= $names[ $i ][ "ERROR" ];
							unset($names[ $i ][ "RESULT" ]);
							unset($names[ $i ][ "ERROR" ]);
							$this->searchKLADR_AO($names[ $i ], $e, false);
						}
						if ($names[ $i ][ "RESULT" ] != 1 && ($i == 1) && (!isset($names[ 0 ][ "MUN" ]))) {
							// в дереве нет областного района, пытаемся найти его и восстановить
							$res .= $names[ $i ][ "ERROR" ];
							unset($names[ $i ][ "RESULT" ]);
							unset($names[ $i ][ "ERROR" ]);
							$a = $this->searchKLADR_AO($names[ $i ], $names[ 0 ]);
						}
						if ($names[ $i ][ "RESULT" ] != 1) {
							if ($this->bad_townid != $names[ $i ][ "SLTY_ID" ]) {
								$res .= $names[ $i ][ "ERROR" ];
								echo "<br>------<br>$pstr<br>";
								echo "<br>Ошибка: $res<br>";
							}
							$this->bad_townid = $names[ $i ][ "SLTY_ID" ];
							$this->errmsg[ "Не связано с КЛАДР" ]++;

							return (-1);
						}
					}
					$this->insSltyByName($names[ $i ]);
					$a = $this->getSltyByName($names[ $i ]);
					if ($names[ $i ][ "RESULT" ] == 1) {
						continue;
					} else {
						echo "<br>------<br>$pstr<br>";
						echo "<pre>";
						echo "<br>Ошибка: в БД найдено " . $names[ $i ][ "RESULT" ] . " элементов<br>";
						print_r($names[ $i ]);
						print_r($a);
						echo "</pre>";
						if ($names[ $i ][ "RESULT" ] > 1) {
							$this->errmsg[ "Дубль в БД" ]++;
						} else {
							$this->errmsg[ "Нет в БД" ]++;
						}

						return (-2);
					}
				}
			}
		}

		return (0);
	}

	/*
     *
     */
	private function loadTehnoDB(&$arr, &$types, $ln)
	{

		// распарсить полную строку адреса
		$names = $this->parseTehnoStr($arr, $types);

		return (0);
	}

	/*
     *
     */
	private function loadTehnoDBNew(&$arr, &$types, $ln)
	{

		// распарсить полную строку адреса
		$ret = $this->parseTehnoStrNew($arr, $types);

		return ($ret);
	}

	/*
     *
     */
	protected function explodeLine($line, $delim = "|", $rem = false)
	{
		//$ln = iconv('cp1251', 'UTF-8', $line);
		$array = explode($delim, $line);
		foreach ($array as &$element) {
			$element = trim($element);
			if ($rem === true) {
				$element = mb_ereg_replace("^\"", "", $element);
				$element = mb_ereg_replace("\"$", "", $element);
			}
			$element = trim($element);
		}

		return $array;
	}

	/*
   * поиск населенного пункта в КЛАДР по ID родителя, названию и типу(файлы Техноград)
   */
	protected function searchKLADR_AO(&$arr, $parent = false, $istype = true, $isequal = true)
	{
		//echo "<pre>";
		//echo "<br>searchKLADR_AO: isequal=($isequal) istype=($istype)<br>";
		//print_r($arr);
		//print_r($parent);
		//echo "</pre>";
		if ($isequal) {
			$txt = " = ";
		} else {
			$txt = " like ";
		}
		if (isset($parent[ "NORM" ])) {
			// ищем элемент между $arr и $chld
			$query = "select ao2.* from
                        FOO_GIS.KLADR_MAIN ao1, FOO_GIS.KLADR_MAIN ao2
                        where
                        FOO_GIS.GET_KLADR_PARENT(ao1.CODE) = :1 and
                        FOO_GIS.GET_KLADR_PARENT(ao2.CODE) = ao1.CODE and
                        lower(ao2.NAME) = :2 and
                        lower(ao2.SOCR) = :3 and
                        ao1.STATE = 0 and
                        ao2.STATE = 0
                ";

			$param = [
				"1" => $parent[ "KL_CODE" ],
				"2" => $arr[ "NORM" ],
				"3" => $arr[ "ABBR" ],
			];
		} else {
			if ($istype) {
				if (!isset($arr[ "KL_PAR_CODE" ])) {
					$query = "select * from
                          FOO_GIS.KLADR_MAIN ao
                          where
                          lower(ao.NAME) $txt :2 and
                          lower(ao.SOCR) = :3 and
                          to_number(substr(ao.CODE,3,11)) = 0 and
                          ao.STATE = 0";

					$param = [
						"2" => $arr[ "NORM" ],
						"3" => $arr[ "ABBR" ],
					];
				} else {
					$query = "select * from
                          FOO_GIS.KLADR_MAIN ao
                         where
                          lower(ao.NAME) $txt :2 and
                          lower(ao.SOCR) = :3 and
                          FOO_GIS.GET_KLADR_PARENT(ao.CODE) = :1 and
                          ao.STATE = 0";

					$param = [
						"1" => $arr[ "KL_PAR_CODE" ],
						"2" => $arr[ "NORM" ],
						"3" => $arr[ "ABBR" ],
					];
				}
			} else {
				if (!isset($arr[ "KL_PAR_CODE" ])) {
					$query = "select * from
                          FOO_GIS.KLADR_MAIN ao
                          where
                          lower(ao.NAME) $txt :2
                          and to_number(substr(ao.CODE,3,11)) = 0
                          and ao.STATE = 0";

					$param = [
						"2" => $arr[ "NORM" ],
					];
				} else {
					$query = "select * from
                          FOO_GIS.KLADR_MAIN ao
                         where
                          lower(ao.NAME) $txt :2 and
                          FOO_GIS.GET_KLADR_PARENT(ao.CODE) = :1 and
                          ao.STATE = 0";

					$param = [
						"1" => $arr[ "KL_PAR_CODE" ],
						"2" => $arr[ "NORM" ],
					];
				}
			}
		}
		//echo "<pre>";
		//echo "<br>query=($query)<br>";
		//print_r($param);
		//echo "<pre>";
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		if (count($a) == 1) {
			$arr[ "KL_ID" ] = $a[ 0 ][ "ID" ];
			$arr[ "KL_CODE" ] = $a[ 0 ][ "CODE" ];
			$arr[ "RESULT" ] = count($a);
		} else {
			$arr[ "RESULT" ] = count($a);

			if ($istype) {
				$arr[ "ERROR" ] = "Не найден населенный пункт (" . $arr[ "NAME" ] . "),нормализован к (" . $arr[ "NORM" ] . "), тип (" . $arr[ "ABBR" ] . ") , родитель " . $arr[ "PAR_ID" ] . ", совпадений " . count(
						$a
					) . "<br>";
			} else {
				$arr[ "ERROR" ] = "Не найден населенный пункт (" . $arr[ "NAME" ] . "),нормализован к (" . $arr[ "NORM" ] . "), поиск без типа, родитель " . $arr[ "PAR_ID" ] . ", совпадений " . count(
						$a
					) . "<br>";
			}

		}

		//echo "<pre>";
		//echo "<br>arr=<br>";
		//print_r($arr);
		//echo "</pre>";
		return ($a);
	}

	/*
     *
     */
	public function getTableColumns($table)
	{
		$query = "select * from $table";
		$param = [];
		$a = $this->db->queryDesc($query);

		return ($a);
	}

	/*
     * нормализация названия населенного пункта (файлы Гипросвязь)
     */
	protected function normSP_AO($name, $svh = true)
	{
		$a = [];
		$abbr = "";
		// справочник типов
		$abbr_types = [
			//  "г. о. г." => "г",
			"муниципальный район" => "р-н",
			"сельское поселение" => "с/п",
			"сельсовет" => "с/с",
			"город-курорт" => "г",
			"станица" => "ст-ца",
			"город " => "г",
			"поселок" => "п",
			"хутор" => "х",
			"деревня" => "д",
			"станция" => "ст",
			"слобода" => "сл",
			"область" => "обл",
			"автономный округ" => "ао",
			"улус" => "у",
			"ст-ца." => "ст-ца",
			"пгт" => "пгт",
			"село" => "с",
			"п." => "п",
			"д." => "д",
			"х." => "х",
			"г." => "г",
			"с." => "с",
			"сл." => "сл",
			"ст." => "ст",
			"р.п." => "рп",
			"п. г.т." => "пгт",
			"м.р." => "р-н",
			"м. р." => "р-н",
			"м.р " => "р-н",
			"с.п." => "с/п",
			"с.с." => "с/с",
			"п.с." => "п/с",
			"г.п." => "г/п",
			"с.о." => "с/о",
			"с.мо." => "с/мо",
			"с.т." => "с/т",
			"п.а." => "п/а",
			"с.а." => "с/а",

		];
		// справочник сокращений
		$st_types = [
			"муниципальный район",
			"сельское поселение",
			"сельсовет",
			"город-курорт",
			"станица",
			"город ",
			" город",
			"поселок",
			"хутор",
			"деревня",
			"станция",
			"слобода",
			"область",
			"район",
			"республика",
			"автономный округ",
			"улус",
			"автономный",
			"край",
			"пгт",
			"село",
			"«",
			"»",
			"\"",
			",",
			"м\. р\.",
			"м\.р\.",
			"м\.р ",
			"п\. г\.т\.",
			"г\.п\.-г\.",
			"г\.о\.-г\.",
			// "г\. о\. г\.",
			"г\.п\. - г\.",
			"п\. г\.",
			"с\.п\.",
			"с\.с\.",
			"п\.с\.",
			"с\.о\.",
			"с\.мо\.",
			"с\.т\.",
			"п\.а\.",
			"с\.а\.",
			"ст-ца\.",
			"г\.п\.",
			"с\.п\.",
			"р\.п\.",
			"д\.х\.",
			"с\.",
			"п\.",
			"г\.",
			"д\.",
			"х\.",
			"сл\.",
			"^д\.",
			"ст\.",
			"г\.",
			"х\.",

		];
		// пары сивпадающих латинских и кириллических букв (после перевода в нижний регистр)
		$latin_types = [
			"e" => "е",
			"x" => "х",
			"a" => "а",
			"c" => "с",
			"o" => "о",
			"p" => "р",
			"h" => "н",
			"k" => "к",
			"b" => "в",
			"m" => "м",
			"t" => "т",
		];

		// в нижний регистр
		$str = mb_strtolower(trim($name));
		// замена ё на е
		$str = mb_ereg_replace("ё", "е", $str);
		// замена похожих латинских букв на кириллические
		foreach ($latin_types as $k => $v) {
			$str = mb_ereg_replace($k, $v, $str);
		}
		$str = mb_ereg_replace(", д\..*", "", $str);
		// цикл очистки строки от сокращений
		for ($zz = 0; $zz < count($st_types); $zz++) {
			// поиск сокращения по шаблону
			mb_ereg_search_init($str);
			$socr = mb_ereg_search_regs($st_types[ $zz ]);
			if ($socr !== false) {
				// если найдено - проверка по справочнику
				$abbr = $abbr_types[ $socr[ 0 ] ];
				unset($socr);
			}
			// удаление сокращения из строки
			$str = trim(mb_ereg_replace($st_types[ $zz ], "", $str));
		}
		// удаление содержимого в круглых скобках из строки
		//$str = trim(mb_ereg_replace("\((.*)\)","", $str));
		// Замена № на N
		$str = mb_ereg_replace("№ ", "n", $str);
		// Замена № на N
		$str = mb_ereg_replace("№", "n", $str);
		// Замена совхоз на свх
		if ($svh) {
			$str = mb_ereg_replace("совхоза", "свх", $str);
		}
		// Замена совхоз на свх
		$str = mb_ereg_replace("с/х", "свх", $str);
		$str = mb_ereg_replace("с/з", "свх", $str);
		// Замена им. на им
		$str = mb_ereg_replace("им\.", "им", $str);
		// убрать пробелы внутри текста
		$str = preg_replace('/\s{2,}/', ' ', $str);
		//$str = trim($str);

		// заполнение массива
		// оригинальное название
		$a[ "NAME" ] = $name;
		// пормализованное название
		$a[ "NORM" ] = $str;

		// тип населенного пункта
		$a[ "ABBR" ] = $abbr;

		return ($a);
	}

	/*
    * поиск населенного пункта в Ситплан по ID родителя, названию и типу(файлы Гипросвязь)
    */
	protected function searchSP_AO(&$arr, $istype = true, $isequal = true)
	{
		if ($isequal) {
			$txt = " = ";
		} else {
			$txt = " like ";
		}
		if ($istype) {
			// поиск с типом
			if (!isset($arr[ "PAR_ID" ])) {
				$query = "select ao.ID,ao.NAME from
                        FOO_GIS.ATD_OBJECT ao
                        where
                        lower(ao.NAME) $txt :2
                        and ao.PAR_ID is null";

				$param = [
					"2" => $arr[ "NORM" ],
				];
			} else {
				$query = "select ao.ID,ao.NAME from
                        FOO_GIS.ATD_OBJECT ao,
                        FOO_GIS.ATD_OBJ_TYPE aot
                        where
                        lower(ao.NAME) $txt :2 and
                        ao.ATDOBJ_TYPE = aot.CODE and
                        aot.ABBR = :3
                        connect by prior ao.ID = ao.PAR_ID start with ao.ID = :1";

				$param = [
					"1" => $arr[ "PAR_ID" ],
					"2" => $arr[ "NORM" ],
					"3" => $arr[ "ABBR" ],
				];
			}
		} else {
			// поиск без типа
			$query = "select ao.ID,ao.NAME from
                        FOO_GIS.ATD_OBJECT ao
                        where
                        lower(ao.NAME) $txt :2
                        connect by prior ao.ID = ao.PAR_ID start with ao.ID = :1";

			$param = [
				"1" => $arr[ "PAR_ID" ],
				"2" => $arr[ "NORM" ],
			];
		}
		//echo "<br>query = ($query)<br>";
		//echo "<pre>";
		//print_r($param);
		//echo "</pre>";
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//echo "<br>a=<br>";
		//print_r($a);
		//echo "</pre>";
		if (count($a) == 1) {
			$arr[ "ID" ] = $a[ 0 ][ "ID" ];
			$arr[ "RESULT" ] = count($a);
		} else {
			$arr[ "RESULT" ] = count($a);
			if ($istype) {
				$arr[ "ERROR" ] = "Не найден населенный пункт (" . $arr[ "NAME" ] . "),нормализован к (" . $arr[ "NORM" ] . "), с типом (" . $arr[ "ABBR" ] . "), родитель " . $arr[ "PAR_ID" ] . ", совпадений " . count(
						$a
					) . "<br>";
			} else {
				$arr[ "ERROR" ] = "Не найден населенный пункт (" . $arr[ "NAME" ] . "),нормализован к (" . $arr[ "NORM" ] . "), поиск без типа, родитель " . $arr[ "PAR_ID" ] . ", совпадений " . count(
						$a
					) . "<br>";
			}
		}

		//echo "<pre>";
		//echo "<br>arr=<br>";
		//print_r($arr);
		//echo "</pre>";
		return;
	}

	/*
     *
     */
	protected function getSPchildren($par_id)
	{
		$query = "
        select ao.ID,lower(ao.NAME) as NAME,aot.ABBR from
                        FOO_GIS.ATD_OBJECT ao,
                        FOO_GIS.ATD_OBJ_TYPE aot
                        where
                        ao.ATDOBJ_TYPE = aot.CODE
                        connect by prior ao.ID = ao.PAR_ID start with ao.PAR_ID = :1
        ";
		$param = [ "1" => $par_id ];
		$a = $this->db->queryArray($query, $param);
		//for($i = 0; $i < count($a); $i++) {
		//    $b[$a[$i]["ID"]] = $a[$i];
		//}
		//unset($a);
		return ($a);
	}

	/*
     *
     */
	protected function loadCityParamLineDB(&$arr, $param, $line, $issum = true, $ao_id = null, &$log, $logmode = 0)
	{
		//$this->logdebug = true;
		if ((is_numeric($arr[ 5 ]) && $arr[ 5 ] == 0) || $arr[ 5 ] == "") {
			$log .= " ошибка, нельзя обнулить параметр из файла";

			return (0);
		}
		if (!isset($ao_id)) {
			$city = [];

			$res = "";
			//echo "<br>loadCityParamLineDB($line)<br>";
			// цикла по полям строки файла (Гипросвязь)
			for ($i = 0; $i < 4; $i++) {
				//echo "<br>i=$i/".count($arr)." arr[$i]=(".$arr[$i].")<br>";
				if ($logmode != 0) {
					$ff = 0;
				}
				// нормализация названия и выделение типа
				$city[ $i ] = $this->normSP_AO($arr[ $i ]);
				//--------------------------------------
				//echo "<pre>";
				//echo "<br>city[$i]=<br>";
				//echo "<br>NORM=(". $city[$i]["NORM"].")<br>";
				//print_r($city[$i]);
				//echo "</pre>";
				//--------------------------------------
				if ($i > 0) {
					// для элемента уровня ниже первого указываем его родителя
					$city[ $i ][ "PAR_ID" ] = $city[ $i - 1 ][ "ID" ];
				}
				if ((mb_strlen($city[ $i ][ "NAME" ]) == 0) && ($i != 3)) {
					// пустое поле пропускаем
					$city[ $i ][ "ID" ] = $city[ $i - 1 ][ "ID" ];
					if ($logmode != 0 && $ff == 0) {
						$log .= " " . $city[ $i ][ "NAME" ] . " (пропущено пустое поле), ";
						$ff = 1;

					}
					continue;
				}
				// ищем элемент в СитПлан по родителю, названию и типу
				$this->searchSP_AO($city[ $i ]);
				// проверка на ошибку
				if (isset($city[ $i ][ "ERROR" ])) {
					$res .= $city[ $i ][ "ERROR" ];
				}
				if ($logmode != 0 && $ff == 0) {
					if ($city[ $i ][ "RESULT" ] == 1) {
						$log .= " " . $city[ $i ][ "NAME" ] . " (по совпадению), ";
						$ff = 1;
					}
				}
				if (($city[ $i ][ "RESULT" ] != 1) && ($i == 3)) {
					// если не нашди населенный пункт, пытаемся искать его в вышестоящем регионе
					unset($city[ $i ][ "ERROR" ]);
					unset($city[ $i ][ "RESULT" ]);
					$ct = $city[ $i ];
					$ct[ "PAR_ID" ] = $city[ $i - 1 ][ "PAR_ID" ];
					// ищем элемент в СитПлан по родителю, названию, без типа
					$this->searchSP_AO($ct, false);
					// проверка на ошибку
					if (isset($ct[ "ERROR" ])) {
						$res .= $ct[ "ERROR" ];
					}
					if (isset($ct[ "ID" ])) {
						$city[ $i ][ "ID" ] = $ct[ "ID" ];
					}
					$city[ $i ][ "RESULT" ] = $ct[ "RESULT" ];
					$city[ $i ][ "ERROR" ] = $ct[ "ERROR" ];
				}
				if ($logmode != 0 && $ff == 0) {
					if ($city[ $i ][ "RESULT" ] == 1) {
						$log .= " " . $city[ $i ][ "NAME" ] . " (по совпадению в вышестоящем регионе без типа),";
						$ff = 1;
					}
				}
				if (($city[ $i ][ "RESULT" ] != 1) && ($i == 3)) {
					// если не нашди населенный пункт, пытаемся искать его без типа
					// ищем элемент в СитПлан по родителю, названию
					unset($city[ $i ][ "ERROR" ]);
					unset($city[ $i ][ "RESULT" ]);
					$this->searchSP_AO($city[ $i ], false);
					// проверка на ошибку
					if (isset($city[ $i ][ "ERROR" ])) {
						$res .= $city[ $i ][ "ERROR" ];
					}
				}
				if ($logmode != 0 && $ff == 0) {
					if ($city[ $i ][ "RESULT" ] == 1) {
						$log .= " " . $city[ $i ][ "NAME" ] . " (по совпадению без типа),";
						$ff = 1;
					}
				}
				if (($city[ $i ][ "RESULT" ] != 1) && ($i == 3) && (mb_strlen($arr[ $i + 1 ]) != 0)) {
					// если не нашди населенный пункт, пытаемся искать с сельскип поселением
					// ищем элемент в СитПлан по родителю, названию
					//unset($city[$i]["ERROR"]);
					//unset($city[$i]["RESULT"]);
					// нормализация названия и выделение типа
					$city[ $i + 1 ] = $this->normSP_AO($arr[ $i + 1 ]);
					$city[ $i + 1 ][ "PAR_ID" ] = $city[ $i ][ "PAR_ID" ];
					//echo "<pre>";
					//print_r($city[$i+1]);
					//echo "</pre>";
					$city[ $i + 1 ][ "NORM" ] = $city[ $i ][ "NORM" ] . " " . "(" . $city[ $i + 1 ][ "NORM" ] . " _/_)";
					//echo "<pre>";
					//print_r($city[$i+1]);
					//echo "</pre>";
					$this->searchSP_AO($city[ $i + 1 ], false, false);
					// проверка на ошибку
					if (isset($city[ $i + 1 ][ "ERROR" ])) {
						$res .= $city[ $i + 1 ][ "ERROR" ];
					}
					// если нашли с сельским поселением - считаем нашли населенный пункт !!!
					if ($city[ $i + 1 ][ "RESULT" ] == 1) {
						$city[ $i ][ "RESULT" ] = 1;
						$city[ $i ][ "ID" ] = $city[ $i + 1 ][ "ID" ];
						unset($city[ $i ][ "ERROR" ]);
					}
					if ($logmode != 0 && $ff == 0) {
						if ($city[ $i ][ "RESULT" ] == 1) {
							$log .= " " . $city[ $i ][ "NAME" ] . " (по совпадению " . $city[ $i + 1 ][ "NORM" ] . "),";
							$ff = 1;
						}
					}
				}
				if (($city[ $i ][ "RESULT" ] != 1) && ($i != 3)) {
					// не найденное поле пропускаем
					$city[ $i ][ "ID" ] = $city[ $i - 1 ][ "ID" ];
					if ($logmode != 0 && $ff == 0) {

						$log .= " " . $city[ $i ][ "NAME" ] . " (пропущено),";
						$ff = 1;

					}
					continue;
				}
				//echo "<pre>";
				//print_r($city);
				//echo "</pre>";
			}

			//echo "<br>Нашли все<br>";
			//echo "<pre>";
			//print_r($city);
			//echo "</pre>";
			if (isset($city[ $i - 1 ][ "ID" ]) && isset($city[ $i - 1 ][ "RESULT" ]) && $city[ $i - 1 ][ "RESULT" ] == 1) {
				if ($this->logdebug) {
					echo "<br>($line) найден СитПлан ID=" . $city[ $i - 1 ][ "ID" ] . "<br>";
				}
				$ao_id = $city[ $i - 1 ][ "ID" ];
			} else {
				if ($this->logdebug) {
					echo "<br>($line) не найден в СитПлан: $res<br>";
				}
				if (($city[ $i - 1 ][ "RESULT" ] < 1) && ($city[ $i ][ "RESULT" ] < 1)) {
					// получить всех детей последнего найденного элемента
					$par_id = $city[ $i - 1 ][ "PAR_ID" ];
					$chld = $this->getSPchildren($par_id);
					$ret = $this->getSPmaxweight($city[ $i - 1 ], $chld);
					if ($ret > 0) {
						if ($logmode != 0 && $ff == 0) {

							$log .= " " . $city[ $i - 1 ][ "NAME" ] . " (по лучшему весу),";
							$ff = 1;

						}
						if ($this->logdebug) {
							echo "<br>($line) найден СитПлан ID=" . $ret . "<br>";
						}
						$ao_id = $ret;
					} else {
						$res .= "<br>Не найден среди всех детей последнего элемента";
						if ($logmode != 0 && $ff == 0) {

							$log .= " " . $city[ $i - 1 ][ "NAME" ] . " (не найдено), ";
							$ff = 1;

						}
					}
				} else {
					if ($logmode != 0 && $ff == 0) {

						$log .= " " . $city[ $i - 1 ][ "NAME" ] . " (не найдено), ";
						$ff = 1;

					}
				}
			}
		} else {
			if ($logmode != 0) {
				$log .= " (по идентификатору),";
			}
		}
		//else {
		//    echo "---";
		//}
		if (isset($ao_id) && ($ao_id > 0)) {
			//нашли населенный пункт
			$param[ "VALUE" ] = $arr[ 5 ];
			$ret = $this->insSPparam($ao_id, $param, $issum, true, $log);
			if ($ret != 0) {
				return (0);
			}

			return ($ao_id);
		}
		if ($logmode != 0) {

			$log .= " !(не найдено)! ";
			$ff = 1;

		}
		if ($this->logdebug) {
			echo "<br>развернутая информация по строке ($line)<br>------<br> не найден в СитПлан: $res<br>------<br><br>";
		}

		return (0);
	}

	/*
     *
     */
	private function insSPparam($ao_id, $param, $issum = true, $docommit = true, &$log)
	{
		$name = $param[ "NAME" ];
		$type = $param[ "TYPE" ];
		$value = $param[ "VALUE" ];
		if ($this->isLockAO($ao_id, $name) == 1) {
			$log .= " ошибка, параметр заблокирован в регионе";

			return 1;
		}
		if ($value == "" || $value == 0) {
			// попытка обнулить параметр
			if ($this->checkSPaddr($ao_id, $name) != 0) {
				// у параметра есть адрес в облаке или кластере - обнулять нельзя
				$log .= " ошибка, нельзя обнулить параметр с адресом в облаке";

				return (1);
			}
		}
		if ($issum && (strstr($name, "_NUM") !== false)) {
			$updtxt = "rc.$name = nvl(rc.$name,0) + :2";
		} else {
			// проверить на тип _DATE
			if (strstr($name, "_DATE") !== false) {
				// значение MM.YYYY или DD.MM.YYYY, уже проверено в checkSPparamsType
				$arr = explode(".", $value);
				if (count($arr) == 3) {
					// DD.MM.YYYY
					$updtxt = " rc.$name = to_date(:2,'DD.MM.YYYY') ";

				} else {
					// MM.YYYY
					$updtxt = " rc.$name = to_date(:2,'MM.YYYY') ";
				}
			} else {
				$updtxt = "rc.$name = :2";
			}
		}
		$updtxt .= " , rc.AU_ID = :au_id ";


		$query = "
            MERGE INTO FOO_GIS.RTK_CITYPARAMS rc
                    USING (SELECT 1 FROM DUAL) sd
                    ON (rc.ID = :1)
                    WHEN MATCHED THEN
                        UPDATE SET
                        $updtxt
                    WHEN NOT MATCHED THEN
                        INSERT (
                            AU_ID,
                            ID,
                            $name
                            )
                         VALUES (
                            :au_id,
                            :1,
                            :2
                            )";
		$param = [
			"1" => $ao_id,
			"2" => $value,
			"au_id" => $this->perm->UserID,
		];

		$this->db->query($query, $param);
		if ($docommit) {
			$this->db->commit();
		}

		return (0);
	}

	/*
     *
     */
	private function getSPmaxweight($cityo, $chld)
	{
		$maxres1 = 10000;
		$maxres2 = 10000;
		$saved1 = -1;
		$saved2 = -1;

		$city = $this->normSP_AO($cityo[ "NAME" ], false);
		//$city = $cityo;
		for ($i = 0; $i < count($chld); $i++) {
			/* вычислить вес совпадения */
			$res = $this->calcInstrKLADR($city[ "NORM" ], $chld[ $i ][ "NAME" ]);
			if ($res < $maxres1) {
				$saved1 = $i;
				$maxres1 = $res;
			}
		}
		$res = 100000;
		for ($i = 0; $i < count($chld); $i++) {
			if ($i == $saved1) {
				continue;
			}
			/* вычислить вес совпадения */
			$res = $this->calcInstrKLADR($city[ "NORM" ], $chld[ $i ][ "NAME" ]);
			if ($res < $maxres2) {
				$saved2 = $i;
				$maxres2 = $res;
			}
		}
		//echo "<pre>";
		//echo "<br>Искомое: <br>";
		//print_r($city);
		//echo "<br>Претендентов: ".count($chld)."<br>";
		//echo "<br>Наилучшее: вес = $maxres1<br>";
		//print_r($chld[$saved1]);
		//echo "<br>Второе место: вес = $maxres2<br>";
		//print_r($chld[$saved2]);
		//echo "</pre>";
		$len = floor((mb_strlen($city[ "NORM" ]) / 3));
		//echo "len=$len";
		if (($maxres1 <= $len) && (($maxres2 - $maxres1) >= 2)) {
			//echo "<br>Нашли совпадение!!!<br>";
			return ($chld[ $saved1 ][ "ID" ]);
		}

		return (0);
	}

	public function getSPTypes()
	{
		$query = "select * from FOO_GIS.ATD_OBJ_TYPE order by code";
		$param = [];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/*
     *
     */
	public function delSPparams($ao_id, $param_name = "")
	{
		$found = 0;
		if (mb_strlen($param_name) == 0) {
			return null;
		}
		if ($this->mb_strcmp($param_name, "ALL") == 0) {
			$td = $this->getRtkTdTypes();

			for ($i = 0; $i < count($td); $i++) {
				if ($this->checkSPaddr($ao_id, $td[ $i ][ "PARAM_NAME" ]) != 0) {
					// у параметра есть адрес в облаке или кластере - обнулять нельзя
					return "<br> ошибка, нельзя обнулить параметр " . $td[ $i ][ "PARAM_NAME" ] . " с адресом в облаке <br>";

				}
			}
			//здесь надо пробежаться в цикле по всем именам параметров из типов ТД и проверить их !!!!
			$columns = $this->getTableColumns("FOO_GIS.RTK_CITYPARAMS");

			for ($i = 0; $i < count($columns); $i++) {
				if (strcmp($columns[ $i ][ "NAME" ], "ID") == 0) {
					continue;
				}
				if (strcmp($columns[ $i ][ "NAME" ], "AU_ID") == 0) {
					continue;
				}
				if ($this->isLockAO($ao_id, $columns[ $i ][ "NAME" ]) == 1) {
					return "<br> ошибка, параметр " . $columns[ $i ][ "NAME" ] . " заблокирован в регионе<br>";

				}
			}

			$query = "delete from FOO_GIS.RTK_CITYPARAMS where ID in
                    (select ID from FOO_GIS.ATD_OBJECT
                    connect by prior ID = PAR_ID start with ID = :1)";
			$param = [
				"1" => $ao_id,
			];
		} else {

			if ($this->checkSPaddr($ao_id, $param_name) != 0) {
				// у параметра есть адрес в облаке или кластере - обнулять нельзя
				return "<br> ошибка, нельзя обнулить параметр ($param_name) с адресом в облаке <br>";

			}
			if ($this->isLockAO($ao_id, $param_name) == 1) {
				return "<br> ошибка, параметр " . $param_name . " заблокирован в регионе<br>";

			}
			$columns = $this->getTableColumns("FOO_GIS.RTK_CITYPARAMS");
			for ($i = 0; $i < count($columns); $i++) {
				if (strcmp($columns[ $i ][ "NAME" ], $param_name) == 0) {
					$found = 1;
					break;
				}
			}
			if ($found == 0) {
				return "<br>Ошибка, неизвестный параметр $param_name<br>";

			}
			$query = "update FOO_GIS.RTK_CITYPARAMS set $param_name = null, AU_ID = :au_id where ID in
                    (select ID from FOO_GIS.ATD_OBJECT
                    connect by prior ID = PAR_ID start with ID = :1)";
			$param = [
				"1" => $ao_id,
				"au_id" => $this->perm->UserID,
			];
		}

		//echo "<br>($query)<br>";
		$this->db->query($query, $param);
		$this->db->commit();

		return null;
	}


	/*
     *
     */
	public function getSPparamNames()
	{
		$result = [];
		$query = "select * from all_col_comments where table_name = 'RTK_CITYPARAMS'";
		$param = [];
		$b = $this->db->queryArray($query, $param);

		$columns = $this->getTableColumns("FOO_GIS.RTK_CITYPARAMS");
		for ($i = 0; $i < count($columns); $i++) {
			if (strcmp($columns[ $i ][ "NAME" ], "AU_ID") == 0) {
				continue;
			}
			if (strcmp($columns[ $i ][ "NAME" ], "ADDRID_NUM") == 0) {
				continue;
			}
			if (strcmp($columns[ $i ][ "NAME" ], "ID") == 0) {
				$columns[ $i ][ "NAME" ] = "ALL";
				$columns[ $i ][ "COMMENT" ] = "Все параметры";
				$result[] = $columns[ $i ];
				continue;
			}
			for ($j = 0; $j < count($b); $j++) {
				if (strcmp($columns[ $i ][ "NAME" ], $b[ $j ][ "COLUMN_NAME" ]) == 0) {
					$columns[ $i ][ "COMMENT" ] = $b[ $j ][ "COMMENTS" ];
				}
			}
			$result[] = $columns[ $i ];
		}

		return ($result);
	}

	public function sendSPregparams2CSVHTML($info, $sep, $encoding)
	{
		$head = "Название субъекта РФ" .
			$sep . "Количество таксофонов" .
			$sep . "Количество населенных пунктов таксофонов" .
			$sep . "Количество ПКД" .
			$sep . "Количество населенных пунктов ПКД" .
			$sep . "Количество точек доступа" .
			$sep . "Количество населенных пунктов точек доступа" .
			$sep . "Количество объектов энергетиков" .
			$sep . "Количество населенных пунктов объектов энергетиков" .
			$sep . "Количество точек доступа РТК" .
			$sep . "Количество населенных пунктов точек доступа РТК" .
			"\n";

		if ($encoding != "UTF-8") {
			$head = iconv("UTF-8", $encoding, $head);
		}
		echo $head;
		$data = $info;
		$str = "";


		for ($k = 0; $k < count($data); $k++) {
			$str =

				$data[ $k ][ "ao" ][ 'NAME' ] . $sep .

				$data[ $k ][ "params" ][ 'TFONES_NUM' ] . $sep .
				$data[ $k ][ "params" ][ 'TFONES_NUM_CITY' ] . $sep .
				$data[ $k ][ "params" ][ 'PKD_NUM' ] . $sep .
				$data[ $k ][ "params" ][ 'PKD_NUM_CITY' ] . $sep .
				$data[ $k ][ "params" ][ 'AP_NUM' ] . $sep .
				$data[ $k ][ "params" ][ 'AP_NUM_CITY' ] . $sep .
				$data[ $k ][ "params" ][ 'OE_NUM' ] . $sep .
				$data[ $k ][ "params" ][ 'OE_NUM_CITY' ] . $sep .
				$data[ $k ][ "params" ][ 'TDRTK_NUM' ] . $sep .
				$data[ $k ][ "params" ][ 'TDRTK_NUM_CITY' ] . $sep .
				"\n";
			if ($encoding != "UTF-8") {
				$str = iconv("UTF-8", $encoding, $str);
			}
			echo $str;

		}

	}

	public function sendClusterCableReport2CSVHTML($info, $sep, $encoding)
	{
		$head = "Филиал" .
			$sep . "Субъект РФ" .
			$sep . "Населенный пункт" .
			$sep . "Опорная точка" .
			$sep . "Кластер" .
			$sep . "Кабель" .
			$sep . "Существующий" .
			$sep . "Привязка" .
			$sep . "Номер заказа" .
			$sep . "Номер заявки ПИР" .
			$sep . "Номер заявки СМР" .
			"\n";

		if ($encoding != "UTF-8") {
			$head = iconv("UTF-8", $encoding, $head);
		}
		echo $head;
		$data = $info;

		for ($k = 0; $k < count($data); $k++) {
			$str =

				$data[ $k ][ "DEP" ] . $sep .
				$data[ $k ][ "REG" ] . $sep .
				$data[ $k ][ "CITY" ] . $sep .
				$data[ $k ][ "OT" ] . $sep .
				$data[ $k ][ "CL" ] . $sep .
				$data[ $k ][ "CABLE" ] . $sep .
				$data[ $k ][ "OLD" ] . $sep .
				$data[ $k ][ "CON_TD" ] . $sep .
				$data[ $k ][ "CONTRACT_NUMBER" ] . $sep .
				$data[ $k ][ "PIR_ORDER_ID" ] . $sep .
				$data[ $k ][ "SMR_ORDER_ID" ] . $sep .
				"\n";
			if ($encoding != "UTF-8") {
				$str = iconv("UTF-8", $encoding, $str);
			}
			echo $str;

		}

	}

	/**
	 * Вывод в excel отчета по готовности клстеров УЦН
	 *
	 * @param $info
	 * @param $sep
	 * @param $encoding
	 *
	 */
	public function sendClusteredReady2CSVHTML($info, $sep, $encoding, $mode, $notexists)
	{
		if ($mode == "Reg") {
			$head = "Субъект РФ";
			$txt = "субъекте";
		} else {
			if ($mode == "Dep") {
				$head = "Филиал";
				$txt = "филиале";
			} else {
				if ($mode == "Voc") {
					$head = "Филиал" . $sep . "Субъект РФ" . $sep . "Населенный пункт" . $sep . "облако";
					$txt = "облаке";
				} else {
					if ($mode == "Mun") {
						$head = "Филиал" . $sep . "Субъект РФ" . $sep . "Район";
						$txt = "районе";
					}
				}
			}
		}
		if ($notexists == "on") {
			$notextxt = "(без учета существующих)";
		} else {
			$notextxt = "";
		}
		if ($mode != "Voc") {
			$head .=
				$sep . "ТД в $txt" .
				$sep . "Населенных пунктов ТД" .
				$sep . "Объектов энергетиков в $txt" .
				$sep . "Населенных пунктов объектов энергетиков" .
				$sep . "ТД РТК в $txt" .
				$sep . "Населенных пунктов ТД РТК" .
				$sep . "Облаков УЦН в $txt" .
				$sep . "Кластеров УЦН в $txt" .
				$sep . "Кластеризовано ТД в $txt" .
				$sep . "Кластеризовано ОЭ в $txt" .
				$sep . "Кластеризовано ТДРТК в $txt";
		}
		if ($mode != "Mun") {
			$head .=
				$sep . "Внесено в СитПлан участков ВОК $notextxt в $txt" .
				$sep . "Для участков ВОК $notextxt указан тип прокладки в $txt" .
				$sep . "Для участков ВОК $notextxt указана длина>0 в $txt" .
				$sep . "Для участков ВОК $notextxt указан тип кабеля в $txt" .
				$sep . "Для участков ВОК $notextxt указан тип муфт в $txt" .
				$sep . "Для участков ВОК $notextxt указан признак существующего кабеля в $txt" .
				$sep . "Всего владельцев кабельной инфраструктуры $notextxt в $txt" .
				$sep . "Для участков ВОК $notextxt указан владелец в $txt" .
				$sep . "Количество транзитных ВОК $notextxt в $txt" .
				$sep . "Количество транзитных ВОК $notextxt, привязанных к ТД в $txt" .
				$sep . "Количество ВОК $notextxt, привязанных к ТД в $txt";
		}
		$head .=
			"\n";

		if ($encoding != "UTF-8") {
			$head = iconv("UTF-8", $encoding, $head);
		}
		echo $head;
		$data = $info;
		$str = "";


		for ($k = 0; $k < count($data); $k++) {
			if ($mode == "Mun") {
				$str =
					$data[ $k ][ "dep" ][ 'NAME' ] . $sep .
					$data[ $k ][ "ao" ][ 'PNAME' ] . $sep .
					$data[ $k ][ "ao" ][ 'NAME' ] . $sep;
			} else {
				if ($mode == "Voc") {
					$str =
						$data[ $k ][ "dep" ][ 'NAME' ] . $sep .
						$data[ $k ][ "ao" ][ 'PNAME' ] . $sep .
						$data[ $k ][ "ao" ][ 'NAME' ] . $sep .
						$data[ $k ][ "ot" ][ 'NAME' ] . $sep;
				} else {
					$str =
						$data[ $k ][ "ao" ][ 'NAME' ] . $sep;
				}
			}
			if ($mode != "Voc") {
				$str .=
					$data[ $k ][ 'ap_num' ] . $sep .
					$data[ $k ][ 'ap_num_city' ] . $sep .
					$data[ $k ][ 'oe_num' ] . $sep .
					$data[ $k ][ 'oe_num_city' ] . $sep .
					$data[ $k ][ 'tdrtk_num' ] . $sep .
					$data[ $k ][ 'tdrtk_num_city' ] . $sep .
					$data[ $k ][ 'ot_num' ] . $sep .
					$data[ $k ][ 'cl_num' ] . $sep .
					$data[ $k ][ 'td_cl_num' ] . $sep .
					$data[ $k ][ 'oe_cl_num' ] . $sep .
					$data[ $k ][ 'tdrtk_cl_num' ] . $sep;
			}
			if ($mode != "Mun") {
				$str .=
					$data[ $k ][ "voc" ][ 'VOK_CNT' ] . $sep .
					$data[ $k ][ "voc" ][ 'VOK_CABLING_CNT' ] . $sep .
					$data[ $k ][ "voc" ][ 'VOK_CABLELEN_CNT' ] . $sep .
					$data[ $k ][ "voc" ][ 'VOK_CABLETYPE_CNT' ] . $sep .
					$data[ $k ][ "voc" ][ 'VOK_MFRIGHT_CNT' ] . $sep .
					$data[ $k ][ "voc" ][ 'VOK_OLDCABLE_CNT' ] . $sep .
					$data[ $k ][ "voc" ][ 'VOK_UNIQCUSTOMID_CNT' ] . $sep .
					$data[ $k ][ "voc" ][ 'VOK_CUSTOMID_CNT' ] . $sep .
					$data[ $k ][ "voc" ][ 'VOK_TRANSIT_CNT' ] . $sep .
					$data[ $k ][ "voc" ][ 'VOK_TRANSITCONTD_CNT' ] . $sep .
					$data[ $k ][ "voc" ][ 'VOK_CONID_CNT' ] . $sep;
			}
			$str .=
				"\n";
			if ($encoding != "UTF-8") {
				$str = iconv("UTF-8", $encoding, $str);
			}
			echo $str;

		}

	}

	/**
	 * Вывести отчет о длинах кабелей УЦН
	 *
	 * @param $data
	 * @param $sep
	 * @param $encoding
	 * @param $mode
	 * @param $notexists
	 *
	 */
	public function sendCablesLength2CSVHTML($data, $sep, $encoding, $mode, $notexists)
	{

		if ($mode == "Reg") {
			$head = "Субъект РФ";
		} else {
			if ($mode == "Dep") {
				$head = "Филиал";
			}
		}
		$head .=
			$sep . "Тип прокладки" .
			$sep . "Емкость кабеля" .
			$sep . "Длина (м)";
		$head .=
			"\n";

		if ($encoding != "UTF-8") {
			$head = iconv("UTF-8", $encoding, $head);
		}
		echo $head;

		$str = "";

		for ($k = 0; $k < count($data); $k++) {
			for ($m = 0; $m < count($data[ $k ][ "replen" ]); $m++) {
				if (isset($data[ $k ][ "replen" ][ $m ][ "LEN" ]) && ($data[ $k ][ "replen" ][ $m ][ "LEN" ] > 0)) {
					$str .=
						$data[ $k ][ "ao" ][ 'NAME' ] . $sep .
						$data[ $k ][ "replen" ][ $m ][ "CABLING_TYPE_NAME" ] . $sep .
						$data[ $k ][ "replen" ][ $m ][ "CABLE_WIDTH" ] . $sep .
						$data[ $k ][ "replen" ][ $m ][ "LEN" ] . $sep;
					$str .=
						"\n";
				}
			}
		}
		if ($encoding != "UTF-8") {
			$str = iconv("UTF-8", $encoding, $str);
		}
		echo $str;
	}


	public function sendOrdersReport2CSVHTML($data, $sep, $encoding, $notexists)
	{

		$head =
			"Филиал" .
			$sep . "Субъект РФ" .
			$sep . "Населенный пункт" .
			$sep . "Номер заказа" .
			$sep . "Длина ВОЛС (км)" .
			$sep . "Длина ВОЛС СитПлан (км)" .
			$sep . "Номер заявки ПИР" .
			$sep . "Сумма ПИР (руб)" .
			$sep . "Номер заявки СМР" .
			$sep . "Сумма СМР (руб)" .
			$sep . "Дата начала ПИР" .
			$sep . "Дата окончания ПИР" .
			$sep . "Дата начала СМР" .
			$sep . "Дата окончания СМР" .
			$sep . "Дата ввода в эксплуатацию" .
			$sep . "ID ДС ОФУ R12" .
			$sep . "Номер групповой заявки" .
			$sep . "Групповой ID ДС ОФУ R12";
		$head .=
			"\n";

		if ($encoding != "UTF-8") {
			$head = iconv("UTF-8", $encoding, $head);
		}
		echo $head;

		$str = "";

		for ($k = 0; $k < count($data); $k++) {
			$str .=
				$data[ $k ][ 'AU_DEPNAME' ] . $sep .
				$data[ $k ][ 'REGION' ] . $sep .
				$data[ $k ][ 'NAME' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'CONTRACT_NUMBER' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'VOLS_LEN' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'MF_LEN' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'PIR' ][ 'ORDER_ID' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'PIR' ][ 'ACTION_SUM' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'SMR' ][ 'ORDER_ID' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'SMR' ][ 'ACTION_SUM' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'PIR' ][ 'CONTRACT_FROMDATE' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'PIR' ][ 'CONTRACT_TODATE' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'SMR' ][ 'CONTRACT_FROMDATE' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'SMR' ][ 'CONTRACT_TODATE' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'SMR' ][ 'CONTRACT_ENDDATE' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'ID_DSOFYR12' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'GRP_ORDER_ID' ] . $sep .
				$data[ $k ][ 'ORDER' ][ 'GRP_ID_DSOFYR12' ];
			$str .=
				"\n";
		}
		if ($encoding != "UTF-8") {
			$str = iconv("UTF-8", $encoding, $str);
		}
		echo $str;
	}


	public function sendSPparams2CSVHTML($info, $sep, $encoding)
	{
		$head = "Идентификатор записи" .
			$sep . "Номер по реестру МинСвязи" .
			$sep . "Название субъекта РФ" .
			$sep . "Название МО" .
			// $sep . "Название населенного пункта" .
			$sep . "Сельское поселение" .
			$sep . "Название внутригородского населенного пункта" .
			$sep . "Название населенного пункта" .
			// $sep . "Сельское поселение" .
			$sep . "Количество жителей" .
			$sep . "Наличие ВОЛС" .
			$sep . "Наличие спутниковой связи" .
			$sep . "Количество таксофонов" .
			$sep . "Количество ПКД" .
			$sep . "Количество точек доступа" .
			$sep . "Количество точек доступа для допсоглашения" .
			$sep . "Количество точек доступа по плану 'Б'" .
			$sep . "Количество объектов  энергетиков" .
			$sep . "Количество точек доступа РТК" .
			$sep . "Статус включения в договор" .
			$sep . "Наличие арендованной линии связи" .
			$sep . "Наличие существующей СПД" .
			$sep . "Дата строительства ВОЛС" .
			$sep . "Фактическая дата окончания строительства" .
			$sep . "КЛАДР" .
			$sep . "ФИАС" .
			$sep . "Комментарий" .
			$sep . "Признак плана" .
			$sep . "IP адрес базовой станции" .
			$sep . "Хост базовой станции" .
			"\n";

		if ($encoding != "UTF-8") {
			$head = iconv("UTF-8", $encoding, $head);
		}
		echo $head;
		$data = $info[ "data" ];
		$str = "";

		for ($k = 0; $k < count($data); $k++) {
			$str =
				$data[ $k ][ 'ID' ] . $sep .
				$data[ $k ][ 'REESTR_STR' ] . $sep .
				$data[ $k ][ 'SUBJECT_NAME' ] . $sep .
				$data[ $k ][ 'MO_NAME' ] . $sep .
				// $data[ $k ][ 'NP_NAME' ] . $sep .
				$data[ $k ][ 'SETTLEMENT_STR' ] . $sep .
				$data[ $k ][ 'INP_NAME' ] . $sep .
				// $data[ $k ][ 'SETTLEMENT_STR' ] . $sep .
				$data[ $k ][ 'NP_NAME' ] . $sep .
				$data[ $k ][ 'PEOPLE_NUM' ] . $sep .
				$data[ $k ][ 'FIBEROPTIC_FLAG' ] . $sep .
				$data[ $k ][ 'SATELLITE_FLAG' ] . $sep .
				$data[ $k ][ 'TFONES_NUM' ] . $sep .
				$data[ $k ][ 'PKD_NUM' ] . $sep .
				$data[ $k ][ 'AP_NUM' ] . $sep .
				$data[ $k ][ 'APDOP_NUM' ] . $sep .
				$data[ $k ][ 'APB_NUM' ] . $sep .
				$data[ $k ][ 'OE_NUM' ] . $sep .
				$data[ $k ][ 'TDRTK_NUM' ] . $sep .
				$data[ $k ][ 'CONTRACT_FLAG' ] . $sep .
				$data[ $k ][ 'LEASEDLINE_FLAG' ] . $sep .
				$data[ $k ][ 'EXISTDTN_FLAG' ] . $sep .
				$data[ $k ][ 'FIBERBUILD_DATE' ] . $sep .
				$data[ $k ][ 'FIBERBUILDFACT_DATE' ] . $sep .
				$data[ $k ][ 'KLADR' ] . $sep .
				$data[ $k ][ 'FIAS' ] . $sep .
				$data[ $k ][ 'COMMENT_STR' ] . $sep .
				$data[ $k ][ 'PLAN_STR' ] . $sep .
				$data[ $k ][ 'IP_STR' ] . $sep .
				$data[ $k ][ 'HOST_STR' ] .
				"\n";
			if ($encoding != "UTF-8") {
				$str = iconv("UTF-8", $encoding, $str);
			}
			echo $str;
		}
	}


	/*
     *
     */
	public function getSPparams($ao_id = "", $dep_id = "", $dofull = true, $doaddr = false, $odf_type = "")
	{
		$txt = "";
		$aotxt = " ";
		$deptxt = " ";
		$param = [];

		$columns = $this->getTableColumns("FOO_GIS.RTK_CITYPARAMS");

		for ($k = 0; $k < count($columns); $k++) {
			if (strstr($columns[ $k ][ "NAME" ], "_DATE") !== false) {
				$txt .= ", to_char(rc." . $columns[ $k ][ "NAME" ] . ", 'DD.MM.YYYY') as " . $columns[ $k ][ "NAME" ];
			} else {
				$txt .= ", rc." . $columns[ $k ][ "NAME" ];
			}
		}
		if ($ao_id != "") {
			$aotxt = "connect by prior ao.ID = ao.PAR_ID start with ao.ID = :1 ";
			$param = [
				"1" => $ao_id,
			];
		}
		if ($dep_id != "") {
			$deptxt = " connect by prior ao.ID = ao.PAR_ID start with ao.ID in
            (select AREA_ID from FOO_AUTH.AUTH_DEPARTMENT_AREAS where AU_DEPID in
            (select AU_DEPID from FOO_AUTH.AUTH_DEPARTMENTS
            connect by prior AU_DEPID = AU_DEPPARID start with AU_DEPID = :1))";
			$param = [
				"1" => $dep_id,
			];
		}
		if (!$dofull) {
			// ограничиться списком регионов департамента
			$dofulltxt = "FOO_GIS.CHECKCITY(:2, ao.ID) = 1 and";
			$param[ "2" ] = $this->perm->UserID;
		}

		if ($odf_type != "") {
			$td = $this->getRtkTdType($odf_type);
			if (count($td) > 0) {
				$dofulltxt .= " nvl(rc." . $td[ "PARAM_NAME" ] . ",0)>0 and ";
			}
		}
		$query = "select LEVEL,
        SYS_CONNECT_BY_PATH(ao.NAME || ' ' || aot.NAME, '|') as PATH $txt from
                    FOO_GIS.RTK_CITYPARAMS rc,
                    FOO_GIS.ATD_OBJECT ao,
                    FOO_GIS.ATD_OBJ_TYPE aot
                    where
                    rc.ID (+)= ao.ID and rc.ID is not null and
                    $dofulltxt
                    nvl(AO.ATDOBJ_TYPE,0) = AOT.CODE(+)
                    $aotxt $deptxt
                    order by PATH";

		//echo "<br>($query)<br>";
		$a = $this->db->queryArray($query, $param);
		for ($i = 0; $i < count($a); $i++) {
			$d = $this->getFiasBySpId($a[ $i ][ "ID" ]);
			$a[ $i ][ "KLADR" ] = $d[ 0 ][ "CODE" ];
			$a[ $i ][ "FIAS" ] = $d[ 0 ][ "FIAS" ];
			if ($odf_type != "" && $doaddr) {
				$c = $this->getRtkTDbyAOandType($a[ $i ][ "ID" ], $odf_type);
				$a[ $i ][ "ADDRESS" ] = $c[ 0 ][ "ADDRESS" ];
				$a[ $i ][ "ADDR_ID" ] = $c[ 0 ][ "ADDR_ID" ];
				$a[ $i ][ "ODF_TYPE" ] = $c[ 0 ][ "ODF_TYPE" ];
			}
			$b = explode("|", $a[ $i ][ "PATH" ]);
			$a[ $i ][ "SUBJECT_NAME" ] = $b[ 1 ];
			$a[ $i ][ "MO_NAME" ] = $b[ 2 ];
			$a[ $i ][ "NP_NAME" ] = $b[ 3 ];
			$a[ $i ][ "INP_NAME" ] = $b[ 4 ];
		}
		//echo "<pre>";
		//print_r($param);
		//print_r($a);
		//echo "</pre>";
		$result[ "columns" ] = $columns;
		$result[ "data" ] = $a;
		$result[ "count" ] = count($a);

		return ($result);
	}

	/**
	 * получить коды КЛАДР и ФИАС по ID НП СП
	 *
	 * @param $ao_id
	 *
	 * @return array|int
	 *
	 */
	public function getFiasBySpId($ao_id)
	{
		$query = "select km.FIAS, km.CODE from FOO_GIS.KLADR_MAIN km, FOO_GIS.ATD_OBJECT ao
          where ao.ID = :ao_id and km.CODE = ao.CODE
        ";
		$param = [ "ao_id" => $ao_id ];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/*
       *
       */
	public function getSPparamsForOT($ao_id, $ot_id = null)
	{
		$txt = "";

		$columns = $this->getTableColumns("FOO_GIS.RTK_CITYPARAMS");

		for ($k = 0; $k < count($columns); $k++) {
			$txt .= ", rc." . $columns[ $k ][ "NAME" ];
		}
		if ($ao_id != "") {
			$aotxt = "connect by prior ao.ID = ao.PAR_ID start with ao.ID = :1 ";
			$param = [
				"1" => $ao_id,
			];
		} else {
			$aotxt = " ";
			$param = [];
		}

		$query = "select
                    (select ao1.NAME || ' ' || aot1.NAME
                    from FOO_GIS.ATD_OBJECT ao1,
                    FOO_GIS.ATD_OBJ_TYPE aot1 where
                    ao1.PAR_ID is null and
                    nvl(ao1.ATDOBJ_TYPE,0) = aot1.CODE(+)
                    connect by prior ao1.PAR_ID = ao1.ID start with ao1.ID = ao.ID) as  SUBJECT_NAME,
                    (select ao2.NAME || ' ' || aot2.NAME
                    from FOO_GIS.ATD_OBJECT ao2,
                    FOO_GIS.ATD_OBJ_TYPE aot2,
                    FOO_GIS.ATD_OBJECT ao1
                     where
                    ao1.PAR_ID is null and
                    ao2.PAR_ID = ao1.ID and
                    nvl(ao2.ATDOBJ_TYPE,0) = aot2.CODE(+)
                    connect by prior ao2.PAR_ID = ao2.ID start with ao2.ID = ao.ID) as  MO_NAME,
                    ao.NAME || ' ' || aot.NAME as NP_NAME $txt from
                    FOO_GIS.RTK_CITYPARAMS rc,
                    FOO_GIS.ATD_OBJECT ao,
                    FOO_GIS.ATD_OBJ_TYPE aot
                    where
                    rc.ID (+)= ao.ID and rc.ID is not null and nvl(rc.AP_NUM,0) > 0 and
                    nvl(AO.ATDOBJ_TYPE,0) = AOT.CODE(+)
                    $aotxt
                    order by SUBJECT_NAME, MO_NAME, NP_NAME";

		//echo "<br>($query)<br>";
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($param);
		//print_r($a);
		//echo "</pre>";
		$result[ "columns" ] = $columns;
		$result[ "data" ] = $a;
		$result[ "count" ] = count($a);

		return ($result);
	}

	/** поменять формат даты c 2014-12-18 00:00:00 на DD.MM.YYYY
	 *
	 * @param $a
	 */
	public function toSPDate(&$a)
	{
		while (list($k, $v) = each($a)) {
			if (strstr($k, "_DATE") !== false) {
				if (strlen($v) > 0) {
					$dt = date_parse($v);
					$a[ $k ] = sprintf("%2d.%2d.%4d", $dt[ "day" ], $dt[ "month" ], $dt[ "year" ]);
				}
			}
		}
	}

	/*
        *
        */
	public function getSPparamsById($city_id)
	{
		$result = [];

		$query = "select * from all_col_comments where table_name = 'RTK_CITYPARAMS'";
		$param = [];
		$b = $this->db->queryArray($query, $param);

		$columns = $this->getTableColumns("FOO_GIS.RTK_CITYPARAMS");
		for ($i = 0; $i < count($columns); $i++) {
			for ($j = 0; $j < count($b); $j++) {
				if (strcmp($columns[ $i ][ "NAME" ], $b[ $j ][ "COLUMN_NAME" ]) == 0) {
					$columns[ $i ][ "COMMENT" ] = $b[ $j ][ "COMMENTS" ];
				}
			}
		}

		$query = "select * from FOO_GIS.RTK_CITYPARAMS where ID = :1";
		$param = [ "1" => $city_id ];

		//echo "<br>($query)<br>";
		$a = $this->db->queryArray($query, $param);
		if (count($a) > 0) {
			$d = $this->getFiasBySpId($city_id);
			$a[ 0 ][ "FIAS" ] = $d[ 0 ][ "FIAS" ];
			$columns[] = [ "NAME" => "FIAS", "COMMENT" => "Код ФИАС", "TYPE" => "VARCHAR2" ];
		}
		//echo "<pre>";
		//print_r($param);
		//print_r($a);
		//echo "</pre>";
		$result[ "columns" ] = $columns;
		$this->toSPDate($a[ 0 ]);
		$result[ "data" ] = $a[ 0 ];

		return ($result);
	}


	/*
        *
        */
	public function checkSPparamsType($name, $value, &$err)
	{
		if (mb_strlen($value) == 0) {
			// пустое поле - или ноль или пустая строка
			return (0);
		}
		if (strstr($name, "_NUM") !== false) {
			// цифровое значение
			if (!is_numeric($value)) {
				$err = "ошибка, ожидается числовое значение ($value) для параметра $name";

				return (-1);
			}
		} else {
			if (strstr($name, "_FLAG") !== false) {
				// цифровое значение
				if (!is_numeric($value)) {
					$err = "ошибка, ожидается числовое значение ($value) для параметра $name";

					return (-1);
				}

			} else {
				if (strstr($name, "_DATE") !== false) {
					// значение MM.YYYY или DD.MM.YYYY
					$arr = explode(".", $value);
					if (count($arr) == 3) {
						// DD.MM.YYYY
						if (checkdate($arr[ 1 ], $arr[ 0 ], $arr[ 2 ]) !== true) {
							$err = "ошибка, ожидается дата в формате DD.MM.YYYYY или MM.YYYY вместо ($value) для параметра $name";

							return (-1);
						}
					} else {
						if (count($arr) == 2) {
							// MM.YYYY
							if (checkdate($arr[ 0 ], 1, $arr[ 1 ]) !== true) {
								$err = "ошибка, ожидается дата в формате DD.MM.YYYYY или MM.YYYY вместо ($value) для параметра $name";

								return (-1);
							}
						} else {
							$err = "ошибка, ожидается дата в формате DD.MM.YYYYY или MM.YYYY вместо ($value) для параметра $name";

							return (-1);
						}
					}
				} else {
					if (strstr($name, "_STR") !== false) {
						// строковое значение
						// HE-3128 Проверка IP при занесении в параметры УЦН
						if (strcmp($name, "IP_STR") == 0) {
							if ($value != "") {
								if (($v_ip = filter_var($value, FILTER_VALIDATE_IP)) === false) {
									$err .= " ошибка, некорректное значение ($value) для параметра $name. ";

									return (-1);
								} else {
									if (($v_cnt = $this->getIpStrFromSpParams($value)) > 0) {
										$err .= " ошибка, попытка занесения дубля значения ($value) для параметра $name. ";

										return (-1);
									}
								}
							}
						}


					} else {
						$err = "неизвестный тип $name";
					}
				}
			}
		}

		return (0);
	}

	/**
	 * получить количество дублей IP из параметров НП
	 *
	 * @param $ip_str
	 *
	 * @return mixed
	 */
	public function getIpStrFromSpParams($ip_str)
	{
		/* временный FIX HE-3128 */

		return 0;
		/* временный FIX HE-3128 */
		$query = "
        select 
        count(IP_STR) as CNT
        from FOO_GIS.RTK_CITYPARAMS 
        where IP_STR is not null 
        and REGEXP_LIKE(IP_STR, 
        '^(([0-9]{1}|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}([0-9]{1}|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])$')
        and ARTX_PROJ.STR2IP(IP_STR) = ARTX_PROJ.STR2IP(:ip_str)";
		$param = [ "ip_str" => $ip_str ];

		return $this->db->queryArray($query, $param)[ 0 ][ "CNT" ];
	}

	/*
    *
    */
	public function renewSPparams(&$data)
	{
		$key = "_param_";
		$param = [];
		$txt = "";
		$insnames = "";
		$insvals = "";
		$i = 0;
		$valcnt = 0; // счетчик непустых полей
		$id = -1; // идентификатор населенного пункта
		$data[ "_param_AU_ID" ] = $this->perm->UserID;
		$data[ "error" ] = "";
		$ao_id = $data[ "_param_ID" ];
		$doblock = false;

		while (list($k, $v) = each($data)) {
			if (strncmp($k, $key, strlen($key)) != 0) {
				continue;
			}
			$k = substr($k, strlen($key));
			if (strcmp($k, "ID") == 0) {
				$param[ $k ] = $v;
				$id = $v;
				continue;
			}
			if ($this->isLockAO($ao_id, $k) == 1) {
				$data[ "error" ] .= " Предупреждение: параметр ($k) заблокирован в регионе. ";
				$doblock = true;
				continue;
			}
			if ($v == "" || $v == 0) {
				// попытка обнулить параметр
				if ($this->checkSPaddr($ao_id, $k) != 0) {
					// у параметра есть адрес в облаке или кластере - обнулять нельзя
					$data[ "error" ] .= " Ошибка, нельзя обнулить параметр ($k) с адресом в облаке. ";

					return (-1);
				}
			}
			if ($this->checkSPparamsType($k, $v, $err) != 0) {
				$data[ "error" ] = $err;

				return (-1);
			}
			if ($i == 0) {
				$i = 1;
			} else {
				$txt .= " , ";
			}
			$insnames .= ",$k";
			if (mb_strlen($v) == 0) {
				// поле пустое
				$txt .= " $k = null";
				$insvals .= ",null";
			} else {
				// поле непустое
				$valcnt++;
				// проверить на тип _DATE
				if (strstr($k, "_DATE") !== false) {
					// значение MM.YYYY или DD.MM.YYYY, уже проверено в checkSPparamsType
					$arr = explode(".", $v);
					if (count($arr) == 3) {
						// DD.MM.YYYY
						$txt .= " $k = to_date(:$k,'DD.MM.YYYY') ";
						$insvals .= ",to_date(:$k,'DD.MM.YYYY') ";
					} else {
						// MM.YYYY
						$txt .= " $k = to_date(:$k,'MM.YYYY') ";
						$insvals .= ",to_date(:$k,'MM.YYYY') ";
					}
				} else {
					$txt .= " $k = :$k";
					$insvals .= ",:$k";
				}
				$param[ $k ] = $v;
			}

		}
		if ($valcnt == 0) {
			if ($doblock) {
				return (-1);
			}
			// все поля пустые - удаляем запись с параметрами
			if ($id == -1) {
				$data[ "error" ] = "Ошибка: Не найден идентификатор населенного пункта";

				return (-1);
			}
			$query = "delete from FOO_GIS.RTK_CITYPARAMS where ID = :1";
			$param = [ "1" => $id ];
			$this->db->query($query, $param);
			$this->db->commit();
			$data[ "ok" ] = "Параметры населенного пункта успешно удалены";
			if ($data[ "error" ] == "") {
				unset($data[ "error" ]);
			}

			return (0);
		}
		$query = "
            MERGE INTO FOO_GIS.RTK_CITYPARAMS rc
                    USING (SELECT 1 FROM DUAL) sd
                    ON (rc.ID = :ID)
                    WHEN MATCHED THEN
                        UPDATE SET
                        $txt
                    WHEN NOT MATCHED THEN
                        INSERT (ID $insnames)
                         VALUES (:ID $insvals)";

		$this->db->query($query, $param);
		$this->db->commit();

		/*echo "<br>($query)<br>";
        echo "<pre>";
        print_r($param);
        echo "</pre>";*/
		$data[ "ok" ] = "Изменения успешно сохранены";
		if ($data[ "error" ] == "") {
			unset($data[ "error" ]);
		}

		return (0);
	}

	/*
     *
     */
	public function updSPparams(&$data)
	{
		$key = "_param_";
		$param = [];
		$txt = "";
		$i = 0;
		$data[ "_param_AU_ID" ] = $this->perm->UserID;
		$ao_id = $data[ "_param_ID" ];
		$data[ "error" ] = "";
		while (list($k, $v) = each($data)) {
			if (strncmp($k, $key, strlen($key)) != 0) {
				continue;
			}
			$k = substr($k, strlen($key));
			if (strcmp($k, "ID") == 0) {
				$param[ $k ] = $v;
				continue;
			}
			if ($this->isLockAO($ao_id, $k) == 1) {
				$data[ "error" ] .= " Предупреждение: параметр ($k) заблокирован в регионе. ";
				continue;
			}
			if ($v == "" || $v == 0) {
				// попытка обнулить параметр
				if ($this->checkSPaddr($ao_id, $k) != 0) {
					// у параметра есть адрес в облаке или кластере - обнулять нельзя
					$data[ "error" ] = " ошибка, нельзя обнулить параметр ($k) с адресом в облаке ";

					return (-1);
				}
			}
			if ($this->checkSPparamsType($k, $v, $err) != 0) {
				$data[ "error" ] = $err;

				return (-1);
			}
			if ($i == 0) {
				$i = 1;
			} else {
				$txt .= " , ";
			}
			if (mb_strlen($v) == 0) {
				$txt .= " $k = null";
			} else {
				if (strstr($k, "_DATE") !== false) {
					// значение MM.YYYY или DD.MM.YYYY, уже проверено в checkSPparamsType
					$arr = explode(".", $v);
					if (count($arr) == 3) {
						// DD.MM.YYYY
						$txt .= " $k = to_date(:$k,'DD.MM.YYYY') ";

					} else {
						// MM.YYYY
						$txt .= " $k = to_date(:$k,'MM.YYYY') ";

					}
				} else {
					$txt .= " $k = :$k";
				}
				$param[ $k ] = $v;
			}

		}
		$query = "update FOO_GIS.RTK_CITYPARAMS set $txt where ID = :ID";
		$this->db->query($query, $param);
		$this->db->commit();

		//echo "<br>($query)<br>";
		//echo "<pre>";
		// print_r($param);
		//echo "</pre>";
		$data[ "ok" ] = "Изменения успешно сохранены";
		if ($data[ "error" ] == "") {
			unset($data[ "error" ]);
		}

		return (0);
	}

	/**
	 * Получить список регионов СЛТУ
	 *
	 * @return array|int
	 */
	public function getSLTYregions()
	{
		$query = "SELECT ao.ID, ao.NAME || ' (' || ao.ABBR || ')' as NAME FROM FOO_GIS.SLTY_OBJECT ao,
          FOO_GIS.ATD_OBJ_TYPE aot
          where ao.SOURCE_ID = :1 and
          ao.PAR_ID is null and
          nvl(AO.ATDOBJ_TYPE,0) = AOT.CODE(+)
          ORDER BY ao.NAME";
		//$query = "SELECT ID, NAME as NAME FROM FOO_GIS.SLTY_OBJECT
		//      WHERE PAR_ID is null ORDER BY 2";
		$param = [ "1" => $this->source_id ];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	/**
	 * Получить список населенных пунктов СЛТУ
	 *
	 * @return array|int
	 */
	public function getSLTYCities($ao_id)
	{
		$query = "SELECT ID, sys_connect_by_path(NAME || ' (' || ABBR || ')', '|') as NAME FROM FOO_GIS.SLTY_OBJECT
              WHERE SOURCE_ID = :2 AND id in (select distinct obj_id from FOO_GIS.SLTY_STREET where SOURCE_ID = :2)
              connect by prior id = par_id start with id = :1";
		$param = [
			"1" => $ao_id,
			"2" => $this->source_id,
		];
		//echo "<br>query=($query)<br>";
		//echo "<pre>";
		//print_r($param);
		//cho "</pre>";
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	/**
	 * Получить список регионов КЛАДР
	 *
	 * @return array|int
	 */
	public function getKLADRregions()
	{
		$query = "SELECT ID, NAME || ' (' || SOCR || ')' as NAME FROM FOO_GIS.KLADR_MAIN
              WHERE to_number(substr(CODE,3,11)) = 0 ORDER BY NAME";
		$param = [];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	/**
	 * Получить список населенных пунктов КЛАДР
	 *
	 * @return array|int
	 */
	public function getKLADRCities($ao_id)
	{
		$query = "SELECT km.ID, sys_connect_by_path(km.NAME || ' (' || km.SOCR || ')', '|') as NAME FROM FOO_GIS.KLADR_MAIN km
              where km.ID in (select distinct MAIN_ID from FOO_GIS.KLADR_STREET)
              connect by prior km.id = km.par_id start with km.id = :1 order by km.NAME";
		$param = [ "1" => $ao_id ];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	/**
	 * Получить список населенных пунктов КЛАДР
	 *
	 * @return array|int
	 */
	public function getKLADRbyRegion($ao_id)
	{
		$query = "SELECT (select count(1) from FOO_GIS.KLADR_STREET ks where ks.MAIN_ID = km.ID) as STREET_CNT, km.ID, km.PAR_ID, km.SOCR, km.CODE, km.GNINMB, km.UNO, km.OCATD, km.STATUS, km.STATE, sys_connect_by_path(NAME || ' (' || km.SOCR ||')', '|') as NAME,
            CONNECT_BY_ISLEAF AS ISLEAF,
            LEVEL
            FROM FOO_GIS.KLADR_MAIN km
              connect by prior km.id = km.par_id start with km.id = :1";
		$param = [ "1" => $ao_id ];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	/**
	 * Получить список улиц города КЛАДР
	 *
	 * @return array|int
	 */
	public function getKLADRStreets($city_id, $orderby = false)
	{
		$query = "select *
            FROM FOO_GIS.KLADR_STREET where MAIN_ID = :1";
		if ($orderby) {
			$query .= " order by NAME";
		}
		$param = [ "1" => $city_id ];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	public function getKLADRStreetsWithNoStreets($city_id, $orderby = false)
	{
		$query = "select *
            FROM FOO_GIS.KLADR_STREET where MAIN_ID = :1";
		if ($orderby) {
			$query .= " order by NAME";
		}
		$param = [ "1" => $city_id ];
		$a = $this->db->queryArray($query, $param);
		if (!is_array($a)) {
			$a = [];
		}
		$a[] = $this->nostreet;

		return $a;
	}

	/**
	 * Получить список населенных пунктов с параметрами ЦН
	 *
	 * @return array|int
	 */
	public function getDNECities($ao_id, $loaded = true)
	{
		if (!$loaded) {
			$loadedtxt = " not ";
		} else {
			$loadedtxt = "";
		}
		$query = "SELECT ao.ID, sys_connect_by_path(ao.NAME || ' (' || aot.NAME || ')', '|') as NAME FROM FOO_GIS.ATD_OBJECT ao,
          FOO_GIS.ATD_OBJ_TYPE aot
          where  ao.ID $loadedtxt in (select distinct id from FOO_GIS.RTK_CITYPARAMS) and
          nvl(AO.ATDOBJ_TYPE,0) = AOT.CODE(+)
          connect by prior id = par_id start with id = :1
          ORDER BY ao.NAME";
		$param = [ "1" => $ao_id ];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	/**
	 * Получить список адресных систем
	 *
	 * @return array|int
	 */
	public function getSLTYsources()
	{
		$query = "select * from FOO_GIS.SOURCE_ADDRESS";
		$param = [];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}


	/**
	 * Выбрать улицу по имени и типу
	 *
	 * @return array|int
	 */
	public
	function getSPobjectByID(
		$ao_id
	) {
		//echo "<br>getSPHouseByNum: ao_id=$ao_id<br>";
		$query = "select NAME from FOO_GIS.ATD_OBJECT where ID = :1";
		$param = [ "1" => $ao_id ];
		$a = $this->db->queryArray($query, $param);
		// echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		return ($a);
	}

	/**
	 * Удалить дом по идентификатору
	 *
	 * @return array|int
	 */
	public
	function delSPHouseByID(
		$addr_id
	) {
		//echo "<br>getSPHouseByNum: ao_id=$ao_id, street_id=$street_id, num=$num<br>";
		$query = "delete from  FOO_GIS.ATD_ADDRESS where ADDR_ID = :1";
		$param = [
			"1" => $addr_id,
		];
		$a = $this->db->query($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		return;
	}

	/**
	 * Выбрать дом по идентификатору
	 *
	 * @return array|int
	 */
	public
	function getSPHouseByID(
		$addr_id
	) {
		//echo "<br>getSPHouseByNum: ao_id=$ao_id, street_id=$street_id, num=$num<br>";
		$query = "select * from  FOO_GIS.ATD_ADDRESS where ADDR_ID = :1";
		$param = [
			"1" => $addr_id,
		];
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		return ($a);
	}

	/**
	 * Выбрать дом УЦН по населенному пункту, улице и номеру
	 * выборка урезана, т.к. такой дом будет один на уникальной улице "ТД_<название населенного пункта>"
	 *
	 * @return array|int
	 */
	public
	function getSPHouseByNum(
		$ao_id,
		$street_id,
		$num
	) {
		//echo "<br>getSPHouseByNum: ao_id=$ao_id, street_id=$street_id, num=$num<br>";
		$query = "select * from  FOO_GIS.ATD_ADDRESS
        where
        OBJ_ID = :1 AND
        STR_STD1 = :2 AND
        NUMBER1 = :3";
		$param = [
			"1" => $ao_id,
			"2" => $street_id,
			"3" => $num,
		];
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		return ($a);
	}

	/**
	 * Получить список домов на улице в НП
	 *
	 * @param $ao_id
	 * @param $street_id
	 *
	 * @return array|int
	 *
	 *
	 */
	public
	function getSPHousesByStreetID(
		$ao_id,
		$street_id
	) {
		$query = "select * from  FOO_GIS.ATD_ADDRESS
        where
        OBJ_ID = :1 AND
        STR_STD1 = :2";
		$param = [
			"1" => $ao_id,
			"2" => $street_id,
		];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/**
	 * Создать дом по населенному пункту, улице и номеру
	 *
	 * @return array|int
	 */
	public
	function createSPHouseByNum(
		$ao_id,
		$street_id,
		$num
	) {
		//echo "<br>createSPHouseByNum: ao_id=$ao_id, street_id=$street_id, num=$num<br>";
		$query = "insert into FOO_GIS.ATD_ADDRESS(ADDR_ID, OBJ_ID, STR_STD1, NUMBER1)
          values (FOO_GIS.ATD_ADDRESS_SEQ.nextval,:1, :2, :3)";
		$param = [
			"1" => $ao_id,
			"2" => $street_id,
			"3" => $num,
		];
		$a = $this->db->query($query, $param);

		return;
	}

	/**
	 * Получить дом РТК по его идентификатору
	 *
	 * @return array
	 */
	public
	function getRTKByID(
		$addr_id
	) {
		//echo "<br>createSPHouseByNum: ao_id=$ao_id, street_id=$street_id, num=$num<br>";
		$query = "select * from  FOO_GIS.RTK_ADDRESS where ADDR_ID = :1";
		$param = [
			"1" => $addr_id,
		];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/**
	 * Создать дом РТК
	 *
	 * @return array|int
	 */
	public
	function createRTKByID(
		$addr_id,
		$type_odf = "ТД"
	) {
		//echo "<br>createSPHouseByNum: ao_id=$ao_id, street_id=$street_id, num=$num<br>";
		$query = "insert into FOO_GIS.RTK_ADDRESS(ADDR_ID, CONN_TECHNOLOGY, TYPE_ODF)
          values (:1, :2, :2)";
		$param = [
			"1" => $addr_id,
			"2" => $type_odf,
		];
		$a = $this->db->query($query, $param);

		return;
	}

	/**
	 * Удалить улицу по идентификатору
	 *
	 * @return array|int
	 */
	public
	function delSPstreetByID(
		$street_id
	) {
		//echo "<br>getSPstreetByName: ao_id=$ao_id, street_name=$street_name, street_type=$street_type<br>";
		$query = "delete from  FOO_GIS.ATD_STREET where ID = :1";

		$param = [
			"1" => $street_id,
		];
		$a = $this->db->query($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		return;
	}

	/**
	 * Получить улицу по идентификатору
	 *
	 * @param $street_id
	 *
	 */
	public
	function getSPstreetByID(
		$street_id
	) {
		$query = "select ast.ID, ast.NAME, ast.ATDOBJ_TYPE, aot.ABBR from FOO_GIS.ATD_STREET ast, FOO_GIS.ATD_OBJ_TYPE aot where
              ast.ID = :street_id and ast.ATDOBJ_TYPE = aot.CODE";


		$param = [
			"street_id" => $street_id,
		];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	public
	function getSPstreetByCityID(
		$ao_id = null
	) {
		$a = [];
		if ($ao_id) {
			$query = "select ID, FOO_GIS.GET_STREET(ID) as NAME from FOO_GIS.ATD_STREET where OBJ_ID = :ao_id";
			$param = [
				"ao_id" => $ao_id,
			];
			$a = $this->db->queryArray($query, $param);
		}

		return ($a);
	}

	/**
	 * Сохранить изменения в улице Сп
	 *
	 * @param $street_id
	 * @param $name
	 * @param $type
	 *
	 * @return array|int
	 *
	 */
	public
	function updSPstreet(
		$street_id,
		$name,
		$type
	) {
		$query = "update FOO_GIS.ATD_STREET set NAME = :name, ATDOBJ_TYPE = :type where ID = :street_id";


		$param = [
			"street_id" => $street_id,
			"name" => $name,
			"type" => $type,
		];
		$this->db->query($query, $param);
		$this->db->commit();

		return;
	}

	/**
	 * Выбрать улицу по населенному пункту, имени и типу
	 *
	 * @return array|int
	 */
	public
	function getSPstreetByName(
		$ao_id,
		$street_name,
		$street_type
	) {
		//echo "<br>getSPstreetByName: ao_id=$ao_id, street_name=$street_name, street_type=$street_type<br>";
		$query = "select * from  FOO_GIS.ATD_STREET st, FOO_GIS.ATD_OBJ_TYPE aot
        where
        st.OBJ_ID = :1 AND
        st.NAME = :2 AND
        aot.ABBR = :3 AND
        st.ATDOBJ_TYPE = aot.CODE";
		$param = [
			"1" => $ao_id,
			"2" => $street_name,
			"3" => $street_type,
		];
		$a = $this->db->queryArray($query, $param);
		//echo "<pre>";
		//print_r($a);
		//echo "</pre>";
		return ($a);
	}

	/**
	 * Создать улицу по населенному пункту, имени и типу
	 *
	 * @return array|int
	 */
	public
	function createSPstreetByName(
		$ao_id,
		$street_name,
		$street_type
	) {
		//echo "<br>createSPstreetByName: ao_id=$ao_id, street_name=$street_name, street_type=$street_type<br>";
		$query = "insert into FOO_GIS.ATD_STREET (ID, OBJ_ID, NAME, FULLNAME, ATDOBJ_TYPE)
          values (FOO_GIS.ATD_STREET_SEQ.nextval,:1, :2, :2, (select CODE from FOO_GIS.ATD_OBJ_TYPE where ABBR = :3))";
		$param = [
			"1" => $ao_id,
			"2" => $street_name,
			"3" => $street_type,
		];
		$this->db->query($query, $param);

		return;
	}


	/**
	 * Обработать массив населенных пунктов для создания ТД
	 */
	public function createDNEelements($addr, $odf_type)
	{
		$res = [ "all" => 0, "exist" => 0, "house" => 0 ];
		$a = [];
		$td = $this->getRtkTdType($odf_type);
		// цикл добавления ТД
		foreach ($addr as $k => $v) {
			// пропустить элементы без чекбокса
			if (strcmp($v, "on") != 0) {
				continue;
			}
			// создать-проверить элемент ТД
			$addr_id = $this->createDNEel($k, $td, $res);
			if ($addr_id != 0) {
				$a[ $addr_id ] = "on";
			}
		}
		//echo "<pre>";
		//print_r($res);
		//echo "</pre>";
		return ($a);
	}

	/**
	 * Обработать массив населенных пунктов для создания ТД
	 */
	public function createDNEelements2($params)
	{
		$a = [];
		$res = [ "all" => 0, "exist" => 0, "house" => 0 ];

		if (count($params) > 0) {
			$addr = $params[ "data" ];
			$td = $params[ "td" ];

			// цикл добавления ТД
			for ($i = 0; $i < count($addr); $i++) {

				// создать-проверить элемент ТД
				//if($i == 10) break;
				$a[ $i ][ "addr_id" ] = $this->createDNEel($addr[ $i ][ "ID" ], $td, $res);
			}

			$this->db->commit();
			$res[ "exist" ] = $res[ "all" ] - $res[ "house" ];
		}

		return ($res);
	}

	/**
	 * Обработать массив населенных пунктов для удаления ТД
	 */
	public function delDNEelements($addr)
	{
		$a = [];

		foreach ($addr as $k => $v) {
			// пропустить элементы без чекбокса
			if (strcmp($v, "on") != 0) {
				continue;
			}
			// удалить элемент ТД
			$a = $this->getRtkTDAddr($k);
			if (count($a) != 1) {
				echo "<br>Адрес не в ЦН<br>";
				continue;
			}
			$addr_id = $this->delDNEel($k);
		}
		unset($a);
		$this->db->commit();

		return ($a);
	}

	/**
	 * Создать элемент ЦН в населенном пункте
	 *
	 * @return array|int
	 */
	public
	function createDNEel(
		$ao_id,
		$td,
		&$res = null
	) {
		// получить название населенного пункта
		$a = $this->getSPobjectByID($ao_id);
		$city_name = $a[ 0 ][ "NAME" ];
		if (isset($td[ "STREET_PREFIX" ])) {
			$prefix = $td[ "STREET_PREFIX" ];
		} else {
			$prefix = "ТД_";
		}
		// название и тип улицы ТД
		$street_name = $prefix . $city_name;
		$street_type = "ул";
		// получить улицу
		$a = $this->getSPstreetByName($ao_id, $street_name, $street_type);
		if (count($a) == 0) {
			// такой улицы еще нет, создаем
			$this->createSPstreetByName($ao_id, $street_name, $street_type);
			$a = $this->getSPstreetByName($ao_id, $street_name, $street_type);
			if (isset($res)) {
				$res[ "street" ] += 1;
			}
		}
		$street_id = $a[ 0 ][ "ID" ];
		// получить дом
		$num = $td[ "HOUSE_NUM" ];
		$a = $this->getSPHouseByNum($ao_id, $street_id, $num);
		if (count($a) == 0) {
			if (\FOO\FeatureToggle\Features::isEnabled('Address::HE-4408')) {
				$odf_type = $td[ "ODF_TYPE" ] ?? "ТД";
				if ($odf_type == "ТД") { // HE-4408 - запрет на создание новых ТД в системе
					return 0;
				}
			}
			// такого дома нет, создаем
			$this->createSPHouseByNum($ao_id, $street_id, $num);
			$a = $this->getSPHouseByNum($ao_id, $street_id, $num);
			if (isset($res)) {
				$res[ "house" ] += 1;
			}
		}
		$addr_id = $a[ 0 ][ "ADDR_ID" ];
		$a = $this->getRTKByID($addr_id);
		if (count($a) == 0) {
			// такого дома нет, создаем
			$this->createRTKByID($addr_id, $td[ "ODF_TYPE" ]);
			if (isset($res)) {
				$res[ "rtk" ] += 1;
			}
		}
		// обновить параметр адреса ТД населенного пункта
		$this->insRtkTDAddr($ao_id, $addr_id, $td[ "ODF_TYPE" ]);
		if (isset($res)) {
			$res[ "all" ] += 1;
		}

		return ($addr_id);
	}

	/**
	 * Добавить-обновить адрес ТД
	 *
	 * @param $ao_id
	 * @param $addr_id
	 * @param $type_odf
	 *
	 */
	public function insRtkTDAddr($ao_id, $addr_id, $odf_type)
	{
		$query = "
            MERGE INTO FOO_GIS.RTK_TD_ADDRESS ta
                    USING (SELECT 1 FROM DUAL) sd
                    ON (ta.ID = :ao_id and ta.ADDR_ID = :addr_id)
                    WHEN MATCHED THEN
                        UPDATE SET
                        ta.ODF_TYPE =:odf_type
                    WHEN NOT MATCHED THEN
                        INSERT (ID, ADDR_ID, ODF_TYPE)
                         VALUES (:ao_id, :addr_id, :odf_type)";
		$param = [ "ao_id" => $ao_id, "addr_id" => $addr_id, "odf_type" => $odf_type ];
		$this->db->query($query, $param);
	}

	/**
	 * Удалить элемент ЦН в населенном пункте
	 *
	 * @return array|int
	 */
	public function delDNEel($addr_id)
	{
		// получить название населенного пункта
		$a = $this->getSPHouseByID($addr_id);
		$city_id = $a[ 0 ][ "OBJ_ID" ];
		$street_id = $a[ 0 ][ "STR_STD1" ];
		$this->delSPHouseByID($addr_id);
		$this->delRtkTDAddr($addr_id);
		// получить список домов на улице
		$b = $this->getSPHousesByStreetID(
			$city_id,
			$street_id
		);
		if (count($b) == 0) {
			// если домов больше нет - удалить улицу
			$this->delSPstreetByID($street_id);
		}

		return ($addr_id);
	}

	/**
	 * Удалить ТД
	 *
	 * @param $addr_id
	 *
	 */
	public function delRtkTDAddr($addr_id)
	{
		$query = "delete from FOO_GIS.RTK_TD_ADDRESS where ADDR_ID = :addr_id";
		$param = [ "addr_id" => $addr_id ];
		$this->db->query($query, $param);
	}

	/**
	 * Получить ТД
	 *
	 * @param $addr_id
	 *
	 * @return array|int
	 *
	 */
	public function getRtkTDAddr($addr_id)
	{
		$query = "select * from FOO_GIS.RTK_TD_ADDRESS where ADDR_ID = :addr_id";
		$param = [ "addr_id" => $addr_id ];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/**
	 * Получить список точек доступа
	 *
	 * @return array|int
	 *
	 */
	public function getRtkTDAddresses()
	{
		$query = "select FOO_GIS.FULLNAME_OBJECT(rta.ID) as NAME, rta.ID, rta.ADDR_ID, rta.ODF_TYPE
          from FOO_GIS.RTK_TD_ADDRESS rta
          where FOO_GIS.CHECKCITY(:1, rta.ID) = 1 order by NAME";
		$param = [ "1" => $this->perm->UserID ];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/**
	 * Получить ТД по НП и типу
	 *
	 * @param $ao_id
	 * @param $odf_type
	 *
	 * @return array|int
	 *
	 *
	 */
	public function getRtkTDbyAOandType($ao_id, $odf_type)
	{
		$query = "select
          FOO_GIS.GET_ADDRESS(tda.ADDR_ID,1) as ADDRESS,
          tda.ADDR_ID,
          tda.ODF_TYPE
          from FOO_GIS.RTK_TD_ADDRESS tda
          where
           tda.ID = :ao_id AND
           tda.ODF_TYPE = :odf_type";
		$param = [ "ao_id" => $ao_id, "odf_type" => $odf_type ];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/**
	 * Получить список ТД по проекту
	 *
	 * @param $project_id
	 */
	public function getODFListbyProject($project_id)
	{
		$query = "select ot.ODF_TYPE, ot.ODF_TYPEDESC
        from  FOO.CLUSTER_PROJECTS_ODF po,
        FOO_GIS.ATD_ODF_TYPE ot
        where
        po.PROJECT_ID = :project_id AND
        ot.ODF_TYPE = po.ODF_TYPE
         order by 1";
		$param = [
			"project_id" => $project_id,

		];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/**
	 * Получить тип ТД по типу ODF
	 *
	 * @param $odf_type
	 *
	 * @return array|int
	 *
	 */
	public function getRtkTdType($odf_type)
	{
		$query = "select *
        from  FOO_GIS.RTK_TD_TYPES
        where ODF_TYPE =:odf_type";
		$param = [
			"odf_type" => $odf_type,

		];
		$a = $this->db->queryArray($query, $param);

		return ($a[ 0 ]);
	}

	/**
	 * Получить тип ТД по названию параметра
	 *
	 * @param $param_name
	 *
	 * @return array|int
	 *
	 */
	public function getRtkTdTypeByName($param_name)
	{
		$query = "select *
        from  FOO_GIS.RTK_TD_TYPES
        where PARAM_NAME =:param_name";
		$param = [
			"param_name" => $param_name,

		];
		$a = $this->db->queryArray($query, $param);

		return ($a[ 0 ]);
	}

	/**
	 * Получить список типов ТД
	 *
	 * @return array|int
	 *
	 */
	public function getRtkTdTypes()
	{
		$query = "select *
        from  FOO_GIS.RTK_TD_TYPES";
		$param = [
		];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/**
	 * Получить список параметров населенных пунктов по конкретному типу ТД
	 *
	 * @param $odf_type
	 *
	 */
	public function getSPparamsByTDtype($odf_type)
	{
		$result = [];
		$td = $this->getRtkTdType($odf_type);
		if (count($td) > 0) {
			$p = explode('_', $td[ "PARAM_NAME" ]);
			$sql = "SELECT OBJ_ID as ID FROM FOO_GIS.CITY_PARAM_VALUES WHERE PARAM_ID=:1 AND PARAM_VALUE<>'0'";
			$args = [ "1" => $p[ 0 ] ];
			$a = $this->db->queryArray($sql, $args);
			$result[ "data" ] = $a;
			$result[ "td" ] = $td;
		}

		return ($result);
	}

	/** Удалить историю параметра НП
	 *
	 * @param $ao_id
	 * @param $param_name
	 *
	 */
	public function delSPparamHistory($ao_id, $param_name)
	{
		if ($this->mb_strcmp($param_name, "ALL") == 0) {
			$columns = $this->getTableColumns("FOO_GIS.RTK_CITYPARAMS");

			for ($i = 0; $i < count($columns); $i++) {
				if (strcmp($columns[ $i ][ "NAME" ], "ID") == 0) {
					continue;
				}
				if (strcmp($columns[ $i ][ "NAME" ], "AU_ID") == 0) {
					continue;
				}
				if ($this->isLockAO($ao_id, $columns[ $i ][ "NAME" ]) == 1) {
					return "<br> ошибка, параметр " . $columns[ $i ][ "NAME" ] . " заблокирован в регионе<br>";
				}
			}
			$query = "delete from FOO_GIS.RTK_CITYPARAMS_HISTORY where ID = :ao_id";
		} else {
			if ($this->isLockAO($ao_id, $param_name) == 1) {
				return "<br> ошибка, параметр " . $param_name . " заблокирован в регионе<br>";
			}
			$query = "update FOO_GIS.RTK_CITYPARAMS_HISTORY
        set $param_name =
        (select distinct $param_name from FOO_GIS.RTK_CITYPARAMS_HISTORY
        where ID = :ao_id and HISTORY_DATE =
        (select min(HISTORY_DATE) from FOO_GIS.RTK_CITYPARAMS_HISTORY where ID = :ao_id))
        where ID = :ao_id";
		}
		$param = [ "ao_id" => $ao_id ];

		$this->db->query($query, $param);
		$this->db->commit();

	}

	/**
	 * Проверить адресный объект и его статус у параметра
	 *
	 * @param $ao_id
	 * @param $param_name
	 *
	 * @return int
	 *
	 */
	public function checkSPaddr($ao_id, $param_name)
	{
		//echo "<br>checkSPaddr($ao_id, $param_name)<br>";
		$found = false;
		// получить тип ТД для параметра
		$tdtype = $this->getRtkTdTypeByName($param_name);

		if (count($tdtype) == 0) {
			// для данного параметра не предполагается создание адреса
			return (0);
		}
		// Получить адрес объекта ТД по НП и типу параметра
		$odf_type = $tdtype[ "ODF_TYPE" ];
		$addr = $this->getRtkTDbyAOandType($ao_id, $odf_type);

		if (count($addr) == 0) {
			// для данного параметра еще не создано адреса
			return (0);
		}
		// проверить адрес в облаке
		$addr_id = $addr[ 0 ][ "ADDR_ID" ];
		if (!$this->isAddressInCloud($addr_id)) {
			//адресный объект не добавлен в облако
			return (0);
		}

		// адрес уже в облаке или кластере
		return (1);
	}

	public function isAddressInCloud($addrId)
	{
		$query = "SELECT c.ADDR_ID
              FROM FOO.CLOUDS_ADDRESS c
              WHERE c.ADDR_ID = :1";

		$param = [ "1" => $addrId ];
		$a = $this->db->queryArray($query, $param);

		return (0 !== count($a));
	}

	/**
	 *
	 * Получить историю изменений параметра $param_name в НП региона $ao_id за период с $dt_from по $dt_to
	 *
	 * @param $ao_id
	 * @param $param_name
	 * @param $dt_from
	 * @param $dt_to
	 *
	 * @return array|int
	 *
	 *
	 */
	public function getSPhistoryOld($ao_id, $param_name, $dt_from, $dt_to)
	{
		$err = "";
		$res = [];
		if ($this->checkSPparamsType("FROM_DATE", $dt_from, $err) != 0) {
			$res[ "error" ] = $err;

			return ($res);
		}
		if ($this->checkSPparamsType("TO_DATE", $dt_to, $err) != 0) {
			$res[ "error" ] = $err;

			return ($res);
		}
		$query = "
          select AU_ID, FOO_AUTH.GETUSERNAME(AU_ID) as USERNAME,ID, FOO_GIS.FULLNAME_OBJECT(ID) as ADDRESS, to_char(HISTORY_DATE, 'DD.MM.YYYY HH24:MI:SS') as HISTORY_DATE, HISTORY_TYPE, PREV_VALUE, CUR_VALUE from
          (select AU_ID, ID, HISTORY_TYPE, HISTORY_DATE, CUR_VALUE,
          lag(CUR_VALUE) over (partition by ID order by HISTORY_DATE,HISTORY_ID) as PREV_VALUE,
          instr(nvl(to_char(CUR_VALUE),'zero'),nvl(to_char(lag(CUR_VALUE) over (partition by ID order by HISTORY_DATE,HISTORY_ID)),'zero')) as IS_EQ
          from
          (select rch.AU_ID, rch.HISTORY_ID, rch.HISTORY_TYPE, rch.HISTORY_DATE, rch.ID, rch.$param_name as CUR_VALUE,
          row_number() over ( partition by rch.ID, rch.$param_name order by HISTORY_DATE,HISTORY_ID) as row_num
          from (select ID from FOO_GIS.ATD_OBJECT
          connect by prior ID = PAR_ID start with ID = :ao_id) ao, FOO_GIS.RTK_CITYPARAMS_HISTORY rch
          where rch.ID = ao.ID and
          rch.HISTORY_DATE <= (to_date(:dt_to,'DD.MM.YYYY')+1)
          order by rch.ID, rch.HISTORY_DATE, rch.HISTORY_ID) BBB
          where row_num = 1 order by ID, HISTORY_DATE,HISTORY_ID) CCC
          where IS_EQ != 1 and
          HISTORY_DATE BETWEEN to_date(:dt_from,'DD.MM.YYYY') and (to_date(:dt_to,'DD.MM.YYYY')+1)";
		$param = [
			"ao_id" => $ao_id,
			"dt_from" => $dt_from,
			"dt_to" => $dt_to,
		];
		$res[ "report" ] = $this->db->queryArray($query, $param);

		return ($res);
	}

	public function getSPhistory($ao_id, $param_name, $dt_from, $dt_to)
	{
		$err = "";
		$res = [];
		if ($this->checkSPparamsType("FROM_DATE", $dt_from, $err) != 0) {
			$res[ "error" ] = $err;

			return ($res);
		}
		if ($this->checkSPparamsType("TO_DATE", $dt_to, $err) != 0) {
			$res[ "error" ] = $err;

			return ($res);
		}
		$query = "
          select
          rch.AU_ID,
          FOO_AUTH.GETUSERNAME(rch.AU_ID) as USERNAME,
          rch.ID,
          FOO_GIS.FULLNAME_OBJECT(rch.ID) as ADDRESS,
          to_char(rch.HISTORY_DATE, 'DD.MM.YYYY HH24:MI:SS') as HISTORY_DATE,
          rch.HISTORY_TYPE,
          nvl(rch.$param_name,0) as CUR_VALUE
          from
          FOO_GIS.ATD_OBJECT ao,
          FOO_GIS.RTK_CITYPARAMS_HISTORY rch
          where
          rch.ID (+)= ao.ID and
          rch.ID is not null and
          rch.HISTORY_DATE BETWEEN to_date(:dt_from,'DD.MM.YYYY') and (to_date(:dt_to,'DD.MM.YYYY')+1)
          connect by prior ao.ID = ao.PAR_ID start with ao.ID = :ao_id
          order by rch.ID, rch.HISTORY_DATE, rch.HISTORY_ID
          ";
		$param = [
			"ao_id" => $ao_id,
			"dt_from" => $dt_from,
			"dt_to" => $dt_to,
		];
		$a = $this->db->queryArray($query, $param);
		$rep = [];
		for ($i = 0, $j = 0, $cur_id = -1, $prev_val = "не определено"; $i < count($a); $i++) {
			if ($cur_id < 0) {//начало
				$cur_id = $a[ $i ][ "ID" ];
			} else {
				if ($cur_id != $a[ $i ][ "ID" ]) {//сменился населенный пункт
					$cur_id = $a[ $i ][ "ID" ];
					$prev_val = "не определено";//сбросить параметр
				}
			}
			if ($prev_val != $a[ $i ][ "CUR_VALUE" ]) {//параметр изменился, выводим
				$rep[ $j ][ "ID" ] = $a[ $i ][ "ID" ];
				$rep[ $j ][ "USERNAME" ] = $a[ $i ][ "USERNAME" ];
				$rep[ $j ][ "ADDRESS" ] = $a[ $i ][ "ADDRESS" ];
				$rep[ $j ][ "HISTORY_DATE" ] = $a[ $i ][ "HISTORY_DATE" ];
				$rep[ $j ][ "HISTORY_TYPE" ] = $a[ $i ][ "HISTORY_TYPE" ];
				$rep[ $j ][ "PREV_VALUE" ] = $prev_val;
				$rep[ $j ][ "CUR_VALUE" ] = $a[ $i ][ "CUR_VALUE" ];
				$prev_val = $a[ $i ][ "CUR_VALUE" ];
				$j++;
			}
		}
		$res[ "report" ] = $rep;

		return ($res);
	}

	/**
	 * Получить улицу СП по идентификатору города СП и улицы КЛАДР
	 *
	 * @param $kladr_street_id
	 *
	 */
	public function getStreetByKladrId($city_id, $kladr_street_id)
	{

		$query = "select st.* from
        FOO_GIS.ATD_STREET st,
        FOO_GIS.ATD_OBJ_TYPE aot,
        FOO_GIS.KLADR_STREET ks where
        ks.ID = :kladr_street_id and
        st.OBJ_ID = :city_id and
        lower(st.NAME) = lower(ks.NAME) and
        aot.ABBR = ks.SOCR and
        aot.CODE = st.ATDOBJ_TYPE";
		$param = [ "city_id" => $city_id, "kladr_street_id" => $kladr_street_id ];
		$a = $this->db->queryArray($query, $param);

		return ($a);

	}

	/**
	 * Создать улицу в городе СП из улицы КЛАДР
	 *
	 * @param $city_id
	 * @param $kladr_street_id
	 *
	 */
	public function createStreetByKladrId($city_id, $kladr_street_id)
	{

		$street_id = $this->getStreetByKladrId($city_id, $kladr_street_id)[ 0 ][ "ID" ];
		if (!isset($street_id)) {
			$query = "insert into
                      FOO_GIS.ATD_STREET (ID, OBJ_ID, NAME, FULLNAME, CODE, ATDOBJ_TYPE)

          select FOO_GIS.ATD_STREET_SEQ.nextval, :city_id, ks.NAME, ks.NAME, ks.CODE, aot.CODE from FOO_GIS.KLADR_STREET ks, FOO_GIS.ATD_OBJ_TYPE aot
          where
            ks.ID = :kladr_street_id AND
            aot.ABBR = ks.SOCR
            and round(aot.CODE/100) = 5
          ";
			$param = [
				"city_id" => $city_id,
				"kladr_street_id" => $kladr_street_id,
			];
			$this->db->query($query, $param);
			$street_id = $this->getStreetByKladrId($city_id, $kladr_street_id)[ 0 ][ "ID" ];
		}

		return ($street_id);
	}

	/**
	 * создать улицу "Без улицы" в НП
	 *
	 * @param $city_id
	 *
	 * @return mixed
	 */
	public function createNoStreet($city_id)
	{

		$query = "
        merge into FOO_GIS.ATD_STREET st
        using (select 1 from dual) sd
        on (st.OBJ_ID = :city_id and st.NAME = :name and st.ATDOBJ_TYPE = :type)
        when not matched then
        insert (OBJ_ID, NAME, FULLNAME, ATDOBJ_TYPE, CODE) values(:city_id, :name, :name, :type, :code)
        ";
		$param = [
			"city_id" => $city_id,
			"name" => $this->nostreet[ "NAME" ],
			"type" => $this->nostreet[ "AOT_CODE" ],
			"code" => $this->nostreet[ "CODE" ],
		];
		$this->db->query($query, $param);
		$query = "
        select st.ID from FOO_GIS.ATD_STREET st
        where st.OBJ_ID = :city_id and st.NAME = :name and st.ATDOBJ_TYPE = :type
        ";
		$param = [
			"city_id" => $city_id,
			"name" => $this->nostreet[ "NAME" ],
			"type" => $this->nostreet[ "AOT_CODE" ],
		];
		$a = $this->db->queryArray($query, $param);

		return $a[ 0 ][ "ID" ];
	}

	/**
	 * Создать дом на улице города по основным реквизитам
	 *
	 * @param $city_id
	 * @param $street_id
	 * @param $data
	 *
	 * метод createHousebyNum теперь работает с идентификатором СитПлан !!!
	 */
	public function createHousebyNum($city_id, $kladr_street_id, &$data, $odf_type = "ОРШЖ")
	{
		if ($kladr_street_id == $this->nostreet[ "ID" ]) {
			$street_id = $this->createNoStreet($city_id);
		} else {
			/* $street_id = $this->createStreetByKladrId($city_id, $kladr_street_id); */
			$street_id = $kladr_street_id; // не создавать улицу, так как kladr_street_id и есть street_id !!!
		}
		if (!isset($street_id)) {
			$data[ "note" ][ "error" ] = "Ошибка создания улицы";

			return;
		}
		$data[ "NEW_STREET_ID" ] = $street_id;
		$colquery = "insert into FOO_GIS.ATD_ADDRESS (ADDR_ID, OBJ_ID, STR_STD1 ";
		$valquery = " values (FOO_GIS.ATD_ADDRESS_SEQ.nextval, :city_id, :street_id ";
		$param = [ "city_id" => $city_id, "street_id" => $street_id ];
		$req =
			[ "NUMBER1", "NUMBER2", "TANK", "STRUCT", "VLADENIE", "LIT_N1", "LIT_N2", "LIT_TN", "LIT_ST", "LIT_VL" ];
		$a = $this->getHousebyNum($city_id, $street_id, $data);
		if (isset($a) && count($a) > 0) {
			$data[ "NEW_ADDR_ID" ] = $a[ 0 ][ "ADDR_ID" ];
			$data[ "note" ][ "error" ] = "Ошибка, адрес уже создан";

			return;
		}
		if (isset($data[ "note" ][ "error" ])) {
			return;
		}
		foreach ($req as $col) {
			$colquery .= ", $col ";
			if ($data[ $col ] != "") {
				$valquery .= ", :$col ";
				$param[ $col ] = $data[ $col ];
			} else {
				$valquery .= ", null ";
			}
		}
		$query = $colquery . ")" . $valquery . ")";
		$this->db->query($query, $param);
		//echo "<br>$query<br>";
		//echo "<pre>";
		//print_r($param);
		//print_r($a);
		//echo "</pre>";
		$a = $this->getHousebyNum($city_id, $street_id, $data);
		if (!isset($a) || count($a) == 0) {
			if (isset($data[ "note" ][ "error" ])) {
				return;
			}
			$data[ "note" ][ "error" ] = "Ошибка создания адреса ";

			return;
		}
		$addr_id = $a[ 0 ][ "ADDR_ID" ];
		//echo "<br>addr_id=$addr_id<br>";
		$a = $this->getRTKByID($addr_id);
		if (count($a) == 0) {
			// такого дома нет, создаем
			$this->createRTKByID($addr_id, $odf_type);
		}
		$this->db->commit();
		$data[ "NEW_ADDR_ID" ] = $addr_id;
		$data[ "note" ][ "ok" ] = "Адрес успешно создан";
	}

	/**
	 * Найти дом на улице города по основным реквизитам
	 *
	 * @param $city_id
	 * @param $street_id
	 * @param $data
	 *
	 * @return array|int|null
	 *
	 *
	 */
	public function getHousebyNum($city_id, $street_id, &$data)
	{
		$found = 0;
		$param = [ "street_id" => $street_id ];
		$query = "select * from FOO_GIS.ATD_ADDRESS where STR_STD1 = :street_id ";
		if ($data[ "NUMBER1" ] != "") {
			if (is_numeric($data[ "NUMBER1" ]) && (round(
						$data[ "NUMBER1" ]
					) == $data[ "NUMBER1" ]) && ($data[ "NUMBER1" ] > 0)) {
				$query .= "and NUMBER1 = :NUMBER1 ";
				$param[ "NUMBER1" ] = $data[ "NUMBER1" ];
				$found++;
			} else {
				$data[ "note" ][ "error" ] = "Ошибка, номер дома должен быть целым положительным числом";

				return (null);
			}
		} else {
			$query .= "and NUMBER1 is null ";
		}
		if ($data[ "NUMBER2" ] != "") {
			if (is_numeric($data[ "NUMBER2" ]) && (round(
						$data[ "NUMBER2" ]
					) == $data[ "NUMBER2" ]) && ($data[ "NUMBER2" ] > 0)) {
				$query .= "and NUMBER2 = :NUMBER2 ";
				$param[ "NUMBER2" ] = $data[ "NUMBER2" ];
				$found++;
			} else {
				$data[ "note" ][ "error" ] = "Ошибка, номер дома должен быть целым положительным числом";

				return (null);
			}
		} else {
			$query .= "and NUMBER2 is null ";
		}
		if ($data[ "TANK" ] != "") {
			if (is_numeric($data[ "TANK" ]) && (round($data[ "TANK" ]) == $data[ "TANK" ]) && ($data[ "TANK" ] > 0)) {
				$query .= "and TANK = :TANK ";
				$param[ "TANK" ] = $data[ "TANK" ];
				$found++;
			} else {
				$data[ "note" ][ "error" ] = "Ошибка, номер корпуса должен быть целым положительным числом";

				return (null);
			}
		} else {
			$query .= "and TANK is null ";
		}
		if ($data[ "STRUCT" ] != "") {
			if (is_numeric($data[ "STRUCT" ]) && (round(
						$data[ "STRUCT" ]
					) == $data[ "STRUCT" ]) && ($data[ "STRUCT" ] > 0)) {
				$query .= "and STRUCT = :STRUCT ";
				$param[ "STRUCT" ] = $data[ "STRUCT" ];
				$found++;
			} else {
				$data[ "note" ][ "error" ] = "Ошибка, номер строения должен быть целым положительным числом";

				return (null);
			}
		} else {
			$query .= "and STRUCT is null ";
		}
		if ($data[ "VLADENIE" ] != "") {
			if (is_numeric($data[ "VLADENIE" ]) && (round(
						$data[ "VLADENIE" ]
					) == $data[ "VLADENIE" ]) && ($data[ "VLADENIE" ] > 0)) {
				$query .= "and VLADENIE = :VLADENIE ";
				$param[ "VLADENIE" ] = $data[ "VLADENIE" ];
				$found++;
			} else {
				$data[ "note" ][ "error" ] = "Ошибка, номер владения должен быть целым положительным числом";

				return (null);
			}
		} else {
			$query .= "and VLADENIE is null ";
		}
		if ($data[ "LIT_N1" ] != "") {
			if (mb_strlen($data[ "LIT_N1" ]) <= 5) {
				$query .= "and LIT_N1 = :LIT_N1 ";
				$param[ "LIT_N1" ] = $data[ "LIT_N1" ];
				$found++;
			} else {
				$data[ "note" ][ "error" ] = "Ошибка, литера номера дома не должна превышать пяти символов";

				return (null);
			}
		} else {
			$query .= "and LIT_N1 is null ";
		}
		if ($data[ "LIT_N2" ] != "") {
			if (mb_strlen($data[ "LIT_N2" ]) <= 5) {
				$query .= "and LIT_N2 = :LIT_N2 ";
				$param[ "LIT_N2" ] = $data[ "LIT_N2" ];
				$found++;
			} else {
				$data[ "note" ][ "error" ] = "Ошибка, литера номера дома не должна превышать пяти символов";

				return (null);
			}
		} else {
			$query .= "and LIT_N2 is null ";
		}
		if ($data[ "LIT_TN" ] != "") {
			if (mb_strlen($data[ "LIT_TN" ]) <= 5) {
				$query .= "and LIT_TN = :LIT_TN ";
				$param[ "LIT_TN" ] = $data[ "LIT_TN" ];
				$found++;
			} else {
				$data[ "note" ][ "error" ] = "Ошибка, литера корпуса дома не должна превышать пяти символов";

				return (null);
			}
		} else {
			$query .= "and LIT_TN is null ";
		}
		if ($data[ "LIT_ST" ] != "") {
			if (mb_strlen($data[ "LIT_ST" ]) <= 5) {
				$query .= "and LIT_ST = :LIT_ST ";
				$param[ "LIT_ST" ] = $data[ "LIT_ST" ];
				$found++;
			} else {
				$data[ "note" ][ "error" ] = "Ошибка, литера строения не должна превышать пяти символов";

				return (null);
			}
		} else {
			$query .= "and LIT_ST is null ";
		}
		if ($data[ "LIT_VL" ] != "") {
			if (mb_strlen($data[ "LIT_VL" ]) <= 5) {
				$query .= "and LIT_VL = :LIT_VL ";
				$param[ "LIT_VL" ] = $data[ "LIT_VL" ];
				$found++;
			} else {
				$data[ "note" ][ "error" ] = "Ошибка, литера владения не должна превышать пяти символов";

				return (null);
			}
		} else {
			$query .= "and LIT_VL is null ";
		}
		if ($found == 0) {
			$data[ "note" ][ "error" ] = "Ошибка, не указаны реквизиты дома";

			return (null);
		}
		$a = $this->db->queryArray($query, $param);
		//echo "<br>$query<br>";
		//echo "<pre>";
		//print_r($param);
		//print_r($a);
		//echo "</pre>";
		return ($a);
	}

	public function getRegionById($ao_id)
	{
		$query = "select ID, FOO_GIS.FULLNAME_OBJECT(ID) as NAME
          from FOO_GIS.ATD_OBJECT ao
          where PAR_ID is null connect by prior PAR_ID = ID start with ID = :ao_id";
		$param = [ "ao_id" => $ao_id ];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	public function getDepById($ao_id)
	{
		$query = "select ad.AU_DEPNAME
          from FOO_AUTH.AUTH_DEPARTMENT_CITIES adc, FOO_AUTH.AUTH_DEPARTMENTS ad
          where adc.CITY_ID = :ao_id and adc.AU_DEPID = ad.AU_DEPID";
		$param = [ "ao_id" => $ao_id ];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}

	/**
	 * проверить блокировку параметра в регионе
	 *
	 * @param $ao_id
	 * @param $param_name
	 *
	 * @return int
	 *
	 */
	public function isLockDne($ao_id, $param_name)
	{
		$query = "select ID, PARAM_NAME, RCL_DATE, FOO_AUTH.GETUSERNAME(AU_ID) as NAME
          from FOO_GIS.RTK_CITYPARAMS_LOCK
          where ID = :ao_id and PARAM_NAME = :param_name";
		$param = [ "ao_id" => $ao_id, "param_name" => $param_name ];
		$a = $this->db->queryArray($query, $param);
		if (count($a) > 0) {
			return 1;
		}

		return 0;
	}

	/**
	 * проверить блокировку параметра в НП
	 *
	 * @param $ao_id
	 * @param $param_name
	 *
	 * @return int
	 *
	 */
	public function isLockAO($ao_id, $param_name)
	{
		$query = "select rcl.ID, rcl.PARAM_NAME, rcl.RCL_DATE, FOO_AUTH.GETUSERNAME(rcl.AU_ID) as NAME
          from FOO_GIS.RTK_CITYPARAMS_LOCK rcl, FOO_GIS.ATD_OBJECT ao
          where rcl.ID (+)= ao.ID and rcl.PARAM_NAME (+)= :param_name and rcl.ID is not null AND
          rcl.PARAM_NAME is not null
          connect by prior ao.PAR_ID = ao.ID start with ao.ID = :ao_id
          ";
		$param = [ "ao_id" => $ao_id, "param_name" => $param_name ];
		$a = $this->db->queryArray($query, $param);

		if (count($a) > 0) {
			return 1;
		}

		return 0;
	}

	/**
	 * заблокировать-разблокировать параметр в регионе
	 *
	 * @param $ao_id
	 * @param $param_name
	 * @param $curLockState
	 */
	public function setLockDne($ao_id, $param_name, $curLockState)
	{
		if ($curLockState == 0) {
			$query = "merge into FOO_GIS.RTK_CITYPARAMS_LOCK rcl
            using (select 1 from dual) sd
            on (rcl.ID = :ao_id and rcl.param_name = :param_name)
            when not matched then
            insert (ID, PARAM_NAME, AU_ID) values (:ao_id, :param_name, :au_id)";
			$param = [ "ao_id" => $ao_id, "param_name" => $param_name, "au_id" => $this->perm->UserID ];
			$msg = "Параметр $param_name заблокирован пользователем " . $this->perm->getUserInfo(
					$this->perm->UserID
				)[ "AU_NAME" ];
			$op = "ins";
		} else {
			$query = "delete
          from FOO_GIS.RTK_CITYPARAMS_LOCK
          where ID = :ao_id and PARAM_NAME = :param_name";
			$param = [ "ao_id" => $ao_id, "param_name" => $param_name ];
			$op = "del";
			$msg = "Параметр $param_name разблокирован пользователем " . $this->perm->getUserInfo(
					$this->perm->UserID
				)[ "AU_NAME" ];
		}
		$this->db->query($query, $param);
		$ret = $this->logging->globalLogging($this->db, $ao_id, 5, $msg, $op, "web");
		if ($ret != "OK") {
			$this->db->rollback();
		} else {
			$this->db->commit();
		}
	}

	/**
	 * Найти объект логирования по части названия
	 *
	 * @param $search
	 *
	 * @return array|int
	 */
	public function searchRegObject($search)
	{
		$search = mb_strtolower($search);

		$query = "select ID as OBJ_ID, FOO_GIS.FULLNAME_OBJECT(ID) as OBJ_NAME from FOO_GIS.ATD_OBJECT where PAR_ID is null and lower(NAME) LIKE :1";
		$param = [ "1" => "%$search%" ];
		$a = $this->db->queryArray($query, $param);

		return $a;
	}

	/**
	 * получить намера домов на улице СП по идентификатору улицы КЛАДР
	 *
	 * @param $kladr_street_id
	 *
	 * @return array|int
	 */
	public function getSpHousesByKladrSreetId($ao_id, $kladr_street_id)
	{
		$list = "";
		if ($kladr_street_id == "nostreet") {
			$query = "
            select st.ID, ad.ADDR_ID, FOO_GIS.GET_HOUSENUMBER(ad.ADDR_ID) as HOUSE 
            from FOO_GIS.ATD_ADDRESS ad, FOO_GIS.ATD_STREET st
            where
             st.OBJ_ID = :ao_id
            and st.CODE = :code
            and ad.STR_STD1 = st.ID
            order by ad.NUMBER1
            ";
			$param = [ "ao_id" => $ao_id, "code" => $this->nostreet[ "CODE" ] ];
		} else {
			$query = "
            select st.ID, ad.ADDR_ID, FOO_GIS.GET_HOUSENUMBER(ad.ADDR_ID) as HOUSE 
            from FOO_GIS.ATD_ADDRESS ad, FOO_GIS.ATD_STREET st, FOO_GIS.KLADR_STREET ks
            where
            ks.ID = :kladr_street_id
            and st.OBJ_ID = :ao_id
            and st.CODE = ks.CODE
            and ad.STR_STD1 = st.ID
            order by ad.NUMBER1
        ";
			$param = [ "ao_id" => $ao_id, "kladr_street_id" => $kladr_street_id ];
		}
		$a = $this->db->queryArray($query, $param);
		if (count($a) > 0) {
			for ($i = 0; $i < count($a); $i++) {
				if ($list != "") {
					$list .= ", ";
				}
				$list .= $a[ $i ][ "HOUSE" ];
			}
		} else {
			$list = "Не определено";
		}

		return $list;
	}

	/**
	 * Новая функция getSPparams для Анатолия
	 *
	 * @param string $ao_id
	 * @param string $dep_id
	 * @param bool   $dofull
	 * @param bool   $doaddr
	 * @param string $odf_type
	 *
	 * @return mixed
	 *
	 */
	public function getSP($ao_id = "", $dep_id = "", $dofull = true, $doaddr = false, $odf_type = "")
	{
		$txt = "";
		$aotxt = " ";
		$deptxt = " ";
		$param = [];

		if ($ao_id != "") {
			$aotxt = "connect by prior ao.ID = ao.PAR_ID start with ao.ID = :1 ";
			$param = [
				"1" => $ao_id,
			];
		}
		if ($dep_id != "") {
			$deptxt = " connect by prior ao.ID = ao.PAR_ID start with ao.ID in
            (select AREA_ID from FOO_AUTH.AUTH_DEPARTMENT_AREAS where AU_DEPID in
            (select AU_DEPID from FOO_AUTH.AUTH_DEPARTMENTS
            connect by prior AU_DEPID = AU_DEPPARID start with AU_DEPID = :1))";
			$param = [
				"1" => $dep_id,
			];
		}
		if (!$dofull) {
			// ограничиться списком регионов департамента
			$dofulltxt = "FOO_GIS.CHECKCITY(:2, ao.ID) = 1 and";
			$param[ "2" ] = $this->perm->UserID;
		}

		if ($odf_type != "") {
			$td = $this->getRtkTdType($odf_type);
			if (count($td) > 0) {
				$pos = strpos($td[ "PARAM_NAME" ], "_");
				if ($pos !== false) {
					$td_param = substr($td[ "PARAM_NAME" ], 0, $pos);
				} else {
					$td_param = $td[ "PARAM_NAME" ];
				}
				$dofulltxt .= " nvl((SELECT PARAM_VALUE FROM FOO_GIS.CITY_PARAM_VALUES WHERE OBJ_ID=ao.ID AND PARAM_ID=:3),0) > 0 AND ";
				$param [ "3" ] = $td_param;
			}
		}
		$query = "select LEVEL,
        SYS_CONNECT_BY_PATH(ao.NAME || ' ' || aot.NAME, '|') as PATH, ao.ID from
                    FOO_GIS.ATD_OBJECT ao,
                    FOO_GIS.ATD_OBJ_TYPE aot
                    where        
                    $dofulltxt
                    nvl(AO.ATDOBJ_TYPE,0) = AOT.CODE(+)
                    $aotxt $deptxt
                    order by PATH";

		//echo "<br>($query)<br>";
		$a = $this->db->queryArray($query, $param);
		for ($i = 0; $i < count($a); $i++) {
			$d = $this->getFiasBySpId($a[ $i ][ "ID" ]);
			$a[ $i ][ "KLADR" ] = $d[ 0 ][ "CODE" ];
			$a[ $i ][ "FIAS" ] = $d[ 0 ][ "FIAS" ];
			if ($odf_type != "" && $doaddr) {
				if (\FOO\FeatureToggle\Features::isEnabled('Address::HE-4408')) {
					$c = $this->getRtkTDbyAOandTypeFixed($a[ $i ][ "ID" ], $odf_type);
				} else {
					$c = $this->getRtkTDbyAOandType($a[ $i ][ "ID" ], $odf_type);
				}
				$a[ $i ][ "ADDRESS" ] = $c[ 0 ][ "ADDRESS" ];
				$a[ $i ][ "ADDR_ID" ] = $c[ 0 ][ "ADDR_ID" ];
				$a[ $i ][ "ODF_TYPE" ] = $c[ 0 ][ "ODF_TYPE" ];
			}
			$b = explode("|", $a[ $i ][ "PATH" ]);
			$a[ $i ][ "SUBJECT_NAME" ] = $b[ 1 ];
			$a[ $i ][ "MO_NAME" ] = $b[ 2 ];
			$a[ $i ][ "NP_NAME" ] = $b[ 3 ];
			$a[ $i ][ "INP_NAME" ] = $b[ 4 ];
		}
		//echo "<pre>";
		//print_r($param);
		//print_r($a);
		//echo "</pre>";
		//$result[ "columns" ] = $columns;
		$result[ "data" ] = $a;
		$result[ "count" ] = count($a);

		return ($result);
	}

	/**
	 * Получить ТД по НП и типу (из фиксированной таблицы для ТД)
	 *
	 * @param $ao_id
	 * @param $odf_type
	 *
	 * @return array|int
	 *
	 *
	 */
	public function getRtkTDbyAOandTypeFixed($ao_id, $odf_type)
	{
		if ($odf_type != 'ТД') {
			return $this->getRtkTDbyAOandType($ao_id, $odf_type);
		}
		$query = "select
          FOO_GIS.GET_ADDRESS(tda.ADDR_ID,1) as ADDRESS,
          tda.ADDR_ID,
          tda.ODF_TYPE
          from FOO_GIS.RTK_TD_FIXED tda
          where
           tda.ID = :ao_id AND
           tda.ODF_TYPE = :odf_type";
		$param = [ "ao_id" => $ao_id, "odf_type" => $odf_type ];
		$a = $this->db->queryArray($query, $param);

		return ($a);
	}
}


