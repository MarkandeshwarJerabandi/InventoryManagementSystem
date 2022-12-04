<?php

class crypto
{
	public function cypher( $q ) 
	{
    	$cryptKey  = 'PssManagementSystem';
    	$qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    	return( $qEncoded );
	
	}
}
?>
