<?php

namespace Core;

use Core\Session;
use Core\Request;

class RedirectResponse
{
    private $uri;
    private $except = ['password', 'password_confirmation', 'pass'];

    static private $previous_url;
    function __construct($uri = null)
    {
        $this->uri = $uri;
    }

    public function with($key, $value)
    {
        Session::flash($key, $value);
        return $this;
    }


    public function notify($status, $title = null, $msg = null)
    {
        if (Session::has('notify')) {
            $old_notify = Session::get("notify");
            $notify = array_merge($old_notify, [$status => compact('title', 'msg')]);
        }
        self::with("notify", [$status => compact('title', 'msg')]);
        return $this;
    }

    public function withInput()
    {
        $request = Request::except($this->except);
        $this->with('old', $request);
        return $this;
    }

    public static function setPreviousUrl($uri)
    {
        Session::set("previous_url", $uri);
    }
    public static function capturePreviousUrl()
    {
        self::$previous_url = Session::get("previous_url", "/");
    }

    public static function previous_url()
    {
        return static::$previous_url;
    }

    public function redirect()
    {
        header("Location: " . ($this->uri ?? static::previous_url()));
        exit;
    }
}
