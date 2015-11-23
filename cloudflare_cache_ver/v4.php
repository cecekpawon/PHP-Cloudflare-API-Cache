<?php

# Cloudflare API Cache v4
# @cecekpawon 11/23/2015 10:05:10 AM
# thrsh.net

# docs: https://api.cloudflare.com/

class cloudflare_cache_ver extends cloudflare_cache {
  public $ver = "v4";
  public $api = "https://api.cloudflare.com/client/v4/zones";
  public $params = array("email", "key", "domain", "id");
  public $email;
  public $key;
  public $id;
  private $domain;

  public function __construct() {
    $params = func_get_args();

    foreach ($params[0] as $key => $value) {
      $key = strtolower($key);
      if (in_array($key, $this->params) && $value) {
        $this->{$key} = $value;
      }
    }

    $this->get_zones();
  }

  private function get_zones() {
    if ($this->id) {
      return;
    }

    $fields = array(
      "name" => $this->domain,
      "status" => "active",
      "page" => "1",
      "per_page" => "1",
      "order" => "status",
      "direction" => "desc",
      "match" => "all"
    );

    $opts = array(
      "fields" => $fields
    );

    if ($res = $this->seek($opts)) {
      $this->id = $res->id;
    }
  }

  public function purge_file($fields) {
    if (!$this->id) {
      return $this->error("No zone id");
    }

    if (is_bool($fields) === true) {
      $fields = array(
        "purge_everything" => $fields
      );
    } else {
      if (!is_array($fields)) {
        $fields = array($fields);
      }
      $fields = array("files" => $fields);
    }

    $fields = json_encode($fields);

    $opts = array(
      "fields" => $fields,
      "method" => "DELETE",
      "path" => "/" . $this->id . "/purge_cache"
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
