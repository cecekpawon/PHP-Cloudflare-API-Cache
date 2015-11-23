<?php

# Cloudflare Cache API v1
# @cecekpawon Fri Aug 14 10:16:39 2015
# thrsh.net

# docs: https://www.cloudflare.com/docs/client-api.html

class cloudflare_cache_ver extends cloudflare_cache {
  public $ver = "v1";
  public $api = "https://www.cloudflare.com/api_json.html";
  public $params = array("tkn"=>"", "email"=>"", "z"=>"");

  public function __construct() {
    $params = func_get_args();

    foreach ($params[0] as $key => $value) {
      $key = strtolower($key);
      if (array_key_exists($key, $this->params) && $value) {
        $this->params[$key] = $value;
      }
    }
  }

  public function purge_file($fields) {
    if (is_bool($fields) === true) {
      $fields = array(
        "a" => "fpurge_ts",
        "v" => 1
      );
    } else {
      $fields = array(
        "a" => "zone_file_purge",
        "url" => $fields
      );
    }

    $opts = array(
      "fields" => $fields,
      "method" => "POST"
    );

    if ($res = $this->seek($opts)) {
      return $this->results($res);
    }

    return $this->error("No results");
  }

  public function purge_all() {
    return $this->purge_file(true);
  }
}
?>
