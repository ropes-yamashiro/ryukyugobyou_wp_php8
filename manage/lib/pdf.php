<?php
#==================================
# Hudom CMS
# File version: 1.0.0
# Author: Takamichi.Hara
# created: 2012/09/12
# History:
# modefied: 2012/00/00 Takamichi.Hara
#==================================

function pdf_upload($srcfile,$newfile){
	if (is_uploaded_file($srcfile)) {
		move_uploaded_file($srcfile, $newfile);
	}
}

?>