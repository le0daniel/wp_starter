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
    <title>Maintenance</title>
    <style>

        body{
            background-color: #efefef;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            padding: 50px;
        }
        h1{
            display: block;
            text-align: center;
            font-size: 55px;
            line-height: 60px;
            font-weight: 100;
            color: #4c4c4c;
        }

    </style>
</head>
<body>
<h1>- We'll be back soon -</h1>
</body>
</html>
<?php die(); ?>
