<?php

class Utility 
{	
	function encrypt($input, $key) 
	{		
	    return  base64_encode(openssl_encrypt($input,"AES-256-ECB", $key, OPENSSL_RAW_DATA ));
	}
	
	function decrypt($sStr, $key) 
	{		
		return  openssl_decrypt(base64_decode($sStr), 'AES-256-ECB', $key, OPENSSL_RAW_DATA);
	}	
	
	function generateSecurehash($sortedData)
	{ 
		if($sortedData)
		{
			$SecureHash = SECURE_SECRET;

			foreach($sortedData as $key => $val)
			{
				  $SecureHash = $SecureHash . ($val);
				
			}
		}
		//Generate SHA-256 hash
		$SecureHash = hash('sha256', utf8_encode($SecureHash));
		return $SecureHash;
	}
	
	function null2unknown($check_null,$Array_data)
	{
		if(!isset($Array_data[$check_null]))
		{
			return "No Value Returned";
		}
		else
		{
			return $Array_data[$check_null];
		}
	}
}
?>