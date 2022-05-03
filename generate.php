<?php
//ini_set('max_execution_time', 0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$AmazonLinksFile = 'AmazonLinks.txt';
$groupID = 00000; //Group ID from Geniuslink dashboard. This is where the generated short links will be added.

if (!file_exists($AmazonLinksFile)) {   
    die("File not found!");
}

$i = 1;
$handle = fopen($AmazonLinksFile, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) { //process each link from the text file
        if(!empty(trim($line))){
            $AMZNlink = urlencode(trim($line));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://api.geni.us/v3/shorturls?url=$AMZNlink&groupId=$groupID");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $headers = [
                'X-Api-Key: <your-api-key>', //add your API Key here
                'X-Api-Secret: <your-api-secret>' //add your API secret here
            ];
          
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $server_output = curl_exec ($ch);
            $json = json_decode($server_output);
            
            if(!empty($json->shortUrl->baseCode)){ //check if API returned the short code
                $baseCode = $json->shortUrl->baseCode;
                $baseDomain = $json->shortUrl->baseDomain;
                echo "$i,\"$AMZNlink\",https://$baseDomain/$baseCode<br/>";
            }
            else {
                echo "$i,\"$AMZNlink\",ERROR <br/>";
            }
            $i++;
            curl_close ($ch);
        }
    }
} else {
    die("Could not read Amazon links");
} 
fclose($handle);

unlink($amznLinksFile); //delete the AmazonLinks text file to prevent the script from bring run again with same links accidently.
?>
