<?php


//--- 汎用関数

//【機能】メールアドレスの妥当性チェック
	function sbfunc_sbfunc_CheckEmail($email){
	//【引数】チェック対象メールアドレス
	//【戻値】妥当：1　携帯：0　不正：0未満
	//【備考】
	//【履歴】
	//obj	date		uther		comment
	//-----------------------------------------------
	//1.00	2006-10-07	K.Arikata	new

		$ret = 1;
		if( empty($email) == true ){ return $ret; }

		$ary = explode("@",$email);
		if( count($ary) != 2 ){
			return -3;	//アドレスには@は唯一
		}

		if( ereg("^[^@]+@[^.@]+\..[^@]+",$email) == false ){
			//アドレス不整合
			//(^[^@]+)	・・・文字列先頭から @ の直前の間に @ 以外の任意の文字がひとつ以上
			//([^.@]+)	・・・@ の後にはドットおよび@以外の文字がひとつ以上
			//(\..[^@]+)・・・ドット(\.)の後に@以外の任意の文字がひとつ以上
			$ret = -1;
		}
		if($ret > 0 ){
			//ドメイン不正
			$address = explode("@",$email);
if( !OS_WINDOWS ){
			if( checkdnsrr($address[1]) == false ){
				$ret = -2;
			}
}
		}
		if($ret > 0 ){
			//携帯メルアドかどうか
			if( 	strpos($email,"docomo.ne.jp")	!== false
				||	strpos($email,"ezweb.ne.jp")	!== false
				||	strpos($email,"vodafone.ne.jp")	!== false
				||	strpos($email,"softbank.ne.jp")	!== false
				||	strpos($email,"pdx.ne.jp")		!== false	){
				$ret = 0;
			}
		}

		return $ret;

	}
//【機能】URLの妥当性チェック
	function sbfunc_sbfunc_CheckURL($url){
	//【引数】チェック対象URL
	//【戻値】妥当：1　URLなし：0　不正：0未満
	//【備考】
	//【履歴】
	//obj	date		uther		comment
	//-----------------------------------------------
	//1.00	2006-10-15	K.Arikata	new

		$ret = 1;
		if( empty($url) == true ){ return 0; }

		if( ereg("^http://[^\.].+\.[^\.].+[^\.]$",$url) == false ){
			//URL不整合
			//http://からはじまる
			//([^\.].+)	・・・//の後は、ドット以外の任意の文字列がひとつ以上
			//(\.[^\.].)・・・ドットの後、ドット以外の任意の文字列がひとつ以上
			//([^\.]$)	・・・ドット以外で終わる
			$ret = -1;
		}

		return $ret;

	}

//----- 数値整形
//【機能】数値形式→貨幣形式
	function sbfunc_FormatCurrency($str_target, $digit = "", $space = " "){
	//【引数】$str_target：変換対象文字列
	//　　　　$digit：そろえる桁数
	//【戻値】変換後文字列　桁揃えは空白
	//【備考】bookcore ncjsfunc.jsより
	//【履歴】
	//obj	date		auther		comment
	//-----------------------------------------------
	//1.00	2004-09-22	K.Arikata	bookcore ncjsfunc.jsより

		$strReturn	= '';
		$strPre		= '';

		// マイナス数値の場合の処理
		if( substr($str_target,0,1) == "-" )
		{
			$strPre		= '-';
			$str_target	= substr($str_target,1);
		}
		// 数値が４桁以上の場合のみ実行
		if( strlen($str_target) > 3 ){
			$Rest = strlen($str_target) % 3;	//３桁ずつ区切ったときの余り
			if( $Rest == 0 ){
				$Add = 0;
			}
			else{
				$Add = 3 - $Rest;	//頭に付加する仮文字の数
			}
			$idx = $Add;
			for( $idx = 1; $idx <= $Add; $idx ++ )		// ３桁で区切れるように'Q'を頭に付加
			{
				$str_target = 'Q'.$str_target;
			}
			$Count = ( strlen($str_target) / 3 );				// 区切りの数を算出
			$Point = 0;
			for( $idx = 1; $idx <= $Count - 1; $idx++ )
			{
				$strReturn	.= substr($str_target, $Point, 3).',';	// ３桁毎にカンマを付加
				$Point += 3;
			}
			$strReturn	.= substr($str_target, $Point);	// 最後の３桁を付加
			$strReturn	 = substr($strReturn, $Add);	// 頭の'Q'をとる
			$strReturn	 = $strPre.$strReturn;			// マイナス数値の場合は'-'を付加
		}
		else{
			$strReturn	 = $strPre.$str_target;				// マイナス数値の場合は'-'を付加
		}
		
		if( !empty($digit) ){
			if( strlen($strReturn) < $digit ){
			//	$strReturn = substr( str_repeat($space, $digit).$strReturn, $digit * -1);
				$strReturn = str_pad($strReturn, $digit, $space, STR_PAD_LEFT);
			}
		}
		
		return $strReturn;
	}


//----- 数値整形	（貨幣形式→数値形式） -----
	function sbfunc_FormatNumber($value)
	{
		return str_replace(',','',$value);
	}


//----- YouTube動画 -----

//【機能】YouTubeの動画タグの整合性をチェックする
	function sbfunc_YouTubeTagCheck($youtube_tag){
	//【引数】$youtube_tag：チェック対象タグ
	//【戻値】OK：1　NG：< 0
	//【備考】_cl_html.phpライブラリが必要
	//【履歴】
	//obj	date		auther		comment
	//----------
	//1.00	2010-09-21	K.Arikata	New
	//1.01	2013-01-23	K.Arikata	iframeに対応

	//	$youtube_tag = str_replace("&amp;", "&", $youtube_tag);

		$objTag = str_get_html($youtube_tag);

		//タグの有無
		$aryTag_object = $objTag->find('object');
		//OBJECTタグの有無
		if( count($aryTag_object) == 0 ){
			
			$aryTag_iframe = $objTag->find('iframe');
			if( count($aryTag_iframe) == 0 ){
			
				//タグなし
				return -1;
			}
		}
		
		if( count($aryTag_object) > 0 ){

			//OBJECTタグの数
			if( count($aryTag_object) != 1 ){
				//OBJECTタグの数が不正
				return -2;
			}
	
			//OBJECTタグの有無
			$aryTag_param = $objTag->find('param');
			if( count($aryTag_param) == 0 ){
				//PARAMタグなし
				return -3;
			}
			if( count($aryTag_param) != 3 ){
				//PARAMタグの数が不正
				return -4;
			}
	
			foreach($aryTag_param as $param)
			{
				switch($param->name)
				{
					case "movie":
						$movie_url = $param->value;
						$aryURL = parse_url($movie_url);
						if( $aryURL["host"] != "www.youtube.com" ){
							//MOVIEパラメータのURLが不正
							return -5;
						}
						break;
					case "allowFullScreen":
					case "allowscriptaccess":
						//無視
						break;
					default:
						//PARAMタグのNAME属性が不正
						return -6;
						break;
				}
			}
	
	
			$aryTag_embed = $objTag->find('embed');
			if( count($aryTag_embed) == 0 ){
				//EMBEDタグなし
				return -7;
			}
	
			if( count($aryTag_embed) != 1 ){
				//EMBEDタグの数が不正
				return -8;
			}
	
			$embed_src = $aryTag_embed[0]->src;
			$arySRC = parse_url($embed_src);
			if( $arySRC["host"] != "www.youtube.com" ){
				//EMBEDのURLが不正
				return -9;
			}


			return 1;
			
		}//if( count($aryTag_object) > 0 )
		
		elseif( count($aryTag_iframe) > 0 ){
			
			if( count($aryTag_iframe) != 1 ){
				//OBJECTタグの数が不正
				return -12;
			}
						
			$movie_url = $aryTag_iframe[0]->attr["src"];	
			$aryURL = parse_url($movie_url);
			if( $aryURL["host"] != "www.youtube.com" ){
				//MOVIEパラメータのURLが不正
				return -15;
			}

			return 2;
		}
		
		else{
			
			return -99;
			
		}
	}


//【機能】YouTubeの動画タグを生成する
	function sbfunc_MakeYouTubeTag($youtube_tag, $autoplay = "0", $loop = "0", $width = "", $height = ""){
	//【引数】$youtube_tag：YouTubeから取得した動画タグ
	//　　　　$autoplay	：ロード時自動再生するかどうか( する：1　しない：0 )
	//　　　　$loop		：連続再生するかどうか( する：1　しない：0 )
	//　　　　$return_src：ソースURLのみ返値する
	//【戻値】成功：パラメータを付加した動画タグ　失敗：< 0
	//【備考】_cl_html.phpライブラリが必要
	//【履歴】
	//obj	date		auther		comment
	//----------
	//1.00	2010-09-21	K.Arikata	New
	//1.01	2012-01-15	K.Arikata	$width引数の既定値を削除
	//1.02	2013-01-22	K.Arikata	iframeに対応

		//動画タグの整合性チェック
		$check = sbfunc_YouTubeTagCheck($youtube_tag);
		if( $check < 0 ){
			return $check;
		}

		$youtube_tag = str_replace("&amp;", "&",	 $youtube_tag);
		$youtube_tag = str_replace("&",		"&amp;", $youtube_tag);

		//動画タグを取得
		$objTag = str_get_html($youtube_tag);
				
		switch($check)
		{
			case 1:	//objectタグ
				//EMBEDタグを取得
				$aryTag_embed = $objTag->find('embed');
		
				//--- 再生パラメータ設定
				//EMBEDタグのSRC属性を取得
				$embed_src = $aryTag_embed[0]->src;
				$embed_src = html_entity_decode($embed_src, ENT_QUOTES, CURRENT_CHARACTERSET_PHP);
				//SRC属性のクエリー文字列に、パラメータを設定
				$arySRC = parse_url($embed_src);
				$str_query = $arySRC["query"];
				parse_str($str_query, $aryQuery);
				$aryQuery["rel"]		= "0";			//終了時の関連動画を表示しない
				$aryQuery["autoplay"]	= $autoplay;	//ロード時自動再生するかどうか( する：1　しない：0 )
				$aryQuery["loop"]		= $loop;		//連続再生するかどうか( する：1　しない：0 )
				$aryQuery["color1"]		= "";			//既定値に初期化
				$aryQuery["color2"]		= "";			//　　　〃
				$aryQuery["border"]		= "0";			//「なし」に設定
		
				$arySRC["query"] = http_build_query($aryRet);
				$arySRC["query"] = htmlentities($arySRC["query"], ENT_QUOTES, CURRENT_CHARACTERSET_PHP);
		
				$aryTag_embed[0]->src = $arySRC["scheme"]."://".$arySRC["host"].$arySRC["path"]."?".$arySRC["query"];
		
				//--- サイズパラメータ設定
				//表示レートの算出
				if( empty($width) ){
					$rate = 1;
				}
				else{
					$rate	= $width / $aryTag_embed[0]->width;	//幅の縮小率
				}
				$height	= $aryTag_embed[0]->height * $rate;
				//サイズ属性にパラメータを設定
				$aryTag_embed[0]->width		= $width;
				$aryTag_embed[0]->height	= $height;
		
				//変更を保存して文字列変換
				$output = $objTag->save();
				
				break;
				
			case 2:	//iframeタグ
		
				$aryTag_iframe = $objTag->find('iframe');
				$iframe_src = $aryTag_iframe[0]->attr["src"];

				//SRC属性のクエリー文字列に、パラメータを設定
				$arySRC = parse_url($iframe_src);
				parse_str($arySRC["query"], $aryQuery);
				$aryQuery["rel"]		= "0";			//終了時の関連動画を表示しない
				$aryQuery["autoplay"]	= $autoplay;	//ロード時自動再生するかどうか( する：1　しない：0 )
				$aryQuery["loop"]		= $loop;		//連続再生するかどうか( する：1　しない：0 )
				$aryQuery["color1"]		= "";			//既定値に初期化
				$aryQuery["color2"]		= "";			//　　　〃
				$aryQuery["border"]		= "0";			//「なし」に設定
				$arySRC["query"] = http_build_query($aryQuery);
				$arySRC["query"] = htmlentities($arySRC["query"], ENT_QUOTES, CURRENT_CHARACTERSET_PHP);
				
				$aryTag_iframe[0]->attr["src"] = $arySRC["scheme"]."://".$arySRC["host"].$arySRC["path"]."?".$arySRC["query"];
		
				//--- サイズパラメータ設定
				//表示レートの算出
				if( empty($width) ){
					$rate = 1;
				}
				else{
					$rate	= $width / $aryTag_iframe[0]->attr["width"];	//幅の縮小率
				}
				if( empty($height) ){
					$height	= $aryTag_iframe[0]->attr["height"] * $rate;
				}

				//サイズ属性にパラメータを設定
				$aryTag_iframe[0]->attr["width"]	= $width;
				$aryTag_iframe[0]->attr["height"]	= $height;
		
				//変更を保存して文字列変換
				$output = $objTag->save();
			
				break;
				
		}
			

		return $output;
	}
/**
 *	YouTubeタグからURLを取得する
 *	
 *	@param	string	Youtubeタグ
 *	@return	string	YoutubeへのURL
 *	@version
 *		1.00	2013-01-25	K.Arikata	New
 **/
	function sbfunc_GetYouTubeURL($youtube_tag){
		
		//動画タグの整合性チェック
		$check = sbfunc_YouTubeTagCheck($youtube_tag);
		if( $check < 0 ){
			return $check;
		}

		$youtube_tag = str_replace("&amp;", "&",	 $youtube_tag);
		$youtube_tag = str_replace("&",		"&amp;", $youtube_tag);

		//動画タグを取得
		$objTag = str_get_html($youtube_tag);
				
		switch($check)
		{
			case 1:	//objectタグ
				//EMBEDタグを取得
				$aryTag_embed = $objTag->find('embed');
				//EMBEDタグのSRC属性を取得
				$src = $aryTag_embed[0]->src;
				$src = html_entity_decode($src, ENT_QUOTES, CURRENT_CHARACTERSET_PHP);
				
				break;
				
			case 2:	//iframeタグ
		
				$aryTag_iframe = $objTag->find('iframe');
				//iframeタグのSRC属性を取得
				$src = $aryTag_iframe[0]->attr["src"];
				
				break;
				
		}
		
		return $src;
	}
/**
 *	YouTubeタグから動画IDを取得する
 *	
 *	@param	string	Youtubeタグ
 *	@return	string	YoutubeへのURL
 *	@version
 *		1.00	2013-01-25	K.Arikata	New
	watch?v=
 **/
 	function sbfunc_GetYouTubeID($youtube_tag){
		
		$src = sbfunc_GetYouTubeURL($youtube_tag);
		
		$arySrc = parse_url($src);
		
		$id	= end(explode("/", $arySrc["path"]));	
		
		return $id;
	}
/**
 *	YouTubeタグから動画IDを取得する
 *	
 *	@param	string	Youtubeタグ
 *	@return	string	YoutubeへのURL
 *	@version
 *		1.00	2013-01-25	K.Arikata	New
	watch?v=
 **/
 	function sbfunc_GetYouTubeWatch($youtube_tag){
		
		$src = sbfunc_GetYouTubeURL($youtube_tag);
		
		$arySrc = parse_url($src);
			
		$link	= $arySrc["scheme"]."://".$arySrc["host"]."/watch?v=".sbfunc_GetYouTubeID($youtube_tag);
		
		return $link;
	}

/**
 *	住所から緯度と経度を取得する
 *	
 *	@param	string	住所
 *	@return	array	[0]緯度，[1]経度
 *	@cation	GoogleMap APIを仕様
 *	@version
 *		1.00	2013-01-10	K.Arikata	New
 **/
	function sbfunc_GetLocation($address){

		$ret_json = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false');

		$aryGeo = json_decode( $ret_json, true );

		if ( isset( $aryGeo['results'][0] ) ){
			$longitude	= (string)$aryGeo['results'][0]['geometry']['location']['lng'];
			$latitude	= (string)$aryGeo['results'][0]['geometry']['location']['lat'];
		}
		
		return array(0=>$longitude, 1=>$latitude, "longitude"=>$longitude, "latitude"=>$latitude);
	}
/**
 *	住所を元にGoogleMapのリンクを生成する
 *	
 *	@param	float	$latitude		緯度
 *	@param	float	$longitude		経度
 *	@param	string	$marker_string	地図マーカの吹き出しに表示する文字列
 *	@param	string	$for_iframe		iframeのsrc用　output=embed（情報パネルなし）を付加する
 *
 *	@return	string	リンクURL
 *	@version
 *		1.00	2013-01-25	K.Arikata	New
 *		1.01	2013-02-15	K.Arikata	引数に$marker_string追加
 **/
 	function sbfunc_GetGoogleMapLink($latitude, $longitude, $marker_string = "", $for_iframe = false){
		
		$zoom	= 15;
		$hl		= "ja";
		$ie		= "UTF8";
				
		$url = "http://maps.google.co.jp/maps?q=".( empty($marker_string) ? "loc:" : $marker_string."@" ).$latitude.",".$longitude."&amp;ll=".$latitude.",".$longitude."&amp;z=".$zoom."&amp;hl=".$hl."&amp;ie=".$ie."&amp;iwloc=J".( $for_iframe ? "&amp;output=embed" : "" );
		
		return $url;
	}
/**
 *	住所を元にGoogleMapのリンクを生成する
 *	
 *	@param	string	住所
 *	@return	string	リンクURL
 *	@version
 *		1.00	2013-01-25	K.Arikata	New
 **/
	function sbfunc_GetGoogleMapLinkByAddress($address, $marker_string = ""){
		
		$aryLoc = sbfunc_GetLocation($address);
		
		return sbfunc_GetGoogleMapLink($aryLoc["latitude"], $aryLoc["longitude"], $marker_string);
	}
		
	
//PHP5→PHP7アップグレード関数
if (!function_exists("ereg")) {	
	function ereg($pattern, $string, &$matches = array()) {	
		if (strpos($pattern, "/")) {
			return 	preg_match("{".$pattern."}", $string, $matches);	//デリミタ：{}
		}
		else {
			return 	preg_match("/".$pattern."/", $string, $matches);
		}
	}
}
if (!function_exists("split")) {
	
	function split($delimiter, $subject) {
		return explode($delimiter, $subject);
	}
}
	 
?>