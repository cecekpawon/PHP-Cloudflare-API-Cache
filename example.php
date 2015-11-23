<?php

# Cloudflare API Cache Example
# @cecekpawon 11/23/2015 1:08:57 PM
# thrsh.net

### Main class include
require_once("cloudflare_cache.class.php");

### API ver 1 example
$cf_cache_v1 = new cloudflare_cache(
  array(
      "tkn" => "<token/API-key>",
      "email" => "<email>",
      "z" => "<domain>"
    )
  );

# Update cache per-file (url)
$res = $cf_cache_v1->purge_file("<domain>/some.css");

# Update cache all in domain
$res = $cf_cache_v1->purge_all();

### API ver 4 example

$cf_cache_v4 = new cloudflare_cache(
  array(
      "email" => "<email>",
      "key" => "<token/API-key>",
      "domain" => "<domain>"
    )
  );

# Update cache file(s) (url)
$res = $cf_cache_v4->purge_file(
  array(
    "<domain>/some.css",
    "<domain>/some.js",
    "<domain>/some.jpg",
  )
);

# Update cache all in domain
$res = $cf_cache_v4->purge_all();

?>
