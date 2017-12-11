<?php
/**
 * Created by PhpStorm.
 * User: kernbrand
 * Date: 05.12.17
 * Time: 15:20
 */
$to_wait = time() - $GLOBALS['upgrading'];

/* set Wait header */
if( is_int($to_wait) && $to_wait > 0 && $to_wait <= 600 ){
	header( sprintf('Retry-After: %d',$to_wait) );
}
header( 'Content-Type: text/html; charset=utf-8' );

/* Output minimal HTML */
?>
<html>

<head>
    <head>
        <title>Maintenance</title>
        <meta charset="UTF-8">
        <meta name="viewport"               content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta http-equiv="x-ua-compatible"  content="ie=edge">
        <style>body{background-color:#efefef;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;padding:50px;margin:0;-webkit-box-sizing:border-box;box-sizing:border-box;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center;min-height:100vh}h1{display:block;text-align:center;font-size:55px;line-height:60px;font-weight:100;color:#4c4c4c}@media (max-width:767px){h1{font-size:30px}}</style>
    </head>
</head>
<body>
<h1>- We'll be back soon -</h1>
</body>
</html>
<?php die(); ?>
