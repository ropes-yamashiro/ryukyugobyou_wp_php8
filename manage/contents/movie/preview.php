<?php
$id = $_GET["id"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>動画プレビュー</title>
<link rel="stylesheet" type="text/css" href="../../../css/default.css"/>
<link rel="stylesheet" type="text/css" href="../../../css/movie.css"/>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

<script type="text/javascript">
window.onload = function(){
	var url		= '../../../movie_call.php';
	var id	= <?php echo $id ?>;	//動画ID
	var data	=	{
						 mid	:	id
					};

	$.ajax({
				dataType:	"json",
				url		:	url,
				data	:	data,
				success	:	function(ret){
								
								var title		= htmlspecialchars_decode(ret.title);
								var explain		= htmlspecialchars_decode(ret.explain);
								var movie_tag	= htmlspecialchars_decode(ret.movie_tag);
								var movie_link	= htmlspecialchars_decode(ret.movie_link);
								//タイトル
								if( $('#movie_title')	){ $('#movie_title').html(title);		}
								//動画タグ
								if( $('#movie_tag')		){ $('#movie_tag').html(movie_tag);		}						
								//動画説明
								if( $('#movie_explain')	){ $('#movie_explain').html(explain);	}
								//動画リンク
								if( $('#movie_link')	){ $('#movie_link').attr("href", movie_link);	}
							},
				error:		function( data ) {
								alert( '読み込み失敗：' + data );
							}
 			});
			
	
}
function htmlspecialchars_decode(str) {
  return str.replace(/&(gt|lt|#039|quot|amp);/ig, function($0, $1) {
    if (/^gt$/i.test($1))   return ">";
    if (/^lt$/i.test($1))   return "<";
    if (/^#039$/.test($1))  return "'";
    if (/^quot$/i.test($1)) return "\"";
    if (/^amp$/i.test($1))  return "&";
  });
};
</script>

</head>
<body class="movie">
<!-- ラッパー -->
<div id="wrapper" class="clearfix">
<div id="container">
<!-- コンテンツ -->
<div id="contents">


<p id="movie_title">&nbsp;</p>

<p id="movie_tag">&nbsp;</p>

<p id="movie_explain">&nbsp;</p>
<p id="movie_link_box"><a href="" target="_blank" id="movie_link"><img src="../../../img/img-movie/btn_01.jpg" width="181" height="49" alt="Youtubeで見る" /></a></p>


</div>
<!-- （終）コンテンツ -->

</div>
</div>
<!-- （終）ラッパー -->
</body>
</html>