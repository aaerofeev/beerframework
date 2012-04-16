<?php defined('START_APP') or die('Bad request');

class Url {
    public static function base($protocol = FALSE)
    {        
        $base_url = '/';
        
        if (is_string($protocol))
        {
            if ($domain = parse_url($base_url, PHP_URL_HOST))
            {
                // Remove everything but the path from the URL
                $base_url = parse_url($base_url, PHP_URL_PATH);
            }
            else
            {
                // Attempt ot use HTPP_HOST and fallback to SERVER_NAME
                $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
            }

            // Add the protocol and domain to the base URL
            $base_url = $protocol.'://'.$domain.$base_url;
        }

        return $base_url;
    }
    
    public static function server($url='')
    {
        
        return 'http://'.$_SERVER['HTTP_HOST'].self::site($url);
    }

    public static function site($uri = '', $protocol = FALSE)
    {
        // Chop off possible scheme, host, port, user and pass parts
        $path = preg_replace('~^[-a-z0-9+.]++://[^/]++/?~', '', trim($uri, '/'));

        // Concat the URL
        return self::base($protocol).$path;
    }

    public static function query(array $params = NULL)
    {
        if ($params === NULL)
        {
            // Use only the current parameters
            $params = $_GET;
        }
        else
        {
            // Merge the current and new parameters
            $params = array_merge($_GET, $params);
        }

        if (empty($params))
        {
            // No query parameters
            return '';
        }

        $query = http_build_query($params, '', '&');

        // Don't prepend '?' to an empty string
        return ($query === '') ? '' : '?'.$query;
    }

    public static function fromSite($path)
    {
        return self::site(str_replace(ROOT_PATH, DIRECTORY_SEPARATOR, $path));
    }

    public static function insertSubDir($path, $subDir)
    {
        return pathinfo($path, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR .
            $subDir . DIRECTORY_SEPARATOR . pathinfo($path, PATHINFO_BASENAME);
    }
} // End url