<?php // lint >= 8.4

$response = new Response()->withStatus(200);
$response = (new Response)->withStatus(200);
$ip = new RemoteAddress()?->getIpAddress();
