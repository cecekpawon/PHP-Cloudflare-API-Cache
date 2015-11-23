<?php

# Cloudflare API Cache Wrapper
# @cecekpawon 11/23/2015 10:11:44 AM
# thrsh.net

class cloudflare_cache {
  private $cf;

  public function __construct() {
    $params = func_get_args()[0];
    $ver = (isset($params["domain"])) ? "v4" : "v1";
    $path = __DIR__ . "/" . basename(__FILE__, ".php") . "_ver/" . $ver . ".php";

    if (file_exists($path)) {
      require_once($path);
      $this->cf = new cloudflare_cache_ver($params);
    } else {
      die("Class doesn't exists: \"{$path}\"");
    }
  }

  private function parse($json_str) {
    $result = false;
    $json = @json_decode($json_str);

    if (json_last_error() == JSON_ERROR_NONE) {
      $result = $json;
    }

    return $result;
  }

  public function error($res="") {
    return $this->results($res, false);
  }

  public function results($res, $success=true) {
    $ret = new stdClass();
    $ret->success = (bool) $success;
    $ret->result = $res;

    return $ret;
  }

  public function purge_file($fields) {
    return $this->cf->purge_file($fields);
  }

  public function purge_all() {
    return $this->cf->purge_file(true);
  }

  public function seek($options=array()) {
    foreach ($options as $key => $val) {
      $$key = $val;
    }

    $curl_opt_array = array();

    if (!isset($fields)) $fields = array();
    if (!isset($method)) $method = "GET";
    if (!isset($path)) $path = "";

    switch ($this->ver) {
      case "v4":
        $headers = array(
          "X-Auth-Email: {$this->email}",
          "X-Auth-Key: {$this->key}",
          "Content-Type: application/json"
        );

        $curl_opt_array += array(
          CURLOPT_HTTPHEADER => $headers
        );
        break;

      default:
        $fields = array_merge($this->params, $fields);
    }

    $method = strtoupper($method);

    $url = $this->api . $path;

    switch ($method) {
      case "GET":
        $url .= "?" . http_build_query($fields);
        break;

      case "POST":
        $curl_opt_array += array(
          CURLOPT_POST => true,
          CURLOPT_POSTFIELDS => $fields
        );
        break;

      case "DELETE":
        $curl_opt_array += array(
          CURLOPT_CUSTOMREQUEST => $method,
          CURLOPT_POSTFIELDS => $fields
        );
        break;

      default:
        return;
    }

    $curl_opt_array += array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_VERBOSE => 0,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0
    );

    $ch = curl_init();
    curl_setopt_array($ch, $curl_opt_array);
    $res = curl_exec($ch);
    //$info = curl_getinfo($ch);
    curl_close($ch);

    if (
      ($res = $this->parse($res)) &&
      isset($res->success)
    ) {
      switch ($this->ver) {
        case "v4":
          if (
            count($res) &&
            ($res->success === true) &&
            isset($res->result)
          ) {
            $res = (
              is_array($res->result) &&
              count($res->result)
            )
              ? $res->result[0]
              : $res->result;
            if (isset($res->id)) {
              return $res;
            }
          }
          break;

        default:
          if ($res->success === "success") {
            return $res;
          }
      }
    }
  }
}
?>
