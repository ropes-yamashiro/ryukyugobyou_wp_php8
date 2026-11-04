function delfile(target){
    if(window.confirm('ファイルを削除しますか？')){
        document.formx.cmd.value = "del";
        document.formx.typex.value="2";
        document.formx.param.value=target;
        document.formx.submit();
    }
	return false;
}
function selector(target){
	if(target == 'repeat'){
		document.formx.typex.value="0";
		document.formx.submit();	
	}
	if(target == 'clear'){
		document.formx.typex.value="1";
		document.formx.submit();	
	}
	if(target == 'back'){
		document.formx.typex.value="2";
		document.formx.submit();	
	}
	if(target == 'next'){
		document.formx.typex.value="3";
		document.formx.submit();	
	}
	if(target == 'send'){
		document.formx.typex.value="4";
		document.formx.submit();
	}
	return false;
}